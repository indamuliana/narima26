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
        if (! Schema::hasColumn('wawancara_siswa', 'cita_cita')) {
            Schema::table('wawancara_siswa', function (Blueprint $table) {
                $table->string('cita_cita')->nullable()->after('alasan_pilih_jurusan');
            });
        }

        // Salin nilai cita_cita yang pernah diisi di wawancara_orang_tua ke wawancara_siswa jika belum terisi
        if (Schema::hasColumn('wawancara_orang_tua', 'cita_cita')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("
                    UPDATE wawancara_siswa ws
                    JOIN wawancara_orang_tua wo ON ws.calon_siswa_id = wo.calon_siswa_id
                    SET ws.cita_cita = wo.cita_cita
                    WHERE wo.cita_cita IS NOT NULL
                      AND (ws.cita_cita IS NULL OR ws.cita_cita = '')
                ");
            } else {
                DB::statement("
                    UPDATE wawancara_siswa
                    SET cita_cita = (
                        SELECT cita_cita FROM wawancara_orang_tua
                        WHERE wawancara_orang_tua.calon_siswa_id = wawancara_siswa.calon_siswa_id
                          AND wawancara_orang_tua.cita_cita IS NOT NULL
                        LIMIT 1
                    )
                    WHERE cita_cita IS NULL OR cita_cita = ''
                ");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('wawancara_siswa', 'cita_cita')) {
            Schema::table('wawancara_siswa', function (Blueprint $table) {
                $table->dropColumn('cita_cita');
            });
        }
    }
};
