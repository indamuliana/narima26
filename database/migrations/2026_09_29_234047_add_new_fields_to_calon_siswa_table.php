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
            $table->unsignedTinyInteger('anak_ke')->nullable()->after('tanggal_lahir');
            $table->unsignedTinyInteger('jumlah_saudara')->nullable()->after('anak_ke');
            $table->string('tahun_lulus', 4)->nullable()->after('asal_sekolah_lainnya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calon_siswa', function (Blueprint $table) {
            $table->dropColumn(['anak_ke', 'jumlah_saudara', 'tahun_lulus']);
        });
    }
};
