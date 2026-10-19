<?php

namespace Tests\Feature;

use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalonSiswaFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected MasterProgram $programReguler;
    protected MasterProgram $programUnggulan;
    protected MasterJurusan $jurusan;
    protected MasterGelombang $gelombang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        $this->adminUser = User::where('role', User::ROLE_ADMIN)->first();
        $this->programReguler = MasterProgram::firstOrCreate(
            ['kode' => 'REG'],
            ['nama' => 'Reguler', 'aktif' => true]
        );
        $this->programUnggulan = MasterProgram::firstOrCreate(
            ['kode' => 'UGG'],
            ['nama' => 'Unggulan', 'aktif' => true]
        );
        $this->jurusan = MasterJurusan::first();
        $this->gelombang = MasterGelombang::first();
    }

    public function test_filter_ui_displays_program_and_gender_selects(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.index'));

        $response->assertStatus(200);
        $response->assertSee('name="program_id"', false);
        $response->assertSee('Semua Program');
        $response->assertSee('name="jenis_kelamin"', false);
        $response->assertSee('Semua Gender');
        $response->assertSee('Laki-laki (L)');
        $response->assertSee('Perempuan (P)');
    }

    public function test_filter_by_program(): void
    {
        $user1 = User::factory()->create(['role' => User::ROLE_CALON_SISWA]);
        $csReguler = CalonSiswa::factory()->create([
            'user_id' => $user1->id,
            'nama_lengkap' => 'Ahmad Reguler',
            'nomor_pendaftaran' => '26REG0001',
            'program_id' => $this->programReguler->id,
            'jenis_kelamin' => 'L',
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $this->gelombang->id,
        ]);

        $user2 = User::factory()->create(['role' => User::ROLE_CALON_SISWA]);
        $csUnggulan = CalonSiswa::factory()->create([
            'user_id' => $user2->id,
            'nama_lengkap' => 'Siti Unggulan',
            'nomor_pendaftaran' => '26UGG0002',
            'program_id' => $this->programUnggulan->id,
            'jenis_kelamin' => 'P',
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $this->gelombang->id,
        ]);

        // Filter Unggulan
        $respUnggulan = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.index', ['program_id' => $this->programUnggulan->id]));
        $respUnggulan->assertStatus(200);
        $respUnggulan->assertSee('Siti Unggulan');
        $respUnggulan->assertDontSee('Ahmad Reguler');

        // Filter Reguler
        $respReguler = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.index', ['program_id' => $this->programReguler->id]));
        $respReguler->assertStatus(200);
        $respReguler->assertSee('Ahmad Reguler');
        $respReguler->assertDontSee('Siti Unggulan');
    }

    public function test_filter_by_gender(): void
    {
        $user1 = User::factory()->create(['role' => User::ROLE_CALON_SISWA]);
        CalonSiswa::factory()->create([
            'user_id' => $user1->id,
            'nama_lengkap' => 'Doni Laki',
            'nomor_pendaftaran' => '26REG0010',
            'program_id' => $this->programReguler->id,
            'jenis_kelamin' => 'L',
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $this->gelombang->id,
        ]);

        $user2 = User::factory()->create(['role' => User::ROLE_CALON_SISWA]);
        CalonSiswa::factory()->create([
            'user_id' => $user2->id,
            'nama_lengkap' => 'Rina Cewek',
            'nomor_pendaftaran' => '26REG0011',
            'program_id' => $this->programReguler->id,
            'jenis_kelamin' => 'P',
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $this->gelombang->id,
        ]);

        // Filter Laki-laki
        $respL = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.index', ['jenis_kelamin' => 'L']));
        $respL->assertStatus(200);
        $respL->assertSee('Doni Laki');
        $respL->assertDontSee('Rina Cewek');

        // Filter Perempuan
        $respP = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.index', ['jenis_kelamin' => 'P']));
        $respP->assertStatus(200);
        $respP->assertSee('Rina Cewek');
        $respP->assertDontSee('Doni Laki');
    }
}
