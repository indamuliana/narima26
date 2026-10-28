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
        $totalPendaftar = CalonSiswa::tanpaDemo()->count();
        $unggulanAll = CalonSiswa::tanpaDemo()->whereHas('program', fn($q) => $q->where('kode', 'UGG')->orWhere('nama', 'like', '%Unggulan%'))->count();
        $regulerAll = CalonSiswa::tanpaDemo()->whereHas('program', fn($q) => $q->where('kode', 'REG')->orWhere('nama', 'like', '%Reguler%'))->count();
        $unggulanLaki = CalonSiswa::tanpaDemo()->whereHas('program', fn($q) => $q->where('kode', 'UGG')->orWhere('nama', 'like', '%Unggulan%'))->where('jenis_kelamin', 'L')->count();
        $unggulanPerempuan = CalonSiswa::tanpaDemo()->whereHas('program', fn($q) => $q->where('kode', 'UGG')->orWhere('nama', 'like', '%Unggulan%'))->where('jenis_kelamin', 'P')->count();
        $regulerLaki = CalonSiswa::tanpaDemo()->whereHas('program', fn($q) => $q->where('kode', 'REG')->orWhere('nama', 'like', '%Reguler%'))->where('jenis_kelamin', 'L')->count();
        $regulerPerempuan = CalonSiswa::tanpaDemo()->whereHas('program', fn($q) => $q->where('kode', 'REG')->orWhere('nama', 'like', '%Reguler%'))->where('jenis_kelamin', 'P')->count();

        $baris1 = [
            'total_pendaftar' => $totalPendaftar,
            'total_unggulan' => $unggulanAll,
            'total_reguler' => $regulerAll,
            'unggulan_laki' => $unggulanLaki,
            'unggulan_perempuan' => $unggulanPerempuan,
            'reguler_laki' => $regulerLaki,
            'reguler_perempuan' => $regulerPerempuan,
        ];

        // Baris 2: 8 Status Kunci Alur SPMB
        $statusCounts = [
            'menunggu_bayar' => [
                'label' => 'Menunggu Bayar Seleksi',
                'count' => CalonSiswa::tanpaDemo()->whereIn('status_spmb', [SpmbStatus::REGISTRASI->value, SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI->value])->count(),
                'color' => 'amber',
                'status_key' => SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI->value,
            ],
            'melengkapi_data' => [
                'label' => 'Melengkapi Data & Dokumen',
                'count' => CalonSiswa::tanpaDemo()->whereIn('status_spmb', [SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI->value, SpmbStatus::MELENGKAPI_DATA->value, SpmbStatus::DATA_LENGKAP->value])->count(),
                'color' => 'orange',
                'status_key' => SpmbStatus::MELENGKAPI_DATA->value,
            ],
            'menunggu_wawancara' => [
                'label' => 'Menunggu Wawancara',
                'count' => CalonSiswa::tanpaDemo()->where('status_spmb', SpmbStatus::MENUNGGU_WAWANCARA->value)->count(),
                'color' => 'cyan',
                'status_key' => SpmbStatus::MENUNGGU_WAWANCARA->value,
            ],
            'menunggu_keputusan' => [
                'label' => 'Menunggu Sidang / Keputusan',
                'count' => CalonSiswa::tanpaDemo()->whereIn('status_spmb', [SpmbStatus::SUDAH_DIWAWANCARA->value, SpmbStatus::MENUNGGU_KEPUTUSAN->value])->count(),
                'color' => 'indigo',
                'status_key' => SpmbStatus::MENUNGGU_KEPUTUSAN->value,
            ],
            'diterima' => [
                'label' => 'Lulus / Menunggu Daftar Ulang',
                'count' => CalonSiswa::tanpaDemo()->whereIn('status_spmb', [SpmbStatus::DITERIMA->value, SpmbStatus::MENUNGGU_DAFTAR_ULANG->value, SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value])->count(),
                'color' => 'emerald',
                'status_key' => SpmbStatus::DITERIMA->value,
            ],
            'resmi_terdaftar' => [
                'label' => 'Resmi Terdaftar',
                'count' => CalonSiswa::tanpaDemo()->where('status_spmb', SpmbStatus::RESMI_TERDAFTAR->value)->count(),
                'color' => 'teal',
                'status_key' => SpmbStatus::RESMI_TERDAFTAR->value,
            ],
            'ditolak' => [
                'label' => 'Ditolak',
                'count' => CalonSiswa::tanpaDemo()->where('status_spmb', SpmbStatus::DITOLAK->value)->count(),
                'color' => 'rose',
                'status_key' => SpmbStatus::DITOLAK->value,
            ],
            'mengundurkan_diri' => [
                'label' => 'Mengundurkan Diri',
                'count' => CalonSiswa::tanpaDemo()->where('status_spmb', SpmbStatus::MENGUNDURKAN_DIRI->value)->count(),
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
