<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\MasterProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppCsRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Landing page memuat modal konsultasi panitia dengan pilihan CS Reguler dan CS Unggulan.
     */
    public function test_landing_page_has_cs_consultation_modal_and_both_numbers(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('showCsModal', false);
        $response->assertSee('Konsultasi Panitia via WhatsApp');
        $response->assertSee('Konsultasi Pendaftaran');
        $response->assertSee('Konsultasi Asrama');
        $response->assertSee('628112232880');
        $response->assertSee('6281122802880');
    }

    /**
     * Test 2: Pendaftaran sukses untuk Program Unggulan mengarahkan ke CS Unggulan (6281122802880).
     */
    public function test_pendaftaran_sukses_for_program_unggulan_routes_to_cs_unggulan(): void
    {
        $programUnggulan = MasterProgram::create([
            'kode' => 'UGG',
            'nama' => 'Program Unggulan Berasrama',
            'aktif' => true,
        ]);

        $calonSiswa = CalonSiswa::factory()->create([
            'nomor_pendaftaran' => 'A16269901',
            'nama_lengkap' => 'Santri Unggulan',
            'program_id' => $programUnggulan->id,
        ]);

        $response = $this->get(route('pendaftaran.sukses', $calonSiswa->nomor_pendaftaran));

        $response->assertStatus(200);
        $response->assertSee('https://wa.me/6281122802880', false);
        $response->assertSee('admin Program Unggulan');
        $response->assertSee('Program+Unggulan', false);
    }

    /**
     * Test 3: Pendaftaran sukses untuk Program Reguler mengarahkan ke CS Reguler (628112232880).
     */
    public function test_pendaftaran_sukses_for_program_reguler_routes_to_cs_reguler(): void
    {
        $programReguler = MasterProgram::create([
            'kode' => 'REG',
            'nama' => 'Program Reguler',
            'aktif' => true,
        ]);

        $calonSiswa = CalonSiswa::factory()->create([
            'nomor_pendaftaran' => 'A16269902',
            'nama_lengkap' => 'Siswa Reguler',
            'program_id' => $programReguler->id,
        ]);

        $response = $this->get(route('pendaftaran.sukses', $calonSiswa->nomor_pendaftaran));

        $response->assertStatus(200);
        $response->assertSee('https://wa.me/628112232880', false);
        $response->assertSee('admin Program Reguler');
        $response->assertSee('Program+Reguler', false);
    }

    /**
     * Test 4: Dashboard Calon Siswa Unggulan menampilkan link header CS Asrama.
     */
    public function test_dashboard_header_routes_calon_siswa_unggulan_to_cs_unggulan(): void
    {
        $programUnggulan = MasterProgram::create([
            'kode' => 'UGG',
            'nama' => 'Program Unggulan Berasrama',
            'aktif' => true,
        ]);

        $user = User::factory()->create([
            'role' => User::ROLE_CALON_SISWA,
            'name' => 'Calon Unggulan User',
        ]);

        CalonSiswa::factory()->create([
            'user_id' => $user->id,
            'nomor_pendaftaran' => 'A16269903',
            'nama_lengkap' => 'Calon Unggulan User',
            'program_id' => $programUnggulan->id,
        ]);

        $response = $this->actingAs($user)->get(route('calon-siswa.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('https://wa.me/6281122802880', false);
        $response->assertSee('CS Asrama');
    }

    /**
     * Test 5: Dashboard Calon Siswa Reguler menampilkan link header Bantuan CS (Reguler).
     */
    public function test_dashboard_header_routes_calon_siswa_reguler_to_cs_reguler(): void
    {
        $programReguler = MasterProgram::create([
            'kode' => 'REG',
            'nama' => 'Program Reguler',
            'aktif' => true,
        ]);

        $user = User::factory()->create([
            'role' => User::ROLE_CALON_SISWA,
            'name' => 'Calon Reguler User',
        ]);

        CalonSiswa::factory()->create([
            'user_id' => $user->id,
            'nomor_pendaftaran' => 'A16269904',
            'nama_lengkap' => 'Calon Reguler User',
            'program_id' => $programReguler->id,
        ]);

        $response = $this->actingAs($user)->get(route('calon-siswa.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('https://wa.me/628112232880', false);
        $response->assertSee('Bantuan CS');
    }

    /**
     * Test 6: Dashboard Admin menampilkan link header Bantuan CS (Reguler).
     */
    public function test_dashboard_header_routes_admin_to_cs_reguler(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'name' => 'Administrator',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('https://wa.me/628112232880', false);
        $response->assertSee('Bantuan CS');
    }
}
