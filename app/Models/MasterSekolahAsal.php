<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterSekolahAsal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_sekolah_asal';

    protected $fillable = [
        'npsn',
        'nama_sekolah',
        'alamat',
        'kecamatan',
        'kabupaten_kota',
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
        return $this->hasMany(CalonSiswa::class, 'asal_sekolah_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}
