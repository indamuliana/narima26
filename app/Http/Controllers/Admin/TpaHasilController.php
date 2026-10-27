<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\TpaSoal;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TpaHasilController extends Controller
{
    /**
     * Bangun query rekapitulasi TPA berdasarkan filter.
     */
    protected function buildFilteredQuery(Request $request)
    {
        $query = CalonSiswa::query()
            ->where('nomor_pendaftaran', 'not like', 'SIM-%')
            ->with([
                'jurusan',
                'gelombang',
                'tpaSesi.jawabanSiswa',
                'tpaJadwal',
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

        if ($status = $request->input('status_ujian')) {
            if ($status === 'SELESAI') {
                $query->whereHas('tpaSesi', fn($q) => $q->where('status', 'selesai'));
            } elseif ($status === 'PROSES') {
                $query->whereHas('tpaSesi', fn($q) => $q->whereIn('status', ['babak_1', 'transisi', 'babak_2']));
            } elseif ($status === 'BELUM_MULAI') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('tpaSesi')
                      ->orWhereHas('tpaSesi', fn($sub) => $sub->where('status', 'belum_mulai'));
                });
            } elseif ($status === 'DIDISKUALIFIKASI') {
                $query->whereHas('tpaSesi', fn($q) => $q->where('status', 'didiskualifikasi'));
            }
        }

        return $query;
    }

    /**
     * Halaman Rekapitulasi Hasil Jawaban & Nilai TPA.
     */
    public function index(Request $request): View
    {
        $query = $this->buildFilteredQuery($request);

        // Sorting: default urutkan berdasarkan total_skor tertinggi pada sesi
        $calonSiswaList = $query->paginate(25)->withQueryString();

        $jurusanList = MasterJurusan::orderBy('nama')->get();
        $gelombangList = MasterGelombang::orderBy('nama')->get();

        // Statistik singkat
        $totalPeserta = CalonSiswa::count();
        $totalSelesai = \App\Models\TpaSesiSiswa::where('status', 'selesai')->count();
        $totalSedang = \App\Models\TpaSesiSiswa::whereIn('status', ['babak_1', 'transisi', 'babak_2'])->count();
        $totalDiskualifikasi = \App\Models\TpaSesiSiswa::where('status', 'didiskualifikasi')->count();

        return view('admin.tpa.hasil.index', compact(
            'calonSiswaList',
            'jurusanList',
            'gelombangList',
            'totalPeserta',
            'totalSelesai',
            'totalSedang',
            'totalDiskualifikasi'
        ));
    }

    /**
     * Tampilkan detail lembar jawaban siswa dan audit log kecurangan.
     */
    public function show(CalonSiswa $calonSiswa): View
    {
        $calonSiswa->loadMissing(['jurusan', 'gelombang', 'tpaSesi.jawabanSiswa.soal', 'tpaJadwal']);
        $sesi = $calonSiswa->tpaSesi;

        $soalLiterasi = collect();
        $soalNumerasi = collect();
        $jawabanMap = collect();

        if ($sesi) {
            $jawabanMap = $sesi->jawabanSiswa->keyBy('tpa_soal_id');
            $litIds = $sesi->urutan_soal_literasi ?? [];
            $numIds = $sesi->urutan_soal_numerasi ?? [];

            $allSoal = TpaSoal::whereIn('id', array_merge($litIds, $numIds))->get()->keyBy('id');

            $soalLiterasi = collect($litIds)->map(fn($id) => $allSoal->get($id))->filter();
            $soalNumerasi = collect($numIds)->map(fn($id) => $allSoal->get($id))->filter();
        }

        return view('admin.tpa.hasil.show', compact(
            'calonSiswa',
            'sesi',
            'soalLiterasi',
            'soalNumerasi',
            'jawabanMap'
        ));
    }

    /**
     * Export rekapitulasi nilai TPA ke format Excel (.xls).
     */
    public function exportXls(Request $request): StreamedResponse
    {
        $candidates = $this->buildFilteredQuery($request)->get();
        $filename = 'Rekap_Nilai_TPA_' . date('Ymd_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($candidates) {
            echo view('admin.tpa.hasil.export_xls', ['candidates' => $candidates])->render();
        }, 200, $headers);
    }

    /**
     * Export rekapitulasi nilai TPA ke dokumen PDF resmi.
     */
    public function exportPdf(Request $request, PdfService $pdfService): Response
    {
        $candidates = $this->buildFilteredQuery($request)->get();
        $printedAt = now();

        $pdf = $pdfService->renderPdf('admin.tpa.hasil.export_pdf', [
            'candidates' => $candidates,
            'printedAt' => $printedAt,
        ], 'a4', 'landscape');

        return $pdf->download('Berita_Acara_Hasil_TPA_' . date('Ymd_His') . '.pdf');
    }
}
