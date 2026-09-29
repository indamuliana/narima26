<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\SpmbStatus;
use App\Enums\UserRole;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\PembayaranSeleksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PembayaranSeleksiTest extends TestCase
{
    use RefreshDatabase;

    protected User $siswaUser;
    protected CalonSiswa $calonSiswa;
    protected User $bendahara;
    protected User $pewawancara;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        // Calon Siswa
        $this->siswaUser = User::where('role', 'calon_siswa')->first();
        $program = MasterProgram::first();
        $jurusan = MasterJurusan::first();
        $gelombang = MasterGelombang::first();

        $this->calonSiswa = CalonSiswa::factory()->create([
            'user_id' => $this->siswaUser->id,
            'nomor_pendaftaran' => '26AAY0010',
            'nisn' => $this->siswaUser->username,
            'nama_lengkap' => $this->siswaUser->name,
            'status_spmb' => SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI,
            'program_id' => $program->id,
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);

        // Tagihan Seleksi Awal
        PembayaranSeleksi::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'nominal_tagihan' => 200000,
            'nominal_dibayar' => 0,
            'status' => 'PENDING',
        ]);

        $this->bendahara = User::where('role', 'bendahara')->first();
        $this->pewawancara = User::where('role', 'pewawancara')->first();
    }

    /**
     * Test 1: Calon Siswa dapat melihat halaman pembayaran seleksi.
     */
    public function test_calon_siswa_can_view_pembayaran_seleksi_page(): void
    {
        $response = $this->actingAs($this->siswaUser)->get('/calon-siswa/pembayaran-seleksi');

        $response->assertStatus(200);
        $response->assertSee('Tagihan Biaya Pendaftaran Seleksi');
        $response->assertSee('200.000');
        $response->assertSee('Formulir Konfirmasi Bukti Transfer');
    }

    /**
     * Test 2: Calon Siswa dapat mengunggah bukti transfer seleksi.
     */
    public function test_calon_siswa_can_upload_bukti_transfer(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('struk_atm_mandiri.jpg', 600, 800);

        $response = $this->actingAs($this->siswaUser)->post('/calon-siswa/pembayaran-seleksi', [
            'bank_pengirim' => 'Bank Mandiri',
            'nama_pengirim' => 'Ahmad Subagyo',
            'nomor_referensi' => 'MDR-998877',
            'tanggal_bayar' => now()->toDateString(),
            'nominal_dibayar' => 200000,
            'bukti_transfer' => $file,
        ]);

        $response->assertRedirect('/calon-siswa/pembayaran-seleksi');
        $response->assertSessionHas('success');

        $pembayaran = PembayaranSeleksi::where('calon_siswa_id', $this->calonSiswa->id)->first();
        $this->assertEquals(PaymentStatus::PENDING->value, $pembayaran->status);
        $this->assertEquals('Bank Mandiri', $pembayaran->bank_pengirim);
        $this->assertEquals('Ahmad Subagyo', $pembayaran->nama_pengirim);
        $this->assertNotEmpty($pembayaran->bukti_transfer_path);

        Storage::disk('public')->assertExists($pembayaran->bukti_transfer_path);

        // Audit Trail
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'finance',
            'causer_id' => $this->siswaUser->id,
        ]);
    }

    /**
     * Test 3: Calon Siswa dilarang mengunggah file berbahaya.
     */
    public function test_calon_siswa_cannot_upload_invalid_file(): void
    {
        Storage::fake('public');
        $badFile = UploadedFile::fake()->create('malicious.php', 100);

        $response = $this->actingAs($this->siswaUser)->post('/calon-siswa/pembayaran-seleksi', [
            'bank_pengirim' => 'Bank BCA',
            'nama_pengirim' => 'Pengirim',
            'tanggal_bayar' => now()->toDateString(),
            'nominal_dibayar' => 200000,
            'bukti_transfer' => $badFile,
        ]);

        $response->assertSessionHasErrors(['bukti_transfer']);
    }

    /**
     * Test 4: Bendahara dapat melihat daftar dan detail pembayaran seleksi.
     */
    public function test_bendahara_can_view_pembayaran_seleksi_list_and_detail(): void
    {
        $pembayaran = PembayaranSeleksi::where('calon_siswa_id', $this->calonSiswa->id)->first();

        // 1. List
        $responseList = $this->actingAs($this->bendahara)->get('/bendahara/pembayaran-seleksi');
        $responseList->assertStatus(200);
        $responseList->assertSee('Verifikasi Pembayaran Seleksi');
        $responseList->assertSee($this->calonSiswa->nama_lengkap);

        // 2. Detail
        $responseDetail = $this->actingAs($this->bendahara)->get("/bendahara/pembayaran-seleksi/{$pembayaran->id}");
        $responseDetail->assertStatus(200);
        $responseDetail->assertSee('Tinjau Pembayaran Seleksi');
        $responseDetail->assertSee($this->calonSiswa->nomor_pendaftaran);
    }

    /**
     * Test 5: Bendahara dapat memverifikasi pembayaran seleksi & status SPMB otomatis berubah.
     */
    public function test_bendahara_can_verify_pembayaran_seleksi(): void
    {
        $pembayaran = PembayaranSeleksi::where('calon_siswa_id', $this->calonSiswa->id)->first();
        $pembayaran->update([
            'nominal_dibayar' => 200000,
            'bank_pengirim' => 'BCA',
            'nama_pengirim' => 'Hendra',
            'bukti_transfer_path' => 'bukti-bayar-seleksi/dummy.jpg',
            'status' => 'PENDING',
        ]);

        $response = $this->actingAs($this->bendahara)
            ->post("/bendahara/pembayaran-seleksi/{$pembayaran->id}/verify", [
                'nominal_diterima' => 200000,
                'catatan' => 'Dana mutasi telah masuk di rekening BNI Wikrama.',
            ]);

        $response->assertRedirect('/bendahara/pembayaran-seleksi');
        $response->assertSessionHas('success');

        // Status pembayaran berubah ke DIVERIFIKASI
        $pembayaranFresh = $pembayaran->fresh();
        $this->assertEquals(PaymentStatus::DIVERIFIKASI->value, $pembayaranFresh->status);
        $this->assertEquals($this->bendahara->id, $pembayaranFresh->verified_by);
        $this->assertNotNull($pembayaranFresh->verified_at);

        // Status SPMB calon siswa otomatis berubah ke PEMBAYARAN_SELEKSI_DIVERIFIKASI
        $calonSiswaFresh = $this->calonSiswa->fresh();
        $this->assertEquals(SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI, $calonSiswaFresh->status_spmb);

        // Histori status tercatat
        $this->assertDatabaseHas('riwayat_status_spmb', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'status_baru' => SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI->value,
            'changed_by' => $this->bendahara->id,
        ]);
    }

    /**
     * Test 6: Bendahara dapat menolak bukti transfer dengan alasan.
     */
    public function test_bendahara_can_reject_pembayaran_seleksi(): void
    {
        $pembayaran = PembayaranSeleksi::where('calon_siswa_id', $this->calonSiswa->id)->first();

        $response = $this->actingAs($this->bendahara)
            ->post("/bendahara/pembayaran-seleksi/{$pembayaran->id}/reject", [
                'alasan' => 'Struk ATM buram dan tidak tampak nominal serta nomor rekening tujuan.',
            ]);

        $response->assertRedirect('/bendahara/pembayaran-seleksi');
        $response->assertSessionHas('warning');

        $pembayaranFresh = $pembayaran->fresh();
        $this->assertEquals(PaymentStatus::DITOLAK->value, $pembayaranFresh->status);
        $this->assertEquals('Struk ATM buram dan tidak tampak nominal serta nomor rekening tujuan.', $pembayaranFresh->catatan_bendahara);

        // Calon siswa tetap berstatus MENUNGGU_PEMBAYARAN_SELEKSI agar bisa mengunggah ulang
        $this->assertEquals(SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI, $this->calonSiswa->fresh()->status_spmb);
    }

    /**
     * Test 7: Kwitansi resmi ber-KOP surat dapat diunduh setelah pembayaran diverifikasi.
     */
    public function test_kwitansi_pdf_can_be_downloaded_after_verification(): void
    {
        $pembayaran = PembayaranSeleksi::where('calon_siswa_id', $this->calonSiswa->id)->first();
        $pembayaran->update([
            'nominal_dibayar' => 200000,
            'status' => PaymentStatus::DIVERIFIKASI->value,
            'verified_by' => $this->bendahara->id,
            'verified_at' => now(),
        ]);

        // Calon Siswa mengunduh kwitansi
        $responseSiswa = $this->actingAs($this->siswaUser)->get('/calon-siswa/pembayaran-seleksi/cetak');
        $responseSiswa->assertStatus(200);
        $responseSiswa->assertHeader('content-type', 'application/pdf');

        // Bendahara mengunduh kwitansi
        $responseBendahara = $this->actingAs($this->bendahara)->get("/bendahara/pembayaran-seleksi/{$pembayaran->id}/cetak");
        $responseBendahara->assertStatus(200);
        $responseBendahara->assertHeader('content-type', 'application/pdf');
    }

    /**
     * Test 8: Pembayaran yang belum diverifikasi tidak boleh mengunduh kwitansi (403).
     */
    public function test_unverified_payment_cannot_download_kwitansi(): void
    {
        $pembayaran = PembayaranSeleksi::where('calon_siswa_id', $this->calonSiswa->id)->first();
        $pembayaran->update(['status' => 'PENDING']);

        $response = $this->actingAs($this->siswaUser)->get('/calon-siswa/pembayaran-seleksi/cetak');
        $response->assertStatus(403);
    }

    /**
     * Test 9: Role selain Bendahara/Admin dilarang memverifikasi pembayaran (dialihkan dengan error & 403 on json).
     */
    public function test_non_bendahara_cannot_verify_payment(): void
    {
        $pembayaran = PembayaranSeleksi::where('calon_siswa_id', $this->calonSiswa->id)->first();

        // 1. Web request: Dialihkan ke dashboard sendiri dengan notifikasi error
        $response = $this->actingAs($this->pewawancara)
            ->post("/bendahara/pembayaran-seleksi/{$pembayaran->id}/verify", [
                'nominal_diterima' => 200000,
            ]);

        $response->assertRedirect(route('pewawancara.dashboard'));
        $response->assertSessionHas('error');

        // 2. JSON request: Mendapat HTTP 403 Forbidden
        $responseJson = $this->actingAs($this->pewawancara)
            ->postJson("/bendahara/pembayaran-seleksi/{$pembayaran->id}/verify", [
                'nominal_diterima' => 200000,
            ]);

        $responseJson->assertStatus(403);
    }
}
