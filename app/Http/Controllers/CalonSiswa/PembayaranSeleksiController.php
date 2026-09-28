<?php

namespace App\Http\Controllers\CalonSiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitBuktiBayarSeleksiRequest;
use App\Services\PaymentVerificationService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PembayaranSeleksiController extends Controller
{
    public function __construct(
        protected PaymentVerificationService $paymentService
    ) {}

    /**
     * Tampilkan formulir dan status pembayaran seleksi calon siswa.
     */
    public function index(): View
    {
        $calonSiswa = auth()->user()->calonSiswa;

        if (!$calonSiswa) {
            abort(403, 'Profil calon siswa tidak ditemukan.');
        }

        $calonSiswa->loadMissing(['pembayaranSeleksi.verifikator', 'program', 'jurusan', 'gelombang']);
        $pembayaran = $calonSiswa->pembayaranSeleksi;

        return view('calon-siswa.pembayaran-seleksi.index', compact('calonSiswa', 'pembayaran'));
    }

    /**
     * Unggah bukti transfer pembayaran seleksi.
     */
    public function store(SubmitBuktiBayarSeleksiRequest $request): RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;

        if (!$calonSiswa) {
            abort(403, 'Profil calon siswa tidak ditemukan.');
        }

        $file = $request->file('bukti_transfer');
        $this->paymentService->submitSelectionPaymentProof($calonSiswa, $request->validated(), $file);

        return redirect()->route('calon-siswa.pembayaran-seleksi.index')
            ->with('success', 'Bukti pembayaran seleksi berhasil dikirim. Menunggu verifikasi dari Bendahara sekolah.');
    }

    /**
     * Unduh Kwitansi Resmi Pembayaran Seleksi (PDF ber-KOP surat).
     */
    public function cetakKwitansi(PdfService $pdfService): Response
    {
        $calonSiswa = auth()->user()->calonSiswa;

        if (!$calonSiswa) {
            abort(403, 'Profil calon siswa tidak ditemukan.');
        }

        $pembayaran = $calonSiswa->pembayaranSeleksi;

        if (!$pembayaran || $pembayaran->status !== 'DIVERIFIKASI') {
            abort(403, 'Kwitansi hanya dapat diunduh untuk pembayaran yang telah berstatus DIVERIFIKASI.');
        }

        $pdf = $pdfService->generateBuktiPembayaran($pembayaran, 'Biaya Pendaftaran Seleksi');

        return $pdf->download("Kwitansi_Seleksi_{$calonSiswa->nomor_pendaftaran}.pdf");
    }
}
