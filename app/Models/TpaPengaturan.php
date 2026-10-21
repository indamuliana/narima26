<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TpaPengaturan extends Model
{
    protected $table = 'tpa_pengaturan';

    protected $fillable = [
        'durasi_literasi_menit',
        'durasi_numerasi_menit',
        'max_pelanggaran',
        'petunjuk_literasi',
        'petunjuk_numerasi',
        'is_active',
    ];

    protected $casts = [
        'durasi_literasi_menit' => 'integer',
        'durasi_numerasi_menit' => 'integer',
        'max_pelanggaran' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Dapatkan konfigurasi TPA aktif (singleton fallback).
     */
    public static function getSettings(): self
    {
        return static::first() ?? static::create([
            'durasi_literasi_menit' => 45,
            'durasi_numerasi_menit' => 45,
            'max_pelanggaran' => 3,
            'is_active' => true,
        ]);
    }
}
