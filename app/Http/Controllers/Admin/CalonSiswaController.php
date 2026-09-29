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
                  ->orWhere('asal_sekolah_lainnya', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status_spmb')) {
            if ($status !== 'SEMUA') {
                $query->where('status_spmb', $status);
            }
        }

        if ($jurusanId = $request->input('jurusan_id')) {
            $query->where('jurusan_id', $jurusanId);
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

        $callback = function () use ($candidates, $mode, $invoiceSnapshotService) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens it with proper encoding
            fputs($handle, "\xEF\xBB\xBF");

            if ($mode === 'simple') {
                // Simple 15 Column Headers
                fputcsv($handle, [
                    'No',
                    'Nomor Pendaftaran',
                    'NISN',
                    'Nama Lengkap',
                    'Jenis Kelamin',
                    'No. HP Siswa',
                    'Sekolah Asal',
                    'Kompetensi Keahlian',
                    'Program',
                    'Gelombang',
                    'Status SPMB',
                    'Status Seleksi Bayar',
                    'Status Kelulusan',
                    'Status Daftar Ulang',
                    'Tanggal Daftar',
                ]);

                foreach ($candidates as $index => $cs) {
                    $statusVal = is_string($cs->status_spmb) ? $cs->status_spmb : ($cs->status_spmb?->value ?? '-');
                    $sekolah = $cs->sekolahAsal?->nama_sekolah ?? $cs->asal_sekolah_lainnya ?? '-';
                    $bayarSeleksi = $cs->pembayaranSeleksi?->status ?? 'BELUM';
                    $keputusan = $cs->keputusanKelulusan?->keputusan ?? '-';
                    $statusTagihan = $cs->tagihan->first()?->status ?? '-';

                    fputcsv($handle, [
                        $index + 1,
                        $cs->nomor_pendaftaran,
                        "'{$cs->nisn}",
                        $cs->nama_lengkap,
                        $cs->jenis_kelamin,
                        $cs->no_hp_siswa ? "'{$cs->no_hp_siswa}" : '-',
                        $sekolah,
                        $cs->jurusan?->nama_jurusan ?? '-',
                        $cs->program?->nama_program ?? '-',
                        $cs->gelombang?->nama_gelombang ?? '-',
                        $statusVal,
                        $bayarSeleksi,
                        $keputusan,
                        $statusTagihan,
                        $cs->created_at?->format('Y-m-d H:i:s') ?? '-',
                    ]);
                }
            } else {
                // Master Comprehensive Headers (65+ Columns)
                fputcsv($handle, [
                    'No',
                    'Nomor Pendaftaran',
                    'Tanggal Pendaftaran',
                    'Status SPMB',
                    'Gelombang',
                    'Kompetensi Keahlian (Jurusan)',
                    'Program Belajar',
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
                    'No. HP Siswa',
                    'Email Siswa',
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
                    'Kondisi Kesehatan / Alergi Siswa',
                    'Catatan Khusus Wawancara Siswa',
                    'Status Wawancara Orang Tua',
                    'Pewawancara Orang Tua',
                    'Narasumber Wawancara Orang Tua',
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
                ]);

                foreach ($candidates as $index => $cs) {
                    $statusVal = is_string($cs->status_spmb) ? $cs->status_spmb : ($cs->status_spmb?->value ?? '-');
                    $sekolah = $cs->sekolahAsal?->nama_sekolah ?? $cs->asal_sekolah_lainnya ?? '-';
                    $bayarSeleksi = $cs->pembayaranSeleksi;
                    $keputusan = $cs->keputusanKelulusan;
                    $wSiswa = $cs->wawancaraSiswa;
                    $wOrtu = $cs->wawancaraOrangTua;
                    $eula = $cs->kesepahaman?->last();
                    $ortu = $cs->dataOrangtua;
                    $rapor = $cs->nilaiRapor;
                    $dok = $cs->dokumenPendaftaran;

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
                        ? $cs->ukuranSeragam->map(fn($s) => ($s->jenisSeragam?->nama ?? 'Seragam') . ": " . $s->ukuran)->join('; ')
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

                    // Kesehatan & Alergi
                    $kesehatanParts = array_filter([
                        $wSiswa?->kondisi_kesehatan ? "Kondisi: {$wSiswa->kondisi_kesehatan}" : null,
                        $wSiswa?->alergi ? "Alergi: {$wSiswa->alergi}" : null,
                        $wSiswa?->disabilitas ? "Disabilitas: {$wSiswa->disabilitas}" : null,
                    ]);
                    $kesehatanText = !empty($kesehatanParts) ? implode(' | ', $kesehatanParts) : '-';

                    // EULA
                    $poinEulaCount = is_array($eula?->poin_disetujui) ? count($eula->poin_disetujui) . ' Butir' : ($eula?->setuju ? 'Semua Butir' : '0 Butir');

                    // Nomor Telepon Ortu
                    $noHpAyah = $cs->no_hp_ayah ?? $ortu?->no_hp_ayah;
                    $noHpIbu = $cs->no_hp_ibu ?? $ortu?->no_hp_ibu;

                    fputcsv($handle, [
                        $index + 1,
                        $cs->nomor_pendaftaran,
                        $cs->created_at?->format('Y-m-d H:i:s') ?? '-',
                        $statusVal,
                        $cs->gelombang?->nama_gelombang ?? '-',
                        $cs->jurusan?->nama_jurusan ?? '-',
                        $cs->program?->nama_program ?? '-',
                        "'{$cs->nisn}",
                        $cs->nik ? "'{$cs->nik}" : '-',
                        $cs->no_kk ? "'{$cs->no_kk}" : '-',
                        $cs->nama_lengkap,
                        $cs->nama_panggilan ?? '-',
                        $cs->jenis_kelamin,
                        $cs->tempat_lahir ?? '-',
                        $cs->tanggal_lahir?->format('Y-m-d') ?? '-',
                        $cs->tanggal_lahir ? $cs->tanggal_lahir->age . ' Thn' : '-',
                        $cs->agama ?? '-',
                        $cs->no_hp_siswa ? "'{$cs->no_hp_siswa}" : '-',
                        $cs->email ?? $cs->user?->email ?? '-',
                        $cs->alamat_lengkap ?? '-',
                        $cs->rt ?? '-',
                        $cs->rw ?? '-',
                        $cs->desa?->nama ?? '-',
                        $cs->kecamatan?->nama ?? '-',
                        $cs->kabupaten?->nama ?? '-',
                        $cs->provinsi?->nama ?? '-',
                        $cs->kode_pos ?? '-',
                        $sekolah,
                        $cs->dataAkademik?->npsn ?? $cs->sekolahAsal?->npsn ?? '-',
                        $cs->referensi_jenis ?? '-',
                        $cs->referensi_nama ?? '-',
                        $cs->referensi_rayon ?? '-',
                        $ortu?->status_ayah ?? '-',
                        $ortu?->nama_ayah ?? '-',
                        $ortu?->nik_ayah ? "'{$ortu->nik_ayah}" : '-',
                        $ortu?->tahun_lahir_ayah ?? '-',
                        $ortu?->pendidikan_ayah ?? '-',
                        $ortu?->pekerjaanAyah?->nama ?? $ortu?->pekerjaan_ayah_nama ?? '-',
                        $ortu?->penghasilan_ayah ?? '-',
                        $noHpAyah ? "'{$noHpAyah}" : '-',
                        $ortu?->alamat_ayah ?? '-',
                        $ortu?->status_ibu ?? '-',
                        $ortu?->nama_ibu ?? '-',
                        $ortu?->nik_ibu ? "'{$ortu->nik_ibu}" : '-',
                        $ortu?->tahun_lahir_ibu ?? '-',
                        $ortu?->pendidikan_ibu ?? '-',
                        $ortu?->pekerjaanIbu?->nama ?? $ortu?->pekerjaan_ibu_nama ?? '-',
                        $ortu?->penghasilan_ibu ?? '-',
                        $noHpIbu ? "'{$noHpIbu}" : '-',
                        $ortu?->alamat_ibu ?? '-',
                        $ortu?->nama_wali ?? '-',
                        $ortu?->hubungan_wali ?? '-',
                        $ortu?->pekerjaanWali?->nama ?? '-',
                        $ortu?->penghasilan_wali ?? '-',
                        $ortu?->no_hp_wali ? "'{$ortu->no_hp_wali}" : '-',
                        $cs->dataAkademik?->nilai_rata_rata ?? '-',
                        $mtkAvg ? number_format($mtkAvg, 2) : '-',
                        $indAvg ? number_format($indAvg, 2) : '-',
                        $engAvg ? number_format($engAvg, 2) : '-',
                        $paiAvg ? number_format($paiAvg, 2) : '-',
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
                        $kesehatanText,
                        $wSiswa?->catatan_pewawancara ?? '-',
                        $wOrtu?->status ?? '-',
                        $wOrtu?->nama_petugas ?? $wOrtu?->pewawancara?->name ?? '-',
                        $wOrtu?->nama_diwawancarai ? "{$wOrtu->nama_diwawancarai} (" . ($wOrtu->hubungan_dengan_siswa ?? '-') . ")" : '-',
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
                    ]);
                }
            }

            fclose($handle);
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
            $filters['jurusan_nama'] = MasterJurusan::find($jurusanId)?->nama_jurusan;
        }
        if ($gelombangId = $request->input('gelombang_id')) {
            $filters['gelombang_nama'] = MasterGelombang::find($gelombangId)?->nama_gelombang;
        }
        if ($status = $request->input('status_spmb')) {
            if ($status !== 'SEMUA') {
                $filters['status_nama'] = $status;
            }
        }

        $pdf = $this->pdfService->generateRekapCalonSiswa($candidates, $filters);

        return $pdf->download('Rekap_Calon_Siswa_SPMB_' . date('Ymd_His') . '.pdf');
    }
}
