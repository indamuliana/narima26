<?php

namespace App\Services;

use App\Models\CalonSiswa;
use App\Models\PembayaranDaftarUlang;
use App\Models\PembayaranSeleksi;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;

class PdfService
{
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
        $calonSiswa->loadMissing(['program', 'jurusan', 'gelombang', 'sekolahAsal']);

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

        return $this->renderPdf('pdf.bukti_pembayaran', [
            'pembayaran' => $pembayaran,
            'calonSiswa' => $pembayaran->calonSiswa,
            'jenisPembayaran' => $jenisPembayaran,
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

        return $this->renderPdf('pdf.tagihan_daftar_ulang', [
            'tagihan' => $tagihan,
            'calonSiswa' => $tagihan->calonSiswa,
            'totalPaid' => $totalPaid,
            'remainingBalance' => $remainingBalance,
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
        $calonSiswa->loadMissing(['program', 'jurusan', 'orangTua', 'sekolahAsal', 'kesepahaman']);

        $eula = $calonSiswa->kesepahaman()->where('setuju', true)->latest()->first();

        return $this->renderPdf('pdf.kesepahaman_eula', [
            'calonSiswa' => $calonSiswa,
            'eula' => $eula,
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
        $calonSiswa->loadMissing(['program', 'jurusan', 'sekolahAsal']);

        return $this->renderPdf('pdf.keputusan_kelulusan', [
            'calonSiswa' => $calonSiswa,
            'keputusan' => strtoupper($keputusan),
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
}

