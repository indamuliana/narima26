<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display Kepala Sekolah executive dashboard.
     */
    public function index(): View
    {
        $totalPendaftar = CalonSiswa::count();

        $stats = [
            'total_pendaftar' => $totalPendaftar,
            'menunggu_sidang' => CalonSiswa::whereIn('status_spmb', [
                SpmbStatus::SUDAH_DIWAWANCARA->value,
                SpmbStatus::MENUNGGU_KEPUTUSAN->value,
            ])->count(),
            'diterima' => CalonSiswa::whereIn('status_spmb', [
                SpmbStatus::DITERIMA->value,
                SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
                SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
                SpmbStatus::RESMI_TERDAFTAR->value,
            ])->count(),
            'ditolak' => CalonSiswa::where('status_spmb', SpmbStatus::DITOLAK->value)->count(),
            'mengundurkan_diri' => CalonSiswa::where('status_spmb', SpmbStatus::MENGUNDURKAN_DIRI->value)->count(),
            'resmi_terdaftar' => CalonSiswa::where('status_spmb', SpmbStatus::RESMI_TERDAFTAR->value)->count(),
        ];

        // Distribution by Department
        $jurusanStats = MasterJurusan::aktif()->withCount([
            'calonSiswa as total_count',
            'calonSiswa as diterima_count' => function ($q) {
                $q->whereIn('status_spmb', [
                    SpmbStatus::DITERIMA->value,
                    SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
                    SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
                    SpmbStatus::RESMI_TERDAFTAR->value,
                ]);
            },
        ])->get();

        // Candidates recently interviewed waiting for plenary review
        $antrianSidang = CalonSiswa::with(['jurusan', 'program', 'wawancara.pewawancara'])
            ->whereIn('status_spmb', [
                SpmbStatus::SUDAH_DIWAWANCARA->value,
                SpmbStatus::MENUNGGU_KEPUTUSAN->value,
            ])
            ->latest('updated_at')
            ->limit(5)
            ->get();

        return view('kepala-sekolah.dashboard', compact('stats', 'jurusanStats', 'antrianSidang'));
    }
}
