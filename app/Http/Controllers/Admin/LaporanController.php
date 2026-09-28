<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\Diskon;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\MasterSekolahAsal;
use App\Models\PembayaranDaftarUlang;
use App\Models\PembayaranSeleksi;
use App\Models\Tagihan;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function __construct(
        protected PdfService $pdfService
    ) {}

    /**
     * Display comprehensive executive analytics and reporting suite.
     */
    public function index(): View
    {
        $totalPendaftar = CalonSiswa::count();

        // 1. Funnel Konversi Alur SPMB
        $funnel = [
            'total_registrasi' => $totalPendaftar,
            'seleksi_terbayar' => CalonSiswa::whereNotIn('status_spmb', [
                SpmbStatus::REGISTRASI->value,
                SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI->value,
            ])->count(),
            'biodata_selesai' => CalonSiswa::whereIn('status_spmb', [
                SpmbStatus::DATA_LENGKAP->value,
                SpmbStatus::MENUNGGU_WAWANCARA->value,
                SpmbStatus::SUDAH_DIWAWANCARA->value,
                SpmbStatus::MENUNGGU_KEPUTUSAN->value,
                SpmbStatus::DITERIMA->value,
                SpmbStatus::DITOLAK->value,
                SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
                SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
                SpmbStatus::RESMI_TERDAFTAR->value,
            ])->count(),
            'telah_wawancara' => CalonSiswa::whereIn('status_spmb', [
                SpmbStatus::SUDAH_DIWAWANCARA->value,
                SpmbStatus::MENUNGGU_KEPUTUSAN->value,
                SpmbStatus::DITERIMA->value,
                SpmbStatus::DITOLAK->value,
                SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
                SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
                SpmbStatus::RESMI_TERDAFTAR->value,
            ])->count(),
            'diterima' => CalonSiswa::whereIn('status_spmb', [
                SpmbStatus::DITERIMA->value,
                SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
                SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
                SpmbStatus::RESMI_TERDAFTAR->value,
            ])->count(),
            'resmi_terdaftar' => CalonSiswa::where('status_spmb', SpmbStatus::RESMI_TERDAFTAR->value)->count(),
        ];

        // 2. Rekapitulasi per Kompetensi Keahlian
        $targetQuota = 72;
        $rekapJurusan = MasterJurusan::aktif()->get()->map(function ($j) use ($targetQuota) {
            $total = CalonSiswa::where('jurusan_id', $j->id)->count();
            $diterima = CalonSiswa::where('jurusan_id', $j->id)
                ->whereIn('status_spmb', [
                    SpmbStatus::DITERIMA->value,
                    SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
                    SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
                    SpmbStatus::RESMI_TERDAFTAR->value,
                ])->count();
            $ditolak = CalonSiswa::where('jurusan_id', $j->id)->where('status_spmb', SpmbStatus::DITOLAK->value)->count();
            $mengundurkanDiri = CalonSiswa::where('jurusan_id', $j->id)->where('status_spmb', SpmbStatus::MENGUNDURKAN_DIRI->value)->count();
            $resmi = CalonSiswa::where('jurusan_id', $j->id)->where('status_spmb', SpmbStatus::RESMI_TERDAFTAR->value)->count();

            $persen = $targetQuota > 0 ? round(($diterima / $targetQuota) * 100, 1) : 0;

            return [
                'id' => $j->id,
                'kode' => $j->kode,
                'nama' => $j->nama_jurusan,
                'kuota' => $targetQuota,
                'pendaftar' => $total,
                'diterima' => $diterima,
                'ditolak' => $ditolak,
                'mengundurkan_diri' => $mengundurkanDiri,
                'resmi' => $resmi,
                'sisa' => max(0, $targetQuota - $diterima),
                'persentase' => min(100, $persen),
            ];
        });

        // 3. Rekapitulasi Keuangan SPMB
        $totalTagihanSeleksi = (float) PembayaranSeleksi::sum('nominal_tagihan');
        $kasSeleksiMasuk = (float) PembayaranSeleksi::where('status', 'DIVERIFIKASI')->sum('nominal_dibayar');
        $totalTagihanDaftarUlang = (float) Tagihan::sum('total_netto');
        $kasDaftarUlangMasuk = (float) PembayaranDaftarUlang::where('status', 'DIVERIFIKASI')->sum('nominal_dibayar');
        $totalDiskon = (float) Diskon::sum('nominal_potongan');
        $sisaPiutang = max(0, $totalTagihanDaftarUlang - $kasDaftarUlangMasuk);

        $keuangan = [
            'tagihan_seleksi' => $totalTagihanSeleksi,
            'kas_seleksi' => $kasSeleksiMasuk,
            'tagihan_daftar_ulang' => $totalTagihanDaftarUlang,
            'kas_daftar_ulang' => $kasDaftarUlangMasuk,
            'total_kas_masuk' => $kasSeleksiMasuk + $kasDaftarUlangMasuk,
            'total_diskon' => $totalDiskon,
            'sisa_piutang' => $sisaPiutang,
        ];

        // 4. Asal Sekolah Terbanyak (Top 5 Feeder Schools)
        $topSchools = MasterSekolahAsal::withCount('calonSiswa')
            ->orderByDesc('calon_siswa_count')
            ->take(5)
            ->get();

        return view('admin.laporan.index', compact(
            'funnel',
            'rekapJurusan',
            'keuangan',
            'topSchools'
        ));
    }

    /**
     * Export Rekapitulasi Laporan PDF.
     */
    public function exportRekapPdf(Request $request): Response
    {
        $query = CalonSiswa::with(['jurusan', 'program', 'gelombang', 'sekolahAsal'])
            ->orderBy('jurusan_id')
            ->orderBy('nama_lengkap');

        if ($jurusanId = $request->input('jurusan_id')) {
            $query->where('jurusan_id', $jurusanId);
        }

        $candidates = $query->get();
        $filters = [];
        if ($jurusanId) {
            $filters['jurusan_nama'] = MasterJurusan::find($jurusanId)?->nama_jurusan;
        }

        $pdf = $this->pdfService->generateRekapCalonSiswa($candidates, $filters);

        return $pdf->download('Rekapitulasi_SPMB_' . date('Ymd_His') . '.pdf');
    }
}
