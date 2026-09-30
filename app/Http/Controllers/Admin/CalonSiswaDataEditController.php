<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAkademikRequest;
use App\Http\Requests\UpdateBiodataRequest;
use App\Http\Requests\UpdateKesehatanRequest;
use App\Http\Requests\UpdateOrangTuaRequest;
use App\Http\Requests\UpdateSeragamRequest;
use App\Models\CalonSiswa;
use App\Models\MasterDesa;
use App\Models\MasterJurusan;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterPekerjaan;
use App\Models\MasterProvinsi;
use App\Models\MasterSekolahAsal;
use App\Models\MasterSeragam;
use App\Services\LengkapiDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalonSiswaDataEditController extends Controller
{
    public function __construct(
        protected LengkapiDataService $lengkapiDataService
    ) {}

    /**
     * Tampilkan formulir edit data calon siswa untuk admin.
     */
    public function edit(CalonSiswa $calonSiswa, Request $request): View
    {
        $calonSiswa->load([
            'provinsi', 'kabupaten', 'kecamatan', 'desa',
            'dataOrangtua', 'dataAkademik', 'prestasi',
            'ukuranSeragam.jenisSeragam', 'dokumenPendaftaran',
            'nilaiRapor', 'dataKesehatan', 'sekolahAsal',
            'jurusan', 'jurusan2', 'program'
        ]);

        $completion = $this->lengkapiDataService->calculateCompletion($calonSiswa);

        // Master Wilayah
        $provinsi = MasterProvinsi::orderBy('nama')->get();
        $kabupaten = $calonSiswa->provinsi_id
            ? MasterKabupaten::where('provinsi_id', $calonSiswa->provinsi_id)->orderBy('nama')->get()
            : collect();
        $kecamatan = $calonSiswa->kabupaten_id
            ? MasterKecamatan::where('kabupaten_id', $calonSiswa->kabupaten_id)->orderBy('nama')->get()
            : collect();
        $desa = $calonSiswa->kecamatan_id
            ? MasterDesa::where('kecamatan_id', $calonSiswa->kecamatan_id)->orderBy('nama')->get()
            : collect();

        // Master Pekerjaan, Jurusan
        $pekerjaanList = MasterPekerjaan::where('aktif', true)->orderBy('nama')->get();
        $jurusanList = MasterJurusan::orderBy('nama')->get();

        // Master Seragam
        $seragamQuery = MasterSeragam::aktif();
        if ($calonSiswa->jenis_kelamin) {
            $seragamQuery->where(function ($q) use ($calonSiswa) {
                $q->whereNull('jenis_kelamin')
                  ->orWhere('jenis_kelamin', $calonSiswa->jenis_kelamin);
            });
        }
        $seragamList = $seragamQuery->orderBy('urutan_klaster')->orderBy('id')->get();
        $seragamTypes = $seragamList->groupBy('nama_jenis');
        $chosenSeragam = $calonSiswa->ukuranSeragam?->keyBy('jenis_seragam_id') ?? collect();

        $activeTab = $request->query('tab', 'biodata');

        return view('admin.calon-siswa.edit-data', compact(
            'calonSiswa',
            'completion',
            'provinsi',
            'kabupaten',
            'kecamatan',
            'desa',
            'pekerjaanList',
            'jurusanList',
            'seragamTypes',
            'chosenSeragam',
            'activeTab'
        ));
    }

    /**
     * Update Biodata & Wilayah oleh Admin.
     */
    public function updateBiodata(UpdateBiodataRequest $request, CalonSiswa $calonSiswa): RedirectResponse
    {
        $this->lengkapiDataService->saveBiodata($calonSiswa, $request->validated());

        if (function_exists('activity')) {
            activity('admin_calon_siswa_edit')
                ->performedOn($calonSiswa)
                ->causedBy(auth()->user())
                ->withProperties(['tab' => 'biodata'])
                ->log("Administrator " . auth()->user()->name . " memperbarui biodata calon siswa {$calonSiswa->nama_lengkap} ({$calonSiswa->nomor_pendaftaran}).");
        }

        $tab = $request->input('action') === 'next' ? 'orang_tua' : 'biodata';

        return redirect()->route('admin.calon-siswa.edit-data', [$calonSiswa, 'tab' => $tab])
            ->with('success', 'Biodata & Identitas calon siswa berhasil diperbarui oleh Administrator.');
    }

    /**
     * Update Data Orang Tua & Wali oleh Admin.
     */
    public function updateOrangTua(UpdateOrangTuaRequest $request, CalonSiswa $calonSiswa): RedirectResponse
    {
        $this->lengkapiDataService->saveOrangTua($calonSiswa, $request->validated());

        if (function_exists('activity')) {
            activity('admin_calon_siswa_edit')
                ->performedOn($calonSiswa)
                ->causedBy(auth()->user())
                ->withProperties(['tab' => 'orang_tua'])
                ->log("Administrator " . auth()->user()->name . " memperbarui data orang tua calon siswa {$calonSiswa->nama_lengkap} ({$calonSiswa->nomor_pendaftaran}).");
        }

        $tab = $request->input('action') === 'next' ? 'akademik' : 'orang_tua';

        return redirect()->route('admin.calon-siswa.edit-data', [$calonSiswa, 'tab' => $tab])
            ->with('success', 'Data orang tua dan wali berhasil diperbarui oleh Administrator.');
    }

    /**
     * Update Data Akademik & Prestasi oleh Admin.
     */
    public function updateAkademik(UpdateAkademikRequest $request, CalonSiswa $calonSiswa): RedirectResponse
    {
        $this->lengkapiDataService->saveAkademik(
            $calonSiswa,
            $request->validated(),
            $request->input('prestasi', [])
        );

        if (function_exists('activity')) {
            activity('admin_calon_siswa_edit')
                ->performedOn($calonSiswa)
                ->causedBy(auth()->user())
                ->withProperties(['tab' => 'akademik'])
                ->log("Administrator " . auth()->user()->name . " memperbarui nilai akademik calon siswa {$calonSiswa->nama_lengkap} ({$calonSiswa->nomor_pendaftaran}).");
        }

        $tab = $request->input('action') === 'next' ? 'kesehatan' : 'akademik';

        return redirect()->route('admin.calon-siswa.edit-data', [$calonSiswa, 'tab' => $tab])
            ->with('success', 'Data nilai akademik rapor dan prestasi berhasil diperbarui oleh Administrator.');
    }

    /**
     * Update Data Kesehatan oleh Admin.
     */
    public function updateKesehatan(UpdateKesehatanRequest $request, CalonSiswa $calonSiswa): RedirectResponse
    {
        $calonSiswa->dataKesehatan()->updateOrCreate(
            ['calon_siswa_id' => $calonSiswa->id],
            $request->validated()
        );

        if (function_exists('activity')) {
            activity('admin_calon_siswa_edit')
                ->performedOn($calonSiswa)
                ->causedBy(auth()->user())
                ->withProperties(['tab' => 'kesehatan'])
                ->log("Administrator " . auth()->user()->name . " memperbarui data kesehatan calon siswa {$calonSiswa->nama_lengkap} ({$calonSiswa->nomor_pendaftaran}).");
        }

        $tab = $request->input('action') === 'next' ? 'seragam' : 'kesehatan';

        return redirect()->route('admin.calon-siswa.edit-data', [$calonSiswa, 'tab' => $tab])
            ->with('success', 'Data kesehatan & fisik calon siswa berhasil diperbarui oleh Administrator.');
    }

    /**
     * Update Ukuran Seragam oleh Admin.
     */
    public function updateSeragam(UpdateSeragamRequest $request, CalonSiswa $calonSiswa): RedirectResponse
    {
        $this->lengkapiDataService->saveSeragam($calonSiswa, $request->input('seragam', []));

        if (function_exists('activity')) {
            activity('admin_calon_siswa_edit')
                ->performedOn($calonSiswa)
                ->causedBy(auth()->user())
                ->withProperties(['tab' => 'seragam'])
                ->log("Administrator " . auth()->user()->name . " memperbarui ukuran seragam calon siswa {$calonSiswa->nama_lengkap} ({$calonSiswa->nomor_pendaftaran}).");
        }

        return redirect()->route('admin.calon-siswa.edit-data', [$calonSiswa, 'tab' => 'seragam'])
            ->with('success', 'Data ukuran seragam calon siswa berhasil diperbarui oleh Administrator.');
    }
}
