<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Inda Muliana',
                'email' => 'admin@wikrama.sch.id',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_ADMIN,
                'phone' => '081200000001',
                'is_active' => true,
            ],
            [
                'name' => 'Bendahara Keuangan',
                'email' => 'bendahara@wikrama.sch.id',
                'username' => 'bendahara',
                'password' => Hash::make('bendahara123'),
                'role' => User::ROLE_BENDAHARA,
                'phone' => '081200000002',
                'is_active' => true,
            ],
            [
                'name' => 'Tim Pewawancara',
                'email' => 'pewawancara@wikrama.sch.id',
                'username' => 'pewawancara',
                'password' => Hash::make('pewawancara123'),
                'role' => User::ROLE_PEWAWANCARA,
                'phone' => '081200000003',
                'is_active' => true,
            ],
            [
                'name' => 'Kepala Sekolah SMK Wikrama 1 Garut',
                'email' => 'kepsek@wikrama.sch.id',
                'username' => 'kepsek',
                'password' => Hash::make('kepsek123'),
                'role' => User::ROLE_KEPALA_SEKOLAH,
                'phone' => '081200000004',
                'is_active' => true,
            ],
            [
                'name' => 'Dewan Guru SMK Wikrama',
                'email' => 'guru@wikrama.sch.id',
                'username' => 'guru',
                'password' => Hash::make('guru123'),
                'role' => User::ROLE_GURU,
                'phone' => '081200000005',
                'is_active' => true,
            ],
            [
                'name' => 'Ahmad Fathoni (Calon Siswa)',
                'email' => 'siswa@wikrama.sch.id',
                'username' => '0012345678', // NISN
                'password' => Hash::make('siswa123'),
                'role' => User::ROLE_CALON_SISWA,
                'phone' => '081234567890',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
