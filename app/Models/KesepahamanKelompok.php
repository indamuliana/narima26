<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KesepahamanKelompok extends Model
{
    protected $fillable = ['program_id', 'kode', 'judul', 'urutan'];
    
    public function program()
    {
        return $this->belongsTo(KesepahamanProgram::class, 'program_id');
    }
    
    public function poins()
    {
        return $this->hasMany(KesepahamanPoin::class, 'kelompok_id')->orderBy('urutan');
    }
}
