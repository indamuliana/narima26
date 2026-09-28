<?php

namespace App\Http\Controllers\Pewawancara;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\SimpanWawancaraRequest;
use App\Models\CalonSiswa;
use App\Models\MasterJurusan;
use App\Models\MasterKriteriaWawancara;
use App\Models\Wawancara;
use App\Services\WawancaraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WawancaraController extends Controller
{
    public function __construct(
        protected WawancaraService $wawancaraService
    ) {}

    /**
     * Display Pewawancara Dashboard with summary metrics and active queue.
     */
    public function dashboard(): View
    {
        $stats = $this->wawancaraService->getStatistics(auth()->user());
        $recentAntrian = $this->wawancaraService->getAntrian(statusWawancara: 'BELUM', perPage: 5);
        $recentRiwayat = $this->wawancaraService->getRiwayat(perPage: 5);

        return view('pewawancara.dashboard', compact('stats', 'recentAntrian', 'recentRiwayat'));
    }

    /**
     * Display Candidate Interview Queue with search & filters.
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');
        $jurusanId = $request->filled('jurusan_id') ? (int) $request->input('jurusan_id') : null;
        $statusWawancara = $request->input('status');

        $calonSiswaList = $this->wawancaraService->getAntrian($search, $jurusanId, $statusWawancara);
        $jurusanList = MasterJurusan::aktif()->orderBy('kode')->get();

        return view('pewawancara.antrian', compact('calonSiswaList', 'jurusanList', 'search', 'jurusanId', 'statusWawancara'));
    }

    /**
     * Open the 2-column Interview Assessment Room.
     */
    public function form(CalonSiswa $calonSiswa): View|RedirectResponse
    {
        // Guard: candidate must be at least in MENUNGGU_WAWANCARA or have existing interview
        $validStatuses = [
            SpmbStatus::DATA_LENGKAP->value,
            SpmbStatus::MENUNGGU_WAWANCARA->value,
            SpmbStatus::SUDAH_DIWAWANCARA->value,
            SpmbStatus::MENUNGGU_KEPUTUSAN->value,
            SpmbStatus::DITERIMA->value,
        ];

        $statusVal = $calonSiswa->status_spmb instanceof \BackedEnum
            ? $calonSiswa->status_spmb->value
            : (string) $calonSiswa->status_spmb;

        if (! in_array($statusVal, $validStatuses, true) && ! $calonSiswa->wawancara()->exists()) {
            return redirect()->route('pewawancara.antrian')
                ->with('error', 'Calon siswa belum menyelesaikan kelengkapan data & kesepahaman, belum dapat diwawancara.');
        }

        $wawancara = $this->wawancaraService->getOrCreateWawancara($calonSiswa, auth()->user());
        $wawancara->load(['details.kriteria', 'pewawancara']);

        // Group active criteria into siswa and orang_tua
        $allKriteria = $this->wawancaraService->getRubrikKriteria();
        $kriteriaSiswa = $allKriteria->where('jenis_penilaian', 'siswa');
        $kriteriaOrangTua = $allKriteria->where('jenis_penilaian', 'orang_tua');

        // Map existing detail scores by kriteria_id
        $existingDetails = $wawancara->details->keyBy('kriteria_id');

        $calonSiswa->load([
            'user',
            'jurusan',
            'programBelajar',
            'sekolahAsal',
            'dataOrangtua',
            'dataAkademik',
            'prestasi',
            'ukuranSeragam.seragam',
            'dokumenPendaftaran',
        ]);

        return view('pewawancara.form', compact(
            'calonSiswa',
            'wawancara',
            'kriteriaSiswa',
            'kriteriaOrangTua',
            'existingDetails'
        ));
    }

    /**
     * Store interview scores (draft or completed).
     */
    public function store(SimpanWawancaraRequest $request, CalonSiswa $calonSiswa): RedirectResponse
    {
        $validated = $request->validated();
        $action = $validated['action'];

        if ($action === 'draft') {
            $wawancara = $this->wawancaraService->saveDraft($calonSiswa, $validated, auth()->user());

            return redirect()->route('pewawancara.wawancara.form', $calonSiswa)
                ->with('success', 'Draft penilaian wawancara berhasil disimpan.');
        }

        // Finalize interview
        $wawancara = $this->wawancaraService->selesaiWawancara($calonSiswa, $validated, auth()->user());

        return redirect()->route('pewawancara.wawancara.show', $calonSiswa)
            ->with('success', 'Wawancara seleksi berhasil diselesaikan. Status calon siswa kini SUDAH_DIWAWANCARA.');
    }

    /**
     * Display completed interview summary (read-only view).
     */
    public function show(CalonSiswa $calonSiswa): View
    {
        $calonSiswa->load([
            'user',
            'jurusan',
            'programBelajar',
            'sekolahAsal',
            'dataOrangtua',
            'dataAkademik',
            'prestasi',
            'ukuranSeragam.seragam',
            'dokumenPendaftaran',
            'wawancara.pewawancara',
            'wawancara.details.kriteria',
        ]);

        $wawancara = $calonSiswa->wawancara()->latest('id')->first();

        return view('pewawancara.detail', compact('calonSiswa', 'wawancara'));
    }

    /**
     * Display Riwayat Wawancara (completed interviews history).
     */
    public function riwayat(Request $request): View
    {
        $search = $request->input('q');
        $jurusanId = $request->filled('jurusan_id') ? (int) $request->input('jurusan_id') : null;

        $riwayatList = $this->wawancaraService->getRiwayat($search, $jurusanId);
        $jurusanList = MasterJurusan::aktif()->orderBy('kode')->get();

        return view('pewawancara.riwayat', compact('riwayatList', 'jurusanList', 'search', 'jurusanId'));
    }

    /**
     * Display Master Instrumen & Rubrik reference.
     */
    public function instrumen(): View
    {
        $kriteriaList = $this->wawancaraService->getRubrikKriteria();

        return view('pewawancara.instrumen', compact('kriteriaList'));
    }
}
