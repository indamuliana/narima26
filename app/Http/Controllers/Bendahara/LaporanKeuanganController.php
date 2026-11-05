<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\PembayaranDaftarUlang;
use App\Models\PembayaranSeleksi;
use App\Models\Tagihan;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanKeuanganController extends Controller
{
    /**
     * Tampilkan Laporan Keuangan Terpadu Siswa & Pembukuan SPMB.
     */
    public function index(Request $request): View
    {
        $gelombangId = $request->input('gelombang_id');
        $jurusanId = $request->input('jurusan_id');
        $statusKelunasan = $request->input('status_kelunasan');
        $search = $request->input('q');

        $gelombangList = MasterGelombang::orderBy('periode_mulai')->get();
        $jurusanList = MasterJurusan::aktif()->get();

        $allRows = $this->buildReportData($gelombangId, $jurusanId, $search, $statusKelunasan);

        // Agregat Summary
        $summary = [
            'total_siswa' => $allRows->count(),
            'total_kewajiban' => (float) $allRows->sum('total_kewajiban'),
            'total_terbayar' => (float) $allRows->sum('total_terbayar'),
            'total_piutang' => (float) $allRows->sum('sisa_piutang'),
            'tingkat_pelunasan' => $allRows->sum('total_kewajiban') > 0
                ? round(($allRows->sum('total_terbayar') / $allRows->sum('total_kewajiban')) * 100, 1)
                : 0,
        ];

        // Paginate collection
        $perPage = 20;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginatedRows = new LengthAwarePaginator(
            $allRows->forPage($page, $perPage),
            $allRows->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('bendahara.laporan.index', compact(
            'paginatedRows',
            'summary',
            'gelombangList',
            'jurusanList',
            'gelombangId',
            'jurusanId',
            'statusKelunasan',
            'search'
        ));
    }

    /**
     * Export Laporan Keuangan Terpadu ke format PDF Resmi Landscape.
     */
    public function exportPdf(Request $request, PdfService $pdfService): Response
    {
        $gelombangId = $request->input('gelombang_id');
        $jurusanId = $request->input('jurusan_id');
        $statusKelunasan = $request->input('status_kelunasan');
        $search = $request->input('q');

        $rows = $this->buildReportData($gelombangId, $jurusanId, $search, $statusKelunasan);

        $summary = [
            'total_siswa' => $rows->count(),
            'total_kewajiban' => (float) $rows->sum('total_kewajiban'),
            'total_terbayar' => (float) $rows->sum('total_terbayar'),
            'total_piutang' => (float) $rows->sum('sisa_piutang'),
            'tingkat_pelunasan' => $rows->sum('total_kewajiban') > 0
                ? round(($rows->sum('total_terbayar') / $rows->sum('total_kewajiban')) * 100, 1)
                : 0,
        ];

        $filterInfo = [
            'gelombang' => $gelombangId ? MasterGelombang::find($gelombangId)?->nama_gelombang : 'Semua Gelombang',
            'jurusan' => $jurusanId ? MasterJurusan::find($jurusanId)?->nama_jurusan : 'Semua Jurusan',
            'status' => $statusKelunasan ?: 'Semua Status',
        ];

        $pdf = $pdfService->renderPdf('bendahara.laporan.pdf', [
            'rows' => $rows,
            'summary' => $summary,
            'filterInfo' => $filterInfo,
            'tanggalCetak' => now()->translatedFormat('d F Y H:i'),
        ], 'a4', 'landscape');

        return $pdf->download('Laporan_Keuangan_SPMB_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Export Laporan Keuangan Terpadu ke format Spreadsheet Excel (.xls).
     */
    public function exportXls(Request $request): StreamedResponse
    {
        $gelombangId = $request->input('gelombang_id');
        $jurusanId = $request->input('jurusan_id');
        $statusKelunasan = $request->input('status_kelunasan');
        $search = $request->input('q');

        $rows = $this->buildReportData($gelombangId, $jurusanId, $search, $statusKelunasan);

        $filename = 'Laporan_Keuangan_SPMB_' . date('Ymd_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($rows) {
            $output = fopen('php://output', 'w');

            fwrite($output, "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">\r\n");
            fwrite($output, "<head>\r\n");
            fwrite($output, "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\r\n");
            fwrite($output, "<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Laporan Keuangan SPMB</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->\r\n");
            fwrite($output, "<style>\r\n");
            fwrite($output, "body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }\r\n");
            fwrite($output, "table { border-collapse: collapse; width: 100%; }\r\n");
            fwrite($output, "th { background-color: #0f172a; color: #ffffff; border: 1px solid #334155; padding: 8px 10px; font-weight: bold; text-align: center; }\r\n");
            fwrite($output, "td { border: 1px solid #cbd5e1; padding: 6px 8px; vertical-align: middle; }\r\n");
            fwrite($output, ".text-center { text-align: center; }\r\n");
            fwrite($output, ".text-right { text-align: right; }\r\n");
            fwrite($output, ".font-bold { font-weight: bold; }\r\n");
            fwrite($output, ".bg-header-sub { background-color: #1e293b; color: #ffffff; }\r\n");
            fwrite($output, ".bg-total { background-color: #f1f5f9; font-weight: bold; }\r\n");
            fwrite($output, ".num { mso-number-format:'\#\,\#\#0'; }\r\n");
            fwrite($output, "</style>\r\n");
            fwrite($output, "</head>\r\n<body>\r\n");

            fwrite($output, "<h2 style='margin-bottom: 4px;'>LAPORAN KEUANGAN & PEMBUKUAN SPMB</h2>\r\n");
            fwrite($output, "<p style='margin-top: 0; color: #475569;'>Tanggal Ekspor: " . date('d/m/Y H:i') . "</p>\r\n");

            fwrite($output, "<table>\r\n");
            fwrite($output, "<thead>\r\n");
            fwrite($output, "<tr>\r\n");
            fwrite($output, "<th rowspan='2'>No</th>\r\n");
            fwrite($output, "<th rowspan='2'>No. Pendaftaran</th>\r\n");
            fwrite($output, "<th rowspan='2'>Nama Calon Murid</th>\r\n");
            fwrite($output, "<th rowspan='2'>Jurusan</th>\r\n");
            fwrite($output, "<th rowspan='2'>Gelombang</th>\r\n");
            fwrite($output, "<th colspan='3' class='bg-header-sub'>Biaya Seleksi</th>\r\n");
            fwrite($output, "<th colspan='3' class='bg-header-sub'>Tagihan #1 (Pendidikan: DSP & SPP)</th>\r\n");
            fwrite($output, "<th colspan='3' class='bg-header-sub'>Tagihan #2 (Paket Seragam)</th>\r\n");
            fwrite($output, "<th colspan='3' class='bg-header-sub'>Rekapitulasi Total</th>\r\n");
            fwrite($output, "<th rowspan='2'>Status Kelunasan</th>\r\n");
            fwrite($output, "</tr>\r\n");
            fwrite($output, "<tr>\r\n");
            // Biaya Seleksi
            fwrite($output, "<th>Tagihan</th><th>Terbayar</th><th>Status</th>\r\n");
            // Pendidikan
            fwrite($output, "<th>Tagihan Netto</th><th>Terbayar</th><th>Sisa</th>\r\n");
            // Seragam
            fwrite($output, "<th>Tagihan Netto</th><th>Terbayar</th><th>Sisa</th>\r\n");
            // Rekap Total
            fwrite($output, "<th>Total Kewajiban</th><th>Total Terbayar</th><th>Sisa Piutang</th>\r\n");
            fwrite($output, "</tr>\r\n");
            fwrite($output, "</thead>\r\n");
            fwrite($output, "<tbody>\r\n");

            $no = 1;
            $totSel = 0; $totSelBayar = 0;
            $totEdu = 0; $totEduBayar = 0; $totEduSisa = 0;
            $totSrg = 0; $totSrgBayar = 0; $totSrgSisa = 0;
            $totKewajiban = 0; $totTerbayar = 0; $totPiutang = 0;

            foreach ($rows as $r) {
                $totSel += $r['tagihan_sel'];
                $totSelBayar += $r['bayar_sel'];
                $totEdu += $r['netto_edu'];
                $totEduBayar += $r['bayar_edu'];
                $totEduSisa += $r['sisa_edu'];
                $totSrg += $r['netto_srg'];
                $totSrgBayar += $r['bayar_srg'];
                $totSrgSisa += $r['sisa_srg'];
                $totKewajiban += $r['total_kewajiban'];
                $totTerbayar += $r['total_terbayar'];
                $totPiutang += $r['sisa_piutang'];

                $s = $r['calon_siswa'];
                fwrite($output, "<tr>\r\n");
                fwrite($output, "<td class='text-center'>" . $no++ . "</td>\r\n");
                fwrite($output, "<td class='text-center'>'" . htmlspecialchars($s->nomor_pendaftaran ?? '-') . "</td>\r\n");
                fwrite($output, "<td>" . htmlspecialchars($s->nama_lengkap ?? '-') . "</td>\r\n");
                fwrite($output, "<td>" . htmlspecialchars($s->jurusan?->nama_jurusan ?? '-') . "</td>\r\n");
                fwrite($output, "<td class='text-center'>" . htmlspecialchars($s->gelombang?->nama_gelombang ?? '-') . "</td>\r\n");

                // Seleksi
                fwrite($output, "<td class='text-right num'>" . $r['tagihan_sel'] . "</td>\r\n");
                fwrite($output, "<td class='text-right num'>" . $r['bayar_sel'] . "</td>\r\n");
                fwrite($output, "<td class='text-center'>" . htmlspecialchars($r['status_sel']) . "</td>\r\n");

                // Pendidikan
                fwrite($output, "<td class='text-right num'>" . $r['netto_edu'] . "</td>\r\n");
                fwrite($output, "<td class='text-right num'>" . $r['bayar_edu'] . "</td>\r\n");
                fwrite($output, "<td class='text-right num'>" . $r['sisa_edu'] . "</td>\r\n");

                // Seragam
                fwrite($output, "<td class='text-right num'>" . $r['netto_srg'] . "</td>\r\n");
                fwrite($output, "<td class='text-right num'>" . $r['bayar_srg'] . "</td>\r\n");
                fwrite($output, "<td class='text-right num'>" . $r['sisa_srg'] . "</td>\r\n");

                // Total
                fwrite($output, "<td class='text-right num font-bold'>" . $r['total_kewajiban'] . "</td>\r\n");
                fwrite($output, "<td class='text-right num font-bold' style='color: #059669;'>" . $r['total_terbayar'] . "</td>\r\n");
                fwrite($output, "<td class='text-right num font-bold' style='color: #dc2626;'>" . $r['sisa_piutang'] . "</td>\r\n");
                fwrite($output, "<td class='text-center font-bold'>" . htmlspecialchars($r['status_kelunasan']) . "</td>\r\n");
                fwrite($output, "</tr>\r\n");
            }

            // Baris Total
            fwrite($output, "<tr class='bg-total'>\r\n");
            fwrite($output, "<td colspan='5' class='text-center font-bold'>TOTAL KESELURUHAN</td>\r\n");
            fwrite($output, "<td class='text-right num font-bold'>" . $totSel . "</td>\r\n");
            fwrite($output, "<td class='text-right num font-bold'>" . $totSelBayar . "</td>\r\n");
            fwrite($output, "<td class='text-center'>-</td>\r\n");
            fwrite($output, "<td class='text-right num font-bold'>" . $totEdu . "</td>\r\n");
            fwrite($output, "<td class='text-right num font-bold'>" . $totEduBayar . "</td>\r\n");
            fwrite($output, "<td class='text-right num font-bold'>" . $totEduSisa . "</td>\r\n");
            fwrite($output, "<td class='text-right num font-bold'>" . $totSrg . "</td>\r\n");
            fwrite($output, "<td class='text-right num font-bold'>" . $totSrgBayar . "</td>\r\n");
            fwrite($output, "<td class='text-right num font-bold'>" . $totSrgSisa . "</td>\r\n");
            fwrite($output, "<td class='text-right num font-bold'>" . $totKewajiban . "</td>\r\n");
            fwrite($output, "<td class='text-right num font-bold' style='color: #059669;'>" . $totTerbayar . "</td>\r\n");
            fwrite($output, "<td class='text-right num font-bold' style='color: #dc2626;'>" . $totPiutang . "</td>\r\n");
            fwrite($output, "<td class='text-center font-bold'>-</td>\r\n");
            fwrite($output, "</tr>\r\n");

            fwrite($output, "</tbody>\r\n");
            fwrite($output, "</table>\r\n");
            fwrite($output, "</body>\r\n</html>");

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper privat untuk query dan normalisasi baris laporan keuangan per siswa.
     */
    protected function buildReportData(?int $gelombangId, ?int $jurusanId, ?string $search, ?string $statusKelunasan): Collection
    {
        $query = CalonSiswa::tanpaDemo()
            ->with([
                'jurusan',
                'program',
                'gelombang',
                'pembayaranSeleksi',
                'tagihan.pembayaran',
                'pembayaranDaftarUlang',
            ]);

        if (!empty($gelombangId)) {
            $query->where('gelombang_id', $gelombangId);
        }

        if (!empty($jurusanId)) {
            $query->where('jurusan_id', $jurusanId);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $candidates = $query->orderBy('nomor_pendaftaran')->get();

        $rows = $candidates->map(function ($s) {
            $seleksi = $s->pembayaranSeleksi;
            $tagihanEdu = $s->tagihan->firstWhere('jenis_tagihan', Tagihan::JENIS_DAFTAR_ULANG);
            $tagihanSrg = $s->tagihan->firstWhere('jenis_tagihan', Tagihan::JENIS_SERAGAM);

            $tagihanSel = $seleksi ? (float) $seleksi->nominal_tagihan : 200000;
            $bayarSel = ($seleksi && $seleksi->status === PembayaranSeleksi::STATUS_DIVERIFIKASI) ? (float) $seleksi->nominal_dibayar : 0;
            $statusSel = $seleksi ? $seleksi->status : 'BELUM_BAYAR';

            $nettoEdu = $tagihanEdu ? (float) $tagihanEdu->total_netto : 0;
            $bayarEdu = $tagihanEdu ? (float) $tagihanEdu->pembayaran->where('status', PembayaranDaftarUlang::STATUS_DIVERIFIKASI)->sum('nominal_dibayar') : 0;
            $sisaEdu = max(0, $nettoEdu - $bayarEdu);

            $nettoSrg = $tagihanSrg ? (float) $tagihanSrg->total_netto : 0;
            $bayarSrg = $tagihanSrg ? (float) $tagihanSrg->pembayaran->where('status', PembayaranDaftarUlang::STATUS_DIVERIFIKASI)->sum('nominal_dibayar') : 0;
            $sisaSrg = max(0, $nettoSrg - $bayarSrg);

            $totalKewajiban = $tagihanSel + $nettoEdu + $nettoSrg;
            $totalTerbayar = $bayarSel + $bayarEdu + $bayarSrg;
            $sisaPiutang = max(0, $totalKewajiban - $totalTerbayar);

            if ($nettoEdu > 0 || $nettoSrg > 0) {
                if ($sisaEdu <= 0 && $sisaSrg <= 0) {
                    $status = 'LUNAS';
                } elseif (($bayarEdu + $bayarSrg) > 0) {
                    $status = 'CICILAN';
                } else {
                    $status = 'BELUM_BAYAR';
                }
            } else {
                $status = ($bayarSel >= $tagihanSel) ? 'SELEKSI_LUNAS' : 'BELUM_BAYAR';
            }

            return [
                'calon_siswa' => $s,
                'tagihan_sel' => $tagihanSel,
                'bayar_sel' => $bayarSel,
                'status_sel' => $statusSel,
                'netto_edu' => $nettoEdu,
                'bayar_edu' => $bayarEdu,
                'sisa_edu' => $sisaEdu,
                'tagihan_edu_id' => $tagihanEdu?->id,
                'netto_srg' => $nettoSrg,
                'bayar_srg' => $bayarSrg,
                'sisa_srg' => $sisaSrg,
                'tagihan_srg_id' => $tagihanSrg?->id,
                'total_kewajiban' => $totalKewajiban,
                'total_terbayar' => $totalTerbayar,
                'sisa_piutang' => $sisaPiutang,
                'status_kelunasan' => $status,
            ];
        });

        if (!empty($statusKelunasan)) {
            $rows = $rows->filter(fn($r) => $r['status_kelunasan'] === $statusKelunasan)->values();
        }

        return $rows;
    }
}
