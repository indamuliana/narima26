<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WawancaraDetail extends Model
{
    use HasFactory;

    protected $table = 'wawancara_detail';

    public const WARNA_HIJAU = 'HIJAU';
    public const WARNA_ORANYE = 'ORANYE';
    public const WARNA_MERAH = 'MERAH';

    protected $fillable = [
        'wawancara_id',
        'kriteria_id',
        'indikator',
        'nilai',
        'warna',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
        ];
    }

    public function wawancara(): BelongsTo
    {
        return $this->belongsTo(Wawancara::class, 'wawancara_id');
    }

    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(MasterKriteriaWawancara::class, 'kriteria_id');
    }
}
