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
            $table->boolean('is_catatan_admin_done')->default(false)->after('catatan_admin');
            $table->boolean('is_catatan_pewawancara_done')->default(false)->after('catatan_untuk_pewawancara');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calon_siswa', function (Blueprint $table) {
            $table->dropColumn(['is_catatan_admin_done', 'is_catatan_pewawancara_done']);
        });
    }
};
