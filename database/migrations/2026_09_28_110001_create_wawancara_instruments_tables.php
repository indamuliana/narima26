<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Wawancara Siswa (26 butir instrumen)
        Schema::create('wawancara_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnDelete();
            $table->foreignId('pewawancara_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_petugas')->nullable();
            $table->date('tanggal_wawancara')->nullable();
            $table->enum('status', ['DRAFT', 'SELESAI'])->default('DRAFT');

            // Instrumen Pertanyaan Siswa
            $table->string('tinggal_bersama')->nullable(); // Selama sekolah tinggal bersama
            $table->string('penanggung_jawab_belajar')->nullable(); // Penanggung jawab selama belajar
            $table->string('info_wikrama_dari')->nullable(); // Mendapat informasi tentang wikrama dari
            $table->text('kesan_siswa_aktif')->nullable(); // Kesan terhadap siswa aktif di wikrama saat ini
            $table->string('baca_quran')->nullable(); // Lancar membaca alquran? (Ya / Kurang / Belum)
            $table->string('hafalan_quran')->nullable(); // Jumlah hafalan yang dimiliki
            $table->text('alasan_masuk_wikrama')->nullable(); // Masuk ke SMK Wikrama 1 Garut karena
            $table->text('alasan_pilih_program')->nullable(); // Alasan memilih program reguler/unggulan
            $table->text('alasan_pilih_jurusan')->nullable(); // Alasan memilih jurusan
            $table->text('aktivitas_rutin')->nullable(); // Kesibukan/aktivitas rutin di luar sekolah
            $table->string('merokok')->nullable(); // Apakah siswa seorang perokok (Ya / Pernah / Tidak)
            $table->text('penyakit_menahun')->nullable(); // Penyakit menahun yang diderita
            $table->string('kondisi_kesehatan')->nullable(); // Kondisi kesehatan saat ini
            $table->string('disabilitas')->nullable(); // Disabilitas yang dimiliki

            // Observasi & Kerapihan (3 kategori)
            $table->enum('kerapihan_rambut', ['HIJAU', 'OREN', 'MERAH'])->default('HIJAU');
            $table->enum('kerapihan_seragam', ['HIJAU', 'OREN', 'MERAH'])->default('HIJAU');
            $table->enum('status_pendengaran', ['HIJAU', 'OREN', 'MERAH'])->default('HIJAU');

            // Status Penglihatan & Kelainan
            $table->string('status_penglihatan', 50)->default('NORMAL'); // Normal / Minus / Plus / Lainnya
            $table->string('mata_kiri', 30)->nullable(); // Nilai minus/plus mata kiri
            $table->string('mata_kanan', 30)->nullable(); // Nilai minus/plus mata kanan
            $table->string('status_penglihatan_lainnya')->nullable(); // Jika lainnya

            $table->string('alergi')->nullable(); // Memiliki alergi

            // Catatan Rahasia & Rekomendasi Pewawancara
            $table->text('hal_perhatian_khusus')->nullable(); // Hal yang perlu diperhatikan dari siswa (rahasia)
            $table->text('kesan_pewawancara')->nullable(); // Kesan dari pewawancara (rahasia)
            $table->text('catatan_pewawancara')->nullable(); // Catatan penting pewawancara (rahasia)
            $table->enum('rekomendasi', ['TERIMA', 'PERTIMBANGKAN', 'TOLAK'])->default('TERIMA'); // Rekomendasi

            $table->timestamps();
        });

        // 2. Tabel Wawancara Orang Tua (24 butir instrumen)
        Schema::create('wawancara_orang_tua', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnDelete();
            $table->foreignId('pewawancara_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_petugas')->nullable();
            $table->date('tanggal_wawancara')->nullable();
            $table->enum('status', ['DRAFT', 'SELESAI'])->default('DRAFT');

            // Identitas Narasumber Ortu
            $table->string('nama_diwawancarai')->nullable(); // Nama yang diwawancarai
            $table->string('hubungan_dengan_siswa', 50)->nullable(); // Ayah / Ibu / Wali / Saudara / Kerabat

            // Instrumen Pertanyaan Ortu
            $table->string('tinggal_bersama')->nullable(); // Selama sekolah tinggal bersama
            $table->string('jarak_rumah')->nullable(); // Jarak dari rumah ke sekolah
            $table->string('transportasi')->nullable(); // Transportasi yang digunakan ke sekolah
            $table->string('penanggung_jawab_belajar')->nullable(); // Penanggung jawab belajar
            $table->string('info_wikrama_dari')->nullable(); // Mendapat informasi tentang wikrama dari
            $table->string('baca_quran')->nullable(); // Lancar membaca alquran
            $table->string('hafalan_quran')->nullable(); // Memiliki hafalan quran
            $table->string('minat_program')->nullable(); // Minat program
            $table->string('minat_jurusan')->nullable(); // Minat jurusan
            $table->text('alasan_masuk_wikrama')->nullable(); // Alasan masuk SMK Wikrama 1 Garut
            $table->text('alasan_pilih_program_jurusan')->nullable(); // Alasan memilih program / jurusan
            $table->string('kebiasaan_tempat_tidur')->nullable(); // Kebiasaan anak merapihkan bekas tempat tidur
            $table->string('hobi')->nullable(); // Hobi siswa
            $table->string('cita_cita')->nullable(); // Cita cita siswa
            $table->string('merokok')->nullable(); // Apakah siswa pernah merokok
            $table->text('penyakit_diderita')->nullable(); // Penyakit yang diderita
            $table->string('alergi')->nullable(); // Memiliki alergi / Apakah siswa memiliki alergi
            $table->text('hal_perhatian_ortu')->nullable(); // Hal yang perlu diperhatikan dari siswa (menurut ortu)

            // Catatan Rahasia Pewawancara
            $table->text('kesan_pewawancara')->nullable(); // Kesan dari pewawancara (rahasia)
            $table->text('catatan_tambahan')->nullable(); // Catatan Tambahan pewawancara (rahasia)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wawancara_orang_tua');
        Schema::dropIfExists('wawancara_siswa');
    }
};
