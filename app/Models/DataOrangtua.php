<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataOrangtua extends Model
{
    use HasFactory;

    protected $table = 'data_orangtua';

    protected $fillable = [
        'calon_siswa_id',
        // Ayah
        'nama_ayah',
        'nik_ayah',
        'tahun_lahir_ayah',
        'pekerjaan_ayah_id',
        'penghasilan_ayah',
        'pendidikan_ayah',
        'no_hp_ayah',
        'alamat_ayah',
        // Ibu
        'nama_ibu',
        'nik_ibu',
        'tahun_lahir_ibu',
        'pekerjaan_ibu_id',
        'penghasilan_ibu',
        'pendidikan_ibu',
        'no_hp_ibu',
        'alamat_ibu',
        // Wali
        'nama_wali',
        'hubungan_wali',
        'pekerjaan_wali_id',
        'penghasilan_wali',
        'no_hp_wali',
        'alamat_wali',
    ];

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    public function pekerjaanAyah(): BelongsTo
    {
        return $this->belongsTo(MasterPekerjaan::class, 'pekerjaan_ayah_id');
    }

    public function pekerjaanIbu(): BelongsTo
    {
        return $this->belongsTo(MasterPekerjaan::class, 'pekerjaan_ibu_id');
    }

    public function pekerjaanWali(): BelongsTo
    {
        return $this->belongsTo(MasterPekerjaan::class, 'pekerjaan_wali_id');
    }
}
