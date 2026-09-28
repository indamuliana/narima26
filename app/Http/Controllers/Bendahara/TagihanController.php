<?php

namespace App\Http\Controllers\Bendahara;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\Tagihan;
use App\Services\InvoiceService;
use App\Services\PdfService;
use App\Services\SpmbStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TagihanController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService,
        protected PdfService $pdfService,
        protected SpmbStatusService $spmbStatusService
    ) {}

    /**
     * Display list of all issued tuition/registration invoices.
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');
        $status = $request->input('status');

        $query = Tagihan::query()
            ->with(['calonSiswa.jurusan', 'calonSiswa.program', 'diskon', 'pembayaran']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_tagihan', 'like', "%{$search}%")
                  ->orWhereHas('calonSiswa', function ($sq) use ($search) {
                      $sq->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nomor_pendaftaran', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                  });
            });
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        $tagihanList = $query->latest('id')->paginate(15)->withQueryString();

        // Calculate aggregate statistics
        $stats = [
            'total_invoices' => Tagihan::count(),
            'total_netto' => (float) Tagihan::sum('total_netto'),
            'total_lunas' => Tagihan::where('status', Tagihan::STATUS_LUNAS)->count(),
            'total_cicilan' => Tagihan::where('status', Tagihan::STATUS_CICILAN)->count(),
            'total_belum_lunas' => Tagihan::where('status', Tagihan::STATUS_BELUM_LUNAS)->count(),
        ];

        return view('bendahara.tagihan.index', compact('tagihanList', 'stats', 'search', 'status'));
    }

    /**
     * Show form / candidate picker to generate a new registration fee invoice.
     */
    public function create(): View
    {
        // Candidates eligible for invoice: SUDAH_DIWAWANCARA, DITERIMA, MENUNGGU_DAFTAR_ULANG without active invoice
        $eligibleCandidates = CalonSiswa::query()
            ->with(['jurusan', 'program', 'gelombang'])
            ->whereIn('status_spmb', [
                SpmbStatus::SUDAH_DIWAWANCARA->value,
                SpmbStatus::DITERIMA->value,
                SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
            ])
            ->whereDoesntHave('tagihan')
            ->orderBy('nama_lengkap')
            ->get();

        return view('bendahara.tagihan.create', compact('eligibleCandidates'));
    }

    /**
     * Generate snapshot invoice for a candidate.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'calon_siswa_id' => ['required', 'exists:calon_siswa,id'],
        ]);

        $calonSiswa = CalonSiswa::findOrFail($validated['calon_siswa_id']);

        if ($calonSiswa->tagihan()->exists()) {
            return redirect()->route('bendahara.tagihan.index')
                ->with('error', 'Tagihan untuk calon siswa ini sudah pernah diterbitkan.');
        }

        $tagihan = $this->invoiceService->generateInvoice($calonSiswa, actor: auth()->user());

        // Advance candidate status to MENUNGGU_DAFTAR_ULANG if valid
        $currentStatus = is_string($calonSiswa->status_spmb)
            ? SpmbStatus::from($calonSiswa->status_spmb)
            : $calonSiswa->status_spmb;

        if (in_array($currentStatus, [SpmbStatus::SUDAH_DIWAWANCARA, SpmbStatus::DITERIMA], true)) {
            $this->spmbStatusService->changeStatus(
                calonSiswa: $calonSiswa,
                targetStatus: SpmbStatus::MENUNGGU_DAFTAR_ULANG,
                alasan: 'Tagihan daftar ulang resmi telah diterbitkan oleh Bendahara',
                catatan: "Nomor Tagihan: {$tagihan->nomor_tagihan}",
                changedBy: auth()->user()
            );
        }

        return redirect()->route('bendahara.tagihan.show', $tagihan)
            ->with('success', "Tagihan daftar ulang #{$tagihan->nomor_tagihan} berhasil diterbitkan.");
    }

    /**
     * Show detail of an invoice, its frozen snapshot components, discounts, and payments.
     */
    public function show(Tagihan $tagihan): View
    {
        $tagihan->load([
            'calonSiswa.jurusan',
            'calonSiswa.program',
            'calonSiswa.dataOrangtua',
            'details',
            'diskon.diberikanOleh',
            'pembayaran.verifiedBy',
        ]);

        $totalPaid = $this->invoiceService->getTotalPaidVerified($tagihan);
        $remainingBalance = $this->invoiceService->getRemainingBalance($tagihan);

        return view('bendahara.tagihan.show', compact('tagihan', 'totalPaid', 'remainingBalance'));
    }

    /**
     * Download or stream PDF of the tuition invoice.
     */
    public function cetakPdf(Tagihan $tagihan): Response
    {
        $pdf = $this->pdfService->generateTagihan($tagihan);

        return $pdf->stream("Tagihan-{$tagihan->nomor_tagihan}.pdf");
    }
}
