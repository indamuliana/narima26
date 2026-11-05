<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\Diskon;
use App\Models\MasterDiskon;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\PembayaranDaftarUlang;
use App\Models\PembayaranSeleksi;
use App\Models\Tagihan;
use App\Services\PdfService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanKeuanganController extends Controller
{
    /**
     * Tampilkan Laporan Keuangan Multi-Tab Bendahara SPMB.
     */
    public function index(Request $request): View
    {
        $validTabs = ['piutang_siswa', 'buku_kas', 'matrix_jurusan', 'diskon'];
        $activeTab = in_array($request->input('tab'), $validTabs) ? $request->input('tab') : 'piutang_siswa';

        $gelombangList = MasterGelombang::orderBy('periode_mulai')->get();
        $jurusanList = MasterJurusan::aktif()->get();
        $masterDiskonList = MasterDiskon::where('is_active', true)->get();

        $gelombangId = $request->input('gelombang_id');
        $jurusanId = $request->input('jurusan_id');
        $search = $request->input('q');

        // Parameter khusus per tab
        $statusKelunasan = $request->input('status_kelunasan');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $posKategori = $request->input('pos_kategori');
        $statusVerifikasi = $request->input('status_verifikasi', 'DIVERIFIKASI');
        $jenisDiskon = $request->input('jenis_diskon');

        $tabData = [];

        if ($activeTab === 'piutang_siswa') {
            $allRows = $this->buildPiutangSiswaData($gelombangId, $jurusanId, $search, $statusKelunasan);
            $summary = [
                'total_siswa' => $allRows->count(),
                'total_kewajiban' => (float) $allRows->sum('total_kewajiban'),
                'total_terbayar' => (float) $allRows->sum('total_terbayar'),
                'total_piutang' => (float) $allRows->sum('sisa_piutang'),
                'tingkat_pelunasan' => $allRows->sum('total_kewajiban') > 0
                    ? round(($allRows->sum('total_terbayar') / $allRows->sum('total_kewajiban')) * 100, 1)
                    : 0,
            ];
            $tabData['paginatedRows'] = $this->paginateCollection($allRows, 20, $request);
            $tabData['summary'] = $summary;

        } elseif ($activeTab === 'buku_kas') {
            $allRows = $this->buildBukuKasData($startDate, $endDate, $posKategori, $statusVerifikasi, $gelombangId, $jurusanId, $search);
            $summary = [
                'total_transaksi' => $allRows->count(),
                'total_kas_masuk' => (float) $allRows->sum('nominal'),
                'kas_seleksi' => (float) $allRows->where('pos_kode', 'SELEKSI')->sum('nominal'),
                'kas_daftar_ulang' => (float) $allRows->whereIn('pos_kode', [Tagihan::JENIS_DAFTAR_ULANG, Tagihan::JENIS_SERAGAM])->sum('nominal'),
            ];
            $tabData['paginatedRows'] = $this->paginateCollection($allRows, 25, $request);
            $tabData['summary'] = $summary;

        } elseif ($activeTab === 'matrix_jurusan') {
            $matrixRows = $this->buildMatrixJurusanData($gelombangId);
            $summary = [
                'total_target' => (float) $matrixRows->sum('total_target'),
                'total_kas_masuk' => (float) $matrixRows->sum('total_kas'),
                'total_piutang' => (float) $matrixRows->sum('sisa_piutang'),
                'rata_capaian' => $matrixRows->sum('total_target') > 0
                    ? round(($matrixRows->sum('total_kas') / $matrixRows->sum('total_target')) * 100, 1)
                    : 0,
            ];
            $tabData['matrixRows'] = $matrixRows;
            $tabData['summary'] = $summary;

        } elseif ($activeTab === 'diskon') {
            $allRows = $this->buildDiskonData($gelombangId, $jurusanId, $jenisDiskon, $search);
            $summary = [
                'total_penerima' => $allRows->count(),
                'total_diskon' => (float) $allRows->sum('nominal_potongan'),
                'rata_diskon' => $allRows->count() > 0
                    ? round($allRows->sum('nominal_potongan') / $allRows->count(), 0)
                    : 0,
            ];
            $tabData['paginatedRows'] = $this->paginateCollection($allRows, 20, $request);
            $tabData['summary'] = $summary;
        }

        return view('bendahara.laporan.index', array_merge($tabData, [
            'activeTab' => $activeTab,
            'gelombangList' => $gelombangList,
            'jurusanList' => $jurusanList,
            'masterDiskonList' => $masterDiskonList,
            'gelombangId' => $gelombangId,
            'jurusanId' => $jurusanId,
            'statusKelunasan' => $statusKelunasan,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'posKategori' => $posKategori,
            'statusVerifikasi' => $statusVerifikasi,
            'jenisDiskon' => $jenisDiskon,
            'search' => $search,
        ]));
    }

    /**
     * Export Laporan Tab Aktif ke format PDF Landscape.
     */
    public function exportPdf(Request $request, PdfService $pdfService): Response
    {
        $validTabs = ['piutang_siswa', 'buku_kas', 'matrix_jurusan', 'diskon'];
        $activeTab = in_array($request->input('tab'), $validTabs) ? $request->input('tab') : 'piutang_siswa';

        $gelombangId = $request->input('gelombang_id');
        $jurusanId = $request->input('jurusan_id');
        $search = $request->input('q');

        $statusKelunasan = $request->input('status_kelunasan');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $posKategori = $request->input('pos_kategori');
        $statusVerifikasi = $request->input('status_verifikasi', 'DIVERIFIKASI');
        $jenisDiskon = $request->input('jenis_diskon');

        $tabData = [];
        $tabTitle = 'Laporan Keuangan SPMB';

        if ($activeTab === 'piutang_siswa') {
            $rows = $this->buildPiutangSiswaData($gelombangId, $jurusanId, $search, $statusKelunasan);
            $summary = [
                'total_siswa' => $rows->count(),
                'total_kewajiban' => (float) $rows->sum('total_kewajiban'),
                'total_terbayar' => (float) $rows->sum('total_terbayar'),
                'total_piutang' => (float) $rows->sum('sisa_piutang'),
                'tingkat_pelunasan' => $rows->sum('total_kewajiban') > 0
                    ? round(($rows->sum('total_terbayar') / $rows->sum('total_kewajiban')) * 100, 1)
                    : 0,
            ];
            $tabData['rows'] = $rows;
            $tabData['summary'] = $summary;
            $tabTitle = 'Rekapitulasi_Piutang_Siswa';

        } elseif ($activeTab === 'buku_kas') {
            $rows = $this->buildBukuKasData($startDate, $endDate, $posKategori, $statusVerifikasi, $gelombangId, $jurusanId, $search);
            $summary = [
                'total_transaksi' => $rows->count(),
                'total_kas_masuk' => (float) $rows->sum('nominal'),
                'kas_seleksi' => (float) $rows->where('pos_kode', 'SELEKSI')->sum('nominal'),
                'kas_daftar_ulang' => (float) $rows->whereIn('pos_kode', [Tagihan::JENIS_DAFTAR_ULANG, Tagihan::JENIS_SERAGAM])->sum('nominal'),
            ];
            $tabData['rows'] = $rows;
            $tabData['summary'] = $summary;
            $tabTitle = 'Jurnal_Buku_Kas_Masuk';

        } elseif ($activeTab === 'matrix_jurusan') {
            $matrixRows = $this->buildMatrixJurusanData($gelombangId);
            $summary = [
                'total_target' => (float) $matrixRows->sum('total_target'),
                'total_kas_masuk' => (float) $matrixRows->sum('total_kas'),
                'total_piutang' => (float) $matrixRows->sum('sisa_piutang'),
                'rata_capaian' => $matrixRows->sum('total_target') > 0
                    ? round(($matrixRows->sum('total_kas') / $matrixRows->sum('total_target')) * 100, 1)
                    : 0,
            ];
            $tabData['matrixRows'] = $matrixRows;
            $tabData['summary'] = $summary;
            $tabTitle = 'Rekap_Pendapatan_per_Jurusan';

        } elseif ($activeTab === 'diskon') {
            $rows = $this->buildDiskonData($gelombangId, $jurusanId, $jenisDiskon, $search);
            $summary = [
                'total_penerima' => $rows->count(),
                'total_diskon' => (float) $rows->sum('nominal_potongan'),
                'rata_diskon' => $rows->count() > 0
                    ? round($rows->sum('nominal_potongan') / $rows->count(), 0)
                    : 0,
            ];
            $tabData['rows'] = $rows;
            $tabData['summary'] = $summary;
            $tabTitle = 'Rekapitulasi_Diskon_Beasiswa';
        }

        $filterInfo = [
            'gelombang' => $gelombangId ? MasterGelombang::find($gelombangId)?->nama_gelombang : 'Semua Gelombang',
            'jurusan' => $jurusanId ? MasterJurusan::find($jurusanId)?->nama_jurusan : 'Semua Jurusan',
            'status' => $statusKelunasan ?: ($statusVerifikasi ?: 'Semua Status'),
            'periode' => ($startDate && $endDate) ? ($startDate . ' s/d ' . $endDate) : 'Semua Waktu',
        ];

        $pdf = $pdfService->renderPdf('bendahara.laporan.pdf', array_merge($tabData, [
            'activeTab' => $activeTab,
            'filterInfo' => $filterInfo,
            'tanggalCetak' => now()->translatedFormat('d F Y H:i'),
        ]), 'a4', 'landscape');

        return $pdf->download($tabTitle . '_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Export Laporan Tab Aktif ke format Spreadsheet Excel (.xls).
     */
    public function exportXls(Request $request): StreamedResponse
    {
        $validTabs = ['piutang_siswa', 'buku_kas', 'matrix_jurusan', 'diskon'];
        $activeTab = in_array($request->input('tab'), $validTabs) ? $request->input('tab') : 'piutang_siswa';

        $gelombangId = $request->input('gelombang_id');
        $jurusanId = $request->input('jurusan_id');
        $search = $request->input('q');

        $statusKelunasan = $request->input('status_kelunasan');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $posKategori = $request->input('pos_kategori');
        $statusVerifikasi = $request->input('status_verifikasi', 'DIVERIFIKASI');
        $jenisDiskon = $request->input('jenis_diskon');

        $filename = 'Laporan_' . ucfirst($activeTab) . '_' . date('Ymd_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use (
            $activeTab, $gelombangId, $jurusanId, $search,
            $statusKelunasan, $startDate, $endDate, $posKategori, $statusVerifikasi, $jenisDiskon
        ) {
            $output = fopen('php://output', 'w');

            fwrite($output, "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">\r\n");
            fwrite($output, "<head>\r\n");
            fwrite($output, "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\r\n");
            fwrite($output, "<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Laporan Keuangan</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->\r\n");
            fwrite($output, "<style>\r\n");
            fwrite($output, "body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }\r\n");
            fwrite($output, "table { border-collapse: collapse; width: 100%; }\r\n");
            fwrite($output, "th { background-color: #0f172a; color: #ffffff; border: 1px solid #334155; padding: 8px 10px; font-weight: bold; text-align: center; }\r\n");
            fwrite($output, "td { border: 1px solid #cbd5e1; padding: 6px 8px; vertical-align: middle; }\r\n");
            fwrite($output, ".text-center { text-align: center; }\r\n");
            fwrite($output, ".text-right { text-align: right; }\r\n");
            fwrite($output, ".font-bold { font-weight: bold; }\r\n");
            fwrite($output, ".bg-sub { background-color: #1e293b; color: #ffffff; }\r\n");
            fwrite($output, ".bg-total { background-color: #f1f5f9; font-weight: bold; }\r\n");
            fwrite($output, ".num { mso-number-format:'\#\,\#\#0'; }\r\n");
            fwrite($output, "</style>\r\n");
            fwrite($output, "</head>\r\n<body>\r\n");

            if ($activeTab === 'piutang_siswa') {
                $rows = $this->buildPiutangSiswaData($gelombangId, $jurusanId, $search, $statusKelunasan);
                fwrite($output, "<h2>REKAPITULASI PIUTANG & STATUS CALON MURID</h2>\r\n");
                fwrite($output, "<p>Tanggal Ekspor: " . date('d/m/Y H:i') . "</p>\r\n");
                fwrite($output, "<table>\r\n<thead>\r\n<tr>\r\n");
                fwrite($output, "<th rowspan='2'>No</th><th rowspan='2'>No. Reg</th><th rowspan='2'>Nama Calon Murid</th><th rowspan='2'>Jurusan</th><th rowspan='2'>Gelombang</th>\r\n");
                fwrite($output, "<th colspan='3' class='bg-sub'>Biaya Seleksi</th><th colspan='3' class='bg-sub'>Tagihan #1 (Pendidikan)</th><th colspan='3' class='bg-sub'>Tagihan #2 (Seragam)</th>\r\n");
                fwrite($output, "<th colspan='3' class='bg-sub'>Rekapitulasi Total</th><th rowspan='2'>Status</th>\r\n</tr>\r\n<tr>\r\n");
                fwrite($output, "<th>Tagihan</th><th>Terbayar</th><th>Status</th><th>Netto</th><th>Terbayar</th><th>Sisa</th><th>Netto</th><th>Terbayar</th><th>Sisa</th><th>Kewajiban</th><th>Terbayar</th><th>Sisa Piutang</th>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n");

                $no = 1;
                $totSel = 0; $totSelBayar = 0;
                $totEdu = 0; $totEduBayar = 0; $totEduSisa = 0;
                $totSrg = 0; $totSrgBayar = 0; $totSrgSisa = 0;
                $totKewajiban = 0; $totTerbayar = 0; $totPiutang = 0;

                foreach ($rows as $r) {
                    $s = $r['calon_siswa'];
                    $totSel += $r['tagihan_sel']; $totSelBayar += $r['bayar_sel'];
                    $totEdu += $r['netto_edu']; $totEduBayar += $r['bayar_edu']; $totEduSisa += $r['sisa_edu'];
                    $totSrg += $r['netto_srg']; $totSrgBayar += $r['bayar_srg']; $totSrgSisa += $r['sisa_srg'];
                    $totKewajiban += $r['total_kewajiban']; $totTerbayar += $r['total_terbayar']; $totPiutang += $r['sisa_piutang'];

                    fwrite($output, "<tr>\r\n");
                    fwrite($output, "<td class='text-center'>" . $no++ . "</td>\r\n");
                    fwrite($output, "<td class='text-center'>'" . htmlspecialchars($s->nomor_pendaftaran ?? '-') . "</td>\r\n");
                    fwrite($output, "<td>" . htmlspecialchars($s->nama_lengkap ?? '-') . "</td>\r\n");
                    fwrite($output, "<td>" . htmlspecialchars($s->jurusan?->nama_jurusan ?? '-') . "</td>\r\n");
                    fwrite($output, "<td class='text-center'>" . htmlspecialchars($s->gelombang?->nama_gelombang ?? '-') . "</td>\r\n");
                    fwrite($output, "<td class='text-right num'>" . $r['tagihan_sel'] . "</td><td class='text-right num'>" . $r['bayar_sel'] . "</td><td class='text-center'>" . htmlspecialchars($r['status_sel']) . "</td>\r\n");
                    fwrite($output, "<td class='text-right num'>" . $r['netto_edu'] . "</td><td class='text-right num'>" . $r['bayar_edu'] . "</td><td class='text-right num'>" . $r['sisa_edu'] . "</td>\r\n");
                    fwrite($output, "<td class='text-right num'>" . $r['netto_srg'] . "</td><td class='text-right num'>" . $r['bayar_srg'] . "</td><td class='text-right num'>" . $r['sisa_srg'] . "</td>\r\n");
                    fwrite($output, "<td class='text-right num font-bold'>" . $r['total_kewajiban'] . "</td><td class='text-right num font-bold' style='color: #059669;'>" . $r['total_terbayar'] . "</td><td class='text-right num font-bold' style='color: #dc2626;'>" . $r['sisa_piutang'] . "</td>\r\n");
                    fwrite($output, "<td class='text-center font-bold'>" . htmlspecialchars($r['status_kelunasan']) . "</td>\r\n</tr>\r\n");
                }
                fwrite($output, "<tr class='bg-total'><td colspan='5' class='text-center font-bold'>TOTAL</td>");
                fwrite($output, "<td class='text-right num font-bold'>{$totSel}</td><td class='text-right num font-bold'>{$totSelBayar}</td><td>-</td>");
                fwrite($output, "<td class='text-right num font-bold'>{$totEdu}</td><td class='text-right num font-bold'>{$totEduBayar}</td><td class='text-right num font-bold'>{$totEduSisa}</td>");
                fwrite($output, "<td class='text-right num font-bold'>{$totSrg}</td><td class='text-right num font-bold'>{$totSrgBayar}</td><td class='text-right num font-bold'>{$totSrgSisa}</td>");
                fwrite($output, "<td class='text-right num font-bold'>{$totKewajiban}</td><td class='text-right num font-bold' style='color: #059669;'>{$totTerbayar}</td><td class='text-right num font-bold' style='color: #dc2626;'>{$totPiutang}</td><td>-</td></tr>\r\n");

            } elseif ($activeTab === 'buku_kas') {
                $rows = $this->buildBukuKasData($startDate, $endDate, $posKategori, $statusVerifikasi, $gelombangId, $jurusanId, $search);
                fwrite($output, "<h2>JURNAL BUKU KAS PENERIMAAN SPMB</h2>\r\n");
                fwrite($output, "<p>Tanggal Ekspor: " . date('d/m/Y H:i') . "</p>\r\n");
                fwrite($output, "<table>\r\n<thead>\r\n<tr>\r\n");
                fwrite($output, "<th>No</th><th>Tanggal Bayar</th><th>Waktu Verifikasi</th><th>No. Referensi / Kwitansi</th><th>No. Reg</th><th>Nama Calon Murid</th><th>Jurusan</th><th>Pos Penerimaan</th><th>Metode Bayar</th><th>Nominal Masuk</th><th>Status</th><th>Verifikator</th>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n");

                $no = 1;
                $totNominal = 0;
                foreach ($rows as $r) {
                    $s = $r['calon_siswa'];
                    $totNominal += $r['nominal'];
                    $tglBayar = $r['tanggal_bayar'] ? Carbon::parse($r['tanggal_bayar'])->format('d/m/Y') : '-';
                    $waktuVerif = $r['waktu'] ? Carbon::parse($r['waktu'])->format('d/m/Y H:i') : '-';

                    fwrite($output, "<tr>\r\n");
                    fwrite($output, "<td class='text-center'>" . $no++ . "</td>\r\n");
                    fwrite($output, "<td class='text-center'>" . $tglBayar . "</td>\r\n");
                    fwrite($output, "<td class='text-center'>" . $waktuVerif . "</td>\r\n");
                    fwrite($output, "<td class='text-center'>'" . htmlspecialchars($r['nomor_transaksi']) . "</td>\r\n");
                    fwrite($output, "<td class='text-center'>'" . htmlspecialchars($s?->nomor_pendaftaran ?? '-') . "</td>\r\n");
                    fwrite($output, "<td>" . htmlspecialchars($s?->nama_lengkap ?? '-') . "</td>\r\n");
                    fwrite($output, "<td>" . htmlspecialchars($s?->jurusan?->nama_jurusan ?? '-') . "</td>\r\n");
                    fwrite($output, "<td>" . htmlspecialchars($r['pos']) . "</td>\r\n");
                    fwrite($output, "<td>" . htmlspecialchars($r['metode_bayar']) . "</td>\r\n");
                    fwrite($output, "<td class='text-right num font-bold'>" . $r['nominal'] . "</td>\r\n");
                    fwrite($output, "<td class='text-center font-bold'>" . htmlspecialchars($r['status']) . "</td>\r\n");
                    fwrite($output, "<td>" . htmlspecialchars($r['verifikator']) . "</td>\r\n");
                    fwrite($output, "</tr>\r\n");
                }
                fwrite($output, "<tr class='bg-total'><td colspan='9' class='text-center font-bold'>TOTAL KAS MASUK</td><td class='text-right num font-bold' style='color: #059669;'>{$totNominal}</td><td colspan='2'>-</td></tr>\r\n");

            } elseif ($activeTab === 'matrix_jurusan') {
                $matrixRows = $this->buildMatrixJurusanData($gelombangId);
                fwrite($output, "<h2>REKAPITULASI TARGET & REALISASI PER KOMPETENSI KEAHLIAN</h2>\r\n");
                fwrite($output, "<p>Tanggal Ekspor: " . date('d/m/Y H:i') . "</p>\r\n");
                fwrite($output, "<table>\r\n<thead>\r\n<tr>\r\n");
                fwrite($output, "<th rowspan='2'>No</th><th rowspan='2'>Kompetensi Keahlian</th><th rowspan='2'>Pendaftar</th>\r\n");
                fwrite($output, "<th colspan='2' class='bg-sub'>Seleksi</th><th colspan='2' class='bg-sub'>Pendidikan (DSP/SPP)</th><th colspan='2' class='bg-sub'>Seragam</th>\r\n");
                fwrite($output, "<th rowspan='2'>Potongan Diskon</th><th rowspan='2'>Target Bersih</th><th rowspan='2'>Kas Masuk</th><th rowspan='2'>Sisa Piutang</th><th rowspan='2'>% Realisasi</th>\r\n</tr>\r\n<tr>\r\n");
                fwrite($output, "<th>Target</th><th>Kas</th><th>Target</th><th>Kas</th><th>Target</th><th>Kas</th>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n");

                $no = 1;
                $totPendaftar = 0; $totTarSel = 0; $totKasSel = 0;
                $totTarEdu = 0; $totKasEdu = 0; $totTarSrg = 0; $totKasSrg = 0;
                $totDiskon = 0; $totTarget = 0; $totKas = 0; $totPiutang = 0;

                foreach ($matrixRows as $r) {
                    $totPendaftar += $r['pendaftar_count'];
                    $totTarSel += $r['target_seleksi']; $totKasSel += $r['kas_seleksi'];
                    $totTarEdu += $r['target_edu']; $totKasEdu += $r['kas_edu'];
                    $totTarSrg += $r['target_srg']; $totKasSrg += $r['kas_srg'];
                    $totDiskon += $r['diskon'];
                    $totTarget += $r['total_target']; $totKas += $r['total_kas']; $totPiutang += $r['sisa_piutang'];

                    fwrite($output, "<tr>\r\n");
                    fwrite($output, "<td class='text-center'>" . $no++ . "</td>\r\n");
                    fwrite($output, "<td>" . htmlspecialchars($r['jurusan']->nama_jurusan) . " (" . $r['jurusan']->kode . ")</td>\r\n");
                    fwrite($output, "<td class='text-center'>" . $r['pendaftar_count'] . "</td>\r\n");
                    fwrite($output, "<td class='text-right num'>" . $r['target_seleksi'] . "</td><td class='text-right num'>" . $r['kas_seleksi'] . "</td>\r\n");
                    fwrite($output, "<td class='text-right num'>" . $r['target_edu'] . "</td><td class='text-right num'>" . $r['kas_edu'] . "</td>\r\n");
                    fwrite($output, "<td class='text-right num'>" . $r['target_srg'] . "</td><td class='text-right num'>" . $r['kas_srg'] . "</td>\r\n");
                    fwrite($output, "<td class='text-right num'>" . $r['diskon'] . "</td>\r\n");
                    fwrite($output, "<td class='text-right num font-bold'>" . $r['total_target'] . "</td>\r\n");
                    fwrite($output, "<td class='text-right num font-bold' style='color: #059669;'>" . $r['total_kas'] . "</td>\r\n");
                    fwrite($output, "<td class='text-right num font-bold' style='color: #dc2626;'>" . $r['sisa_piutang'] . "</td>\r\n");
                    fwrite($output, "<td class='text-center font-bold'>" . $r['persentase'] . "%</td>\r\n");
                    fwrite($output, "</tr>\r\n");
                }
                $grandPct = $totTarget > 0 ? round(($totKas / $totTarget) * 100, 1) : 0;
                fwrite($output, "<tr class='bg-total'><td colspan='2' class='text-center font-bold'>TOTAL SEKOLAH</td><td class='text-center font-bold'>{$totPendaftar}</td>");
                fwrite($output, "<td class='text-right num font-bold'>{$totTarSel}</td><td class='text-right num font-bold'>{$totKasSel}</td>");
                fwrite($output, "<td class='text-right num font-bold'>{$totTarEdu}</td><td class='text-right num font-bold'>{$totKasEdu}</td>");
                fwrite($output, "<td class='text-right num font-bold'>{$totTarSrg}</td><td class='text-right num font-bold'>{$totKasSrg}</td>");
                fwrite($output, "<td class='text-right num font-bold'>{$totDiskon}</td>");
                fwrite($output, "<td class='text-right num font-bold'>{$totTarget}</td><td class='text-right num font-bold' style='color: #059669;'>{$totKas}</td><td class='text-right num font-bold' style='color: #dc2626;'>{$totPiutang}</td><td class='text-center font-bold'>{$grandPct}%</td></tr>\r\n");

            } elseif ($activeTab === 'diskon') {
                $rows = $this->buildDiskonData($gelombangId, $jurusanId, $jenisDiskon, $search);
                fwrite($output, "<h2>REKAPITULASI DISKON & BEASISWA CALON MURID</h2>\r\n");
                fwrite($output, "<p>Tanggal Ekspor: " . date('d/m/Y H:i') . "</p>\r\n");
                fwrite($output, "<table>\r\n<thead>\r\n<tr>\r\n");
                fwrite($output, "<th>No</th><th>Tanggal SK</th><th>No. Reg</th><th>Nama Calon Murid</th><th>Jurusan</th><th>Gelombang</th><th>Jenis Beasiswa / Diskon</th><th>Skema / Acuan</th><th>Nominal Potongan</th><th>Alasan / Keterangan</th><th>Pemberi</th><th>Penyetuju</th>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n");

                $no = 1;
                $totPotongan = 0;
                foreach ($rows as $d) {
                    $s = $d->calonSiswa;
                    $totPotongan += (float) $d->nominal_potongan;
                    $tgl = $d->diberikan_at ? Carbon::parse($d->diberikan_at)->format('d/m/Y') : ($d->created_at ? Carbon::parse($d->created_at)->format('d/m/Y') : '-');

                    fwrite($output, "<tr>\r\n");
                    fwrite($output, "<td class='text-center'>" . $no++ . "</td>\r\n");
                    fwrite($output, "<td class='text-center'>" . $tgl . "</td>\r\n");
                    fwrite($output, "<td class='text-center'>'" . htmlspecialchars($s?->nomor_pendaftaran ?? '-') . "</td>\r\n");
                    fwrite($output, "<td>" . htmlspecialchars($s?->nama_lengkap ?? '-') . "</td>\r\n");
                    fwrite($output, "<td>" . htmlspecialchars($s?->jurusan?->nama_jurusan ?? '-') . "</td>\r\n");
                    fwrite($output, "<td class='text-center'>" . htmlspecialchars($s?->gelombang?->nama_gelombang ?? '-') . "</td>\r\n");
                    fwrite($output, "<td>" . htmlspecialchars($d->jenis_diskon ?? 'Diskon Tagihan') . "</td>\r\n");
                    fwrite($output, "<td class='text-center'>" . htmlspecialchars($d->metode_diskon ?? 'Nominal') . "</td>\r\n");
                    fwrite($output, "<td class='text-right num font-bold'>" . $d->nominal_potongan . "</td>\r\n");
                    fwrite($output, "<td>" . htmlspecialchars($d->alasan ?? ($d->keterangan ?? '-')) . "</td>\r\n");
                    fwrite($output, "<td>" . htmlspecialchars($d->diberikanOleh?->name ?? '-') . "</td>\r\n");
                    fwrite($output, "<td>" . htmlspecialchars($d->disetujuiOleh?->name ?? '-') . "</td>\r\n");
                    fwrite($output, "</tr>\r\n");
                }
                fwrite($output, "<tr class='bg-total'><td colspan='8' class='text-center font-bold'>TOTAL ALOKASI DISKON</td><td class='text-right num font-bold' style='color: #059669;'>{$totPotongan}</td><td colspan='3'>-</td></tr>\r\n");
            }

            fwrite($output, "</tbody>\r\n</table>\r\n</body>\r\n</html>");
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Builder Data Tab 1: Rekap Piutang & Status Siswa.
     */
    protected function buildPiutangSiswaData(?int $gelombangId, ?int $jurusanId, ?string $search, ?string $statusKelunasan): Collection
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

    /**
     * Builder Data Tab 2: Jurnal Buku Kas Masuk (Kronologis Transaksi).
     */
    protected function buildBukuKasData(
        ?string $startDate,
        ?string $endDate,
        ?string $posKategori,
        ?string $statusVerifikasi,
        ?int $gelombangId,
        ?int $jurusanId,
        ?string $search
    ): Collection {
        // 1. Ambil transaksi seleksi
        $seleksiQuery = PembayaranSeleksi::with(['calonSiswa.jurusan', 'calonSiswa.gelombang', 'verifiedBy'])
            ->whereHas('calonSiswa', fn($q) => $q->tanpaDemo());

        if (!empty($statusVerifikasi)) {
            $seleksiQuery->where('status', $statusVerifikasi);
        }
        if (!empty($startDate)) {
            $seleksiQuery->whereDate('tanggal_bayar', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $seleksiQuery->whereDate('tanggal_bayar', '<=', $endDate);
        }
        if (!empty($gelombangId)) {
            $seleksiQuery->whereHas('calonSiswa', fn($q) => $q->where('gelombang_id', $gelombangId));
        }
        if (!empty($jurusanId)) {
            $seleksiQuery->whereHas('calonSiswa', fn($q) => $q->where('jurusan_id', $jurusanId));
        }
        if (!empty($search)) {
            $seleksiQuery->where(function ($q) use ($search) {
                $q->where('nomor_referensi', 'like', "%{$search}%")
                  ->orWhereHas('calonSiswa', fn($sq) => $sq->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nomor_pendaftaran', 'like', "%{$search}%"));
            });
        }

        $seleksiItems = collect();
        if (empty($posKategori) || $posKategori === 'SELEKSI') {
            $seleksiItems = $seleksiQuery->get()->map(function ($p) {
                return [
                    'id' => 'sel_' . $p->id,
                    'source_id' => $p->id,
                    'source_type' => 'SELEKSI',
                    'waktu' => $p->verified_at ?? $p->created_at,
                    'tanggal_bayar' => $p->tanggal_bayar ?? $p->created_at,
                    'nomor_transaksi' => $p->nomor_referensi ?: ('KW-SEL-' . str_pad($p->id, 5, '0', STR_PAD_LEFT)),
                    'calon_siswa' => $p->calonSiswa,
                    'pos' => 'Biaya Seleksi',
                    'pos_kode' => 'SELEKSI',
                    'metode_bayar' => $p->metode_bayar ?: 'Transfer Bank BNI',
                    'nominal' => (float) $p->nominal_dibayar,
                    'status' => $p->status,
                    'verifikator' => $p->verifiedBy?->name ?? '-',
                ];
            });
        }

        // 2. Ambil transaksi daftar ulang
        $duQuery = PembayaranDaftarUlang::with(['calonSiswa.jurusan', 'calonSiswa.gelombang', 'tagihan', 'verifiedBy'])
            ->whereHas('calonSiswa', fn($q) => $q->tanpaDemo());

        if (!empty($statusVerifikasi)) {
            $duQuery->where('status', $statusVerifikasi);
        }
        if (!empty($startDate)) {
            $duQuery->whereDate('tanggal_bayar', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $duQuery->whereDate('tanggal_bayar', '<=', $endDate);
        }
        if (!empty($gelombangId)) {
            $duQuery->whereHas('calonSiswa', fn($q) => $q->where('gelombang_id', $gelombangId));
        }
        if (!empty($jurusanId)) {
            $duQuery->whereHas('calonSiswa', fn($q) => $q->where('jurusan_id', $jurusanId));
        }
        if (!empty($search)) {
            $duQuery->where(function ($q) use ($search) {
                $q->where('nomor_referensi', 'like', "%{$search}%")
                  ->orWhereHas('calonSiswa', fn($sq) => $sq->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nomor_pendaftaran', 'like', "%{$search}%"));
            });
        }
        if (!empty($posKategori) && $posKategori !== 'SELEKSI') {
            $duQuery->whereHas('tagihan', fn($q) => $q->where('jenis_tagihan', $posKategori));
        }

        $duItems = collect();
        if (empty($posKategori) || $posKategori !== 'SELEKSI') {
            $duItems = $duQuery->get()->map(function ($p) {
                $jenis = $p->tagihan?->jenis_tagihan;
                $pos = $jenis === Tagihan::JENIS_SERAGAM ? 'Paket Seragam' : 'DSP & SPP (Pendidikan)';
                return [
                    'id' => 'du_' . $p->id,
                    'source_id' => $p->id,
                    'source_type' => 'DAFTAR_ULANG',
                    'waktu' => $p->verified_at ?? $p->created_at,
                    'tanggal_bayar' => $p->tanggal_bayar ?? $p->created_at,
                    'nomor_transaksi' => $p->nomor_referensi ?: ('KW-DU-' . str_pad($p->id, 5, '0', STR_PAD_LEFT)),
                    'calon_siswa' => $p->calonSiswa,
                    'pos' => $pos,
                    'pos_kode' => $jenis ?: Tagihan::JENIS_DAFTAR_ULANG,
                    'metode_bayar' => $p->metode_bayar ?: 'Transfer Bank BNI',
                    'nominal' => (float) $p->nominal_dibayar,
                    'status' => $p->status,
                    'verifikator' => $p->verifiedBy?->name ?? '-',
                ];
            });
        }

        return $seleksiItems->concat($duItems)->sortByDesc('waktu')->values();
    }

    /**
     * Builder Data Tab 3: Rekapitulasi per Jurusan & Pos Biaya.
     */
    protected function buildMatrixJurusanData(?int $gelombangId): Collection
    {
        return MasterJurusan::aktif()->get()->map(function ($j) use ($gelombangId) {
            $studentQuery = CalonSiswa::tanpaDemo()->where('jurusan_id', $j->id);
            if (!empty($gelombangId)) {
                $studentQuery->where('gelombang_id', $gelombangId);
            }
            $studentIds = $studentQuery->pluck('id');

            $targetSeleksi = (float) PembayaranSeleksi::whereIn('calon_siswa_id', $studentIds)->sum('nominal_tagihan');
            if ($targetSeleksi == 0 && $studentIds->count() > 0) {
                $targetSeleksi = $studentIds->count() * 200000;
            }
            $kasSeleksi = (float) PembayaranSeleksi::whereIn('calon_siswa_id', $studentIds)
                ->where('status', PembayaranSeleksi::STATUS_DIVERIFIKASI)->sum('nominal_dibayar');

            $targetEdu = (float) Tagihan::whereIn('calon_siswa_id', $studentIds)
                ->where('jenis_tagihan', Tagihan::JENIS_DAFTAR_ULANG)->sum('total_netto');
            $kasEdu = (float) PembayaranDaftarUlang::whereIn('calon_siswa_id', $studentIds)
                ->where('status', PembayaranDaftarUlang::STATUS_DIVERIFIKASI)
                ->whereHas('tagihan', fn($q) => $q->where('jenis_tagihan', Tagihan::JENIS_DAFTAR_ULANG))->sum('nominal_dibayar');

            $targetSrg = (float) Tagihan::whereIn('calon_siswa_id', $studentIds)
                ->where('jenis_tagihan', Tagihan::JENIS_SERAGAM)->sum('total_netto');
            $kasSrg = (float) PembayaranDaftarUlang::whereIn('calon_siswa_id', $studentIds)
                ->where('status', PembayaranDaftarUlang::STATUS_DIVERIFIKASI)
                ->whereHas('tagihan', fn($q) => $q->where('jenis_tagihan', Tagihan::JENIS_SERAGAM))->sum('nominal_dibayar');

            $diskon = (float) Diskon::whereIn('calon_siswa_id', $studentIds)->sum('nominal_potongan');

            $totalTarget = $targetSeleksi + $targetEdu + $targetSrg;
            $totalKas = $kasSeleksi + $kasEdu + $kasSrg;
            $sisa = max(0, $totalTarget - $totalKas);
            $persen = $totalTarget > 0 ? round(($totalKas / $totalTarget) * 100, 1) : 0;

            return [
                'jurusan' => $j,
                'pendaftar_count' => $studentIds->count(),
                'target_seleksi' => $targetSeleksi,
                'kas_seleksi' => $kasSeleksi,
                'target_edu' => $targetEdu,
                'kas_edu' => $kasEdu,
                'target_srg' => $targetSrg,
                'kas_srg' => $kasSrg,
                'diskon' => $diskon,
                'total_target' => $totalTarget,
                'total_kas' => $totalKas,
                'sisa_piutang' => $sisa,
                'persentase' => $persen,
            ];
        });
    }

    /**
     * Builder Data Tab 4: Rekapitulasi Diskon & Beasiswa.
     */
    protected function buildDiskonData(?int $gelombangId, ?int $jurusanId, ?string $jenisDiskon, ?string $search): Collection
    {
        $query = Diskon::with(['calonSiswa.jurusan', 'calonSiswa.gelombang', 'diberikanOleh', 'disetujuiOleh'])
            ->whereHas('calonSiswa', fn($q) => $q->tanpaDemo());

        if (!empty($gelombangId)) {
            $query->whereHas('calonSiswa', fn($q) => $q->where('gelombang_id', $gelombangId));
        }
        if (!empty($jurusanId)) {
            $query->whereHas('calonSiswa', fn($q) => $q->where('jurusan_id', $jurusanId));
        }
        if (!empty($jenisDiskon)) {
            $query->where('jenis_diskon', $jenisDiskon);
        }
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('alasan', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('calonSiswa', fn($sq) => $sq->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nomor_pendaftaran', 'like', "%{$search}%"));
            });
        }

        return $query->latest('id')->get();
    }

    /**
     * Helper manual paginator untuk Collection.
     */
    protected function paginateCollection(Collection $collection, int $perPage, Request $request): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        return new LengthAwarePaginator(
            $collection->forPage($page, $perPage)->values(),
            $collection->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );
    }
}
