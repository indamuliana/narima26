<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\MasterSekolahAsal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MappingSekolahController extends Controller
{
    /**
     * Tampilan utama daftar sekolah asal yang perlu dipetakan.
     */
    public function index(Request $request): View
    {
        $tab = $request->get('tab', 'unmapped');
        $search = trim($request->get('q', ''));

        // Statistik
        $totalCalon = CalonSiswa::tanpaDemo()->count();
        $tertautCount = CalonSiswa::tanpaDemo()->whereNotNull('asal_sekolah_id')->count();
        $manualCount = CalonSiswa::tanpaDemo()
            ->whereNull('asal_sekolah_id')
            ->whereNotNull('asal_sekolah_lainnya')
            ->where('asal_sekolah_lainnya', '!=', '')
            ->count();

        // Daftar input manual yang belum tertaut ke master
        $unmappedQuery = CalonSiswa::tanpaDemo()
            ->whereNull('asal_sekolah_id')
            ->whereNotNull('asal_sekolah_lainnya')
            ->where('asal_sekolah_lainnya', '!=', '')
            ->when($search, fn($q) => $q->where('asal_sekolah_lainnya', 'like', "%{$search}%"))
            ->selectRaw('TRIM(asal_sekolah_lainnya) as nama_manual, count(*) as total_siswa')
            ->groupBy('nama_manual')
            ->orderByDesc('total_siswa');

        $unmappedSchools = $unmappedQuery->get()->map(function ($item) {
            $suggestion = $this->findBestMatch($item->nama_manual);
            return [
                'nama_manual' => $item->nama_manual,
                'total_siswa' => $item->total_siswa,
                'suggestion' => $suggestion,
            ];
        });

        // Daftar sekolah yang sudah tertaut
        $mappedQuery = CalonSiswa::tanpaDemo()
            ->whereNotNull('asal_sekolah_id')
            ->join('master_sekolah_asal', 'calon_siswa.asal_sekolah_id', '=', 'master_sekolah_asal.id')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('master_sekolah_asal.nama_sekolah', 'like', "%{$search}%")
                        ->orWhere('master_sekolah_asal.kokab', 'like', "%{$search}%")
                        ->orWhere('calon_siswa.asal_sekolah_lainnya', 'like', "%{$search}%");
                });
            })
            ->selectRaw('
                master_sekolah_asal.id as master_id,
                master_sekolah_asal.nama_sekolah,
                master_sekolah_asal.kokab,
                master_sekolah_asal.kecamatan,
                master_sekolah_asal.jenis,
                calon_siswa.asal_sekolah_lainnya as legacy_input,
                count(calon_siswa.id) as total_siswa
            ')
            ->groupBy(
                'master_sekolah_asal.id',
                'master_sekolah_asal.nama_sekolah',
                'master_sekolah_asal.kokab',
                'master_sekolah_asal.kecamatan',
                'master_sekolah_asal.jenis',
                'calon_siswa.asal_sekolah_lainnya'
            )
            ->orderByDesc('total_siswa');

        $mappedSchools = $tab === 'mapped' ? $mappedQuery->paginate(25)->withQueryString() : collect();

        return view('admin.mapping-sekolah.index', [
            'tab' => $tab,
            'search' => $search,
            'totalCalon' => $totalCalon,
            'tertautCount' => $tertautCount,
            'manualCount' => $manualCount,
            'unmappedSchools' => $unmappedSchools,
            'mappedSchools' => $mappedSchools,
        ]);
    }

    /**
     * Endpoint API untuk preview auto-matching sebelum dieksekusi massal.
     */
    public function previewAutoMatch(): JsonResponse
    {
        $unmapped = CalonSiswa::tanpaDemo()
            ->whereNull('asal_sekolah_id')
            ->whereNotNull('asal_sekolah_lainnya')
            ->where('asal_sekolah_lainnya', '!=', '')
            ->selectRaw('TRIM(asal_sekolah_lainnya) as nama_manual, count(*) as total_siswa')
            ->groupBy('nama_manual')
            ->orderByDesc('total_siswa')
            ->get();

        $preview = [];
        foreach ($unmapped as $item) {
            $match = $this->findBestMatch($item->nama_manual);
            if ($match) {
                $preview[] = [
                    'nama_manual' => $item->nama_manual,
                    'total_siswa' => $item->total_siswa,
                    'master_id' => $match['school']->id,
                    'master_nama' => $match['school']->nama_sekolah,
                    'master_kokab' => $match['school']->kokab,
                    'score' => $match['score'],
                    'type' => $match['type'],
                    'selected' => $match['score'] >= 75, // Default terpilih jika confidence >= 75%
                ];
            } else {
                $preview[] = [
                    'nama_manual' => $item->nama_manual,
                    'total_siswa' => $item->total_siswa,
                    'master_id' => null,
                    'master_nama' => '-',
                    'master_kokab' => '-',
                    'score' => 0,
                    'type' => 'Tidak Ada',
                    'selected' => false,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $preview,
        ]);
    }

    /**
     * Terapkan hasil auto-match massal yang telah disetujui admin di preview modal.
     */
    public function applyAutoMatch(Request $request): RedirectResponse
    {
        $matches = $request->input('matches', []);
        if (empty($matches) || !is_array($matches)) {
            return back()->with('error', 'Tidak ada data penautan yang dipilih.');
        }

        $appliedCount = 0;
        $schoolCount = 0;

        DB::transaction(function () use ($matches, &$appliedCount, &$schoolCount) {
            foreach ($matches as $match) {
                $raw = trim($match['raw_name'] ?? '');
                $masterId = (int) ($match['master_id'] ?? 0);

                if (!empty($raw) && $masterId > 0) {
                    $affected = CalonSiswa::where('asal_sekolah_lainnya', $raw)
                        ->whereNull('asal_sekolah_id')
                        ->update(['asal_sekolah_id' => $masterId]);

                    if ($affected > 0) {
                        $appliedCount += $affected;
                        $schoolCount++;
                    }
                }
            }
        });

        return redirect()->route('admin.mapping-sekolah.index')
            ->with('success', "Berhasil menautkan {$schoolCount} nama sekolah ke Master ({$appliedCount} calon murid disinkronkan).");
    }

    /**
     * Tautkan satu nama sekolah manual ke master tertentu.
     */
    public function linkSingle(Request $request): RedirectResponse
    {
        $request->validate([
            'raw_name' => ['required', 'string'],
            'master_id' => ['required', 'exists:master_sekolah_asal,id'],
        ]);

        $affected = CalonSiswa::where('asal_sekolah_lainnya', trim($request->raw_name))
            ->update(['asal_sekolah_id' => $request->master_id]);

        $master = MasterSekolahAsal::find($request->master_id);

        return redirect()->route('admin.mapping-sekolah.index')
            ->with('success', "Sekolah '{$request->raw_name}' berhasil ditautkan ke '{$master->nama_sekolah}' ({$affected} calon murid terupdate).");
    }

    /**
     * Buat data master sekolah baru dari input manual lalu tautkan.
     */
    public function storeMasterAndLink(Request $request): RedirectResponse
    {
        $request->validate([
            'raw_name' => ['required', 'string'],
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'kokab' => ['required', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'jenis' => ['nullable', 'string', 'in:SMP,MTS,PKBM,SPK SMP'],
            'status' => ['nullable', 'string', 'in:Negeri,Swasta'],
        ]);

        $newMaster = MasterSekolahAsal::create([
            'nama_sekolah' => strtoupper(trim($request->nama_sekolah)),
            'kokab' => trim($request->kokab),
            'kecamatan' => $request->kecamatan ? trim($request->kecamatan) : null,
            'provinsi' => $request->provinsi ?: 'Jawa Barat',
            'jenis' => $request->jenis ?: 'SMP',
            'status' => $request->status ?: 'Swasta',
        ]);

        $affected = CalonSiswa::where('asal_sekolah_lainnya', trim($request->raw_name))
            ->update(['asal_sekolah_id' => $newMaster->id]);

        return redirect()->route('admin.mapping-sekolah.index')
            ->with('success', "Master sekolah baru '{$newMaster->nama_sekolah}' berhasil dibuat dan ditautkan ke {$affected} calon murid.");
    }

    /**
     * Lepaskan tautan master dari input manual (kembalikan ke manual).
     */
    public function unlink(Request $request): RedirectResponse
    {
        $request->validate([
            'master_id' => ['required', 'exists:master_sekolah_asal,id'],
            'raw_name' => ['nullable', 'string'],
        ]);

        $query = CalonSiswa::where('asal_sekolah_id', $request->master_id);
        if ($request->filled('raw_name')) {
            $query->where('asal_sekolah_lainnya', trim($request->raw_name));
        }

        $affected = $query->update(['asal_sekolah_id' => null]);

        return redirect()->route('admin.mapping-sekolah.index', ['tab' => 'mapped'])
            ->with('success', "Tautan master berhasil dilepas ({$affected} data calon murid kembali berstatus manual).");
    }

    /**
     * Algoritma pencocokan cerdas antara teks manual dengan tabel master_sekolah_asal.
     */
    protected function findBestMatch(string $rawName): ?array
    {
        $clean = trim($rawName);
        if (empty($clean)) return null;

        // 1. Cek Exact Match
        $exact = MasterSekolahAsal::where('nama_sekolah', 'like', $clean)->first();
        if ($exact) {
            return ['school' => $exact, 'score' => 100, 'type' => 'Pasti 100%'];
        }

        // 2. Normalisasi & Bersihkan stop words
        $stopWords = [
            'SMP', 'SMPS', 'SMPIT', 'MTS', 'MTSS', 'SMPN', 'MTSN', 'SEKOLAH', 'ISLAM', 'TERPADU',
            'IT', 'KAB', 'KOTA', 'BOGOR', 'GARUT', 'KUNINGAN', 'TASIKMALAYA', 'BANDUNG', 'CIMAHI',
            'JAKARTA', 'SELATAN', 'SUBANG', 'CIANJUR', 'SUKABUMI'
        ];

        // Pisahkan kata dan bersihkan karakter khusus
        $words = preg_split('/[\s\-\.,\x{2019}\x{0027}\'\"]+/u', mb_strtoupper($clean));
        $coreWords = array_values(array_filter($words, fn($w) => !in_array($w, $stopWords) && mb_strlen($w) >= 3));

        if (empty($coreWords)) {
            return null;
        }

        // Cari kandidat di database master berdasarkan kata kunci inti
        $query = MasterSekolahAsal::query();
        foreach ($coreWords as $cw) {
            $query->where('nama_sekolah', 'like', '%' . mb_substr($cw, 0, 4) . '%');
        }
        $candidates = $query->limit(20)->get();

        if ($candidates->isEmpty() && count($coreWords) > 1) {
            $candidates = MasterSekolahAsal::where('nama_sekolah', 'like', '%' . $coreWords[0] . '%')
                ->limit(20)
                ->get();
        }

        $best = null;
        $bestScore = 0;
        foreach ($candidates as $cand) {
            similar_text(mb_strtoupper($clean), mb_strtoupper($cand->nama_sekolah), $pct);
            if ($pct > $bestScore) {
                $bestScore = $pct;
                $best = $cand;
            }
        }

        if ($best && $bestScore >= 55) {
            $rounded = (int) round($bestScore);
            $type = $rounded >= 85 ? 'Tinggi' : 'Sedang';
            return [
                'school' => $best,
                'score' => $rounded,
                'type' => "{$type} ({$rounded}%)",
            ];
        }

        return null;
    }
}
