<?php

namespace App\Services;

use App\Models\CalonSiswa;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RegistrationNumberService
{
    /**
     * Default prefix format: 26AAY
     * Sesuai Section 13: 26AAYXXXX (Contoh: 26AAY0001, 26AAY0002)
     */
    public const DEFAULT_PREFIX = '26AAY';

    /**
     * Generate nomor pendaftaran baru secara terpusat, unik, dan aman terhadap konkurensi.
     * Menggunakan DB Transaction dan lockForUpdate (termasuk withTrashed).
     *
     * @param string $prefix
     * @return string
     */
    public function generate(string $prefix = self::DEFAULT_PREFIX): string
    {
        return DB::transaction(function () use ($prefix) {
            // Ambil nomor pendaftaran terakhir dengan prefix yang sama menggunakan row-level lock
            $latest = CalonSiswa::withTrashed()
                ->where('nomor_pendaftaran', 'LIKE', "{$prefix}%")
                ->lockForUpdate()
                ->orderByDesc('nomor_pendaftaran')
                ->first();

            $nextSequence = 1;

            if ($latest && !empty($latest->nomor_pendaftaran)) {
                $rawSequence = substr($latest->nomor_pendaftaran, strlen($prefix));
                if (is_numeric($rawSequence)) {
                    $nextSequence = ((int) $rawSequence) + 1;
                }
            }

            // Loop verifikasi keunikan ekstra untuk menjamin nomor tidak pernah bertabrakan
            do {
                $candidateNumber = $prefix . str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
                $exists = CalonSiswa::withTrashed()
                    ->where('nomor_pendaftaran', $candidateNumber)
                    ->exists();

                if ($exists) {
                    $nextSequence++;
                }
            } while ($exists);

            return $candidateNumber;
        });
    }

    /**
     * Validasi format nomor pendaftaran.
     *
     * @param string|null $number
     * @param string|null $prefix
     * @return bool
     */
    public function isValid(?string $number, ?string $prefix = self::DEFAULT_PREFIX): bool
    {
        if (empty($number)) {
            return false;
        }

        $expectedPrefix = preg_quote($prefix ?? self::DEFAULT_PREFIX, '/');
        return (bool) preg_match('/^' . $expectedPrefix . '\d{4}$/', $number);
    }

    /**
     * Parse nomor urut dari nomor registrasi.
     *
     * @param string $number
     * @param string $prefix
     * @return int|null
     */
    public function parseSequence(string $number, string $prefix = self::DEFAULT_PREFIX): ?int
    {
        if (!$this->isValid($number, $prefix)) {
            return null;
        }

        $raw = substr($number, strlen($prefix));
        return is_numeric($raw) ? (int) $raw : null;
    }
}
