<?php

namespace Database\Factories;

use App\Models\CalonSiswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalonSiswaFactory extends Factory
{
    protected $model = CalonSiswa::class;

    public function definition(): array
    {
        return [
            'nomor_pendaftaran' => '26AAY' . fake()->unique()->numerify('####'),
            'user_id' => User::factory(),
            'nisn' => fake()->unique()->numerify('00########'),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'nama_lengkap' => fake()->name(),
            'nama_panggilan' => fake()->firstName(),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->dateTimeBetween('-17 years', '-14 years')->format('Y-m-d'),
            'nik' => fake()->numerify('320501##########'),
            'no_kk' => fake()->numerify('320501##########'),
            'agama' => 'Islam',
            'alamat_lengkap' => fake()->address(),
            'no_hp_siswa' => fake()->numerify('08##########'),
            'no_hp_ayah' => fake()->numerify('08##########'),
            'no_hp_ibu' => fake()->numerify('08##########'),
            'email' => fake()->unique()->safeEmail(),
            'status_spmb' => 'REGISTRASI',
            'status_data' => 'BELUM_LENGKAP',
        ];
    }
}
