<?php

namespace Database\Seeders;

use App\Models\MasterBiaya;
use App\Models\MasterDesa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterKriteriaWawancara;
use App\Models\MasterPekerjaan;
use App\Models\MasterProgram;
use App\Models\MasterProvinsi;
use App\Models\MasterSekolahAsal;
use App\Models\MasterSeragam;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Master Program
        $programs = [
            [
                'kode' => 'REG',
                'nama' => 'Reguler',
                'keterangan' => 'Kurikulum vokasi standar industri dengan pembelajaran teori dan praktik seimbang.',
                'aktif' => true,
            ],
            [
                'kode' => 'UGG',
                'nama' => 'Unggulan',
                'keterangan' => 'Pendalaman intensif kejuruan, Hafalan Qur\'an, Kemandirian dan pembinaan Akhlak dan Adab.',
                'aktif' => true,
            ],
        ];
        foreach ($programs as $prog) {
            MasterProgram::updateOrCreate(['kode' => $prog['kode']], $prog);
        }

        // 2. Master Jurusan
        $jurusan = [
            [
                'kode' => 'TJKT',
                'nama' => 'Teknik Jaringan Komputer dan Telekomunikasi',
                'keterangan' => 'Keahlian infrastruktur jaringan, server, cyber security, dan cloud administration.',
                'aktif' => true,
            ],
            [
                'kode' => 'PPLG',
                'nama' => 'Pengembangan Perangkat Lunak dan Gim',
                'keterangan' => 'Fokus pembuatan web modern, mobile apps, database backend, dan game logic.',
                'aktif' => true,
            ],
            [
                'kode' => 'PEMASARAN',
                'nama' => 'Pemasaran',
                'keterangan' => 'Keahlian digital marketing, social media marketing, content creation, dan e-commerce.',
                'aktif' => true,
            ],
            [
                'kode' => 'PERHOTELAN',
                'nama' => 'Perhotelan',
                'keterangan' => 'Layanan front office, food & beverage, tata graha, dan manajemen perhotelan.',
                'aktif' => true,
            ],
        ];
        foreach ($jurusan as $jur) {
            MasterJurusan::updateOrCreate(['kode' => $jur['kode']], $jur);
        }

        // 3. Master Gelombang
        $gelombang = [
            [
                'kode' => 'GEL1',
                'nama' => 'Gelombang 1',
                'periode_mulai' => '2026-01-01',
                'periode_selesai' => '2026-03-31',
                'aktif' => true,
            ],
            [
                'kode' => 'GEL2',
                'nama' => 'Gelombang 2',
                'periode_mulai' => '2026-04-01',
                'periode_selesai' => '2026-06-30',
                'aktif' => false,
            ],
            [
                'kode' => 'GEL3',
                'nama' => 'Gelombang 3',
                'periode_mulai' => '2026-07-01',
                'periode_selesai' => '2026-08-31',
                'aktif' => false,
            ],
        ];
        foreach ($gelombang as $gel) {
            MasterGelombang::updateOrCreate(['kode' => $gel['kode']], $gel);
        }

        // 4. Master Pekerjaan
        $pekerjaan = [
            'PNS',
            'TNI/Polri',
            'Karyawan Swasta',
            'Wiraswasta',
            'Petani',
            'Buruh',
            'Guru',
            'Pedagang',
            'Tidak Bekerja',
            'Lainnya',
        ];
        foreach ($pekerjaan as $pek) {
            MasterPekerjaan::firstOrCreate(['nama' => $pek], ['aktif' => true]);
        }

        // 5. Master Seragam
        $seragamJenis = [
            'Baju Olahraga',
            'Seragam Batik',
            'Seragam Kejuruan / Wearpack',
            'Jas Almamater',
        ];
        $ukuranList = ['S', 'M', 'L', 'XL', 'XXL'];

        foreach ($seragamJenis as $jenis) {
            foreach ($ukuranList as $uk) {
                $kode = strtoupper(substr(str_replace(' ', '', $jenis), 0, 3)) . '-' . $uk;
                MasterSeragam::updateOrCreate(['kode' => $kode], [
                    'nama_jenis' => $jenis,
                    'ukuran' => $uk,
                    'aktif' => true,
                ]);
            }
        }

        // 6. Master Sekolah Asal
        $sekolah = [
            [
                'npsn' => '20225901',
                'nama_sekolah' => 'SMP Negeri 1 Garut',
                'alamat' => 'Jl. Ahmad Yani No. 43',
                'kecamatan' => 'Garut Kota',
                'kabupaten_kota' => 'Kabupaten Garut',
                'aktif' => true,
            ],
            [
                'npsn' => '20225902',
                'nama_sekolah' => 'SMP Negeri 2 Garut',
                'alamat' => 'Jl. Pasundan No. 34',
                'kecamatan' => 'Garut Kota',
                'kabupaten_kota' => 'Kabupaten Garut',
                'aktif' => true,
            ],
            [
                'npsn' => '20225903',
                'nama_sekolah' => 'SMP Negeri 1 Tarogong Kidul',
                'alamat' => 'Jl. Pembangunan No. 12',
                'kecamatan' => 'Tarogong Kidul',
                'kabupaten_kota' => 'Kabupaten Garut',
                'aktif' => true,
            ],
            [
                'npsn' => '20277801',
                'nama_sekolah' => 'MTs Negeri 1 Garut',
                'alamat' => 'Jl. Suherman No. 20',
                'kecamatan' => 'Tarogong Kaler',
                'kabupaten_kota' => 'Kabupaten Garut',
                'aktif' => true,
            ],
            [
                'npsn' => null,
                'nama_sekolah' => 'Lainnya',
                'alamat' => null,
                'kecamatan' => null,
                'kabupaten_kota' => null,
                'aktif' => true,
            ],
        ];
        foreach ($sekolah as $sek) {
            MasterSekolahAsal::updateOrCreate(
                ['nama_sekolah' => $sek['nama_sekolah']],
                $sek
            );
        }

        // 7. Master Biaya (Dummy yang dapat diedit Admin)
        $biaya = [
            [
                'kode_biaya' => 'BIAYA-SEL',
                'nama_biaya' => 'Biaya Pendaftaran & Seleksi Masuk',
                'kategori' => 'seleksi',
                'nominal' => 250000,
                'tipe_nominal' => 'tetap',
                'wajib' => true,
                'aktif' => true,
                'keterangan' => 'Biaya administrasi seleksi dan tes wawancara',
            ],
            [
                'kode_biaya' => 'BIAYA-DSP',
                'nama_biaya' => 'Dana Sarana Prasarana (DSP)',
                'kategori' => 'daftar_ulang',
                'nominal' => 3000000,
                'tipe_nominal' => 'tetap',
                'wajib' => true,
                'aktif' => true,
                'keterangan' => 'Pembangunan sarana dan fasilitas pembelajaran laboratorium',
            ],
            [
                'kode_biaya' => 'BIAYA-SPP',
                'nama_biaya' => 'SPP Bulanan (Bulan Pertama)',
                'kategori' => 'daftar_ulang',
                'nominal' => 450000,
                'tipe_nominal' => 'tetap',
                'wajib' => true,
                'aktif' => true,
                'keterangan' => 'Iuran penyelenggaraan pendidikan bulan pertama',
            ],
            [
                'kode_biaya' => 'BIAYA-SRG',
                'nama_biaya' => 'Paket Seragam & Atribut Lengkap',
                'kategori' => 'daftar_ulang',
                'nominal' => 1000000,
                'tipe_nominal' => 'tetap',
                'wajib' => true,
                'aktif' => true,
                'keterangan' => 'Seragam batik, olahraga, kejuruan, dan almamater',
            ],
            [
                'kode_biaya' => 'BIAYA-ASR',
                'nama_biaya' => 'Biaya Pondok / Asrama (Opsional)',
                'kategori' => 'daftar_ulang',
                'nominal' => 2000000,
                'tipe_nominal' => 'tetap',
                'wajib' => false,
                'aktif' => true,
                'keterangan' => 'Bagi peserta didik yang memilih opsi asrama/pondok',
            ],
            [
                'kode_biaya' => 'BIAYA-KGT',
                'nama_biaya' => 'Kegiatan Masa Pengenalan Lingkungan Sekolah (MPLS)',
                'kategori' => 'daftar_ulang',
                'nominal' => 500000,
                'tipe_nominal' => 'tetap',
                'wajib' => true,
                'aktif' => true,
                'keterangan' => 'Biaya materi dan perlengkapan pengenalan sekolah',
            ],
        ];
        foreach ($biaya as $b) {
            MasterBiaya::updateOrCreate(['kode_biaya' => $b['kode_biaya']], $b);
        }

        // 8. Master Kriteria Wawancara
        $kriteria = [
            ['kode' => 'KRP', 'nama_kriteria' => 'Kerapihan dan Penampilan', 'jenis_penilaian' => 'siswa', 'urutan' => 1],
            ['kode' => 'SKP', 'nama_kriteria' => 'Sikap, Akhlak, dan Adab', 'jenis_penilaian' => 'siswa', 'urutan' => 2],
            ['kode' => 'KOM', 'nama_kriteria' => 'Komunikasi dan Artikulasi', 'jenis_penilaian' => 'siswa', 'urutan' => 3],
            ['kode' => 'DIS', 'nama_kriteria' => 'Kedisiplinan dan Komitmen Belajar', 'jenis_penilaian' => 'siswa', 'urutan' => 4],
            ['kode' => 'MOT', 'nama_kriteria' => 'Motivasi dan Minat Bidang Kejuruan', 'jenis_penilaian' => 'siswa', 'urutan' => 5],
            ['kode' => 'KSP', 'nama_kriteria' => 'Kesiapan Mengikuti Program', 'jenis_penilaian' => 'siswa', 'urutan' => 6],
            ['kode' => 'PPH', 'nama_kriteria' => 'Pemahaman Pilihan Jurusan', 'jenis_penilaian' => 'siswa', 'urutan' => 7],
            ['kode' => 'DUK', 'nama_kriteria' => 'Dukungan & Perhatian Orang Tua', 'jenis_penilaian' => 'orang_tua', 'urutan' => 8],
            ['kode' => 'FIN', 'nama_kriteria' => 'Komitmen Finansial & Pembiayaan', 'jenis_penilaian' => 'orang_tua', 'urutan' => 9],
        ];
        foreach ($kriteria as $k) {
            MasterKriteriaWawancara::updateOrCreate(['kode' => $k['kode']], array_merge($k, ['aktif' => true]));
        }

        // 9. Master Wilayah (Jawa Barat & Kab. Garut)
        $prov = MasterProvinsi::firstOrCreate(
            ['kode' => '32'],
            ['nama' => 'Jawa Barat']
        );

        $kabGarut = MasterKabupaten::firstOrCreate(
            ['kode' => '3205'],
            ['provinsi_id' => $prov->id, 'nama' => 'Kabupaten Garut']
        );

        $kabBdg = MasterKabupaten::firstOrCreate(
            ['kode' => '3273'],
            ['provinsi_id' => $prov->id, 'nama' => 'Kota Bandung']
        );

        $kecTarkid = MasterKecamatan::firstOrCreate(
            ['kode' => '320501'],
            ['kabupaten_id' => $kabGarut->id, 'nama' => 'Tarogong Kidul']
        );

        $kecTarkal = MasterKecamatan::firstOrCreate(
            ['kode' => '320502'],
            ['kabupaten_id' => $kabGarut->id, 'nama' => 'Tarogong Kaler']
        );

        $kecGarkot = MasterKecamatan::firstOrCreate(
            ['kode' => '320503'],
            ['kabupaten_id' => $kabGarut->id, 'nama' => 'Garut Kota']
        );

        // Desa
        MasterDesa::firstOrCreate(
            ['kode' => '320501001'],
            ['kecamatan_id' => $kecTarkid->id, 'nama' => 'Sukagalih', 'kode_pos' => '44151']
        );
        MasterDesa::firstOrCreate(
            ['kode' => '320501002'],
            ['kecamatan_id' => $kecTarkid->id, 'nama' => 'Patallassang', 'kode_pos' => '44151']
        );
        MasterDesa::firstOrCreate(
            ['kode' => '320503001'],
            ['kecamatan_id' => $kecGarkot->id, 'nama' => 'Kota Kulon', 'kode_pos' => '44111']
        );
    }
}
