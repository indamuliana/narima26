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
        Schema::create('kesepahaman_poins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')->constrained('kesepahaman_kelompoks')->cascadeOnDelete();
            $table->string('kode_poin')->unique(); // e.g. reg_a_1
            $table->string('nomor'); // 1, 2, 3
            $table->text('uraian');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kesepahaman_poins');
    }
};
