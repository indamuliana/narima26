<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiRapor extends Model
{
    use HasFactory;

    protected $table = 'nilai_rapor';

    protected $fillable = [
        'calon_siswa_id',
        // Matematika
        'mtk_sem1', 'mtk_sem2', 'mtk_sem3', 'mtk_sem4', 'mtk_sem5',
        // Bahasa Indonesia
        'ind_sem1', 'ind_sem2', 'ind_sem3', 'ind_sem4', 'ind_sem5',
        // Bahasa Inggris
        'eng_sem1', 'eng_sem2', 'eng_sem3', 'eng_sem4', 'eng_sem5',
        // Pendidikan Agama
        'pai_sem1', 'pai_sem2', 'pai_sem3', 'pai_sem4', 'pai_sem5',
    ];

    protected function casts(): array
    {
        $casts = [];
        foreach (['mtk', 'ind', 'eng', 'pai'] as $prefix) {
            for ($i = 1; $i <= 5; $i++) {
                $casts["{$prefix}_sem{$i}"] = 'decimal:2';
            }
        }
        return $casts;
    }

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    /**
     * Get all grade values as a structured array for the view.
     * Returns: ['mtk' => [1 => val, 2 => val, ...], 'ind' => [...], ...]
     */
    public function getMatrixAttribute(): array
    {
        $matrix = [];
        foreach (['mtk', 'ind', 'eng', 'pai'] as $prefix) {
            for ($i = 1; $i <= 5; $i++) {
                $matrix[$prefix][$i] = $this->{"{$prefix}_sem{$i}"};
            }
        }
        return $matrix;
    }
}
