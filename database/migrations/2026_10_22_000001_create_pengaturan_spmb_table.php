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
        Schema::create('pengaturan_spmb', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran', 20)->default('2027/2028');
            $table->boolean('is_pendaftaran_buka')->default(true);
            $table->text('pesan_penutupan')->nullable();
            $table->string('wa_cs_reguler', 25)->default('628112232880');
            $table->string('wa_cs_unggulan', 25)->default('6281122802880');
            $table->timestamps();
        });

        // Seed initial record
        DB::table('pengaturan_spmb')->insert([
            'tahun_ajaran' => '2027/2028',
            'is_pendaftaran_buka' => true,
            'pesan_penutupan' => 'Pendaftaran online SPMB SMK Wikrama 1 Garut saat ini sedang ditutup atau belum dibuka. Silakan hubungi narahubung/CS kami untuk informasi lebih lanjut.',
            'wa_cs_reguler' => '628112232880',
            'wa_cs_unggulan' => '6281122802880',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_spmb');
    }
};
