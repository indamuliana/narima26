<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wawancara extends Model
{
    use HasFactory;

    protected $table = 'wawancara';

    public const STATUS_MENUNGGU = 'MENUNGGU';
    public const STATUS_PROSES = 'PROSES';
    public const STATUS_SELESAI = 'SELESAI';

    protected $fillable = [
        'calon_siswa_id',
        'pewawancara_id',
        'tanggal_wawancara',
        'status',
        'catatan_umum',
        'catatan_orang_tua',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_wawancara' => 'date',
        ];
    }

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    public function pewawancara(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pewawancara_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(WawancaraDetail::class, 'wawancara_id');
    }

    public function getCatatanSiswaAttribute(): ?string
    {
        return $this->attributes['catatan_umum'] ?? null;
    }

    public function getCatatanOrangtuaAttribute(): ?string
    {
        return $this->attributes['catatan_orang_tua'] ?? null;
    }
}
