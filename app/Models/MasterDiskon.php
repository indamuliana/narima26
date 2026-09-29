<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDiskon extends Model
{
    use HasFactory;

    protected $table = 'master_diskon';

    protected $fillable = [
        'nama_diskon',
        'metode_diskon',
        'nilai_diskon',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'nilai_diskon' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
