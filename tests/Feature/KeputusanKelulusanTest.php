<?php

namespace Tests\Feature;

use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\KeputusanKelulusan;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\User;
use App\Models\Wawancara;
use App\Models\WawancaraDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeputusanKelulusanTest extends TestCase
{
    use RefreshDatabase;

    protected User $kepalaSekolahUser;
    protected User $adminUser;
    protected User $pewawancaraUser;
    protected User $siswaUser;
    protected CalonSiswa $calonSiswa;
    protected MasterJurusan $jurusan;
    protected MasterProgram $program;
    protected MasterGelombang $gelombang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        $this->kepalaSekolahUser = User::where('role', User::ROLE_KEPALA_SEKOLAH)->first();
        $this->adminUser = User::where('role', User::ROLE_ADMIN)->first();
        $this->pewawancaraUser = User::where('role', User::ROLE_PEWAWANCARA)->first();
        $this->siswaUser = User::where('role', User::ROLE_CALON_SISWA)->first();

        $this->program = MasterProgram::first();
        $this->jurusan = MasterJurusan::first();
        $this->gelombang = MasterGelombang::first();

        $this->calonSiswa = CalonSiswa::factory()->create([
            'user_id' => $this->siswaUser->id,
            'nomor_pendaftaran' => '26AAY0001',
            'nisn' => '0099887766',
            'nama_lengkap' => 'Muhammad Rizky',
            'status_spmb' => SpmbStatus::SUDAH_DIWAWANCARA,
            'program_id' => $this->program->id,
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $this->gelombang->id,
        ]);

        $kriteria = \App\Models\MasterKriteriaWawancara::first();

        // Create completed interview for candidate
        $wawancara = Wawancara::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'pewawancara_id' => $this->pewawancaraUser->id,
            'tanggal_wawancara' => now()->toDateString(),
            'status' => 'SELESAI',
            'catatan_umum' => 'Motivasi tinggi dan pemahaman jurusan baik',
            'catatan_orang_tua' => 'Orang tua mendukung penuh',
        ]);

        WawancaraDetail::create([
            'wawancara_id' => $wawancara->id,
            'kriteria_id' => $kriteria->id,
            'indikator' => 'Sangat Baik',
            'nilai' => 90,
            'warna' => 'HIJAU',
            'catatan' => 'Sopan dan ramah',
        ]);
    }

    public function test_kepala_sekolah_can_view_dashboard_and_sidang_pleno_index(): void
    {
        $dashResponse = $this->actingAs($this->kepalaSekolahUser)
            ->get(route('kepala-sekolah.dashboard'));

        $dashResponse->assertOk();
        $dashResponse->assertSee('Executive Management');
        $dashResponse->assertSee('Keterisian Kuota Kompetensi Keahlian');

        $indexResponse = $this->actingAs($this->kepalaSekolahUser)
            ->get(route('kepala-sekolah.sidang-kelulusan.index'));

        $indexResponse->assertOk();
        $indexResponse->assertSee('Sidang Pleno Kelulusan SPMB');
        $indexResponse->assertSee($this->calonSiswa->nama_lengkap);
    }

    public function test_admin_can_also_access_sidang_pleno_routes(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('kepala-sekolah.sidang-kelulusan.index'));

        $response->assertOk();
        $response->assertSee('Sidang Pleno Kelulusan SPMB');
    }

    public function test_kepala_sekolah_can_view_candidate_evaluation_room(): void
    {
        $response = $this->actingAs($this->kepalaSekolahUser)
            ->get(route('kepala-sekolah.sidang-kelulusan.show', $this->calonSiswa));

        $response->assertOk();
        $response->assertSee($this->calonSiswa->nama_lengkap);
        $response->assertSee('Hasil Evaluasi Tes Wawancara');
        $response->assertSee('Kerapihan dan Penampilan');
        $response->assertSee('Tetapkan / Perbarui Keputusan');
    }

    public function test_kepala_sekolah_can_decide_candidate_diterima(): void
    {
        $response = $this->actingAs($this->kepalaSekolahUser)
            ->post(route('kepala-sekolah.sidang-kelulusan.putuskan', $this->calonSiswa), [
                'keputusan' => 'DITERIMA',
                'alasan_catatan' => 'Lulus seleksi wawancara dengan predikat Sangat Baik.',
            ]);

        $response->assertRedirect(route('kepala-sekolah.sidang-kelulusan.show', $this->calonSiswa));

        $this->calonSiswa->refresh();
        $this->assertEquals(SpmbStatus::DITERIMA, $this->calonSiswa->status_spmb);

        $this->assertDatabaseHas('keputusan_kelulusan', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'keputusan' => 'DITERIMA',
            'ditetapkan_oleh' => $this->kepalaSekolahUser->id,
        ]);
    }

    public function test_kepala_sekolah_can_decide_candidate_ditolak(): void
    {
        $response = $this->actingAs($this->kepalaSekolahUser)
            ->post(route('kepala-sekolah.sidang-kelulusan.putuskan', $this->calonSiswa), [
                'keputusan' => 'DITOLAK',
                'alasan_catatan' => 'Skor observasi belum memenuhi standar minimal.',
            ]);

        $response->assertRedirect(route('kepala-sekolah.sidang-kelulusan.show', $this->calonSiswa));

        $this->calonSiswa->refresh();
        $this->assertEquals(SpmbStatus::DITOLAK, $this->calonSiswa->status_spmb);

        $this->assertDatabaseHas('keputusan_kelulusan', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'keputusan' => 'DITOLAK',
        ]);
    }

    public function test_batch_pleno_decision_for_multiple_candidates(): void
    {
        $user2 = User::factory()->create(['role' => User::ROLE_CALON_SISWA, 'is_active' => true]);
        $calonSiswa2 = CalonSiswa::factory()->create([
            'user_id' => $user2->id,
            'status_spmb' => SpmbStatus::SUDAH_DIWAWANCARA,
            'program_id' => $this->program->id,
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $this->gelombang->id,
        ]);

        $response = $this->actingAs($this->kepalaSekolahUser)
            ->post(route('kepala-sekolah.sidang-kelulusan.batch'), [
                'calon_siswa_ids' => [$this->calonSiswa->id, $calonSiswa2->id],
                'keputusan' => 'DITERIMA',
                'catatan_sidang' => 'Dinyatakan diterima bersama dalam sidang pleno.',
            ]);

        $response->assertRedirect(route('kepala-sekolah.sidang-kelulusan.index'));

        $this->calonSiswa->refresh();
        $calonSiswa2->refresh();

        $this->assertEquals(SpmbStatus::DITERIMA, $this->calonSiswa->status_spmb);
        $this->assertEquals(SpmbStatus::DITERIMA, $calonSiswa2->status_spmb);
    }

    public function test_candidate_can_download_surat_keputusan_pdf_when_diterima(): void
    {
        // Decide candidate DITERIMA first
        $this->actingAs($this->kepalaSekolahUser)
            ->post(route('kepala-sekolah.sidang-kelulusan.putuskan', $this->calonSiswa), [
                'keputusan' => 'DITERIMA',
            ]);

        // Student accesses download
        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.dokumen.kelulusan'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');

        // Student dashboard reflects DITERIMA
        $dashResponse = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.dashboard'));

        $dashResponse->assertOk();
        $dashResponse->assertSee('Selamat! Anda Dinyatakan LULUS');
    }

    public function test_candidate_can_download_surat_keputusan_pdf_when_ditolak(): void
    {
        // Decide candidate DITOLAK
        $this->actingAs($this->kepalaSekolahUser)
            ->post(route('kepala-sekolah.sidang-kelulusan.putuskan', $this->calonSiswa), [
                'keputusan' => 'DITOLAK',
                'alasan_catatan' => 'Kandidat belum lulus seleksi',
            ]);

        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.dokumen.kelulusan'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');

        // Student dashboard reflects DITOLAK
        $dashResponse = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.dashboard'));

        $dashResponse->assertOk();
        $dashResponse->assertSee('Pemberitahuan Hasil Seleksi SPMB');
    }

    public function test_candidate_without_decision_cannot_download_surat_keputusan(): void
    {
        // Set candidate status to MENUNGGU_WAWANCARA (not yet decided)
        $this->calonSiswa->update(['status_spmb' => SpmbStatus::MENUNGGU_WAWANCARA]);

        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.dokumen.kelulusan'));

        $response->assertRedirect(route('calon-siswa.dokumen.index'));
        $response->assertSessionHas('error');
    }

    public function test_kepala_sekolah_can_process_withdrawal(): void
    {
        $response = $this->actingAs($this->kepalaSekolahUser)
            ->post(route('kepala-sekolah.pengunduran-diri.store', $this->calonSiswa), [
                'alasan' => 'Diterima di SMA Negeri 1 Garut',
                'catatan' => 'Surat permohonan pengunduran diri orang tua terlampir',
            ]);

        $response->assertRedirect(route('kepala-sekolah.pengunduran-diri.index', ['tab' => 'withdrawn']));

        $this->calonSiswa->refresh();
        $this->assertEquals(SpmbStatus::MENGUNDURKAN_DIRI, $this->calonSiswa->status_spmb);

        $this->assertDatabaseHas('riwayat_status_spmb', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'status_sebelumnya' => SpmbStatus::SUDAH_DIWAWANCARA->value,
            'status_baru' => SpmbStatus::MENGUNDURKAN_DIRI->value,
        ]);
    }

    public function test_kepala_sekolah_can_restore_withdrawn_candidate(): void
    {
        // Withdraw candidate first
        $this->actingAs($this->kepalaSekolahUser)
            ->post(route('kepala-sekolah.pengunduran-diri.store', $this->calonSiswa), [
                'alasan' => 'Permohonan awal orang tua',
            ]);

        $this->calonSiswa->refresh();
        $this->assertEquals(SpmbStatus::MENGUNDURKAN_DIRI, $this->calonSiswa->status_spmb);

        // Restore candidate
        $response = $this->actingAs($this->kepalaSekolahUser)
            ->post(route('kepala-sekolah.pengunduran-diri.restore', $this->calonSiswa), [
                'alasan_restorasi' => 'Orang tua membatalkan penarikan berkas dan siap mendaftar ulang',
            ]);

        $response->assertRedirect(route('kepala-sekolah.pengunduran-diri.index', ['tab' => 'active']));

        $this->calonSiswa->refresh();
        // Restored back to previous status (SUDAH_DIWAWANCARA)
        $this->assertEquals(SpmbStatus::SUDAH_DIWAWANCARA, $this->calonSiswa->status_spmb);
    }

    public function test_unauthorized_roles_cannot_access_sidang_kelulusan_or_withdrawal(): void
    {
        $this->actingAs($this->pewawancaraUser)
            ->get(route('kepala-sekolah.sidang-kelulusan.index'))
            ->assertRedirect(route('pewawancara.dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($this->siswaUser)
            ->get(route('kepala-sekolah.pengunduran-diri.index'))
            ->assertRedirect(route('calon-siswa.dashboard'))
            ->assertSessionHas('error');
    }
}
