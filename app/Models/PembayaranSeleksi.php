<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranSeleksi extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_seleksi';

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_DIVERIFIKASI = 'DIVERIFIKASI';
    public const STATUS_DITOLAK = 'DITOLAK';

    protected $fillable = [
        'calon_siswa_id',
        'nominal_tagihan',
        'nominal_dibayar',
        'tanggal_bayar',
        'metode_bayar',
        'bank_pengirim',
        'nama_pengirim',
        'nomor_referensi',
        'bukti_transfer_path',
        'status',
        'catatan_bendahara',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'nominal_tagihan' => 'decimal:2',
            'nominal_dibayar' => 'decimal:2',
            'tanggal_bayar' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isDiverifikasi(): bool
    {
        return $this->status === self::STATUS_DIVERIFIKASI;
    }

    public function isDitolak(): bool
    {
        return $this->status === self::STATUS_DITOLAK;
    }
}
