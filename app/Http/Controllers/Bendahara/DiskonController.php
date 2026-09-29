<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\Diskon;
use App\Models\MasterJurusan;
use App\Models\Tagihan;
use App\Services\DiscountService;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
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
     * Display list of all discounts given with filtering.
     */
    public function index(Request $request): View
    {
        $search       = $request->input('q');
        $jenisDiskon  = $request->input('jenis_diskon');
        $jurusanId    = $request->input('jurusan_id');
        $tanggalDari  = $request->input('tanggal_dari');
        $tanggalSampai = $request->input('tanggal_sampai');

        $query = Diskon::query()
            ->with(['calonSiswa.jurusan', 'calonSiswa.program', 'diberikanOleh', 'calonSiswa.tagihan']);

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

        if (! empty($jenisDiskon)) {
            $query->where('jenis_diskon', 'like', "%{$jenisDiskon}%");
        }

        if (! empty($jurusanId)) {
            $query->whereHas('calonSiswa', fn ($q) => $q->where('jurusan_id', $jurusanId));
        }

        if (! empty($tanggalDari)) {
            $query->whereDate('created_at', '>=', $tanggalDari);
        }

        if (! empty($tanggalSampai)) {
            $query->whereDate('created_at', '<=', $tanggalSampai);
        }

        $diskonList = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_discounts' => Diskon::count(),
            'total_amount'    => (float) Diskon::sum('nominal_potongan'),
            'breakdown'       => Diskon::selectRaw('jenis_diskon, COUNT(*) as jumlah, SUM(nominal_potongan) as total')
                ->groupBy('jenis_diskon')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),
        ];

        $jurusanList = MasterJurusan::orderBy('nama')->get();

        // Daftar jenis diskon unik untuk dropdown filter
        $jenisDiskonList = Diskon::select('jenis_diskon')
            ->distinct()
            ->orderBy('jenis_diskon')
            ->pluck('jenis_diskon');

        // Semua calon siswa yang memiliki tagihan aktif (belum lunas, belum ada diskon)
        $siswaWithTagihan = CalonSiswa::query()
            ->whereHas('tagihan', function ($q) {
                $q->whereIn('status', [Tagihan::STATUS_BELUM_LUNAS, Tagihan::STATUS_CICILAN])
                  ->whereNull('diskon_id');
            })
            ->with(['jurusan', 'program', 'tagihan' => function ($q) {
                $q->whereIn('status', [Tagihan::STATUS_BELUM_LUNAS, Tagihan::STATUS_CICILAN])
                  ->whereNull('diskon_id')
                  ->latest('id');
            }])
            ->orderBy('nama_lengkap')
            ->get();

        $masterDiskonList = \App\Models\MasterDiskon::where('is_active', true)->get();

        return view('bendahara.diskon.index', compact(
            'diskonList', 'stats', 'search',
            'jenisDiskon', 'jurusanId', 'tanggalDari', 'tanggalSampai',
            'jurusanList', 'jenisDiskonList', 'siswaWithTagihan', 'masterDiskonList'
        ));
    }

    /**
     * Apply discount directly to a tuition invoice (from tagihan detail page).
     */
    public function store(Request $request, Tagihan $tagihan): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_diskon'  => ['required', 'string', 'max:100'],
            'metode_diskon' => ['required', 'in:nominal,persentase'],
            'nilai_diskon'  => ['required', 'numeric', 'min:1'],
            'alasan'        => ['required', 'string', 'max:1000'],
            'keterangan'    => ['nullable', 'string', 'max:1000'],
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

    /**
     * Apply discount from the Kelola Diskon index page — search siswa, link to their active invoice.
     */
    public function storeFromIndex(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'calon_siswa_id' => ['required', 'exists:calon_siswa,id'],
            'jenis_diskon'   => ['required', 'string', 'max:100'],
            'metode_diskon'  => ['required', 'in:nominal,persentase'],
            'nilai_diskon'   => ['required', 'numeric', 'min:1'],
            'alasan'         => ['required', 'string', 'max:1000'],
            'keterangan'     => ['nullable', 'string', 'max:1000'],
        ]);

        $calonSiswa = CalonSiswa::findOrFail($validated['calon_siswa_id']);

        // Cari tagihan aktif (belum lunas) milik siswa ini
        $tagihan = Tagihan::where('calon_siswa_id', $calonSiswa->id)
            ->whereIn('status', [Tagihan::STATUS_BELUM_LUNAS, Tagihan::STATUS_CICILAN])
            ->whereNull('diskon_id')
            ->latest('id')
            ->first();

        if (! $tagihan) {
            return redirect()->route('bendahara.diskon.index')
                ->with('error', "Siswa {$calonSiswa->nama_lengkap} tidak memiliki tagihan aktif yang belum mendapat diskon.");
        }

        $diskon = $this->discountService->createDiscount(
            calonSiswa: $calonSiswa,
            data: $validated,
            grantedBy: auth()->user(),
            approvedBy: auth()->user(),
            referensiBruto: (float) $tagihan->total_bruto
        );

        $this->discountService->applyDiscountToInvoice($tagihan, $diskon, auth()->user());
        $this->invoiceService->syncPaymentStatus($tagihan);

        return redirect()->route('bendahara.diskon.index')
            ->with('success', "Diskon {$diskon->jenis_diskon} berhasil diterapkan untuk {$calonSiswa->nama_lengkap} (Tagihan #{$tagihan->nomor_tagihan}).");
    }

    /**
     * Revoke a discount — remove it from the invoice and recalculate totals.
     */
    public function destroy(Diskon $diskon): RedirectResponse
    {
        // Cari tagihan yang memakai diskon ini
        $tagihan = Tagihan::where('diskon_id', $diskon->id)->first();

        if ($tagihan) {
            if ($tagihan->status === Tagihan::STATUS_LUNAS) {
                return redirect()->back()
                    ->with('error', "Diskon tidak dapat dicabut karena tagihan #{$tagihan->nomor_tagihan} sudah LUNAS.");
            }

            // Kembalikan tagihan ke nilai bruto
            $tagihan->update([
                'diskon_id'    => null,
                'total_diskon' => 0,
                'total_netto'  => $tagihan->total_bruto,
            ]);

            app(\App\Services\InvoiceService::class)->syncPaymentStatus($tagihan);
        }

        $namaDiskon   = $diskon->jenis_diskon;
        $namaSiswa    = $diskon->calonSiswa?->nama_lengkap ?? '—';

        $diskon->delete();

        return redirect()->back()
            ->with('success', "Diskon {$namaDiskon} untuk {$namaSiswa} berhasil dicabut.");
    }

    /**
     * AJAX: Search calon siswa yang memiliki tagihan aktif (belum lunas, belum ada diskon).
     */
    public function searchSiswa(Request $request): JsonResponse
    {
        $keyword = $request->input('q', '');

        $results = CalonSiswa::query()
            ->whereHas('tagihan', function ($q) {
                $q->whereIn('status', [Tagihan::STATUS_BELUM_LUNAS, Tagihan::STATUS_CICILAN])
                  ->whereNull('diskon_id');
            })
            ->with(['jurusan', 'tagihan' => function ($q) {
                $q->whereIn('status', [Tagihan::STATUS_BELUM_LUNAS, Tagihan::STATUS_CICILAN])
                  ->whereNull('diskon_id')
                  ->latest('id');
            }])
            ->where(function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', "%{$keyword}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$keyword}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($cs) {
                $tagihan = $cs->tagihan->first();
                return [
                    'id'                => $cs->id,
                    'nama_lengkap'      => $cs->nama_lengkap,
                    'nomor_pendaftaran' => $cs->nomor_pendaftaran,
                    'jurusan'           => $cs->jurusan?->nama ?? '—',
                    'tagihan_id'        => $tagihan?->id,
                    'nomor_tagihan'     => $tagihan?->nomor_tagihan ?? '—',
                    'total_bruto'       => $tagihan ? number_format((float) $tagihan->total_bruto, 0, ',', '.') : '—',
                    'total_netto'       => $tagihan ? number_format((float) $tagihan->total_netto, 0, ',', '.') : '—',
                    'status_tagihan'    => $tagihan?->status ?? '—',
                ];
            });

        return response()->json($results);
    }
}
