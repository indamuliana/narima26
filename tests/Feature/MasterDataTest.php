<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\MasterBiaya;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterKriteriaWawancara;
use App\Models\MasterPekerjaan;
use App\Models\MasterProgram;
use App\Models\MasterSekolahAsal;
use App\Models\MasterSeragam;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            UserSeeder::class,
            MasterDataSeeder::class,
        ]);
    }

    /**
     * Test master program is seeded and has soft deletes.
     */
    public function test_master_program_is_seeded_and_supports_soft_deletes(): void
    {
        $this->assertDatabaseHas('master_program', ['kode' => 'REG', 'nama' => 'Reguler']);
        $this->assertDatabaseHas('master_program', ['kode' => 'UGG', 'nama' => 'Unggulan']);

        $program = MasterProgram::where('kode', 'REG')->first();
        $program->delete();

        $this->assertSoftDeleted('master_program', ['id' => $program->id]);
        $this->assertEquals(1, MasterProgram::count());
        $this->assertEquals(2, MasterProgram::withTrashed()->count());
    }

    /**
     * Test master jurusan is seeded with 4 Wikrama programs.
     */
    public function test_master_jurusan_contains_four_departments(): void
    {
        $this->assertDatabaseHas('master_jurusan', ['kode' => 'TJKT']);
        $this->assertDatabaseHas('master_jurusan', ['kode' => 'PPLG']);
        $this->assertDatabaseHas('master_jurusan', ['kode' => 'PEMASARAN']);
        $this->assertDatabaseHas('master_jurusan', ['kode' => 'PERHOTELAN']);
        $this->assertEquals(4, MasterJurusan::count());
    }

    /**
     * Test master gelombang contains active gelombang 1.
     */
    public function test_master_gelombang_has_active_wave(): void
    {
        $this->assertDatabaseHas('master_gelombang', ['kode' => 'GEL1', 'aktif' => true]);
        $activeWave = MasterGelombang::aktif()->first();
        $this->assertNotNull($activeWave);
        $this->assertEquals('GEL1', $activeWave->kode);
    }

    /**
     * Test master biaya contains seleksi and daftar ulang components.
     */
    public function test_master_biaya_is_seeded_with_expected_categories(): void
    {
        $this->assertDatabaseHas('master_biaya', ['kode_biaya' => 'BIAYA-SEL', 'kategori' => 'seleksi', 'nominal' => 200000]);
        $this->assertDatabaseHas('master_biaya', ['kode_biaya' => 'BIAYA-DSP', 'kategori' => 'daftar_ulang', 'nominal' => 3000000]);
        $this->assertDatabaseHas('master_biaya', ['kode_biaya' => 'BIAYA-SPP', 'kategori' => 'daftar_ulang', 'nominal' => 450000]);
    }

    /**
     * Test factories create valid models.
     */
    public function test_factories_create_models_correctly(): void
    {
        $customProgram = MasterProgram::factory()->create();
        $this->assertNotNull($customProgram->id);
        $this->assertDatabaseHas('master_program', ['id' => $customProgram->id]);

        $customJurusan = MasterJurusan::factory()->create();
        $this->assertNotNull($customJurusan->id);
        $this->assertDatabaseHas('master_jurusan', ['id' => $customJurusan->id]);

        $program = MasterProgram::where('kode', 'PPLG')->first() ?? MasterProgram::first();
        $jurusan = MasterJurusan::where('kode', 'PPLG')->first();
        $gelombang = MasterGelombang::first();

        $student = CalonSiswa::factory()->create([
            'program_id' => $program->id,
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);

        $this->assertNotNull($student->id);
        $this->assertEquals($program->id, $student->program->id);
        $this->assertEquals($jurusan->id, $student->jurusan->id);
        $this->assertEquals($gelombang->id, $student->gelombang->id);
    }
}
