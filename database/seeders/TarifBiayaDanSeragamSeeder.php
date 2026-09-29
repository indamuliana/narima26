<?php

namespace Database\Seeders;

use App\Models\MasterBiaya;
use App\Models\MasterGelombang;
use App\Models\MasterProgram;
use App\Models\MasterSeragam;
use Illuminate\Database\Seeder;

class TarifBiayaDanSeragamSeeder extends Seeder
{
    public function run(): void
    {
        $reguler = MasterProgram::where('kode', 'REG')->first();
        $unggulan = MasterProgram::where('kode', 'UGG')->first();

        $gel1 = MasterGelombang::where('kode', 'GEL1')->first();
        $gel2 = MasterGelombang::where('kode', 'GEL2')->first();
        $gel3 = MasterGelombang::where('kode', 'GEL3')->first();

        // Nonaktifkan data dummy daftar ulang lama
        MasterBiaya::whereIn('kode_biaya', [
            'BIAYA-DSP', 'BIAYA-SPP', 'BIAYA-SRG', 'BIAYA-ASR', 'BIAYA-KGT', 'DSP-r-G1'
        ])->update(['aktif' => false]);

        MasterSeragam::whereIn('nama_jenis', ['Seragam Batik', 'Seragam Kejuruan / Wearpack'])->update(['aktif' => false]);

        // 1. DSP (Dana Sumbangan Pendidikan)
        $dspData = [
            // Gelombang 1
            [
                'kode_biaya' => 'DSP-G1-REG',
                'nama_biaya' => 'DSP Reguler Gelombang 1',
                'kategori' => 'DSP',
                'program_id' => $reguler?->id,
                'gelombang_id' => $gel1?->id,
                'jenis_kelamin' => null,
                'nominal' => 3000000,
                'tipe_nominal' => 'tetap',
                'wajib' => true,
                'aktif' => true,
                'keterangan' => 'Dana Sumbangan Pendidikan - Program Reguler Gelombang 1 (Dapat dicicil)',
            ],
            [
                'kode_biaya' => 'DSP-G1-UGG',
                'nama_biaya' => 'DSP Unggulan Gelombang 1',
                'kategori' => 'DSP',
                'program_id' => $unggulan?->id,
                'gelombang_id' => $gel1?->id,
                'jenis_kelamin' => null,
                'nominal' => 4500000,
                'tipe_nominal' => 'tetap',
                'wajib' => true,
                'aktif' => true,
                'keterangan' => 'Dana Sumbangan Pendidikan - Program Unggulan Gelombang 1 (Dapat dicicil)',
            ],
            // Gelombang 2
            [
                'kode_biaya' => 'DSP-G2-REG',
                'nama_biaya' => 'DSP Reguler Gelombang 2',
                'kategori' => 'DSP',
                'program_id' => $reguler?->id,
                'gelombang_id' => $gel2?->id,
                'jenis_kelamin' => null,
                'nominal' => 4000000,
                'tipe_nominal' => 'tetap',
                'wajib' => true,
                'aktif' => true,
                'keterangan' => 'Dana Sumbangan Pendidikan - Program Reguler Gelombang 2 (Dapat dicicil)',
            ],
            [
                'kode_biaya' => 'DSP-G2-UGG',
                'nama_biaya' => 'DSP Unggulan Gelombang 2',
                'kategori' => 'DSP',
                'program_id' => $unggulan?->id,
                'gelombang_id' => $gel2?->id,
                'jenis_kelamin' => null,
                'nominal' => 5500000,
                'tipe_nominal' => 'tetap',
                'wajib' => true,
                'aktif' => true,
                'keterangan' => 'Dana Sumbangan Pendidikan - Program Unggulan Gelombang 2 (Dapat dicicil)',
            ],
            // Gelombang 3
            [
                'kode_biaya' => 'DSP-G3-REG',
                'nama_biaya' => 'DSP Reguler Gelombang 3',
                'kategori' => 'DSP',
                'program_id' => $reguler?->id,
                'gelombang_id' => $gel3?->id,
                'jenis_kelamin' => null,
                'nominal' => 5000000,
                'tipe_nominal' => 'tetap',
                'wajib' => true,
                'aktif' => true,
                'keterangan' => 'Dana Sumbangan Pendidikan - Program Reguler Gelombang 3 (Dapat dicicil)',
            ],
            [
                'kode_biaya' => 'DSP-G3-UGG',
                'nama_biaya' => 'DSP Unggulan Gelombang 3',
                'kategori' => 'DSP',
                'program_id' => $unggulan?->id,
                'gelombang_id' => $gel3?->id,
                'jenis_kelamin' => null,
                'nominal' => 6500000,
                'tipe_nominal' => 'tetap',
                'wajib' => true,
                'aktif' => true,
                'keterangan' => 'Dana Sumbangan Pendidikan - Program Unggulan Gelombang 3 (Dapat dicicil)',
            ],
        ];

        foreach ($dspData as $data) {
            MasterBiaya::updateOrCreate(['kode_biaya' => $data['kode_biaya']], $data);
        }

        // 2. SPP Bulan ke-1 (Hanya berbeda antar program)
        $sppData = [
            [
                'kode_biaya' => 'SPP-REG',
                'nama_biaya' => 'SPP Bulan ke-1 (Reguler)',
                'kategori' => 'SPP',
                'program_id' => $reguler?->id,
                'gelombang_id' => null,
                'jenis_kelamin' => null,
                'nominal' => 450000,
                'tipe_nominal' => 'tetap',
                'wajib' => true,
                'aktif' => true,
                'keterangan' => 'Iuran SPP bulan pertama - Program Reguler',
            ],
            [
                'kode_biaya' => 'SPP-UGG',
                'nama_biaya' => 'SPP Bulan ke-1 (Unggulan)',
                'kategori' => 'SPP',
                'program_id' => $unggulan?->id,
                'gelombang_id' => null,
                'jenis_kelamin' => null,
                'nominal' => 650000,
                'tipe_nominal' => 'tetap',
                'wajib' => true,
                'aktif' => true,
                'keterangan' => 'Iuran SPP bulan pertama - Program Unggulan',
            ],
        ];

        foreach ($sppData as $data) {
            MasterBiaya::updateOrCreate(['kode_biaya' => $data['kode_biaya']], $data);
        }

        // 2b. Biaya Asrama (Wajib untuk Program Unggulan, sama untuk setiap gelombang)
        MasterBiaya::updateOrCreate(
            ['kode_biaya' => 'ASR-UGG'],
            [
                'nama_biaya' => 'Biaya Asrama',
                'kategori' => 'ASRAMA',
                'program_id' => $unggulan?->id,
                'gelombang_id' => null,
                'jenis_kelamin' => null,
                'nominal' => 1500000,
                'tipe_nominal' => 'tetap',
                'wajib' => true,
                'aktif' => true,
                'keterangan' => 'Biaya Asrama bagi calon siswa program Unggulan (wajib, sama untuk setiap gelombang)',
            ]
        );

        // 3. Seragam Wajib & Opsional (8 Item Sesuai Revisi)
        // Klaster 1 prioritas:
        // - Jas Almamater 150.000
        // - 1 stel seragam Olah Raga 200.000
        // - Atribut sekolah 50.000
        //
        // Klaster 2:
        // - Celana/Rok Hijau 150.000
        // - 1 stel seragam muslim 250.000
        //
        // Klaster 3 (pilihan : Pesan sekarang, Pesan nanti, Tidak Pesan):
        // - 1 stel seragam pramuka 200.000
        // - Kemeja putih 100.000
        // - Sepatu pantovel 210.000 (laki laki dan perempuan sama)

        MasterBiaya::where('kategori', 'SERAGAM')->update(['aktif' => false]);
        MasterSeragam::query()->update(['aktif' => false]);

        $seragamItems = [
            // Klaster 1
            [
                'kode_biaya' => 'SRG-JAS',
                'nama' => 'Jas Almamater',
                'nominal' => 150000,
                'klaster' => 'MPLS',
                'urutan_klaster' => 1,
                'wajib' => true,
                'jenis_kelamin' => null,
                'ukuran' => ['S', 'M', 'L', 'XL', 'XXL'],
            ],
            [
                'kode_biaya' => 'SRG-OLR',
                'nama' => '1 stel seragam Olah Raga',
                'nominal' => 200000,
                'klaster' => 'MPLS',
                'urutan_klaster' => 1,
                'wajib' => true,
                'jenis_kelamin' => null,
                'ukuran' => ['S', 'M', 'L', 'XL', 'XXL'],
            ],
            [
                'kode_biaya' => 'SRG-ATR',
                'nama' => 'Atribut sekolah',
                'nominal' => 50000,
                'klaster' => 'MPLS',
                'urutan_klaster' => 1,
                'wajib' => true,
                'jenis_kelamin' => null,
                'ukuran' => ['All Size'],
            ],

            // Klaster 2
            [
                'kode_biaya' => 'SRG-CL-HJU',
                'nama' => 'Celana/Rok Hijau',
                'nominal' => 150000,
                'klaster' => 'KBM',
                'urutan_klaster' => 2,
                'wajib' => true,
                'jenis_kelamin' => null,
                'ukuran' => ['S', 'M', 'L', 'XL', 'XXL'],
            ],
            [
                'kode_biaya' => 'SRG-MSL',
                'nama' => '1 stel seragam muslim',
                'nominal' => 250000,
                'klaster' => 'KBM',
                'urutan_klaster' => 2,
                'wajib' => true,
                'jenis_kelamin' => null,
                'ukuran' => ['S', 'M', 'L', 'XL', 'XXL'],
            ],

            // Klaster 3
            [
                'kode_biaya' => 'SRG-PRM',
                'nama' => '1 stel seragam pramuka',
                'nominal' => 200000,
                'klaster' => 'OPSIONAL',
                'urutan_klaster' => 3,
                'wajib' => false,
                'jenis_kelamin' => null,
                'ukuran' => ['S', 'M', 'L', 'XL', 'XXL'],
            ],
            [
                'kode_biaya' => 'SRG-KM-PTH',
                'nama' => 'Kemeja putih',
                'nominal' => 100000,
                'klaster' => 'OPSIONAL',
                'urutan_klaster' => 3,
                'wajib' => false,
                'jenis_kelamin' => null,
                'ukuran' => ['S', 'M', 'L', 'XL', 'XXL'],
            ],
            [
                'kode_biaya' => 'SRG-SPT',
                'nama' => 'Sepatu pantovel',
                'nominal' => 210000,
                'klaster' => 'OPSIONAL',
                'urutan_klaster' => 3,
                'wajib' => false,
                'jenis_kelamin' => null,
                'ukuran' => ['36', '37', '38', '39', '40', '41', '42', '43', '44'],
            ],
        ];

        foreach ($seragamItems as $item) {
            MasterBiaya::updateOrCreate(
                ['kode_biaya' => $item['kode_biaya']],
                [
                    'nama_biaya' => $item['nama'],
                    'kategori' => 'SERAGAM',
                    'program_id' => null,
                    'gelombang_id' => null,
                    'jenis_kelamin' => $item['jenis_kelamin'],
                    'nominal' => $item['nominal'],
                    'tipe_nominal' => 'tetap',
                    'wajib' => $item['wajib'],
                    'aktif' => true,
                    'keterangan' => 'Seragam sekolah - ' . $item['nama'],
                ]
            );

            foreach ($item['ukuran'] as $uk) {
                $kode = $item['kode_biaya'] . '-' . $uk;

                MasterSeragam::updateOrCreate(
                    ['kode' => $kode],
                    [
                        'nama_jenis' => $item['nama'],
                        'ukuran' => $uk,
                        'wajib' => $item['wajib'],
                        'klaster' => $item['klaster'],
                        'urutan_klaster' => $item['urutan_klaster'],
                        'jenis_kelamin' => $item['jenis_kelamin'],
                        'aktif' => true,
                    ]
                );
            }
        }
    }
}
