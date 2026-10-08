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
        Schema::create('kesepahaman_programs', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // e.g., 'reguler', 'unggulan'
            $table->string('nama'); // e.g., 'REGULER', 'UNGGULAN'
            $table->string('tahun_pelajaran')->default('2027/2028');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kesepahaman_programs');
    }
};

