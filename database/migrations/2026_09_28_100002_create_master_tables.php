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
        // 7.1 master_program
        Schema::create('master_program', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->text('keterangan')->nullable();
            $table->boolean('aktif')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        // 7.2 master_jurusan
        Schema::create('master_jurusan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->text('keterangan')->nullable();
            $table->boolean('aktif')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        // 7.3 master_gelombang
        Schema::create('master_gelombang', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->boolean('aktif')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        // 7.4 master_pekerjaan
        Schema::create('master_pekerjaan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->boolean('aktif')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        // 7.5 master_seragam
        Schema::create('master_seragam', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama_jenis', 100);
            $table->string('ukuran', 10);
            $table->text('keterangan')->nullable();
            $table->boolean('aktif')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        // 7.6 master_sekolah_asal
        Schema::create('master_sekolah_asal', function (Blueprint $table) {
            $table->id();
            $table->string('npsn', 20)->nullable()->index();
            $table->string('nama_sekolah', 150);
            $table->text('alamat')->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kabupaten_kota', 100)->nullable();
            $table->boolean('aktif')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        // 7.7 master_biaya
        Schema::create('master_biaya', function (Blueprint $table) {
            $table->id();
            $table->string('kode_biaya', 30)->unique();
            $table->string('nama_biaya', 150);
            $table->string('kategori', 50)->default('daftar_ulang')->index(); // seleksi, daftar_ulang, dll
            $table->foreignId('program_id')->nullable()->constrained('master_program')->nullOnDelete();
            $table->foreignId('gelombang_id')->nullable()->constrained('master_gelombang')->nullOnDelete();
            $table->decimal('nominal', 12, 2)->default(0);
            $table->string('tipe_nominal', 30)->default('tetap');
            $table->boolean('wajib')->default(true);
            $table->boolean('aktif')->default(true)->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 7.8 master_kriteria_wawancara
        Schema::create('master_kriteria_wawancara', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama_kriteria', 150);
            $table->string('jenis_penilaian', 50)->default('siswa'); // siswa, orang_tua
            $table->integer('urutan')->default(1);
            $table->boolean('aktif')->default(true)->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_kriteria_wawancara');
        Schema::dropIfExists('master_biaya');
        Schema::dropIfExists('master_sekolah_asal');
        Schema::dropIfExists('master_seragam');
        Schema::dropIfExists('master_pekerjaan');
        Schema::dropIfExists('master_gelombang');
        Schema::dropIfExists('master_jurusan');
        Schema::dropIfExists('master_program');
    }
};
