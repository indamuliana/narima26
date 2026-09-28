<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';

    public const STATUS_BELUM_LUNAS = 'BELUM_LUNAS';
    public const STATUS_CICILAN = 'CICILAN';
    public const STATUS_LUNAS = 'LUNAS';

    protected $fillable = [
        'calon_siswa_id',
        'nomor_tagihan',
        'program_snapshot',
        'gelombang_snapshot',
        'total_bruto',
        'total_diskon',
        'total_netto',
        'status',
        'diskon_id',
    ];

    protected function casts(): array
    {
        return [
            'total_bruto' => 'decimal:2',
            'total_diskon' => 'decimal:2',
            'total_netto' => 'decimal:2',
        ];
    }

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    public function diskon(): BelongsTo
    {
        return $this->belongsTo(Diskon::class, 'diskon_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(TagihanDetail::class, 'tagihan_id');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(PembayaranDaftarUlang::class, 'tagihan_id');
    }
}
