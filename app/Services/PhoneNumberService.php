<?php

namespace App\Services;

class PhoneNumberService
{
    /**
     * Normalisasi nomor telepon ke standar internasional Indonesia (62xxx).
     * Contoh:
     * - 081233445566 -> 6281233445566
     * - +6281233445566 -> 6281233445566
     * - 81233445566 -> 6281233445566
     * - 0812-3344-5566 -> 6281233445566
     *
     * @param string|null $number
     * @return string|null
     */
    public function normalize(?string $number): ?string
    {
        if ($number === null) {
            return null;
        }

        // Hapus karakter non-digit selain tanda plus di awal
        $cleaned = preg_replace('/[^\d+]/', '', trim($number));

        if (empty($cleaned)) {
            return null;
        }

        // Hapus tanda '+' jika ada
        if (str_starts_with($cleaned, '+')) {
            $cleaned = substr($cleaned, 1);
        }

        // Jika diawali 0, ganti dengan 62 (contoh: 0812 -> 62812)
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        }
        // Jika diawali langsung angka 8 (contoh: 8123344 -> 628123344)
        elseif (str_starts_with($cleaned, '8')) {
            $cleaned = '62' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Validasi nomor telepon Indonesia.
     * Karakteristik nomor seluler Indonesia:
     * - Diawali dengan '628'
     * - Total digit setelah normalisasi: 10 s.d 15 digit
     *
     * @param string|null $number
     * @return bool
     */
    public function isValid(?string $number): bool
    {
        if (empty($number)) {
            return false;
        }

        $trimmed = trim($number);

        // Hanya izinkan karakter angka, spasi, tanda minus, tanda kurung, dan opsional '+' di awal
        if (!preg_match('/^[+]?[\d\s\-\(\)\.]+$/', $trimmed)) {
            return false;
        }

        $normalized = $this->normalize($trimmed);

        if (!$normalized || !ctype_digit($normalized)) {
            return false;
        }

        $length = strlen($normalized);

        // Nomor seluler Indonesia harus diawali 628 dan panjang antara 10 - 15 digit
        return str_starts_with($normalized, '628') && $length >= 10 && $length <= 15;
    }

    /**
     * Format tampilan nomor telepon ramah lokal (08xx-xxxx-xxxx).
     *
     * @param string|null $number
     * @return string|null
     */
    public function toLocalDisplay(?string $number): ?string
    {
        $normalized = $this->normalize($number);
        if (!$normalized) {
            return null;
        }

        // Ubah prefix 62 kembali ke 0
        $local = str_starts_with($normalized, '62') ? '0' . substr($normalized, 2) : $normalized;

        if (strlen($local) >= 10 && strlen($local) <= 13) {
            // Contoh 0812-3344-5566
            $p1 = substr($local, 0, 4);
            $p2 = substr($local, 4, 4);
            $p3 = substr($local, 8);
            return "{$p1}-{$p2}-{$p3}";
        }

        return $local;
    }

    /**
     * Format link WhatsApp (https://wa.me/628xxx?text=...).
     *
     * @param string|null $number
     * @param string|null $message
     * @return string|null
     */
    public function toWhatsappUrl(?string $number, ?string $message = null): ?string
    {
        $normalized = $this->normalize($number);
        if (!$this->isValid($normalized)) {
            return null;
        }

        $url = "https://wa.me/{$normalized}";
        if (!empty($message)) {
            $url .= '?text=' . urlencode($message);
        }

        return $url;
    }
}
