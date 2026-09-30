<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    /**
     * Test login screen can be rendered.
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('NAMPI SPMB');
        $response->assertSee('SMK Wikrama 1 Garut');
    }

    /**
     * Test admin can authenticate using email and redirect to admin dashboard.
     */
    public function test_admin_can_authenticate_using_email(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin@wikrama.sch.id',
            'password' => 'admin123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test calon siswa can authenticate using NISN / username.
     */
    public function test_calon_siswa_can_authenticate_using_nisn_username(): void
    {
        $response = $this->post('/login', [
            'login' => '0012345678', // NISN
            'password' => 'siswa123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('calon-siswa.dashboard'));
    }

    /**
     * Test bendahara can authenticate using email.
     */
    public function test_bendahara_can_authenticate(): void
    {
        $response = $this->post('/login', [
            'login' => 'bendahara@wikrama.sch.id',
            'password' => 'bendahara123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('bendahara.dashboard'));
    }

    /**
     * Test pewawancara can authenticate using email.
     */
    public function test_pewawancara_can_authenticate(): void
    {
        $response = $this->post('/login', [
            'login' => 'pewawancara@wikrama.sch.id',
            'password' => 'pewawancara123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('pewawancara.dashboard'));
    }

    /**
     * Test kepala sekolah can authenticate using email.
     */
    public function test_kepala_sekolah_can_authenticate(): void
    {
        $response = $this->post('/login', [
            'login' => 'kepsek@wikrama.sch.id',
            'password' => 'kepsek123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('kepala-sekolah.dashboard'));
    }

    /**
     * Test users can not authenticate with invalid password.
     */
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin@wikrama.sch.id',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    /**
     * Test inactive user cannot authenticate.
     */
    public function test_inactive_user_cannot_authenticate(): void
    {
        $user = User::where('email', 'admin@wikrama.sch.id')->first();
        $user->update(['is_active' => false]);

        $response = $this->post('/login', [
            'login' => 'admin@wikrama.sch.id',
            'password' => 'admin123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    /**
     * Test users can logout via POST.
     */
    public function test_users_can_logout_via_post(): void
    {
        $user = User::where('email', 'admin@wikrama.sch.id')->first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /**
     * Test users can logout via GET request (e.g., clicking a link or typing URL).
     */
    public function test_users_can_logout_via_get(): void
    {
        $user = User::where('email', 'admin@wikrama.sch.id')->first();

        $response = $this->actingAs($user)->get('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /**
     * Test authenticated user accessing login screen sees active session banner and quick actions.
     */
    public function test_authenticated_user_can_access_login_screen_with_active_session_info(): void
    {
        $user = User::where('email', 'admin@wikrama.sch.id')->first();

        $response = $this->actingAs($user)->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sesi Aktif');
        $response->assertSee($user->name);
        $response->assertSee('Buka Dashboard');
        $response->assertSee('Keluar (Logout)');
    }

    /**
     * Test authenticated user can switch account directly from login form without manual pre-logout.
     */
    public function test_authenticated_user_can_switch_account_directly(): void
    {
        $admin = User::where('email', 'admin@wikrama.sch.id')->first();

        // Admin is initially logged in
        $response = $this->actingAs($admin)->post('/login', [
            'login' => 'bendahara@wikrama.sch.id',
            'password' => 'bendahara123',
        ]);

        $this->assertAuthenticated();
        $this->assertEquals('bendahara@wikrama.sch.id', auth()->user()->email);
        $response->assertRedirect(route('bendahara.dashboard'));
    }

    /**
     * Test universal /dashboard route redirects to the correct role dashboard.
     */
    public function test_universal_dashboard_redirects_to_role_dashboard(): void
    {
        $admin = User::where('email', 'admin@wikrama.sch.id')->first();
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertRedirect(route('admin.dashboard'));

        $calonSiswa = User::where('username', '0012345678')->first();
        $response = $this->actingAs($calonSiswa)->get('/dashboard');
        $response->assertRedirect(route('calon-siswa.dashboard'));
    }
}

