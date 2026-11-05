<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BendaharaLaporanKeuanganTest extends TestCase
{
    use RefreshDatabase;

    protected User $bendahara;
    protected User $admin;
    protected User $calonSiswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        $this->bendahara = User::where('role', User::ROLE_BENDAHARA)->first();
        $this->admin = User::where('role', User::ROLE_ADMIN)->first();
        $this->calonSiswa = User::where('role', User::ROLE_CALON_SISWA)->first();
    }

    public function test_bendahara_can_access_laporan_keuangan_index(): void
    {
        $response = $this->actingAs($this->bendahara)->get(route('bendahara.laporan.index'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Keuangan Terpadu');
    }

    public function test_admin_can_access_laporan_keuangan_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('bendahara.laporan.index'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Keuangan Terpadu');
    }

    public function test_calon_siswa_cannot_access_laporan_keuangan_index(): void
    {
        $response = $this->actingAs($this->calonSiswa)->get(route('bendahara.laporan.index'));

        $response->assertRedirect(route('calon-siswa.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_bendahara_can_export_pdf_laporan_keuangan(): void
    {
        $response = $this->actingAs($this->bendahara)->get(route('bendahara.laporan.export.pdf'));

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition', ''), '.pdf'));
    }

    public function test_bendahara_can_export_xls_laporan_keuangan(): void
    {
        $response = $this->actingAs($this->bendahara)->get(route('bendahara.laporan.export.xls'));

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition', ''), '.xls'));
    }
}
