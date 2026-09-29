<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\PembayaranSeleksi;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class PaymentVerificationService
{
    public function __construct(
        protected FileUploadService $fileUploadService,
        protected SpmbStatusService $statusService
    ) {}

    /**
     * Calon siswa mengunggah bukti pembayaran biaya seleksi.
     *
     * @param CalonSiswa $calonSiswa
     * @param array $data [bank_pengirim, nama_pengirim, nomor_referensi, tanggal_bayar, nominal_dibayar]
     * @param UploadedFile $file
     * @return PembayaranSeleksi
     */
    public function submitSelectionPaymentProof(
        CalonSiswa $calonSiswa,
        array $data,
        UploadedFile $file
    ): PembayaranSeleksi {
        return DB::transaction(function () use ($calonSiswa, $data, $file) {
            // Upload file dengan nama UUID acak dan terproteksi (Section 32)
            $filePath = $this->fileUploadService->uploadPaymentProof($file, 'seleksi');

            // Ambil record pembayaran seleksi atau buat baru jika belum ada
            $pembayaran = PembayaranSeleksi::firstOrNew([
                'calon_siswa_id' => $calonSiswa->id,
            ]);

            // Hapus file bukti transfer lama jika ada (misal saat upload ulang setelah ditolak)
            if ($pembayaran->exists && $pembayaran->bukti_transfer_path) {
                $this->fileUploadService->deleteFile($pembayaran->bukti_transfer_path);
            }

            $pembayaran->fill([
                'nominal_tagihan' => $pembayaran->nominal_tagihan ?: 200000,
                'nominal_dibayar' => $data['nominal_dibayar'] ?? 200000,
                'tanggal_bayar' => $data['tanggal_bayar'] ?? now()->toDateString(),
                'metode_bayar' => $data['metode_bayar'] ?? 'transfer_bank',
                'bank_pengirim' => $data['bank_pengirim'],
                'nama_pengirim' => $data['nama_pengirim'],
                'nomor_referensi' => $data['nomor_referensi'] ?? null,
                'bukti_transfer_path' => $filePath,
                'status' => PaymentStatus::PENDING->value,
                'catatan_bendahara' => null, // Reset catatan jika sebelumnya pernah ditolak
            ]);

            $pembayaran->save();

            // Audit Trail
            activity('finance')
                ->performedOn($pembayaran)
                ->causedBy(auth()->user())
                ->withProperties([
                    'calon_siswa_id' => $calonSiswa->id,
                    'nomor_pendaftaran' => $calonSiswa->nomor_pendaftaran,
                    'bank_pengirim' => $pembayaran->bank_pengirim,
                    'nominal_dibayar' => $pembayaran->nominal_dibayar,
                ])
                ->log("Calon siswa #{$calonSiswa->nomor_pendaftaran} mengunggah bukti pembayaran seleksi");

            return $pembayaran->fresh();
        });
    }

    /**
     * Bendahara memverifikasi pembayaran seleksi (DIVERIFIKASI).
     * Memicu transisi status calon siswa ke PEMBAYARAN_SELEKSI_DIVERIFIKASI (Section 5 & 17).
     *
     * @param PembayaranSeleksi $pembayaran
     * @param User $bendahara
     * @param float|null $nominalDiterima
     * @param string|null $catatan
     * @return PembayaranSeleksi
     */
    public function verifySelectionPayment(
        PembayaranSeleksi $pembayaran,
        User $bendahara,
        ?float $nominalDiterima = null,
        ?string $catatan = null
    ): PembayaranSeleksi {
        return DB::transaction(function () use ($pembayaran, $bendahara, $nominalDiterima, $catatan) {
            if ($pembayaran->status === PaymentStatus::DIVERIFIKASI->value) {
                throw new RuntimeException("Pembayaran seleksi ini telah diverifikasi sebelumnya.");
            }

            $calonSiswa = $pembayaran->calonSiswa;

            // 1. Update status pembayaran seleksi
            $pembayaran->update([
                'status' => PaymentStatus::DIVERIFIKASI->value,
                'nominal_dibayar' => $nominalDiterima ?? $pembayaran->nominal_dibayar,
                'catatan_bendahara' => $catatan,
                'verified_by' => $bendahara->id,
                'verified_at' => now(),
            ]);

            // 2. Transisi status SPMB calon siswa
            if ($calonSiswa->status_spmb === SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI) {
                $this->statusService->transition(
                    calonSiswa: $calonSiswa,
                    targetStatus: SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI,
                    catatan: "Pembayaran seleksi sebesar Rp " . number_format($pembayaran->nominal_dibayar, 0, ',', '.') . " telah diverifikasi oleh {$bendahara->name}.",
                    changedBy: $bendahara
                );
            }

            // 3. Catat ke Audit Trail
            activity('finance')
                ->performedOn($pembayaran)
                ->causedBy($bendahara)
                ->withProperties([
                    'calon_siswa_id' => $calonSiswa->id,
                    'nomor_pendaftaran' => $calonSiswa->nomor_pendaftaran,
                    'nominal_diverifikasi' => $pembayaran->nominal_dibayar,
                    'verified_by' => $bendahara->name,
                ])
                ->log("Bendahara memverifikasi pembayaran seleksi calon siswa #{$calonSiswa->nomor_pendaftaran}");

            return $pembayaran->fresh();
        });
    }

    /**
     * Bendahara menolak bukti pembayaran seleksi (DITOLAK) dengan menyertakan alasan.
     * Calon siswa tetap berstatus MENUNGGU_PEMBAYARAN_SELEKSI untuk mengunggah ulang bukti baru.
     *
     * @param PembayaranSeleksi $pembayaran
     * @param User $bendahara
     * @param string $alasan
     * @return PembayaranSeleksi
     */
    public function rejectSelectionPayment(
        PembayaranSeleksi $pembayaran,
        User $bendahara,
        string $alasan
    ): PembayaranSeleksi {
        if (empty(trim($alasan))) {
            throw new InvalidArgumentException("Alasan penolakan wajib diisi agar calon siswa memahami kendala transfer.");
        }

        return DB::transaction(function () use ($pembayaran, $bendahara, $alasan) {
            $calonSiswa = $pembayaran->calonSiswa;

            $pembayaran->update([
                'status' => PaymentStatus::DITOLAK->value,
                'catatan_bendahara' => $alasan,
                'verified_by' => $bendahara->id,
                'verified_at' => now(),
            ]);

            // Audit Trail
            activity('finance')
                ->performedOn($pembayaran)
                ->causedBy($bendahara)
                ->withProperties([
                    'calon_siswa_id' => $calonSiswa->id,
                    'nomor_pendaftaran' => $calonSiswa->nomor_pendaftaran,
                    'alasan_penolakan' => $alasan,
                    'verified_by' => $bendahara->name,
                ])
                ->log("Bendahara menolak bukti pembayaran seleksi calon siswa #{$calonSiswa->nomor_pendaftaran}");

            return $pembayaran->fresh();
        });
    }
}
