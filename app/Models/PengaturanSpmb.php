<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PengaturanSpmb extends Model
{
    protected $table = 'pengaturan_spmb';

    protected $fillable = [
        'tahun_ajaran',
        'is_pendaftaran_buka',
        'pesan_penutupan',
        'wa_cs_reguler',
        'wa_cs_unggulan',
    ];

    protected $casts = [
        'is_pendaftaran_buka' => 'boolean',
    ];

    /**
     * Dapatkan instance konfigurasi SPMB terpusat (singleton).
     */
    public static function getSettings(): self
    {
        // Self-healing migration jika tabel belum dibuat di hosting/local
        if (! Schema::hasTable('pengaturan_spmb')) {
            Schema::create('pengaturan_spmb', function (Blueprint $table) {
                $table->id();
                $table->string('tahun_ajaran', 20)->default('2027/2028');
                $table->boolean('is_pendaftaran_buka')->default(true);
                $table->text('pesan_penutupan')->nullable();
                $table->string('wa_cs_reguler', 25)->default('628112232880');
                $table->string('wa_cs_unggulan', 25)->default('6281122802880');
                $table->timestamps();
            });
        }

        return static::first() ?? static::create([
            'tahun_ajaran' => '2027/2028',
            'is_pendaftaran_buka' => true,
            'pesan_penutupan' => 'Pendaftaran online SPMB SMK Wikrama 1 Garut saat ini sedang ditutup atau belum dibuka. Silakan hubungi narahubung/CS kami untuk informasi lebih lanjut.',
            'wa_cs_reguler' => '628112232880',
            'wa_cs_unggulan' => '6281122802880',
        ]);
    }
}
