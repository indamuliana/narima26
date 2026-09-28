<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeputusanKelulusan extends Model
{
    use HasFactory;

    protected $table = 'keputusan_kelulusan';

    public const KEPUTUSAN_DITERIMA = 'DITERIMA';
    public const KEPUTUSAN_DITOLAK = 'DITOLAK';

    protected $fillable = [
        'calon_siswa_id',
        'keputusan',
        'alasan_catatan',
        'ditetapkan_oleh',
        'ditetapkan_at',
        'versi_keputusan',
    ];

    protected function casts(): array
    {
        return [
            'ditetapkan_at' => 'datetime',
            'versi_keputusan' => 'integer',
        ];
    }

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    public function ditetapkanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditetapkan_oleh');
    }

    public function isDiterima(): bool
    {
        return $this->keputusan === self::KEPUTUSAN_DITERIMA;
    }

    public function isDitolak(): bool
    {
        return $this->keputusan === self::KEPUTUSAN_DITOLAK;
    }
}
