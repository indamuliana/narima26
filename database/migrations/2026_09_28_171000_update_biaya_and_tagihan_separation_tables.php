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
        // 1. Tambah jenis_kelamin pada master_biaya (L = Putra, P = Putri, null = Semua)
        Schema::table('master_biaya', function (Blueprint $table) {
            $table->char('jenis_kelamin', 1)->nullable()->after('gelombang_id')->index();
        });

        // 2. Tambah jenis_tagihan pada tagihan (DAFTAR_ULANG vs SERAGAM)
        Schema::table('tagihan', function (Blueprint $table) {
            $table->string('jenis_tagihan', 30)->default('DAFTAR_ULANG')->after('nomor_tagihan')->index();
        });

        // 3. Tambah wajib dan jenis_kelamin pada master_seragam
        Schema::table('master_seragam', function (Blueprint $table) {
            $table->boolean('wajib')->default(true)->after('ukuran')->index();
            $table->char('jenis_kelamin', 1)->nullable()->after('wajib')->index();
        });

        // 4. Tambah beli_di_sekolah pada ukuran_seragam
        Schema::table('ukuran_seragam', function (Blueprint $table) {
            $table->boolean('beli_di_sekolah')->default(true)->after('jumlah')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ukuran_seragam', function (Blueprint $table) {
            $table->dropColumn('beli_di_sekolah');
        });

        Schema::table('master_seragam', function (Blueprint $table) {
            $table->dropColumn(['wajib', 'jenis_kelamin']);
        });

        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropColumn('jenis_tagihan');
        });

        Schema::table('master_biaya', function (Blueprint $table) {
            $table->dropColumn('jenis_kelamin');
        });
    }
};
