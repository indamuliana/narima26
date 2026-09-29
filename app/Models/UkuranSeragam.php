<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UkuranSeragam extends Model
{
    use HasFactory;

    protected $table = 'ukuran_seragam';

    public const STATUS_PESAN_SEKARANG = 'PESAN_SEKARANG';
    public const STATUS_PESAN_NANTI = 'PESAN_NANTI';

    protected $fillable = [
        'calon_siswa_id',
        'jenis_seragam_id',
        'ukuran',
        'jumlah',
        'beli_di_sekolah',
        'status_pemesanan',
        'tahap_pemesanan',
        'tagihan_id',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'beli_di_sekolah' => 'boolean',
            'tahap_pemesanan' => 'integer',
        ];
    }

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    public function jenisSeragam(): BelongsTo
    {
        return $this->belongsTo(MasterSeragam::class, 'jenis_seragam_id');
    }

    public function seragam(): BelongsTo
    {
        return $this->belongsTo(MasterSeragam::class, 'jenis_seragam_id');
    }

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }

    public function isPesanSekarang(): bool
    {
        return $this->status_pemesanan === self::STATUS_PESAN_SEKARANG;
    }

    public function isPesanNanti(): bool
    {
        return $this->status_pemesanan === self::STATUS_PESAN_NANTI;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_pemesanan) {
            self::STATUS_PESAN_SEKARANG => 'Pesan Sekarang',
            self::STATUS_PESAN_NANTI => 'Pesan Nanti',
            default => 'Pesan Nanti',
        };
    }
}
