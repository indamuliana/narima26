<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WawancaraSiswa extends Model
{
    use HasFactory;

    protected $table = 'wawancara_siswa';

    protected $fillable = [
        'calon_siswa_id',
        'pewawancara_id',
        'nama_petugas',
        'tanggal_wawancara',
        'status',
        'tinggal_bersama',
        'penanggung_jawab_belajar',
        'info_wikrama_dari',
        'kesan_siswa_aktif',
        'baca_quran',
        'hafalan_quran',
        'alasan_masuk_wikrama',
        'alasan_pilih_program',
        'alasan_pilih_jurusan',
        'aktivitas_rutin',
        'merokok',
        'penyakit_menahun',
        'kondisi_kesehatan',
        'disabilitas',
        'kerapihan_rambut',
        'kerapihan_seragam',
        'status_pendengaran',
        'status_penglihatan',
        'mata_kiri',
        'mata_kanan',
        'status_penglihatan_lainnya',
        'alergi',
        'hal_perhatian_khusus',
        'kesan_pewawancara',
        'catatan_pewawancara',
        'rekomendasi',
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
