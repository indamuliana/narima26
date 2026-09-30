<?php

namespace App\Http\Controllers\Bendahara;

use App\Enums\PaymentStatus;
use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Models\PembayaranDaftarUlang;
use App\Services\InvoiceService;
use App\Services\PdfService;
use App\Services\SpmbStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PembayaranDaftarUlangController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService,
        protected PdfService $pdfService,
        protected SpmbStatusService $spmbStatusService
    ) {}

    /**
     * Display list of tuition/re-registration payment transactions.
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');
        $status = $request->input('status');

        $query = PembayaranDaftarUlang::query()
            ->with(['calonSiswa.jurusan', 'calonSiswa.jurusan2', 'calonSiswa.program', 'tagihan', 'verifiedBy']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_referensi', 'like', "%{$search}%")
                  ->orWhere('nama_pengirim', 'like', "%{$search}%")
                  ->orWhereHas('calonSiswa', function ($sq) use ($search) {
                      $sq->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nomor_pendaftaran', 'like', "%{$search}%");
                  })
                  ->orWhereHas('tagihan', function ($tq) use ($search) {
                      $tq->where('nomor_tagihan', 'like', "%{$search}%");
                  });
            });
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        $pembayaranList = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_transaksi' => PembayaranDaftarUlang::count(),
            'pending' => PembayaranDaftarUlang::where('status', PaymentStatus::PENDING->value)->count(),
            'diverifikasi' => PembayaranDaftarUlang::where('status', PaymentStatus::DIVERIFIKASI->value)->count(),
            'total_dana_masuk' => (float) PembayaranDaftarUlang::where('status', PaymentStatus::DIVERIFIKASI->value)->sum('nominal_dibayar'),
            'ditolak' => PembayaranDaftarUlang::where('status', PaymentStatus::DITOLAK->value)->count(),
        ];

        return view('bendahara.pembayaran-daftar-ulang.index', compact('pembayaranList', 'stats', 'search', 'status'));
    }

    /**
     * Show detail of a single tuition payment submission with verification controls.
     */
    public function show(PembayaranDaftarUlang $pembayaranDaftarUlang): View
    {
        $pembayaranDaftarUlang->load([
            'calonSiswa.jurusan',
            'calonSiswa.program',
            'calonSiswa.dataOrangtua',
            'tagihan.details',
            'tagihan.diskon',
            'verifiedBy',
        ]);

        $tagihan = $pembayaranDaftarUlang->tagihan;
        $totalPaid = $this->invoiceService->getTotalPaidVerified($tagihan);
        $remainingBalance = $this->invoiceService->getRemainingBalance($tagihan);

        return view('bendahara.pembayaran-daftar-ulang.show', compact('pembayaranDaftarUlang', 'tagihan', 'totalPaid', 'remainingBalance'));
    }

    /**
     * Approve and verify candidate's re-registration payment.
     */
    public function verify(Request $request, PembayaranDaftarUlang $pembayaranDaftarUlang): RedirectResponse
    {
        $request->validate([
            'catatan_bendahara' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $pembayaranDaftarUlang) {
            $pembayaranDaftarUlang->update([
                'status' => PaymentStatus::DIVERIFIKASI->value,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'catatan_bendahara' => $request->input('catatan_bendahara') ?: 'Pembayaran daftar ulang diverifikasi valid oleh Bendahara.',
            ]);

            // Sync invoice balance and status (BELUM_LUNAS / CICILAN / LUNAS)
            $tagihan = $this->invoiceService->syncPaymentStatus($pembayaranDaftarUlang->tagihan);

            // Advance candidate SPMB status if currently MENUNGGU_DAFTAR_ULANG
            $calonSiswa = $pembayaranDaftarUlang->calonSiswa;
            $currentStatus = is_string($calonSiswa->status_spmb)
                ? SpmbStatus::from($calonSiswa->status_spmb)
                : $calonSiswa->status_spmb;

            if ($currentStatus === SpmbStatus::MENUNGGU_DAFTAR_ULANG) {
                $this->spmbStatusService->changeStatus(
                    calonSiswa: $calonSiswa,
                    targetStatus: SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI,
                    alasan: 'Pembayaran daftar ulang telah diverifikasi oleh Bendahara',
                    catatan: "Nomor Tagihan: {$tagihan->nomor_tagihan}, Nominal: Rp " . number_format($pembayaranDaftarUlang->nominal_dibayar, 0, ',', '.'),
                    changedBy: auth()->user()
                );
            }

            // Audit Trail
            activity('finance')
                ->performedOn($pembayaranDaftarUlang)
                ->causedBy(auth()->user())
                ->withProperties([
                    'calon_siswa_id' => $calonSiswa->id,
                    'nomor_pendaftaran' => $calonSiswa->nomor_pendaftaran,
                    'nominal_dibayar' => $pembayaranDaftarUlang->nominal_dibayar,
                    'tagihan_status' => $tagihan->status,
                ])
                ->log("Verifikasi pembayaran daftar ulang sebesar Rp " . number_format($pembayaranDaftarUlang->nominal_dibayar, 0, ',', '.') . " untuk {$calonSiswa->nama_lengkap}");
        });

        return redirect()->route('bendahara.pembayaran-daftar-ulang.show', $pembayaranDaftarUlang)
            ->with('success', 'Pembayaran daftar ulang berhasil diverifikasi. Status tagihan dan calon siswa telah diperbarui.');
    }

    /**
     * Reject candidate's re-registration payment with a mandatory explanation note.
     */
    public function reject(Request $request, PembayaranDaftarUlang $pembayaranDaftarUlang): RedirectResponse
    {
        $request->validate([
            'catatan_bendahara' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $pembayaranDaftarUlang) {
            $pembayaranDaftarUlang->update([
                'status' => PaymentStatus::DITOLAK->value,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'catatan_bendahara' => $request->input('catatan_bendahara'),
            ]);

            $this->invoiceService->syncPaymentStatus($pembayaranDaftarUlang->tagihan);

            activity('finance')
                ->performedOn($pembayaranDaftarUlang)
                ->causedBy(auth()->user())
                ->withProperties([
                    'calon_siswa_id' => $pembayaranDaftarUlang->calon_siswa_id,
                    'alasan_penolakan' => $request->input('catatan_bendahara'),
                ])
                ->log("Penolakan bukti transfer daftar ulang untuk calon siswa #{$pembayaranDaftarUlang->calonSiswa->nomor_pendaftaran}");
        });

        return redirect()->route('bendahara.pembayaran-daftar-ulang.show', $pembayaranDaftarUlang)
            ->with('success', 'Pembayaran daftar ulang telah ditolak dengan catatan alasan.');
    }

    /**
     * Print official receipt kwitansi PDF.
     */
    public function cetakKwitansi(PembayaranDaftarUlang $pembayaranDaftarUlang): Response
    {
        if ($pembayaranDaftarUlang->status !== PaymentStatus::DIVERIFIKASI->value) {
            abort(403, 'Kwitansi resmi hanya dapat dicetak setelah pembayaran diverifikasi.');
        }

        $pdf = $this->pdfService->generateBuktiPembayaran($pembayaranDaftarUlang, 'Pembayaran Daftar Ulang');

        return $pdf->stream("Kwitansi-DaftarUlang-{$pembayaranDaftarUlang->id}.pdf");
    }
}
