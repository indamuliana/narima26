<?php

namespace App\Services;

use App\Models\CalonSiswa;

class KesepahamanService
{
    /**
     * Cek apakah calon siswa mengambil Program Unggulan.
     */
    public function isUnggulan(CalonSiswa $calonSiswa): bool
    {
        $calonSiswa->loadMissing('program');
        $nama = strtoupper($calonSiswa->program?->nama ?? '');
        $kode = strtoupper($calonSiswa->program?->kode ?? '');

        return str_contains($nama, 'UNGGULAN') || in_array($kode, ['UGG', 'UNGGULAN']);
    }

    /**
     * Ambil data kelompok dan butir klausul sesuai program calon siswa.
     */
    public function getKlausulByCalonSiswa(CalonSiswa $calonSiswa): array
    {
        $programKey = $this->isUnggulan($calonSiswa) ? 'unggulan' : 'reguler';

        $configData = config("spmb_kesepahaman.{$programKey}", []);
        if (empty($configData)) {
            // Fallback default
            $configData = [
                'program_title' => strtoupper($programKey),
                'tahun_pelajaran' => '2027/2028',
                'kelompok' => [],
            ];
        }

        return [
            'program_key' => $programKey,
            'program_type' => strtoupper($programKey),
            'program_title' => $configData['program_title'] ?? strtoupper($programKey),
            'tahun_pelajaran' => $configData['tahun_pelajaran'] ?? '2027/2028',
            'kelompok' => $configData['kelompok'] ?? [],
        ];
    }

    /**
     * Ambil seluruh ID poin yang wajib dicentang untuk program tertentu.
     */
    public function getRequiredPointIds(string $programKey): array
    {
        $programKey = strtolower($programKey);
        $configData = config("spmb_kesepahaman.{$programKey}", []);
        $kelompokList = $configData['kelompok'] ?? [];

        $pointIds = [];
        foreach ($kelompokList as $kelompok) {
            foreach ($kelompok['poin'] ?? [] as $poin) {
                if (!empty($poin['id'])) {
                    $pointIds[] = $poin['id'];
                }
            }
        }

        return $pointIds;
    }

    /**
     * Ambil seluruh butir poin dalam bentuk flat array (untuk rendering cepat).
     */
    public function getAllPointsFlat(string $programKey): array
    {
        $programKey = strtolower($programKey);
        $configData = config("spmb_kesepahaman.{$programKey}", []);
        $kelompokList = $configData['kelompok'] ?? [];

        $flatPoints = [];
        $globalIndex = 1;

        foreach ($kelompokList as $kelompok) {
            foreach ($kelompok['poin'] ?? [] as $poin) {
                $flatPoints[] = array_merge($poin, [
                    'global_no' => $globalIndex++,
                    'kelompok_kode' => $kelompok['kode'] ?? '',
                    'kelompok_judul' => $kelompok['judul'] ?? '',
                ]);
            }
        }

        return $flatPoints;
    }
}
