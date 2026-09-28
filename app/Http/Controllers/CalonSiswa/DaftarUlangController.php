<?php

namespace App\Http\Controllers\CalonSiswa;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\PembayaranDaftarUlang;
use App\Services\FileUploadService;
use App\Services\InvoiceService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DaftarUlangController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService,
        protected PdfService $pdfService,
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Display student's re-registration and tuition invoice summary.
     */
    public function index(): View
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $calonSiswa->load([
            'jurusan',
            'program',
            'gelombang',
            'tagihan.details',
            'tagihan.diskon',
            'tagihan.pembayaran.verifiedBy',
        ]);

        $tagihan = $calonSiswa->tagihan()->latest('id')->first();
        $totalPaid = $tagihan ? $this->invoiceService->getTotalPaidVerified($tagihan) : 0;
        $remainingBalance = $tagihan ? $this->invoiceService->getRemainingBalance($tagihan) : 0;

        return view('calon-siswa.daftar-ulang.index', compact('calonSiswa', 'tagihan', 'totalPaid', 'remainingBalance'));
    }

    /**
     * Show form to submit tuition payment proof (full or installment).
     */
    public function bayar(): View|RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $tagihan = $calonSiswa->tagihan()->latest('id')->first();

        if (! $tagihan) {
            return redirect()->route('calon-siswa.daftar-ulang.index')
                ->with('error', 'Tagihan daftar ulang belum diterbitkan oleh panitia/bendahara.');
        }

        $remainingBalance = $this->invoiceService->getRemainingBalance($tagihan);

        if ($remainingBalance <= 0) {
            return redirect()->route('calon-siswa.daftar-ulang.index')
                ->with('info', 'Tagihan daftar ulang Anda sudah lunas.');
        }

        return view('calon-siswa.daftar-ulang.bayar', compact('calonSiswa', 'tagihan', 'remainingBalance'));
    }

    /**
     * Store candidate's tuition payment proof.
     */
    public function storeBayar(Request $request): RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $tagihan = $calonSiswa->tagihan()->latest('id')->first();

        if (! $tagihan) {
            return redirect()->route('calon-siswa.daftar-ulang.index')
                ->with('error', 'Tagihan tidak ditemukan.');
        }

        $remainingBalance = $this->invoiceService->getRemainingBalance($tagihan);

        $validated = $request->validate([
            'nominal_dibayar' => ['required', 'numeric', 'min:10000', "max:{$remainingBalance}"],
            'tanggal_bayar' => ['required', 'date'],
            'bank_pengirim' => ['required', 'string', 'max:100'],
            'nama_pengirim' => ['required', 'string', 'max:150'],
            'nomor_referensi' => ['nullable', 'string', 'max:100'],
            'bukti_transfer' => ['required', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:2048'],
        ], [
            'nominal_dibayar.max' => 'Nominal yang dibayarkan tidak boleh melebihi sisa tagihan (Rp ' . number_format($remainingBalance, 0, ',', '.') . ').',
            'bukti_transfer.max' => 'Ukuran berkas bukti transfer maksimal 2MB.',
        ]);

        $filePath = $this->fileUploadService->uploadPaymentProof(
            $request->file('bukti_transfer'),
            'daftar_ulang'
        );

        $pembayaran = PembayaranDaftarUlang::create([
            'calon_siswa_id' => $calonSiswa->id,
            'tagihan_id' => $tagihan->id,
            'nominal_tagihan' => $tagihan->total_netto,
            'nominal_dibayar' => $validated['nominal_dibayar'],
            'tanggal_bayar' => $validated['tanggal_bayar'],
            'metode_bayar' => 'transfer_bank',
            'bank_pengirim' => $validated['bank_pengirim'],
            'nama_pengirim' => $validated['nama_pengirim'],
            'nomor_referensi' => $validated['nomor_referensi'] ?? null,
            'bukti_transfer_path' => $filePath,
            'status' => PaymentStatus::PENDING->value,
        ]);

        if (function_exists('activity')) {
            activity('finance')
                ->performedOn($pembayaran)
                ->causedBy(auth()->user())
                ->withProperties([
                    'calon_siswa_id' => $calonSiswa->id,
                    'nominal_dibayar' => $pembayaran->nominal_dibayar,
                ])
                ->log("Konfirmasi pembayaran daftar ulang Rp " . number_format($pembayaran->nominal_dibayar, 0, ',', '.') . " diunggah oleh siswa #{$calonSiswa->nomor_pendaftaran}");
        }

        return redirect()->route('calon-siswa.daftar-ulang.index')
            ->with('success', 'Bukti transfer pembayaran daftar ulang berhasil dikirim. Menunggu verifikasi Bendahara.');
    }

    /**
     * Download or stream student's tuition invoice PDF.
     */
    public function cetakTagihan(): Response
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $tagihan = $calonSiswa->tagihan()->latest('id')->first();

        if (! $tagihan) {
            abort(404, 'Tagihan daftar ulang belum tersedia.');
        }

        $pdf = $this->pdfService->generateTagihan($tagihan);

        return $pdf->stream("Tagihan-DaftarUlang-{$tagihan->nomor_tagihan}.pdf");
    }

    /**
     * Download or stream receipt kwitansi PDF for a verified payment.
     */
    public function cetakKwitansi(PembayaranDaftarUlang $pembayaranDaftarUlang): Response
    {
        $calonSiswa = auth()->user()->calonSiswa;

        if ($pembayaranDaftarUlang->calon_siswa_id !== $calonSiswa->id) {
            abort(403, 'Akses ditolak.');
        }

        if ($pembayaranDaftarUlang->status !== PaymentStatus::DIVERIFIKASI->value) {
            abort(403, 'Kwitansi hanya dapat diunduh untuk pembayaran yang telah diverifikasi.');
        }

        $pdf = $this->pdfService->generateBuktiPembayaran($pembayaranDaftarUlang, 'Pembayaran Daftar Ulang');

        return $pdf->stream("Kwitansi-DaftarUlang-{$pembayaranDaftarUlang->id}.pdf");
    }
}
