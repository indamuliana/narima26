<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\Diskon;
use App\Models\MasterBiaya;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\PembayaranDaftarUlang;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KeuanganDaftarUlangTest extends TestCase
{
    use RefreshDatabase;

    protected User $bendaharaUser;
    protected User $siswaUser;
    protected User $siswa2User;
    protected User $pewawancaraUser;
    protected CalonSiswa $calonSiswa;
    protected CalonSiswa $calonSiswa2;
    protected MasterJurusan $jurusan;
    protected MasterProgram $program;
    protected MasterGelombang $gelombang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        $this->bendaharaUser = User::where('role', User::ROLE_BENDAHARA)->first();
        $this->siswaUser = User::where('role', User::ROLE_CALON_SISWA)->first();
        $this->pewawancaraUser = User::where('role', User::ROLE_PEWAWANCARA)->first();

        // Second student for authorization boundary testing
        $this->siswa2User = User::factory()->create([
            'name' => 'Siswa Lain',
            'email' => 'siswa2@test.id',
            'username' => '0099881122',
            'role' => User::ROLE_CALON_SISWA,
        ]);

        $this->program = MasterProgram::first();
        $this->jurusan = MasterJurusan::first();
        $this->gelombang = MasterGelombang::first();

        $this->calonSiswa = CalonSiswa::factory()->create([
            'user_id' => $this->siswaUser->id,
            'nomor_pendaftaran' => '26AAY0001',
            'nisn' => '0099887766',
            'nama_lengkap' => 'Ahmad Santoso',
            'status_spmb' => SpmbStatus::SUDAH_DIWAWANCARA,
            'program_id' => $this->program->id,
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $this->gelombang->id,
        ]);

        $this->calonSiswa2 = CalonSiswa::factory()->create([
            'user_id' => $this->siswa2User->id,
            'nomor_pendaftaran' => '26AAY0002',
            'nisn' => '0099881122',
            'nama_lengkap' => 'Bambang Sudirman',
            'status_spmb' => SpmbStatus::MENUNGGU_WAWANCARA,
            'program_id' => $this->program->id,
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $this->gelombang->id,
        ]);

        Storage::fake('public');
    }

    public function test_bendahara_can_view_tagihan_index_and_dashboard(): void
    {
        $response = $this->actingAs($this->bendaharaUser)
            ->get(route('bendahara.tagihan.index'));

        $response->assertOk();
        $response->assertSee('Tagihan Biaya Daftar Ulang');
        $response->assertSee('Terbitkan Tagihan Baru');

        $dashResponse = $this->actingAs($this->bendaharaUser)
            ->get(route('bendahara.dashboard'));

        $dashResponse->assertOk();
        $dashResponse->assertSee('Kas Masuk Daftar Ulang');
    }

    public function test_bendahara_can_issue_tagihan_snapshot_for_candidate(): void
    {
        $response = $this->actingAs($this->bendaharaUser)
            ->post(route('bendahara.tagihan.store'), [
                'calon_siswa_id' => $this->calonSiswa->id,
                'jatuh_tempo' => now()->addDays(14)->format('Y-m-d'),
                'catatan' => 'Tagihan daftar ulang gelombang 1',
            ]);

        $this->calonSiswa->refresh();
        $tagihan = Tagihan::where('calon_siswa_id', $this->calonSiswa->id)->first();

        $this->assertNotNull($tagihan);
        $this->assertStringStartsWith('TAG-', $tagihan->nomor_tagihan);
        $this->assertGreaterThan(0, $tagihan->total_bruto);
        $this->assertEquals($tagihan->total_bruto, $tagihan->total_netto);
        $this->assertEquals('BELUM_LUNAS', $tagihan->status);
        $this->assertNotEmpty($tagihan->details);

        // Candidate transitioned to MENUNGGU_DAFTAR_ULANG
        $this->assertEquals(SpmbStatus::MENUNGGU_DAFTAR_ULANG, $this->calonSiswa->status_spmb);

        $response->assertRedirect(route('bendahara.tagihan.show', $tagihan));
    }

    public function test_snapshot_immutability_guarantee(): void
    {
        // 1. Issue invoice
        $this->actingAs($this->bendaharaUser)
            ->post(route('bendahara.tagihan.store'), [
                'calon_siswa_id' => $this->calonSiswa->id,
            ]);

        $tagihan = Tagihan::where('calon_siswa_id', $this->calonSiswa->id)->first();
        $firstDetail = $tagihan->details->first();
        $originalNominal = (float) $firstDetail->nominal_snapshot;
        $originalTotalBruto = (float) $tagihan->total_bruto;

        // 2. Change master fee price drastically
        $masterBiaya = MasterBiaya::where('kode_biaya', $firstDetail->kode_biaya_snapshot)->first();
        $this->assertNotNull($masterBiaya);
        $masterBiaya->update(['nominal' => $originalNominal + 5000000]);

        // 3. Reload invoice snapshot from database
        $tagihan->refresh();
        $reloadedDetail = $tagihan->details()->where('id', $firstDetail->id)->first();

        // 4. Verify snapshot was completely immune to master fee alteration
        $this->assertEquals($originalNominal, (float) $reloadedDetail->nominal);
        $this->assertEquals($originalTotalBruto, (float) $tagihan->total_bruto);
    }

    public function test_bendahara_can_apply_percentage_and_nominal_discounts(): void
    {
        // Issue invoice first
        $this->actingAs($this->bendaharaUser)
            ->post(route('bendahara.tagihan.store'), [
                'calon_siswa_id' => $this->calonSiswa->id,
            ]);

        $tagihan = Tagihan::where('calon_siswa_id', $this->calonSiswa->id)->first();
        $initialBruto = (float) $tagihan->total_bruto;

        // Verify show page renders clean optional discount section
        $showResponse = $this->actingAs($this->bendaharaUser)
            ->get(route('bendahara.tagihan.show', $tagihan));
        $showResponse->assertOk();
        $showResponse->assertSee('Diskon & Keringanan Biaya', false);
        $showResponse->assertSee('Opsional');
        $showResponse->assertSee('Tidak Ada Diskon (Tarif Standar Penuh)');
        $showResponse->assertDontSee('Section 24');
        $showResponse->assertDontSeeText('d.id == value');

        // Apply 10% discount
        $response = $this->actingAs($this->bendaharaUser)
            ->post(route('bendahara.diskon.store', $tagihan), [
                'jenis_diskon' => 'Diskon Prestasi Rapor',
                'metode_diskon' => 'persentase',
                'nilai_diskon' => 10,
                'alasan' => 'Juara 1 paralel SMP',
            ]);

        $response->assertRedirect(route('bendahara.tagihan.show', $tagihan));

        $tagihan->refresh();
        $expectedDiscount = round($initialBruto * 0.10);
        $expectedNetto = $initialBruto - $expectedDiscount;

        $this->assertEquals($expectedDiscount, (float) $tagihan->total_diskon);
        $this->assertEquals($expectedNetto, (float) $tagihan->total_netto);
        $this->assertNotNull($tagihan->diskon_id);
    }

    public function test_candidate_can_view_their_tuition_invoice(): void
    {
        // Issue invoice
        $this->actingAs($this->bendaharaUser)
            ->post(route('bendahara.tagihan.store'), [
                'calon_siswa_id' => $this->calonSiswa->id,
            ]);

        $tagihan = Tagihan::where('calon_siswa_id', $this->calonSiswa->id)->first();

        // Candidate accesses /calon-siswa/daftar-ulang
        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.daftar-ulang.index'));

        $response->assertOk();
        $response->assertSee($tagihan->nomor_tagihan);
        $response->assertSee('Rincian Komponen Biaya Baku');
        $response->assertSee('Konfirmasi Transfer');
    }

    public function test_candidate_can_submit_payment_proof(): void
    {
        // Issue invoice
        $this->actingAs($this->bendaharaUser)
            ->post(route('bendahara.tagihan.store'), [
                'calon_siswa_id' => $this->calonSiswa->id,
            ]);

        $file = UploadedFile::fake()->create('bukti_transfer.jpg', 500, 'image/jpeg');

        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.daftar-ulang.store-bayar'), [
                'nominal_dibayar' => 1500000,
                'tanggal_bayar' => '2026-09-28',
                'bank_pengirim' => 'Bank BCA',
                'nama_pengirim' => 'Ahmad Santoso',
                'nomor_referensi' => 'TRX-12345678',
                'bukti_transfer' => $file,
            ]);

        $response->assertRedirect(route('calon-siswa.daftar-ulang.index'));

        $this->assertDatabaseHas('pembayaran_daftar_ulang', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'nominal_dibayar' => 1500000,
            'bank_pengirim' => 'Bank BCA',
            'status' => PaymentStatus::PENDING->value,
        ]);
    }

    public function test_candidate_cannot_submit_payment_exceeding_remaining_balance(): void
    {
        // Issue invoice
        $this->actingAs($this->bendaharaUser)
            ->post(route('bendahara.tagihan.store'), [
                'calon_siswa_id' => $this->calonSiswa->id,
            ]);

        $tagihan = Tagihan::where('calon_siswa_id', $this->calonSiswa->id)->first();
        $excessiveAmount = (float) $tagihan->total_netto + 1000000;

        $file = UploadedFile::fake()->create('bukti.jpg', 300, 'image/jpeg');

        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.daftar-ulang.store-bayar'), [
                'nominal_dibayar' => $excessiveAmount,
                'tanggal_bayar' => '2026-09-28',
                'bank_pengirim' => 'BCA',
                'nama_pengirim' => 'Ahmad',
                'bukti_transfer' => $file,
            ]);

        $response->assertSessionHasErrors('nominal_dibayar');
    }

    public function test_bendahara_can_verify_installment_and_advance_candidate_status(): void
    {
        // Issue invoice
        $this->actingAs($this->bendaharaUser)
            ->post(route('bendahara.tagihan.store'), [
                'calon_siswa_id' => $this->calonSiswa->id,
            ]);

        $tagihan = Tagihan::where('calon_siswa_id', $this->calonSiswa->id)->first();

        // Candidate submits partial payment (half)
        $halfAmount = round($tagihan->total_netto / 2);
        $pembayaran = PembayaranDaftarUlang::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'tagihan_id' => $tagihan->id,
            'nominal_tagihan' => $tagihan->total_netto,
            'nominal_dibayar' => $halfAmount,
            'tanggal_bayar' => now()->toDateString(),
            'metode_bayar' => 'transfer_bank',
            'bank_pengirim' => 'BSI',
            'nama_pengirim' => 'Ahmad Santoso',
            'bukti_transfer_path' => 'dummy/path.jpg',
            'status' => PaymentStatus::PENDING->value,
        ]);

        // Bendahara verifies payment
        $response = $this->actingAs($this->bendaharaUser)
            ->post(route('bendahara.pembayaran-daftar-ulang.verify', $pembayaran), [
                'catatan_bendahara' => 'Valid mutasi BSI masuk',
            ]);

        $response->assertRedirect(route('bendahara.pembayaran-daftar-ulang.show', $pembayaran));

        $pembayaran->refresh();
        $tagihan->refresh();
        $this->calonSiswa->refresh();

        $this->assertEquals(PaymentStatus::DIVERIFIKASI->value, $pembayaran->status);
        $this->assertEquals('CICILAN', $tagihan->status);
        $this->assertEquals(SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI, $this->calonSiswa->status_spmb);
    }

    public function test_full_payment_transitions_tagihan_to_lunas(): void
    {
        // Issue invoice
        $this->actingAs($this->bendaharaUser)
            ->post(route('bendahara.tagihan.store'), [
                'calon_siswa_id' => $this->calonSiswa->id,
            ]);

        $tagihan = Tagihan::where('calon_siswa_id', $this->calonSiswa->id)->first();

        // Full payment submission
        $pembayaran = PembayaranDaftarUlang::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'tagihan_id' => $tagihan->id,
            'nominal_tagihan' => $tagihan->total_netto,
            'nominal_dibayar' => $tagihan->total_netto,
            'tanggal_bayar' => now()->toDateString(),
            'metode_bayar' => 'transfer_bank',
            'bank_pengirim' => 'Mandiri',
            'nama_pengirim' => 'Ahmad Santoso',
            'bukti_transfer_path' => 'dummy/path.jpg',
            'status' => PaymentStatus::PENDING->value,
        ]);

        // Bendahara verifies
        $this->actingAs($this->bendaharaUser)
            ->post(route('bendahara.pembayaran-daftar-ulang.verify', $pembayaran));

        $tagihan->refresh();
        $this->assertEquals('LUNAS', $tagihan->status);
    }

    public function test_bendahara_can_reject_payment_with_reason(): void
    {
        $this->actingAs($this->bendaharaUser)
            ->post(route('bendahara.tagihan.store'), [
                'calon_siswa_id' => $this->calonSiswa->id,
            ]);

        $tagihan = Tagihan::where('calon_siswa_id', $this->calonSiswa->id)->first();

        $pembayaran = PembayaranDaftarUlang::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'tagihan_id' => $tagihan->id,
            'nominal_tagihan' => $tagihan->total_netto,
            'nominal_dibayar' => 1000000,
            'tanggal_bayar' => now()->toDateString(),
            'metode_bayar' => 'transfer_bank',
            'bank_pengirim' => 'BCA',
            'nama_pengirim' => 'Ahmad Santoso',
            'bukti_transfer_path' => 'dummy/path.jpg',
            'status' => PaymentStatus::PENDING->value,
        ]);

        $response = $this->actingAs($this->bendaharaUser)
            ->post(route('bendahara.pembayaran-daftar-ulang.reject', $pembayaran), [
                'catatan_bendahara' => 'Foto bukti buram dan nominal tidak terbaca',
            ]);

        $response->assertRedirect(route('bendahara.pembayaran-daftar-ulang.show', $pembayaran));

        $pembayaran->refresh();
        $this->assertEquals(PaymentStatus::DITOLAK->value, $pembayaran->status);
        $this->assertEquals('Foto bukti buram dan nominal tidak terbaca', $pembayaran->catatan_bendahara);
    }

    public function test_kwitansi_pdf_security_and_printing(): void
    {
        $this->actingAs($this->bendaharaUser)
            ->post(route('bendahara.tagihan.store'), [
                'calon_siswa_id' => $this->calonSiswa->id,
            ]);

        $tagihan = Tagihan::where('calon_siswa_id', $this->calonSiswa->id)->first();

        $pembayaran = PembayaranDaftarUlang::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'tagihan_id' => $tagihan->id,
            'nominal_tagihan' => $tagihan->total_netto,
            'nominal_dibayar' => 1000000,
            'tanggal_bayar' => now()->toDateString(),
            'metode_bayar' => 'transfer_bank',
            'bank_pengirim' => 'BCA',
            'nama_pengirim' => 'Ahmad',
            'bukti_transfer_path' => 'dummy/path.jpg',
            'status' => PaymentStatus::PENDING->value,
        ]);

        // Attempt printing unverified payment -> 403
        $this->actingAs($this->bendaharaUser)
            ->get(route('bendahara.pembayaran-daftar-ulang.cetak-kwitansi', $pembayaran))
            ->assertStatus(403);

        // Verify payment
        $pembayaran->update([
            'status' => PaymentStatus::DIVERIFIKASI->value,
            'verified_by' => $this->bendaharaUser->id,
            'verified_at' => now(),
        ]);

        // Bendahara can print kwitansi
        $this->actingAs($this->bendaharaUser)
            ->get(route('bendahara.pembayaran-daftar-ulang.cetak-kwitansi', $pembayaran))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        // Candidate can print their own kwitansi
        $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.daftar-ulang.cetak-kwitansi', $pembayaran))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        // Other candidate cannot print this kwitansi (403)
        $this->actingAs($this->siswa2User)
            ->get(route('calon-siswa.daftar-ulang.cetak-kwitansi', $pembayaran))
            ->assertStatus(403);
    }

    public function test_master_biaya_crud_and_toggle_by_bendahara(): void
    {
        // 1. Create master biaya
        $response = $this->actingAs($this->bendaharaUser)
            ->post(route('bendahara.master-biaya.store'), [
                'kode_biaya' => 'TEST-01',
                'nama_biaya' => 'Biaya Praktik Tambahan',
                'kategori' => 'PRAKTIK',
                'nominal' => 750000,
                'wajib' => 1,
                'keterangan' => 'Komponen uji coba',
            ]);

        $response->assertRedirect(route('bendahara.master-biaya.index'));
        $this->assertDatabaseHas('master_biaya', ['kode_biaya' => 'TEST-01', 'nominal' => 750000]);

        $biaya = MasterBiaya::where('kode_biaya', 'TEST-01')->first();

        // 2. Update master biaya
        $this->actingAs($this->bendaharaUser)
            ->put(route('bendahara.master-biaya.update', $biaya), [
                'nama_biaya' => 'Biaya Praktik Diperbarui',
                'nominal' => 850000,
            ]);

        $biaya->refresh();
        $this->assertEquals(850000, (float) $biaya->nominal);

        // 3. Toggle master biaya
        $this->actingAs($this->bendaharaUser)
            ->patch(route('bendahara.master-biaya.toggle', $biaya));

        $biaya->refresh();
        $this->assertFalse((bool) $biaya->aktif);
    }

    public function test_unauthorized_user_cannot_access_bendahara_finance_routes(): void
    {
        $this->actingAs($this->pewawancaraUser)
            ->get(route('bendahara.tagihan.index'))
            ->assertRedirect(route('pewawancara.dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($this->siswaUser)
            ->get(route('bendahara.master-biaya.index'))
            ->assertRedirect(route('calon-siswa.dashboard'))
            ->assertSessionHas('error');
    }
}
