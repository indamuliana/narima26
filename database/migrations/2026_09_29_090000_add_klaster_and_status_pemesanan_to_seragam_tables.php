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
        // 1. Tambah klaster dan urutan_klaster pada master_seragam
        Schema::table('master_seragam', function (Blueprint $table) {
            $table->string('klaster', 30)->default('KBM')->after('wajib')->index();
            $table->integer('urutan_klaster')->default(2)->after('klaster');
        });

        // Inisialisasi data klaster pada master_seragam
        DB::table('master_seragam')
            ->whereIn('nama_jenis', ['Jas Almamater', 'Baju Olahraga', 'Atribut Sekolah'])
            ->update(['klaster' => 'MPLS', 'urutan_klaster' => 1]);

        DB::table('master_seragam')
            ->whereIn('nama_jenis', [
                'Celana/Rok Panjang Hijau',
                'Celana/Rok Panjang Muslim',
                'Baju Muslim',
                'Celana/Rok Pramuka',
                'Baju Pramuka'
            ])
            ->update(['klaster' => 'KBM', 'urutan_klaster' => 2]);

        DB::table('master_seragam')
            ->whereIn('nama_jenis', [
                'Celana/Rok Hitam',
                'Kemeja Putih',
                'Sepatu Pantofel (Laki-laki)',
                'Sepatu Pantofel (Perempuan)'
            ])
            ->orWhere('wajib', false)
            ->update(['klaster' => 'OPSIONAL', 'urutan_klaster' => 3]);

        // 2. Tambah status_pemesanan, tahap_pemesanan, dan tagihan_id pada ukuran_seragam
        Schema::table('ukuran_seragam', function (Blueprint $table) {
            $table->string('status_pemesanan', 30)->default('PESAN_SEKARANG')->after('beli_di_sekolah')->index();
            $table->integer('tahap_pemesanan')->default(1)->after('status_pemesanan');
            $table->foreignId('tagihan_id')->nullable()->after('tahap_pemesanan')->constrained('tagihan')->nullOnDelete();
        });

        // Sinkronisasi data lama pada ukuran_seragam
        DB::table('ukuran_seragam')
            ->where('beli_di_sekolah', false)
            ->update(['status_pemesanan' => 'PESAN_NANTI']);

        // 3. Tambah tahap_seragam pada tagihan
        Schema::table('tagihan', function (Blueprint $table) {
            $table->integer('tahap_seragam')->nullable()->default(1)->after('jenis_tagihan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropColumn('tahap_seragam');
        });

        Schema::table('ukuran_seragam', function (Blueprint $table) {
            $table->dropForeign(['tagihan_id']);
            $table->dropColumn(['tagihan_id', 'tahap_pemesanan', 'status_pemesanan']);
        });

        Schema::table('master_seragam', function (Blueprint $table) {
            $table->dropColumn(['urutan_klaster', 'klaster']);
        });
    }
};
