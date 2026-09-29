<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterSekolahAsal extends Model
{
    use HasFactory;

    protected $table = 'master_sekolah_asal';

    protected $fillable = [
        'id',
        'npsn',
        'nama_sekolah',
        'status',
        'jenis',
        'provinsi',
        'kokab',
        'kecamatan',
        // Backward-compatibility fillable aliases
        'kabupaten_kota',
    ];

    public function calonSiswa(): HasMany
    {
        return $this->hasMany(CalonSiswa::class, 'asal_sekolah_id');
    }

    public function scopeAktif($query)
    {
        return $query;
    }

    public function getKabupatenKotaAttribute(): ?string
    {
        return $this->kokab;
    }

    public function setKabupatenKotaAttribute($value): void
    {
        $this->attributes['kokab'] = $value;
    }

    public function getKabupatenAttribute(): ?string
    {
        return $this->kokab;
    }

    public function getAktifAttribute(): bool
    {
        return true;
    }

    public function getAlamatAttribute(): ?string
    {
        return null;
    }
}
