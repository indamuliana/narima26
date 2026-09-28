<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterSeragam extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_seragam';

    protected $fillable = [
        'kode',
        'nama_jenis',
        'ukuran',
        'keterangan',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function getNamaSeragamAttribute(): string
    {
        return $this->nama_jenis ?? '';
    }
}
