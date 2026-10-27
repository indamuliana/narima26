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

        // 2. Soal Babak 1: Literasi
        $soalLiterasi = [
            [
                'babak' => 'literasi',
                'nomor_urut' => 1,
                'pertanyaan' => "Bacalah teks berikut!\nFenomena Urban Heat Island (Pulau Bahang Perkotaan) menyebabkan suhu di wilayah perkotaan lebih tinggi 2°C hingga 5°C dibandingkan wilayah pedesaan di sekitarnya. Hal ini dipicu oleh dominasi material konstruksi seperti aspal dan beton yang menyerap serta menahan panas matahari lebih lama. Selain itu, minimnya ruang terbuka hijau mengurangi proses evapotranspirasi tanaman yang berfungsi sebagai pendingin alami udara. Penggunaan pendingin ruangan (AC) secara masif di gedung-gedung perkotaan turut memperparah kondisi ini karena membuang udara panas ke lingkungan luar.\n\nBerdasarkan teks tersebut, alasan utama berkurangnya proses pendinginan alami di wilayah perkotaan adalah ....",
                'bobot' => 8,
                'kunci_jawaban' => 'D',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Penggunaan pendingin ruangan (AC) yang membuang udara panas ke luar gedung'],
                    ['key' => 'B', 'text' => 'Kenaikan suhu udara yang mencapai 2°C hingga 5°C setiap tahunnya'],
                    ['key' => 'C', 'text' => 'Dominasi material aspal dan beton yang memantulkan cahaya matahari'],
                    ['key' => 'D', 'text' => 'Minimnya ruang terbuka hijau yang menghambat proses evapotranspirasi tanaman'],
                    ['key' => 'E', 'text' => 'Kepadatan penduduk yang meningkatkan aktivitas transportasi di jalan raya'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'literasi',
                'nomor_urut' => 2,
                'pertanyaan' => "Perhatikan pernyataan berikut!\nSemua siswa yang lolos seleksi klub robotika menguasai dasar pemrograman logika. Sebagian siswa yang menguasai dasar pemrograman logika juga mahir merakit rangkaian elektronik. Raka adalah siswa yang lolos seleksi klub robotika.\n\nSimpulan yang paling tepat berdasarkan pernyataan di atas adalah ....",
                'bobot' => 8,
                'kunci_jawaban' => 'B',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Raka mahir merakit rangkaian elektronik dan menguasai pemrograman logika'],
                    ['key' => 'B', 'text' => 'Raka menguasai dasar pemrograman logika'],
                    ['key' => 'C', 'text' => 'Raka belum tentu menguasai dasar pemrograman logika'],
                    ['key' => 'D', 'text' => 'Semua anggota klub robotika mahir merakit rangkaian elektronik'],
                    ['key' => 'E', 'text' => 'Sebagian siswa yang lolos klub robotika tidak menguasai pemrograman logika'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'literasi',
                'nomor_urut' => 3,
                'pertanyaan' => "Bacalah teks berikut!\nMikroplastik berukuran kurang dari 5 milimeter kini banyak ditemukan di perairan laut Indonesia. Partikel ini berasal dari degradasi limbah plastik besar akibat paparan sinar ultraviolet dan gelombang laut, serta dari serat sintetis pakaian yang terlepas saat proses pencucian. Karena ukurannya menyerupai fitoplankton, mikroplastik sering dikonsumsi oleh biota laut kecil seperti ikan teri dan udang. Akibatnya, kandungan toksik dalam mikroplastik berpindah ke dalam rantai makanan dan berpotensi terakumulasi di tubuh manusia yang mengonsumsi hasil laut tersebut.\n\nSimpulan yang paling tepat untuk mewakili keseluruhan isi paragraf di atas adalah ....",
                'bobot' => 8,
                'kunci_jawaban' => 'D',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Serat pakaian sintetis merupakan penyumbang utama limbah mikroplastik di perairan laut Indonesia'],
                    ['key' => 'B', 'text' => 'Ikan teri dan udang tidak mampu membedakan antara fitoplankton dan partikel mikroplastik'],
                    ['key' => 'C', 'text' => 'Sinar ultraviolet dan gelombang laut mempercepat proses penghancuran sampah plastik di lautan'],
                    ['key' => 'D', 'text' => 'Limbah mikroplastik mencemari ekosistem laut dan mengancam kesehatan manusia melalui rantai makanan'],
                    ['key' => 'E', 'text' => 'Pemerintah perlu melarang penggunaan plastik sekali pakai untuk melindungi populasi fitoplankton'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'literasi',
                'nomor_urut' => 4,
                'pertanyaan' => "Perhatikan hubungan kata berikut!\nKLOROFIL : FOTOSINTESIS : TUMBUHAN = .... : .... : ....\n\nPasangan kata yang memiliki hubungan analogi paling setara dengan deret kata di atas adalah ....",
                'bobot' => 10,
                'kunci_jawaban' => 'A',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Hemoglobin : Respirasi : Manusia'],
                    ['key' => 'B', 'text' => 'Akar : Air : Tanah'],
                    ['key' => 'C', 'text' => 'Oksigen : Paru-paru : Darah'],
                    ['key' => 'D', 'text' => 'Stomata : Daun : Batang'],
                    ['key' => 'E', 'text' => 'Enzim : Pencernaan : Lambung'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'literasi',
                'nomor_urut' => 5,
                'pertanyaan' => "Bacalah kutipan cerita berikut!\nDamar menatap layar laptopnya dengan dahi berkerut. Data hasil pengamatan kualitas air sungai yang ia ambil kemarin sore menunjukkan angka pencemaran di atas ambang batas. Padahal, laporan kelompoknya harus dikumpulkan besok pagi, dan teman-temannya mendesak agar angka tersebut diubah sedikit supaya proyek penelitian mereka terlihat berhasil di depan juri. Damar menarik napas panjang, lalu mengetikkan angka asli apa adanya ke dalam tabel laporan sambil menyiapkan catatan analisis penyebab kegagalan filtrasi mereka.\n\nMakna tindakan Damar pada kalimat terakhir kutipan cerita tersebut menunjukkan bahwa ia ....",
                'bobot' => 10,
                'kunci_jawaban' => 'B',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Lebih mementingkan nilai pribadi daripada kekompakan tim penelitiannya'],
                    ['key' => 'B', 'text' => 'Memiliki integritas ilmiah dan berani mempertanggungjawabkan fakta di lapangan'],
                    ['key' => 'C', 'text' => 'Merasa pesimis bahwa kelompoknya dapat memenangkan perlombaan karya ilmiah'],
                    ['key' => 'D', 'text' => 'Kurang memiliki kemampuan komunikasi untuk bernegosiasi dengan rekan satu tim'],
                    ['key' => 'E', 'text' => 'Terburu-buru menyelesaikan laporan karena tenggat waktu pengumpulan sudah dekat'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'literasi',
                'nomor_urut' => 6,
                'pertanyaan' => "Lima orang siswa SMP (Aldi, Bela, Cakra, Dinda, dan Edo) mengikuti ujian simulasi olimpiade sains dengan rincian perolehan skor sebagai berikut:\n\n- Skor Cakra lebih tinggi daripada skor Dinda, tetapi lebih rendah daripada skor Bela.\n- Skor Edo lebih rendah daripada skor Aldi, namun lebih tinggi daripada skor Bela.\n- Tidak ada siswa yang memiliki skor sama.\n\nUrutan tiga siswa dengan perolehan skor tertinggi mulai dari peringkat pertama hingga ketiga adalah ....",
                'bobot' => 10,
                'kunci_jawaban' => 'C',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Aldi – Bela – Cakra'],
                    ['key' => 'B', 'text' => 'Edo – Bela – Cakra'],
                    ['key' => 'C', 'text' => 'Aldi – Edo – Bela'],
                    ['key' => 'D', 'text' => 'Bela – Edo – Aldi'],
                    ['key' => 'E', 'text' => 'Aldi – Edo – Cakra'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'literasi',
                'nomor_urut' => 7,
                'pertanyaan' => "Bacalah teks berikut!\nPaparan cahaya biru (blue light) dari layar gawai pada malam hari terbukti menekan produksi hormon melatonin di dalam otak hingga 50%. Hormon melatonin berfungsi memberikan sinyal kantuk dan mengatur ritme sirkadian tubuh. Remaja yang menggunakan gawai satu jam sebelum tidur rata-rata mengalami penundaan waktu tidur lelap selama 45 menit, sehingga bangun dalam kondisi kelelahan dan mengalami penurunan konsentrasi belajar di sekolah keesokan harinya.\n\nPernyataan manakah yang paling tepat untuk memperkuat argumen penulis dalam teks tersebut?",
                'bobot' => 10,
                'kunci_jawaban' => 'C',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Sebagian besar remaja menggunakan gawai pada malam hari untuk mengerjakan tugas sekolah dan berdiskusi'],
                    ['key' => 'B', 'text' => 'Penjualan kacamata antiradiasi meningkat pesat seiring bertambahnya pengguna telepon pintar di kalangan pelajar'],
                    ['key' => 'C', 'text' => 'Penelitian klinis menunjukkan bahwa membatasi layar gawai 60 menit sebelum tidur meningkatkan daya ingat siswa sebesar 30% di kelas'],
                    ['key' => 'D', 'text' => 'Hormon melatonin juga dapat ditemukan secara alami pada beberapa jenis makanan seperti kacang-kacangan dan buah ceri'],
                    ['key' => 'E', 'text' => 'Durasi tidur ideal bagi remaja usia sekolah menengah pertama adalah sekitar 8 hingga 10 jam setiap malam'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'literasi',
                'nomor_urut' => 8,
                'pertanyaan' => "Perhatikan premis-premis berikut!\nPremis 1: Jika perpustakaan sekolah menerapkan sistem katalog digital, maka proses peminjaman buku menjadi lebih cepat.\nPremis 2: Jika proses peminjaman buku menjadi lebih cepat, maka minat kunjung siswa ke perpustakaan meningkat.\nPremis 3: Pada semester ini, minat kunjung siswa ke perpustakaan tidak meningkat.\n\nSimpulan yang sah dari ketiga premis tersebut adalah ....",
                'bobot' => 12,
                'kunci_jawaban' => 'B',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Proses peminjaman buku sudah cepat, tetapi koleksi buku perpustakaan kurang menarik'],
                    ['key' => 'B', 'text' => 'Perpustakaan sekolah belum menerapkan sistem katalog digital'],
                    ['key' => 'C', 'text' => 'Sebagian siswa lebih memilih membaca buku elektronik melalui gawai masing-masing'],
                    ['key' => 'D', 'text' => 'Sistem katalog digital mengalami gangguan teknis pada semester ini'],
                    ['key' => 'E', 'text' => 'Minat kunjung siswa tidak dipengaruhi oleh kecepatan layanan peminjaman buku'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'literasi',
                'nomor_urut' => 9,
                'pertanyaan' => "Bacalah kedua teks berikut!\n\nTeks 1\nSistem pertanian hidroponik memanfaatkan air yang diperkaya larutan nutrisi mineral tanpa menggunakan media tanah. Metode ini sangat efisien karena menghemat penggunaan air hingga 80% dibandingkan pertanian konvensional, namun petani harus rutin membeli dan menakar pupuk cair buatan agar kepekatan nutrisi tetap stabil.\n\nTeks 2\nSistem akuaponik memadukan budidaya ikan dan tanaman secara simultan dalam satu siklus tertutup. Kotoran ikan di dalam kolam dipompa menuju media tanam untuk diurai oleh bakteri menjadi nutrisi alami bagi tanaman. Air yang telah disaring oleh akar tanaman kemudian dikembalikan lagi ke kolam ikan dalam kondisi bersih.\n\nPerbedaan mendasar mengenai sumber nutrisi tanaman antara kedua metode pada teks di atas adalah ....",
                'bobot' => 12,
                'kunci_jawaban' => 'C',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Hidroponik menggunakan bakteri pengurai air, sedangkan akuaponik mengandalkan mineral dari tanah liat'],
                    ['key' => 'B', 'text' => 'Hidroponik membutuhkan pasokan air yang jauh lebih banyak dibandingkan sistem sirkulasi akuaponik'],
                    ['key' => 'C', 'text' => 'Hidroponik bergantung pada penambahan larutan pupuk buatan, sedangkan akuaponik memanfaatkan hasil olahan limbah biologis ikan'],
                    ['key' => 'D', 'text' => 'Akuaponik hanya dapat diterapkan di lahan pertanian luas, sedangkan hidroponik dapat diterapkan di lahan sempit'],
                    ['key' => 'E', 'text' => 'Akuaponik memerlukan penggantian air secara berkala setiap hari, sedangkan hidroponik menggunakan siklus air tertutup'],
                ],
                'is_active' => true,
            ],
            [
                'babak' => 'literasi',
                'nomor_urut' => 10,
                'pertanyaan' => "Sebuah sekolah akan mengirimkan 3 orang perwakilan untuk mengikuti lomba cerdas cermat dari 6 kandidat terbaik: Fajar, Gilang, Hana, Indah, Jihan, dan Kiki. Pemilihan anggota tim harus mematuhi aturan berikut:\n\n- Jika Fajar dipilih, maka Gilang tidak boleh dipilih.\n- Hana dan Indah harus selalu dipilih bersama-sama, atau tidak dipilih keduanya sama sekali.\n- Jika Jihan dipilih, maka Kiki juga harus dipilih.\n\nKombinasi 3 siswa yang memenuhi seluruh persyaratan untuk menjadi tim perwakilan sekolah adalah ....",
                'bobot' => 12,
                'kunci_jawaban' => 'D',
                'opsi_jawaban' => [
                    ['key' => 'A', 'text' => 'Fajar, Gilang, dan Kiki'],
                    ['key' => 'B', 'text' => 'Fajar, Hana, dan Jihan'],
                    ['key' => 'C', 'text' => 'Gilang, Indah, dan Kiki'],
                    ['key' => 'D', 'text' => 'Fajar, Jihan, dan Kiki'],
                    ['key' => 'E', 'text' => 'Hana, Indah, dan Jihan'],
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
