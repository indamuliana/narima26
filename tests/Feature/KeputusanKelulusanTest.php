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
        \App\Models\WawancaraSiswa::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'pewawancara_id' => $this->pewawancaraUser->id,
            'tanggal_wawancara' => now()->toDateString(),
            'status' => 'SELESAI',
            'catatan_pewawancara' => 'Motivasi tinggi dan pemahaman jurusan baik',
            'rekomendasi' => 'TERIMA',
        ]);

        \App\Models\WawancaraOrangTua::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'pewawancara_id' => $this->pewawancaraUser->id,
            'tanggal_wawancara' => now()->toDateString(),
            'status' => 'SELESAI',
            'kesan_pewawancara' => 'Orang tua mendukung penuh',
            'nama_diwawancarai' => 'Bapak Budi',
            'hubungan_dengan_siswa' => 'Ayah',
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
        \App\Models\Tagihan::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'nomor_tagihan' => 'TAG-DU260001',
            'jenis_tagihan' => \App\Models\Tagihan::JENIS_DAFTAR_ULANG,
            'program_snapshot' => 'Reguler',
            'gelombang_snapshot' => 'Gelombang 1',
            'total_bruto' => 3450000,
            'total_diskon' => 0,
            'total_netto' => 3450000,
            'status' => \App\Models\Tagihan::STATUS_LUNAS,
        ]);

        $response = $this->actingAs($this->kepalaSekolahUser)
            ->get(route('kepala-sekolah.sidang-kelulusan.show', $this->calonSiswa));

        $response->assertOk();
        $response->assertSee($this->calonSiswa->nama_lengkap);
        $response->assertSee('Hasil Evaluasi Wawancara');
        $response->assertSee('Wawancara Siswa');
        $response->assertSee('Tetapkan / Perbarui Keputusan');
        $response->assertSee('TAG-DU260001');
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

        // Assert Tagihan Daftar Ulang automatically generated and active
        $this->assertDatabaseHas('tagihan', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'jenis_tagihan' => \App\Models\Tagihan::JENIS_DAFTAR_ULANG,
            'status' => \App\Models\Tagihan::STATUS_BELUM_LUNAS,
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

    public function test_kepala_sekolah_batch_decision_generates_invoices_for_all_accepted(): void
    {
        $candidate2 = CalonSiswa::factory()->create([
            'status_spmb' => SpmbStatus::SUDAH_DIWAWANCARA,
            'program_id' => $this->program->id,
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $this->gelombang->id,
        ]);

        $response = $this->actingAs($this->kepalaSekolahUser)
            ->post(route('kepala-sekolah.sidang-kelulusan.batch'), [
                'calon_siswa_ids' => [$this->calonSiswa->id, $candidate2->id],
                'keputusan' => 'DITERIMA',
                'alasan_catatan' => 'Lulus seleksi sidang pleno batch.',
            ]);

        $response->assertRedirect(route('kepala-sekolah.sidang-kelulusan.index'));

        $this->calonSiswa->refresh();
        $candidate2->refresh();
        $this->assertEquals(SpmbStatus::DITERIMA, $this->calonSiswa->status_spmb);
        $this->assertEquals(SpmbStatus::DITERIMA, $candidate2->status_spmb);

        $this->assertDatabaseHas('tagihan', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'jenis_tagihan' => \App\Models\Tagihan::JENIS_DAFTAR_ULANG,
        ]);
        $this->assertDatabaseHas('tagihan', [
            'calon_siswa_id' => $candidate2->id,
            'jenis_tagihan' => \App\Models\Tagihan::JENIS_DAFTAR_ULANG,
        ]);
    }

    public function test_kepala_sekolah_can_view_calon_siswa_directory_and_360_profile(): void
    {
        // Create verified selection payment to test rendering of payment column
        \App\Models\PembayaranSeleksi::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'nominal_tagihan' => 200000,
            'nominal_dibayar' => 200000,
            'tanggal_bayar' => now()->toDateString(),
            'metode_bayar' => 'TRANSFER_BANK',
            'bank_pengirim' => 'BCA',
            'nama_pengirim' => 'Budi Santoso',
            'status' => \App\Models\PembayaranSeleksi::STATUS_DIVERIFIKASI,
            'verified_by' => $this->adminUser->id,
            'verified_at' => now(),
        ]);

        $candidatePending = CalonSiswa::factory()->create([
            'status_spmb' => SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI,
            'program_id' => $this->program->id,
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $this->gelombang->id,
        ]);
        \App\Models\PembayaranSeleksi::create([
            'calon_siswa_id' => $candidatePending->id,
            'nominal_tagihan' => 200000,
            'nominal_dibayar' => 200000,
            'tanggal_bayar' => now()->toDateString(),
            'metode_bayar' => 'TRANSFER_BANK',
            'status' => \App\Models\PembayaranSeleksi::STATUS_PENDING,
        ]);

        // Directory Index
        $indexResponse = $this->actingAs($this->kepalaSekolahUser)
            ->get(route('kepala-sekolah.calon-siswa.index'));

        $indexResponse->assertOk();
        $indexResponse->assertSee('Direktori Data Calon Murid');
        $indexResponse->assertSee($this->calonSiswa->nama_lengkap);
        $indexResponse->assertSee('✓ Lunas');
        $indexResponse->assertSee('⏳ Verifikasi');

        // 360 Full Profile View
        $showResponse = $this->actingAs($this->kepalaSekolahUser)
            ->get(route('kepala-sekolah.calon-siswa.show', $this->calonSiswa));

        $showResponse->assertOk();
        $showResponse->assertSee($this->calonSiswa->nama_lengkap);
        $showResponse->assertSee('Biodata Pribadi Calon Murid');
        $showResponse->assertSee('Hasil Wawancara Seleksi');
        $showResponse->assertSee('Keuangan Daftar Ulang');
        $showResponse->assertSee('Berkas & Dokumen Terunggah', false);
    }

    public function test_kepala_sekolah_can_download_calon_siswa_360_pdf(): void
    {
        $response = $this->actingAs($this->kepalaSekolahUser)
            ->get(route('kepala-sekolah.calon-siswa.cetak-pdf', $this->calonSiswa));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
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

