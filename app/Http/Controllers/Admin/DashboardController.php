<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\PembayaranDaftarUlang;
use App\Models\PembayaranSeleksi;
use App\Models\Tagihan;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    /**
     * Display Administrator executive dashboard with live metrics.
     */
    public function index(): View
    {
        $totalPendaftar = CalonSiswa::count();

        // Status Counts
        $stats = [
            'total_pendaftar' => $totalPendaftar,
            'menunggu_bayar_seleksi' => CalonSiswa::whereIn('status_spmb', [
                SpmbStatus::REGISTRASI->value,
                SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI->value,
            ])->count(),
            'bayar_terverifikasi' => CalonSiswa::where('status_spmb', SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI->value)->count(),
            'sedang_lengkapi_data' => CalonSiswa::whereIn('status_spmb', [
                SpmbStatus::MELENGKAPI_DATA->value,
                SpmbStatus::DATA_LENGKAP->value,
                SpmbStatus::MENUNGGU_WAWANCARA->value,
            ])->count(),
            'wawancara_selesai' => CalonSiswa::whereIn('status_spmb', [
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

        // Kuota & Progres Keterisian Kompetensi Keahlian (Standar 2 rombel = 72 siswa)
        $targetQuota = 72;
        $jurusanStats = MasterJurusan::aktif()->get()->map(function ($j) use ($targetQuota) {
            $totalApplied = CalonSiswa::where('jurusan_id', $j->id)->count();
            $acceptedCount = CalonSiswa::where('jurusan_id', $j->id)
                ->whereIn('status_spmb', [
                    SpmbStatus::DITERIMA->value,
                    SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
                    SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
                    SpmbStatus::RESMI_TERDAFTAR->value,
                ])
                ->count();
            $resmiCount = CalonSiswa::where('jurusan_id', $j->id)
                ->where('status_spmb', SpmbStatus::RESMI_TERDAFTAR->value)
                ->count();

            $percentage = $targetQuota > 0 ? round(($acceptedCount / $targetQuota) * 100, 1) : 0;

            return [
                'id' => $j->id,
                'kode' => $j->kode_jurusan,
                'nama' => $j->nama_jurusan,
                'kuota' => $targetQuota,
                'pendaftar' => $totalApplied,
                'diterima' => $acceptedCount,
                'resmi' => $resmiCount,
                'sisa' => max(0, $targetQuota - $acceptedCount),
                'persentase' => min(100, $percentage),
            ];
        });

        // Distribusi per Gelombang
        $gelombangStats = MasterGelombang::withCount('calonSiswa')->get();

        // Ringkasan Keuangan
        $kasSeleksi = (float) PembayaranSeleksi::where('status', 'DIVERIFIKASI')->sum('nominal_dibayar');
        $kasDaftarUlang = (float) PembayaranDaftarUlang::where('status', 'DIVERIFIKASI')->sum('nominal_dibayar');
        $totalTagihanDaftarUlang = (float) Tagihan::sum('total_netto');
        $sisaPiutang = max(0, $totalTagihanDaftarUlang - $kasDaftarUlang);

        $keuangan = [
            'kas_seleksi' => $kasSeleksi,
            'kas_daftar_ulang' => $kasDaftarUlang,
            'total_kas_masuk' => $kasSeleksi + $kasDaftarUlang,
            'total_tagihan_daftar_ulang' => $totalTagihanDaftarUlang,
            'sisa_piutang' => $sisaPiutang,
        ];

        // Pendaftar Terbaru (10 record)
        $recentCandidates = CalonSiswa::with(['jurusan', 'program', 'gelombang'])
            ->latest('id')
            ->take(10)
            ->get();

        // Log Aktivitas Sistem Terbaru (10 log)
        $recentActivities = Activity::with('causer')
            ->latest('id')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'jurusanStats',
            'gelombangStats',
            'keuangan',
            'recentCandidates',
            'recentActivities'
        ));
    }
}
