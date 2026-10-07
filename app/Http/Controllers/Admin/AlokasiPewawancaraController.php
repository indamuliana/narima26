<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\MasterJurusan;
use App\Models\User;
use App\Models\Wawancara;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlokasiPewawancaraController extends Controller
{
    /**
     * Display candidate interview allocation queue and assigned interviewers.
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');
        $jurusanId = $request->input('jurusan_id');
        $pewawancaraId = $request->input('pewawancara_id');
        $statusAlokasi = $request->input('status_alokasi');

        // Calon siswa yang sudah menyelesaikan berkas & masuk tahapan wawancara
        $query = CalonSiswa::with([
            'jurusan',
            'program',
            'latestWawancara.pewawancara',
            'latestWawancara.details',
        ])->whereIn('status_spmb', [
            SpmbStatus::DATA_LENGKAP->value,
            SpmbStatus::MENUNGGU_WAWANCARA->value,
            SpmbStatus::SUDAH_DIWAWANCARA->value,
            SpmbStatus::MENUNGGU_KEPUTUSAN->value,
            SpmbStatus::DITERIMA->value,
            SpmbStatus::DITOLAK->value,
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($jurusanId) {
            $query->where('jurusan_id', $jurusanId);
        }

        if ($pewawancaraId) {
            $query->whereHas('wawancara', function ($q) use ($pewawancaraId) {
                $q->where('pewawancara_id', $pewawancaraId);
            });
        }

        if ($statusAlokasi === 'BELUM') {
            $query->whereDoesntHave('wawancara');
        } elseif ($statusAlokasi === 'SUDAH') {
            $query->whereHas('wawancara');
        }

        $calonSiswaList = $query->paginate(20)->withQueryString();

        $pewawancaraList = User::whereIn('role', [User::ROLE_PEWAWANCARA, User::ROLE_ADMIN, User::ROLE_OPERATOR])
            ->where('is_active', true)
            ->withCount('wawancara')
            ->orderBy('name')
            ->get();

        $jurusanList = MasterJurusan::aktif()->orderBy('kode')->get();

        return view('admin.alokasi-pewawancara.index', compact(
            'calonSiswaList',
            'pewawancaraList',
            'jurusanList',
            'search',
            'jurusanId',
            'pewawancaraId',
            'statusAlokasi'
        ));
    }

    /**
     * Assign or reassign interviewer for a single candidate.
     */
    public function alokasikan(Request $request, CalonSiswa $calonSiswa): RedirectResponse
    {
        $validated = $request->validate([
            'pewawancara_id' => ['required', 'exists:users,id'],
            'tanggal_wawancara' => ['nullable', 'date'],
        ]);

        $pewawancara = User::findOrFail($validated['pewawancara_id']);

        // Cari atau buat record wawancara
        $wawancara = Wawancara::where('calon_siswa_id', $calonSiswa->id)->latest('id')->first();

        if ($wawancara) {
            $wawancara->update([
                'pewawancara_id' => $pewawancara->id,
                'tanggal_wawancara' => $validated['tanggal_wawancara'] ?? $wawancara->tanggal_wawancara ?? now(),
            ]);
        } else {
            Wawancara::create([
                'calon_siswa_id' => $calonSiswa->id,
                'pewawancara_id' => $pewawancara->id,
                'tanggal_wawancara' => $validated['tanggal_wawancara'] ?? now(),
                'status' => Wawancara::STATUS_MENUNGGU,
            ]);
        }

        // Pastikan status calon siswa minimal MENUNGGU_WAWANCARA jika sebelumnya DATA_LENGKAP
        if ($calonSiswa->status_spmb === SpmbStatus::DATA_LENGKAP) {
            $calonSiswa->update([
                'status_spmb' => SpmbStatus::MENUNGGU_WAWANCARA,
            ]);
        }

        activity('wawancara_alokasi')
            ->performedOn($calonSiswa)
            ->causedBy(auth()->user())
            ->log("Admin mengalokasikan calon siswa {$calonSiswa->nama_lengkap} ke pewawancara {$pewawancara->name}");

        return back()->with('success', "Calon siswa {$calonSiswa->nama_lengkap} berhasil dialokasikan ke {$pewawancara->name}.");
    }

    /**
     * Batch assign multiple candidates to a selected interviewer.
     */
    public function alokasiBatch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'calon_siswa_ids' => ['required', 'array', 'min:1'],
            'calon_siswa_ids.*' => ['exists:calon_siswa,id'],
            'pewawancara_id' => ['required', 'exists:users,id'],
        ], [
            'calon_siswa_ids.required' => 'Pilih minimal satu calon siswa untuk dialokasikan.',
            'pewawancara_id.required' => 'Pilih guru pewawancara penerima tugas.',
        ]);

        $pewawancara = User::findOrFail($validated['pewawancara_id']);
        $candidates = CalonSiswa::whereIn('id', $validated['calon_siswa_ids'])->get();

        foreach ($candidates as $cs) {
            $w = Wawancara::where('calon_siswa_id', $cs->id)->latest('id')->first();
            if ($w) {
                $w->update(['pewawancara_id' => $pewawancara->id]);
            } else {
                Wawancara::create([
                    'calon_siswa_id' => $cs->id,
                    'pewawancara_id' => $pewawancara->id,
                    'tanggal_wawancara' => now(),
                    'status' => Wawancara::STATUS_MENUNGGU,
                ]);
            }

            if ($cs->status_spmb === SpmbStatus::DATA_LENGKAP) {
                $cs->update(['status_spmb' => SpmbStatus::MENUNGGU_WAWANCARA]);
            }
        }

        activity('wawancara_alokasi')
            ->causedBy(auth()->user())
            ->log("Admin mengalokasikan massal " . count($candidates) . " calon siswa ke pewawancara {$pewawancara->name}");

        return back()->with('success', "Berhasil mengalokasikan " . count($candidates) . " calon siswa ke pewawancara {$pewawancara->name}.");
    }
}
