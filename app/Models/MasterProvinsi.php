<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterProvinsi extends Model
{
    use HasFactory;

    protected $table = 'master_provinsi';

    protected $fillable = [
        'kode',
        'nama',
    ];

    public function kabupaten(): HasMany
    {
        return $this->hasMany(MasterKabupaten::class, 'provinsi_id');
    }
}
