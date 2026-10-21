<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TpaSoal extends Model
{
    protected $table = 'tpa_soal';

    protected $fillable = [
        'babak',
        'nomor_urut',
        'pertanyaan',
        'gambar_path',
        'bobot',
        'kunci_jawaban',
        'opsi_jawaban',
        'is_active',
    ];

    protected $casts = [
        'nomor_urut' => 'integer',
        'bobot' => 'integer',
        'opsi_jawaban' => 'array',
        'is_active' => 'boolean',
    ];

    public function jawabanSiswa(): HasMany
    {
        return $this->hasMany(TpaJawabanSiswa::class, 'tpa_soal_id');
    }

    public function scopeLiterasi(Builder $query): Builder
    {
        return $query->where('babak', 'literasi');
    }

    public function scopeNumerasi(Builder $query): Builder
    {
        return $query->where('babak', 'numerasi');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
