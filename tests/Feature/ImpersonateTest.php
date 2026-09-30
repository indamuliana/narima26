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

class ImpersonateTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $siswaUser;
    protected User $guruUser;
    protected CalonSiswa $calonSiswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        $this->adminUser = User::where('role', User::ROLE_ADMIN)->first();
        $this->siswaUser = User::where('role', User::ROLE_CALON_SISWA)->first();
        $this->guruUser = User::where('role', User::ROLE_GURU)->first();

        $program = MasterProgram::first();
        $jurusan = MasterJurusan::first();
        $gelombang = MasterGelombang::first();

        $this->calonSiswa = CalonSiswa::factory()->create([
            'user_id' => $this->siswaUser->id,
            'nomor_pendaftaran' => '26AAY0999',
            'nisn' => '0011223344',
            'nama_lengkap' => 'Muhammad Rizky',
            'status_spmb' => SpmbStatus::MELENGKAPI_DATA,
            'program_id' => $program->id,
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);
    }

    public function test_admin_can_impersonate_calon_siswa(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.calon-siswa.impersonate', $this->calonSiswa));

        $response->assertRedirect(route('calon-siswa.dashboard'));
        $this->assertAuthenticatedAs($this->siswaUser);
        $this->assertEquals($this->adminUser->id, session('impersonate_admin_id'));

        // Visit student dashboard and verify impersonation banner is displayed
        $dashboardResponse = $this->withSession(['impersonate_admin_id' => $this->adminUser->id])
            ->get(route('calon-siswa.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Mode Impersonasi Aktif');
        $dashboardResponse->assertSee('Kembali ke Akun Admin');
    }

    public function test_admin_can_leave_impersonation_and_return_to_admin_session(): void
    {
        // Simulate impersonation session
        $response = $this->actingAs($this->siswaUser)
            ->withSession([
                'impersonate_admin_id' => $this->adminUser->id,
                'impersonated_calon_siswa_id' => $this->calonSiswa->id,
            ])
            ->post(route('impersonate.leave'));

        $response->assertRedirect(route('admin.calon-siswa.show', $this->calonSiswa));
        $this->assertAuthenticatedAs($this->adminUser);
        $this->assertNull(session('impersonate_admin_id'));
    }

    public function test_non_admin_cannot_impersonate(): void
    {
        $response = $this->actingAs($this->guruUser)
            ->post(route('admin.calon-siswa.impersonate', $this->calonSiswa));

        $response->assertRedirect(route($this->guruUser->getDashboardRoute()));
        $this->assertAuthenticatedAs($this->guruUser);
    }
}
