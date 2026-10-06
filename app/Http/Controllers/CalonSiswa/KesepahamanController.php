<?php

namespace App\Http\Controllers\CalonSiswa;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Models\KesepahamanEula;
use App\Services\KesepahamanService;
use App\Services\PdfService;
use App\Services\SpmbStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class KesepahamanController extends Controller
{
    public function __construct(
        protected SpmbStatusService $spmbStatusService,
        protected PdfService $pdfService,
        protected KesepahamanService $kesepahamanService
    ) {}

    /**
     * Tampilkan lembar kesepahaman / EULA SPMB.
     */
    public function index(): View|RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;

        if (!$calonSiswa) {
            abort(404, 'Data pendaftaran calon siswa tidak ditemukan.');
        }

        // Guard: Harus sudah melengkapi seluruh data (DATA_LENGKAP atau sesudahnya)
        $uncompletedStatuses = [
            SpmbStatus::REGISTRASI,
            SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI,
            SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI,
            SpmbStatus::MELENGKAPI_DATA,
        ];

        if (in_array($calonSiswa->status_spmb, $uncompletedStatuses, true) && $calonSiswa->status_data !== 'LENGKAP') {
            return redirect()->route('calon-siswa.lengkapi-data.index')
                ->with('error', 'Silakan lengkapi seluruh formulir biodata dan berkas persyaratan terlebih dahulu sebelum menyetujui Lembar Kesepahaman.');
        }

        $calonSiswa->loadMissing(['program', 'jurusan', 'gelombang', 'sekolahAsal', 'orangTua', 'dataOrangtua']);

        $eula = $calonSiswa->kesepahaman()->where('setuju', true)->latest()->first();
        $klausulData = $this->kesepahamanService->getKlausulByCalonSiswa($calonSiswa);

        return view('calon-siswa.kesepahaman.index', compact('calonSiswa', 'eula', 'klausulData'));
    }

    /**
     * Simpan persetujuan lembar kesepahaman digital (EULA consent).
     */
    public function store(Request $request): RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;

        if (!$calonSiswa) {
            abort(404, 'Data pendaftaran tidak ditemukan.');
        }

        $klausulData = $this->kesepahamanService->getKlausulByCalonSiswa($calonSiswa);
        $programKey = $klausulData['program_key'];
        $requiredPointIds = $this->kesepahamanService->getRequiredPointIds($programKey);

        $request->validate([
            'setuju' => ['required', 'accepted'],
            'checklist_poin' => ['required', 'array', 'min:1'],
        ], [
            'setuju.accepted' => 'Anda wajib mencentang persetujuan lembar kesepahaman dan tata tertib SPMB untuk melanjutkan.',
            'checklist_poin.required' => 'Anda wajib mencentang setiap butir poin kesepahaman.',
            'checklist_poin.min' => 'Anda wajib mencentang setiap butir poin kesepahaman.',
        ]);

        $submittedPoints = $request->input('checklist_poin', []);
        $missingPoints = array_diff($requiredPointIds, $submittedPoints);

        if (!empty($missingPoints)) {
            return back()->withInput()->with('error', 'Seluruh butir poin kesepahaman (' . count($requiredPointIds) . ' butir) wajib dicentang secara lengkap.');
        }

        $versiDokumen = 'v2.0 - 2027/2028';
        $klausulSummary = "Naskah Persetujuan Siswa SMK Wikrama 1 Garut dan Orang Tua tentang Ketentuan Umum SMK Wikrama 1 Garut {$klausulData['program_title']} Tahun Pelajaran 2027/2028.";

        // Simpan / update record persetujuan
        $eula = KesepahamanEula::create([
            'calon_siswa_id' => $calonSiswa->id,
            'versi_dokumen' => $versiDokumen,
            'program_snapshot' => $klausulData['program_type'],
            'isi_dokumen_atau_referensi_dokumen' => $klausulSummary,
            'poin_disetujui' => $submittedPoints,
            'klausul_snapshot' => $klausulData['kelompok'],
            'setuju' => true,
            'agreed_at' => now(),
            'agreed_by' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Transisi status SPMB: DATA_LENGKAP -> MENUNGGU_WAWANCARA
        if ($calonSiswa->status_spmb === SpmbStatus::DATA_LENGKAP) {
            $this->spmbStatusService->changeStatus(
                $calonSiswa,
                SpmbStatus::MENUNGGU_WAWANCARA,
                'Calon siswa dan orang tua/wali telah menyetujui lembar kesepahaman SPMB secara digital.',
                null,
                auth()->user()
            );
        }

        if (function_exists('activity')) {
            activity('spmb_consent')
                ->performedOn($calonSiswa)
                ->causedBy(auth()->user())
                ->withProperties([
                    'nomor_pendaftaran' => $calonSiswa->nomor_pendaftaran,
                    'program' => $klausulData['program_type'],
                    'versi_dokumen' => $versiDokumen,
                    'total_poin' => count($submittedPoints),
                    'agreed_at' => $eula->agreed_at,
                    'ip_address' => $eula->ip_address,
                ])
                ->log("Calon siswa {$calonSiswa->nama_lengkap} ({$calonSiswa->nomor_pendaftaran}) menyetujui lembar kesepahaman SPMB {$klausulData['program_title']}.");
        }

        return redirect()->route('calon-siswa.kesepahaman.index')
            ->with('success', 'Lembar Kesepahaman SPMB berhasil disetujui! Status Ananda sekarang: Menunggu Wawancara. Ananda dapat mencetak Kartu Tanda Peserta dan Surat Kesepahaman.');
    }

    /**
     * Download PDF Surat Kesepahaman yang telah disetujui.
     */
    public function cetakPdf(): Response|RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;

        $eula = $calonSiswa->kesepahaman()->where('setuju', true)->latest()->first();
        if (!$eula) {
            return redirect()->route('calon-siswa.kesepahaman.index')
                ->with('error', 'Anda harus menyetujui lembar kesepahaman terlebih dahulu sebelum mengunduh PDF.');
        }

        $pdf = $this->pdfService->generateEula($calonSiswa);

        return $pdf->download("Surat_Kesepahaman_{$calonSiswa->nomor_pendaftaran}.pdf");
    }
}
