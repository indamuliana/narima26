<?php

namespace App\Http\Controllers\CalonSiswa;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAkademikRequest;
use App\Http\Requests\UpdateBiodataRequest;
use App\Http\Requests\UpdateOrangTuaRequest;
use App\Http\Requests\UpdateSeragamRequest;
use App\Http\Requests\UploadDokumenRequest;
use App\Models\MasterDesa;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterPekerjaan;
use App\Models\MasterProvinsi;
use App\Models\MasterSeragam;
use App\Services\LengkapiDataService;
use App\Services\SpmbStatusService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LengkapiDataController extends Controller
{
    public function __construct(
        protected LengkapiDataService $lengkapiDataService,
        protected SpmbStatusService $spmbStatusService
    ) {}

    /**
     * Tampilkan halaman formulir kelengkapan data & berkas.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;

        if (!$calonSiswa) {
            abort(404, 'Data pendaftaran calon siswa tidak ditemukan.');
        }

        // Guard: Harus sudah melalui pembayaran seleksi terverifikasi
        $unverifiedStatuses = [
            SpmbStatus::REGISTRASI,
            SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI,
        ];

        if (in_array($calonSiswa->status_spmb, $unverifiedStatuses, true)) {
            return redirect()->route('calon-siswa.pembayaran-seleksi.index')
                ->with('error', 'Formulir Lengkapi Data hanya dapat diakses setelah bukti pembayaran seleksi Anda diverifikasi oleh Bendahara/Panitia SPMB.');
        }

        // Auto transisi dari PEMBAYARAN_SELEKSI_DIVERIFIKASI -> MELENGKAPI_DATA saat membuka halaman
        if ($calonSiswa->status_spmb === SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI) {
            $this->spmbStatusService->changeStatus(
                $calonSiswa,
                SpmbStatus::MELENGKAPI_DATA,
                'Calon siswa membuka portal pengisian data pendaftaran.'
            );
            $calonSiswa->refresh();
        }

        $calonSiswa->load([
            'provinsi', 'kabupaten', 'kecamatan', 'desa',
            'dataOrangtua', 'dataAkademik', 'prestasi',
            'ukuranSeragam.jenisSeragam', 'dokumenPendaftaran'
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

        // Master Pekerjaan & Seragam
        $pekerjaanList = MasterPekerjaan::where('aktif', true)->orderBy('nama')->get();

        // Distinct Master Seragam items by nama_jenis
        $seragamList = MasterSeragam::aktif()->orderBy('id')->get();
        $seragamTypes = $seragamList->groupBy('nama_jenis');

        // Existing chosen uniforms mapped by jenis_seragam_id or nama_jenis
        $chosenSeragam = $calonSiswa->ukuranSeragam->keyBy('jenis_seragam_id');

        return view('calon-siswa.lengkapi-data.index', compact(
            'calonSiswa',
            'completion',
            'provinsi',
            'kabupaten',
            'kecamatan',
            'desa',
            'pekerjaanList',
            'seragamTypes',
            'chosenSeragam'
        ));
    }

    /**
     * Update Biodata & Wilayah.
     */
    public function updateBiodata(UpdateBiodataRequest $request): RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $this->lengkapiDataService->saveBiodata($calonSiswa, $request->validated());

        return redirect()->route('calon-siswa.lengkapi-data.index', ['tab' => 'biodata'])
            ->with('success', 'Biodata dan informasi tempat tinggal berhasil disimpan!');
    }

    /**
     * Update Data Orang Tua & Wali.
     */
    public function updateOrangTua(UpdateOrangTuaRequest $request): RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $this->lengkapiDataService->saveOrangTua($calonSiswa, $request->validated());

        return redirect()->route('calon-siswa.lengkapi-data.index', ['tab' => 'orang_tua'])
            ->with('success', 'Data orang tua dan wali berhasil disimpan!');
    }

    /**
     * Update Data Akademik & Prestasi.
     */
    public function updateAkademik(UpdateAkademikRequest $request): RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $this->lengkapiDataService->saveAkademik(
            $calonSiswa,
            $request->validated(),
            $request->input('prestasi', [])
        );

        return redirect()->route('calon-siswa.lengkapi-data.index', ['tab' => 'akademik'])
            ->with('success', 'Data akademik rapor dan prestasi berhasil disimpan!');
    }

    /**
     * Update Ukuran Seragam.
     */
    public function updateSeragam(UpdateSeragamRequest $request): RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $this->lengkapiDataService->saveSeragam($calonSiswa, $request->input('seragam', []));

        return redirect()->route('calon-siswa.lengkapi-data.index', ['tab' => 'seragam'])
            ->with('success', 'Pilihan ukuran seragam berhasil disimpan!');
    }

    /**
     * Upload Dokumen Persyaratan.
     */
    public function uploadDokumen(UploadDokumenRequest $request): RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $this->lengkapiDataService->uploadDokumen($calonSiswa, $request->allFiles());

        return redirect()->route('calon-siswa.lengkapi-data.index', ['tab' => 'dokumen'])
            ->with('success', 'Berkas dokumen persyaratan berhasil diunggah!');
    }

    /**
     * Finalisasi Data Pendaftaran.
     */
    public function finalize(Request $request): RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;

        try {
            $this->lengkapiDataService->finalize($calonSiswa, auth()->user());

            return redirect()->route('calon-siswa.lengkapi-data.index')
                ->with('success', 'Selamat! Seluruh data pendaftaran dan berkas persyaratan Anda telah lengkap (100%). Status SPMB Anda sekarang: Data Lengkap.');
        } catch (Exception $e) {
            return redirect()->route('calon-siswa.lengkapi-data.index')
                ->with('error', $e->getMessage());
        }
    }
}
