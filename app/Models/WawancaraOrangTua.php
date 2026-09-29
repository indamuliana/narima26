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
        'infaq_rutin_bulanan',
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
        'infaq_rutin_bulanan' => 'integer',
    ];

    public function setInfaqRutinBulananAttribute($value): void
    {
        if (is_null($value) || $value === '') {
            $this->attributes['infaq_rutin_bulanan'] = null;
            return;
        }

        $clean = preg_replace('/[^0-9]/', '', (string) $value);
        $this->attributes['infaq_rutin_bulanan'] = $clean !== '' ? (int) $clean : null;
    }

    public function getFormattedInfaqRutinBulananAttribute(): ?string
    {
        return $this->infaq_rutin_bulanan !== null
            ? number_format($this->infaq_rutin_bulanan, 0, ',', '.')
            : null;
    }

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class);
    }

    public function pewawancara(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pewawancara_id');
    }
}
