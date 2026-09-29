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
            'BELUM/TIDAK BEKERJA',
            'MENGURUS RUMAH TANGGA',
            'PELAJAR/MAHASISWA',
            'PENSIUNAN',
            'PEGAWAI NEGERI SIPIL (PNS)',
            'TENTARA NASIONAL INDONESIA (TNI)',
            'KEPOLISIAN RI (POLRI)',
            'PERDAGANGAN',
            'PETANI/PERKEBUNAN',
            'PETERNAK',
            'NELAYAN/PERIKANAN',
            'INDUSTRI',
            'KONSTRUKSI',
            'TRANSPORTASI',
            'KARYAWAN SWASTA',
            'KARYAWAN BUMN',
            'KARYAWAN BUMD',
            'KARYAWAN HONORER',
            'BURUH HARIAN LEPAS',
            'BURUH TANI/PERKEBUNAN',
            'BURUH NELAYAN/PERIKANAN',
            'BURUH PETERNAKAN',
            'PEMBANTU RUMAH TANGGA',
            'TUKANG CUKUR',
            'TUKANG LISTRIK',
            'TUKANG BATU',
            'TUKANG KAYU',
            'TUKANG SOL SEPATU',
            'TUKANG LAS/PANDAI BESI',
            'TUKANG JAHIT',
            'TUKANG GIGI',
            'PENATA RIAS',
            'PENATA BUSANA',
            'PENATA RAMBUT',
            'MEKANIK',
            'SENIMAN',
            'TABIB',
            'PARAJI',
            'PERANCANG BUSANA',
            'PENTERJEMAH',
            'IMAM MASJID',
            'PENDETA',
            'PASTOR',
            'WARTAWAN',
            'USTADZ/MUBALIGH',
            'JURU MASAK',
            'PROMOTOR ACARA',
            'ANGGOTA DPR-RI',
            'ANGGOTA DPD',
            'ANGGOTA BPK',
            'PRESIDEN',
            'WAKIL PRESIDEN',
            'ANGGOTA MAHKAMAH KONSTITUSI',
            'ANGGOTA KABINET KEMENTERIAN',
            'DUTA BESAR',
            'GUBERNUR',
            'WAKIL GUBERNUR',
            'BUPATI',
            'WAKIL BUPATI',
            'WALIKOTA',
            'WAKIL WALIKOTA',
            'ANGGOTA DPRD PROVINSI',
            'ANGGOTA DPRD KABUPATEN/KOTA',
            'DOSEN',
            'GURU',
            'PILOT',
            'PENGACARA',
            'NOTARIS',
            'ARSITEK',
            'AKUNTAN',
            'KONSULTAN',
            'DOKTER',
            'BIDAN',
            'PERAWAT',
            'APOTEKER',
            'PSIKIATER/PSIKOLOG',
            'PENYIAR TELEVISI',
            'PENYIAR RADIO',
            'PELAUT',
            'PENELITI',
            'SOPIR',
            'PIALANG',
            'PARANORMAL',
            'PEDAGANG',
            'PERANGKAT DESA',
            'KEPALA DESA',
            'BIARAWATI',
            'WIRASWASTA',
            'LAINNYA',
        ];
        foreach ($pekerjaan as $pek) {
            MasterPekerjaan::firstOrCreate(['nama' => $pek], ['aktif' => true]);
        }


        // 6. Master Sekolah Asal
        $sekolah = [
            [
                'id' => 1,
                'npsn' => '70055392',
                'nama_sekolah' => 'Sekolah Rakyat Menengah Pertama 10 Bogor',
                'status' => 'Negeri',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 2,
                'npsn' => '20254243',
                'nama_sekolah' => 'SMP N 4 CIBINONG',
                'status' => 'Negeri',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 3,
                'npsn' => '20200611',
                'nama_sekolah' => 'SMP NEGERI 1 CIBINONG',
                'status' => 'Negeri',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 4,
                'npsn' => '20200627',
                'nama_sekolah' => 'SMP NEGERI 2 CIBINONG',
                'status' => 'Negeri',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 5,
                'npsn' => '20200649',
                'nama_sekolah' => 'SMP NEGERI 3 CIBINONG',
                'status' => 'Negeri',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 6,
                'npsn' => '20255885',
                'nama_sekolah' => 'SMP AL AZHAR SYIFA BUDI CIBINONG',
                'status' => 'Swasta',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 7,
                'npsn' => '20230936',
                'nama_sekolah' => 'SMP AL KHOER',
                'status' => 'Swasta',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 8,
                'npsn' => '20200624',
                'nama_sekolah' => 'SMP AL MIZAN',
                'status' => 'Swasta',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 9,
                'npsn' => '20200614',
                'nama_sekolah' => 'SMP AL NUR',
                'status' => 'Swasta',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 10,
                'npsn' => '69982632',
                'nama_sekolah' => 'SMP AL QURAN WAHDAH ISLAMIYAH CIBINONG-BOGOR',
                'status' => 'Swasta',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
        ];
        foreach ($sekolah as $sek) {
            MasterSekolahAsal::updateOrCreate(
                ['id' => $sek['id']],
                $sek
            );
        }

        // 7. Master Biaya (Dummy yang dapat diedit Admin)
        $biaya = [
            [
                'kode_biaya' => 'BIAYA-SEL',
                'nama_biaya' => 'Biaya Pendaftaran & Seleksi Masuk',
                'kategori' => 'seleksi',
                'nominal' => 200000,
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

        // 9. Master Wilayah (38 Provinsi, 27 Kab/Kota Jawa Barat, & Kab. Garut)
        $this->call(WilayahIndonesiaSeeder::class);

        $this->call(TarifBiayaDanSeragamSeeder::class);
    }
}
