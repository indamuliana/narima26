<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Naskah Klausul Kesepahaman & Pakta Integritas SPMB
    | SMK WIKRAMA 1 GARUT TAHUN PELAJARAN 2027/2028
    |--------------------------------------------------------------------------
    | Digunakan oleh KesepahamanService dan KesepahamanSeeder untuk mengisi data
    | klausul pada database kesepahaman_programs, kelompoks, dan poins.
    */

    'reguler' => [
        'program_title' => 'PROGRAM REGULER',
        'tahun_pelajaran' => '2027/2028',
        'kelompok' => [
            [
                'kode' => 'A',
                'judul' => 'Dalam kaitannya dengan Sistem Penerimaan Murid Baru (SPMB), murid bersedia :',
                'poin' => [
                    [
                        'id' => 'reg_a_1',
                        'nomor' => '1',
                        'uraian' => 'Melaporkan kepada SMK WIKRAMA 1 GARUT jika tidak lulus SMP/MTs sederajat paling lambat dua hari setelah pengumuman kelulusan, dengan menyerahkan surat keterangan tidak lulus.',
                    ],
                    [
                        'id' => 'reg_a_2',
                        'nomor' => '2',
                        'uraian' => 'Memenuhi persyaratan terbebas dari permasalahan hukum.',
                    ],
                    [
                        'id' => 'reg_a_3',
                        'nomor' => '3',
                        'uraian' => 'Memenuhi ketentuan belum pernah menikah dan tidak menikah selama menjadi Murid SMK Wikrama 1 Garut',
                    ],
                    [
                        'id' => 'reg_a_4',
                        'nomor' => '4',
                        'uraian' => 'Mengundurkan diri atau dikeluarkan dari SMK WIKRAMA 1 GARUT apabila dikemudian hari terbukti memberikan keterangan atau dokumen palsu.',
                    ],
                ],
            ],
            [
                'kode' => 'B',
                'judul' => 'Dalam kaitannya dengan mengikuti pendidikan di SMK Wikrama 1 Garut, murid dan orang tua bersedia mengikuti skema pembelajaran sebagai berikut :',
                'poin' => [
                    [
                        'id' => 'reg_b_1',
                        'nomor' => '1',
                        'uraian' => 'Menerima keputusan penjurusan yang telah ditetapkan oleh pihak sekolah berdasarkan kuota dan hasil penelusuran minat dan bakat.',
                    ],
                    [
                        'id' => 'reg_b_2',
                        'nomor' => '2',
                        'uraian' => 'Mengikuti kegiatan sekolah sesuai dengan kalender akademik SMK Wikrama 1 Garut',
                    ],
                    [
                        'id' => 'reg_b_3',
                        'nomor' => '3',
                        'uraian' => 'Mengikuti Kegiatan Belajar Mengajar (KBM) dari hari Senin sampai Jumat pukul 06.50 sampai 16.00 WIB',
                    ],
                    [
                        'id' => 'reg_b_4',
                        'nomor' => '4',
                        'uraian' => 'Mengikuti kegiatan mata pelajaran seni budaya dan ekstrakurikuler, yang pelaksanaannya setiap hari tertentu mulai pukul 16.00 sampai 17.30 WIB.',
                    ],
                    [
                        'id' => 'reg_b_5',
                        'nomor' => '5',
                        'uraian' => 'Mengikuti kegiatan Pembelajaran Berbasis Proyek (8 Dimensi Profil Lulusan) sesuai dengan tema dari sekolah.',
                    ],
                    [
                        'id' => 'reg_b_6',
                        'nomor' => '6',
                        'uraian' => "Memenuhi persyaratan naik kelas sebagai berikut :\n- Menyelesaikan program pembelajaran dan memenuhi Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) dalam satu tahun pelajaran.\n- Memiliki nilai sikap minimal baik, nilai sikap bersumber dari :\n  * BKP (Buku Kejar Prestasi)\n  * Penilaian sikap dari orang tua/pengasuh santri\n  * Penilaian sikap dari guru\n- Persentase kehadiran dalam Kegiatan Belajar Mengajar (KBM) dan ekstrakurikuler sekurang-kurangnya 90% dalam satu tahun pelajaran.\n- Nilai ekstrakurikuler pramuka sekurang-kurangnya baik.",
                    ],
                    [
                        'id' => 'reg_b_7',
                        'nomor' => '7',
                        'uraian' => 'Mengikuti program remedial dan atau pengayaan sebagai tindak lanjut dari proses pendidikan.',
                    ],
                    [
                        'id' => 'reg_b_8',
                        'nomor' => '8',
                        'uraian' => 'Mengundurkan diri sebagai murid SMK Wikrama 1 Garut jika tidak naik kelas 2 (dua) tahun berturut-turut.',
                    ],
                    [
                        'id' => 'reg_b_9',
                        'nomor' => '9',
                        'uraian' => 'Mengikuti kegiatan sekolah pada hari Sabtu untuk ekstrakurikuler tertentu secara terjadwal atau kegiatan lainnya berdasarkan surat edaran resmi atau pengumuman dari sekolah.',
                    ],
                    [
                        'id' => 'reg_b_10',
                        'nomor' => '10',
                        'uraian' => 'Mengikuti kegiatan Competence Based Training (CBT) dalam rangka persiapan Praktik Kerja Lapangan (PKL).',
                    ],
                    [
                        'id' => 'reg_b_11',
                        'nomor' => '11',
                        'uraian' => 'Mengikuti Praktik Kerja Lapangan (PKL) selama 6 bulan (atau sesuai dengan peraturan pemerintah) dan bersedia ditempatkan di berbagai wilayah sesuai dengan program PKL yang berlaku.',
                    ],
                    [
                        'id' => 'reg_b_12',
                        'nomor' => '12',
                        'uraian' => 'Mengikuti kegiatan sertifikasi internasional bahasa Inggris atau bahasa asing lainnya dan sertifikasi kompetensi keahlian.',
                    ],
                    [
                        'id' => 'reg_b_13',
                        'nomor' => '13',
                        'uraian' => "Wajib mengikuti kegiatan pembelajaran kokurikuler yaitu :\n- Kelas peminatan persiapan bekerja\n- Kelas peminatan persiapan melanjutkan pendidikan\n- Kelas peminatan wirausaha",
                    ],
                ],
            ],
            [
                'kode' => 'C',
                'judul' => 'Dalam kaitannya dengan kedisiplinan, murid bersedia :',
                'poin' => [
                    [
                        'id' => 'reg_c_1',
                        'nomor' => '1',
                        'uraian' => "Dikembalikan kepada orang tua apabila :\n- Terbukti melakukan pencurian, pornografi, pornoaksi, berbuat asusila, provokasi, menggunakan/mengedarkan/membawa narkoba/minuman beralkohol, psikotropika, zat adiktif, obat-obat tertentu (OOT) yang disalahgunakan dan berkelahi/tawuran/pengeroyokan/penganiayaan.\n- Sudah mendapatkan surat peringatan ke-3 (SP 3).",
                    ],
                    [
                        'id' => 'reg_c_2',
                        'nomor' => '2',
                        'uraian' => 'Mematuhi ketentuan berpakaian dan berpenampilan yang berlaku di sekolah meliputi seragam sekolah, rambut, aksesoris, kosmetik, sepatu dan lainnya.',
                    ],
                    [
                        'id' => 'reg_c_3',
                        'nomor' => '3',
                        'uraian' => 'Membawa Buku Kejar Prestasi (BKP) dan mengenakan name tag setiap hari sebagai tanda kehadiran di sekolah.',
                    ],
                    [
                        'id' => 'reg_c_4',
                        'nomor' => '4',
                        'uraian' => "Mematuhi untuk :\n- Tidak membawa handphone ke sekolah dengan alasan apapun. Untuk HP yang disimpan/disita di sekolah akan dikembalikan setelah lulus. (Kecuali digunakan untuk kepentingan pembelajaran yang disertai dengan adanya surat edaran resmi dari sekolah)\n- Tidak mengendarai kendaraan bermotor jika belum memiliki SIM.\n- Tidak terlibat dalam kelompok/komunitas/organisasi/geng meresahkan yang dilarang oleh sekolah maupun oleh pemerintah.\n- Tidak merokok/vape/shisha/sejenisnya selama menjadi murid SMK Wikrama 1 Garut.\n- Tidak memiliki tindik/piercing/tato/sejenisnya.\n- Tidak mengunggah foto, video, kalimat yang berbau SARA (Suku, Agama, Ras dan Antar golongan) dan tindakan negatif lainnya di sosial media (Instagram, WhatsApp, Facebook, Twitter/X, Youtube, Tiktok, dan lainnya) yang diatur oleh UU ITE.",
                    ],
                    [
                        'id' => 'reg_c_5',
                        'nomor' => '5',
                        'uraian' => 'Menginfakkan sebagian seragam dan atribut sekolah layak pakai ke SMK Wikrama 1 Garut pada saat dinyatakan lulus dari satuan pendidikan dan atau pada saat dikembalikan kepada orang tua.',
                    ],
                    [
                        'id' => 'reg_c_6',
                        'nomor' => '6',
                        'uraian' => 'Diberikan surat peringatan apabila sudah mendapatkan pembinaan/teguran.',
                    ],
                    [
                        'id' => 'reg_c_7',
                        'nomor' => '7',
                        'uraian' => 'Menjaga nama baik diri sendiri, orang tua, keluarga dan sekolah baik di lingkungan sekolah, masyarakat atau di media sosial.',
                    ],
                    [
                        'id' => 'reg_c_8',
                        'nomor' => '8',
                        'uraian' => 'Dilakukan pengecekan akun media sosial dan konten yang tidak sesuai dengan norma baik di handphone maupun laptop oleh pihak sekolah.',
                    ],
                    [
                        'id' => 'reg_c_9',
                        'nomor' => '9',
                        'uraian' => 'Menjaga kesehatan jasmani dan rohani seperti berkonsultasi dengan psikolog, mengikuti tes urine ataupun hal lainnya yang dianggap perlu (sesuai rujukan) serta menerapkan pola perilaku hidup bersih dan sehat baik di lingkungan sekolah maupun di luar sekolah.',
                    ],
                    [
                        'id' => 'reg_c_10',
                        'nomor' => '10',
                        'uraian' => 'Menjaga dan merawat seluruh fasilitas sekolah dan peralatan milik pribadi.',
                    ],
                ],
            ],
            [
                'kode' => 'D',
                'judul' => 'Dalam kaitannya dengan pembiasaan positif, murid diwajibkan untuk :',
                'poin' => [
                    [
                        'id' => 'reg_d_1',
                        'nomor' => '1',
                        'uraian' => 'Mengikuti program pembinaan akhlak mulia berupa shalat dhuha setiap hari, 3T (Tahsin, Tajwid, Tahfidz) dan Tadabbur Al Qur’an, shalat wajib berjamaah, malam bina iman dan takwa dan sholat tahajud di sekolah sesuai jadwal yang telah ditentukan, membaca Al-Qur’an sebelum shalat dhuha dan setelah shalat ashar, serta membaca do’a dzikir pagi dan sore.',
                    ],
                    [
                        'id' => 'reg_d_2',
                        'nomor' => '2',
                        'uraian' => 'Mengikuti seluruh kegiatan Pramuka termasuk kegiatan perkemahan Penerimaan Tamu Ambalan (PTA), Bantara dan Laksana Gugus Depan 02-117 & 02-118 sebagai bagian dari proses pendidikan karakter dan wawasan kebangsaan.',
                    ],
                    [
                        'id' => 'reg_d_3',
                        'nomor' => '3',
                        'uraian' => 'Mengikuti kegiatan teaching factory dan usaha bersama sekolah sebagai bagian dari program kegiatan praktik kewirausahaan.',
                    ],
                    [
                        'id' => 'reg_d_4',
                        'nomor' => '4',
                        'uraian' => 'Mengikuti program kegiatan kepedulian sosial dan kegiatan gugus murid berbasis wilayah/rayon.',
                    ],
                    [
                        'id' => 'reg_d_5',
                        'nomor' => '5',
                        'uraian' => 'Mengikuti program kegiatan budaya kerja 5R (Ringkas, Rapi, Resik, Rawat, Rajin), budaya 5S (Senyum, Salam, Sapa, Sopan, Santun) untuk membentuk kebiasaan positif murid dalam berinteraksi agar tercipta lingkungan yang harmonis, nyaman, dan saling menghargai, baik di lingkungan sekolah maupun di luar sekolah, budaya cinta lingkungan P4LH (Pembibitan, Penanaman, Pemeliharaan, dan Pengawasan Lingkungan Hidup) sebagai bagian dari bentuk komitmen warga sekolah untuk melestarikan, menjaga, serta mengelola kelestarian lingkungan hidup secara berkelanjutan, budaya K3LH (Kesehatan, Keselamatan Kerja, dan Lingkungan Hidup) untuk menciptakan lingkungan belajar yang aman, sehat, nyaman, serta mencegah terjadinya kecelakaan dan gangguan kesehatan bagi seluruh warga sekolah',
                    ],
                ],
            ],
            [
                'kode' => 'E',
                'judul' => 'Dalam kaitannya dengan partisipasi pendidikan, orangtua bersedia :',
                'poin' => [
                    [
                        'id' => 'reg_e_1',
                        'nomor' => '1',
                        'uraian' => "Memahami bahwa biaya pendidikan program reguler setiap murid per bulan untuk tahun pelajaran 2027/2028 adalah sebesar Rp. 750.000,- (tujuh ratus lima puluh ribu rupiah) yang bersumber dari pemerintah berupa Bantuan Operasional Sekolah (BOS) sebesar Rp. 100.000,- (seratus ribu rupiah), subsidi Yayasan Prawitama sebesar Rp. 200.000,- (dua ratus ribu rupiah) dan dari orang tua/wali Murid berupa Sumbangan Pembinaan Pendidikan (SPP) Program Reguler sebesar Rp. 450.000,- (Empat ratus lima puluh ribu rupiah) dengan rincian sebagai berikut :\n- SPP Sekolah (Operasional KBM & praktikum) : Rp 450.000,-",
                    ],
                    [
                        'id' => 'reg_e_2',
                        'nomor' => '2',
                        'uraian' => 'Membayar Sumbangan Pembinaan Pendidikan (SPP) Program Reguler paling lambat tanggal 10 setiap bulannya sebesar Rp. 450.000,- (Empat ratus lima puluh ribu rupiah). Pembayaran SPP dapat dilakukan melalui Virtual Account yang akan diinformasikan lebih lanjut.',
                    ],
                    [
                        'id' => 'reg_e_3',
                        'nomor' => '3',
                        'uraian' => 'Memahami bahwa biaya Dana Sumbangan Pendidikan (DSP) untuk tahun pelajaran 2027/2028 (Gelombang 1) adalah sebesar Rp. 3.500.000,- (Tiga juta lima ratus ribu rupiah)',
                    ],
                    [
                        'id' => 'reg_e_4',
                        'nomor' => '4',
                        'uraian' => 'Memberikan sejumlah dana bantuan yang tetap setiap bulannya berupa sumbangan infak/sodakoh/jariyah rutin bulanan di luar pembayaran untuk SPP sebesar Rp. ………..…………… setiap bulan (Untuk nominalnya dapat diisi pada saat wawancara)',
                    ],
                    [
                        'id' => 'reg_e_5',
                        'nomor' => '5',
                        'uraian' => 'Memahami bahwa kegiatan kunjungan industri, rihlah (outing class) dan perpisahan murid merupakan kegiatan yang diselenggarakan atas dasar kesepakatan para orangtua dengan pembiayaan yang ditanggung oleh orang tua.',
                    ],
                    [
                        'id' => 'reg_e_6',
                        'nomor' => '6',
                        'uraian' => 'Menyetujui bahwa poin 1 s.d 4 tidak termasuk kegiatan sertifikasi kompetensi keahlian, program pemantapan kompetensi bahasa, kejuruan, rihlah (outing class), persiapan kerja dalam dan luar negeri, dan kegiatan lainnya (seperti : PKL/magang, kunjungan industri, berenang, dan biaya akhir tahun/perpisahan).',
                    ],
                    [
                        'id' => 'reg_e_7',
                        'nomor' => '7',
                        'uraian' => 'Menerima ketentuan bahwa biaya yang sudah dibayarkan tidak dapat dikembalikan dengan alasan apapun apabila murid mengundurkan diri dari SMK Wikrama 1 Garut (kecuali tidak lulus dari SMP/MTs/Sederajat).',
                    ],
                    [
                        'id' => 'reg_e_8',
                        'nomor' => '8',
                        'uraian' => 'Menghadiri undangan sekolah dalam rangka (a) kegiatan umum sekolah, (b) konsultasi dengan pembimbing rayon untuk pembinaan murid, dan (c) kegiatan parenting.',
                    ],
                    [
                        'id' => 'reg_e_9',
                        'nomor' => '9',
                        'uraian' => 'Mendorong murid untuk meningkatkan potensi dan kompetensinya melalui kegiatan akademik dan atau non akademik seperti berkonsultasi dengan psikolog, mengikuti tes urine ataupun hal lainnya yang dianggap perlu (sesuai rujukan) yang pembiayaannya dibebankan kepada orang tua',
                    ],
                    [
                        'id' => 'reg_e_10',
                        'nomor' => '10',
                        'uraian' => "Bekerjasama dengan pihak sekolah untuk :\n- Mendorong murid memenuhi ketentuan Buku Kejar Prestasi (BKP)\n- Memperhatikan kesehatan dan asupan gizi murid, termasuk melarang merokok.\n- Menyelesaikan permasalahan administrasi sekolah dengan orang tua yang menandatangani naskah persetujuan kesepahaman.\n- Menyelesaikan setiap permasalahan akademik dan non akademik murid secara kekeluargaan dengan orang tua yang menandatangani naskah persetujuan kesepahaman.",
                    ],
                ],
            ],
        ],
    ],

    'unggulan' => [
        'program_title' => 'PROGRAM UNGGULAN',
        'tahun_pelajaran' => '2027/2028',
        'kelompok' => [
            [
                'kode' => 'A',
                'judul' => 'Dalam kaitannya dengan Sistem Penerimaan Murid Baru (SPMB), murid bersedia :',
                'poin' => [
                    [
                        'id' => 'ugg_a_1',
                        'nomor' => '1',
                        'uraian' => 'Melaporkan kepada SMK WIKRAMA 1 GARUT jika tidak lulus SMP/MTs sederajat paling lambat dua hari setelah pengumuman kelulusan, dengan menyerahkan surat keterangan tidak lulus.',
                    ],
                    [
                        'id' => 'ugg_a_2',
                        'nomor' => '2',
                        'uraian' => 'Memenuhi persyaratan terbebas dari permasalahan hukum.',
                    ],
                    [
                        'id' => 'ugg_a_3',
                        'nomor' => '3',
                        'uraian' => 'Memenuhi ketentuan belum pernah menikah dan tidak menikah selama menjadi Murid SMK Wikrama 1 Garut',
                    ],
                    [
                        'id' => 'ugg_a_4',
                        'nomor' => '4',
                        'uraian' => 'Mengundurkan diri atau dikeluarkan dari SMK WIKRAMA 1 GARUT apabila dikemudian hari terbukti memberikan keterangan atau dokumen palsu.',
                    ],
                ],
            ],
            [
                'kode' => 'B',
                'judul' => 'Dalam kaitannya dengan mengikuti pendidikan di SMK Wikrama 1 Garut, murid dan orang tua bersedia mengikuti skema pembelajaran sebagai berikut :',
                'poin' => [
                    [
                        'id' => 'ugg_b_1',
                        'nomor' => '1',
                        'uraian' => 'Menerima keputusan penjurusan yang telah ditetapkan oleh pihak sekolah berdasarkan kuota dan hasil penelusuran minat dan bakat.',
                    ],
                    [
                        'id' => 'ugg_b_2',
                        'nomor' => '2',
                        'uraian' => 'Mengikuti kegiatan sekolah sesuai dengan kalender akademik SMK Wikrama 1 Garut',
                    ],
                    [
                        'id' => 'ugg_b_3',
                        'nomor' => '3',
                        'uraian' => 'Mengikuti Kegiatan Belajar Mengajar (KBM) dari hari Senin sampai Jumat pukul 06.50 sampai 16.00 WIB',
                    ],
                    [
                        'id' => 'ugg_b_4',
                        'nomor' => '4',
                        'uraian' => 'Mengikuti kegiatan mata pelajaran seni budaya dan ekstrakurikuler, yang pelaksanaannya setiap hari tertentu mulai pukul 16.00 sampai 17.30 WIB.',
                    ],
                    [
                        'id' => 'ugg_b_5',
                        'nomor' => '5',
                        'uraian' => 'Mengikuti kegiatan Pembelajaran Berbasis Proyek (8 Dimensi Profil Lulusan) sesuai dengan tema dari sekolah.',
                    ],
                    [
                        'id' => 'ugg_b_6',
                        'nomor' => '6',
                        'uraian' => "Memenuhi persyaratan naik kelas sebagai berikut :\n- Menyelesaikan program pembelajaran dan memenuhi Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) dalam satu tahun pelajaran.\n- Memiliki nilai sikap minimal baik, nilai sikap bersumber dari :\n  * BKP (Buku Kejar Prestasi)\n  * Penilaian sikap dari orang tua/pengasuh santri\n  * Penilaian sikap dari guru\n- Persentase kehadiran dalam Kegiatan Belajar Mengajar (KBM) dan ekstrakurikuler sekurang-kurangnya 90% dalam satu tahun pelajaran.\n- Nilai ekstrakurikuler pramuka sekurang-kurangnya baik.",
                    ],
                    [
                        'id' => 'ugg_b_7',
                        'nomor' => '7',
                        'uraian' => 'Mengikuti program remedial dan atau pengayaan sebagai tindak lanjut dari proses pendidikan.',
                    ],
                    [
                        'id' => 'ugg_b_8',
                        'nomor' => '8',
                        'uraian' => 'Mengundurkan diri sebagai murid SMK Wikrama 1 Garut jika tidak naik kelas 2 (dua) tahun berturut-turut.',
                    ],
                    [
                        'id' => 'ugg_b_9',
                        'nomor' => '9',
                        'uraian' => 'Mengikuti kegiatan sekolah pada hari Sabtu untuk ekstrakurikuler tertentu secara terjadwal atau kegiatan lainnya berdasarkan surat edaran resmi atau pengumuman dari sekolah.',
                    ],
                    [
                        'id' => 'ugg_b_10',
                        'nomor' => '10',
                        'uraian' => 'Mengikuti kegiatan Competence Based Training (CBT) dalam rangka persiapan Praktik Kerja Lapangan (PKL).',
                    ],
                    [
                        'id' => 'ugg_b_11',
                        'nomor' => '11',
                        'uraian' => 'Mengikuti Praktik Kerja Lapangan (PKL) selama 6 bulan (atau sesuai dengan peraturan pemerintah) dan bersedia ditempatkan di berbagai wilayah sesuai dengan program PKL yang berlaku.',
                    ],
                    [
                        'id' => 'ugg_b_12',
                        'nomor' => '12',
                        'uraian' => 'Mengikuti kegiatan sertifikasi internasional bahasa Inggris atau bahasa asing lainnya dan sertifikasi kompetensi keahlian.',
                    ],
                    [
                        'id' => 'ugg_b_13',
                        'nomor' => '13',
                        'uraian' => "Wajib mengikuti kegiatan pembelajaran kokurikuler yaitu :\n- Kelas peminatan persiapan bekerja\n- Kelas peminatan persiapan melanjutkan pendidikan\n- Kelas peminatan wirausaha",
                    ],
                ],
            ],
            [
                'kode' => 'C',
                'judul' => 'Dalam kaitannya dengan kedisiplinan, murid bersedia :',
                'poin' => [
                    [
                        'id' => 'ugg_c_1',
                        'nomor' => '1',
                        'uraian' => "Dikembalikan kepada orang tua apabila :\n- Terbukti melakukan pencurian, pornografi, pornoaksi, berbuat asusila, provokasi, menggunakan/mengedarkan/membawa narkoba/minuman beralkohol, psikotropika, zat adiktif, obat-obat tertentu (OOT) yang disalahgunakan dan berkelahi/tawuran/pengeroyokan/penganiayaan.\n- Sudah mendapatkan surat peringatan ke-3 (SP 3).",
                    ],
                    [
                        'id' => 'ugg_c_2',
                        'nomor' => '2',
                        'uraian' => 'Mematuhi ketentuan berpakaian dan berpenampilan yang berlaku di sekolah meliputi seragam sekolah, rambut, aksesoris, kosmetik, sepatu dan lainnya.',
                    ],
                    [
                        'id' => 'ugg_c_3',
                        'nomor' => '3',
                        'uraian' => 'Membawa Buku Kejar Prestasi (BKP) dan mengenakan name tag setiap hari sebagai tanda kehadiran di sekolah.',
                    ],
                    [
                        'id' => 'ugg_c_4',
                        'nomor' => '4',
                        'uraian' => "Mematuhi untuk :\n- Tidak membawa handphone ke sekolah dengan alasan apapun. Untuk HP yang disimpan/disita di sekolah akan dikembalikan setelah lulus. (Kecuali digunakan untuk kepentingan pembelajaran yang disertai dengan adanya surat edaran resmi dari sekolah)\n- Tidak mengendarai kendaraan bermotor jika belum memiliki SIM.\n- Tidak terlibat dalam kelompok/komunitas/organisasi/geng meresahkan yang dilarang oleh sekolah maupun oleh pemerintah.\n- Tidak merokok/vape/shisha/sejenisnya selama menjadi murid SMK Wikrama 1 Garut.\n- Tidak memiliki tindik/piercing/tato/sejenisnya.\n- Tidak mengunggah foto, video, kalimat yang berbau SARA (Suku, Agama, Ras dan Antar golongan) dan tindakan negatif lainnya di sosial media (Instagram, WhatsApp, Facebook, Twitter/X, Youtube, Tiktok, dan lainnya) yang diatur oleh UU ITE.",
                    ],
                    [
                        'id' => 'ugg_c_5',
                        'nomor' => '5',
                        'uraian' => 'Menginfakkan sebagian seragam dan atribut sekolah layak pakai ke SMK Wikrama 1 Garut pada saat dinyatakan lulus dari satuan pendidikan dan atau pada saat dikembalikan kepada orang tua.',
                    ],
                    [
                        'id' => 'ugg_c_6',
                        'nomor' => '6',
                        'uraian' => 'Diberikan surat peringatan apabila sudah mendapatkan pembinaan/teguran.',
                    ],
                    [
                        'id' => 'ugg_c_7',
                        'nomor' => '7',
                        'uraian' => 'Menjaga nama baik diri sendiri, orang tua, keluarga dan sekolah baik di lingkungan sekolah, masyarakat atau di media sosial.',
                    ],
                    [
                        'id' => 'ugg_c_8',
                        'nomor' => '8',
                        'uraian' => 'Dilakukan pengecekan akun media sosial dan konten yang tidak sesuai dengan norma baik di handphone maupun laptop oleh pihak sekolah.',
                    ],
                    [
                        'id' => 'ugg_c_9',
                        'nomor' => '9',
                        'uraian' => 'Menjaga kesehatan jasmani dan rohani seperti berkonsultasi dengan psikolog, mengikuti tes urine ataupun hal lainnya yang dianggap perlu (sesuai rujukan) serta menerapkan pola perilaku hidup bersih dan sehat baik di lingkungan sekolah maupun di luar sekolah.',
                    ],
                    [
                        'id' => 'ugg_c_10',
                        'nomor' => '10',
                        'uraian' => 'Menjaga dan merawat seluruh fasilitas sekolah dan peralatan milik pribadi.',
                    ],
                ],
            ],
            [
                'kode' => 'D',
                'judul' => 'Dalam kaitannya dengan pembiasaan positif, murid diwajibkan untuk :',
                'poin' => [
                    [
                        'id' => 'ugg_d_1',
                        'nomor' => '1',
                        'uraian' => 'Mengikuti program pembinaan akhlak mulia berupa shalat dhuha setiap hari, 3T (Tahsin, Tajwid, Tahfidz) dan Tadabbur Al Qur’an, shalat wajib berjamaah, malam bina iman dan takwa dan sholat tahajud di sekolah sesuai jadwal yang telah ditentukan, membaca Al-Qur’an sebelum shalat dhuha dan setelah shalat ashar, serta membaca do’a dzikir pagi dan sore.',
                    ],
                    [
                        'id' => 'ugg_d_2',
                        'nomor' => '2',
                        'uraian' => 'Mengikuti seluruh kegiatan Pramuka termasuk kegiatan perkemahan Penerimaan Tamu Ambalan (PTA), Bantara dan Laksana Gugus Depan 02-117 & 02-118 sebagai bagian dari proses pendidikan karakter dan wawasan kebangsaan.',
                    ],
                    [
                        'id' => 'ugg_d_3',
                        'nomor' => '3',
                        'uraian' => 'Mengikuti kegiatan teaching factory dan usaha bersama sekolah sebagai bagian dari program kegiatan praktik kewirausahaan.',
                    ],
                    [
                        'id' => 'ugg_d_4',
                        'nomor' => '4',
                        'uraian' => 'Mengikuti program kegiatan kepedulian sosial dan kegiatan gugus murid berbasis wilayah/rayon.',
                    ],
                    [
                        'id' => 'ugg_d_5',
                        'nomor' => '5',
                        'uraian' => 'Mengikuti program kegiatan budaya kerja 5R (Ringkas, Rapi, Resik, Rawat, Rajin), budaya 5S (Senyum, Salam, Sapa, Sopan, Santun) untuk membentuk kebiasaan positif murid dalam berinteraksi agar tercipta lingkungan yang harmonis, nyaman, dan saling menghargai, baik di lingkungan sekolah maupun di luar sekolah, budaya cinta lingkungan P4LH (Pembibitan, Penanaman, Pemeliharaan, dan Pengawasan Lingkungan Hidup) sebagai bagian dari bentuk komitmen warga sekolah untuk melestarikan, menjaga, serta mengelola kelestarian lingkungan hidup secara berkelanjutan, budaya K3LH (Kesehatan, Keselamatan Kerja, dan Lingkungan Hidup) untuk menciptakan lingkungan belajar yang aman, sehat, nyaman, serta mencegah terjadinya kecelakaan dan gangguan kesehatan bagi seluruh warga sekolah',
                    ],
                ],
            ],
            [
                'kode' => 'E',
                'judul' => 'Dalam kaitannya dengan partisipasi pendidikan, orang tua bersedia :',
                'poin' => [
                    [
                        'id' => 'ugg_e_1',
                        'nomor' => '1',
                        'uraian' => "Memahami bahwa biaya pendidikan program unggulan setiap murid per bulan untuk tahun pelajaran 2027/2028 adalah sebesar Rp. 2.200.000,- (dua juta dua ratus ribu rupiah) yang bersumber dari pemerintah berupa Bantuan Operasional Sekolah (BOS) sebesar Rp. 100.000,- (seratus ribu rupiah) dan dari orang tua/wali Murid berupa Sumbangan Pembinaan Pendidikan (SPP) Program Unggulan sebesar Rp. 2.100.000,- (dua juta seratus ribu rupiah) dengan rincian sebagai berikut :\n- SPP Sekolah (KBM SMK, Praktikum, dan pembelajaran agama & tahfidz) : Rp 650.000,-\n- SPP Pondok dengan fasilitas asrama, lemari, tempat tidur, makan 2x weekday & 3x weekend, laundry, serta kegiatan pendukung : Rp 1.450.000,-",
                    ],
                    [
                        'id' => 'ugg_e_2',
                        'nomor' => '2',
                        'uraian' => 'Membayar Sumbangan Pembinaan Pendidikan (SPP) Program Unggulan paling lambat tanggal 10 setiap bulannya sebesar Rp. 2.100.000,- (dua juta seratus ribu rupiah). Pembayaran SPP dapat dilakukan melalui Virtual Account yang akan diinformasikan lebih lanjut.',
                    ],
                    [
                        'id' => 'ugg_e_3',
                        'nomor' => '3',
                        'uraian' => 'Memahami bahwa biaya Dana Sumbangan Pendidikan (DSP) untuk tahun pelajaran 2027/2028 gelombang 1 adalah sebesar Rp. 7.500.000,- (Tujuh juta lima ratus ribu rupiah)',
                    ],
                    [
                        'id' => 'ugg_e_4',
                        'nomor' => '4',
                        'uraian' => 'Memberikan sejumlah dana bantuan yang tetap setiap bulannya berupa sumbangan infak/sodakoh/jariyah rutin bulanan di luar pembayaran untuk SPP sebesar Rp. ………..…………… setiap bulan (Untuk nominalnya dapat diisi pada saat wawancara)',
                    ],
                    [
                        'id' => 'ugg_e_5',
                        'nomor' => '5',
                        'uraian' => 'Memahami bahwa kegiatan kunjungan industri, rihlah (outing class) dan perpisahan murid merupakan kegiatan yang diselenggarakan atas dasar kesepakatan para orangtua dengan pembiayaan yang ditanggung oleh orang tua.',
                    ],
                    [
                        'id' => 'ugg_e_6',
                        'nomor' => '6',
                        'uraian' => 'Menyetujui bahwa poin 1 s.d 4 tidak termasuk kegiatan sertifikasi kompetensi keahlian, program pemantapan kompetensi bahasa, kejuruan, rihlah (outing class), persiapan kerja dalam dan luar negeri, dan kegiatan lainnya (seperti : PKL/magang, kunjungan industri, berenang, dan biaya akhir tahun/perpisahan).',
                    ],
                    [
                        'id' => 'ugg_e_7',
                        'nomor' => '7',
                        'uraian' => 'Menerima ketentuan bahwa biaya yang sudah dibayarkan tidak dapat dikembalikan dengan alasan apapun apabila murid mengundurkan diri dari SMK Wikrama 1 Garut (kecuali tidak lulus dari SMP/MTs/Sederajat).',
                    ],
                    [
                        'id' => 'ugg_e_8',
                        'nomor' => '8',
                        'uraian' => 'Menghadiri undangan sekolah dalam rangka (a) kegiatan umum sekolah, (b) konsultasi dengan pembimbing rayon untuk pembinaan murid, dan (c) kegiatan parenting.',
                    ],
                    [
                        'id' => 'ugg_e_9',
                        'nomor' => '9',
                        'uraian' => 'Mendorong murid untuk meningkatkan potensi dan kompetensinya melalui kegiatan akademik dan atau non akademik seperti berkonsultasi dengan psikolog, mengikuti tes urine ataupun hal lainnya yang dianggap perlu (sesuai rujukan) yang pembiayaannya dibebankan kepada orang tua',
                    ],
                    [
                        'id' => 'ugg_e_10',
                        'nomor' => '10',
                        'uraian' => "Bekerjasama dengan pihak sekolah untuk :\n- Mendorong murid memenuhi ketentuan Buku Kejar Prestasi (BKP)\n- Memperhatikan kesehatan dan asupan gizi murid, termasuk melarang merokok.\n- Menyelesaikan permasalahan administrasi sekolah dengan orang tua yang menandatangani naskah persetujuan kesepahaman.\n- Menyelesaikan setiap permasalahan akademik dan non akademik murid secara kekeluargaan dengan orang tua yang menandatangani naskah persetujuan kesepahaman.",
                    ],
                ],
            ],
            [
                'kode' => 'F',
                'judul' => 'Dalam kaitannya dengan Program Unggulan, murid bersedia :',
                'poin' => [
                    [
                        'id' => 'ugg_f_1',
                        'nomor' => '1',
                        'uraian' => 'Tinggal di Asrama yang sudah disediakan oleh pihak sekolah.',
                    ],
                    [
                        'id' => 'ugg_f_2',
                        'nomor' => '2',
                        'uraian' => 'Memahami dan menyetujui kesepahaman atau tata tertib Islamic Boarding School Al Ikrom.',
                    ],
                    [
                        'id' => 'ugg_f_3',
                        'nomor' => '3',
                        'uraian' => 'Memahami dan menyetujui ketentuan program unggulan tidak dapat pindah ke program reguler.',
                    ],
                ],
            ],
        ],
    ],
];
