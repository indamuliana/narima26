<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterKriteriaWawancara extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_kriteria_wawancara';

    protected $fillable = [
        'kode',
        'nama_kriteria',
        'jenis_penilaian',
        'urutan',
        'aktif',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
            'aktif' => 'boolean',
        ];
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}
