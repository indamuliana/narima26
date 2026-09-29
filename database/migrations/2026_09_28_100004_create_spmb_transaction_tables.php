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
        // 17. pembayaran_seleksi
        Schema::create('pembayaran_seleksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnDelete();
            $table->decimal('nominal_tagihan', 12, 2)->default(200000);
            $table->decimal('nominal_dibayar', 12, 2)->default(0);
            $table->date('tanggal_bayar')->nullable();
            $table->string('metode_bayar', 50)->default('transfer_bank');
            $table->string('bank_pengirim', 100)->nullable();
            $table->string('nama_pengirim', 150)->nullable();
            $table->string('nomor_referensi', 100)->nullable();
            $table->string('bukti_transfer_path')->nullable();
            $table->string('status', 30)->default('PENDING')->index(); // PENDING, DIVERIFIKASI, DITOLAK
            $table->text('catatan_bendahara')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // 21.1 wawancara
        Schema::create('wawancara', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnDelete();
            $table->foreignId('pewawancara_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_wawancara')->nullable();
            $table->string('status', 30)->default('MENUNGGU')->index(); // MENUNGGU, PROSES, SELESAI
            $table->text('catatan_umum')->nullable();
            $table->text('catatan_orang_tua')->nullable();
            $table->timestamps();
        });

        // 21.2 wawancara_detail
        Schema::create('wawancara_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wawancara_id')->constrained('wawancara')->cascadeOnDelete();
            $table->foreignId('kriteria_id')->constrained('master_kriteria_wawancara')->cascadeOnDelete();
            $table->string('indikator', 150)->nullable();
            $table->integer('nilai')->default(0);
            $table->enum('warna', ['HIJAU', 'ORANYE', 'MERAH'])->default('HIJAU');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 24. diskon
        Schema::create('diskon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnDelete();
            $table->string('jenis_diskon', 100); // Diskon Prestasi, Diskon Saudara Kandung, dll
            $table->enum('metode_diskon', ['persentase', 'nominal'])->default('nominal');
            $table->decimal('nilai_diskon', 12, 2)->default(0);
            $table->decimal('nominal_potongan', 12, 2)->default(0);
            $table->text('alasan')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('diberikan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diberikan_at')->nullable();
            $table->timestamps();
        });

        // 22.1 tagihan (snapshot tagihan daftar ulang)
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnDelete();
            $table->string('nomor_tagihan', 50)->unique();
            $table->string('program_snapshot', 100);
            $table->string('gelombang_snapshot', 100);
            $table->decimal('total_bruto', 12, 2)->default(0);
            $table->decimal('total_diskon', 12, 2)->default(0);
            $table->decimal('total_netto', 12, 2)->default(0);
            $table->string('status', 30)->default('BELUM_LUNAS')->index(); // BELUM_LUNAS, CICILAN, LUNAS
            $table->foreignId('diskon_id')->nullable()->constrained('diskon')->nullOnDelete();
            $table->timestamps();
        });

        // 22.2 tagihan_detail (snapshot item biaya)
        Schema::create('tagihan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihan')->cascadeOnDelete();
            $table->string('kode_biaya_snapshot', 30);
            $table->string('nama_biaya_snapshot', 150);
            $table->string('kategori_snapshot', 50);
            $table->decimal('nominal_snapshot', 12, 2)->default(0);
            $table->integer('jumlah')->default(1);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();
        });

        // 23. pembayaran_daftar_ulang
        Schema::create('pembayaran_daftar_ulang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnDelete();
            $table->foreignId('tagihan_id')->constrained('tagihan')->cascadeOnDelete();
            $table->decimal('nominal_tagihan', 12, 2)->default(0);
            $table->decimal('nominal_dibayar', 12, 2)->default(0);
            $table->date('tanggal_bayar')->nullable();
            $table->string('metode_bayar', 50)->default('transfer_bank');
            $table->string('bank_pengirim', 100)->nullable();
            $table->string('nama_pengirim', 150)->nullable();
            $table->string('nomor_referensi', 100)->nullable();
            $table->string('bukti_transfer_path')->nullable();
            $table->string('status', 30)->default('PENDING')->index(); // PENDING, DIVERIFIKASI, DITOLAK
            $table->text('catatan_bendahara')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // 25. keputusan_kelulusan
        Schema::create('keputusan_kelulusan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->unique()->constrained('calon_siswa')->cascadeOnDelete();
            $table->enum('keputusan', ['DITERIMA', 'DITOLAK'])->index();
            $table->text('alasan_catatan')->nullable();
            $table->foreignId('ditetapkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ditetapkan_at')->nullable();
            $table->integer('versi_keputusan')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keputusan_kelulusan');
        Schema::dropIfExists('pembayaran_daftar_ulang');
        Schema::dropIfExists('tagihan_detail');
        Schema::dropIfExists('tagihan');
        Schema::dropIfExists('diskon');
        Schema::dropIfExists('wawancara_detail');
        Schema::dropIfExists('wawancara');
        Schema::dropIfExists('pembayaran_seleksi');
    }
};
