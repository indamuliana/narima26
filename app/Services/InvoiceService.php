<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\CalonSiswa;
use App\Models\Diskon;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InvoiceService
{
    public function __construct(
        protected InvoiceSnapshotService $snapshotService,
        protected DiscountService $discountService
    ) {}

    /**
     * Format nomor tagihan unik berbasis nomor pendaftaran.
     * Contoh: TAG-26AAY0001
     *
     * @param CalonSiswa $calonSiswa
     * @return string
     */
    public function generateInvoiceNumber(CalonSiswa $calonSiswa): string
    {
        $baseNumber = 'TAG-' . ($calonSiswa->nomor_pendaftaran ?? ('REG' . $calonSiswa->id));
        $invoiceNumber = $baseNumber;
        $counter = 1;

        while (Tagihan::where('nomor_tagihan', $invoiceNumber)->exists()) {
            $counter++;
            $invoiceNumber = "{$baseNumber}-{$counter}";
        }

        return $invoiceNumber;
    }

    /**
     * Terbitkan tagihan daftar ulang calon siswa dengan snapshot biaya.
     * Jika diskon disertakan, potongan langsung diterapkan ke tagihan.
     *
     * @param CalonSiswa $calonSiswa
     * @param Diskon|null $diskon
     * @param User|null $actor
     * @return Tagihan
     */
    public function generateInvoice(
        CalonSiswa $calonSiswa,
        ?Diskon $diskon = null,
        ?User $actor = null
    ): Tagihan {
        return DB::transaction(function () use ($calonSiswa, $diskon, $actor) {
            // Pastikan relasi program & gelombang dimuat
            $calonSiswa->loadMissing(['program', 'gelombang']);

            $nomorTagihan = $this->generateInvoiceNumber($calonSiswa);

            // Buat snapshot tagihan beku dari master biaya aktif
            $tagihan = $this->snapshotService->createSnapshot($calonSiswa, $nomorTagihan);

            // Terapkan diskon jika ada
            if ($diskon) {
                $tagihan = $this->discountService->applyDiscountToInvoice($tagihan, $diskon, $actor);
            }

            // Catat ke Audit Trail
            activity('finance')
                ->performedOn($tagihan)
                ->causedBy($actor ?? auth()->user())
                ->withProperties([
                    'calon_siswa_id' => $calonSiswa->id,
                    'nomor_pendaftaran' => $calonSiswa->nomor_pendaftaran,
                    'nomor_tagihan' => $tagihan->nomor_tagihan,
                    'total_bruto' => $tagihan->total_bruto,
                    'total_diskon' => $tagihan->total_diskon,
                    'total_netto' => $tagihan->total_netto,
                ])
                ->log("Penerbitan tagihan daftar ulang #{$tagihan->nomor_tagihan} untuk calon siswa #{$calonSiswa->nomor_pendaftaran}");

            return $tagihan->fresh(['details', 'diskon']);
        });
    }

    /**
     * Hitung total pembayaran daftar ulang yang telah diverifikasi Bendahara.
     *
     * @param Tagihan $tagihan
     * @return float
     */
    public function getTotalPaidVerified(Tagihan $tagihan): float
    {
        return (float) $tagihan->pembayaran()
            ->where('status', PaymentStatus::DIVERIFIKASI->value)
            ->sum('nominal_dibayar');
    }

    /**
     * Hitung sisa tagihan yang belum terbayar.
     *
     * @param Tagihan $tagihan
     * @return float
     */
    public function getRemainingBalance(Tagihan $tagihan): float
    {
        $totalPaid = $this->getTotalPaidVerified($tagihan);
        return max(0, (float) $tagihan->total_netto - $totalPaid);
    }

    /**
     * Sinkronisasi status tagihan (BELUM_LUNAS, CICILAN, LUNAS)
     * berdasarkan riwayat pembayaran yang telah diverifikasi.
     *
     * @param Tagihan $tagihan
     * @return Tagihan
     */
    public function syncPaymentStatus(Tagihan $tagihan): Tagihan
    {
        return DB::transaction(function () use ($tagihan) {
            $totalPaid = $this->getTotalPaidVerified($tagihan);
            $totalNetto = (float) $tagihan->total_netto;

            $previousStatus = $tagihan->status;
            $newStatus = Tagihan::STATUS_BELUM_LUNAS;

            if ($totalPaid >= $totalNetto && $totalNetto > 0) {
                $newStatus = Tagihan::STATUS_LUNAS;
            } elseif ($totalPaid > 0) {
                $newStatus = Tagihan::STATUS_CICILAN;
            }

            if ($previousStatus !== $newStatus) {
                $tagihan->update(['status' => $newStatus]);

                activity('finance')
                    ->performedOn($tagihan)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'nomor_tagihan' => $tagihan->nomor_tagihan,
                        'previous_status' => $previousStatus,
                        'new_status' => $newStatus,
                        'total_netto' => $totalNetto,
                        'total_paid' => $totalPaid,
                    ])
                    ->log("Perubahan status tagihan #{$tagihan->nomor_tagihan} menjadi {$newStatus}");
            }

            return $tagihan->fresh();
        });
    }
}
