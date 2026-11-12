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

class KendaliCalonSiswaTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $guruUser;
    protected User $pewawancaraUser;
    protected CalonSiswa $calonSiswa;
    protected MasterProgram $programReguler;
    protected MasterJurusan $jurusan;
    protected MasterGelombang $gelombang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        $this->adminUser = User::where('role', User::ROLE_ADMIN)->first();
        $this->guruUser = User::where('role', User::ROLE_GURU)->first();
        $this->pewawancaraUser = User::where('role', User::ROLE_PEWAWANCARA)->first();

        $this->programReguler = MasterProgram::firstOrCreate(
            ['kode' => 'REG'],
            ['nama' => 'Reguler', 'aktif' => true]
        );
        $this->jurusan = MasterJurusan::first();
        $this->gelombang = MasterGelombang::first();

        $siswaUser = User::factory()->create(['role' => User::ROLE_CALON_SISWA]);
        $this->calonSiswa = CalonSiswa::factory()->create([
            'user_id' => $siswaUser->id,
            'nama_lengkap' => 'Budi Santoso',
            'nomor_pendaftaran' => '26REG0001',
            'nisn' => '0098765432',
            'program_id' => $this->programReguler->id,
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $this->gelombang->id,
            'status_spmb' => SpmbStatus::MENUNGGU_WAWANCARA,
            'referensi_nama' => 'Pak Ahmad Guru',
            'referensi_jenis' => 'GURU_WIKRAMA_GARUT',
            'referensi_rayon' => 'Cibatu 1',
        ]);
    }

    public function test_guest_and_non_admin_cannot_access_kendali_menu(): void
    {
        // 1. Guest redirected to login
        $this->get(route('admin.kendali-calon-siswa.index'))
            ->assertRedirect(route('login'));

        // 2. Guru redirected to dashboard by RoleMiddleware
        $this->actingAs($this->guruUser)
            ->get(route('admin.kendali-calon-siswa.index'))
            ->assertRedirect(route($this->guruUser->getDashboardRoute()));

        // 3. Pewawancara redirected to dashboard by RoleMiddleware
        $this->actingAs($this->pewawancaraUser)
            ->get(route('admin.kendali-calon-siswa.index'))
            ->assertRedirect(route($this->pewawancaraUser->getDashboardRoute()));

        // 4. JSON request gets 403 Forbidden
        $this->actingAs($this->guruUser)
            ->getJson(route('admin.kendali-calon-siswa.index'))
            ->assertStatus(403);
    }

    public function test_admin_can_access_kendali_calon_murid_page(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.kendali-calon-siswa.index'));

        $response->assertStatus(200);
        $response->assertSee('Kendali Calon Murid');
        $response->assertSee('Khusus Admin');
        $response->assertSee('Nama Calon Murid');
        $response->assertSee('Tanggal Daftar');
        $response->assertSee('Bulan Daftar');
        $response->assertSee('Legenda Warna Status');
        $response->assertSee('Panduan Warna Baris Status SPMB');
        $response->assertSee('Program & Gelombang', false);
        $response->assertDontSee('Biaya Seleksi');
        $response->assertSee('Pemberi Rekomendasi');
        $response->assertSee('Catatan Khusus CS');
        $response->assertSee('Catatan untuk Pewawancara');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Pak Ahmad Guru');
    }

    public function test_admin_can_update_catatan_via_ajax(): void
    {
        // Update Catatan Khusus CS with is_done = true
        $responseCs = $this->actingAs($this->adminUser)
            ->patchJson(route('admin.kendali-calon-siswa.update-catatan', $this->calonSiswa), [
                'field' => 'catatan_admin',
                'value' => 'Orang tua siswa minta dispensasi waktu daftar ulang.',
                'is_done' => true,
            ]);

        $responseCs->assertStatus(200);
        $responseCs->assertJson([
            'success' => true,
            'catatan_admin' => 'Orang tua siswa minta dispensasi waktu daftar ulang.',
            'is_catatan_admin_done' => true,
        ]);

        $this->assertDatabaseHas('calon_siswa', [
            'id' => $this->calonSiswa->id,
            'catatan_admin' => 'Orang tua siswa minta dispensasi waktu daftar ulang.',
            'is_catatan_admin_done' => 1,
        ]);

        // Update Catatan untuk Pewawancara with is_done = false
        $responseWawancara = $this->actingAs($this->adminUser)
            ->patchJson(route('admin.kendali-calon-siswa.update-catatan', $this->calonSiswa), [
                'field' => 'catatan_untuk_pewawancara',
                'value' => 'Fokus gali minat bakat di jurusan PPLG karena pernah juara coding.',
                'is_done' => false,
            ]);

        $responseWawancara->assertStatus(200);
        $responseWawancara->assertJson([
            'success' => true,
            'catatan_untuk_pewawancara' => 'Fokus gali minat bakat di jurusan PPLG karena pernah juara coding.',
            'is_catatan_pewawancara_done' => false,
        ]);

        $this->assertDatabaseHas('calon_siswa', [
            'id' => $this->calonSiswa->id,
            'catatan_untuk_pewawancara' => 'Fokus gali minat bakat di jurusan PPLG karena pernah juara coding.',
            'is_catatan_pewawancara_done' => 0,
        ]);
    }

    public function test_pewawancara_sees_catatan_in_wawancara_hub(): void
    {
        // Set note for interviewer
        $this->calonSiswa->update([
            'catatan_untuk_pewawancara' => 'Perhatian: Calon murid memiliki alergi dingin.',
        ]);

        $response = $this->actingAs($this->pewawancaraUser)
            ->get(route('pewawancara.wawancara.hub', $this->calonSiswa));

        $response->assertStatus(200);
        $response->assertSee('Catatan Khusus dari Panitia / CS');
        $response->assertSee('Perhatian: Calon murid memiliki alergi dingin.');
    }

    public function test_admin_can_export_kendali_xls(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.kendali-calon-siswa.export.xls'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
        $this->assertStringContainsString('.xls', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('Budi Santoso', $response->streamedContent());
        $this->assertStringContainsString('Kendali Calon Murid', $response->streamedContent());
    }

    public function test_admin_can_export_kendali_pdf(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.kendali-calon-siswa.export.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
        $this->assertStringContainsString('.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_long_catatan_text_renders_properly_in_kendali_and_exports(): void
    {
        $longCsText = str_repeat("Catatan CS panjang dengan rincian khusus orang tua meminta jadwal ulang wawancara. \n", 8);
        $longPewawancaraText = str_repeat("Instruksi pewawancara: verifikasi sertifikat kejuaraan coding tingkat provinsi dan minat jurusan. \n", 8);

        $this->calonSiswa->update([
            'catatan_admin' => $longCsText,
            'is_catatan_admin_done' => true,
            'catatan_untuk_pewawancara' => $longPewawancaraText,
            'is_catatan_pewawancara_done' => false,
        ]);

        // 1. Check web table renders properly
        $responseWeb = $this->actingAs($this->adminUser)
            ->get(route('admin.kendali-calon-siswa.index'));
        $responseWeb->assertStatus(200);
        $responseWeb->assertSee('Catatan CS panjang');
        $responseWeb->assertSee('Instruksi pewawancara');

        // 2. Check PDF export renders with long text
        $responsePdf = $this->actingAs($this->adminUser)
            ->get(route('admin.kendali-calon-siswa.export.pdf'));
        $responsePdf->assertStatus(200);

        // 3. Check XLS export streams with long text
        $responseXls = $this->actingAs($this->adminUser)
            ->get(route('admin.kendali-calon-siswa.export.xls'));
        $responseXls->assertStatus(200);
        $this->assertStringContainsString('Catatan CS panjang', $responseXls->streamedContent());
    }

    public function test_admin_can_filter_kendali_by_bulan_daftar(): void
    {
        $jurusan = MasterJurusan::first();
        $gelombang = MasterGelombang::first();
        $program = MasterProgram::first();

        // 1. Buat calon siswa di bulan Oktober 2026 dan November 2026
        $siswaOktober = CalonSiswa::factory()->create([
            'nama_lengkap' => 'Siswa Bulan Oktober',
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'program_id' => $program->id,
            'created_at' => '2026-10-15 10:00:00',
        ]);
        $siswaNovember = CalonSiswa::factory()->create([
            'nama_lengkap' => 'Siswa Bulan November',
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'program_id' => $program->id,
            'created_at' => '2026-11-05 14:00:00',
        ]);

        // Filter November 2026
        $responseNov = $this->actingAs($this->adminUser)
            ->get(route('admin.kendali-calon-siswa.index', ['bulan_daftar' => '2026-11']));

        $responseNov->assertOk();
        $responseNov->assertSee('Siswa Bulan November');
        $responseNov->assertDontSee('Siswa Bulan Oktober');

        // Filter Oktober 2026
        $responseOkt = $this->actingAs($this->adminUser)
            ->get(route('admin.kendali-calon-siswa.index', ['bulan_daftar' => '2026-10']));

        $responseOkt->assertOk();
        $responseOkt->assertSee('Siswa Bulan Oktober');
        $responseOkt->assertDontSee('Siswa Bulan November');
    }

    public function test_all_spmb_statuses_have_configured_row_background_colors(): void
    {
        $cases = SpmbStatus::cases();
        $this->assertCount(14, $cases);

        $colors = array_map(fn($st) => $st->rowBgColor(), $cases);

        // Pastikan setiap status memiliki hex color yang valid (#RRGGBB)
        foreach ($colors as $color) {
            $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $color);
        }

        // Pastikan ke-14 status memiliki warna unik (14 warna berbeda)
        $this->assertCount(14, array_unique($colors));

        // Cek pemetaan semantik kunci
        $this->assertEquals('#F7BBBB', SpmbStatus::DITOLAK->rowBgColor());
        $this->assertEquals('#F7BBDE', SpmbStatus::MENGUNDURKAN_DIRI->rowBgColor());
        $this->assertEquals('#BBF7D4', SpmbStatus::DITERIMA->rowBgColor());
        $this->assertEquals('#E8BBF7', SpmbStatus::RESMI_TERDAFTAR->rowBgColor());
    }

    public function test_kendali_table_rows_render_background_color_inline_style(): void
    {
        $this->calonSiswa->update([
            'status_spmb' => SpmbStatus::MENUNGGU_WAWANCARA,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.kendali-calon-siswa.index'));

        $response->assertOk();
        $expectedColor = SpmbStatus::MENUNGGU_WAWANCARA->rowBgColor();
        $response->assertSee('style="background-color: ' . $expectedColor . ';"', false);
    }
}
