<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KesepahamanPoin extends Model
{
    protected $fillable = ['kelompok_id', 'kode_poin', 'nomor', 'uraian', 'urutan'];
    
    public function kelompok()
    {
        return $this->belongsTo(KesepahamanKelompok::class, 'kelompok_id');
    }
}
