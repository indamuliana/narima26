<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WawancaraOrangTua extends Model
{
    use HasFactory;

    protected $table = 'wawancara_orang_tua';

    protected $fillable = [
        'calon_siswa_id',
        'pewawancara_id',
        'nama_petugas',
        'tanggal_wawancara',
        'status',
        'nama_diwawancarai',
        'hubungan_dengan_siswa',
        'tinggal_bersama',
        'jarak_rumah',
        'transportasi',
        'penanggung_jawab_belajar',
        'info_wikrama_dari',
        'baca_quran',
        'hafalan_quran',
        'minat_program',
        'minat_jurusan',
        'alasan_masuk_wikrama',
        'alasan_pilih_program_jurusan',
        'kebiasaan_tempat_tidur',
        'hobi',
        'cita_cita',
        'merokok',
        'penyakit_diderita',
        'alergi',
        'hal_perhatian_ortu',
        'kesan_pewawancara',
        'catatan_tambahan',
    ];

    protected $casts = [
        'tanggal_wawancara' => 'date',
    ];

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class);
    }

    public function pewawancara(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pewawancara_id');
    }
}
