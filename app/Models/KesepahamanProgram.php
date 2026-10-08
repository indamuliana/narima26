<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KesepahamanProgram extends Model
{
    protected $fillable = ['kode', 'nama', 'tahun_pelajaran', 'aktif'];
    
    public function kelompoks()
    {
        return $this->hasMany(KesepahamanKelompok::class, 'program_id')->orderBy('urutan');
    }
}
