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
use App\Models\Wawancara;
use App\Models\WawancaraDetail;
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
        // 1. Guest redirected to login
        $this->get(route('pewawancara.dashboard'))
            ->assertRedirect(route('login'));

        // 2. Calon Siswa redirected to candidate dashboard with error
        $this->actingAs($this->siswaUser)
            ->get(route('pewawancara.dashboard'))
            ->assertRedirect(route('calon-siswa.dashboard'))
            ->assertSessionHas('error');

        // 3. Bendahara redirected to bendahara dashboard with error
        $this->actingAs($this->bendaharaUser)
            ->get(route('pewawancara.dashboard'))
            ->assertRedirect(route('bendahara.dashboard'))
            ->assertSessionHas('error');
    }

    public function test_pewawancara_can_view_dashboard_with_metrics(): void
    {
        $response = $this->actingAs($this->pewawancaraUser)
            ->get(route('pewawancara.dashboard'));

        $response->assertOk();
        $response->assertSee('Panel Penguji & Wawancara', false);
        $response->assertSee('Antrian Menunggu');
        $response->assertSee('Budi Santoso');
    }

    public function test_pewawancara_can_view_antrian_list(): void
    {
        $response = $this->actingAs($this->pewawancaraUser)
            ->get(route('pewawancara.antrian'));

        $response->assertOk();
        $response->assertSee('Antrian Calon Siswa');
        $response->assertSee('26AAY0099');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Mulai Wawancara');
    }

    public function test_pewawancara_can_filter_antrian_by_search_and_jurusan(): void
    {
        // Another candidate in different jurusan
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

        // Filter by Budi's name
        $response1 = $this->actingAs($this->pewawancaraUser)
            ->get(route('pewawancara.antrian', ['q' => 'Budi']));
        $response1->assertSee('Budi Santoso');
        $response1->assertDontSee('Rina Gunawan');

        // Filter by Rina's Jurusan
        $response2 = $this->actingAs($this->pewawancaraUser)
            ->get(route('pewawancara.antrian', ['jurusan_id' => $otherJurusan->id]));
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

        $response = $this->actingAs($this->pewawancaraUser)
            ->get(route('pewawancara.wawancara.form', $ineligibleSiswa));

        $response->assertRedirect(route('pewawancara.antrian'));
        $response->assertSessionHas('error');
    }

    public function test_pewawancara_can_open_interview_form_for_eligible_candidate(): void
    {
        $response = $this->actingAs($this->pewawancaraUser)
            ->get(route('pewawancara.wawancara.form', $this->calonSiswa));

        $response->assertOk();
        $response->assertSee('Budi Santoso');
        $response->assertSee('26AAY0099');
        $response->assertSee('Joko Santoso'); // Nama Ayah
        $response->assertSee('88.5'); // Rata-rata Rapor
        $response->assertSee('Kerapihan dan Penampilan');
        $response->assertSee('Dukungan &amp; Perhatian Orang Tua', false);
        $response->assertSee('Simpan Draft');
        $response->assertSee('Selesai Wawancara');
    }

    public function test_pewawancara_can_save_interview_draft(): void
    {
        $kriteriaList = MasterKriteriaWawancara::aktif()->get();
        $penilaian = [];
        foreach ($kriteriaList as $k) {
            $penilaian[$k->id] = [
                'kriteria_id' => $k->id,
                'nilai' => 80,
                'warna' => 'HIJAU',
                'indikator' => 'Baik',
                'catatan' => 'Catatan draft kriteria ' . $k->kode,
            ];
        }

        $payload = [
            'action' => 'draft',
            'tanggal_wawancara' => now()->toDateString(),
            'catatan_umum' => 'Draft catatan siswa memiliki motivasi cukup.',
            'catatan_orang_tua' => 'Draft catatan orang tua bersedia mendukung.',
            'penilaian' => $penilaian,
        ];

        $response = $this->actingAs($this->pewawancaraUser)
            ->post(route('pewawancara.wawancara.store', $this->calonSiswa), $payload);

        $response->assertRedirect(route('pewawancara.wawancara.form', $this->calonSiswa));
        $response->assertSessionHas('success');

        // Check database
        $this->assertDatabaseHas('wawancara', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'pewawancara_id' => $this->pewawancaraUser->id,
            'status' => Wawancara::STATUS_PROSES,
            'catatan_umum' => 'Draft catatan siswa memiliki motivasi cukup.',
        ]);

        $this->assertDatabaseHas('wawancara_detail', [
            'kriteria_id' => $kriteriaList->first()->id,
            'nilai' => 80,
            'warna' => 'HIJAU',
        ]);

        // Status SPMB should still be MENUNGGU_WAWANCARA
        $this->assertEquals(SpmbStatus::MENUNGGU_WAWANCARA, $this->calonSiswa->fresh()->status_spmb);
    }

    public function test_pewawancara_can_finalize_interview_and_transition_status(): void
    {
        $kriteriaList = MasterKriteriaWawancara::aktif()->get();
        $penilaian = [];
        foreach ($kriteriaList as $k) {
            $penilaian[$k->id] = [
                'kriteria_id' => $k->id,
                'nilai' => 90,
                'warna' => 'HIJAU',
                'indikator' => 'Sangat Baik',
                'catatan' => 'Siswa sangat kompeten.',
            ];
        }

        $payload = [
            'action' => 'selesai',
            'tanggal_wawancara' => now()->toDateString(),
            'catatan_umum' => 'Siswa sangat direkomendasikan masuk PPLG.',
            'catatan_orang_tua' => 'Orang tua berkomitmen penuh mendampingi proses belajar.',
            'penilaian' => $penilaian,
        ];

        $response = $this->actingAs($this->pewawancaraUser)
            ->post(route('pewawancara.wawancara.store', $this->calonSiswa), $payload);

        $response->assertRedirect(route('pewawancara.wawancara.show', $this->calonSiswa));
        $response->assertSessionHas('success');

        // Check database: status wawancara = SELESAI
        $this->assertDatabaseHas('wawancara', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'pewawancara_id' => $this->pewawancaraUser->id,
            'status' => Wawancara::STATUS_SELESAI,
            'catatan_umum' => 'Siswa sangat direkomendasikan masuk PPLG.',
        ]);

        // Status SPMB transitioned to SUDAH_DIWAWANCARA
        $this->assertEquals(SpmbStatus::SUDAH_DIWAWANCARA, $this->calonSiswa->fresh()->status_spmb);

        // Audit Trail in riwayat_status_spmb
        $this->assertDatabaseHas('riwayat_status_spmb', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'status_sebelumnya' => SpmbStatus::MENUNGGU_WAWANCARA->value,
            'status_baru' => SpmbStatus::SUDAH_DIWAWANCARA->value,
            'changed_by' => $this->pewawancaraUser->id,
        ]);
    }

    public function test_pewawancara_can_view_completed_interview_detail(): void
    {
        $wawancara = Wawancara::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'pewawancara_id' => $this->pewawancaraUser->id,
            'tanggal_wawancara' => now()->toDateString(),
            'status' => Wawancara::STATUS_SELESAI,
            'catatan_umum' => 'Catatan hasil evaluasi final.',
            'catatan_orang_tua' => 'Catatan orang tua sepakat.',
        ]);

        $firstKriteria = MasterKriteriaWawancara::aktif()->first();
        WawancaraDetail::create([
            'wawancara_id' => $wawancara->id,
            'kriteria_id' => $firstKriteria->id,
            'nilai' => 95,
            'warna' => 'HIJAU',
            'indikator' => 'Sangat Baik',
            'catatan' => 'Sangat menguasai logika.',
        ]);

        $response = $this->actingAs($this->pewawancaraUser)
            ->get(route('pewawancara.wawancara.show', $this->calonSiswa));

        $response->assertOk();
        $response->assertSee('Hasil Penilaian Wawancara');
        $response->assertSee('Catatan hasil evaluasi final.');
        $response->assertSee('95');
        $response->assertSee('Sangat Baik');
    }

    public function test_pewawancara_can_view_riwayat_wawancara(): void
    {
        Wawancara::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'pewawancara_id' => $this->pewawancaraUser->id,
            'tanggal_wawancara' => now()->toDateString(),
            'status' => Wawancara::STATUS_SELESAI,
        ]);

        $response = $this->actingAs($this->pewawancaraUser)
            ->get(route('pewawancara.riwayat'));

        $response->assertOk();
        $response->assertSee('Riwayat Sesi Wawancara');
        $response->assertSee('Budi Santoso');
        $response->assertSee('26AAY0099');
    }

    public function test_pewawancara_can_view_rubrik_instrumen(): void
    {
        $response = $this->actingAs($this->pewawancaraUser)
            ->get(route('pewawancara.instrumen'));

        $response->assertOk();
        $response->assertSee('Instrumen & Rubrik Penilaian', false);
        $response->assertSee('Kerapihan dan Penampilan');
        $response->assertSee('Sikap, Akhlak, dan Adab');
        $response->assertSee('Dukungan &amp; Perhatian Orang Tua', false);
    }
}
