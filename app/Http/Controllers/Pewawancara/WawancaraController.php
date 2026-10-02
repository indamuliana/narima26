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
        $programStats = app(\App\Services\DashboardMetricsService::class)->getProgramGenderStats();

        return view('pewawancara.dashboard', compact('stats', 'recentAntrian', 'recentRiwayat', 'programStats'));
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

    public function hub(CalonSiswa $calonSiswa): View|RedirectResponse
    {
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

        if (! in_array($statusVal, $validStatuses, true) && ! $calonSiswa->wawancaraSiswa()->exists() && ! $calonSiswa->wawancaraOrangTua()->exists()) {
            return redirect()->route('pewawancara.antrian')
                ->with('error', 'Calon siswa belum menyelesaikan kelengkapan data & kesepahaman, belum dapat diwawancara.');
        }

        $calonSiswa->load([
            'wawancaraSiswa.pewawancara',
            'wawancaraOrangTua.pewawancara',
        ]);

        return view('pewawancara.hub', compact('calonSiswa'));
    }

    public function formSiswa(CalonSiswa $calonSiswa): View
    {
        $calonSiswa->load([
            'wawancaraSiswa',
            'jurusan',
            'programBelajar',
            'sekolahAsal',
            'dataOrangtua.pekerjaanAyah',
            'dataOrangtua.pekerjaanIbu',
            'dataOrangtua.pekerjaanWali',
            'dataAkademik',
            'nilaiRapor',
            'prestasi',
            'dokumenPendaftaran',
            'provinsi',
            'kabupaten',
            'kecamatan',
            'desa',
        ]);
        
        $wawancara = $calonSiswa->wawancaraSiswa ?? new \App\Models\WawancaraSiswa();

        return view('pewawancara.wawancara-siswa', compact('calonSiswa', 'wawancara'));
    }

    public function saveSiswa(Request $request, CalonSiswa $calonSiswa): RedirectResponse
    {
        $data = $request->except(['_token', 'action']);
        $isDraft = $request->input('action') === 'draft';

        $this->wawancaraService->saveWawancaraSiswa($calonSiswa, $data, auth()->user(), $isDraft);

        return redirect()->route('pewawancara.wawancara.hub', $calonSiswa)
            ->with('success', 'Wawancara siswa berhasil disimpan.');
    }

    public function formOrangTua(CalonSiswa $calonSiswa): View
    {
        $calonSiswa->load([
            'wawancaraOrangTua',
            'jurusan',
            'programBelajar',
            'sekolahAsal',
            'dataOrangtua.pekerjaanAyah',
            'dataOrangtua.pekerjaanIbu',
            'dataOrangtua.pekerjaanWali',
            'dataAkademik',
            'nilaiRapor',
            'prestasi',
            'dokumenPendaftaran',
            'provinsi',
            'kabupaten',
            'kecamatan',
            'desa',
        ]);
        
        $wawancara = $calonSiswa->wawancaraOrangTua ?? new \App\Models\WawancaraOrangTua();

        return view('pewawancara.wawancara-orang-tua', compact('calonSiswa', 'wawancara'));
    }

    public function saveOrangTua(Request $request, CalonSiswa $calonSiswa): RedirectResponse
    {
        $data = $request->except(['_token', 'action']);
        $isDraft = $request->input('action') === 'draft';

        $this->wawancaraService->saveWawancaraOrangTua($calonSiswa, $data, auth()->user(), $isDraft);

        return redirect()->route('pewawancara.wawancara.hub', $calonSiswa)
            ->with('success', 'Wawancara orang tua berhasil disimpan.');
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
            'wawancaraSiswa.pewawancara',
            'wawancaraOrangTua.pewawancara',
        ]);

        $wawancaraSiswa = $calonSiswa->wawancaraSiswa;
        $wawancaraOrangTua = $calonSiswa->wawancaraOrangTua;

        return view('pewawancara.detail', compact('calonSiswa', 'wawancaraSiswa', 'wawancaraOrangTua'));
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
