<?php

namespace Database\Seeders;

use App\Models\TpaPengaturan;
use App\Models\TpaSoal;
use Illuminate\Database\Seeder;

class TpaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan Pengaturan TPA Terinisialisasi
        TpaPengaturan::firstOrCreate([], [
            'durasi_literasi_menit' => 45,
            'durasi_numerasi_menit' => 45,
            'max_pelanggaran' => 3,
            'petunjuk_literasi' => 'Bacalah setiap wacana atau stimulus dengan saksama. Pilihlah satu jawaban yang paling tepat.',
            'petunjuk_numerasi' => 'Selesaikan permasalahan matematika dan logika hitung berikut secara mandiri tanpa menggunakan kalkulator.',
            'is_active' => true,
        ]);

        // 2. Sample Soal Babak 1: Literasi
        $soalLiterasi = [
            [
                'babak' => 'literasi',
                'nomor_urut' => 1,
                'pertanyaan' => "Di era revolusi industri 4.0 dan kecerdasan buatan, kemampuan literasi digital menjadi salah satu kompetensi dasar yang wajib dikuasai siswa SMK. Berdasarkan pernyataan tersebut, apa tujuan utama penguasaan literasi digital bagi peserta didik?",
                'bobot' => 2,
                'kunci_jawaban' => 'B',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Agar siswa dapat menghabiskan waktu lebih banyak di media sosial'],
                    ['key' => 'B', 'text' => 'Mampu mencari, mengevaluasi, dan memanfaatkan informasi secara kritis dan bertanggung jawab'],
                    ['key' => 'C', 'text' => 'Menghindari interaksi sosial secara langsung'],
                    ['key' => 'D', 'text' => 'Menggantikan seluruh buku fisik dengan dokumen digital'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'literasi',
                'nomor_urut' => 2,
                'pertanyaan' => "Perhatikan kutipan berikut: 'Hutan mangrove memiliki peranan ekologis yang sangat vital sebagai benteng pertahanan alami pantai dari abrasi gelombang laut dan tempat berkembang biak beragam biota laut.' Gagasan utama dari kutipan di atas adalah...",
                'bobot' => 1,
                'kunci_jawaban' => 'C',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Gelombang laut yang menyebabkan abrasi pantai'],
                    ['key' => 'B', 'text' => 'Keanekaragaman biota di pesisir pantai'],
                    ['key' => 'C', 'text' => 'Peranan ekologis hutan mangrove yang sangat vital'],
                    ['key' => 'D', 'text' => 'Pemanfaatan kayu mangrove untuk kebutuhan industri'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'literasi',
                'nomor_urut' => 3,
                'pertanyaan' => "Sinonim dari kata 'signifikan' dalam konteks perkembangan teknologi informasi adalah...",
                'bobot' => 1,
                'kunci_jawaban' => 'A',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Berarti / Berdampak penting'],
                    ['key' => 'B', 'text' => 'Sementara'],
                    ['key' => 'C', 'text' => 'Meragukan'],
                    ['key' => 'D', 'text' => 'Sederhana'],
                    ['key' => 'E', 'text' => 'Terbatas'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'literasi',
                'nomor_urut' => 4,
                'pertanyaan' => "Manakah di antara kalimat berikut yang menggunakan tanda baca dan ejaan bahasa Indonesia yang baku?",
                'bobot' => 2,
                'kunci_jawaban' => 'D',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Para siswa-siswa sedang melakukan praktek di laboratorium computer.'],
                    ['key' => 'B', 'text' => 'Ibu membeli apel, jeruk dan mangga di pasar tradisional.'],
                    ['key' => 'C', 'text' => 'Meskipun hujan lebat, tetapi para peserta didik tetap hadir tepat waktu.'],
                    ['key' => 'D', 'text' => 'Para siswa sedang melakukan praktik di laboratorium komputer.'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'literasi',
                'nomor_urut' => 5,
                'pertanyaan' => "Sebuah teks yang memuat langkah-langkah terstruktur dalam merakit perangkat komputer personal (PC) dikelompokkan ke dalam jenis teks...",
                'bobot' => 1,
                'kunci_jawaban' => 'C',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Narasi'],
                    ['key' => 'B', 'text' => 'Deskripsi'],
                    ['key' => 'C', 'text' => 'Prosedur kompleks'],
                    ['key' => 'D', 'text' => 'Eksposisi'],
                    ['key' => 'E', 'text' => 'Anekdot'],
                ],
                'is_active' => true,
            ],
        ];

        // 3. Sample Soal Babak 2: Numerasi
        $soalNumerasi = [
            [
                'babak' => 'numerasi',
                'nomor_urut' => 1,
                'pertanyaan' => "Jika sebuah toko memberikan diskon sebesar 20% untuk sebuah laptop seharga Rp 7.500.000, berapakah harga yang harus dibayar oleh pembeli setelah potongan diskon?",
                'bobot' => 1,
                'kunci_jawaban' => 'B',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Rp 5.500.000'],
                    ['key' => 'B', 'text' => 'Rp 6.000.000'],
                    ['key' => 'C', 'text' => 'Rp 6.250.000'],
                    ['key' => 'D', 'text' => 'Rp 6.500.000'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'numerasi',
                'nomor_urut' => 2,
                'pertanyaan' => "Perhatikan deret angka berikut: 3, 6, 12, 24, 48, ... Angka berikutnya dalam pola deret tersebut adalah...",
                'bobot' => 2,
                'kunci_jawaban' => 'C',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => '72'],
                    ['key' => 'B', 'text' => '84'],
                    ['key' => 'C', 'text' => '96'],
                    ['key' => 'D', 'text' => '108'],
                    ['key' => 'E', 'text' => '120'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'numerasi',
                'nomor_urut' => 3,
                'pertanyaan' => "Sebuah ruangan server memiliki panjang 8 meter dan lebar 6 meter. Jika lantai ruangan tersebut akan dipasangi keramik berukuran 40 cm x 40 cm, berapa jumlah keramik minimal yang dibutuhkan?",
                'bobot' => 2,
                'kunci_jawaban' => 'A',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => '300 buah'],
                    ['key' => 'B', 'text' => '250 buah'],
                    ['key' => 'C', 'text' => '350 buah'],
                    ['key' => 'D', 'text' => '400 buah'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'numerasi',
                'nomor_urut' => 4,
                'pertanyaan' => "Suatu pekerjaan instalasi jaringan dapat diselesaikan oleh 4 orang teknisi dalam waktu 6 hari. Jika pekerjaan tersebut harus diselesaikan dalam waktu 3 hari, berapa total teknisi yang dibutuhkan?",
                'bobot' => 2,
                'kunci_jawaban' => 'D',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => '5 orang'],
                    ['key' => 'B', 'text' => '6 orang'],
                    ['key' => 'C', 'text' => '7 orang'],
                    ['key' => 'D', 'text' => '8 orang'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'numerasi',
                'nomor_urut' => 5,
                'pertanyaan' => "Rata-rata nilai ulangan matematika dari 9 siswa adalah 75. Jika nilai seorang siswa baru digabungkan, nilai rata-ratanya menjadi 76. Berapakah nilai ulangan siswa baru tersebut?",
                'bobot' => 3,
                'kunci_jawaban' => 'C',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => '80'],
                    ['key' => 'B', 'text' => '82'],
                    ['key' => 'C', 'text' => '85'],
                    ['key' => 'D', 'text' => '88'],
                    ['key' => 'E', 'text' => '90'],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($soalLiterasi as $data) {
            TpaSoal::updateOrCreate(
                ['babak' => $data['babak'], 'nomor_urut' => $data['nomor_urut']],
                $data
            );
        }

        foreach ($soalNumerasi as $data) {
            TpaSoal::updateOrCreate(
                ['babak' => $data['babak'], 'nomor_urut' => $data['nomor_urut']],
                $data
            );
        }
    }
}
