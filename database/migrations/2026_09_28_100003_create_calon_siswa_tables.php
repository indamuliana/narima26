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
        // 8.1 calon_siswa
        Schema::create('calon_siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pendaftaran', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nisn', 20)->unique();
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->string('nama_lengkap', 150);
            $table->string('nama_panggilan', 50)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nik', 20)->nullable()->index();
            $table->string('no_kk', 20)->nullable();
            $table->string('agama', 30)->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->foreignId('provinsi_id')->nullable()->constrained('master_provinsi')->nullOnDelete();
            $table->foreignId('kabupaten_id')->nullable()->constrained('master_kabupaten')->nullOnDelete();
            $table->foreignId('kecamatan_id')->nullable()->constrained('master_kecamatan')->nullOnDelete();
            $table->foreignId('desa_id')->nullable()->constrained('master_desa')->nullOnDelete();
            $table->string('no_hp_siswa', 20)->nullable();
            $table->string('no_hp_ayah', 20)->nullable();
            $table->string('no_hp_ibu', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->foreignId('asal_sekolah_id')->nullable()->constrained('master_sekolah_asal')->nullOnDelete();
            $table->string('asal_sekolah_lainnya', 150)->nullable();
            $table->foreignId('program_id')->nullable()->constrained('master_program')->nullOnDelete();
            $table->foreignId('jurusan_id')->nullable()->constrained('master_jurusan')->nullOnDelete();
            $table->foreignId('gelombang_id')->nullable()->constrained('master_gelombang')->nullOnDelete();
            $table->string('status_spmb', 50)->default('REGISTRASI')->index();
            $table->string('status_data', 50)->default('BELUM_LENGKAP')->index();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 8.2 riwayat_status_spmb
        Schema::create('riwayat_status_spmb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnDelete();
            $table->string('status_sebelumnya', 50)->nullable();
            $table->string('status_baru', 50);
            $table->text('alasan')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at');
            $table->timestamps();
        });

        // 9. data_orangtua
        Schema::create('data_orangtua', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->unique()->constrained('calon_siswa')->cascadeOnDelete();
            // Data Ayah
            $table->string('nama_ayah', 150)->nullable();
            $table->string('nik_ayah', 20)->nullable();
            $table->string('tahun_lahir_ayah', 10)->nullable();
            $table->foreignId('pekerjaan_ayah_id')->nullable()->constrained('master_pekerjaan')->nullOnDelete();
            $table->string('penghasilan_ayah', 50)->nullable();
            $table->string('pendidikan_ayah', 50)->nullable();
            $table->string('no_hp_ayah', 20)->nullable();
            $table->text('alamat_ayah')->nullable();
            // Data Ibu
            $table->string('nama_ibu', 150)->nullable();
            $table->string('nik_ibu', 20)->nullable();
            $table->string('tahun_lahir_ibu', 10)->nullable();
            $table->foreignId('pekerjaan_ibu_id')->nullable()->constrained('master_pekerjaan')->nullOnDelete();
            $table->string('penghasilan_ibu', 50)->nullable();
            $table->string('pendidikan_ibu', 50)->nullable();
            $table->string('no_hp_ibu', 20)->nullable();
            $table->text('alamat_ibu')->nullable();
            // Data Wali
            $table->string('nama_wali', 150)->nullable();
            $table->string('hubungan_wali', 50)->nullable();
            $table->foreignId('pekerjaan_wali_id')->nullable()->constrained('master_pekerjaan')->nullOnDelete();
            $table->string('penghasilan_wali', 50)->nullable();
            $table->string('no_hp_wali', 20)->nullable();
            $table->text('alamat_wali')->nullable();
            $table->timestamps();
        });

        // 10. data_akademik
        Schema::create('data_akademik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->unique()->constrained('calon_siswa')->cascadeOnDelete();
            $table->string('nama_sekolah', 150)->nullable();
            $table->string('npsn', 20)->nullable();
            $table->string('nisn', 20)->nullable();
            $table->decimal('nilai_rata_rata', 5, 2)->nullable();
            $table->decimal('nilai_bahasa_indonesia', 5, 2)->nullable();
            $table->decimal('nilai_matematika', 5, 2)->nullable();
            $table->decimal('nilai_bahasa_inggris', 5, 2)->nullable();
            $table->decimal('nilai_ipa', 5, 2)->nullable();
            $table->decimal('nilai_lainnya', 5, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 10. prestasi
        Schema::create('prestasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnDelete();
            $table->string('jenis_prestasi', 50); // akademik, non-akademik
            $table->string('tingkat', 50); // sekolah, kecamatan, kota/kab, provinsi, nasional, internasional
            $table->string('nama_prestasi', 150);
            $table->string('tahun', 10)->nullable();
            $table->string('peringkat', 50)->nullable(); // Juara 1, 2, 3, Harapan
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 11. ukuran_seragam
        Schema::create('ukuran_seragam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnDelete();
            $table->foreignId('jenis_seragam_id')->constrained('master_seragam')->cascadeOnDelete();
            $table->string('ukuran', 10);
            $table->integer('jumlah')->default(1);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 12. dokumen_pendaftaran (spesifik / non-generik)
        Schema::create('dokumen_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->unique()->constrained('calon_siswa')->cascadeOnDelete();
            $table->string('kk_path')->nullable();
            $table->string('akta_path')->nullable();
            $table->string('ijazah_skl_path')->nullable();
            $table->string('pas_foto_path')->nullable();
            $table->string('dokumen_pendukung_path')->nullable();
            $table->timestamps();
        });

        // 19. kesepahaman_eula
        Schema::create('kesepahaman_eula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnDelete();
            $table->string('versi_dokumen', 20)->default('v1.0');
            $table->text('isi_dokumen_atau_referensi_dokumen')->nullable();
            $table->boolean('setuju')->default(false);
            $table->timestamp('agreed_at')->nullable();
            $table->foreignId('agreed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kesepahaman_eula');
        Schema::dropIfExists('dokumen_pendaftaran');
        Schema::dropIfExists('ukuran_seragam');
        Schema::dropIfExists('prestasi');
        Schema::dropIfExists('data_akademik');
        Schema::dropIfExists('data_orangtua');
        Schema::dropIfExists('riwayat_status_spmb');
        Schema::dropIfExists('calon_siswa');
    }
};
