<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Services\InvoiceSnapshotService;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CalonSiswaController extends Controller
{
    public function __construct(
        protected PdfService $pdfService,
        protected InvoiceSnapshotService $snapshotService
    ) {}

    /**
     * Display candidate directory for Kepala Sekolah with executive stats and filters.
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');
        $status = $request->input('status');
        $jurusanId = $request->input('jurusan_id');
        $gelombangId = $request->input('gelombang_id');

        $query = CalonSiswa::query()
            ->with([
                'jurusan',
                'program',
                'gelombang',
                'pembayaranSeleksi',
                'wawancaraSiswa',
                'wawancaraOrangTua',
                'keputusanKelulusan',
                'tagihan',
            ]);

        if ($request->filled('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status_spmb', $status);
        }

        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $jurusanId);
        }

        if ($request->filled('gelombang_id')) {
            $query->where('gelombang_id', $gelombangId);
        }

        $calonSiswaList = $query->latest('id')->paginate(15)->withQueryString();

        $jurusanList = MasterJurusan::aktif()->get();
        $gelombangList = MasterGelombang::all();
        $statusOptions = SpmbStatus::cases();

        // Executive Quick Stats
        $stats = [
            'total' => CalonSiswa::count(),
            'lulus_seleksi' => CalonSiswa::whereIn('status_spmb', [
                SpmbStatus::DITERIMA->value,
                SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
                SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
                SpmbStatus::RESMI_TERDAFTAR->value,
            ])->count(),
            'sudah_wawancara' => CalonSiswa::whereIn('status_spmb', [
                SpmbStatus::SUDAH_DIWAWANCARA->value,
                SpmbStatus::MENUNGGU_KEPUTUSAN->value,
            ])->count(),
            'daftar_ulang_lunas' => CalonSiswa::whereIn('status_spmb', [
                SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
                SpmbStatus::RESMI_TERDAFTAR->value,
            ])->count(),
        ];

        return view('kepala-sekolah.calon-siswa.index', compact(
            'calonSiswaList',
            'jurusanList',
            'gelombangList',
            'statusOptions',
            'stats',
            'search',
            'status',
            'jurusanId',
            'gelombangId'
        ));
    }

    /**
     * Display 360-degree comprehensive profile of a single candidate for Kepala Sekolah.
     */
    public function show(CalonSiswa $calonSiswa): View
    {
        $calonSiswa->loadMissing([
            'jurusan',
            'program',
            'programBelajar',
            'gelombang',
            'sekolahAsal',
            'asalSekolah',
            'provinsi',
            'kabupaten',
            'kecamatan',
            'desa',
            'dataOrangtua.pekerjaanAyah',
            'dataOrangtua.pekerjaanIbu',
            'dataOrangtua.pekerjaanWali',
            'dataAkademik',
            'nilaiRapor',
            'prestasi',
            'ukuranSeragam.seragam',
            'ukuranSeragam.jenisSeragam',
            'dokumenPendaftaran',
            'pembayaranSeleksi.verifiedBy',
            'tagihan.details',
            'tagihan.diskon',
            'tagihan.pembayaran.verifiedBy',
            'pembayaranDaftarUlang',
            'diskon',
            'wawancaraSiswa.pewawancara',
            'wawancaraOrangTua.pewawancara',
            'keputusanKelulusan.ditetapkanOleh',
            'kesepahaman',
            'dataKesehatan',
            'user',
            'riwayatStatus.changedBy',
        ]);

        $estimasiBiayaDaftarUlang = $this->snapshotService->getApplicableRegistrationBiaya($calonSiswa);
        $estimasiBiayaSeragam = $this->snapshotService->getApplicableUniformBiaya($calonSiswa);

        return view('kepala-sekolah.calon-siswa.show', compact('calonSiswa', 'estimasiBiayaDaftarUlang', 'estimasiBiayaSeragam'));
    }

    /**
     * Cetak dokumen PDF profil lengkap calon siswa 360-derajat.
     */
    public function cetakPdf(CalonSiswa $calonSiswa): Response
    {
        $calonSiswa->loadMissing([
            'jurusan',
            'program',
            'programBelajar',
            'gelombang',
            'sekolahAsal',
            'asalSekolah',
            'provinsi',
            'kabupaten',
            'kecamatan',
            'desa',
            'dataOrangtua.pekerjaanAyah',
            'dataOrangtua.pekerjaanIbu',
            'dataOrangtua.pekerjaanWali',
            'dataKesehatan',
            'user',
            'dataAkademik',
            'nilaiRapor',
            'prestasi',
            'ukuranSeragam.seragam',
            'ukuranSeragam.jenisSeragam',
            'dokumenPendaftaran',
            'pembayaranSeleksi.verifiedBy',
            'tagihan.details',
            'tagihan.diskon',
            'tagihan.pembayaran.verifiedBy',
            'pembayaranDaftarUlang',
            'diskon',
            'wawancaraSiswa.pewawancara',
            'wawancaraOrangTua.pewawancara',
            'keputusanKelulusan.ditetapkanOleh',
            'kesepahaman',
            'dataKesehatan',
            'user',
            'riwayatStatus.changedBy',
        ]);

        $estimasiBiayaDaftarUlang = $this->snapshotService->getApplicableRegistrationBiaya($calonSiswa);
        $estimasiBiayaSeragam = $this->snapshotService->getApplicableUniformBiaya($calonSiswa);

        $pdf = $this->pdfService->generateProfilLengkap($calonSiswa, [
            'estimasiBiayaDaftarUlang' => $estimasiBiayaDaftarUlang,
            'estimasiBiayaSeragam' => $estimasiBiayaSeragam,
        ]);

        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $calonSiswa->nama_lengkap);
        return $pdf->stream("Profil_Lengkap_{$calonSiswa->nomor_pendaftaran}_{$safeName}.pdf");
    }
}
