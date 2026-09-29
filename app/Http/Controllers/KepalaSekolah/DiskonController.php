<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\Diskon;
use App\Models\MasterDiskon;
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
     * Display executive discount management dashboard for Kepala Sekolah.
     */
    public function index(Request $request): View
    {
        $search        = $request->input('q');
        $jenisDiskon   = $request->input('jenis_diskon');
        $jurusanId     = $request->input('jurusan_id');
        $tanggalDari   = $request->input('tanggal_dari');
        $tanggalSampai = $request->input('tanggal_sampai');
        $tab           = $request->input('tab', 'siswa');

        // Query riwayat diskon yang telah diberikan kepada calon siswa
        $query = Diskon::query()
            ->with(['calonSiswa.jurusan', 'calonSiswa.program', 'diberikanOleh', 'disetujuiOleh', 'calonSiswa.tagihan']);

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

        $diskonList = $query->latest('id')->paginate(15, ['*'], 'siswa_page')->withQueryString();

        // Query Master Kebijakan Diskon (Template Baku)
        $masterDiskonPaged = MasterDiskon::latest()->paginate(10, ['*'], 'master_page')->withQueryString();

        // Statistik eksekutif
        $stats = [
            'total_discounts' => Diskon::count(),
            'total_amount'    => (float) Diskon::sum('nominal_potongan'),
            'total_master'    => MasterDiskon::where('is_active', true)->count(),
            'breakdown'       => Diskon::selectRaw('jenis_diskon, COUNT(*) as jumlah, SUM(nominal_potongan) as total')
                ->groupBy('jenis_diskon')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),
        ];

        $jurusanList = MasterJurusan::orderBy('nama')->get();

        $jenisDiskonList = Diskon::select('jenis_diskon')
            ->distinct()
            ->orderBy('jenis_diskon')
            ->pluck('jenis_diskon');

        // Calon siswa yang memiliki tagihan aktif tanpa diskon (semua jenis tagihan: DAFTAR_ULANG, SERAGAM, dll)
        $siswaWithTagihan = CalonSiswa::query()
            ->whereHas('tagihan', function ($q) {
                $q->whereIn('status', [Tagihan::STATUS_BELUM_LUNAS, Tagihan::STATUS_CICILAN])
                  ->whereNull('diskon_id');
            })
            ->with(['jurusan', 'program', 'tagihan' => function ($q) {
                $q->whereIn('status', [Tagihan::STATUS_BELUM_LUNAS, Tagihan::STATUS_CICILAN])
                  ->whereNull('diskon_id')
                  ->orderBy('id');
            }])
            ->orderBy('nama_lengkap')
            ->get();

        $siswaTagihanOptions = $siswaWithTagihan->map(function ($cs) {
            return [
                'id'                => $cs->id,
                'nama_lengkap'      => $cs->nama_lengkap,
                'nomor_pendaftaran' => $cs->nomor_pendaftaran,
                'jurusan'           => $cs->jurusan?->nama ?? '—',
                'program'           => $cs->program?->nama ?? '—',
                'tagihans'          => $cs->tagihan->map(function ($t) {
                    $jenisLabel = match ($t->jenis_tagihan) {
                        Tagihan::JENIS_DAFTAR_ULANG => 'Daftar Ulang',
                        Tagihan::JENIS_SERAGAM      => 'Seragam',
                        default                     => $t->jenis_tagihan,
                    };

                    return [
                        'id'            => $t->id,
                        'nomor_tagihan' => $t->nomor_tagihan,
                        'jenis_tagihan' => $t->jenis_tagihan,
                        'jenis_label'   => $jenisLabel,
                        'total_bruto'   => (float) $t->total_bruto,
                        'total_netto'   => (float) $t->total_netto,
                        'status'        => $t->status,
                        'label'         => "[{$jenisLabel}] #{$t->nomor_tagihan} — Rp " . number_format((float) $t->total_bruto, 0, ',', '.') . " ({$t->status})",
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        $masterDiskonList = MasterDiskon::where('is_active', true)->get();

        return view('kepala-sekolah.diskon.index', compact(
            'diskonList', 'masterDiskonPaged', 'stats', 'search',
            'jenisDiskon', 'jurusanId', 'tanggalDari', 'tanggalSampai',
            'jurusanList', 'jenisDiskonList', 'siswaWithTagihan', 'siswaTagihanOptions', 'masterDiskonList', 'tab'
        ));
    }

    /**
     * Grant a new discount directly by Kepala Sekolah for any invoice type.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tagihan_id'     => ['nullable', 'exists:tagihan,id'],
            'calon_siswa_id' => ['nullable', 'exists:calon_siswa,id'],
            'jenis_diskon'   => ['required', 'string', 'max:100'],
            'metode_diskon'  => ['required', 'in:nominal,persentase'],
            'nilai_diskon'   => ['required', 'numeric', 'min:1'],
            'alasan'         => ['required', 'string', 'max:1000'],
            'keterangan'     => ['nullable', 'string', 'max:1000'],
        ]);

        if (empty($validated['tagihan_id']) && empty($validated['calon_siswa_id'])) {
            return redirect()->route('kepala-sekolah.diskon.index', ['tab' => 'siswa'])
                ->with('error', 'Silakan tentukan tagihan calon siswa yang akan diberikan diskon.');
        }

        $tagihan = null;
        if (! empty($validated['tagihan_id'])) {
            $tagihan = Tagihan::with('calonSiswa')->find($validated['tagihan_id']);
        } elseif (! empty($validated['calon_siswa_id'])) {
            $tagihan = Tagihan::with('calonSiswa')
                ->where('calon_siswa_id', $validated['calon_siswa_id'])
                ->whereIn('status', [Tagihan::STATUS_BELUM_LUNAS, Tagihan::STATUS_CICILAN])
                ->whereNull('diskon_id')
                ->latest('id')
                ->first();
        }

        if (! $tagihan) {
            return redirect()->route('kepala-sekolah.diskon.index', ['tab' => 'siswa'])
                ->with('error', 'Tagihan yang dipilih tidak valid atau tidak memiliki tagihan aktif.');
        }

        if ($tagihan->status === Tagihan::STATUS_LUNAS) {
            return redirect()->route('kepala-sekolah.diskon.index', ['tab' => 'siswa'])
                ->with('error', "Tagihan #{$tagihan->nomor_tagihan} sudah LUNAS dan tidak dapat diberikan diskon.");
        }

        if ($tagihan->diskon_id !== null) {
            return redirect()->route('kepala-sekolah.diskon.index', ['tab' => 'siswa'])
                ->with('error', "Tagihan #{$tagihan->nomor_tagihan} sudah memiliki diskon yang aktif.");
        }

        $calonSiswa = $tagihan->calonSiswa;
        if (! $calonSiswa) {
            return redirect()->route('kepala-sekolah.diskon.index', ['tab' => 'siswa'])
                ->with('error', 'Data calon siswa untuk tagihan ini tidak ditemukan.');
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

        $jenisLabel = match ($tagihan->jenis_tagihan) {
            Tagihan::JENIS_DAFTAR_ULANG => 'Daftar Ulang',
            Tagihan::JENIS_SERAGAM      => 'Seragam',
            default                     => $tagihan->jenis_tagihan,
        };

        return redirect()->route('kepala-sekolah.diskon.index', ['tab' => 'siswa'])
            ->with('success', "Diskon {$diskon->jenis_diskon} berhasil diberikan untuk tagihan {$jenisLabel} (#{$tagihan->nomor_tagihan}) calon siswa {$calonSiswa->nama_lengkap}.");
    }

    /**
     * Revoke / cancel a discount awarded to a candidate.
     */
    public function destroy(Diskon $diskon): RedirectResponse
    {
        $tagihan = Tagihan::where('diskon_id', $diskon->id)->first();

        if ($tagihan) {
            if ($tagihan->status === Tagihan::STATUS_LUNAS) {
                return redirect()->route('kepala-sekolah.diskon.index', ['tab' => 'siswa'])
                    ->with('error', "Diskon tidak dapat dibatalkan karena tagihan #{$tagihan->nomor_tagihan} sudah berstatus LUNAS.");
            }

            $tagihan->update([
                'diskon_id'    => null,
                'total_diskon' => 0,
                'total_netto'  => $tagihan->total_bruto,
            ]);

            $this->invoiceService->syncPaymentStatus($tagihan);
        }

        $namaDiskon = $diskon->jenis_diskon;
        $namaSiswa  = $diskon->calonSiswa?->nama_lengkap ?? '—';

        $diskon->delete();

        return redirect()->route('kepala-sekolah.diskon.index', ['tab' => 'siswa'])
            ->with('success', "Diskon {$namaDiskon} untuk {$namaSiswa} berhasil dicabut.");
    }

    /**
     * Store new master discount policy.
     */
    public function storeMaster(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_diskon'   => ['required', 'string', 'max:255'],
            'metode_diskon' => ['required', 'in:nominal,persentase'],
            'nilai_diskon'  => ['required', 'numeric', 'min:0'],
            'deskripsi'     => ['nullable', 'string', 'max:1000'],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        MasterDiskon::create($validated);

        return redirect()->route('kepala-sekolah.diskon.index', ['tab' => 'master'])
            ->with('success', "Kebijakan master diskon {$validated['nama_diskon']} berhasil ditambahkan.");
    }

    /**
     * Update existing master discount policy.
     */
    public function updateMaster(Request $request, MasterDiskon $masterDiskon): RedirectResponse
    {
        $validated = $request->validate([
            'nama_diskon'   => ['required', 'string', 'max:255'],
            'metode_diskon' => ['required', 'in:nominal,persentase'],
            'nilai_diskon'  => ['required', 'numeric', 'min:0'],
            'deskripsi'     => ['nullable', 'string', 'max:1000'],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        $masterDiskon->update($validated);

        return redirect()->route('kepala-sekolah.diskon.index', ['tab' => 'master'])
            ->with('success', "Kebijakan master diskon {$masterDiskon->nama_diskon} berhasil diperbarui.");
    }

    /**
     * Delete master discount policy.
     */
    public function destroyMaster(MasterDiskon $masterDiskon): RedirectResponse
    {
        $nama = $masterDiskon->nama_diskon;
        $masterDiskon->delete();

        return redirect()->route('kepala-sekolah.diskon.index', ['tab' => 'master'])
            ->with('success', "Kebijakan master diskon {$nama} berhasil dihapus.");
    }

    /**
     * AJAX Search for students eligible for discounts.
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
                  ->orderBy('id');
            }])
            ->where(function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', "%{$keyword}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$keyword}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($cs) {
                return [
                    'id'                => $cs->id,
                    'nama_lengkap'      => $cs->nama_lengkap,
                    'nomor_pendaftaran' => $cs->nomor_pendaftaran,
                    'jurusan'           => $cs->jurusan?->nama ?? '—',
                    'tagihans'          => $cs->tagihan->map(function ($t) {
                        $jenisLabel = match ($t->jenis_tagihan) {
                            Tagihan::JENIS_DAFTAR_ULANG => 'Daftar Ulang',
                            Tagihan::JENIS_SERAGAM      => 'Seragam',
                            default                     => $t->jenis_tagihan,
                        };
                        return [
                            'id'            => $t->id,
                            'nomor_tagihan' => $t->nomor_tagihan,
                            'jenis_tagihan' => $t->jenis_tagihan,
                            'jenis_label'   => $jenisLabel,
                            'total_bruto'   => (float) $t->total_bruto,
                            'total_netto'   => (float) $t->total_netto,
                            'status'        => $t->status,
                            'label'         => "[{$jenisLabel}] #{$t->nomor_tagihan} — Rp " . number_format((float) $t->total_bruto, 0, ',', '.') . " ({$t->status})",
                        ];
                    })->values()->all(),
                ];
            });

        return response()->json($results);
    }
}
