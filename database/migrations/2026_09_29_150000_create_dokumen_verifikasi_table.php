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
        Schema::create('dokumen_verifikasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_verifikasi', 64)->unique();
            $table->string('jenis_dokumen', 50)->index();
            $table->string('nomor_dokumen', 100)->index();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnDelete();
            $table->string('penandatangan_role', 50); // BENDAHARA, KEPALA_SEKOLAH
            $table->string('penandatangan_nama', 150);
            $table->string('penandatangan_jabatan', 150);
            $table->dateTime('signed_at');
            $table->json('metadata')->nullable();
            $table->unsignedInteger('scan_count')->default(0);
            $table->timestamp('last_scanned_at')->nullable();
            $table->boolean('is_valid')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_verifikasi');
    }
};
