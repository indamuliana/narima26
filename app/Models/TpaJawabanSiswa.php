<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TpaJawabanSiswa extends Model
{
    protected $table = 'tpa_jawaban_siswa';

    protected $fillable = [
        'tpa_sesi_siswa_id',
        'tpa_soal_id',
        'jawaban_dipilih',
        'is_ragu',
        'is_benar',
        'poin_diperoleh',
    ];

    protected $casts = [
        'is_ragu' => 'boolean',
        'is_benar' => 'boolean',
        'poin_diperoleh' => 'decimal:2',
    ];

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(TpaSesiSiswa::class, 'tpa_sesi_siswa_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(TpaSoal::class, 'tpa_soal_id');
    }
}
