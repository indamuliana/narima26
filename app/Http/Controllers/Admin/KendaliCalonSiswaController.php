<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KendaliCalonSiswaController extends Controller
{
    /**
     * Helper untuk membangun filtered query calon siswa.
     */
    protected function buildFilteredQuery(Request $request)
    {
        $query = CalonSiswa::query()->with([
            'jurusan',
            'jurusan2',
            'program',
            'gelombang',
            'pembayaranSeleksi',
            'dataOrangtua',
        ]);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('no_hp_siswa', 'like', "%{$search}%")
                  ->orWhere('referensi_nama', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status_spmb')) {
            if ($status !== 'SEMUA') {
                $query->where('status_spmb', $status);
            }
        }

        if ($jurusanId = $request->input('jurusan_id')) {
            if ($jurusanId !== 'SEMUA') {
                $query->where(function ($q) use ($jurusanId) {
                    $q->where('jurusan_id', $jurusanId)
                      ->orWhere('jurusan_id_2', $jurusanId);
                });
            }
        }

        if ($gelombangId = $request->input('gelombang_id')) {
            if ($gelombangId !== 'SEMUA') {
                $query->where('gelombang_id', $gelombangId);
            }
        }

        if ($programId = $request->input('program_id')) {
            if ($programId !== 'SEMUA') {
                $query->where('program_id', $programId);
            }
        }

        if ($gender = $request->input('jenis_kelamin')) {
            if ($gender !== 'SEMUA') {
                $query->where('jenis_kelamin', $gender);
            }
        }

        return $query;
    }

    /**
     * Tampilkan tabel kendali calon murid untuk Admin.
     */
    public function index(Request $request): View
    {
        $perPage = min(100, max(10, (int) $request->input('per_page', 20)));

        $query = $this->buildFilteredQuery($request);

        $calonSiswaList = $query->latest('id')->paginate($perPage)->withQueryString();

        $jurusanList = MasterJurusan::aktif()->orderBy('kode')->get();
        $gelombangList = MasterGelombang::orderBy('id')->get();
        $programList = MasterProgram::aktif()->get();
        $statusList = SpmbStatus::cases();
        $totalCount = $calonSiswaList->total();

        return view('admin.kendali-calon-siswa.index', compact(
            'calonSiswaList',
            'jurusanList',
            'gelombangList',
            'programList',
            'statusList',
            'totalCount'
        ));
    }

    /**
     * Simpan catatan khusus CS dan/atau catatan untuk pewawancara via AJAX / Form.
     */
    public function updateCatatan(Request $request, CalonSiswa $calonSiswa): JsonResponse
    {
        if ($request->has('field')) {
            $validated = $request->validate([
                'field' => ['required', 'string', 'in:catatan_admin,catatan_untuk_pewawancara'],
                'value' => ['nullable', 'string', 'max:5000'],
                'is_done' => ['nullable', 'boolean'],
            ]);

            $calonSiswa->{$validated['field']} = $validated['value'];

            if ($request->has('is_done')) {
                if ($validated['field'] === 'catatan_admin') {
                    $calonSiswa->is_catatan_admin_done = (bool) $validated['is_done'];
                } else {
                    $calonSiswa->is_catatan_pewawancara_done = (bool) $validated['is_done'];
                }
            }
        } else {
            $validated = $request->validate([
                'catatan_admin' => ['nullable', 'string', 'max:5000'],
                'is_catatan_admin_done' => ['nullable', 'boolean'],
                'catatan_untuk_pewawancara' => ['nullable', 'string', 'max:5000'],
                'is_catatan_pewawancara_done' => ['nullable', 'boolean'],
            ]);

            if (array_key_exists('catatan_admin', $validated)) {
                $calonSiswa->catatan_admin = $validated['catatan_admin'];
            }
            if (array_key_exists('is_catatan_admin_done', $validated)) {
                $calonSiswa->is_catatan_admin_done = (bool) $validated['is_catatan_admin_done'];
            }
            if (array_key_exists('catatan_untuk_pewawancara', $validated)) {
                $calonSiswa->catatan_untuk_pewawancara = $validated['catatan_untuk_pewawancara'];
            }
            if (array_key_exists('is_catatan_pewawancara_done', $validated)) {
                $calonSiswa->is_catatan_pewawancara_done = (bool) $validated['is_catatan_pewawancara_done'];
            }
        }

        $calonSiswa->save();

        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil disimpan.',
            'catatan_admin' => $calonSiswa->catatan_admin,
            'is_catatan_admin_done' => (bool) $calonSiswa->is_catatan_admin_done,
            'catatan_untuk_pewawancara' => $calonSiswa->catatan_untuk_pewawancara,
            'is_catatan_pewawancara_done' => (bool) $calonSiswa->is_catatan_pewawancara_done,
        ]);
    }

    /**
     * Export data Kendali Calon Murid ke file Microsoft Excel (.xls).
     */
    public function exportXls(Request $request): StreamedResponse
    {
        $candidates = $this->buildFilteredQuery($request)->latest('id')->get();
        $filename = 'Kendali_Calon_Murid_' . date('Ymd_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columnHeaders = [
            'No',
            'No. Pendaftaran',
            'NISN',
            'Nama Lengkap',
            'L/P',
            'Program',
            'Jurusan Pilihan 1',
            'Jurusan Pilihan 2',
            'Gelombang',
            'Biaya Seleksi',
            'Status SPMB',
            'Kategori Rekomendasi',
            'Nama Pemberi Rekomendasi',
            'Rayon Rekomendasi',
            'Catatan Khusus CS',
            'Catatan untuk Pewawancara',
            'No. HP Siswa',
            'No. HP Ayah',
            'No. HP Ibu',
            'No. HP Wali',
            'Asal Sekolah',
        ];

        $callback = function () use ($candidates, $columnHeaders) {
            $output = fopen('php://output', 'w');

            fwrite($output, "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">\r\n");
            fwrite($output, "<head>\r\n");
            fwrite($output, "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\r\n");
            fwrite($output, "<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Kendali Calon Murid</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->\r\n");
            fwrite($output, "<style>\r\n");
            fwrite($output, "table { border-collapse: collapse; font-family: Calibri, Arial, sans-serif; font-size: 11pt; }\r\n");
            fwrite($output, "th { background-color: #ea580c; color: #ffffff; font-weight: bold; border: 1px solid #c2410c; padding: 8px 10px; text-align: left; vertical-align: middle; }\r\n");
            fwrite($output, "td { border: 1px solid #cbd5e1; padding: 6px 8px; vertical-align: top; mso-number-format:\"\\@\"; }\r\n");
            fwrite($output, "tr:nth-child(even) { background-color: #f8fafc; }\r\n");
            fwrite($output, "</style>\r\n");
            fwrite($output, "</head>\r\n");
            fwrite($output, "<body>\r\n");
            fwrite($output, "<table border=\"1\">\r\n");
            fwrite($output, "<thead><tr>\r\n");

            foreach ($columnHeaders as $header) {
                fwrite($output, '<th>' . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . "</th>\r\n");
            }

            fwrite($output, "</tr></thead>\r\n");
            fwrite($output, "<tbody>\r\n");

            foreach ($candidates as $index => $cs) {
                $statusStr = is_string($cs->status_spmb) ? $cs->status_spmb : ($cs->status_spmb?->value ?? '-');
                $bayar = $cs->pembayaranSeleksi;
                $statusBayar = $bayar && $bayar->status === 'DIVERIFIKASI' ? 'LUNAS' : ($bayar && $bayar->status === 'PENDING' ? 'MENUNGGU VERIFIKASI' : 'BELUM BAYAR');
                $ortu = $cs->dataOrangtua;

                $row = [
                    $index + 1,
                    $cs->nomor_pendaftaran,
                    $cs->nisn,
                    $cs->nama_lengkap,
                    $cs->jenis_kelamin === 'PEREMPUAN' || $cs->jenis_kelamin === 'P' ? 'P' : 'L',
                    $cs->program?->nama ?? $cs->program?->nama_program ?? 'Reguler',
                    $cs->jurusan?->nama_jurusan ?? '-',
                    $cs->jurusan2?->nama_jurusan ?? '-',
                    $cs->gelombang?->nama_gelombang ?? '-',
                    $statusBayar,
                    str_replace('_', ' ', $statusStr),
                    $cs->referensi_jenis ? str_replace('_', ' ', $cs->referensi_jenis) : '-',
                    $cs->referensi_nama ?: '-',
                    $cs->referensi_rayon ?: '-',
                    $cs->catatan_admin ? ($cs->is_catatan_admin_done ? '[SELESAI] ' : '[PENDING] ') . $cs->catatan_admin : '-',
                    $cs->catatan_untuk_pewawancara ? ($cs->is_catatan_pewawancara_done ? '[SELESAI] ' : '[PENDING] ') . $cs->catatan_untuk_pewawancara : '-',
                    $cs->no_hp_siswa ?: '-',
                    $cs->no_hp_ayah ?: ($ortu?->no_hp_ayah ?: '-'),
                    $cs->no_hp_ibu ?: ($ortu?->no_hp_ibu ?: '-'),
                    $ortu?->no_hp_wali ?: '-',
                    $cs->sekolah_asal_text ?: '-',
                ];

                fwrite($output, "<tr>\r\n");
                foreach ($row as $cell) {
                    fwrite($output, '<td>' . htmlspecialchars((string) $cell, ENT_QUOTES, 'UTF-8') . "</td>\r\n");
                }
                fwrite($output, "</tr>\r\n");
            }

            fwrite($output, "</tbody>\r\n");
            fwrite($output, "</table>\r\n");
            fwrite($output, "</body>\r\n");
            fwrite($output, "</html>\r\n");

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export data Kendali Calon Murid ke dokumen PDF resmi.
     */
    public function exportPdf(Request $request, PdfService $pdfService): Response
    {
        $candidates = $this->buildFilteredQuery($request)->latest('id')->get();

        $filters = [];
        if ($programId = $request->input('program_id')) {
            if ($programId !== 'SEMUA') {
                $filters['program_nama'] = MasterProgram::find($programId)?->nama;
            }
        }
        if ($jurusanId = $request->input('jurusan_id')) {
            if ($jurusanId !== 'SEMUA') {
                $filters['jurusan_nama'] = MasterJurusan::find($jurusanId)?->nama_jurusan;
            }
        }
        if ($gelombangId = $request->input('gelombang_id')) {
            if ($gelombangId !== 'SEMUA') {
                $filters['gelombang_nama'] = MasterGelombang::find($gelombangId)?->nama_gelombang;
            }
        }
        if ($status = $request->input('status_spmb')) {
            if ($status !== 'SEMUA') {
                $filters['status_nama'] = str_replace('_', ' ', $status);
            }
        }
        if ($gender = $request->input('jenis_kelamin')) {
            if ($gender !== 'SEMUA') {
                $filters['gender_nama'] = $gender === 'L' ? 'Laki-laki' : 'Perempuan';
            }
        }

        $pdf = $pdfService->renderPdf('pdf.rekap_kendali_calon_siswa', [
            'calonSiswaList' => $candidates,
            'filters' => $filters,
            'printedAt' => now(),
        ], 'a4', 'landscape');

        return $pdf->download('Rekap_Kendali_Calon_Murid_' . date('Ymd_His') . '.pdf');
    }
}
