<?php

namespace Tests\Feature;

use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\DokumenPendaftaran;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\PembayaranSeleksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class AdminDeleteCalonSiswaTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $guruUser;
    protected User $bendaharaUser;
    protected User $siswaUser;
    protected CalonSiswa $calonSiswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        $this->adminUser = User::where('role', User::ROLE_ADMIN)->first();
        $this->guruUser = User::where('role', User::ROLE_GURU)->first();
        $this->bendaharaUser = User::where('role', User::ROLE_BENDAHARA)->first();

        // Create student user & candidate
        $this->siswaUser = User::factory()->create([
            'role' => User::ROLE_CALON_SISWA,
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'username' => '0098765432',
        ]);

        $program = MasterProgram::first();
        $jurusan = MasterJurusan::first();
        $gelombang = MasterGelombang::first();

        $this->calonSiswa = CalonSiswa::factory()->create([
            'user_id' => $this->siswaUser->id,
            'nomor_pendaftaran' => '26REG0999',
            'nisn' => '0098765432',
            'nama_lengkap' => 'Budi Santoso',
            'status_spmb' => SpmbStatus::MELENGKAPI_DATA,
            'program_id' => $program->id,
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);
    }

    public function test_non_admin_cannot_delete_candidate(): void
    {
        // 1. Guru cannot delete (web redirects to dashboard, json gets 403)
        $responseGuru = $this->actingAs($this->guruUser)
            ->delete(route('admin.calon-siswa.destroy', $this->calonSiswa), [
                'konfirmasi' => 'HAPUS',
            ]);
        $responseGuru->assertRedirect(route($this->guruUser->getDashboardRoute()));

        $responseGuruJson = $this->actingAs($this->guruUser)
            ->deleteJson(route('admin.calon-siswa.destroy', $this->calonSiswa), [
                'konfirmasi' => 'HAPUS',
            ]);
        $responseGuruJson->assertStatus(403);

        // 2. Bendahara cannot delete
        $responseBendahara = $this->actingAs($this->bendaharaUser)
            ->delete(route('admin.calon-siswa.destroy', $this->calonSiswa), [
                'konfirmasi' => 'HAPUS',
            ]);
        $responseBendahara->assertRedirect(route($this->bendaharaUser->getDashboardRoute()));

        // 3. Student cannot delete
        $responseSiswa = $this->actingAs($this->siswaUser)
            ->delete(route('admin.calon-siswa.destroy', $this->calonSiswa), [
                'konfirmasi' => 'HAPUS',
            ]);
        $responseSiswa->assertRedirect(route($this->siswaUser->getDashboardRoute()));

        // Candidate must still exist in DB
        $this->assertDatabaseHas('calon_siswa', ['id' => $this->calonSiswa->id]);
    }

    public function test_admin_cannot_delete_without_exact_hapus_confirmation(): void
    {
        // Missing confirmation string
        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.calon-siswa.destroy', $this->calonSiswa), []);

        $response->assertSessionHasErrors('konfirmasi');
        $this->assertDatabaseHas('calon_siswa', ['id' => $this->calonSiswa->id]);

        // Lowercase "hapus" should fail
        $responseLower = $this->actingAs($this->adminUser)
            ->delete(route('admin.calon-siswa.destroy', $this->calonSiswa), [
                'konfirmasi' => 'hapus',
            ]);

        $responseLower->assertSessionHasErrors('konfirmasi');
        $this->assertDatabaseHas('calon_siswa', ['id' => $this->calonSiswa->id]);

        // Random word should fail
        $responseWrong = $this->actingAs($this->adminUser)
            ->delete(route('admin.calon-siswa.destroy', $this->calonSiswa), [
                'konfirmasi' => 'YA_SAYA_YAKIN',
            ]);

        $responseWrong->assertSessionHasErrors('konfirmasi');
        $this->assertDatabaseHas('calon_siswa', ['id' => $this->calonSiswa->id]);
    }

    public function test_admin_can_permanently_delete_candidate_with_valid_confirmation(): void
    {
        Storage::fake('public');

        // Setup uploaded dummy files
        $photoFile = UploadedFile::fake()->image('pas_foto.jpg');
        $kkFile = UploadedFile::fake()->create('kartu_keluarga.pdf', 100);
        $buktiBayarFile = UploadedFile::fake()->image('bukti_bayar.png');

        $photoPath = $photoFile->store('dokumen/foto', 'public');
        $kkPath = $kkFile->store('dokumen/kk', 'public');
        $buktiPath = $buktiBayarFile->store('bukti_bayar', 'public');

        Storage::disk('public')->assertExists($photoPath);
        Storage::disk('public')->assertExists($kkPath);
        Storage::disk('public')->assertExists($buktiPath);

        DokumenPendaftaran::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'pas_foto_path' => $photoPath,
            'kk_path' => $kkPath,
        ]);

        PembayaranSeleksi::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'nominal_tagihan' => 200000,
            'nominal_dibayar' => 200000,
            'status' => 'PENDING',
            'bukti_transfer_path' => $buktiPath,
        ]);

        $candidateId = $this->calonSiswa->id;
        $studentUserId = $this->siswaUser->id;

        // Perform permanent deletion
        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.calon-siswa.destroy', $this->calonSiswa), [
                'konfirmasi' => 'HAPUS',
                'alasan' => 'Pendaftaran ganda atas permintaan orang tua siswa.',
            ]);

        $response->assertRedirect(route('admin.calon-siswa.index'));
        $response->assertSessionHas('success');

        // Verify database records are permanently deleted
        $this->assertDatabaseMissing('calon_siswa', ['id' => $candidateId]);
        $this->assertDatabaseMissing('dokumen_pendaftaran', ['calon_siswa_id' => $candidateId]);
        $this->assertDatabaseMissing('pembayaran_seleksi', ['calon_siswa_id' => $candidateId]);
        $this->assertDatabaseMissing('users', ['id' => $studentUserId]);

        // Verify storage files were cleaned up
        Storage::disk('public')->assertMissing($photoPath);
        Storage::disk('public')->assertMissing($kkPath);
        Storage::disk('public')->assertMissing($buktiPath);

        // Verify Activity Log was recorded
        $log = Activity::where('log_name', 'calon_siswa')->latest('id')->first();
        $this->assertNotNull($log);
        $this->assertEquals($this->adminUser->id, $log->causer_id);
        $this->assertStringContainsString('Budi Santoso', $log->description);
        $this->assertEquals('Pendaftaran ganda atas permintaan orang tua siswa.', $log->properties['alasan']);
        $this->assertEquals('26REG0999', $log->properties['nomor_pendaftaran']);
        $this->assertEquals('0098765432', $log->properties['nisn']);
    }

    public function test_ui_delete_button_and_modal_visible_only_to_admin(): void
    {
        // 1. Admin visits detail page
        $adminShow = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.show', $this->calonSiswa));
        $adminShow->assertStatus(200);
        $adminShow->assertSee('Hapus Pendaftar');
        $adminShow->assertSee('Konfirmasi Hapus Pendaftar');

        // 2. Admin visits index page
        $adminIndex = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.index'));
        $adminIndex->assertStatus(200);
        $adminIndex->assertSee('Hapus Calon Siswa (Admin Only)');
        $adminIndex->assertSee('Konfirmasi Hapus Pendaftar');

        // 3. Guru visits detail page -> cannot see Hapus button or modal
        $guruShow = $this->actingAs($this->guruUser)
            ->get(route('admin.calon-siswa.show', $this->calonSiswa));
        $guruShow->assertStatus(200);
        $guruShow->assertDontSee('Hapus Pendaftar');
        $guruShow->assertDontSee('Konfirmasi Hapus Pendaftar');

        // 4. Guru visits index page -> cannot see Hapus button or modal
        $guruIndex = $this->actingAs($this->guruUser)
            ->get(route('admin.calon-siswa.index'));
        $guruIndex->assertStatus(200);
        $guruIndex->assertDontSee('Hapus Calon Siswa (Admin Only)');
        $guruIndex->assertDontSee('Konfirmasi Hapus Pendaftar');
    }
}
