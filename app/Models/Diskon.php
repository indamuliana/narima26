<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diskon extends Model
{
    use HasFactory;

    protected $table = 'diskon';

    protected $fillable = [
        'calon_siswa_id',
        'jenis_diskon',
        'metode_diskon',
        'nilai_diskon',
        'nominal_potongan',
        'alasan',
        'keterangan',
        'diberikan_oleh',
        'disetujui_oleh',
        'diberikan_at',
    ];

    protected function casts(): array
    {
        return [
            'nilai_diskon' => 'decimal:2',
            'nominal_potongan' => 'decimal:2',
            'diberikan_at' => 'datetime',
        ];
    }

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    public function diberikanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diberikan_oleh');
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
