<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\TpaJadwalSiswa;
use App\Models\TpaPengaturan;
use App\Models\TpaSesiSiswa;
use App\Models\TpaSoal;
use App\Models\User;
use App\Services\TpaExamService;
use Database\Seeders\TpaSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TpaFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(TpaSeeder::class);
    }

    /**
     * Calon siswa dapat membuka halaman login khusus TPA.
     */
    public function test_can_view_tpa_login_page(): void
    {
        $response = $this->get(route('tpa.login'));
        $response->assertStatus(200);
        $response->assertSee('TES POTENSI AKADEMIK');
    }

    /**
     * Admin dapat mengakses halaman Bank Soal, Pengaturan, dan Jadwal TPA.
     */
    public function test_admin_can_access_tpa_management_pages(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $this->actingAs($admin)->get(route('admin.tpa.pengaturan.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.tpa.soal.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.tpa.jadwal.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.tpa.hasil.index'))->assertStatus(200);
    }

    /**
     * Pengujian alur pengerjaan soal dan kalkulasi skor otomatis.
     */
    public function test_tpa_exam_service_scoring_flow(): void
    {
        $user = User::create([
            'name' => 'Budi Santoso',
            'username' => '0099887766',
            'email' => 'budi@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_CALON_SISWA,
            'is_active' => true,
        ]);

        $calonSiswa = CalonSiswa::create([
            'user_id' => $user->id,
            'nama_lengkap' => 'Budi Santoso',
            'nomor_pendaftaran' => '260999',
            'nisn' => '0099887766',
        ]);

        $service = app(TpaExamService::class);
        $sesi = $service->getOrCreateSesi($calonSiswa);

        $this->assertNotNull($sesi);
        $this->assertEquals('belum_mulai', $sesi->status);
        $this->assertNotEmpty($sesi->urutan_soal_literasi);
        $this->assertNotEmpty($sesi->urutan_soal_numerasi);

        // Mulai Babak 1
        $service->mulaiBabak($sesi, 'literasi');
        $sesi->refresh();
        $this->assertEquals('babak_1', $sesi->status);

        // Jawab soal pertama di literasi dengan kunci yang benar
        $firstLitId = $sesi->urutan_soal_literasi[0];
        $soal = TpaSoal::find($firstLitId);

        $service->simpanJawaban($sesi, $soal->id, $soal->kunci_jawaban, false);

        // Selesaikan Babak 1
        $service->selesaikanBabak1($sesi);
        $sesi->refresh();
        $this->assertEquals('transisi', $sesi->status);

        // Mulai Babak 2
        $service->mulaiBabak($sesi, 'numerasi');
        $sesi->refresh();
        $this->assertEquals('babak_2', $sesi->status);

        // Selesaikan Babak 2
        $service->selesaikanBabak2($sesi);
        $sesi->refresh();

        $this->assertEquals('selesai', $sesi->status);
        $this->assertEquals(1, $sesi->benar_literasi);
        $this->assertEquals($soal->bobot, (float) $sesi->skor_literasi);
        $this->assertEquals($soal->bobot, (float) $sesi->total_skor);
    }

    /**
     * Pengujian auto-diskualifikasi jika pelanggaran melebihi batas.
     */
    public function test_anti_cheat_disqualification_flow(): void
    {
        $user = User::create([
            'name' => 'Siti Aminah',
            'username' => '0088776655',
            'email' => 'siti@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_CALON_SISWA,
            'is_active' => true,
        ]);

        $calonSiswa = CalonSiswa::create([
            'user_id' => $user->id,
            'nama_lengkap' => 'Siti Aminah',
            'nomor_pendaftaran' => '260888',
            'nisn' => '0088776655',
        ]);

        $service = app(TpaExamService::class);
        $sesi = $service->getOrCreateSesi($calonSiswa);
        $service->mulaiBabak($sesi, 'literasi');

        // Simulasikan 3x pelanggaran (batas toleransi)
        $this->actingAs($user)->postJson(route('tpa.pelanggaran'), ['alasan' => 'Ganti tab 1'])->assertJson(['violations' => 1]);
        $this->actingAs($user)->postJson(route('tpa.pelanggaran'), ['alasan' => 'Ganti tab 2'])->assertJson(['violations' => 2]);
        $resp = $this->actingAs($user)->postJson(route('tpa.pelanggaran'), ['alasan' => 'Ganti tab 3']);

        $resp->assertJson([
            'violations' => 3,
            'is_disqualified' => true,
        ]);

        $sesi->refresh();
        $this->assertEquals('didiskualifikasi', $sesi->status);
    }

    /**
     * Pengujian izin masuk dan jadwal akses siswa.
     */
    public function test_student_schedule_and_permission_check(): void
    {
        $user = User::create([
            'name' => 'Ahmad Dani',
            'username' => '0077665544',
            'email' => 'ahmad@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_CALON_SISWA,
            'is_active' => true,
        ]);

        $calonSiswa = CalonSiswa::create([
            'user_id' => $user->id,
            'nama_lengkap' => 'Ahmad Dani',
            'nomor_pendaftaran' => '260777',
            'nisn' => '0077665544',
        ]);

        // 1. Saat belum dijadwalkan / izin ditutup
        TpaJadwalSiswa::create([
            'calon_siswa_id' => $calonSiswa->id,
            'is_diizinkan' => false,
        ]);

        $this->actingAs($user)->post(route('tpa.mulai'))
            ->assertRedirect(route('tpa.ruang-tunggu'))
            ->assertSessionHas('error');

        // 2. Saat izin dibuka
        $calonSiswa->tpaJadwal->update([
            'is_diizinkan' => true,
            'waktu_mulai' => now()->subMinutes(10),
            'waktu_selesai' => now()->addMinutes(60),
        ]);

        $this->actingAs($user)->post(route('tpa.mulai'))
            ->assertRedirect(route('tpa.ujian'));
    }
}
