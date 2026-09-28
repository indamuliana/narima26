<?php

namespace Database\Factories;

use App\Models\MasterProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

class MasterProgramFactory extends Factory
{
    protected $model = MasterProgram::class;

    public function definition(): array
    {
        return [
            'kode' => fake()->unique()->lexify('PROG-???'),
            'nama' => fake()->words(2, true),
            'keterangan' => fake()->sentence(),
            'aktif' => true,
        ];
    }
}
