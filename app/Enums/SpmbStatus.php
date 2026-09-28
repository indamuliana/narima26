<?php

namespace App\Enums;

enum SpmbStatus: string
{
    case REGISTRASI = 'REGISTRASI';
    case MENUNGGU_PEMBAYARAN_SELEKSI = 'MENUNGGU_PEMBAYARAN_SELEKSI';
    case PEMBAYARAN_SELEKSI_DIVERIFIKASI = 'PEMBAYARAN_SELEKSI_DIVERIFIKASI';
    case MELENGKAPI_DATA = 'MELENGKAPI_DATA';
    case DATA_LENGKAP = 'DATA_LENGKAP';
    case MENUNGGU_WAWANCARA = 'MENUNGGU_WAWANCARA';
    case SUDAH_DIWAWANCARA = 'SUDAH_DIWAWANCARA';
    case MENUNGGU_KEPUTUSAN = 'MENUNGGU_KEPUTUSAN';
    case DITERIMA = 'DITERIMA';
    case DITOLAK = 'DITOLAK';
    case MENUNGGU_DAFTAR_ULANG = 'MENUNGGU_DAFTAR_ULANG';
    case DAFTAR_ULANG_DIVERIFIKASI = 'DAFTAR_ULANG_DIVERIFIKASI';
    case RESMI_TERDAFTAR = 'RESMI_TERDAFTAR';
    case MENGUNDURKAN_DIRI = 'MENGUNDURKAN_DIRI';

    /**
     * Get readable Indonesian label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::REGISTRASI => 'Registrasi Awal',
            self::MENUNGGU_PEMBAYARAN_SELEKSI => 'Menunggu Pembayaran Seleksi',
            self::PEMBAYARAN_SELEKSI_DIVERIFIKASI => 'Pembayaran Seleksi Terverifikasi',
            self::MELENGKAPI_DATA => 'Melengkapi Data & Dokumen',
            self::DATA_LENGKAP => 'Data Lengkap',
            self::MENUNGGU_WAWANCARA => 'Menunggu Wawancara',
            self::SUDAH_DIWAWANCARA => 'Sudah Diwawancara',
            self::MENUNGGU_KEPUTUSAN => 'Menunggu Keputusan',
            self::DITERIMA => 'Diterima',
            self::DITOLAK => 'Ditolak',
            self::MENUNGGU_DAFTAR_ULANG => 'Menunggu Daftar Ulang',
            self::DAFTAR_ULANG_DIVERIFIKASI => 'Daftar Ulang Terverifikasi',
            self::RESMI_TERDAFTAR => 'Resmi Terdaftar',
            self::MENGUNDURKAN_DIRI => 'Mengundurkan Diri',
        };
    }

    /**
     * Get badge color variant for UI components.
     */
    public function badgeVariant(): string
    {
        return match ($this) {
            self::REGISTRASI,
            self::MENUNGGU_PEMBAYARAN_SELEKSI,
            self::MELENGKAPI_DATA => 'orange',

            self::PEMBAYARAN_SELEKSI_DIVERIFIKASI,
            self::MENUNGGU_WAWANCARA,
            self::MENUNGGU_KEPUTUSAN,
            self::MENUNGGU_DAFTAR_ULANG => 'cyan',

            self::DATA_LENGKAP,
            self::SUDAH_DIWAWANCARA => 'blue',

            self::DITERIMA,
            self::DAFTAR_ULANG_DIVERIFIKASI,
            self::RESMI_TERDAFTAR => 'green',

            self::DITOLAK => 'red',

            self::MENGUNDURKAN_DIRI => 'gray',
        };
    }

    /**
     * List of allowed next statuses in normal SPMB state progression.
     * Note: MENGUNDURKAN_DIRI can be set from any active state by Admin/Kepsek.
     *
     * @return array<SpmbStatus>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::REGISTRASI => [
                self::MENUNGGU_PEMBAYARAN_SELEKSI,
                self::MENGUNDURKAN_DIRI,
            ],
            self::MENUNGGU_PEMBAYARAN_SELEKSI => [
                self::PEMBAYARAN_SELEKSI_DIVERIFIKASI,
                self::MENGUNDURKAN_DIRI,
            ],
            self::PEMBAYARAN_SELEKSI_DIVERIFIKASI => [
                self::MELENGKAPI_DATA,
                self::DATA_LENGKAP,
                self::MENGUNDURKAN_DIRI,
            ],
            self::MELENGKAPI_DATA => [
                self::DATA_LENGKAP,
                self::MENGUNDURKAN_DIRI,
            ],
            self::DATA_LENGKAP => [
                self::MENUNGGU_WAWANCARA,
                self::MENGUNDURKAN_DIRI,
            ],
            self::MENUNGGU_WAWANCARA => [
                self::SUDAH_DIWAWANCARA,
                self::MENGUNDURKAN_DIRI,
            ],
            self::SUDAH_DIWAWANCARA => [
                self::MENUNGGU_KEPUTUSAN,
                self::DITERIMA,
                self::DITOLAK,
                self::MENGUNDURKAN_DIRI,
            ],
            self::MENUNGGU_KEPUTUSAN => [
                self::DITERIMA,
                self::DITOLAK,
                self::MENGUNDURKAN_DIRI,
            ],
            self::DITERIMA => [
                self::MENUNGGU_DAFTAR_ULANG,
                self::MENGUNDURKAN_DIRI,
            ],
            self::DITOLAK => [
                self::MENGUNDURKAN_DIRI,
            ],
            self::MENUNGGU_DAFTAR_ULANG => [
                self::DAFTAR_ULANG_DIVERIFIKASI,
                self::MENGUNDURKAN_DIRI,
            ],
            self::DAFTAR_ULANG_DIVERIFIKASI => [
                self::RESMI_TERDAFTAR,
                self::MENGUNDURKAN_DIRI,
            ],
            self::RESMI_TERDAFTAR => [
                self::MENGUNDURKAN_DIRI,
            ],
            self::MENGUNDURKAN_DIRI => [
                // Can be restored to any previous active state via restore workflow
            ],
        };
    }

    /**
     * Check if transition to target status is valid.
     */
    public function canTransitionTo(self $target): bool
    {
        if ($this === $target) {
            return false;
        }

        // MENGUNDURKAN_DIRI can be triggered from almost any non-withdrawn state
        if ($target === self::MENGUNDURKAN_DIRI && $this !== self::MENGUNDURKAN_DIRI) {
            return true;
        }

        return in_array($target, $this->allowedTransitions(), true);
    }
}
