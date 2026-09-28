<?php

namespace App\Http\Controllers\CalonSiswa;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DokumenPdfController extends Controller
{
    public function __construct(
        protected PdfService $pdfService
    ) {}

    /**
     * Halaman Hub Dokumen & Cetak PDF Calon Siswa.
     */
    public function index(): View
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $calonSiswa->loadMissing(['program', 'jurusan', 'gelombang', 'pembayaranSeleksi', 'kesepahaman', 'dokumenPendaftaran']);

        $eula = $calonSiswa->kesepahaman()->where('setuju', true)->latest()->first();
        $pembayaran = $calonSiswa->pembayaranSeleksi;

        // Ketersediaan kartu: minimal status DATA_LENGKAP atau MENUNGGU_WAWANCARA
        $eligibleForCard = !in_array($calonSiswa->status_spmb, [
            SpmbStatus::REGISTRASI,
            SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI,
            SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI,
            SpmbStatus::MELENGKAPI_DATA,
        ], true) || $calonSiswa->status_data === 'LENGKAP';

        return view('calon-siswa.dokumen.index', compact('calonSiswa', 'eula', 'pembayaran', 'eligibleForCard'));
    }

    /**
     * Cetak / Unduh Kartu Tanda Peserta SPMB.
     */
    public function cetakKartu(): Response|RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;

        // Syarat: minimal DATA_LENGKAP
        if ($calonSiswa->status_data !== 'LENGKAP' && in_array($calonSiswa->status_spmb, [
            SpmbStatus::REGISTRASI,
            SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI,
            SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI,
            SpmbStatus::MELENGKAPI_DATA,
        ], true)) {
            return redirect()->route('calon-siswa.dokumen.index')
                ->with('error', 'Kartu Tanda Peserta baru dapat dicetak setelah seluruh data dan berkas pendaftaran lengkap.');
        }

        $pdf = $this->pdfService->generateKartuPendaftaran($calonSiswa);

        return $pdf->download("Kartu_Peserta_{$calonSiswa->nomor_pendaftaran}.pdf");
    }

    /**
     * Cetak / Unduh Surat Kesepahaman & EULA.
     */
    public function cetakKesepahaman(): Response|RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;

        $eula = $calonSiswa->kesepahaman()->where('setuju', true)->latest()->first();
        if (!$eula) {
            return redirect()->route('calon-siswa.kesepahaman.index')
                ->with('error', 'Silakan baca dan setujui Lembar Kesepahaman terlebih dahulu.');
        }

        $pdf = $this->pdfService->generateEula($calonSiswa);

        return $pdf->download("Surat_Kesepahaman_{$calonSiswa->nomor_pendaftaran}.pdf");
    }

    /**
     * Cetak / Unduh Informasi Akun Pendaftaran.
     */
    public function cetakAkun(): Response
    {
        $calonSiswa = auth()->user()->calonSiswa;

        $pdf = $this->pdfService->generateInformasiAkun($calonSiswa);

        return $pdf->download("Informasi_Akun_{$calonSiswa->nomor_pendaftaran}.pdf");
    }
}
