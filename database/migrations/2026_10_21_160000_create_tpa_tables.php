<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pengaturan Global TPA
        Schema::create('tpa_pengaturan', function (Blueprint $table) {
            $table->id();
            $table->integer('durasi_literasi_menit')->default(45);
            $table->integer('durasi_numerasi_menit')->default(45);
            $table->integer('max_pelanggaran')->default(3);
            $table->text('petunjuk_literasi')->nullable();
            $table->text('petunjuk_numerasi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default setting
        DB::table('tpa_pengaturan')->insert([
            'durasi_literasi_menit' => 45,
            'durasi_numerasi_menit' => 45,
            'max_pelanggaran' => 3,
            'petunjuk_literasi' => 'Kerjakan soal literasi dengan teliti dan jujur. Pilihlah salah satu jawaban yang menurut Anda paling benar.',
            'petunjuk_numerasi' => 'Kerjakan soal numerasi secara mandiri. Dilarang menggunakan kalkulator atau alat bantu hitung lainnya.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Bank Soal TPA (Literasi & Numerasi)
        Schema::create('tpa_soal', function (Blueprint $table) {
            $table->id();
            $table->enum('babak', ['literasi', 'numerasi'])->index();
            $table->integer('nomor_urut')->default(1);
            $table->text('pertanyaan');
            $table->string('gambar_path')->nullable();
            $table->integer('bobot')->default(1);
            $table->string('kunci_jawaban', 5); // 'A', 'B', 'C', 'D', 'E'
            $table->json('opsi_jawaban'); // [{'key':'A', 'text':'...'}, ...]
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 3. Jadwal & Hak Akses Per Calon Siswa
        Schema::create('tpa_jadwal_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->unique()->constrained('calon_siswa')->cascadeOnDelete();
            $table->dateTime('waktu_mulai')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->boolean('is_diizinkan')->default(true)->index();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 4. Sesi Ujian Siswa (State Pengerjaan & Skor)
        Schema::create('tpa_sesi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->unique()->constrained('calon_siswa')->cascadeOnDelete();
            $table->enum('status', ['belum_mulai', 'babak_1', 'transisi', 'babak_2', 'selesai', 'didiskualifikasi'])->default('belum_mulai')->index();
            $table->dateTime('babak_1_mulai')->nullable();
            $table->dateTime('babak_1_selesai')->nullable();
            $table->dateTime('babak_2_mulai')->nullable();
            $table->dateTime('babak_2_selesai')->nullable();
            $table->json('urutan_soal_literasi')->nullable();
            $table->json('urutan_soal_numerasi')->nullable();
            $table->decimal('skor_literasi', 8, 2)->default(0);
            $table->decimal('skor_numerasi', 8, 2)->default(0);
            $table->decimal('total_skor', 8, 2)->default(0);
            $table->integer('benar_literasi')->default(0);
            $table->integer('salah_literasi')->default(0);
            $table->integer('kosong_literasi')->default(0);
            $table->integer('benar_numerasi')->default(0);
            $table->integer('salah_numerasi')->default(0);
            $table->integer('kosong_numerasi')->default(0);
            $table->integer('jumlah_pelanggaran')->default(0);
            $table->json('log_pelanggaran')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        // 5. Jawaban Rinci Siswa
        Schema::create('tpa_jawaban_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tpa_sesi_siswa_id')->constrained('tpa_sesi_siswa')->cascadeOnDelete();
            $table->foreignId('tpa_soal_id')->constrained('tpa_soal')->cascadeOnDelete();
            $table->string('jawaban_dipilih', 5)->nullable();
            $table->boolean('is_ragu')->default(false);
            $table->boolean('is_benar')->default(false);
            $table->decimal('poin_diperoleh', 8, 2)->default(0);
            $table->timestamps();

            $table->unique(['tpa_sesi_siswa_id', 'tpa_soal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tpa_jawaban_siswa');
        Schema::dropIfExists('tpa_sesi_siswa');
        Schema::dropIfExists('tpa_jadwal_siswa');
        Schema::dropIfExists('tpa_soal');
        Schema::dropIfExists('tpa_pengaturan');
    }
};
