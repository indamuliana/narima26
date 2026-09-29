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
        Schema::table('kesepahaman_eula', function (Blueprint $table) {
            $table->string('program_snapshot', 50)->nullable()->after('versi_dokumen');
            $table->json('poin_disetujui')->nullable()->after('isi_dokumen_atau_referensi_dokumen');
            $table->json('klausul_snapshot')->nullable()->after('poin_disetujui');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kesepahaman_eula', function (Blueprint $table) {
            $table->dropColumn(['program_snapshot', 'poin_disetujui', 'klausul_snapshot']);
        });
    }
};
