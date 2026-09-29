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
        $response->assertSee('Kesepahaman Bersama (EULA)', false);
        $response->assertSee('Dalam kaitannya dengan penerimaan peserta didik baru', false);
        $response->assertSee('PROGRAM REGULER', false);
    }

    public function test_unggulan_candidate_sees_unggulan_clauses_and_asrama(): void
    {
        $programUnggulan = MasterProgram::where('kode', 'UGG')->first();
        if ($programUnggulan) {
            $this->calonSiswa->update(['program_id' => $programUnggulan->id]);
        }

        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.kesepahaman.index'));

        $response->assertStatus(200);
        $response->assertSee('PROGRAM UNGGULAN', false);
        $response->assertSee('wajib tinggal di Asrama', false);
        $response->assertSee('Dalam kaitannya dengan Program Unggulan, orang tua bersedia', false);
    }

    public function test_candidate_cannot_agree_without_checking_checkbox(): void
    {
        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.kesepahaman.store'), []);

        $response->assertSessionHasErrors(['setuju', 'checklist_poin']);
    }

    public function test_candidate_cannot_agree_with_incomplete_checklist(): void
    {
        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.kesepahaman.store'), [
                'setuju' => '1',
                'checklist_poin' => ['reg_a_1'], // Incomplete
            ]);

        $response->assertSessionHas('error');
    }

    public function test_candidate_can_agree_to_kesepahaman_and_transitions_to_menunggu_wawancara(): void
    {
        $kesepahamanService = app(\App\Services\KesepahamanService::class);
        $requiredPoints = $kesepahamanService->getRequiredPointIds('reguler');

        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.kesepahaman.store'), [
                'setuju' => '1',
                'checklist_poin' => $requiredPoints,
            ]);

        $response->assertRedirect(route('calon-siswa.kesepahaman.index'));
        $response->assertSessionHas('success');

        // Assert record created in kesepahaman_eula
        $this->assertDatabaseHas('kesepahaman_eula', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'setuju' => 1,
            'program_snapshot' => 'REGULER',
            'versi_dokumen' => 'v2.0 - 2027/2028',
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
        $kesepahamanService = app(\App\Services\KesepahamanService::class);
        $klausulData = $kesepahamanService->getKlausulByCalonSiswa($this->calonSiswa);

        KesepahamanEula::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'versi_dokumen' => 'v2.0 - 2027/2028',
            'program_snapshot' => 'REGULER',
            'isi_dokumen_atau_referensi_dokumen' => 'Naskah Persetujuan SPMB',
            'poin_disetujui' => $kesepahamanService->getRequiredPointIds('reguler'),
            'klausul_snapshot' => $klausulData['kelompok'],
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

    public function test_kesepahaman_pdf_view_does_not_contain_kop_surat(): void
    {
        $kesepahamanService = app(\App\Services\KesepahamanService::class);
        $klausulData = $kesepahamanService->getKlausulByCalonSiswa($this->calonSiswa);

        $html = view('pdf.kesepahaman_eula', [
            'calonSiswa' => $this->calonSiswa,
            'eula' => null,
            'programNama' => $klausulData['program_title'],
            'tahunPelajaran' => $klausulData['tahun_pelajaran'],
            'kelompokList' => $klausulData['kelompok'],
            'hideKop' => true,
        ])->render();

        $this->assertStringNotContainsString('class="header-kop"', $html);
        $this->assertStringNotContainsString('Kop Surat SMK Wikrama 1 Garut', $html);
        $this->assertStringContainsString('NASKAH PERSETUJUAN', $html);
        $this->assertStringContainsString('class="fixed-footer-paraf"', $html);
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
