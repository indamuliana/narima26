<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\MasterJurusan;
use App\Models\PembayaranDaftarUlang;
use App\Models\PembayaranSeleksi;
use App\Models\Tagihan;
use App\Services\DashboardMetricsService;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardMetricsService $metricsService
    ) {}

    /**
     * Tampilkan Dashboard Finansial Komprehensif Bendahara SPMB.
     */
    public function index(): View
    {
        $seleksiMasuk = (float) PembayaranSeleksi::where('status', PembayaranSeleksi::STATUS_DIVERIFIKASI)->sum('nominal_dibayar');
        $seleksiPending = PembayaranSeleksi::where('status', PembayaranSeleksi::STATUS_PENDING)->count();

        $duMasuk = (float) PembayaranDaftarUlang::where('status', PembayaranDaftarUlang::STATUS_DIVERIFIKASI)->sum('nominal_dibayar');
        $duPending = PembayaranDaftarUlang::where('status', PembayaranDaftarUlang::STATUS_PENDING)->count();

        $targetTagihanNetto = (float) Tagihan::sum('total_netto');
        $sisaPiutang = max(0, $targetTagihanNetto - $duMasuk);
        $collectionRate = $targetTagihanNetto > 0 ? round(($duMasuk / $targetTagihanNetto) * 100, 1) : 0;

        $stats = [
            'total_kas_masuk' => $seleksiMasuk + $duMasuk,
            'seleksi_masuk' => $seleksiMasuk,
            'seleksi_pending' => $seleksiPending,
            'daftar_ulang_masuk' => $duMasuk,
            'daftar_ulang_pending' => $duPending,
            'target_tagihan_netto' => $targetTagihanNetto,
            'sisa_piutang' => $sisaPiutang,
            'collection_rate' => $collectionRate,
            'total_tagihan' => Tagihan::count(),
            'antrean_verifikasi' => $seleksiPending + $duPending,
        ];

        // Arus Kas Cepat berdasarkan periode
        $today = Carbon::today();
        $startWeek = Carbon::now()->startOfWeek();
        $startMonth = Carbon::now()->startOfMonth();

        $kasHariIni = (float) PembayaranSeleksi::where('status', PembayaranSeleksi::STATUS_DIVERIFIKASI)->whereDate('created_at', $today)->sum('nominal_dibayar')
            + (float) PembayaranDaftarUlang::where('status', PembayaranDaftarUlang::STATUS_DIVERIFIKASI)->whereDate('created_at', $today)->sum('nominal_dibayar');

        $kasMingguIni = (float) PembayaranSeleksi::where('status', PembayaranSeleksi::STATUS_DIVERIFIKASI)->where('created_at', '>=', $startWeek)->sum('nominal_dibayar')
            + (float) PembayaranDaftarUlang::where('status', PembayaranDaftarUlang::STATUS_DIVERIFIKASI)->where('created_at', '>=', $startWeek)->sum('nominal_dibayar');

        $kasBulanIni = (float) PembayaranSeleksi::where('status', PembayaranSeleksi::STATUS_DIVERIFIKASI)->where('created_at', '>=', $startMonth)->sum('nominal_dibayar')
            + (float) PembayaranDaftarUlang::where('status', PembayaranDaftarUlang::STATUS_DIVERIFIKASI)->where('created_at', '>=', $startMonth)->sum('nominal_dibayar');

        $arusKas = [
            'hari_ini' => $kasHariIni,
            'minggu_ini' => $kasMingguIni,
            'bulan_ini' => $kasBulanIni,
        ];

        // Breakdown Kas per Pos Biaya
        $duPendidikanMasuk = (float) PembayaranDaftarUlang::where('status', PembayaranDaftarUlang::STATUS_DIVERIFIKASI)
            ->whereHas('tagihan', fn($q) => $q->where('jenis_tagihan', Tagihan::JENIS_DAFTAR_ULANG))
            ->sum('nominal_dibayar');

        $duSeragamMasuk = (float) PembayaranDaftarUlang::where('status', PembayaranDaftarUlang::STATUS_DIVERIFIKASI)
            ->whereHas('tagihan', fn($q) => $q->where('jenis_tagihan', Tagihan::JENIS_SERAGAM))
            ->sum('nominal_dibayar');

        $posBiaya = [
            'seleksi' => $seleksiMasuk,
            'pendidikan' => $duPendidikanMasuk,
            'seragam' => $duSeragamMasuk,
        ];

        // Status Kelunasan Siswa Daftar Ulang
        $siswaTagihan = Tagihan::select('calon_siswa_id')
            ->groupBy('calon_siswa_id')
            ->get();

        $lunasCount = 0;
        $cicilanCount = 0;
        $belumBayarCount = 0;

        foreach ($siswaTagihan as $st) {
            $totalTagihanSiswa = (float) Tagihan::where('calon_siswa_id', $st->calon_siswa_id)->sum('total_netto');
            $totalBayarSiswa = (float) PembayaranDaftarUlang::where('calon_siswa_id', $st->calon_siswa_id)
                ->where('status', PembayaranDaftarUlang::STATUS_DIVERIFIKASI)
                ->sum('nominal_dibayar');

            if ($totalTagihanSiswa > 0 && $totalBayarSiswa >= $totalTagihanSiswa) {
                $lunasCount++;
            } elseif ($totalBayarSiswa > 0) {
                $cicilanCount++;
            } else {
                $belumBayarCount++;
            }
        }

        $statusKelunasan = [
            'lunas' => $lunasCount,
            'cicilan' => $cicilanCount,
            'belum_bayar' => $belumBayarCount,
            'total_siswa' => $siswaTagihan->count(),
        ];

        // Capaian Keuangan per Kompetensi Keahlian (Jurusan)
        $jurusanStats = MasterJurusan::aktif()->get()->map(function ($j) {
            $target = (float) Tagihan::whereHas('calonSiswa', fn($q) => $q->where('jurusan_id', $j->id))->sum('total_netto');
            $terbayar = (float) PembayaranDaftarUlang::where('status', PembayaranDaftarUlang::STATUS_DIVERIFIKASI)
                ->whereHas('calonSiswa', fn($q) => $q->where('jurusan_id', $j->id))->sum('nominal_dibayar');
            $piutang = max(0, $target - $terbayar);
            $persen = $target > 0 ? round(($terbayar / $target) * 100, 1) : 0;

            return [
                'id' => $j->id,
                'kode' => $j->kode,
                'nama' => $j->nama_jurusan,
                'target' => $target,
                'terbayar' => $terbayar,
                'piutang' => $piutang,
                'persentase' => $persen,
            ];
        });

        // Transaksi Terbaru
        $recentSeleksi = PembayaranSeleksi::with('calonSiswa')->latest('id')->take(5)->get();
        $recentDaftarUlang = PembayaranDaftarUlang::with(['calonSiswa', 'tagihan'])->latest('id')->take(5)->get();
        $programStats = $this->metricsService->getProgramGenderStats();

        return view('bendahara.dashboard', compact(
            'stats',
            'arusKas',
            'posBiaya',
            'statusKelunasan',
            'jurusanStats',
            'recentSeleksi',
            'recentDaftarUlang',
            'programStats'
        ));
    }
}
