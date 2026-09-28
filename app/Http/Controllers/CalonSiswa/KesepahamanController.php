<?php

namespace App\Http\Controllers\CalonSiswa;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Models\KesepahamanEula;
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
        protected PdfService $pdfService
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

        $calonSiswa->loadMissing(['program', 'jurusan', 'gelombang', 'sekolahAsal', 'orangTua']);

        $eula = $calonSiswa->kesepahaman()->where('setuju', true)->latest()->first();

        return view('calon-siswa.kesepahaman.index', compact('calonSiswa', 'eula'));
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

        $request->validate([
            'setuju' => ['required', 'accepted'],
        ], [
            'setuju.accepted' => 'Anda wajib mencentang persetujuan lembar kesepahaman dan tata tertib SPMB untuk melanjutkan.',
        ]);

        $versiDokumen = 'v1.0 - 2026/2027';
        $klausul = 'Pakta Integritas & Kesepahaman Bersama Penerimaan Murid Baru (SPMB) SMK Wikrama 1 Garut Tahun Pelajaran 2026/2027 mengenai keabsahan data, kepatuhan tata tertib, pembiayaan pendidikan, dan integritas calon siswa serta orang tua.';

        // Simpan / update record persetujuan
        $eula = KesepahamanEula::create([
            'calon_siswa_id' => $calonSiswa->id,
            'versi_dokumen' => $versiDokumen,
            'isi_dokumen_atau_referensi_dokumen' => $klausul,
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
                    'versi_dokumen' => $versiDokumen,
                    'agreed_at' => $eula->agreed_at,
                    'ip_address' => $eula->ip_address,
                ])
                ->log("Calon siswa {$calonSiswa->nama_lengkap} ({$calonSiswa->nomor_pendaftaran}) menyetujui lembar kesepahaman SPMB.");
        }

        return redirect()->route('calon-siswa.kesepahaman.index')
            ->with('success', 'Lembar Kesepahaman SPMB berhasil disetujui! Status Anda sekarang: Menunggu Wawancara. Anda dapat mencetak Kartu Tanda Peserta dan Surat Kesepahaman.');
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
