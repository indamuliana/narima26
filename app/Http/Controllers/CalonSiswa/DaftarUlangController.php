<?php

namespace App\Http\Controllers\CalonSiswa;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\PembayaranDaftarUlang;
use App\Models\Tagihan;
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
            'ukuranSeragam.jenisSeragam',
        ]);

        $tagihanDU  = $calonSiswa->tagihan->where('jenis_tagihan', Tagihan::JENIS_DAFTAR_ULANG)->last()
            ?? $calonSiswa->tagihan->first();
        
        $tagihanSRGList = $calonSiswa->tagihan->where('jenis_tagihan', Tagihan::JENIS_SERAGAM)->sortBy('tahap_seragam')->values();
        $tagihanSRG = $tagihanSRGList->last();

        $totalPaidDU  = $tagihanDU ? $this->invoiceService->getTotalPaidVerified($tagihanDU) : 0;
        $remainingDU  = $tagihanDU ? $this->invoiceService->getRemainingBalance($tagihanDU) : 0;

        $totalPaidSRG = $tagihanSRG ? $this->invoiceService->getTotalPaidVerified($tagihanSRG) : 0;
        $remainingSRG = $tagihanSRG ? $this->invoiceService->getRemainingBalance($tagihanSRG) : 0;

        // Seragam yang masih berstatus "Pesan Nanti" / belum ada tagihannya
        $pendingSeragamList = $calonSiswa->ukuranSeragam
            ->whereNull('tagihan_id')
            ->filter(fn($item) => $item->status_pemesanan === \App\Models\UkuranSeragam::STATUS_PESAN_NANTI || !$item->beli_di_sekolah)
            ->values();

        // Peta harga seragam aktif
        $biayaSeragamList = \App\Models\MasterBiaya::aktif()
            ->where('kategori', 'SERAGAM')
            ->when($calonSiswa->jenis_kelamin, function ($q) use ($calonSiswa) {
                $q->where(function ($sub) use ($calonSiswa) {
                    $sub->whereNull('jenis_kelamin')
                        ->orWhere('jenis_kelamin', $calonSiswa->jenis_kelamin);
                });
            })
            ->get();

        // Legacy compatibility
        $tagihan          = $tagihanDU ?? $tagihanSRG;
        $totalPaid        = $totalPaidDU;
        $remainingBalance = $remainingDU;

        return view('calon-siswa.daftar-ulang.index', compact(
            'calonSiswa',
            'tagihanDU', 'totalPaidDU', 'remainingDU',
            'tagihanSRG', 'tagihanSRGList', 'totalPaidSRG', 'remainingSRG',
            'pendingSeragamList', 'biayaSeragamList',
            'tagihan', 'totalPaid', 'remainingBalance'
        ));
    }

    /**
     * Show form to submit tuition/uniform payment proof.
     */
    public function bayar(Request $request): View|RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $tagihanId  = $request->query('tagihan_id');

        $tagihan = $tagihanId
            ? $calonSiswa->tagihan()->find($tagihanId)
            : ($calonSiswa->tagihan()->where('jenis_tagihan', Tagihan::JENIS_DAFTAR_ULANG)->first()
               ?? $calonSiswa->tagihan()->latest('id')->first());

        if (! $tagihan) {
            return redirect()->route('calon-siswa.daftar-ulang.index')
                ->with('error', 'Tagihan belum diterbitkan oleh panitia/bendahara.');
        }

        $remainingBalance = $this->invoiceService->getRemainingBalance($tagihan);

        if ($remainingBalance <= 0) {
            return redirect()->route('calon-siswa.daftar-ulang.index')
                ->with('info', "Tagihan #{$tagihan->nomor_tagihan} Ananda sudah lunas.");
        }

        $tagihan->load('diskon');

        return view('calon-siswa.daftar-ulang.bayar', compact('calonSiswa', 'tagihan', 'remainingBalance'));
    }

    /**
     * Store candidate's payment proof for a specific invoice.
     */
    public function storeBayar(Request $request): RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $tagihanId  = $request->input('tagihan_id');

        $tagihan = $tagihanId
            ? $calonSiswa->tagihan()->find($tagihanId)
            : ($calonSiswa->tagihan()->where('jenis_tagihan', Tagihan::JENIS_DAFTAR_ULANG)->latest('id')->first()
               ?? $calonSiswa->tagihan()->latest('id')->first());

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
            'bukti_transfer' => ['required', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:10240'],
        ], [
            'nominal_dibayar.max' => 'Nominal yang dibayarkan tidak boleh melebihi sisa tagihan (Rp ' . number_format($remainingBalance, 0, ',', '.') . ').',
            'bukti_transfer.max' => 'Ukuran berkas bukti transfer maksimal 10MB.',
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
    public function cetakTagihan(Request $request): Response
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $tagihanId  = $request->query('tagihan_id');

        $tagihan = $tagihanId
            ? $calonSiswa->tagihan()->find($tagihanId)
            : ($calonSiswa->tagihan()->where('jenis_tagihan', Tagihan::JENIS_DAFTAR_ULANG)->latest('id')->first()
               ?? $calonSiswa->tagihan()->latest('id')->first());

        if (! $tagihan) {
            abort(404, 'Tagihan belum tersedia.');
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

    /**
     * Calon siswa mengaktifkan sisa seragam yang sebelumnya berstatus "Pesan Nanti".
     */
    public function aktivasiSeragam(Request $request): RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;

        $validated = $request->validate([
            'ukuran_seragam_ids' => ['required', 'array', 'min:1'],
            'ukuran_seragam_ids.*' => ['required', 'exists:ukuran_seragam,id'],
        ], [
            'ukuran_seragam_ids.required' => 'Pilih minimal satu seragam yang ingin dipesan sekarang.',
            'ukuran_seragam_ids.min' => 'Pilih minimal satu seragam yang ingin dipesan sekarang.',
        ]);

        // Verifikasi bahwa ukuran_seragam_ids milik calon siswa ini dan belum ada tagihannya
        $validIds = $calonSiswa->ukuranSeragam()
            ->whereIn('id', $validated['ukuran_seragam_ids'])
            ->whereNull('tagihan_id')
            ->pluck('id')
            ->toArray();

        if (empty($validIds)) {
            return redirect()->route('calon-siswa.daftar-ulang.index', ['tab' => 'seragam'])
                ->with('error', 'Tidak ada seragam tertunda yang valid untuk dipesan.');
        }

        try {
            $tagihan = $this->invoiceService->activatePendingUniforms($calonSiswa, $validIds, auth()->user());

            return redirect()->route('calon-siswa.daftar-ulang.index', ['tab' => 'seragam'])
                ->with('success', "Pemesanan seragam berhasil diaktifkan! Tagihan susulan #{$tagihan->nomor_tagihan} telah diterbitkan dan siap dibayar.");
        } catch (\Exception $e) {
            return redirect()->route('calon-siswa.daftar-ulang.index', ['tab' => 'seragam'])
                ->with('error', 'Gagal menerbitkan tagihan susulan: ' . $e->getMessage());
        }
    }
}
