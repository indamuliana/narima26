<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Diskon;
use App\Models\Tagihan;
use App\Services\DiscountService;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiskonController extends Controller
{
    public function __construct(
        protected DiscountService $discountService,
        protected InvoiceService $invoiceService
    ) {}

    /**
     * Display list of all discounts given.
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');

        $query = Diskon::query()
            ->with(['calonSiswa.jurusan', 'calonSiswa.program', 'diberikanOleh']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('jenis_diskon', 'like', "%{$search}%")
                  ->orWhere('alasan', 'like', "%{$search}%")
                  ->orWhereHas('calonSiswa', function ($sq) use ($search) {
                      $sq->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nomor_pendaftaran', 'like', "%{$search}%");
                  });
            });
        }

        $diskonList = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_discounts' => Diskon::count(),
            'total_amount' => (float) Diskon::sum('nominal_potongan'),
        ];

        return view('bendahara.diskon.index', compact('diskonList', 'stats', 'search'));
    }

    /**
     * Apply discount directly to a tuition invoice.
     */
    public function store(Request $request, Tagihan $tagihan): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_diskon' => ['required', 'string', 'max:100'],
            'metode_diskon' => ['required', 'in:nominal,persentase'],
            'nilai_diskon' => ['required', 'numeric', 'min:1'],
            'alasan' => ['required', 'string', 'max:1000'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        $diskon = $this->discountService->createDiscount(
            calonSiswa: $tagihan->calonSiswa,
            data: $validated,
            grantedBy: auth()->user(),
            approvedBy: auth()->user(),
            referensiBruto: (float) $tagihan->total_bruto
        );

        $this->discountService->applyDiscountToInvoice($tagihan, $diskon, auth()->user());
        $this->invoiceService->syncPaymentStatus($tagihan);

        return redirect()->route('bendahara.tagihan.show', $tagihan)
            ->with('success', "Diskon {$diskon->jenis_diskon} berhasil diterapkan pada tagihan #{$tagihan->nomor_tagihan}.");
    }
}
