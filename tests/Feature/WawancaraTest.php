<?php

namespace Tests\Feature;

use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\DataAkademik;
use App\Models\DataOrangtua;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterKriteriaWawancara;
use App\Models\MasterProgram;
use App\Models\User;
use App\Models\WawancaraSiswa;
use App\Models\WawancaraOrangTua;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WawancaraTest extends TestCase
{
    use RefreshDatabase;

    protected User $pewawancaraUser;
    protected User $siswaUser;
    protected User $bendaharaUser;
    protected CalonSiswa $calonSiswa;
    protected MasterJurusan $jurusan;
    protected MasterProgram $program;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        $this->pewawancaraUser = User::where('role', User::ROLE_PEWAWANCARA)->first();
        $this->siswaUser = User::where('role', User::ROLE_CALON_SISWA)->first();
        $this->bendaharaUser = User::where('role', User::ROLE_BENDAHARA)->first();

        $this->program = MasterProgram::first();
        $this->jurusan = MasterJurusan::first();
        $gelombang = MasterGelombang::first();

        $this->calonSiswa = CalonSiswa::factory()->create([
            'user_id' => $this->siswaUser->id,
            'nomor_pendaftaran' => '26AAY0099',
            'nisn' => '0099887766',
            'nama_lengkap' => 'Budi Santoso',
            'status_spmb' => SpmbStatus::MENUNGGU_WAWANCARA,
            'status_data' => 'LENGKAP',
            'program_id' => $this->program->id,
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);

        DataOrangtua::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'nama_ayah' => 'Joko Santoso',
            'pekerjaan_ayah' => 'Wiraswasta',
            'no_hp_ayah' => '081299998888',
            'penghasilan_ayah' => 5000000,
            'nama_ibu' => 'Siti Aminah',
            'pekerjaan_ibu' => 'Guru',
            'no_hp_ibu' => '081299997777',
            'penghasilan_ibu' => 4000000,
        ]);

        DataAkademik::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'nilai_rata_rata' => 88.5,
            'nilai_matematika' => 90,
            'nilai_bahasa_indonesia' => 85,
            'nilai_bahasa_inggris' => 88,
            'nilai_ipa' => 89,
        ]);
    }

    public function test_unauthorized_roles_cannot_access_pewawancara_routes(): void
    {
        $this->get(route('pewawancara.dashboard'))->assertRedirect(route('login'));

        $this->actingAs($this->siswaUser)->get(route('pewawancara.dashboard'))
            ->assertRedirect(route('calon-siswa.dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($this->bendaharaUser)->get(route('pewawancara.dashboard'))
            ->assertRedirect(route('bendahara.dashboard'))
            ->assertSessionHas('error');
    }

    public function test_pewawancara_can_view_dashboard_with_metrics(): void
    {
        $response = $this->actingAs($this->pewawancaraUser)->get(route('pewawancara.dashboard'));
        $response->assertOk();
        $response->assertSee('Panel Penguji & Wawancara', false);
        $response->assertSee('Antrian Menunggu');
        $response->assertSee('Budi Santoso');
    }

    public function test_pewawancara_can_view_antrian_list(): void
    {
        $response = $this->actingAs($this->pewawancaraUser)->get(route('pewawancara.antrian'));
        $response->assertOk();
        $response->assertSee('Antrian Calon Siswa');
        $response->assertSee('26AAY0099');
        $response->assertSee('Budi Santoso');
    }

    public function test_pewawancara_can_filter_antrian_by_search_and_jurusan(): void
    {
        $otherJurusan = MasterJurusan::where('id', '!=', $this->jurusan->id)->first();
        $otherUser = User::factory()->create(['role' => User::ROLE_CALON_SISWA]);
        $otherSiswa = CalonSiswa::factory()->create([
            'user_id' => $otherUser->id,
            'nomor_pendaftaran' => '26AAY0100',
            'nisn' => '0099887711',
            'nama_lengkap' => 'Rina Gunawan',
            'status_spmb' => SpmbStatus::MENUNGGU_WAWANCARA,
            'status_data' => 'LENGKAP',
            'program_id' => $this->program->id,
            'jurusan_id' => $otherJurusan->id,
        ]);

        $response1 = $this->actingAs($this->pewawancaraUser)->get(route('pewawancara.antrian', ['q' => 'Budi']));
        $response1->assertSee('Budi Santoso');
        $response1->assertDontSee('Rina Gunawan');

        $response2 = $this->actingAs($this->pewawancaraUser)->get(route('pewawancara.antrian', ['jurusan_id' => $otherJurusan->id]));
        $response2->assertSee('Rina Gunawan');
        $response2->assertDontSee('Budi Santoso');
    }

    public function test_pewawancara_cannot_access_form_for_ineligible_candidate(): void
    {
        $ineligibleUser = User::factory()->create(['role' => User::ROLE_CALON_SISWA]);
        $ineligibleSiswa = CalonSiswa::factory()->create([
            'user_id' => $ineligibleUser->id,
            'nomor_pendaftaran' => '26AAY0101',
            'nama_lengkap' => 'Dedi Ineligible',
            'status_spmb' => SpmbStatus::MELENGKAPI_DATA,
            'status_data' => 'BELUM_LENGKAP',
            'program_id' => $this->program->id,
            'jurusan_id' => $this->jurusan->id,
        ]);

        $response = $this->actingAs($this->pewawancaraUser)->get(route('pewawancara.wawancara.hub', $ineligibleSiswa));
        $response->assertRedirect(route('pewawancara.antrian'));
        $response->assertSessionHas('error');
    }

    public function test_pewawancara_can_open_interview_hub_for_eligible_candidate(): void
    {
        $response = $this->actingAs($this->pewawancaraUser)->get(route('pewawancara.wawancara.hub', $this->calonSiswa));
        $response->assertOk();
        $response->assertSee('Budi Santoso');
        $response->assertSee('Wawancara Siswa');
        $response->assertSee('Wawancara Orang Tua');
    }

    public function test_pewawancara_can_save_interview_siswa_draft(): void
    {
        $payload = [
            'action' => 'draft',
            'nama_petugas' => 'Test Petugas',
            'baca_quran' => 'Ya',
            'alasan_masuk_wikrama' => 'Test',
            'alasan_pilih_program_jurusan' => 'Test',
            'merokok' => 'Tidak',
            'kondisi_kesehatan' => 'Baik',
            'kerapihan_rambut' => 'HIJAU',
            'kerapihan_seragam' => 'HIJAU',
            'status_pendengaran' => 'HIJAU',
            'status_penglihatan' => 'Normal',
            'rekomendasi' => 'TERIMA',
        ];

        $response = $this->actingAs($this->pewawancaraUser)->post(route('pewawancara.wawancara.save-siswa', $this->calonSiswa), $payload);
        $response->assertRedirect(route('pewawancara.wawancara.hub', $this->calonSiswa));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('wawancara_siswa', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'pewawancara_id' => $this->pewawancaraUser->id,
            'status' => 'DRAFT',
            'baca_quran' => 'Ya',
        ]);

        $this->assertEquals(SpmbStatus::MENUNGGU_WAWANCARA, $this->calonSiswa->fresh()->status_spmb);
    }

    public function test_pewawancara_can_finalize_interview_and_transition_status(): void
    {
        // 1. Save Siswa Selesai
        $payloadSiswa = [
            'action' => 'selesai',
            'nama_petugas' => 'Test Petugas',
            'baca_quran' => 'Ya',
            'alasan_masuk_wikrama' => 'Test',
            'alasan_pilih_program_jurusan' => 'Test',
            'merokok' => 'Tidak',
            'kondisi_kesehatan' => 'Baik',
            'kerapihan_rambut' => 'HIJAU',
            'kerapihan_seragam' => 'HIJAU',
            'status_pendengaran' => 'HIJAU',
            'status_penglihatan' => 'Normal',
            'rekomendasi' => 'TERIMA',
        ];

        $this->actingAs($this->pewawancaraUser)->post(route('pewawancara.wawancara.save-siswa', $this->calonSiswa), $payloadSiswa);
        
        $this->assertDatabaseHas('wawancara_siswa', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'status' => 'SELESAI',
        ]);
        
        // Status should still be MENUNGGU_WAWANCARA because Orang Tua is not done
        $this->assertEquals(SpmbStatus::MENUNGGU_WAWANCARA, $this->calonSiswa->fresh()->status_spmb);

        // 2. Save Orang Tua Selesai
        $payloadOrangTua = [
            'action' => 'selesai',
            'nama_diwawancarai' => 'Joko Santoso',
            'hubungan_dengan_siswa' => 'Ayah',
            'tinggal_bersama' => 'Orang Tua',
            'jarak_rumah' => '5 km',
            'transportasi' => 'Motor',
            'penanggung_jawab_belajar' => 'Ayah',
            'info_wikrama_dari' => 'Brosur',
            'baca_quran' => 'Ya',
            'alasan_masuk_wikrama' => 'Bagus',
            'alasan_pilih_program_jurusan' => 'Minat',
            'kebiasaan_tempat_tidur' => 'Selalu',
            'merokok' => 'Tidak',
            'alergi' => '-',
        ];

        $response = $this->actingAs($this->pewawancaraUser)->post(route('pewawancara.wawancara.save-orang-tua', $this->calonSiswa), $payloadOrangTua);
        
        $response->assertRedirect(route('pewawancara.wawancara.hub', $this->calonSiswa));
        
        $this->assertDatabaseHas('wawancara_orang_tua', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'status' => 'SELESAI',
            'nama_diwawancarai' => 'Joko Santoso',
        ]);

        // Status SPMB should now be SUDAH_DIWAWANCARA
        $this->assertEquals(SpmbStatus::SUDAH_DIWAWANCARA, $this->calonSiswa->fresh()->status_spmb);

        $this->assertDatabaseHas('riwayat_status_spmb', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'status_sebelumnya' => SpmbStatus::MENUNGGU_WAWANCARA->value,
            'status_baru' => SpmbStatus::SUDAH_DIWAWANCARA->value,
        ]);
    }

    public function test_pewawancara_can_view_completed_interview_detail(): void
    {
        WawancaraSiswa::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'pewawancara_id' => $this->pewawancaraUser->id,
            'tanggal_wawancara' => now()->toDateString(),
            'status' => 'SELESAI',
            'catatan_pewawancara' => 'Catatan hasil siswa final.',
            'rekomendasi' => 'TERIMA',
        ]);
        
        WawancaraOrangTua::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'pewawancara_id' => $this->pewawancaraUser->id,
            'tanggal_wawancara' => now()->toDateString(),
            'status' => 'SELESAI',
            'kesan_pewawancara' => 'Kesan ortu final.',
            'hubungan_dengan_siswa' => 'Ayah',
        ]);

        $response = $this->actingAs($this->pewawancaraUser)->get(route('pewawancara.wawancara.show', $this->calonSiswa));

        $response->assertOk();
        $response->assertSee('Hasil Penilaian Wawancara');
        $response->assertSee('Catatan hasil siswa final.');
        $response->assertSee('Kesan ortu final.');
        $response->assertSee('TERIMA');
    }

    public function test_pewawancara_can_view_riwayat_wawancara(): void
    {
        WawancaraSiswa::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'pewawancara_id' => $this->pewawancaraUser->id,
            'tanggal_wawancara' => now()->toDateString(),
            'status' => 'SELESAI',
        ]);

        $response = $this->actingAs($this->pewawancaraUser)->get(route('pewawancara.riwayat'));

        $response->assertOk();
        $response->assertSee('Riwayat Sesi Wawancara');
        $response->assertSee('Budi Santoso');
        $response->assertSee('26AAY0099');
    }

    public function test_pewawancara_can_view_rubrik_instrumen(): void
    {
        $response = $this->actingAs($this->pewawancaraUser)->get(route('pewawancara.instrumen'));

        $response->assertOk();
        $response->assertSee('Instrumen & Rubrik Penilaian', false);
    }

    public function test_pewawancara_can_save_infaq_rutin_bulanan_with_delimiter(): void
    {
        $payloadOrangTua = [
            'action' => 'draft',
            'nama_diwawancarai' => 'Siti Aminah',
            'hubungan_dengan_siswa' => 'Ibu',
            'tinggal_bersama' => 'Orang Tua',
            'jarak_rumah' => '2 km',
            'transportasi' => 'Jalan Kaki',
            'penanggung_jawab_belajar' => 'Ibu',
            'info_wikrama_dari' => 'Tetangga',
            'baca_quran' => 'Ya',
            'infaq_rutin_bulanan' => '250.000',
            'alasan_masuk_wikrama' => 'Karakter baik',
            'alasan_pilih_program_jurusan' => 'Bakat IT',
            'kebiasaan_tempat_tidur' => 'Selalu',
            'merokok' => 'Tidak',
            'alergi' => '-',
        ];

        $response = $this->actingAs($this->pewawancaraUser)
            ->post(route('pewawancara.wawancara.save-orang-tua', $this->calonSiswa), $payloadOrangTua);

        $response->assertRedirect(route('pewawancara.wawancara.hub', $this->calonSiswa));

        // Verify integer storage in DB without delimiters
        $this->assertDatabaseHas('wawancara_orang_tua', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'infaq_rutin_bulanan' => 250000,
        ]);

        $wawancara = WawancaraOrangTua::where('calon_siswa_id', $this->calonSiswa->id)->first();
        $this->assertEquals(250000, $wawancara->infaq_rutin_bulanan);
        $this->assertEquals('250.000', $wawancara->formatted_infaq_rutin_bulanan);

        // Verify it renders correctly in detail view
        $detailResponse = $this->actingAs($this->pewawancaraUser)
            ->get(route('pewawancara.wawancara.show', $this->calonSiswa));
        $detailResponse->assertOk();
        $detailResponse->assertSee('250.000');
    }
}
