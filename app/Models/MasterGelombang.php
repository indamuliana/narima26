<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterGelombang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_gelombang';

    protected $fillable = [
        'kode',
        'nama',
        'periode_mulai',
        'periode_selesai',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'periode_mulai' => 'date',
            'periode_selesai' => 'date',
            'aktif' => 'boolean',
        ];
    }

    public function calonSiswa(): HasMany
    {
        return $this->hasMany(CalonSiswa::class, 'gelombang_id');
    }

    public function biaya(): HasMany
    {
        return $this->hasMany(MasterBiaya::class, 'gelombang_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function getNamaGelombangAttribute(): string
    {
        return $this->nama;
    }

    public function getTanggalMulaiAttribute()
    {
        return $this->periode_mulai;
    }

    public function getTanggalSelesaiAttribute()
    {
        return $this->periode_selesai;
    }

    public function getTahunAjaranAttribute(): string
    {
        return '2027/2028';
    }
}
