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
        Schema::table('wawancara_orang_tua', function (Blueprint $table) {
            $table->unsignedBigInteger('infaq_rutin_bulanan')->nullable()->after('hafalan_quran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wawancara_orang_tua', function (Blueprint $table) {
            $table->dropColumn('infaq_rutin_bulanan');
        });
    }
};
