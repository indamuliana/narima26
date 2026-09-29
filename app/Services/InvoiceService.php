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
     * Format nomor tagihan unik berbasis nomor pendaftaran dan jenis tagihan.
     * Contoh: TAG-DU-26AAY0001 atau TAG-SRG-26AAY0001
     *
     * @param CalonSiswa $calonSiswa
     * @param string $prefix
     * @return string
     */
    public function generateInvoiceNumber(CalonSiswa $calonSiswa, string $prefix = 'TAG-DU'): string
    {
        $baseNumber = "{$prefix}-" . ($calonSiswa->nomor_pendaftaran ?? ('REG' . $calonSiswa->id));
        $invoiceNumber = $baseNumber;
        $counter = 1;

        while (Tagihan::where('nomor_tagihan', $invoiceNumber)->exists()) {
            $counter++;
            $invoiceNumber = "{$baseNumber}-{$counter}";
        }

        return $invoiceNumber;
    }

    /**
     * Terbitkan Tagihan Daftar Ulang (DSP & SPP Bulan ke-1).
     */
    public function generateRegistrationInvoice(
        CalonSiswa $calonSiswa,
        ?Diskon $diskon = null,
        ?User $actor = null
    ): Tagihan {
        return DB::transaction(function () use ($calonSiswa, $diskon, $actor) {
            $calonSiswa->loadMissing(['program', 'gelombang']);

            $nomorTagihan = $this->generateInvoiceNumber($calonSiswa, 'TAG-DU');
            $tagihan = $this->snapshotService->createRegistrationInvoice($calonSiswa, $nomorTagihan);

            if ($diskon) {
                $tagihan = $this->discountService->applyDiscountToInvoice($tagihan, $diskon, $actor);
            }

            activity('finance')
                ->performedOn($tagihan)
                ->causedBy($actor ?? auth()->user())
                ->withProperties([
                    'calon_siswa_id' => $calonSiswa->id,
                    'nomor_pendaftaran' => $calonSiswa->nomor_pendaftaran,
                    'nomor_tagihan' => $tagihan->nomor_tagihan,
                    'jenis_tagihan' => Tagihan::JENIS_DAFTAR_ULANG,
                    'total_bruto' => $tagihan->total_bruto,
                    'total_diskon' => $tagihan->total_diskon,
                    'total_netto' => $tagihan->total_netto,
                ])
                ->log("Penerbitan tagihan daftar ulang #{$tagihan->nomor_tagihan} untuk calon siswa #{$calonSiswa->nomor_pendaftaran}");

            return $tagihan->fresh(['details', 'diskon']);
        });
    }

    /**
     * Terbitkan Tagihan Seragam & Atribut (Pilihan Siswa Tahap Tertentu).
     */
    public function generateUniformInvoice(
        CalonSiswa $calonSiswa,
        ?User $actor = null,
        int $tahap = 1,
        ?array $specificUkuranIds = null
    ): ?Tagihan {
        return DB::transaction(function () use ($calonSiswa, $actor, $tahap, $specificUkuranIds) {
            $calonSiswa->loadMissing(['program', 'gelombang', 'ukuranSeragam.jenisSeragam']);

            $prefix = $tahap > 1 ? "TAG-SRG{$tahap}" : 'TAG-SRG';
            $nomorTagihan = $this->generateInvoiceNumber($calonSiswa, $prefix);
            $tagihan = $this->snapshotService->createUniformInvoice($calonSiswa, $nomorTagihan, $tahap, $specificUkuranIds);

            if (! $tagihan) {
                return null;
            }

            activity('finance')
                ->performedOn($tagihan)
                ->causedBy($actor ?? auth()->user())
                ->withProperties([
                    'calon_siswa_id' => $calonSiswa->id,
                    'nomor_pendaftaran' => $calonSiswa->nomor_pendaftaran,
                    'nomor_tagihan' => $tagihan->nomor_tagihan,
                    'jenis_tagihan' => Tagihan::JENIS_SERAGAM,
                    'tahap_seragam' => $tahap,
                    'total_bruto' => $tagihan->total_bruto,
                    'total_netto' => $tagihan->total_netto,
                ])
                ->log("Penerbitan tagihan seragam Tahap {$tahap} #{$tagihan->nomor_tagihan} untuk calon siswa #{$calonSiswa->nomor_pendaftaran}");

            return $tagihan->fresh(['details']);
        });
    }

    /**
     * Aktivasi mandiri pemesanan sisa seragam yang sebelumnya berstatus "Pesan Nanti".
     */
    public function activatePendingUniforms(
        CalonSiswa $calonSiswa,
        array $ukuranSeragamIds,
        ?User $actor = null
    ): Tagihan {
        return DB::transaction(function () use ($calonSiswa, $ukuranSeragamIds, $actor) {
            // Tentukan nomor tahap berikutnya (misal jika tahap 1 sudah ada, gunakan 2)
            $maxTahap = (int) $calonSiswa->tagihan()
                ->where('jenis_tagihan', Tagihan::JENIS_SERAGAM)
                ->max('tahap_seragam');
            $nextTahap = max(2, $maxTahap + 1);

            $tagihan = $this->generateUniformInvoice(
                calonSiswa: $calonSiswa,
                actor: $actor,
                tahap: $nextTahap,
                specificUkuranIds: $ukuranSeragamIds
            );

            if (! $tagihan) {
                throw new RuntimeException("Tidak ada seragam valid yang dapat diterbitkan tagihannya.");
            }

            return $tagihan;
        });
    }

    /**
     * Terbitkan kedua tagihan (Daftar Ulang dan Seragam) untuk calon siswa.
     */
    public function generateInvoice(
        CalonSiswa $calonSiswa,
        ?Diskon $diskon = null,
        ?User $actor = null
    ): Tagihan {
        return DB::transaction(function () use ($calonSiswa, $diskon, $actor) {
            $diskon = $diskon ?? $calonSiswa->diskon()->where('status_persetujuan', 'DISETUJUI')->whereNull('tagihan_id')->first();

            // 1. Tagihan Daftar Ulang (DSP + SPP)
            $tagihanDU = $this->generateRegistrationInvoice($calonSiswa, $diskon, $actor);

            // 2. Tagihan Seragam (Wajib + Opsional)
            $this->generateUniformInvoice($calonSiswa, $actor);

            return $tagihanDU;
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
