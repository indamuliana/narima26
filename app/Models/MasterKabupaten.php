<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterKabupaten extends Model
{
    use HasFactory;

    protected $table = 'master_kabupaten';

    protected $fillable = [
        'provinsi_id',
        'kode',
        'nama',
    ];

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(MasterProvinsi::class, 'provinsi_id');
    }

    public function kecamatan(): HasMany
    {
        return $this->hasMany(MasterKecamatan::class, 'kabupaten_id');
    }
}
