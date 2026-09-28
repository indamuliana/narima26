<?php

namespace Database\Factories;

use App\Models\MasterJurusan;
use Illuminate\Database\Eloquent\Factories\Factory;

class MasterJurusanFactory extends Factory
{
    protected $model = MasterJurusan::class;

    public function definition(): array
    {
        return [
            'kode' => fake()->unique()->lexify('JUR-???'),
            'nama' => fake()->words(3, true),
            'keterangan' => fake()->sentence(),
            'aktif' => true,
        ];
    }
}
