<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterSeragam extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_seragam';

    public const KLASTER_MPLS = 'MPLS';
    public const KLASTER_KBM = 'KBM';
    public const KLASTER_OPSIONAL = 'OPSIONAL';

    protected $fillable = [
        'kode',
        'nama_jenis',
        'ukuran',
        'wajib',
        'klaster',
        'urutan_klaster',
        'jenis_kelamin',
        'keterangan',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'wajib' => 'boolean',
            'aktif' => 'boolean',
            'urutan_klaster' => 'integer',
        ];
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeMpls($query)
    {
        return $query->where('klaster', self::KLASTER_MPLS);
    }

    public function scopeKbm($query)
    {
        return $query->where('klaster', self::KLASTER_KBM);
    }

    public function scopeOpsional($query)
    {
        return $query->where('klaster', self::KLASTER_OPSIONAL);
    }

    public function getNamaSeragamAttribute(): string
    {
        return $this->nama_jenis ?? '';
    }

    public function getNamaKlasterAttribute(): string
    {
        return match ($this->klaster) {
            self::KLASTER_MPLS => 'Perlengkapan Masuk Sekolah & MPLS',
            self::KLASTER_KBM => 'Seragam Harian KBM Reguler',
            self::KLASTER_OPSIONAL => 'Perlengkapan Tambahan (Opsional)',
            default => 'Seragam Sekolah',
        };
    }
}
