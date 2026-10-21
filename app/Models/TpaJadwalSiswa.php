<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TpaJadwalSiswa extends Model
{
    protected $table = 'tpa_jadwal_siswa';

    protected $fillable = [
        'calon_siswa_id',
        'waktu_mulai',
        'waktu_selesai',
        'is_diizinkan',
        'catatan',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'is_diizinkan' => 'boolean',
    ];

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    /**
     * Cek apakah siswa saat ini boleh masuk ujian TPA.
     */
    public function isBolehMasukNow(): bool
    {
        if (! $this->is_diizinkan) {
            return false;
        }

        $now = Carbon::now();

        if ($this->waktu_mulai && $now->lt($this->waktu_mulai)) {
            return false;
        }

        if ($this->waktu_selesai && $now->gt($this->waktu_selesai)) {
            return false;
        }

        return true;
    }
}
