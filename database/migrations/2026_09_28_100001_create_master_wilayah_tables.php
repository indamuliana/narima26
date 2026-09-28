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
        Schema::create('master_provinsi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama', 100);
            $table->timestamps();
        });

        Schema::create('master_kabupaten', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provinsi_id')->constrained('master_provinsi')->cascadeOnDelete();
            $table->string('kode', 10)->unique();
            $table->string('nama', 100);
            $table->timestamps();
        });

        Schema::create('master_kecamatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kabupaten_id')->constrained('master_kabupaten')->cascadeOnDelete();
            $table->string('kode', 15)->unique();
            $table->string('nama', 100);
            $table->timestamps();
        });

        Schema::create('master_desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('master_kecamatan')->cascadeOnDelete();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->string('kode_pos', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_desa');
        Schema::dropIfExists('master_kecamatan');
        Schema::dropIfExists('master_kabupaten');
        Schema::dropIfExists('master_provinsi');
    }
};
