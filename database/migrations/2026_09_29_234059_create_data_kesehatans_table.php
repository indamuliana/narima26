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
        Schema::create('data_kesehatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnDelete();
            $table->unsignedSmallInteger('tinggi_badan')->nullable();
            $table->unsignedSmallInteger('berat_badan')->nullable();
            $table->string('golongan_darah', 20)->nullable();
            $table->string('buta_warna', 50)->nullable();
            $table->string('penyakit_pernah_diderita', 100)->nullable();
            $table->string('penyakit_pernah_diderita_lainnya', 150)->nullable();
            $table->string('penyakit_sedang_diderita', 100)->nullable();
            $table->string('penyakit_sedang_diderita_lainnya', 150)->nullable();
            $table->string('kesehatan_mata', 50)->nullable();
            $table->string('jenis_alergi', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_kesehatan');
    }
};
