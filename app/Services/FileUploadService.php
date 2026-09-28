<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FileUploadService
{
    /**
     * Ekstensi dan MIME types yang diizinkan untuk bukti transfer dan dokumen pendaftaran.
     */
    public const ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png'];
    public const ALLOWED_DOC_EXTENSIONS = ['jpg', 'jpeg', 'png', 'pdf'];

    public const MAX_SIZE_PAYMENT_KB = 2048; // 2 MB
    public const MAX_SIZE_DOC_KB = 5120;     // 5 MB

    /**
     * Validasi berkas upload terhadap ekstensi dan batasan ukuran.
     *
     * @param UploadedFile $file
     * @param array $allowedExtensions
     * @param int $maxKb
     * @return void
     * @throws InvalidArgumentException
     */
    public function validateFile(
        UploadedFile $file,
        array $allowedExtensions = self::ALLOWED_DOC_EXTENSIONS,
        int $maxKb = self::MAX_SIZE_PAYMENT_KB
    ): void {
        if (!$file->isValid()) {
            throw new InvalidArgumentException("File yang diunggah tidak valid atau gagal terkirim.");
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions, true)) {
            $allowedStr = implode(', ', $allowedExtensions);
            throw new InvalidArgumentException("Format berkas .{$extension} tidak diizinkan. Format yang diterima: {$allowedStr}.");
        }

        $sizeKb = $file->getSize() / 1024;
        if ($sizeKb > $maxKb) {
            $maxMb = round($maxKb / 1024, 1);
            throw new InvalidArgumentException("Ukuran berkas melebihi batas maksimal {$maxMb} MB.");
        }
    }

    /**
     * Upload bukti pembayaran transfer (seleksi atau daftar ulang).
     * Nama berkas disanitasi menjadi UUID acak untuk menjamin keamanan (Section 32).
     *
     * @param UploadedFile $file
     * @param string $category 'seleksi' atau 'daftar_ulang'
     * @param string $disk
     * @return string Path relatif file yang disimpan
     */
    public function uploadPaymentProof(UploadedFile $file, string $category = 'seleksi', string $disk = 'public'): string
    {
        $this->validateFile($file, self::ALLOWED_DOC_EXTENSIONS, self::MAX_SIZE_PAYMENT_KB);

        $folder = match ($category) {
            'daftar_ulang' => 'bukti-bayar-daftar-ulang',
            default => 'bukti-bayar-seleksi',
        };

        return $this->storeWithSecureName($file, $folder, $disk);
    }

    /**
     * Upload dokumen persyaratan calon siswa (KK, Ijazah, Akta, dll).
     *
     * @param UploadedFile $file
     * @param int|string $calonSiswaId
     * @param string $docType
     * @param string $disk
     * @return string Path relatif
     */
    public function uploadStudentDocument(
        UploadedFile $file,
        int|string $calonSiswaId,
        string $docType,
        string $disk = 'public'
    ): string {
        $this->validateFile($file, self::ALLOWED_DOC_EXTENSIONS, self::MAX_SIZE_DOC_KB);

        $sanitizedDocType = Str::slug($docType);
        $folder = "dokumen-siswa/{$calonSiswaId}/{$sanitizedDocType}";

        return $this->storeWithSecureName($file, $folder, $disk);
    }

    /**
     * Simpan berkas dengan nama aman (UUIDv4) untuk mencegah collision dan path traversal.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string $disk
     * @return string
     */
    protected function storeWithSecureName(UploadedFile $file, string $folder, string $disk): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = Str::uuid()->toString() . '.' . $extension;

        return $file->storeAs($folder, $filename, $disk);
    }

    /**
     * Hapus berkas dari storage.
     *
     * @param string|null $path
     * @param string $disk
     * @return bool
     */
    public function deleteFile(?string $path, string $disk = 'public'): bool
    {
        if (empty($path)) {
            return false;
        }

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Ambil URL publik dari berkas yang diunggah.
     *
     * @param string|null $path
     * @param string $disk
     * @return string|null
     */
    public function getUrl(?string $path, string $disk = 'public'): ?string
    {
        if (empty($path)) {
            return null;
        }

        return Storage::disk($disk)->url($path);
    }
}
