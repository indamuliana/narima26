<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataAkademik extends Model
{
    use HasFactory;

    protected $table = 'data_akademik';

    protected $fillable = [
        'calon_siswa_id',
        'nama_sekolah',
        'npsn',
        'nisn',
        'nilai_rata_rata',
        'nilai_bahasa_indonesia',
        'nilai_matematika',
        'nilai_bahasa_inggris',
        'nilai_ipa',
        'nilai_lainnya',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'nilai_rata_rata' => 'decimal:2',
            'nilai_bahasa_indonesia' => 'decimal:2',
            'nilai_matematika' => 'decimal:2',
            'nilai_bahasa_inggris' => 'decimal:2',
            'nilai_ipa' => 'decimal:2',
            'nilai_lainnya' => 'decimal:2',
        ];
    }

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }
}
