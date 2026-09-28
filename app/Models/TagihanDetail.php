<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagihanDetail extends Model
{
    use HasFactory;

    protected $table = 'tagihan_detail';

    protected $fillable = [
        'tagihan_id',
        'kode_biaya_snapshot',
        'nama_biaya_snapshot',
        'kategori_snapshot',
        'nominal_snapshot',
        'jumlah',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'nominal_snapshot' => 'decimal:2',
            'jumlah' => 'integer',
            'subtotal' => 'decimal:2',
        ];
    }

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }
}
