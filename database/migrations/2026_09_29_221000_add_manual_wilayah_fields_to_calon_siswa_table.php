<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('calon_siswa', function (Blueprint $table) {
            $table->string('provinsi_nama', 100)->nullable()->after('agama');
            $table->string('kabupaten_nama', 100)->nullable()->after('provinsi_nama');
            $table->string('kecamatan_nama', 100)->nullable()->after('kabupaten_nama');
            $table->string('desa_nama', 100)->nullable()->after('kecamatan_nama');
        });

        // Backfill data from master relations if available
        try {
            DB::statement("
                UPDATE calon_siswa c
                LEFT JOIN master_provinsi p ON c.provinsi_id = p.id
                LEFT JOIN master_kabupaten k ON c.kabupaten_id = k.id
                LEFT JOIN master_kecamatan kc ON c.kecamatan_id = kc.id
                LEFT JOIN master_desa d ON c.desa_id = d.id
                SET 
                    c.provinsi_nama = COALESCE(p.nama, c.provinsi_luar_negeri),
                    c.kabupaten_nama = COALESCE(k.nama, c.kabupaten_luar_negeri),
                    c.kecamatan_nama = COALESCE(kc.nama, c.kecamatan_luar_negeri),
                    c.desa_nama = COALESCE(d.nama, c.desa_luar_negeri)
            ");
        } catch (\Throwable $e) {
            // Ignore in sqlite memory if table structure differs
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calon_siswa', function (Blueprint $table) {
            $table->dropColumn([
                'provinsi_nama',
                'kabupaten_nama',
                'kecamatan_nama',
                'desa_nama',
            ]);
        });
    }
};
