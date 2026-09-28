<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UkuranSeragam extends Model
{
    use HasFactory;

    protected $table = 'ukuran_seragam';

    protected $fillable = [
        'calon_siswa_id',
        'jenis_seragam_id',
        'ukuran',
        'jumlah',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
        ];
    }

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    public function jenisSeragam(): BelongsTo
    {
        return $this->belongsTo(MasterSeragam::class, 'jenis_seragam_id');
    }

    public function seragam(): BelongsTo
    {
        return $this->belongsTo(MasterSeragam::class, 'jenis_seragam_id');
    }
}
