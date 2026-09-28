<?php

namespace Tests\Feature;

use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\User;
use App\Services\SpmbStatusService;
use App\Services\WithdrawalService;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class SpmbStatusWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected SpmbStatusService $statusService;
    protected WithdrawalService $withdrawalService;
    protected User $admin;
    protected User $kepsek;
    protected User $bendahara;
    protected CalonSiswa $calonSiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class,
            MasterDataSeeder::class,
        ]);

        $this->statusService = app(SpmbStatusService::class);
        $this->withdrawalService = app(WithdrawalService::class);

        $this->admin = User::where('role', User::ROLE_ADMIN)->first();
        $this->kepsek = User::where('role', User::ROLE_KEPALA_SEKOLAH)->first();
        $this->bendahara = User::where('role', User::ROLE_BENDAHARA)->first();

        $studentUser = User::where('role', User::ROLE_CALON_SISWA)->first();
        $program = MasterProgram::first();
        $jurusan = MasterJurusan::first();
        $gelombang = MasterGelombang::first();

        $this->calonSiswa = CalonSiswa::create([
            'nomor_pendaftaran' => '26AAY0001',
            'user_id' => $studentUser->id,
            'nisn' => '0012345678',
            'jenis_kelamin' => 'L',
            'nama_lengkap' => 'Ahmad Fathoni',
            'program_id' => $program->id,
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'status_spmb' => SpmbStatus::REGISTRASI,
            'status_data' => 'BELUM_LENGKAP',
        ]);
    }

    /**
     * Test normal status progression through each valid step.
     */
    public function test_normal_spmb_status_progression(): void
    {
        $this->assertEquals(SpmbStatus::REGISTRASI, $this->calonSiswa->status_spmb);

        // Step 1: Menunggu Pembayaran Seleksi
        $this->statusService->changeStatus(
            $this->calonSiswa,
            SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI,
            'Calon siswa menyelesaikan registrasi awal',
            null,
            $this->admin
        );
        $this->assertEquals(SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI, $this->calonSiswa->fresh()->status_spmb);

        // Step 2: Pembayaran Seleksi Diverifikasi
        $this->statusService->changeStatus(
            $this->calonSiswa,
            SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI,
            'Pembayaran seleksi diverifikasi oleh bendahara',
            null,
            $this->bendahara
        );
        $this->assertEquals(SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI, $this->calonSiswa->fresh()->status_spmb);

        // Step 3: Melengkapi Data
        $this->statusService->changeStatus(
            $this->calonSiswa,
            SpmbStatus::MELENGKAPI_DATA,
            'Calon siswa mulai mengisi biodata dan berkas'
        );
        $this->assertEquals(SpmbStatus::MELENGKAPI_DATA, $this->calonSiswa->fresh()->status_spmb);

        // Step 4: Data Lengkap
        $this->statusService->changeStatus(
            $this->calonSiswa,
            SpmbStatus::DATA_LENGKAP,
            'Seluruh data wajib telah lengkap'
        );
        $this->assertEquals(SpmbStatus::DATA_LENGKAP, $this->calonSiswa->fresh()->status_spmb);

        // Step 5: Menunggu Wawancara
        $this->statusService->changeStatus(
            $this->calonSiswa,
            SpmbStatus::MENUNGGU_WAWANCARA,
            'Jadwal wawancara telah diagendakan'
        );
        $this->assertEquals(SpmbStatus::MENUNGGU_WAWANCARA, $this->calonSiswa->fresh()->status_spmb);

        // Step 6: Sudah Diwawancara
        $this->statusService->changeStatus(
            $this->calonSiswa,
            SpmbStatus::SUDAH_DIWAWANCARA,
            'Sesi wawancara selesai dilaksanakan'
        );
        $this->assertEquals(SpmbStatus::SUDAH_DIWAWANCARA, $this->calonSiswa->fresh()->status_spmb);

        // Step 7: Menunggu Keputusan
        $this->statusService->changeStatus(
            $this->calonSiswa,
            SpmbStatus::MENUNGGU_KEPUTUSAN,
            'Menunggu rapat kelulusan'
        );
        $this->assertEquals(SpmbStatus::MENUNGGU_KEPUTUSAN, $this->calonSiswa->fresh()->status_spmb);

        // Step 8: Diterima
        $this->statusService->changeStatus(
            $this->calonSiswa,
            SpmbStatus::DITERIMA,
            'Dinyatakan lulus seleksi oleh panitia',
            null,
            $this->admin
        );
        $this->assertEquals(SpmbStatus::DITERIMA, $this->calonSiswa->fresh()->status_spmb);

        // Step 9: Menunggu Daftar Ulang
        $this->statusService->changeStatus(
            $this->calonSiswa,
            SpmbStatus::MENUNGGU_DAFTAR_ULANG,
            'Tagihan daftar ulang telah diterbitkan'
        );
        $this->assertEquals(SpmbStatus::MENUNGGU_DAFTAR_ULANG, $this->calonSiswa->fresh()->status_spmb);

        // Step 10: Daftar Ulang Diverifikasi
        $this->statusService->changeStatus(
            $this->calonSiswa,
            SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI,
            'Pembayaran daftar ulang lunas dan terverifikasi'
        );
        $this->assertEquals(SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI, $this->calonSiswa->fresh()->status_spmb);

        // Step 11: Resmi Terdaftar
        $this->statusService->changeStatus(
            $this->calonSiswa,
            SpmbStatus::RESMI_TERDAFTAR,
            'Resmi terdaftar sebagai murid baru SMK Wikrama 1 Garut'
        );
        $this->assertEquals(SpmbStatus::RESMI_TERDAFTAR, $this->calonSiswa->fresh()->status_spmb);

        // History count should be 11
        $this->assertEquals(11, $this->calonSiswa->riwayatStatus()->count());
    }

    /**
     * Test illegal status jump throws InvalidArgumentException.
     */
    public function test_illegal_status_jump_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        // Cannot jump directly from REGISTRASI to RESMI_TERDAFTAR
        $this->statusService->changeStatus(
            $this->calonSiswa,
            SpmbStatus::RESMI_TERDAFTAR
        );
    }

    /**
     * Test Admin and Kepala Sekolah can mark Calon Siswa as Mengundurkan Diri.
     */
    public function test_admin_and_kepala_sekolah_can_withdraw_candidate(): void
    {
        // 1. Progress to MENUNGGU_PEMBAYARAN_SELEKSI
        $this->statusService->changeStatus(
            $this->calonSiswa,
            SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI
        );

        // 2. Admin withdraws student
        $history = $this->withdrawalService->withdraw(
            $this->calonSiswa,
            'Diterima di sekolah negeri',
            'Konfirmasi via telepon orang tua',
            $this->admin
        );

        $this->assertEquals(SpmbStatus::MENGUNDURKAN_DIRI, $this->calonSiswa->fresh()->status_spmb);
        $this->assertEquals(SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI->value, $history->status_sebelumnya);
        $this->assertEquals(SpmbStatus::MENGUNDURKAN_DIRI->value, $history->status_baru);

        // Check Audit Trail
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'withdrawal',
        ]);

        // Candidate record must NOT be deleted
        $this->assertDatabaseHas('calon_siswa', [
            'id' => $this->calonSiswa->id,
            'status_spmb' => SpmbStatus::MENGUNDURKAN_DIRI->value,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test unauthorized user (Bendahara) cannot withdraw candidate.
     */
    public function test_bendahara_cannot_withdraw_candidate(): void
    {
        $this->expectException(AuthorizationException::class);

        $this->withdrawalService->withdraw(
            $this->calonSiswa,
            'Alasan tidak sah',
            null,
            $this->bendahara
        );
    }

    /**
     * Test restoration from Mengundurkan Diri restores the exact previous state.
     */
    public function test_restoration_from_withdrawal_restores_previous_status(): void
    {
        // Set to PEMBAYARAN_SELEKSI_DIVERIFIKASI
        $this->statusService->changeStatus(
            $this->calonSiswa,
            SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI
        );
        $this->statusService->changeStatus(
            $this->calonSiswa,
            SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI
        );

        // Withdraw
        $this->withdrawalService->withdraw(
            $this->calonSiswa,
            'Salah informasi pengunduran diri',
            null,
            $this->kepsek
        );
        $this->assertEquals(SpmbStatus::MENGUNDURKAN_DIRI, $this->calonSiswa->fresh()->status_spmb);

        // Restore by Admin
        $this->withdrawalService->restore(
            $this->calonSiswa,
            'Orang tua mengonfirmasi tetap melanjutkan pendaftaran',
            $this->admin
        );

        // Must restore back to PEMBAYARAN_SELEKSI_DIVERIFIKASI
        $this->assertEquals(SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI, $this->calonSiswa->fresh()->status_spmb);
    }
}
