<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CalonSiswaController extends Controller
{
    public function __construct(
        protected PdfService $pdfService
    ) {}

    /**
     * Build base query for CalonSiswa with applied request filters.
     */
    protected function buildFilteredQuery(Request $request)
    {
        $query = CalonSiswa::query()->with([
            'jurusan',
            'jurusan2',
            'program',
            'gelombang',
            'sekolahAsal',
            'pembayaranSeleksi',
            'tagihan',
            'keputusanKelulusan',
        ]);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('no_hp_siswa', 'like', "%{$search}%")
                  ->orWhere('asal_sekolah_lainnya', 'like', "%{$search}%")
                  ->orWhereHas('sekolahAsal', function ($sub) use ($search) {
                      $sub->where('nama_sekolah', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status_spmb')) {
            if ($status !== 'SEMUA') {
                $query->where('status_spmb', $status);
            }
        }

        if ($jurusanId = $request->input('jurusan_id')) {
            $query->where(function ($q) use ($jurusanId) {
                $q->where('jurusan_id', $jurusanId)
                  ->orWhere('jurusan_id_2', $jurusanId);
            });
        }

        if ($gelombangId = $request->input('gelombang_id')) {
            $query->where('gelombang_id', $gelombangId);
        }

        if ($programId = $request->input('program_id')) {
            $query->where('program_id', $programId);
        }

        if ($gender = $request->input('jenis_kelamin')) {
            $query->where('jenis_kelamin', $gender);
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $allowedSorts = ['id', 'nama_lengkap', 'nomor_pendaftaran', 'nisn', 'status_spmb', 'created_at'];

        if (in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest('id');
        }

        return $query;
    }

    /**
     * Display server-side filtered candidate directory (DataTables style).
     */
    public function index(Request $request): View
    {
        $perPage = min(100, max(10, (int) $request->input('per_page', 20)));
        $query = $this->buildFilteredQuery($request);

        $calonSiswaList = $query->paginate($perPage)->withQueryString();

        $jurusanList = MasterJurusan::aktif()->orderBy('kode')->get();
        $gelombangList = MasterGelombang::orderBy('id')->get();
        $programList = MasterProgram::aktif()->get();
        $statusList = SpmbStatus::cases();

        // Total count for current filter
        $totalCount = $calonSiswaList->total();

        return view('admin.calon-siswa.index', compact(
            'calonSiswaList',
            'jurusanList',
            'gelombangList',
            'programList',
            'statusList',
            'totalCount'
        ));
    }

    /**
     * Display 360-degree comprehensive profile of a single candidate.
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
            'ukuranSeragam.seragam',
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

        $snapshotService = app(\App\Services\InvoiceSnapshotService::class);
        $estimasiBiayaDaftarUlang = $snapshotService->getApplicableRegistrationBiaya($calonSiswa);
        $estimasiBiayaSeragam = $snapshotService->getApplicableUniformBiaya($calonSiswa);

        return view('admin.calon-siswa.show', compact('calonSiswa', 'estimasiBiayaDaftarUlang', 'estimasiBiayaSeragam'));
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
            'ukuranSeragam.seragam',
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
            'riwayatStatus.changedBy',
        ]);

        $snapshotService = app(\App\Services\InvoiceSnapshotService::class);
        $estimasiBiayaDaftarUlang = $snapshotService->getApplicableRegistrationBiaya($calonSiswa);
        $estimasiBiayaSeragam = $snapshotService->getApplicableUniformBiaya($calonSiswa);

        $pdf = $this->pdfService->generateProfilLengkap($calonSiswa, [
            'estimasiBiayaDaftarUlang' => $estimasiBiayaDaftarUlang,
            'estimasiBiayaSeragam' => $estimasiBiayaSeragam,
        ]);

        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $calonSiswa->nama_lengkap);
        return $pdf->stream("Profil_Lengkap_{$calonSiswa->nomor_pendaftaran}_{$safeName}.pdf");
    }


    /**
     * Get export column headers based on mode.
     */
    protected function getExportHeaders(string $mode): array
    {
        if ($mode === 'simple') {
            return [
                'No',
                'Nomor Pendaftaran',
                'NISN',
                'Nama Lengkap',
                'Jenis Kelamin',
                'No. HP Siswa',
                'Sekolah Asal',
                'Kompetensi Keahlian Pilihan 1',
                'Kompetensi Keahlian Pilihan 2',
                'Program',
                'Gelombang',
                'Status SPMB',
                'Status Seleksi Bayar',
                'Status Kelulusan',
                'Status Daftar Ulang',
                'Tanggal Daftar',
            ];
        }

        return [
            'No',
            'Nomor Pendaftaran',
            'Tanggal Pendaftaran',
            'Status SPMB',
            'Gelombang',
            'Kompetensi Keahlian (Jurusan) Pilihan 1',
            'Kompetensi Keahlian (Jurusan) Pilihan 2',
            'Program Belajar',
            'Tag Beasiswa',
            'Tag Jalur',
            'NISN',
            'NIK Siswa',
            'No. Kartu Keluarga',
            'Nama Lengkap',
            'Nama Panggilan',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Usia',
            'Agama',
            'Anak Ke-',
            'Dari Berapa Bersaudara',
            'Tahun Lulus SMP',
            'No. HP Siswa',
            'Email Siswa',
            'Status Domisili',
            'Negara',
            'Alamat Lengkap',
            'RT',
            'RW',
            'Desa / Kelurahan',
            'Kecamatan',
            'Kabupaten / Kota',
            'Provinsi',
            'Kode Pos',
            'Asal Sekolah',
            'NPSN Sekolah Asal',
            'Referensi / Promotor Jenis',
            'Nama Promotor',
            'Rayon Promotor',
            'Status Ayah',
            'Nama Ayah',
            'NIK Ayah',
            'Tahun Lahir Ayah',
            'Pendidikan Ayah',
            'Pekerjaan Ayah',
            'Penghasilan Ayah',
            'No. HP Ayah',
            'Alamat Ayah',
            'Status Ibu',
            'Nama Ibu',
            'NIK Ibu',
            'Tahun Lahir Ibu',
            'Pendidikan Ibu',
            'Pekerjaan Ibu',
            'Penghasilan Ibu',
            'No. HP Ibu',
            'Alamat Ibu',
            'Nama Wali',
            'Hubungan Wali',
            'Pekerjaan Wali',
            'Penghasilan Wali',
            'No. HP Wali',
            'Nilai Rata-rata Ujian/Ijazah',
            'Rata-rata Rapor Matematika (Sem 1-5)',
            'Rata-rata Rapor B. Indonesia (Sem 1-5)',
            'Rata-rata Rapor B. Inggris (Sem 1-5)',
            'Rata-rata Rapor PAI (Sem 1-5)',
            'Tinggi Badan (cm)',
            'Berat Badan (kg)',
            'Golongan Darah',
            'Buta Warna',
            'Kesehatan Mata',
            'Jenis Alergi',
            'Penyakit Berat Pernah Diderita',
            'Penyakit Berat Sedang Diderita',
            'Berkas KK',
            'Berkas Akta Kelahiran',
            'Berkas Ijazah / SKL',
            'Berkas Pas Foto',
            'Daftar Prestasi',
            'Status Bayar Biaya Seleksi',
            'Nominal Tagihan Seleksi',
            'Nominal Dibayar Seleksi',
            'Tanggal Bayar Seleksi',
            'Metode / Bank Pengirim Seleksi',
            'Verifikator Biaya Seleksi',
            'Status Wawancara Siswa',
            'Pewawancara Siswa',
            'Rekomendasi Wawancara Siswa',
            'Kemampuan Baca Al-Quran',
            'Hafalan Quran Siswa',
            'Catatan Khusus Wawancara Siswa',
            'Status Wawancara Orang Tua',
            'Pewawancara Orang Tua',
            'Narasumber Wawancara Orang Tua',
            'Kesanggupan Infaq Rutin Bulanan',
            'Catatan Wawancara Orang Tua',
            'Status Pakta Integritas (EULA)',
            'Waktu Persetujuan EULA',
            'IP Address Persetujuan EULA',
            'Jumlah Butir EULA Disetujui',
            'Keputusan Sidang Kelulusan',
            'Penetap Sidang Kelulusan',
            'Tanggal SK Kelulusan',
            'Catatan Sidang Kelulusan',
            'Rincian Ukuran Seragam',
            'Status Tagihan Daftar Ulang',
            'Nomor Tagihan Daftar Ulang',
            'Total Tagihan Bruto (Rp)',
            'Total Potongan Diskon (Rp)',
            'Total Tagihan Netto (Rp)',
            'Total Telah Dibayar (Rp)',
            'Sisa Tagihan (Rp)',
            'Status Pelunasan Tagihan',
        ];
    }

    /**
     * Format a single CalonSiswa row for CSV or XLS export.
     */
    protected function formatExportRow(CalonSiswa $cs, int $index, string $mode, $invoiceSnapshotService, bool $isCsv = true): array
    {
        $statusVal = is_string($cs->status_spmb) ? $cs->status_spmb : ($cs->status_spmb?->value ?? '-');
        $sekolah = $cs->sekolahAsal?->nama_sekolah ?? $cs->asal_sekolah_lainnya ?? '-';
        $bayarSeleksi = $cs->pembayaranSeleksi;
        $keputusan = $cs->keputusanKelulusan;
        $jurusanNama = $cs->jurusan?->nama ?? $cs->jurusan?->nama_jurusan ?? '-';
        $jurusan2Nama = $cs->jurusan2?->nama ?? $cs->jurusan2?->nama_jurusan ?? '-';
        $programNama = $cs->program?->nama ?? $cs->program?->nama_program ?? '-';
        $gelombangNama = $cs->gelombang?->nama ?? $cs->gelombang?->nama_gelombang ?? '-';

        if ($mode === 'simple') {
            $statusBayarSeleksi = $bayarSeleksi?->status ?? 'BELUM';
            $keputusanStatus = $keputusan?->keputusan ?? '-';
            $statusTagihan = $cs->tagihan->first()?->status ?? '-';

            return [
                $index + 1,
                $cs->nomor_pendaftaran,
                $isCsv ? "'{$cs->nisn}" : $cs->nisn,
                $cs->nama_lengkap,
                $cs->jenis_kelamin,
                $cs->no_hp_siswa ? ($isCsv ? "'{$cs->no_hp_siswa}" : $cs->no_hp_siswa) : '-',
                $sekolah,
                $jurusanNama,
                $jurusan2Nama,
                $programNama,
                $gelombangNama,
                $statusVal,
                $statusBayarSeleksi,
                $keputusanStatus,
                $statusTagihan,
                $cs->created_at?->format('Y-m-d H:i:s') ?? '-',
            ];
        }

        $wSiswa = $cs->wawancaraSiswa;
        $wOrtu = $cs->wawancaraOrangTua;
        $eula = $cs->kesepahaman?->last();
        $ortu = $cs->dataOrangtua;
        $rapor = $cs->nilaiRapor;
        $dok = $cs->dokumenPendaftaran;
        $kes = $cs->dataKesehatan;

        // Rata-rata Rapor
        $mtkAvg = $rapor ? collect([$rapor->mtk_sem1, $rapor->mtk_sem2, $rapor->mtk_sem3, $rapor->mtk_sem4, $rapor->mtk_sem5])->filter(fn($v) => !is_null($v) && $v !== '')->avg() : null;
        $indAvg = $rapor ? collect([$rapor->ind_sem1, $rapor->ind_sem2, $rapor->ind_sem3, $rapor->ind_sem4, $rapor->ind_sem5])->filter(fn($v) => !is_null($v) && $v !== '')->avg() : null;
        $engAvg = $rapor ? collect([$rapor->eng_sem1, $rapor->eng_sem2, $rapor->eng_sem3, $rapor->eng_sem4, $rapor->eng_sem5])->filter(fn($v) => !is_null($v) && $v !== '')->avg() : null;
        $paiAvg = $rapor ? collect([$rapor->pai_sem1, $rapor->pai_sem2, $rapor->pai_sem3, $rapor->pai_sem4, $rapor->pai_sem5])->filter(fn($v) => !is_null($v) && $v !== '')->avg() : null;

        // Prestasi & Seragam
        $prestasiText = $cs->prestasi->isNotEmpty()
            ? $cs->prestasi->map(fn($p) => "{$p->nama_prestasi} (" . ($p->tingkat ?? 'Umum') . ")")->join('; ')
            : '-';

        $seragamText = $cs->ukuranSeragam->isNotEmpty()
            ? $cs->ukuranSeragam->map(function ($s) {
                $nama = $s->jenisSeragam?->nama_jenis ?? $s->jenisSeragam?->nama ?? 'Seragam';
                $status = $s->status_label ? " [{$s->status_label}]" : '';
                return "{$nama}: {$s->ukuran}{$status}";
            })->join('; ')
            : '-';

        // Tagihan Rekapitulasi (Sudah Terbit atau Estimasi)
        $tagihanDu = $cs->tagihan->firstWhere('jenis_tagihan', 'DAFTAR_ULANG') ?? $cs->tagihan->first();
        if ($tagihanDu) {
            $statusTerbitTagihan = 'SUDAH TERBIT';
            $nomorTagihan = $tagihanDu->nomor_tagihan;
            $totalBruto = (float) $tagihanDu->total_bruto;
            $totalDiskon = (float) $tagihanDu->total_diskon;
            $totalNetto = (float) $tagihanDu->total_netto;
            $totalBayar = (float) $tagihanDu->pembayaran->where('status', 'DIVERIFIKASI')->sum('nominal_dibayar');
            $sisaTagihan = max(0, $totalNetto - $totalBayar);
            $statusTagihan = $tagihanDu->status;
        } else {
            $statusTerbitTagihan = 'ESTIMASI BELUM TERBIT';
            $nomorTagihan = '-';
            try {
                $applicable = $invoiceSnapshotService->getApplicableRegistrationBiaya($cs);
                $totalBruto = (float) $applicable->sum('nominal');
            } catch (\Throwable $e) {
                $totalBruto = 0;
            }
            $totalDiskon = 0;
            $totalNetto = $totalBruto;
            $totalBayar = 0;
            $sisaTagihan = $totalNetto;
            $statusTagihan = 'BELUM TERBIT';
        }

        // Penyakit Pernah & Sedang Diderita
        $penyakitPernah = '-';
        if ($kes && $kes->penyakit_pernah_diderita) {
            $penyakitPernah = $kes->penyakit_pernah_diderita;
            if ($kes->penyakit_pernah_diderita === 'Lainnya' && $kes->penyakit_pernah_diderita_lainnya) {
                $penyakitPernah .= " ({$kes->penyakit_pernah_diderita_lainnya})";
            }
        }

        $penyakitSedang = '-';
        if ($kes && $kes->penyakit_sedang_diderita) {
            $penyakitSedang = $kes->penyakit_sedang_diderita;
            if ($kes->penyakit_sedang_diderita === 'Lainnya' && $kes->penyakit_sedang_diderita_lainnya) {
                $penyakitSedang .= " ({$kes->penyakit_sedang_diderita_lainnya})";
            }
        }

        // EULA
        $poinEulaCount = is_array($eula?->poin_disetujui) ? count($eula->poin_disetujui) . ' Butir' : ($eula?->setuju ? 'Semua Butir' : '0 Butir');

        // Nomor Telepon Ortu
        $noHpAyah = $cs->no_hp_ayah ?? $ortu?->no_hp_ayah;
        $noHpIbu = $cs->no_hp_ibu ?? $ortu?->no_hp_ibu;

        return [
            $index + 1,
            $cs->nomor_pendaftaran,
            $cs->created_at?->format('Y-m-d H:i:s') ?? '-',
            $statusVal,
            $gelombangNama,
            $jurusanNama,
            $jurusan2Nama,
            $programNama,
            $cs->tag_beasiswa ?? 'Normal',
            $cs->tag_jalur ?? 'Normal',
            $isCsv ? "'{$cs->nisn}" : $cs->nisn,
            $cs->nik ? ($isCsv ? "'{$cs->nik}" : $cs->nik) : '-',
            $cs->no_kk ? ($isCsv ? "'{$cs->no_kk}" : $cs->no_kk) : '-',
            $cs->nama_lengkap,
            $cs->nama_panggilan ?? '-',
            $cs->jenis_kelamin,
            $cs->tempat_lahir ?? '-',
            $cs->tanggal_lahir?->format('Y-m-d') ?? '-',
            $cs->tanggal_lahir ? $cs->tanggal_lahir->age . ' Thn' : '-',
            $cs->agama ?? '-',
            $cs->anak_ke ?? '-',
            $cs->jumlah_saudara ?? '-',
            $cs->tahun_lulus ?? '-',
            $cs->no_hp_siswa ? ($isCsv ? "'{$cs->no_hp_siswa}" : $cs->no_hp_siswa) : '-',
            $cs->email ?? $cs->user?->email ?? '-',
            $cs->is_luar_negeri ? 'Luar Negeri' : 'Dalam Negeri',
            $cs->is_luar_negeri ? ($cs->negara ?? '-') : 'Indonesia',
            $cs->alamat_lengkap ?? '-',
            $cs->rt ?? '-',
            $cs->rw ?? '-',
            $cs->nama_desa ?? '-',
            $cs->nama_kecamatan ?? '-',
            $cs->nama_kabupaten ?? '-',
            $cs->nama_provinsi ?? '-',
            $cs->kode_pos ?? '-',
            $sekolah,
            $cs->dataAkademik?->npsn ?? $cs->sekolahAsal?->npsn ?? '-',
            $cs->referensi_jenis ?? '-',
            $cs->referensi_nama ?? '-',
            $cs->referensi_rayon ?? '-',
            $ortu?->status_ayah ?? '-',
            $ortu?->nama_ayah ?? '-',
            $ortu?->nik_ayah ? ($isCsv ? "'{$ortu->nik_ayah}" : $ortu->nik_ayah) : '-',
            $ortu?->tahun_lahir_ayah ?? '-',
            $ortu?->pendidikan_ayah ?? '-',
            $ortu?->pekerjaanAyah?->nama ?? $ortu?->pekerjaan_ayah_nama ?? '-',
            $ortu?->penghasilan_ayah ?? '-',
            $noHpAyah ? ($isCsv ? "'{$noHpAyah}" : $noHpAyah) : '-',
            $ortu?->alamat_ayah ?? '-',
            $ortu?->status_ibu ?? '-',
            $ortu?->nama_ibu ?? '-',
            $ortu?->nik_ibu ? ($isCsv ? "'{$ortu->nik_ibu}" : $ortu->nik_ibu) : '-',
            $ortu?->tahun_lahir_ibu ?? '-',
            $ortu?->pendidikan_ibu ?? '-',
            $ortu?->pekerjaanIbu?->nama ?? $ortu?->pekerjaan_ibu_nama ?? '-',
            $ortu?->penghasilan_ibu ?? '-',
            $noHpIbu ? ($isCsv ? "'{$noHpIbu}" : $noHpIbu) : '-',
            $ortu?->alamat_ibu ?? '-',
            $ortu?->nama_wali ?? '-',
            $ortu?->hubungan_wali ?? '-',
            $ortu?->pekerjaanWali?->nama ?? '-',
            $ortu?->penghasilan_wali ?? '-',
            $ortu?->no_hp_wali ? ($isCsv ? "'{$ortu->no_hp_wali}" : $ortu->no_hp_wali) : '-',
            $cs->dataAkademik?->nilai_rata_rata ?? '-',
            $mtkAvg ? number_format($mtkAvg, 2) : '-',
            $indAvg ? number_format($indAvg, 2) : '-',
            $engAvg ? number_format($engAvg, 2) : '-',
            $paiAvg ? number_format($paiAvg, 2) : '-',
            $kes?->tinggi_badan ? $kes->tinggi_badan . ' cm' : '-',
            $kes?->berat_badan ? $kes->berat_badan . ' kg' : '-',
            $kes?->golongan_darah ?? '-',
            $kes?->buta_warna ?? '-',
            $kes?->kesehatan_mata ?? '-',
            $kes?->jenis_alergi ?? '-',
            $penyakitPernah,
            $penyakitSedang,
            $dok?->kk_path ? 'ADA' : 'BELUM',
            $dok?->akta_path ? 'ADA' : 'BELUM',
            $dok?->ijazah_skl_path ? 'ADA' : 'BELUM',
            $dok?->pas_foto_path ? 'ADA' : 'BELUM',
            $prestasiText,
            $bayarSeleksi?->status ?? 'BELUM',
            $bayarSeleksi?->nominal_tagihan ? number_format($bayarSeleksi->nominal_tagihan, 0, ',', '.') : '200.000',
            $bayarSeleksi?->nominal_dibayar ? number_format($bayarSeleksi->nominal_dibayar, 0, ',', '.') : '0',
            $bayarSeleksi?->tanggal_bayar?->format('Y-m-d') ?? '-',
            $bayarSeleksi?->metode_bayar ? "{$bayarSeleksi->metode_bayar} (" . ($bayarSeleksi->bank_pengirim ?? '-') . ")" : '-',
            $bayarSeleksi?->verifiedBy?->name ?? '-',
            $wSiswa?->status ?? '-',
            $wSiswa?->nama_petugas ?? $wSiswa?->pewawancara?->name ?? '-',
            $wSiswa?->rekomendasi ?? '-',
            $wSiswa?->baca_quran ?? '-',
            $wSiswa?->hafalan_quran ?? '-',
            $wSiswa?->catatan_pewawancara ?? '-',
            $wOrtu?->status ?? '-',
            $wOrtu?->nama_petugas ?? $wOrtu?->pewawancara?->name ?? '-',
            $wOrtu?->nama_diwawancarai ? "{$wOrtu->nama_diwawancarai} (" . ($wOrtu->hubungan_dengan_siswa ?? '-') . ")" : '-',
            $wOrtu?->infaq_rutin_bulanan ? 'Rp ' . number_format($wOrtu->infaq_rutin_bulanan, 0, ',', '.') : '-',
            $wOrtu?->catatan_tambahan ?? '-',
            ($eula && $eula->setuju) ? 'SETUJU' : 'BELUM',
            $eula?->agreed_at?->format('Y-m-d H:i:s') ?? '-',
            $eula?->ip_address ?? '-',
            $poinEulaCount,
            $keputusan?->keputusan ?? 'MENUNGGU SIDANG',
            $keputusan?->ditetapkanOleh?->name ?? '-',
            $keputusan?->ditetapkan_at?->format('Y-m-d H:i:s') ?? '-',
            $keputusan?->alasan_catatan ?? '-',
            $seragamText,
            $statusTerbitTagihan,
            $nomorTagihan,
            number_format($totalBruto, 0, ',', '.'),
            number_format($totalDiskon, 0, ',', '.'),
            number_format($totalNetto, 0, ',', '.'),
            number_format($totalBayar, 0, ',', '.'),
            number_format($sisaTagihan, 0, ',', '.'),
            $statusTagihan,
        ];
    }

    /**
     * Export filtered candidate list to CSV.
     * Supports mode=full (Comprehensive Master 360°, default) or mode=simple (Quick 15 columns).
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $mode = $request->input('mode', 'full');
        $query = $this->buildFilteredQuery($request);

        if ($mode !== 'simple') {
            $query->with([
                'provinsi',
                'kabupaten',
                'kecamatan',
                'desa',
                'dataOrangtua.pekerjaanAyah',
                'dataOrangtua.pekerjaanIbu',
                'dataOrangtua.pekerjaanWali',
                'dataAkademik',
                'dataKesehatan',
                'nilaiRapor',
                'dokumenPendaftaran',
                'prestasi',
                'ukuranSeragam.jenisSeragam',
                'pembayaranSeleksi.verifiedBy',
                'wawancaraSiswa.pewawancara',
                'wawancaraOrangTua.pewawancara',
                'kesepahaman',
                'keputusanKelulusan.ditetapkanOleh',
                'tagihan.pembayaran',
                'diskon',
                'user',
            ]);
        }

        $candidates = $query->get();
        $filePrefix = $mode === 'simple' ? 'Rekap_Ringkas_Calon_Siswa_' : 'Master_Lengkap_Calon_Siswa_';
        $filename = $filePrefix . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $invoiceSnapshotService = app(\App\Services\InvoiceSnapshotService::class);
        $columnHeaders = $this->getExportHeaders($mode);

        $callback = function () use ($candidates, $mode, $invoiceSnapshotService, $columnHeaders) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens it with proper encoding
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, $columnHeaders);

            foreach ($candidates as $index => $cs) {
                fputcsv($handle, $this->formatExportRow($cs, $index, $mode, $invoiceSnapshotService, true));
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export filtered candidate list to native Excel (.xls).
     * Supports mode=full (Comprehensive Master 360°, default) or mode=simple (Quick 15 columns).
     */
    public function exportXls(Request $request): StreamedResponse
    {
        $mode = $request->input('mode', 'full');
        $query = $this->buildFilteredQuery($request);

        if ($mode !== 'simple') {
            $query->with([
                'provinsi',
                'kabupaten',
                'kecamatan',
                'desa',
                'dataOrangtua.pekerjaanAyah',
                'dataOrangtua.pekerjaanIbu',
                'dataOrangtua.pekerjaanWali',
                'dataAkademik',
                'dataKesehatan',
                'nilaiRapor',
                'dokumenPendaftaran',
                'prestasi',
                'ukuranSeragam.jenisSeragam',
                'pembayaranSeleksi.verifiedBy',
                'wawancaraSiswa.pewawancara',
                'wawancaraOrangTua.pewawancara',
                'kesepahaman',
                'keputusanKelulusan.ditetapkanOleh',
                'tagihan.pembayaran',
                'diskon',
                'user',
            ]);
        }

        $candidates = $query->get();
        $filePrefix = $mode === 'simple' ? 'Rekap_Ringkas_Calon_Siswa_' : 'Master_Lengkap_Calon_Siswa_';
        $filename = $filePrefix . date('Ymd_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $invoiceSnapshotService = app(\App\Services\InvoiceSnapshotService::class);
        $columnHeaders = $this->getExportHeaders($mode);

        $callback = function () use ($candidates, $mode, $invoiceSnapshotService, $columnHeaders) {
            $output = fopen('php://output', 'w');

            // Excel HTML / XML Table wrapper
            fwrite($output, "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">\r\n");
            fwrite($output, "<head>\r\n");
            fwrite($output, "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\r\n");
            fwrite($output, "<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Data Calon Siswa</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->\r\n");
            fwrite($output, "<style>\r\n");
            fwrite($output, "table { border-collapse: collapse; font-family: Calibri, Arial, sans-serif; font-size: 11pt; }\r\n");
            fwrite($output, "th { background-color: #059669; color: #ffffff; font-weight: bold; border: 1px solid #047857; padding: 8px 10px; text-align: left; vertical-align: middle; }\r\n");
            fwrite($output, "td { border: 1px solid #cbd5e1; padding: 6px 8px; vertical-align: top; mso-number-format:\"\\@\"; }\r\n");
            fwrite($output, "tr:nth-child(even) { background-color: #f8fafc; }\r\n");
            fwrite($output, "</style>\r\n");
            fwrite($output, "</head>\r\n");
            fwrite($output, "<body>\r\n");
            fwrite($output, "<table border=\"1\">\r\n");
            fwrite($output, "<thead><tr>\r\n");

            foreach ($columnHeaders as $header) {
                fwrite($output, '<th>' . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . "</th>\r\n");
            }

            fwrite($output, "</tr></thead>\r\n");
            fwrite($output, "<tbody>\r\n");

            foreach ($candidates as $index => $cs) {
                $row = $this->formatExportRow($cs, $index, $mode, $invoiceSnapshotService, false);
                fwrite($output, "<tr>\r\n");
                foreach ($row as $cell) {
                    fwrite($output, '<td>' . htmlspecialchars((string) $cell, ENT_QUOTES, 'UTF-8') . "</td>\r\n");
                }
                fwrite($output, "</tr>\r\n");
            }

            fwrite($output, "</tbody>\r\n");
            fwrite($output, "</table>\r\n");
            fwrite($output, "</body>\r\n");
            fwrite($output, "</html>\r\n");

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export filtered candidate list to official PDF report.
     */
    public function exportPdf(Request $request): Response
    {
        $candidates = $this->buildFilteredQuery($request)->get();

        $filters = [];
        if ($jurusanId = $request->input('jurusan_id')) {
            $filters['jurusan_nama'] = MasterJurusan::find($jurusanId)?->nama;
        }
        if ($gelombangId = $request->input('gelombang_id')) {
            $filters['gelombang_nama'] = MasterGelombang::find($gelombangId)?->nama;
        }
        if ($status = $request->input('status_spmb')) {
            if ($status !== 'SEMUA') {
                $filters['status_nama'] = $status;
            }
        }

        $pdf = $this->pdfService->generateRekapCalonSiswa($candidates, $filters);

        return $pdf->download('Rekap_Calon_Siswa_SPMB_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Update tag beasiswa and tag jalur for a candidate.
     */
    public function updateTags(Request $request, CalonSiswa $calonSiswa)
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya Administrator yang memiliki wewenang mengubah tag siswa.');

        $validated = $request->validate([
            'tag_beasiswa' => ['nullable', 'string', 'max:255'],
            'tag_jalur' => ['nullable', 'string', 'max:255'],
        ]);

        $calonSiswa->update([
            'tag_beasiswa' => $validated['tag_beasiswa'] ?: 'Normal',
            'tag_jalur' => $validated['tag_jalur'] ?: 'Normal',
        ]);

        return redirect()->back()->with('success', 'Tag/Flag siswa berhasil diperbarui.');
    }
}
