<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterBiaya extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_biaya';

    protected $fillable = [
        'kode_biaya',
        'nama_biaya',
        'kategori',
        'program_id',
        'gelombang_id',
        'nominal',
        'tipe_nominal',
        'wajib',
        'aktif',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'wajib' => 'boolean',
            'aktif' => 'boolean',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(MasterProgram::class, 'program_id');
    }

    public function gelombang(): BelongsTo
    {
        return $this->belongsTo(MasterGelombang::class, 'gelombang_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}
