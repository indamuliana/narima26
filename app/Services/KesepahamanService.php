<?php

namespace App\Services;

use App\Models\CalonSiswa;
use App\Models\KesepahamanProgram;

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

    private function getProgramDataFromDb(string $programKey): array
    {
        $program = KesepahamanProgram::with(['kelompoks' => function($q) {
            $q->orderBy('urutan');
        }, 'kelompoks.poins' => function($q) {
            $q->orderBy('urutan');
        }])->where('kode', strtolower($programKey))->first();

        if (!$program) {
            return [
                'program_title' => strtoupper($programKey),
                'tahun_pelajaran' => '2027/2028',
                'kelompok' => [],
            ];
        }

        $kelompokArray = [];
        foreach ($program->kelompoks as $kelompok) {
            $poinArray = [];
            foreach ($kelompok->poins as $poin) {
                $poinArray[] = [
                    'id' => $poin->kode_poin,
                    'nomor' => $poin->nomor,
                    'uraian' => $poin->uraian,
                ];
            }
            $kelompokArray[] = [
                'kode' => $kelompok->kode,
                'judul' => $kelompok->judul,
                'poin' => $poinArray,
            ];
        }

        return [
            'program_title' => $program->nama,
            'tahun_pelajaran' => $program->tahun_pelajaran,
            'kelompok' => $kelompokArray,
        ];
    }

    /**
     * Ambil data kelompok dan butir klausul sesuai program calon siswa.
     */
    public function getKlausulByCalonSiswa(CalonSiswa $calonSiswa): array
    {
        $programKey = $this->isUnggulan($calonSiswa) ? 'unggulan' : 'reguler';
        
        $configData = $this->getProgramDataFromDb($programKey);

        return [
            'program_key' => $programKey,
            'program_type' => strtoupper($programKey),
            'program_title' => $configData['program_title'],
            'tahun_pelajaran' => $configData['tahun_pelajaran'],
            'kelompok' => $configData['kelompok'],
        ];
    }

    /**
     * Ambil seluruh ID poin yang wajib dicentang untuk program tertentu.
     */
    public function getRequiredPointIds(string $programKey): array
    {
        $configData = $this->getProgramDataFromDb($programKey);
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
        $configData = $this->getProgramDataFromDb($programKey);
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
