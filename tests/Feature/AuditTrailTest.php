<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    /**
     * Test login event creates an audit trail entry.
     */
    public function test_login_event_creates_audit_trail_entry(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $this->post('/login', [
            'login' => 'admin@wikrama.sch.id',
            'password' => 'admin123',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'auth',
            'description' => "Pengguna {$admin->name} (Administrator) berhasil login.",
        ]);
    }

    /**
     * Test logout event creates an audit trail entry.
     */
    public function test_logout_event_creates_audit_trail_entry(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $this->actingAs($admin)->post('/logout');

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'auth',
            'description' => "Pengguna {$admin->name} logout.",
        ]);
    }
}
