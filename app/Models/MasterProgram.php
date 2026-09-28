<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_program';

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
        return $this->hasMany(CalonSiswa::class, 'program_id');
    }

    public function biaya(): HasMany
    {
        return $this->hasMany(MasterBiaya::class, 'program_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function getNamaProgramAttribute(): string
    {
        return $this->nama;
    }

    public function getKodeProgramAttribute(): string
    {
        return $this->kode;
    }
}
