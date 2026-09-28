<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CalonSiswaController extends Controller
{
    public function __construct(
        protected PdfService $pdfService
    ) {}

    /**
     * Build base query for CalonSiswa with applied request filters.
     */
    protected function buildFilteredQuery(Request $request)
    {
        $query = CalonSiswa::query()->with([
            'jurusan',
            'program',
            'gelombang',
            'sekolahAsal',
            'pembayaranSeleksi',
            'tagihan',
            'keputusanKelulusan',
        ]);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('no_hp_siswa', 'like', "%{$search}%")
                  ->orWhere('asal_sekolah_lainnya', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status_spmb')) {
            if ($status !== 'SEMUA') {
                $query->where('status_spmb', $status);
            }
        }

        if ($jurusanId = $request->input('jurusan_id')) {
            $query->where('jurusan_id', $jurusanId);
        }

        if ($gelombangId = $request->input('gelombang_id')) {
            $query->where('gelombang_id', $gelombangId);
        }

        if ($programId = $request->input('program_id')) {
            $query->where('program_id', $programId);
        }

        if ($gender = $request->input('jenis_kelamin')) {
            $query->where('jenis_kelamin', $gender);
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $allowedSorts = ['id', 'nama_lengkap', 'nomor_pendaftaran', 'nisn', 'status_spmb', 'created_at'];

        if (in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest('id');
        }

        return $query;
    }

    /**
     * Display server-side filtered candidate directory (DataTables style).
     */
    public function index(Request $request): View
    {
        $perPage = min(100, max(10, (int) $request->input('per_page', 20)));
        $query = $this->buildFilteredQuery($request);

        $calonSiswaList = $query->paginate($perPage)->withQueryString();

        $jurusanList = MasterJurusan::aktif()->orderBy('kode')->get();
        $gelombangList = MasterGelombang::orderBy('id')->get();
        $programList = MasterProgram::aktif()->get();
        $statusList = SpmbStatus::cases();

        // Total count for current filter
        $totalCount = $calonSiswaList->total();

        return view('admin.calon-siswa.index', compact(
            'calonSiswaList',
            'jurusanList',
            'gelombangList',
            'programList',
            'statusList',
            'totalCount'
        ));
    }

    /**
     * Display 360-degree comprehensive profile of a single candidate.
     */
    public function show(CalonSiswa $calonSiswa): View
    {
        $calonSiswa->loadMissing([
            'jurusan',
            'program',
            'gelombang',
            'sekolahAsal',
            'dataOrangtua',
            'dataAkademik',
            'ukuranSeragam',
            'dokumenPendaftaran',
            'pembayaranSeleksi',
            'tagihan.details',
            'pembayaranDaftarUlang',
            'diskon',
            'wawancara.details.kriteria',
            'wawancara.pewawancara',
            'keputusanKelulusan.ditetapkanOleh',
            'kesepahaman',
            'riwayatStatus.diubahOleh',
        ]);

        return view('admin.calon-siswa.show', compact('calonSiswa'));
    }

    /**
     * Export filtered candidate list to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $candidates = $this->buildFilteredQuery($request)->get();
        $filename = 'Rekap_Calon_Siswa_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($candidates) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens it with proper encoding
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Column Headers
            fputcsv($handle, [
                'No',
                'Nomor Pendaftaran',
                'NISN',
                'Nama Lengkap',
                'Jenis Kelamin',
                'No. HP Siswa',
                'Sekolah Asal',
                'Kompetensi Keahlian',
                'Program',
                'Gelombang',
                'Status SPMB',
                'Status Seleksi Bayar',
                'Status Kelulusan',
                'Status Daftar Ulang',
                'Tanggal Daftar',
            ]);

            foreach ($candidates as $index => $cs) {
                $statusVal = is_string($cs->status_spmb) ? $cs->status_spmb : ($cs->status_spmb?->value ?? '-');
                $sekolah = $cs->sekolahAsal?->nama_sekolah ?? $cs->asal_sekolah_lainnya ?? '-';
                $bayarSeleksi = $cs->pembayaranSeleksi?->status ?? 'BELUM';
                $keputusan = $cs->keputusanKelulusan?->keputusan ?? '-';
                $statusTagihan = $cs->tagihan->first()?->status ?? '-';

                fputcsv($handle, [
                    $index + 1,
                    $cs->nomor_pendaftaran,
                    "'{$cs->nisn}", // Prefix quote to prevent scientific notation in Excel
                    $cs->nama_lengkap,
                    $cs->jenis_kelamin,
                    $cs->no_hp_siswa,
                    $sekolah,
                    $cs->jurusan?->nama_jurusan ?? '-',
                    $cs->program?->nama_program ?? '-',
                    $cs->gelombang?->nama_gelombang ?? '-',
                    $statusVal,
                    $bayarSeleksi,
                    $keputusan,
                    $statusTagihan,
                    $cs->created_at?->format('Y-m-d H:i:s') ?? '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export filtered candidate list to official PDF report.
     */
    public function exportPdf(Request $request): Response
    {
        $candidates = $this->buildFilteredQuery($request)->get();

        $filters = [];
        if ($jurusanId = $request->input('jurusan_id')) {
            $filters['jurusan_nama'] = MasterJurusan::find($jurusanId)?->nama_jurusan;
        }
        if ($gelombangId = $request->input('gelombang_id')) {
            $filters['gelombang_nama'] = MasterGelombang::find($gelombangId)?->nama_gelombang;
        }
        if ($status = $request->input('status_spmb')) {
            if ($status !== 'SEMUA') {
                $filters['status_nama'] = $status;
            }
        }

        $pdf = $this->pdfService->generateRekapCalonSiswa($candidates, $filters);

        return $pdf->download('Rekap_Calon_Siswa_SPMB_' . date('Ymd_His') . '.pdf');
    }
}
