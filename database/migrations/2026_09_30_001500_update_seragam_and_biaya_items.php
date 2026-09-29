<?php

use App\Models\MasterBiaya;
use App\Models\MasterSeragam;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Nonaktifkan item seragam lama pada MasterBiaya & MasterSeragam
        MasterBiaya::where('kategori', 'SERAGAM')->update(['aktif' => false]);
        MasterSeragam::query()->update(['aktif' => false]);

        // 2. Daftar 8 Seragam Baru
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

        $items = [
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

        foreach ($items as $item) {
            // MasterBiaya
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

            // MasterSeragam untuk setiap ukuran
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert could reactivate previous items if needed
    }
};
