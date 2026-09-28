<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterKecamatan extends Model
{
    use HasFactory;

    protected $table = 'master_kecamatan';

    protected $fillable = [
        'kabupaten_id',
        'kode',
        'nama',
    ];

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(MasterKabupaten::class, 'kabupaten_id');
    }

    public function desa(): HasMany
    {
        return $this->hasMany(MasterDesa::class, 'kecamatan_id');
    }
}
