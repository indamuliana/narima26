<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenVerifikasi extends Model
{
    use HasFactory;

    protected $table = 'dokumen_verifikasi';

    protected $fillable = [
        'kode_verifikasi',
        'jenis_dokumen',
        'nomor_dokumen',
        'calon_siswa_id',
        'penandatangan_role',
        'penandatangan_nama',
        'penandatangan_jabatan',
        'signed_at',
        'metadata',
        'scan_count',
        'last_scanned_at',
        'is_valid',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'last_scanned_at' => 'datetime',
        'metadata' => 'array',
        'is_valid' => 'boolean',
    ];

    public const JENIS_KWITANSI_SELEKSI = 'KWITANSI_SELEKSI';
    public const JENIS_KWITANSI_DAFTAR_ULANG = 'KWITANSI_DAFTAR_ULANG';
    public const JENIS_TAGIHAN_DAFTAR_ULANG = 'TAGIHAN_DAFTAR_ULANG';
    public const JENIS_SK_KELULUSAN = 'SK_KELULUSAN';
    public const JENIS_KESEPAHAMAN_EULA = 'KESEPAHAMAN_EULA';

    public const ROLE_BENDAHARA = 'BENDAHARA';
    public const ROLE_KEPALA_SEKOLAH = 'KEPALA_SEKOLAH';

    /**
     * Relasi ke Calon Siswa pemilik dokumen.
     */
    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    /**
     * Dapatkan label manusiawi untuk jenis dokumen.
     */
    public function getJudulDokumenAttribute(): string
    {
        return match ($this->jenis_dokumen) {
            self::JENIS_KWITANSI_SELEKSI => 'Kwitansi Resmi Pembayaran Seleksi SPMB',
            self::JENIS_KWITANSI_DAFTAR_ULANG => 'Kwitansi Resmi Pembayaran Daftar Ulang',
            self::JENIS_TAGIHAN_DAFTAR_ULANG => 'Surat Rincian Tagihan Biaya Daftar Ulang',
            self::JENIS_SK_KELULUSAN => 'Surat Keputusan Hasil Seleksi (Kelulusan) SPMB',
            self::JENIS_KESEPAHAMAN_EULA => 'Surat Pernyataan & Perjanjian Kesepahaman (EULA)',
            default => 'Dokumen Resmi SPMB SMK Wikrama 1 Garut',
        };
    }
}
