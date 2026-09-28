<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Services\WithdrawalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengunduranDiriController extends Controller
{
    public function __construct(
        protected WithdrawalService $withdrawalService
    ) {}

    /**
     * Display list of withdrawn candidates and active candidate pool.
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');
        $tab = $request->input('tab', 'withdrawn'); // 'withdrawn' or 'active'

        $withdrawnQuery = CalonSiswa::query()
            ->with(['jurusan', 'program', 'gelombang', 'statusHistory.changedBy'])
            ->where('status_spmb', SpmbStatus::MENGUNDURKAN_DIRI->value);

        $activeQuery = CalonSiswa::query()
            ->with(['jurusan', 'program', 'gelombang'])
            ->where('status_spmb', '!=', SpmbStatus::MENGUNDURKAN_DIRI->value);

        if (! empty($search)) {
            $filterClosure = function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            };

            $withdrawnQuery->where($filterClosure);
            $activeQuery->where($filterClosure);
        }

        $withdrawnList = $withdrawnQuery->latest('updated_at')->paginate(15, ['*'], 'withdrawn_page')->withQueryString();
        $activeList = $activeQuery->orderBy('nama_lengkap')->paginate(15, ['*'], 'active_page')->withQueryString();

        $stats = [
            'total_withdrawn' => CalonSiswa::where('status_spmb', SpmbStatus::MENGUNDURKAN_DIRI->value)->count(),
            'total_active' => CalonSiswa::where('status_spmb', '!=', SpmbStatus::MENGUNDURKAN_DIRI->value)->count(),
        ];

        return view('kepala-sekolah.pengunduran-diri.index', compact(
            'withdrawnList',
            'activeList',
            'stats',
            'search',
            'tab'
        ));
    }

    /**
     * Process withdrawal of a candidate.
     */
    public function store(Request $request, CalonSiswa $calonSiswa): RedirectResponse
    {
        $validated = $request->validate([
            'alasan' => ['required', 'string', 'max:1000'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->withdrawalService->withdraw(
            calonSiswa: $calonSiswa,
            alasan: $validated['alasan'],
            catatan: $validated['catatan'] ?? null,
            by: auth()->user()
        );

        return redirect()->route('kepala-sekolah.pengunduran-diri.index', ['tab' => 'withdrawn'])
            ->with('success', "Calon siswa {$calonSiswa->nama_lengkap} (#{$calonSiswa->nomor_pendaftaran}) berhasil ditandai Mengundurkan Diri.");
    }

    /**
     * Restore a withdrawn candidate back to their previous status.
     */
    public function restore(Request $request, CalonSiswa $calonSiswa): RedirectResponse
    {
        $validated = $request->validate([
            'alasan_restorasi' => ['required', 'string', 'max:1000'],
        ]);

        $this->withdrawalService->restore(
            calonSiswa: $calonSiswa,
            alasanRestorasi: $validated['alasan_restorasi'],
            by: auth()->user()
        );

        return redirect()->route('kepala-sekolah.pengunduran-diri.index', ['tab' => 'active'])
            ->with('success', "Status calon siswa {$calonSiswa->nama_lengkap} (#{$calonSiswa->nomor_pendaftaran}) berhasil dipulihkan.");
    }
}
