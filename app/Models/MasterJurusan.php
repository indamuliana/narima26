<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterJurusan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_jurusan';

    protected $fillable = [
        'kode',
        'nama',
        'keterangan',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    public function calonSiswa(): HasMany
    {
        return $this->hasMany(CalonSiswa::class, 'jurusan_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function getNamaJurusanAttribute(): string
    {
        return $this->nama;
    }

    public function getKodeJurusanAttribute(): string
    {
        return $this->kode;
    }
}
