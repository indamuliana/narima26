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
            $table->boolean('is_luar_negeri')->default(false)->after('agama');
            $table->string('negara', 100)->nullable()->after('is_luar_negeri');
            $table->string('provinsi_luar_negeri', 100)->nullable()->after('negara');
            $table->string('kabupaten_luar_negeri', 100)->nullable()->after('provinsi_luar_negeri');
            $table->string('kecamatan_luar_negeri', 100)->nullable()->after('kabupaten_luar_negeri');
            $table->string('desa_luar_negeri', 100)->nullable()->after('kecamatan_luar_negeri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calon_siswa', function (Blueprint $table) {
            $table->dropColumn([
                'is_luar_negeri',
                'negara',
                'provinsi_luar_negeri',
                'kabupaten_luar_negeri',
                'kecamatan_luar_negeri',
                'desa_luar_negeri',
            ]);
        });
    }
};
