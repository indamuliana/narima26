<?php

namespace App\Services;

use App\Models\CalonSiswa;
use App\Models\MasterBiaya;
use App\Models\Tagihan;
use App\Models\TagihanDetail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InvoiceSnapshotService
{
    /**
     * Ambil seluruh item MasterBiaya aktif yang berlaku untuk Calon Siswa
     * berdasarkan program dan gelombangnya.
     *
     * @param CalonSiswa $calonSiswa
     * @return Collection
     */
    public function getApplicableMasterBiaya(CalonSiswa $calonSiswa): Collection
    {
        return MasterBiaya::aktif()
            ->where(function ($query) use ($calonSiswa) {
                $query->whereNull('program_id')
                      ->orWhere('program_id', $calonSiswa->program_id);
            })
            ->where(function ($query) use ($calonSiswa) {
                $query->whereNull('gelombang_id')
                      ->orWhere('gelombang_id', $calonSiswa->gelombang_id);
            })
            ->orderBy('id')
            ->get();
    }

    /**
     * Buat snapshot tagihan dan seluruh rincian biayanya ke tabel tagihan & tagihan_detail.
     * Setelah snapshot dibuat, perubahan master biaya di masa depan TIDAK AKAN
     * mengubah nilai tagihan yang sudah terbit (Section 22 & 33).
     *
     * @param CalonSiswa $calonSiswa
     * @param string $nomorTagihan
     * @return Tagihan
     */
    public function createSnapshot(CalonSiswa $calonSiswa, string $nomorTagihan): Tagihan
    {
        return DB::transaction(function () use ($calonSiswa, $nomorTagihan) {
            $applicableBiaya = $this->getApplicableMasterBiaya($calonSiswa);

            if ($applicableBiaya->isEmpty()) {
                throw new RuntimeException("Tidak ada master biaya aktif yang ditemukan untuk program/gelombang calon siswa ini.");
            }

            $programName = $calonSiswa->program?->nama_program ?? 'Program Standar';
            $gelombangName = $calonSiswa->gelombang?->nama_gelombang ?? 'Gelombang 1';

            // Hitung total bruto
            $totalBruto = (float) $applicableBiaya->sum('nominal');

            // Simpan header tagihan
            $tagihan = Tagihan::create([
                'calon_siswa_id' => $calonSiswa->id,
                'nomor_tagihan' => $nomorTagihan,
                'program_snapshot' => $programName,
                'gelombang_snapshot' => $gelombangName,
                'total_bruto' => $totalBruto,
                'total_diskon' => 0,
                'total_netto' => $totalBruto,
                'status' => Tagihan::STATUS_BELUM_LUNAS,
                'diskon_id' => null,
            ]);

            // Simpan snapshot setiap detail biaya
            foreach ($applicableBiaya as $biaya) {
                TagihanDetail::create([
                    'tagihan_id' => $tagihan->id,
                    'kode_biaya_snapshot' => $biaya->kode_biaya,
                    'nama_biaya_snapshot' => $biaya->nama_biaya,
                    'kategori_snapshot' => $biaya->kategori ?? 'biaya_pendidikan',
                    'nominal_snapshot' => $biaya->nominal,
                    'jumlah' => 1,
                    'subtotal' => $biaya->nominal,
                ]);
            }

            return $tagihan->load('details');
        });
    }
}
