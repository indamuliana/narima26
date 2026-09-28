<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterDesa extends Model
{
    use HasFactory;

    protected $table = 'master_desa';

    protected $fillable = [
        'kecamatan_id',
        'kode',
        'nama',
        'kode_pos',
    ];

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(MasterKecamatan::class, 'kecamatan_id');
    }
}
