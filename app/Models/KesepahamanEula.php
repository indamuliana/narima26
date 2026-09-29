<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KesepahamanEula extends Model
{
    use HasFactory;

    protected $table = 'kesepahaman_eula';

    protected $fillable = [
        'calon_siswa_id',
        'versi_dokumen',
        'program_snapshot',
        'isi_dokumen_atau_referensi_dokumen',
        'poin_disetujui',
        'klausul_snapshot',
        'setuju',
        'agreed_at',
        'agreed_by',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'setuju' => 'boolean',
            'agreed_at' => 'datetime',
            'poin_disetujui' => 'array',
            'klausul_snapshot' => 'array',
        ];
    }

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    public function agreedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agreed_by');
    }
}
