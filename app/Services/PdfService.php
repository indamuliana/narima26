<?php

namespace App\Services;

use App\Models\CalonSiswa;
use App\Models\DokumenVerifikasi;
use App\Models\PembayaranDaftarUlang;
use App\Models\PembayaranSeleksi;
use App\Models\Tagihan;
use App\Services\ElectronicSignatureService;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;

class PdfService
{
    public function __construct(
        protected ?ElectronicSignatureService $signatureService = null
    ) {
        $this->signatureService = $signatureService ?? app(ElectronicSignatureService::class);
    }
    /**
     * Dapatkan representasi base64 dari KOP_SURAT.jpg agar DomPDF dapat merender kop surat
     * secara konsisten tanpa kendala akses path atau HTTP.
     *
     * @return string|null
     */
    public function getKopSuratBase64(): ?string
    {
        $path = public_path('images/kop_surat.jpg');
        if (file_exists($path)) {
            return 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path));
        }
        return null;
    }

    /**
     * Dapatkan representasi base64 dari logo resmi Wikrama 1 Garut.
     *
     * @return string|null
     */
    public function getLogoBase64(): ?string
    {
        $path = public_path('images/logo.png');
        if (file_exists($path)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
        }
        return null;
    }

    /**
     * Master method untuk merender view blade menjadi PDF terpusat.
     *
     * @param string $view
     * @param array $data
     * @param string $paper
     * @param string $orientation
     * @return DomPdfWrapper
     */
    public function renderPdf(
        string $view,
        array $data = [],
        string $paper = 'a4',
        string $orientation = 'portrait'
    ): DomPdfWrapper {
        $commonData = [
            'kopSuratBase64' => $this->getKopSuratBase64(),
            'logoBase64' => $this->getLogoBase64(),
        ];

        $mergedData = array_merge($commonData, $data);

        return Pdf::loadView($view, $mergedData)
            ->setPaper($paper, $orientation)
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true);
    }

    /**
     * Generate Kartu Tanda Peserta SPMB.
     *
     * @param CalonSiswa $calonSiswa
     * @return DomPdfWrapper
     */
    public function generateKartuPendaftaran(CalonSiswa $calonSiswa): DomPdfWrapper
    {
        $calonSiswa->loadMissing(['program', 'jurusan', 'gelombang', 'sekolahAsal', 'user']);

        return $this->renderPdf('pdf.kartu_pendaftaran', [
            'calonSiswa' => $calonSiswa,
        ]);
    }

    /**
     * Generate Bukti Pembayaran / Kwitansi Resmi (Seleksi atau Daftar Ulang).
     *
     * @param PembayaranSeleksi|PembayaranDaftarUlang $pembayaran
     * @param string $jenisPembayaran
     * @return DomPdfWrapper
     */
    public function generateBuktiPembayaran(
        PembayaranSeleksi|PembayaranDaftarUlang $pembayaran,
        string $jenisPembayaran = 'Pembayaran Seleksi'
    ): DomPdfWrapper {
        $pembayaran->loadMissing(['calonSiswa.program', 'calonSiswa.jurusan', 'verifikator']);

        $calonSiswa = $pembayaran->calonSiswa;
        $nomorDokumen = $pembayaran->nomor_referensi ?? ('TRX-' . str_pad($pembayaran->id, 6, '0', STR_PAD_LEFT));
        $jenisDok = ($pembayaran instanceof PembayaranSeleksi)
            ? DokumenVerifikasi::JENIS_KWITANSI_SELEKSI
            : DokumenVerifikasi::JENIS_KWITANSI_DAFTAR_ULANG;

        $namaBendahara = $pembayaran->verifikator?->name ?? 'Fitria Amalia, S.Pd.';
        $jabatanBendahara = 'Bendahara Penerimaan Sekolah';

        $tte = $this->signatureService->prepareSignatureData(
            jenisDokumen: $jenisDok,
            nomorDokumen: $nomorDokumen,
            calonSiswa: $calonSiswa,
            penandatanganRole: DokumenVerifikasi::ROLE_BENDAHARA,
            penandatanganNama: $namaBendahara,
            penandatanganJabatan: $jabatanBendahara,
            metadata: [
                'jenis_pembayaran' => $jenisPembayaran,
                'nominal' => (float) $pembayaran->nominal_dibayar,
                'metode_bayar' => $pembayaran->metode_bayar,
                'bank_pengirim' => $pembayaran->bank_pengirim,
                'tanggal_bayar' => $pembayaran->tanggal_bayar ?? $pembayaran->created_at,
            ],
            signedAt: $pembayaran->verified_at ?? $pembayaran->created_at
        );

        return $this->renderPdf('pdf.bukti_pembayaran', [
            'pembayaran' => $pembayaran,
            'calonSiswa' => $calonSiswa,
            'jenisPembayaran' => $jenisPembayaran,
            'tte' => $tte,
        ]);
    }

    /**
     * Generate Surat Rincian Tagihan Daftar Ulang.
     *
     * @param Tagihan $tagihan
     * @return DomPdfWrapper
     */
    public function generateTagihan(Tagihan $tagihan): DomPdfWrapper
    {
        $tagihan->loadMissing(['calonSiswa.jurusan', 'details', 'diskon', 'pembayaran']);

        $totalPaid = (float) $tagihan->pembayaran
            ->where('status', 'DIVERIFIKASI')
            ->sum('nominal_dibayar');

        $remainingBalance = max(0, (float) $tagihan->total_netto - $totalPaid);

        $tte = $this->signatureService->prepareSignatureData(
            jenisDokumen: DokumenVerifikasi::JENIS_TAGIHAN_DAFTAR_ULANG,
            nomorDokumen: $tagihan->nomor_tagihan,
            calonSiswa: $tagihan->calonSiswa,
            penandatanganRole: DokumenVerifikasi::ROLE_BENDAHARA,
            penandatanganNama: 'Fitria Amalia, S.Pd.',
            penandatanganJabatan: 'Bendahara Penerimaan SPMB',
            metadata: [
                'total_bruto' => (float) $tagihan->total_bruto,
                'total_diskon' => (float) $tagihan->total_diskon,
                'total_netto' => (float) $tagihan->total_netto,
                'status' => $tagihan->status,
                'sisa_bayar' => (float) $remainingBalance,
            ],
            signedAt: $tagihan->created_at
        );

        return $this->renderPdf('pdf.tagihan_daftar_ulang', [
            'tagihan' => $tagihan,
            'calonSiswa' => $tagihan->calonSiswa,
            'totalPaid' => $totalPaid,
            'remainingBalance' => $remainingBalance,
            'tte' => $tte,
        ]);
    }

    /**
     * Generate Dokumen Informasi Akun Pendaftaran.
     *
     * @param CalonSiswa $calonSiswa
     * @return DomPdfWrapper
     */
    public function generateInformasiAkun(CalonSiswa $calonSiswa): DomPdfWrapper
    {
        $calonSiswa->loadMissing(['program', 'jurusan', 'gelombang', 'sekolahAsal', 'user']);

        return $this->renderPdf('pdf.informasi_akun', [
            'calonSiswa' => $calonSiswa,
        ]);
    }

    /**
     * Generate Surat Pernyataan dan Kesepahaman (EULA).
     *
     * @param CalonSiswa $calonSiswa
     * @return DomPdfWrapper
     */
    public function generateEula(CalonSiswa $calonSiswa): DomPdfWrapper
    {
        $calonSiswa->loadMissing(['program', 'jurusan', 'orangTua', 'dataOrangtua', 'sekolahAsal', 'kesepahaman']);

        $eula = $calonSiswa->kesepahaman()->where('setuju', true)->latest()->first();

        $kesepahamanService = app(\App\Services\KesepahamanService::class);
        $klausulData = $kesepahamanService->getKlausulByCalonSiswa($calonSiswa);

        // Gunakan snapshot jika tersimpan, atau ambil dari klausul aktif
        $kelompokList = (!empty($eula?->klausul_snapshot))
            ? $eula->klausul_snapshot
            : $klausulData['kelompok'];

        $nomorDokumen = "EULA-SPMB-" . date('Y') . "/{$calonSiswa->nomor_pendaftaran}";

        $tte = $this->signatureService->prepareSignatureData(
            jenisDokumen: DokumenVerifikasi::JENIS_KESEPAHAMAN_EULA,
            nomorDokumen: $nomorDokumen,
            calonSiswa: $calonSiswa,
            penandatanganRole: DokumenVerifikasi::ROLE_KEPALA_SEKOLAH,
            penandatanganNama: 'Kunedi, S.Si., Gr.',
            penandatanganJabatan: 'Kepala SMK Wikrama 1 Garut',
            metadata: [
                'program' => $klausulData['program_title'],
                'tahun_pelajaran' => $klausulData['tahun_pelajaran'],
                'agreed_at' => $eula?->agreed_at,
            ],
            signedAt: $eula?->agreed_at ?? now()
        );

        return $this->renderPdf('pdf.kesepahaman_eula', [
            'calonSiswa' => $calonSiswa,
            'eula' => $eula,
            'programNama' => $klausulData['program_title'],
            'tahunPelajaran' => $klausulData['tahun_pelajaran'],
            'kelompokList' => $kelompokList,
            'hideKop' => true,
            'tte' => $tte,
        ]);
    }

    /**
     * Generate Surat Keputusan Hasil Seleksi (Kelulusan).
     *
     * @param CalonSiswa $calonSiswa
     * @param string $keputusan 'DITERIMA' atau 'DITOLAK'
     * @return DomPdfWrapper
     */
    public function generateKelulusan(CalonSiswa $calonSiswa, string $keputusan = 'DITERIMA'): DomPdfWrapper
    {
        $calonSiswa->loadMissing(['program', 'jurusan', 'sekolahAsal', 'keputusanKelulusan']);

        $nomorDokumen = "421.5/SPMB-" . date('Y') . "/{$calonSiswa->nomor_pendaftaran}";

        $tte = $this->signatureService->prepareSignatureData(
            jenisDokumen: DokumenVerifikasi::JENIS_SK_KELULUSAN,
            nomorDokumen: $nomorDokumen,
            calonSiswa: $calonSiswa,
            penandatanganRole: DokumenVerifikasi::ROLE_KEPALA_SEKOLAH,
            penandatanganNama: 'Kunedi, S.Si., Gr.',
            penandatanganJabatan: 'Kepala SMK Wikrama 1 Garut',
            metadata: [
                'keputusan' => strtoupper($keputusan),
                'jurusan' => $calonSiswa->jurusan?->nama_jurusan,
                'program' => $calonSiswa->program?->nama_program,
                'tahun_pelajaran' => date('Y') . '/' . (date('Y') + 1),
            ],
            signedAt: $calonSiswa->keputusanKelulusan?->ditetapkan_at ?? now()
        );

        return $this->renderPdf('pdf.keputusan_kelulusan', [
            'calonSiswa' => $calonSiswa,
            'keputusan' => strtoupper($keputusan),
            'tte' => $tte,
        ]);
    }

    /**
     * Generate Rekapitulasi Data Pendaftar SPMB PDF (Landscape).
     *
     * @param iterable $calonSiswaList
     * @param array $filters
     * @return DomPdfWrapper
     */
    public function generateRekapCalonSiswa(iterable $calonSiswaList, array $filters = []): DomPdfWrapper
    {
        return $this->renderPdf('pdf.rekap_calon_siswa', [
            'calonSiswaList' => $calonSiswaList,
            'filters' => $filters,
            'printedAt' => now(),
        ], 'a4', 'landscape');
    }

    /**
     * Generate Dokumen Profil Lengkap Calon Siswa (Biodata, Nilai, Wawancara, Berkas, Keuangan).
     *
     * @param CalonSiswa $calonSiswa
     * @param array $extraData
     * @return DomPdfWrapper
     */
    public function generateProfilLengkap(CalonSiswa $calonSiswa, array $extraData = []): DomPdfWrapper
    {
        return $this->renderPdf('pdf.profil_lengkap_calon_siswa', array_merge([
            'calonSiswa' => $calonSiswa,
            'printedAt' => now(),
        ], $extraData), 'a4', 'portrait');
    }
}


