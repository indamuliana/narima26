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

        // 3. Seragam Wajib (8 Item)
        $seragamWajib = [
            ['kode_biaya' => 'SRG-JAS', 'nama_biaya' => 'Jas Almamater', 'nominal' => 135000],
            ['kode_biaya' => 'SRG-OLR', 'nama_biaya' => 'Baju Olahraga', 'nominal' => 150000],
            ['kode_biaya' => 'SRG-CL-HJU', 'nama_biaya' => 'Celana/Rok Panjang Hijau', 'nominal' => 140000],
            ['kode_biaya' => 'SRG-CL-MSL', 'nama_biaya' => 'Celana/Rok Panjang Muslim', 'nominal' => 105000],
            ['kode_biaya' => 'SRG-BJ-MSL', 'nama_biaya' => 'Baju Muslim', 'nominal' => 105000],
            ['kode_biaya' => 'SRG-CL-PRM', 'nama_biaya' => 'Celana/Rok Pramuka', 'nominal' => 110000],
            ['kode_biaya' => 'SRG-BJ-PRM', 'nama_biaya' => 'Baju Pramuka', 'nominal' => 110000],
            ['kode_biaya' => 'SRG-ATR', 'nama_biaya' => 'Atribut Sekolah', 'nominal' => 15000],
        ];

        foreach ($seragamWajib as $srg) {
            MasterBiaya::updateOrCreate(
                ['kode_biaya' => $srg['kode_biaya']],
                [
                    'nama_biaya' => $srg['nama_biaya'],
                    'kategori' => 'SERAGAM',
                    'program_id' => null,
                    'gelombang_id' => null,
                    'jenis_kelamin' => null,
                    'nominal' => $srg['nominal'],
                    'tipe_nominal' => 'tetap',
                    'wajib' => true,
                    'aktif' => true,
                    'keterangan' => 'Komponen paket seragam wajib siswa baru',
                ]
            );
        }

        // 4. Seragam Tidak Wajib / Boleh Beli di Luar
        $seragamTidakWajib = [
            [
                'kode_biaya' => 'SRG-CL-HTM',
                'nama_biaya' => 'Celana/Rok Hitam',
                'nominal' => 105000,
                'jenis_kelamin' => null,
                'keterangan' => 'Opsional: boleh beli di sekolah atau beli di luar',
            ],
            [
                'kode_biaya' => 'SRG-KM-PTH',
                'nama_biaya' => 'Kemeja Putih',
                'nominal' => 95000,
                'jenis_kelamin' => null,
                'keterangan' => 'Opsional: boleh beli di sekolah atau beli di luar',
            ],
            [
                'kode_biaya' => 'SRG-SPT-L',
                'nama_biaya' => 'Sepatu Pantofel (Laki-laki)',
                'nominal' => 220000,
                'jenis_kelamin' => 'L',
                'keterangan' => 'Opsional: Sepatu pantofel hitam putra standar sekolah (boleh beli di luar)',
            ],
            [
                'kode_biaya' => 'SRG-SPT-P',
                'nama_biaya' => 'Sepatu Pantofel (Perempuan)',
                'nominal' => 110000,
                'jenis_kelamin' => 'P',
                'keterangan' => 'Opsional: Sepatu pantofel hitam putri standar sekolah (boleh beli di luar)',
            ],
        ];

        foreach ($seragamTidakWajib as $srg) {
            MasterBiaya::updateOrCreate(
                ['kode_biaya' => $srg['kode_biaya']],
                [
                    'nama_biaya' => $srg['nama_biaya'],
                    'kategori' => 'SERAGAM',
                    'program_id' => null,
                    'gelombang_id' => null,
                    'jenis_kelamin' => $srg['jenis_kelamin'],
                    'nominal' => $srg['nominal'],
                    'tipe_nominal' => 'tetap',
                    'wajib' => false,
                    'aktif' => true,
                    'keterangan' => $srg['keterangan'],
                ]
            );
        }

        // 5. Update Master Seragam (master_seragam untuk ukuran formulir siswa)
        $ukuranStandar = ['S', 'M', 'L', 'XL', 'XXL'];

        // Daftar jenis seragam form
        $formSeragamWajib = [
            'Jas Almamater',
            'Baju Olahraga',
            'Celana/Rok Panjang Hijau',
            'Celana/Rok Panjang Muslim',
            'Baju Muslim',
            'Celana/Rok Pramuka',
            'Baju Pramuka',
        ];

        foreach ($formSeragamWajib as $namaJenis) {
            $klaster = in_array($namaJenis, ['Jas Almamater', 'Baju Olahraga']) ? 'MPLS' : 'KBM';
            $urutanKlaster = ($klaster === 'MPLS') ? 1 : 2;

            foreach ($ukuranStandar as $uk) {
                $kode = strtoupper(substr(str_replace([' ', '/', '-'], '', $namaJenis), 0, 4)) . '-' . $uk;
                MasterSeragam::updateOrCreate(
                    ['kode' => $kode],
                    [
                        'nama_jenis' => $namaJenis,
                        'ukuran' => $uk,
                        'wajib' => true,
                        'klaster' => $klaster,
                        'urutan_klaster' => $urutanKlaster,
                        'jenis_kelamin' => null,
                        'aktif' => true,
                    ]
                );
            }
        }

        // Atribut Sekolah (All Size)
        MasterSeragam::updateOrCreate(
            ['kode' => 'ATRIB-ALL'],
            [
                'nama_jenis' => 'Atribut Sekolah',
                'ukuran' => 'All Size',
                'wajib' => true,
                'klaster' => 'MPLS',
                'urutan_klaster' => 1,
                'jenis_kelamin' => null,
                'aktif' => true,
            ]
        );

        // Seragam Tidak Wajib
        foreach (['Celana/Rok Hitam', 'Kemeja Putih'] as $namaJenis) {
            foreach ($ukuranStandar as $uk) {
                $kode = strtoupper(substr(str_replace([' ', '/', '-'], '', $namaJenis), 0, 4)) . '-' . $uk;
                MasterSeragam::updateOrCreate(
                    ['kode' => $kode],
                    [
                        'nama_jenis' => $namaJenis,
                        'ukuran' => $uk,
                        'wajib' => false,
                        'klaster' => 'OPSIONAL',
                        'urutan_klaster' => 3,
                        'jenis_kelamin' => null,
                        'aktif' => true,
                    ]
                );
            }
        }

        // Sepatu Pantofel Laki-laki (Ukuran sepatu 38-44)
        foreach (['38', '39', '40', '41', '42', '43', '44'] as $uk) {
            MasterSeragam::updateOrCreate(
                ['kode' => 'SPT-L-' . $uk],
                [
                    'nama_jenis' => 'Sepatu Pantofel (Laki-laki)',
                    'ukuran' => $uk,
                    'wajib' => false,
                    'klaster' => 'OPSIONAL',
                    'urutan_klaster' => 3,
                    'jenis_kelamin' => 'L',
                    'aktif' => true,
                ]
            );
        }

        // Sepatu Pantofel Perempuan (Ukuran sepatu 36-41)
        foreach (['36', '37', '38', '39', '40', '41'] as $uk) {
            MasterSeragam::updateOrCreate(
                ['kode' => 'SPT-P-' . $uk],
                [
                    'nama_jenis' => 'Sepatu Pantofel (Perempuan)',
                    'ukuran' => $uk,
                    'wajib' => false,
                    'klaster' => 'OPSIONAL',
                    'urutan_klaster' => 3,
                    'jenis_kelamin' => 'P',
                    'aktif' => true,
                ]
            );
        }
    }
}
