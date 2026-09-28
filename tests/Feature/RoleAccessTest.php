<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    /**
     * Unauthenticated users cannot access protected dashboards.
     */
    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $dashboards = [
            '/admin/dashboard',
            '/bendahara/dashboard',
            '/pewawancara/dashboard',
            '/kepala-sekolah/dashboard',
            '/calon-siswa/dashboard',
        ];

        foreach ($dashboards as $url) {
            $response = $this->get($url);
            $response->assertRedirect(route('login'));
        }
    }

    /**
     * Calon siswa cannot access admin dashboard.
     */
    public function test_calon_siswa_cannot_access_admin_dashboard(): void
    {
        $student = User::where('role', User::ROLE_CALON_SISWA)->first();

        $response = $this->actingAs($student)->get('/admin/dashboard');

        // Middleware redirects unauthorized role to their own dashboard with error alert
        $response->assertRedirect(route('calon-siswa.dashboard'));
        $response->assertSessionHas('error');
    }

    /**
     * Bendahara cannot access pewawancara dashboard.
     */
    public function test_bendahara_cannot_access_pewawancara_dashboard(): void
    {
        $bendahara = User::where('role', User::ROLE_BENDAHARA)->first();

        $response = $this->actingAs($bendahara)->get('/pewawancara/dashboard');

        $response->assertRedirect(route('bendahara.dashboard'));
        $response->assertSessionHas('error');
    }

    /**
     * Admin can access admin dashboard.
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Panel Administrator');
    }

    /**
     * Bendahara can access bendahara dashboard.
     */
    public function test_bendahara_can_access_bendahara_dashboard(): void
    {
        $bendahara = User::where('role', User::ROLE_BENDAHARA)->first();

        $response = $this->actingAs($bendahara)->get('/bendahara/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Panel Keuangan');
    }
}
