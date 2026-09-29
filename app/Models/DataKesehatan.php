<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataKesehatan extends Model
{
    protected $table = 'data_kesehatan';

    protected $fillable = [
        'calon_siswa_id',
        'tinggi_badan',
        'berat_badan',
        'golongan_darah',
        'buta_warna',
        'penyakit_pernah_diderita',
        'penyakit_pernah_diderita_lainnya',
        'penyakit_sedang_diderita',
        'penyakit_sedang_diderita_lainnya',
        'kesehatan_mata',
        'jenis_alergi',
    ];

    public function calonSiswa()
    {
        return $this->belongsTo(CalonSiswa::class);
    }
}
