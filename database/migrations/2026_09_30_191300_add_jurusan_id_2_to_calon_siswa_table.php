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
            $table->foreignId('jurusan_id_2')->nullable()->after('jurusan_id')->constrained('master_jurusan')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calon_siswa', function (Blueprint $table) {
            $table->dropForeign(['jurusan_id_2']);
            $table->dropColumn('jurusan_id_2');
        });
    }
};
