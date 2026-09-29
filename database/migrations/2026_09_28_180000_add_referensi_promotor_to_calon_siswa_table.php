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
        Schema::table('calon_siswa', function (Blueprint $table) {
            $table->string('referensi_jenis', 50)->nullable()->after('asal_sekolah_lainnya');
            $table->string('referensi_nama', 150)->nullable()->after('referensi_jenis');
            $table->string('referensi_rayon', 100)->nullable()->after('referensi_nama');
            $table->string('referensi_nomor_seleksi', 50)->nullable()->after('referensi_rayon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calon_siswa', function (Blueprint $table) {
            $table->dropColumn([
                'referensi_jenis',
                'referensi_nama',
                'referensi_rayon',
                'referensi_nomor_seleksi',
            ]);
        });
    }
};
