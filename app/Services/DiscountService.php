<?php

namespace App\Services;

use App\Models\CalonSiswa;
use App\Models\Diskon;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DiscountService
{
    /**
     * Hitung nominal potongan berdasarkan metode diskon (persentase atau nominal).
     * Memastikan potongan tidak pernah melebihi total bruto (netto tidak boleh negatif).
     *
     * @param float $totalBruto
     * @param string $metode ('persentase' atau 'nominal')
     * @param float $nilai (misal 10 untuk 10% atau 500000 untuk Rp 500.000)
     * @return float
     */
    public function calculatePotongan(float $totalBruto, string $metode, float $nilai): float
    {
        if ($totalBruto <= 0 || $nilai <= 0) {
            return 0.0;
        }

        $metode = strtolower(trim($metode));

        if ($metode === 'persentase') {
            if ($nilai > 100) {
                $nilai = 100; // Maksimal diskon 100%
            }
            $potongan = round(($totalBruto * $nilai) / 100, 2);
        } elseif ($metode === 'nominal') {
            $potongan = min($nilai, $totalBruto);
        } else {
            throw new InvalidArgumentException("Metode diskon '{$metode}' tidak valid. Gunakan 'persentase' atau 'nominal'.");
        }

        // Garansi nilai potongan tidak melebihi total bruto
        return min($potongan, $totalBruto);
    }

    /**
     * Buat data diskon baru untuk calon siswa dengan pencatatan audit.
     *
     * @param CalonSiswa $calonSiswa
     * @param array $data [jenis_diskon, metode_diskon, nilai_diskon, alasan, keterangan]
     * @param User|null $grantedBy
     * @param User|null $approvedBy
     * @param float|null $referensiBruto
     * @return Diskon
     */
    public function createDiscount(
        CalonSiswa $calonSiswa,
        array $data,
        ?User $grantedBy = null,
        ?User $approvedBy = null,
        ?float $referensiBruto = null
    ): Diskon {
        return DB::transaction(function () use ($calonSiswa, $data, $grantedBy, $approvedBy, $referensiBruto) {
            $metode = strtolower($data['metode_diskon'] ?? 'nominal');
            $nilai = (float) ($data['nilai_diskon'] ?? 0);

            // Jika referensi bruto diberikan, hitung langsung nominal potongan
            $nominalPotongan = 0.0;
            if ($referensiBruto !== null && $referensiBruto > 0) {
                $nominalPotongan = $this->calculatePotongan($referensiBruto, $metode, $nilai);
            } elseif ($metode === 'nominal') {
                $nominalPotongan = $nilai;
            }

            $diskon = Diskon::create([
                'calon_siswa_id' => $calonSiswa->id,
                'jenis_diskon' => $data['jenis_diskon'] ?? 'Diskon Khusus',
                'metode_diskon' => $metode,
                'nilai_diskon' => $nilai,
                'nominal_potongan' => $nominalPotongan,
                'alasan' => $data['alasan'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
                'diberikan_oleh' => $grantedBy?->id,
                'disetujui_oleh' => $approvedBy?->id,
                'diberikan_at' => now(),
            ]);

            // Audit Trail
            activity('finance')
                ->performedOn($diskon)
                ->causedBy($grantedBy ?? auth()->user())
                ->withProperties([
                    'calon_siswa_id' => $calonSiswa->id,
                    'nomor_pendaftaran' => $calonSiswa->nomor_pendaftaran,
                    'jenis_diskon' => $diskon->jenis_diskon,
                    'metode_diskon' => $metode,
                    'nilai_diskon' => $nilai,
                    'nominal_potongan' => $nominalPotongan,
                ])
                ->log("Pemberian diskon {$diskon->jenis_diskon} untuk calon siswa #{$calonSiswa->nomor_pendaftaran}");

            return $diskon;
        });
    }

    /**
     * Terapkan diskon ke tagihan dan hitung ulang total netto.
     *
     * @param Tagihan $tagihan
     * @param Diskon $diskon
     * @param User|null $actor
     * @return Tagihan
     */
    public function applyDiscountToInvoice(Tagihan $tagihan, Diskon $diskon, ?User $actor = null): Tagihan
    {
        return DB::transaction(function () use ($tagihan, $diskon, $actor) {
            $totalBruto = (float) $tagihan->total_bruto;
            $potongan = $this->calculatePotongan($totalBruto, $diskon->metode_diskon, (float) $diskon->nilai_diskon);

            // Update record diskon dengan nominal potongan akurat berdasarkan bruto tagihan ini
            $diskon->update([
                'nominal_potongan' => $potongan,
            ]);

            $totalNetto = max(0, $totalBruto - $potongan);

            $tagihan->update([
                'diskon_id' => $diskon->id,
                'total_diskon' => $potongan,
                'total_netto' => $totalNetto,
            ]);

            activity('finance')
                ->performedOn($tagihan)
                ->causedBy($actor ?? auth()->user())
                ->withProperties([
                    'nomor_tagihan' => $tagihan->nomor_tagihan,
                    'diskon_id' => $diskon->id,
                    'total_bruto' => $totalBruto,
                    'total_diskon' => $potongan,
                    'total_netto' => $totalNetto,
                ])
                ->log("Penerapan diskon {$diskon->jenis_diskon} pada tagihan #{$tagihan->nomor_tagihan}");

            return $tagihan->fresh();
        });
    }
}
