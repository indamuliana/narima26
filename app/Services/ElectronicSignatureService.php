<?php

namespace App\Services;

use App\Models\CalonSiswa;
use App\Models\DokumenVerifikasi;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Str;

class ElectronicSignatureService
{
    /**
     * Dapatkan Base URL yang valid untuk verifikasi dokumen.
     * Prioritas:
     * 1. env('APP_VERIFY_URL') jika didefinisikan (misal saat testing di jaringan lokal HP)
     * 2. Host aktif dari request HTTP jika ada (misal http://127.0.0.1:8000 saat lokal)
     * 3. Fallback ke config('app.url') (misal https://spmb.smkwikrama1garut.sch.id saat production)
     */
    public function getVerificationBaseUrl(): string
    {
        $override = config('app.verify_url');
        if (!empty($override)) {
            return rtrim($override, '/');
        }

        if (request()->hasHeader('Host')) {
            return rtrim(request()->schemeAndHttpHost(), '/');
        }

        return rtrim(config('app.url', 'http://127.0.0.1:8000'), '/');
    }

    /**
     * Dapatkan URL verifikasi lengkap untuk kode dokumen tertentu.
     */
    public function getVerificationUrl(string $kodeVerifikasi): string
    {
        return $this->getVerificationBaseUrl() . '/verifikasi-dokumen/' . $kodeVerifikasi;
    }

    /**
     * Generate representasi base64 PNG data URI dari URL/konten QR Code.
     * Menggunakan chillerlan/php-qrcode dengan GD Image Output.
     */
    public function generateQrCodeDataUri(string $content, int $scale = 3): string
    {
        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'outputBase64'    => true,
            'scale'           => $scale,
            'eccLevel'        => EccLevel::M,
            'addQuietzone'    => true,
            'quietzoneSize'   => 1,
        ]);

        return (new QRCode($options))->render($content);
    }

    /**
     * Dapatkan atau buat record verifikasi dokumen unik di database.
     */
    public function getOrCreateVerification(
        string $jenisDokumen,
        string $nomorDokumen,
        CalonSiswa $calonSiswa,
        string $penandatanganRole,
        string $penandatanganNama,
        string $penandatanganJabatan,
        array $metadata = [],
        ?\DateTimeInterface $signedAt = null
    ): DokumenVerifikasi {
        // Cari record yang sudah pernah dibuat untuk dokumen ini
        $existing = DokumenVerifikasi::where('jenis_dokumen', $jenisDokumen)
            ->where('nomor_dokumen', $nomorDokumen)
            ->where('calon_siswa_id', $calonSiswa->id)
            ->first();

        if ($existing) {
            // Update metadata jika ada perubahan detail
            if (!empty($metadata) && $existing->metadata !== $metadata) {
                $existing->update(['metadata' => array_merge($existing->metadata ?? [], $metadata)]);
            }
            return $existing;
        }

        // Generate kode verifikasi unik: SPMB-PREFIX-YYYY-RANDOM
        $prefix = match ($jenisDokumen) {
            DokumenVerifikasi::JENIS_KWITANSI_SELEKSI => 'KWT-SEL',
            DokumenVerifikasi::JENIS_KWITANSI_DAFTAR_ULANG => 'KWT-DU',
            DokumenVerifikasi::JENIS_TAGIHAN_DAFTAR_ULANG => 'TGH-DU',
            DokumenVerifikasi::JENIS_SK_KELULUSAN => 'SK-LULUS',
            DokumenVerifikasi::JENIS_KESEPAHAMAN_EULA => 'EULA',
            default => 'DOC',
        };

        $year = date('Y');
        $randomPart = strtoupper(Str::random(10));
        $kodeVerifikasi = "SPMB-{$prefix}-{$year}-{$randomPart}";

        return DokumenVerifikasi::create([
            'kode_verifikasi'       => $kodeVerifikasi,
            'jenis_dokumen'         => $jenisDokumen,
            'nomor_dokumen'         => $nomorDokumen,
            'calon_siswa_id'        => $calonSiswa->id,
            'penandatangan_role'    => $penandatanganRole,
            'penandatangan_nama'    => $penandatanganNama,
            'penandatangan_jabatan' => $penandatanganJabatan,
            'signed_at'             => $signedAt ?? now(),
            'metadata'              => $metadata,
            'is_valid'              => true,
            'scan_count'            => 0,
        ]);
    }

    /**
     * Siapkan paket data TTE lengkap untuk diinjeksikan ke template PDF.
     * Mengembalikan 1 QR Code resmi dan metadata tanda tangan.
     */
    public function prepareSignatureData(
        string $jenisDokumen,
        string $nomorDokumen,
        CalonSiswa $calonSiswa,
        string $penandatanganRole,
        string $penandatanganNama,
        string $penandatanganJabatan,
        array $metadata = [],
        ?\DateTimeInterface $signedAt = null
    ): array {
        $dokumenVerifikasi = $this->getOrCreateVerification(
            jenisDokumen: $jenisDokumen,
            nomorDokumen: $nomorDokumen,
            calonSiswa: $calonSiswa,
            penandatanganRole: $penandatanganRole,
            penandatanganNama: $penandatanganNama,
            penandatanganJabatan: $penandatanganJabatan,
            metadata: $metadata,
            signedAt: $signedAt
        );

        $verificationUrl = $this->getVerificationUrl($dokumenVerifikasi->kode_verifikasi);
        $qrCodeBase64 = $this->generateQrCodeDataUri($verificationUrl, scale: 3);

        return [
            'dokumenVerifikasi'   => $dokumenVerifikasi,
            'kodeVerifikasi'      => $dokumenVerifikasi->kode_verifikasi,
            'verificationUrl'     => $verificationUrl,
            'qrCodeBase64'        => $qrCodeBase64,
            'penandatanganRole'   => $dokumenVerifikasi->penandatangan_role,
            'penandatanganNama'   => $dokumenVerifikasi->penandatangan_nama,
            'penandatanganJabatan'=> $dokumenVerifikasi->penandatangan_jabatan,
            'signedAt'            => $dokumenVerifikasi->signed_at,
        ];
    }
}
