<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\TpaJadwalSiswa;
use App\Models\TpaSesiSiswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TpaJadwalController extends Controller
{
    /**
     * Tampilkan daftar calon siswa beserta jadwal & izin akses TPA.
     */
    public function index(Request $request): View
    {
        $query = CalonSiswa::query()
            ->where('nomor_pendaftaran', 'not like', 'SIM-%')
            ->with([
                'user',
                'jurusan',
                'gelombang',
                'tpaJadwal',
                'tpaSesi',
            ]);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($jurusanId = $request->input('jurusan_id')) {
            if ($jurusanId !== 'SEMUA') {
                $query->where('jurusan_id', $jurusanId);
            }
        }

        if ($gelombangId = $request->input('gelombang_id')) {
            if ($gelombangId !== 'SEMUA') {
                $query->where('gelombang_id', $gelombangId);
            }
        }

        if ($statusIzin = $request->input('status_izin')) {
            if ($statusIzin === 'DI_IZINKAN') {
                $query->whereHas('tpaJadwal', fn($q) => $q->where('is_diizinkan', true));
            } elseif ($statusIzin === 'DITUTUP') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('tpaJadwal')
                      ->orWhereHas('tpaJadwal', fn($sub) => $sub->where('is_diizinkan', false));
                });
            }
        }

        $calonSiswaList = $query->orderBy('nama_lengkap')->paginate(25)->withQueryString();
        $jurusanList = MasterJurusan::orderBy('nama')->get();
        $gelombangList = MasterGelombang::orderBy('nama')->get();

        return view('admin.tpa.jadwal.index', compact(
            'calonSiswaList',
            'jurusanList',
            'gelombangList'
        ));
    }

    /**
     * Simpan jadwal dan izin akses per siswa.
     */
    public function update(Request $request, CalonSiswa $calonSiswa): RedirectResponse
    {
        $validated = $request->validate([
            'waktu_mulai' => ['nullable', 'date'],
            'waktu_selesai' => ['nullable', 'date', 'after_or_equal:waktu_mulai'],
            'is_diizinkan' => ['nullable', 'boolean'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        TpaJadwalSiswa::updateOrCreate(
            ['calon_siswa_id' => $calonSiswa->id],
            [
                'waktu_mulai' => $validated['waktu_mulai'] ?? null,
                'waktu_selesai' => $validated['waktu_selesai'] ?? null,
                'is_diizinkan' => $request->boolean('is_diizinkan'),
                'catatan' => $validated['catatan'] ?? null,
            ]
        );

        return back()->with('success', "Jadwal TPA untuk {$calonSiswa->nama_lengkap} berhasil diperbarui.");
    }

    /**
     * Toggle izin akses satu klik.
     */
    public function toggle(CalonSiswa $calonSiswa): JsonResponse|RedirectResponse
    {
        $jadwal = TpaJadwalSiswa::firstOrNew(['calon_siswa_id' => $calonSiswa->id]);
        $jadwal->is_diizinkan = ! $jadwal->is_diizinkan;
        $jadwal->save();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_diizinkan' => $jadwal->is_diizinkan,
                'message' => 'Status izin berhasil diubah.',
            ]);
        }

        return back()->with('success', 'Status izin akses berhasil diubah.');
    }

    /**
     * Pengaturan massal (Batch Assign) untuk banyak siswa sekaligus.
     */
    public function batch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_ids' => ['required', 'array', 'min:1'],
            'siswa_ids.*' => ['integer', 'exists:calon_siswa,id'],
            'waktu_mulai' => ['nullable', 'date'],
            'waktu_selesai' => ['nullable', 'date'],
            'is_diizinkan' => ['required', 'boolean'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated['siswa_ids'] as $calonSiswaId) {
            TpaJadwalSiswa::updateOrCreate(
                ['calon_siswa_id' => $calonSiswaId],
                [
                    'waktu_mulai' => $validated['waktu_mulai'] ?? null,
                    'waktu_selesai' => $validated['waktu_selesai'] ?? null,
                    'is_diizinkan' => $validated['is_diizinkan'],
                    'catatan' => $validated['catatan'] ?? null,
                ]
            );
        }

        $count = count($validated['siswa_ids']);
        return back()->with('success', "Pengaturan jadwal massal berhasil diterapkan pada {$count} calon siswa.");
    }

    /**
     * Reset sesi ujian calon siswa jika terjadi kendala teknis perangkat/jaringan.
     */
    public function resetSesi(CalonSiswa $calonSiswa): RedirectResponse
    {
        $sesi = $calonSiswa->tpaSesi;
        if ($sesi) {
            $sesi->jawabanSiswa()->delete();
            $sesi->delete();
        }

        return back()->with('success', "Sesi pengerjaan TPA untuk {$calonSiswa->nama_lengkap} telah direset. Siswa dapat memulai kembali ujian dari awal.");
    }
}
