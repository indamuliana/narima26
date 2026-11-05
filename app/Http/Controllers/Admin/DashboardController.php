<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\PembayaranDaftarUlang;
use App\Models\PembayaranSeleksi;
use App\Models\PengaturanSpmb;
use App\Models\Tagihan;
use App\Services\DashboardMetricsService;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    /**
     * Display Administrator executive dashboard with minimalist 4-row layout.
     */
    public function index(DashboardMetricsService $metricsService): View
    {
        // Baris 1: Ringkasan Total & Breakdown Program / Gender (Eksklusif data riil / tanpa demo)
        $programStats = $metricsService->getProgramGenderStats();
        $totalPendaftar = CalonSiswa::tanpaDemo()->count();

        $baris1 = [
            'total_pendaftar' => $totalPendaftar,
            'total_unggulan' => $programStats['unggulan_all'],
            'total_reguler' => $programStats['reguler_all'],
            'unggulan_laki' => $programStats['unggulan_laki'],
            'unggulan_perempuan' => $programStats['unggulan_perempuan'],
            'reguler_laki' => $programStats['reguler_laki'],
            'reguler_perempuan' => $programStats['reguler_perempuan'],
        ];

        // Baris 2: 8 Status Kunci Alur SPMB (1 single grouped query)
        $rawStatusCounts = CalonSiswa::tanpaDemo()
            ->selectRaw('status_spmb, count(*) as count')
            ->groupBy('status_spmb')
            ->pluck('count', 'status_spmb')
            ->toArray();

        $statusCounts = [
            'menunggu_bayar' => [
                'label' => 'Menunggu Bayar Seleksi',
                'count' => ($rawStatusCounts[SpmbStatus::REGISTRASI->value] ?? 0) + ($rawStatusCounts[SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI->value] ?? 0),
                'color' => 'amber',
                'status_key' => SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI->value,
            ],
            'melengkapi_data' => [
                'label' => 'Melengkapi Data & Dokumen',
                'count' => ($rawStatusCounts[SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI->value] ?? 0) + ($rawStatusCounts[SpmbStatus::MELENGKAPI_DATA->value] ?? 0) + ($rawStatusCounts[SpmbStatus::DATA_LENGKAP->value] ?? 0),
                'color' => 'orange',
                'status_key' => SpmbStatus::MELENGKAPI_DATA->value,
            ],
            'menunggu_wawancara' => [
                'label' => 'Menunggu Wawancara',
                'count' => $rawStatusCounts[SpmbStatus::MENUNGGU_WAWANCARA->value] ?? 0,
                'color' => 'cyan',
                'status_key' => SpmbStatus::MENUNGGU_WAWANCARA->value,
            ],
            'menunggu_keputusan' => [
                'label' => 'Menunggu Sidang / Keputusan',
                'count' => ($rawStatusCounts[SpmbStatus::SUDAH_DIWAWANCARA->value] ?? 0) + ($rawStatusCounts[SpmbStatus::MENUNGGU_KEPUTUSAN->value] ?? 0),
                'color' => 'indigo',
                'status_key' => SpmbStatus::MENUNGGU_KEPUTUSAN->value,
            ],
            'diterima' => [
                'label' => 'Lulus / Menunggu Daftar Ulang',
                'count' => ($rawStatusCounts[SpmbStatus::DITERIMA->value] ?? 0) + ($rawStatusCounts[SpmbStatus::MENUNGGU_DAFTAR_ULANG->value] ?? 0) + ($rawStatusCounts[SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value] ?? 0),
                'color' => 'emerald',
                'status_key' => SpmbStatus::DITERIMA->value,
            ],
            'resmi_terdaftar' => [
                'label' => 'Resmi Terdaftar',
                'count' => $rawStatusCounts[SpmbStatus::RESMI_TERDAFTAR->value] ?? 0,
                'color' => 'teal',
                'status_key' => SpmbStatus::RESMI_TERDAFTAR->value,
            ],
            'ditolak' => [
                'label' => 'Ditolak',
                'count' => $rawStatusCounts[SpmbStatus::DITOLAK->value] ?? 0,
                'color' => 'rose',
                'status_key' => SpmbStatus::DITOLAK->value,
            ],
            'mengundurkan_diri' => [
                'label' => 'Mengundurkan Diri',
                'count' => $rawStatusCounts[SpmbStatus::MENGUNDURKAN_DIRI->value] ?? 0,
                'color' => 'slate',
                'status_key' => SpmbStatus::MENGUNDURKAN_DIRI->value,
            ],
        ];

        // Baris 3: Sebaran Data Jurusan (Cukup jumlah saja)
        $jurusanStats = MasterJurusan::aktif()
            ->withCount(['calonSiswa' => fn($q) => $q->tanpaDemo()])
            ->orderBy('kode')
            ->get();

        // Baris 4: Timeline Pendaftar (5 Mode & Milestones)
        $timeline = $metricsService->getTimelineClean();

        // Header Info: Pengaturan SPMB & Gelombang Aktif
        $pengaturan = PengaturanSpmb::getSettings();
        $gelombangAktif = MasterGelombang::aktif()->first()
            ?? MasterGelombang::where('periode_mulai', '<=', now())->where('periode_selesai', '>=', now())->first()
            ?? MasterGelombang::orderBy('periode_mulai')->first();

        // Baris 5 (Kiri): Status Periode & Gelombang Pendaftaran
        $gelombangList = MasterGelombang::withCount(['calonSiswa' => fn($q) => $q->tanpaDemo()])
            ->orderBy('periode_mulai')
            ->get();

        // Baris 5 (Kanan): Kas & Keuangan SPMB
        $kasSeleksi = (float) PembayaranSeleksi::where('status', 'DIVERIFIKASI')->sum('nominal_dibayar');
        $kasDaftarUlang = (float) PembayaranDaftarUlang::where('status', 'DIVERIFIKASI')->sum('nominal_dibayar');
        $totalTagihanDaftarUlang = (float) Tagihan::sum('total_netto');
        $sisaPiutang = max(0, $totalTagihanDaftarUlang - $kasDaftarUlang);

        $keuangan = [
            'kas_seleksi' => $kasSeleksi,
            'kas_daftar_ulang' => $kasDaftarUlang,
            'total_kas_masuk' => $kasSeleksi + $kasDaftarUlang,
            'total_tagihan' => $totalTagihanDaftarUlang,
            'sisa_piutang' => $sisaPiutang,
        ];

        // Baris 6 (Kiri): Saluran Referensi & Promosi (Top & Seluruh Data untuk Modal)
        $demografi = $metricsService->getDemografiData();
        $referensiStats = $demografi['referensi_stats'] ?? collect();

        // Baris 6 (Kanan): Top Sekolah Asal & Seluruh Data untuk Modal
        $topSekolah = $metricsService->getTopAsalSekolah(5);
        $allSekolah = $metricsService->getTopAsalSekolah(null);

        // Baris 7 (Kiri): Pendaftar Terbaru (10 data)
        $recentCandidates = CalonSiswa::tanpaDemo()->with(['jurusan', 'program', 'gelombang'])
            ->latest('id')
            ->take(10)
            ->get();

        // Baris 7 (Kanan): Aktivitas Terkini (Audit Log Sistem)
        $recentActivities = Activity::with('causer')
            ->latest('id')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'pengaturan',
            'gelombangAktif',
            'baris1',
            'statusCounts',
            'jurusanStats',
            'timeline',
            'gelombangList',
            'keuangan',
            'referensiStats',
            'topSekolah',
            'allSekolah',
            'recentCandidates',
            'recentActivities'
        ));
    }
}
