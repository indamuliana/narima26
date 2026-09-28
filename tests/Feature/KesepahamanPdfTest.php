<?php

namespace Tests\Feature;

use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\KesepahamanEula;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KesepahamanPdfTest extends TestCase
{
    use RefreshDatabase;

    protected User $siswaUser;
    protected CalonSiswa $calonSiswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        $this->siswaUser = User::where('role', 'calon_siswa')->first();
        $program = MasterProgram::first();
        $jurusan = MasterJurusan::first();
        $gelombang = MasterGelombang::first();

        $this->calonSiswa = CalonSiswa::factory()->create([
            'user_id' => $this->siswaUser->id,
            'nomor_pendaftaran' => '26AAY0050',
            'nisn' => $this->siswaUser->username,
            'nama_lengkap' => $this->siswaUser->name,
            'status_spmb' => SpmbStatus::DATA_LENGKAP,
            'status_data' => 'LENGKAP',
            'program_id' => $program->id,
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);
    }

    public function test_candidate_cannot_access_kesepahaman_if_data_incomplete(): void
    {
        $this->calonSiswa->update([
            'status_spmb' => SpmbStatus::MELENGKAPI_DATA,
            'status_data' => 'BELUM_LENGKAP',
        ]);

        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.kesepahaman.index'));

        $response->assertRedirect(route('calon-siswa.lengkapi-data.index'));
        $response->assertSessionHas('error');
    }

    public function test_candidate_can_access_kesepahaman_when_data_is_complete(): void
    {
        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.kesepahaman.index'));

        $response->assertStatus(200);
        $response->assertSee('Surat Pernyataan & Kesepahaman Bersama (EULA)', false);
        $response->assertSee('PASAL 1 — KEABSAHAN & KEASLIAN DATA', false);
    }

    public function test_candidate_cannot_agree_without_checking_checkbox(): void
    {
        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.kesepahaman.store'), []);

        $response->assertSessionHasErrors('setuju');
    }

    public function test_candidate_can_agree_to_kesepahaman_and_transitions_to_menunggu_wawancara(): void
    {
        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.kesepahaman.store'), [
                'setuju' => '1',
            ]);

        $response->assertRedirect(route('calon-siswa.kesepahaman.index'));
        $response->assertSessionHas('success');

        // Assert record created in kesepahaman_eula
        $this->assertDatabaseHas('kesepahaman_eula', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'setuju' => 1,
            'versi_dokumen' => 'v1.0 - 2026/2027',
        ]);

        // Assert status transition
        $this->calonSiswa->refresh();
        $this->assertEquals(SpmbStatus::MENUNGGU_WAWANCARA, $this->calonSiswa->status_spmb);

        // Assert audit trail
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'spmb_consent',
            'subject_id' => $this->calonSiswa->id,
        ]);
    }

    public function test_candidate_can_download_pdf_kesepahaman_after_agreement(): void
    {
        KesepahamanEula::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'versi_dokumen' => 'v1.0 - 2026/2027',
            'isi_dokumen_atau_referensi_dokumen' => 'Klausul SPMB',
            'setuju' => true,
            'agreed_at' => now(),
            'agreed_by' => $this->siswaUser->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.kesepahaman.cetak'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_unagreed_candidate_cannot_download_kesepahaman_pdf(): void
    {
        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.dokumen.kesepahaman'));

        $response->assertRedirect(route('calon-siswa.kesepahaman.index'));
        $response->assertSessionHas('error');
    }

    public function test_candidate_can_download_pdf_kartu_peserta(): void
    {
        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.dokumen.kartu'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_candidate_can_download_pdf_informasi_akun(): void
    {
        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.dokumen.akun'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_dokumen_hub_page_renders_correctly(): void
    {
        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.dokumen.index'));

        $response->assertStatus(200);
        $response->assertSee('Dokumen & Berkas PDF SPMB', false);
        $response->assertSee('Kartu Tanda Peserta SPMB');
        $response->assertSee('Surat Kesepahaman & Pernyataan (EULA)', false);
        $response->assertSee('Informasi Akun Calon Siswa');
    }
}
