<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KesepahamanProgram;
use App\Models\KesepahamanKelompok;
use App\Models\KesepahamanPoin;
use Illuminate\Support\Facades\Config;

class KesepahamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = ['reguler', 'unggulan'];

        foreach ($programs as $programKey) {
            $configData = config("spmb_kesepahaman.{$programKey}", []);
            if (empty($configData)) continue;

            $program = KesepahamanProgram::updateOrCreate(
                ['kode' => $programKey],
                [
                    'nama' => $configData['program_title'] ?? strtoupper($programKey),
                    'tahun_pelajaran' => $configData['tahun_pelajaran'] ?? '2027/2028',
                    'aktif' => true,
                ]
            );

            $kelompokList = $configData['kelompok'] ?? [];
            $urutanKelompok = 1;

            foreach ($kelompokList as $kelompokData) {
                $kelompok = KesepahamanKelompok::updateOrCreate(
                    ['program_id' => $program->id, 'kode' => $kelompokData['kode']],
                    [
                        'judul' => $kelompokData['judul'],
                        'urutan' => $urutanKelompok++,
                    ]
                );

                $urutanPoin = 1;
                foreach ($kelompokData['poin'] ?? [] as $poinData) {
                    KesepahamanPoin::updateOrCreate(
                        ['kode_poin' => $poinData['id']],
                        [
                            'kelompok_id' => $kelompok->id,
                            'nomor' => $poinData['nomor'],
                            'uraian' => $poinData['uraian'],
                            'urutan' => $urutanPoin++,
                        ]
                    );
                }
            }
        }
    }
}
