<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementAndGuruRoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guru;
    protected User $bendahara;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::where('role', User::ROLE_ADMIN)->first();
        $this->guru = User::where('role', User::ROLE_GURU)->first();
        $this->bendahara = User::where('role', User::ROLE_BENDAHARA)->first();
    }

    public function test_admin_can_view_user_management_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna Sistem');
        $response->assertSee('Dewan Guru SMK Wikrama');
        $response->assertSee('Bendahara Keuangan');
    }

    public function test_admin_can_view_create_user_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Pengguna Baru');
        $response->assertSee('Guru');
        $response->assertSee('Bendahara');
        $response->assertSee('Kepala Sekolah');
        $response->assertSee('Pewawancara');
    }

    public function test_admin_can_create_bendahara_account(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Staf Bendahara Baru',
            'email' => 'bendahara.baru@wikrama.sch.id',
            'username' => 'bendahara2',
            'phone' => '081234567899',
            'role' => User::ROLE_BENDAHARA,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'bendahara.baru@wikrama.sch.id',
            'role' => User::ROLE_BENDAHARA,
            'username' => 'bendahara2',
        ]);
    }

    public function test_admin_can_create_kepala_sekolah_account(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Wakil Kepala Sekolah',
            'email' => 'wakasek@wikrama.sch.id',
            'username' => 'wakasek',
            'role' => User::ROLE_KEPALA_SEKOLAH,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'wakasek@wikrama.sch.id',
            'role' => User::ROLE_KEPALA_SEKOLAH,
        ]);
    }

    public function test_admin_can_create_pewawancara_account(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Pewawancara Bahasa',
            'email' => 'pewawancara.bahasa@wikrama.sch.id',
            'username' => 'pewawancara_bahasa',
            'role' => User::ROLE_PEWAWANCARA,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'pewawancara.bahasa@wikrama.sch.id',
            'role' => User::ROLE_PEWAWANCARA,
        ]);
    }

    public function test_admin_can_create_guru_account(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Guru Produktif RPL',
            'email' => 'guru.rpl@wikrama.sch.id',
            'username' => 'gururpl',
            'role' => User::ROLE_GURU,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'guru.rpl@wikrama.sch.id',
            'role' => User::ROLE_GURU,
        ]);
    }

    public function test_admin_can_edit_and_update_user(): void
    {
        $targetUser = User::factory()->create([
            'name' => 'Target Guru',
            'email' => 'target.guru@wikrama.sch.id',
            'role' => User::ROLE_GURU,
        ]);

        $editResponse = $this->actingAs($this->admin)->get(route('admin.users.edit', $targetUser));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Edit Akun Pengguna');

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.users.update', $targetUser), [
            'name' => 'Target Guru Updated',
            'email' => 'target.guru.updated@wikrama.sch.id',
            'username' => 'target_guru_updated',
            'role' => User::ROLE_GURU,
            'is_active' => 1,
        ]);

        $updateResponse->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Target Guru Updated',
            'email' => 'target.guru.updated@wikrama.sch.id',
        ]);
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $targetUser = User::factory()->create([
            'is_active' => true,
            'role' => User::ROLE_GURU,
        ]);

        $this->actingAs($this->admin)->patch(route('admin.users.toggle', $targetUser));
        $this->assertFalse($targetUser->fresh()->is_active);

        $this->actingAs($this->admin)->patch(route('admin.users.toggle', $targetUser));
        $this->assertTrue($targetUser->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_or_delete_self(): void
    {
        // Toggle self
        $toggleResponse = $this->actingAs($this->admin)->patch(route('admin.users.toggle', $this->admin));
        $toggleResponse->assertRedirect(route('admin.users.index'));
        $this->assertTrue($this->admin->fresh()->is_active);

        // Delete self
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin));
        $deleteResponse->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_can_delete_user_without_conflict(): void
    {
        $removableUser = User::factory()->create([
            'role' => User::ROLE_GURU,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $removableUser));
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $removableUser->id]);
    }

    public function test_guru_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->guru)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Direktori Calon Murid');
        $response->assertSee('Laporan & Rekapitulasi', false);
    }

    public function test_guru_can_access_calon_siswa_directory_and_detail(): void
    {
        $jurusan = MasterJurusan::first();
        $gelombang = MasterGelombang::first();
        $program = MasterProgram::first();

        $calonSiswa = CalonSiswa::factory()->create([
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'program_id' => $program->id,
            'nama_lengkap' => 'Siswa Uji Coba Guru',
        ]);

        // Index
        $indexResponse = $this->actingAs($this->guru)->get(route('admin.calon-siswa.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Siswa Uji Coba Guru');

        // Show detail
        $showResponse = $this->actingAs($this->guru)->get(route('admin.calon-siswa.show', $calonSiswa));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Siswa Uji Coba Guru');

        // Export CSV
        $csvResponse = $this->actingAs($this->guru)->get(route('admin.calon-siswa.export.csv'));
        $csvResponse->assertStatus(200);

        // Export PDF
        $pdfResponse = $this->actingAs($this->guru)->get(route('admin.calon-siswa.export.pdf'));
        $pdfResponse->assertStatus(200);
    }

    public function test_guru_can_access_laporan_rekapitulasi(): void
    {
        $response = $this->actingAs($this->guru)->get(route('admin.laporan.index'));

        $response->assertStatus(200);
        $response->assertSee('Laporan & Analitik', false);

        // Export Laporan PDF
        $pdfResponse = $this->actingAs($this->guru)->get(route('admin.laporan.rekap.export.pdf'));
        $pdfResponse->assertStatus(200);
    }

    public function test_guru_is_forbidden_from_admin_exclusive_areas(): void
    {
        // 1. User Management
        $this->actingAs($this->guru)
            ->get(route('admin.users.index'))
            ->assertRedirect(route('admin.dashboard'));

        // 2. Jurusan
        $this->actingAs($this->guru)
            ->get(route('admin.jurusan.index'))
            ->assertRedirect(route('admin.dashboard'));

        // 3. Keuangan
        $this->actingAs($this->guru)
            ->get(route('admin.keuangan.index'))
            ->assertRedirect(route('admin.dashboard'));

        // 4. Pembayaran Seleksi
        $this->actingAs($this->guru)
            ->get(route('admin.pembayaran.seleksi'))
            ->assertRedirect(route('admin.dashboard'));

        // 5. Alokasi Pewawancara
        $this->actingAs($this->guru)
            ->get(route('admin.alokasi-pewawancara.index'))
            ->assertRedirect(route('admin.dashboard'));

        // 6. Audit Trail
        $this->actingAs($this->guru)
            ->get(route('admin.audit-trail.index'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_guru_sidebar_does_not_display_restricted_links(): void
    {
        $response = $this->actingAs($this->guru)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee('Manajemen Pengguna');
        $response->assertDontSee('Manajemen Jurusan');
        $response->assertDontSee('Master Tarif Keuangan');
        $response->assertDontSee('Alokasi Pewawancara');
        $response->assertDontSee('Audit Trail Sistem');
    }
}
