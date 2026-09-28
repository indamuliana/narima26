<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\KeputusanKelulusan;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Services\PdfService;
use App\Services\SpmbStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SidangKelulusanController extends Controller
{
    public function __construct(
        protected SpmbStatusService $spmbStatusService,
        protected PdfService $pdfService
    ) {}

    /**
     * Display candidate list for Sidang Pleno Kelulusan.
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');
        $jurusanId = $request->input('jurusan_id');
        $statusFilter = $request->input('status_filter', 'SEMUA');

        // Query candidates who have reached interview or subsequent steps
        $query = CalonSiswa::query()
            ->with([
                'jurusan',
                'program',
                'gelombang',
                'wawancara.details.kriteria',
                'wawancara.pewawancara',
                'keputusanKelulusan.ditetapkanOleh',
                'dataAkademik',
            ]);

        // Filter status
        if ($statusFilter === 'MENUNGGU_SIDANG') {
            $query->whereIn('status_spmb', [
                SpmbStatus::SUDAH_DIWAWANCARA->value,
                SpmbStatus::MENUNGGU_KEPUTUSAN->value,
            ]);
        } elseif ($statusFilter === 'DITERIMA') {
            $query->whereIn('status_spmb', [
                SpmbStatus::DITERIMA->value,
                SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
                SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
                SpmbStatus::RESMI_TERDAFTAR->value,
            ]);
        } elseif ($statusFilter === 'DITOLAK') {
            $query->where('status_spmb', SpmbStatus::DITOLAK->value);
        } else {
            // Show all candidates who have been interviewed or have decisions
            $query->whereIn('status_spmb', [
                SpmbStatus::SUDAH_DIWAWANCARA->value,
                SpmbStatus::MENUNGGU_KEPUTUSAN->value,
                SpmbStatus::DITERIMA->value,
                SpmbStatus::DITOLAK->value,
                SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
                SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
                SpmbStatus::RESMI_TERDAFTAR->value,
            ]);
        }

        if (! empty($jurusanId)) {
            $query->where('jurusan_id', $jurusanId);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $calonSiswaList = $query->orderBy('nama_lengkap')->paginate(20)->withQueryString();

        // Department quota metrics
        $jurusanList = MasterJurusan::aktif()->get()->map(function ($j) {
            $acceptedCount = CalonSiswa::where('jurusan_id', $j->id)
                ->whereIn('status_spmb', [
                    SpmbStatus::DITERIMA->value,
                    SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
                    SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
                    SpmbStatus::RESMI_TERDAFTAR->value,
                ])
                ->count();

            $totalApplied = CalonSiswa::where('jurusan_id', $j->id)->count();
            $targetQuota = 72; // Standar 2 rombel x 36 siswa per kompetensi

            return [
                'id' => $j->id,
                'kode' => $j->kode,
                'nama' => $j->nama_jurusan,
                'kuota' => $targetQuota,
                'diterima' => $acceptedCount,
                'sisa' => max(0, $targetQuota - $acceptedCount),
                'pendaftar' => $totalApplied,
            ];
        });

        // Summary Stats
        $stats = [
            'menunggu_sidang' => CalonSiswa::whereIn('status_spmb', [
                SpmbStatus::SUDAH_DIWAWANCARA->value,
                SpmbStatus::MENUNGGU_KEPUTUSAN->value,
            ])->count(),
            'total_diterima' => CalonSiswa::whereIn('status_spmb', [
                SpmbStatus::DITERIMA->value,
                SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
                SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
                SpmbStatus::RESMI_TERDAFTAR->value,
            ])->count(),
            'total_ditolak' => CalonSiswa::where('status_spmb', SpmbStatus::DITOLAK->value)->count(),
        ];

        return view('kepala-sekolah.sidang-kelulusan.index', compact(
            'calonSiswaList',
            'jurusanList',
            'stats',
            'search',
            'jurusanId',
            'statusFilter'
        ));
    }

    /**
     * Show detail evaluation room for a candidate in Sidang Kelulusan.
     */
    public function show(CalonSiswa $calonSiswa): View
    {
        $calonSiswa->loadMissing([
            'jurusan',
            'program',
            'gelombang',
            'dataOrangtua',
            'dataAkademik',
            'dokumenPendaftaran',
            'wawancara.details.kriteria',
            'wawancara.pewawancara',
            'keputusanKelulusan.ditetapkanOleh',
        ]);

        $wawancara = $calonSiswa->wawancaraTerakhir;
        $keputusan = $calonSiswa->keputusanKelulusan;

        return view('kepala-sekolah.sidang-kelulusan.show', compact('calonSiswa', 'wawancara', 'keputusan'));
    }

    /**
     * Store or update individual selection decision (Diterima / Ditolak).
     */
    public function putuskan(Request $request, CalonSiswa $calonSiswa): RedirectResponse
    {
        $validated = $request->validate([
            'keputusan' => ['required', 'in:DITERIMA,DITOLAK'],
            'alasan_catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $calonSiswa) {
            $isAccepted = $validated['keputusan'] === 'DITERIMA';
            $defaultNote = $isAccepted
                ? 'Dinyatakan lulus seleksi berdasarkan hasil sidang pleno komite SPMB.'
                : 'Belum memenuhi kualifikasi kelulusan seleksi SPMB tahun ini.';
            $note = ($validated['alasan_catatan'] ?? null) ?: $defaultNote;

            $keputusan = KeputusanKelulusan::updateOrCreate(
                ['calon_siswa_id' => $calonSiswa->id],
                [
                    'keputusan' => $validated['keputusan'],
                    'alasan_catatan' => $note,
                    'ditetapkan_oleh' => auth()->id(),
                    'ditetapkan_at' => now(),
                    'versi_keputusan' => 1,
                ]
            );

            // Transition status SPMB
            $targetStatus = $isAccepted ? SpmbStatus::DITERIMA : SpmbStatus::DITOLAK;

            $this->spmbStatusService->changeStatus(
                calonSiswa: $calonSiswa,
                targetStatus: $targetStatus,
                alasan: "Keputusan Sidang Pleno: {$validated['keputusan']}",
                catatan: $note,
                changedBy: auth()->user()
            );

            activity('kelulusan')
                ->performedOn($calonSiswa)
                ->causedBy(auth()->user())
                ->withProperties([
                    'keputusan' => $validated['keputusan'],
                    'nomor_pendaftaran' => $calonSiswa->nomor_pendaftaran,
                    'jurusan' => $calonSiswa->jurusan?->nama_jurusan,
                ])
                ->log("Kepala Sekolah menetapkan hasil seleksi {$calonSiswa->nama_lengkap}: {$validated['keputusan']}");
        });

        return redirect()->route('kepala-sekolah.sidang-kelulusan.show', $calonSiswa)
            ->with('success', "Keputusan kelulusan ({$validated['keputusan']}) untuk {$calonSiswa->nama_lengkap} berhasil ditetapkan.");
    }

    /**
     * Process bulk / batch decision for multiple candidates in plenary session.
     */
    public function batch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'calon_siswa_ids' => ['required', 'array', 'min:1'],
            'calon_siswa_ids.*' => ['required', 'exists:calon_siswa,id'],
            'keputusan' => ['required', 'in:DITERIMA,DITOLAK'],
            'catatan_sidang' => ['nullable', 'string', 'max:1000'],
        ]);

        $candidates = CalonSiswa::whereIn('id', $validated['calon_siswa_ids'])->get();
        $isAccepted = $validated['keputusan'] === 'DITERIMA';
        $targetStatus = $isAccepted ? SpmbStatus::DITERIMA : SpmbStatus::DITOLAK;
        $note = $validated['catatan_sidang'] ?: ($isAccepted ? 'Dinyatakan lulus seleksi pada sidang pleno bersama.' : 'Belum memenuhi kriteria kelulusan.');

        DB::transaction(function () use ($candidates, $validated, $targetStatus, $note) {
            foreach ($candidates as $siswa) {
                KeputusanKelulusan::updateOrCreate(
                    ['calon_siswa_id' => $siswa->id],
                    [
                        'keputusan' => $validated['keputusan'],
                        'alasan_catatan' => $note,
                        'ditetapkan_oleh' => auth()->id(),
                        'ditetapkan_at' => now(),
                    ]
                );

                $this->spmbStatusService->changeStatus(
                    calonSiswa: $siswa,
                    targetStatus: $targetStatus,
                    alasan: "Keputusan Sidang Pleno Massal: {$validated['keputusan']}",
                    catatan: $note,
                    changedBy: auth()->user()
                );
            }
        });

        return redirect()->route('kepala-sekolah.sidang-kelulusan.index')
            ->with('success', "Berhasil menetapkan keputusan ({$validated['keputusan']}) untuk " . count($candidates) . " calon siswa.");
    }

    /**
     * Print official Surat Keputusan Hasil Seleksi PDF.
     */
    public function cetakSk(CalonSiswa $calonSiswa): Response
    {
        $keputusan = $calonSiswa->keputusanKelulusan;
        $statusStr = $keputusan ? $keputusan->keputusan : ($calonSiswa->status_spmb === SpmbStatus::DITOLAK ? 'DITOLAK' : 'DITERIMA');

        $pdf = $this->pdfService->generateKelulusan($calonSiswa, $statusStr);

        return $pdf->stream("SK_Kelulusan_{$calonSiswa->nomor_pendaftaran}.pdf");
    }
}
