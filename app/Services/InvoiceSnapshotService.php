<?php

namespace App\Services;

use App\Models\CalonSiswa;
use App\Models\MasterBiaya;
use App\Models\Tagihan;
use App\Models\TagihanDetail;
use App\Models\UkuranSeragam;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InvoiceSnapshotService
{
    /**
     * Ambil item MasterBiaya untuk biaya pendidikan (DSP & SPP) yang berlaku bagi calon siswa.
     *
     * @param CalonSiswa $calonSiswa
     * @return Collection
     */
    public function getApplicableRegistrationBiaya(CalonSiswa $calonSiswa): Collection
    {
        $calonSiswa->loadMissing(['program', 'gelombang']);

        $programId = $calonSiswa->program_id ?? \App\Models\MasterProgram::where('aktif', true)->first()?->id;
        $gelombangId = $calonSiswa->gelombang_id ?? \App\Models\MasterGelombang::where('aktif', true)->first()?->id;

        // 1. Ambil DSP sesuai program & gelombang
        $dsp = MasterBiaya::aktif()
            ->where('kategori', 'DSP')
            ->when($programId, fn($q) => $q->where('program_id', $programId))
            ->when($gelombangId, fn($q) => $q->where('gelombang_id', $gelombangId))
            ->first();

        // 2. Ambil SPP Bulan ke-1 sesuai program
        $spp = MasterBiaya::aktif()
            ->where('kategori', 'SPP')
            ->when($programId, fn($q) => $q->where('program_id', $programId))
            ->first();

        $items = collect();
        if ($dsp) $items->push($dsp);
        if ($spp) $items->push($spp);

        // 3. Ambil Biaya Asrama: Wajib bagi calon siswa yang memilih program "Unggulan" (sama untuk setiap gelombang)
        $isUnggulan = false;
        if ($calonSiswa->program) {
            $isUnggulan = strcasecmp($calonSiswa->program->kode, 'UGG') === 0
                || stripos($calonSiswa->program->nama, 'Unggulan') !== false;
        } elseif ($programId) {
            $prog = \App\Models\MasterProgram::find($programId);
            $isUnggulan = $prog && (strcasecmp($prog->kode, 'UGG') === 0 || stripos($prog->nama, 'Unggulan') !== false);
        }

        if ($isUnggulan) {
            $asrama = MasterBiaya::aktif()
                ->where('kategori', 'ASRAMA')
                ->where(function ($q) use ($programId) {
                    $q->where('program_id', $programId)
                      ->orWhereNull('program_id');
                })
                ->where(function ($q) use ($gelombangId) {
                    $q->whereNull('gelombang_id')
                      ->orWhere('gelombang_id', $gelombangId);
                })
                ->first();

            if ($asrama) {
                $items->push($asrama);
            }
        }

        // Fallback jika belum spesifik, cari biaya umum dengan kategori daftar_ulang / dsp / spp / asrama
        if ($items->isEmpty()) {
            $items = MasterBiaya::aktif()
                ->whereIn('kategori', ['DSP', 'SPP', 'ASRAMA', 'daftar_ulang'])
                ->get();
        }

        return $items;
    }

    /**
     * Ambil item MasterBiaya untuk paket seragam (Wajib + Opsional yang dipilih siswa).
     *
     * @param CalonSiswa $calonSiswa
     * @return Collection
     */
    /**
     * Ambil item seragam beserta MasterBiaya pasangannya untuk calon siswa.
     *
     * @param CalonSiswa $calonSiswa
     * @param int $tahap
     * @param array|null $specificUkuranIds
     * @return Collection
     */
    public function getApplicableUniformBiayaWithEntries(
        CalonSiswa $calonSiswa,
        int $tahap = 1,
        ?array $specificUkuranIds = null
    ): Collection {
        $calonSiswa->loadMissing(['ukuranSeragam.jenisSeragam']);

        $query = $calonSiswa->ukuranSeragam();

        if ($specificUkuranIds !== null) {
            $query->whereIn('id', $specificUkuranIds);
        } else {
            // Tahap 1: ambil seragam yang berstatus PESAN_SEKARANG (atau beli_di_sekolah = true) dan belum tertaut tagihan
            $query->where(function ($q) {
                $q->where('status_pemesanan', UkuranSeragam::STATUS_PESAN_SEKARANG)
                  ->orWhere('beli_di_sekolah', true);
            })->whereNull('tagihan_id');
        }

        $entries = $query->get();
        $results = collect();

        $biayaList = MasterBiaya::aktif()
            ->where('kategori', 'SERAGAM')
            ->where(function ($q) use ($calonSiswa) {
                $q->whereNull('jenis_kelamin')
                  ->orWhere('jenis_kelamin', $calonSiswa->jenis_kelamin);
            })
            ->get();

        foreach ($entries as $entry) {
            $namaSeragam = $entry->jenisSeragam?->nama_jenis;
            if (!$namaSeragam) {
                continue;
            }

            $matched = $biayaList->first(function ($b) use ($namaSeragam) {
                return stripos($b->nama_biaya, $namaSeragam) !== false || stripos($namaSeragam, $b->nama_biaya) !== false;
            });

            if ($matched) {
                $results->push([
                    'entry' => $entry,
                    'biaya' => $matched,
                ]);
            }
        }

        return $results;
    }

    /**
     * Ambil item MasterBiaya untuk paket seragam yang dipilih siswa pada tahap ini.
     *
     * @param CalonSiswa $calonSiswa
     * @return Collection
     */
    public function getApplicableUniformBiaya(CalonSiswa $calonSiswa): Collection
    {
        return $this->getApplicableUniformBiayaWithEntries($calonSiswa)->pluck('biaya');
    }

    /**
     * Buat snapshot Tagihan Pendaftaran/Daftar Ulang (DSP & SPP Bulan ke-1).
     *
     * @param CalonSiswa $calonSiswa
     * @param string $nomorTagihan
     * @return Tagihan
     */
    public function createRegistrationInvoice(CalonSiswa $calonSiswa, string $nomorTagihan): Tagihan
    {
        return DB::transaction(function () use ($calonSiswa, $nomorTagihan) {
            $applicableBiaya = $this->getApplicableRegistrationBiaya($calonSiswa);

            if ($applicableBiaya->isEmpty()) {
                throw new RuntimeException("Tidak ada master biaya aktif untuk DSP & SPP program/gelombang calon siswa ini.");
            }

            $programName = $calonSiswa->program?->nama ?? 'Reguler';
            $gelombangName = $calonSiswa->gelombang?->nama ?? 'Gelombang 1';
            $totalBruto = (float) $applicableBiaya->sum('nominal');

            $tagihan = Tagihan::create([
                'calon_siswa_id' => $calonSiswa->id,
                'nomor_tagihan' => $nomorTagihan,
                'jenis_tagihan' => Tagihan::JENIS_DAFTAR_ULANG,
                'program_snapshot' => $programName,
                'gelombang_snapshot' => $gelombangName,
                'total_bruto' => $totalBruto,
                'total_diskon' => 0,
                'total_netto' => $totalBruto,
                'status' => Tagihan::STATUS_BELUM_LUNAS,
                'diskon_id' => null,
            ]);

            foreach ($applicableBiaya as $biaya) {
                TagihanDetail::create([
                    'tagihan_id' => $tagihan->id,
                    'kode_biaya_snapshot' => $biaya->kode_biaya,
                    'nama_biaya_snapshot' => $biaya->nama_biaya,
                    'kategori_snapshot' => $biaya->kategori ?? 'DSP',
                    'nominal_snapshot' => $biaya->nominal,
                    'jumlah' => 1,
                    'subtotal' => $biaya->nominal,
                ]);
            }

            return $tagihan->load('details');
        });
    }

    /**
     * Buat snapshot Tagihan Seragam & Atribut (Hanya seragam yang dipesan sekarang).
     *
     * @param CalonSiswa $calonSiswa
     * @param string $nomorTagihan
     * @param int $tahap
     * @param array|null $specificUkuranIds
     * @return Tagihan|null
     */
    public function createUniformInvoice(
        CalonSiswa $calonSiswa,
        string $nomorTagihan,
        int $tahap = 1,
        ?array $specificUkuranIds = null
    ): ?Tagihan {
        return DB::transaction(function () use ($calonSiswa, $nomorTagihan, $tahap, $specificUkuranIds) {
            $matchedPairs = $this->getApplicableUniformBiayaWithEntries($calonSiswa, $tahap, $specificUkuranIds);

            if ($matchedPairs->isEmpty()) {
                return null;
            }

            $programName = $calonSiswa->program?->nama ?? 'Reguler';
            $gelombangName = $calonSiswa->gelombang?->nama ?? 'Gelombang 1';
            $totalBruto = (float) $matchedPairs->sum(fn($pair) => $pair['biaya']->nominal);

            $tagihan = Tagihan::create([
                'calon_siswa_id' => $calonSiswa->id,
                'nomor_tagihan' => $nomorTagihan,
                'jenis_tagihan' => Tagihan::JENIS_SERAGAM,
                'tahap_seragam' => $tahap,
                'program_snapshot' => $programName,
                'gelombang_snapshot' => $gelombangName,
                'total_bruto' => $totalBruto,
                'total_diskon' => 0,
                'total_netto' => $totalBruto,
                'status' => Tagihan::STATUS_BELUM_LUNAS,
                'diskon_id' => null,
            ]);

            foreach ($matchedPairs as $pair) {
                $biaya = $pair['biaya'];
                $entry = $pair['entry'];

                TagihanDetail::create([
                    'tagihan_id' => $tagihan->id,
                    'kode_biaya_snapshot' => $biaya->kode_biaya,
                    'nama_biaya_snapshot' => $biaya->nama_biaya . ($entry->ukuran ? " ({$entry->ukuran})" : ''),
                    'kategori_snapshot' => 'SERAGAM',
                    'nominal_snapshot' => $biaya->nominal,
                    'jumlah' => $entry->jumlah ?? 1,
                    'subtotal' => $biaya->nominal * ($entry->jumlah ?? 1),
                ]);

                // Hubungkan ukuran_seragam ke tagihan ini
                $entry->update([
                    'tagihan_id' => $tagihan->id,
                    'tahap_pemesanan' => $tahap,
                    'status_pemesanan' => UkuranSeragam::STATUS_PESAN_SEKARANG,
                    'beli_di_sekolah' => true,
                ]);
            }

            return $tagihan->load('details');
        });
    }

    /**
     * Backward-compatibility: create snapshot default registration invoice.
     */
    public function createSnapshot(CalonSiswa $calonSiswa, string $nomorTagihan): Tagihan
    {
        return $this->createRegistrationInvoice($calonSiswa, $nomorTagihan);
    }
}
