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
        // 1. Drop active index safely across SQLite and MySQL before dropping column
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS master_sekolah_asal_aktif_index');
        } else {
            try {
                Schema::table('master_sekolah_asal', function (Blueprint $table) {
                    $table->dropIndex(['aktif']);
                });
            } catch (\Throwable $e) {
                // Index may already be dropped or named differently
            }
        }

        // 2. Alter master_sekolah_asal table structure to match requested schema:
        // id, npsn, nama_sekolah, status, created_at, updated_at, jenis, provinsi, kokab, kecamatan
        Schema::table('master_sekolah_asal', function (Blueprint $table) {
            if (Schema::hasColumn('master_sekolah_asal', 'alamat')) {
                $table->dropColumn('alamat');
            }
            if (Schema::hasColumn('master_sekolah_asal', 'kabupaten_kota')) {
                $table->dropColumn('kabupaten_kota');
            }
            if (Schema::hasColumn('master_sekolah_asal', 'aktif')) {
                $table->dropColumn('aktif');
            }
            if (Schema::hasColumn('master_sekolah_asal', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            if (!Schema::hasColumn('master_sekolah_asal', 'status')) {
                $table->string('status', 50)->nullable()->after('nama_sekolah');
            }
            if (!Schema::hasColumn('master_sekolah_asal', 'jenis')) {
                $table->string('jenis', 50)->nullable()->after('updated_at');
            }
            if (!Schema::hasColumn('master_sekolah_asal', 'provinsi')) {
                $table->string('provinsi', 100)->nullable()->after('jenis');
            }
            if (!Schema::hasColumn('master_sekolah_asal', 'kokab')) {
                $table->string('kokab', 100)->nullable()->after('provinsi');
            }
        });

        // Reposition kecamatan after kokab on MySQL
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('master_sekolah_asal', function (Blueprint $table) {
                $table->string('kecamatan', 100)->nullable()->after('kokab')->change();
            });
        }

        // 3. Overwrite / insert the 10 temporary school records provided by user
        $sekolahList = [
            [
                'id' => 1,
                'npsn' => '70055392',
                'nama_sekolah' => 'Sekolah Rakyat Menengah Pertama 10 Bogor',
                'status' => 'Negeri',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 2,
                'npsn' => '20254243',
                'nama_sekolah' => 'SMP N 4 CIBINONG',
                'status' => 'Negeri',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 3,
                'npsn' => '20200611',
                'nama_sekolah' => 'SMP NEGERI 1 CIBINONG',
                'status' => 'Negeri',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 4,
                'npsn' => '20200627',
                'nama_sekolah' => 'SMP NEGERI 2 CIBINONG',
                'status' => 'Negeri',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 5,
                'npsn' => '20200649',
                'nama_sekolah' => 'SMP NEGERI 3 CIBINONG',
                'status' => 'Negeri',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 6,
                'npsn' => '20255885',
                'nama_sekolah' => 'SMP AL AZHAR SYIFA BUDI CIBINONG',
                'status' => 'Swasta',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 7,
                'npsn' => '20230936',
                'nama_sekolah' => 'SMP AL KHOER',
                'status' => 'Swasta',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 8,
                'npsn' => '20200624',
                'nama_sekolah' => 'SMP AL MIZAN',
                'status' => 'Swasta',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 9,
                'npsn' => '20200614',
                'nama_sekolah' => 'SMP AL NUR',
                'status' => 'Swasta',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
            [
                'id' => 10,
                'npsn' => '69982632',
                'nama_sekolah' => 'SMP AL QURAN WAHDAH ISLAMIYAH CIBINONG-BOGOR',
                'status' => 'Swasta',
                'jenis' => 'SMP',
                'provinsi' => 'Jawa Barat',
                'kokab' => 'Kab. Bogor',
                'kecamatan' => 'Cibinong',
            ],
        ];

        foreach ($sekolahList as $item) {
            DB::table('master_sekolah_asal')->updateOrInsert(
                ['id' => $item['id']],
                $item
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert columns if needed
    }
};
