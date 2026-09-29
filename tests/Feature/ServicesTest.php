<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\CalonSiswa;
use App\Models\Diskon;
use App\Models\MasterBiaya;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\PembayaranDaftarUlang;
use App\Models\PembayaranSeleksi;
use App\Models\Tagihan;
use App\Models\User;
use App\Services\DiscountService;
use App\Services\FileUploadService;
use App\Services\InvoiceService;
use App\Services\InvoiceSnapshotService;
use App\Services\PdfService;
use App\Services\PhoneNumberService;
use App\Services\RegistrationNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServicesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);
    }

    /**
     * Test 1: RegistrationNumberService format, sequencing, and validation.
     */
    public function test_registration_number_service_generates_and_validates(): void
    {
        $service = app(RegistrationNumberService::class);

        // 1. Validasi regex
        $this->assertTrue($service->isValid('A16260001'));
        $this->assertTrue($service->isValid('A16269999'));
        $this->assertFalse($service->isValid('25AAY0001'));
        $this->assertFalse($service->isValid('A1626001'));
        $this->assertFalse($service->isValid('INVALID'));

        // 2. Generate nomor awal (urutan 1)
        $num1 = $service->generate('A1626');
        $this->assertEquals('A16260001', $num1);

        // Simpan calon siswa dengan nomor ini
        $program = MasterProgram::first();
        $jurusan = MasterJurusan::first();
        $gelombang = MasterGelombang::first();

        CalonSiswa::factory()->create([
            'nomor_pendaftaran' => $num1,
            'program_id' => $program->id,
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);

        // 3. Generate nomor kedua (urutan 2)
        $num2 = $service->generate('A1626');
        $this->assertEquals('A16260002', $num2);
    }

    /**
     * Test 2: PhoneNumberService normalization and validation.
     */
    public function test_phone_number_service_normalizes_and_validates(): void
    {
        $service = app(PhoneNumberService::class);

        // Normalisasi format beragam ke 62xxx
        $this->assertEquals('6281233445566', $service->normalize('081233445566'));
        $this->assertEquals('6281233445566', $service->normalize('+6281233445566'));
        $this->assertEquals('6281233445566', $service->normalize('6281233445566'));
        $this->assertEquals('6281233445566', $service->normalize('81233445566'));
        $this->assertEquals('6281233445566', $service->normalize('0812-3344-5566'));
        $this->assertEquals('6281233445566', $service->normalize('  +62 812 3344 5566  '));

        // Validasi
        $this->assertTrue($service->isValid('081234567890'));
        $this->assertTrue($service->isValid('+6281234567890'));
        $this->assertFalse($service->isValid('12345')); // terlalu pendek
        $this->assertFalse($service->isValid('02155566677')); // telepon rumah (bukan seluler 628)
        $this->assertFalse($service->isValid('abc08123456789')); // huruf

        // WhatsApp link
        $waUrl = $service->toWhatsappUrl('081233445566', 'Halo Panitia');
        $this->assertStringContainsString('https://wa.me/6281233445566', $waUrl);
        $this->assertStringContainsString('Halo+Panitia', $waUrl);
    }

    /**
     * Test 3: DiscountService calculations, limits, and audit logs.
     */
    public function test_discount_service_calculates_and_audits(): void
    {
        $service = app(DiscountService::class);
        $calonSiswa = CalonSiswa::factory()->create();
        $admin = User::where('role', 'admin')->first();

        // 1. Kalkulasi persentase
        $potonganPersen = $service->calculatePotongan(10000000, 'persentase', 10);
        $this->assertEquals(1000000, $potonganPersen);

        // 2. Kalkulasi nominal
        $potonganNominal = $service->calculatePotongan(10000000, 'nominal', 500000);
        $this->assertEquals(500000, $potonganNominal);

        // 3. Batas maksimal potongan (tidak boleh melebihi bruto)
        $potonganOver = $service->calculatePotongan(2000000, 'nominal', 5000000);
        $this->assertEquals(2000000, $potonganOver);

        // 4. Create discount model
        $diskon = $service->createDiscount($calonSiswa, [
            'jenis_diskon' => 'Diskon Prestasi Tahfidz',
            'metode_diskon' => 'persentase',
            'nilai_diskon' => 20,
            'alasan' => 'Hafal 5 Juz Al-Quran',
        ], grantedBy: $admin, referensiBruto: 5000000);

        $this->assertInstanceOf(Diskon::class, $diskon);
        $this->assertEquals(1000000, (float) $diskon->nominal_potongan);

        // Periksa audit log
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'finance',
            'causer_id' => $admin->id,
        ]);
    }

    /**
     * Test 4: InvoiceSnapshotService freezes master fees and prevents retroactive modification.
     */
    public function test_invoice_snapshot_service_freezes_master_fees(): void
    {
        $program = MasterProgram::first();
        $gelombang = MasterGelombang::first();

        $calonSiswa = CalonSiswa::factory()->create([
            'program_id' => $program->id,
            'gelombang_id' => $gelombang->id,
        ]);

        $snapshotService = app(InvoiceSnapshotService::class);
        $invoiceService = app(InvoiceService::class);

        // Buat tagihan
        $tagihan = $invoiceService->generateInvoice($calonSiswa);

        $this->assertInstanceOf(Tagihan::class, $tagihan);
        $this->assertNotEmpty($tagihan->nomor_tagihan);
        $this->assertGreaterThan(0, (float) $tagihan->total_bruto);
        $this->assertCount($tagihan->details()->count(), $tagihan->details);

        $originalBruto = (float) $tagihan->total_bruto;

        // UBAH MASTER BIAYA SETELAH TAGIHAN DIBUAT (simulasi perubahan tarif di kemudian hari)
        $detailToTest = $tagihan->details->first();
        $firstBiaya = MasterBiaya::where('kode_biaya', $detailToTest->kode_biaya_snapshot)->first();
        $oldNominal = $firstBiaya->nominal;
        $firstBiaya->update(['nominal' => 99999999]);

        // Tagihan lama HARUS TETAP SAMA (tidak boleh berubah retroaktif)
        $tagihanFresh = $tagihan->fresh();
        $this->assertEquals($originalBruto, (float) $tagihanFresh->total_bruto);

        $detailItem = $tagihanFresh->details()->where('kode_biaya_snapshot', $firstBiaya->kode_biaya)->first();
        $this->assertEquals((float) $oldNominal, (float) $detailItem->nominal_snapshot);
    }

    /**
     * Test 5: InvoiceService payment status sync and remaining balance.
     */
    public function test_invoice_service_syncs_payment_status(): void
    {
        $calonSiswa = CalonSiswa::factory()->create();
        $invoiceService = app(InvoiceService::class);
        $bendahara = User::where('role', 'bendahara')->first();

        $tagihan = $invoiceService->generateInvoice($calonSiswa);
        $this->assertEquals(Tagihan::STATUS_BELUM_LUNAS, $tagihan->status);

        // Simulasi pembayaran cicilan 1 (Rp 1.000.000)
        PembayaranDaftarUlang::create([
            'calon_siswa_id' => $calonSiswa->id,
            'tagihan_id' => $tagihan->id,
            'nominal_tagihan' => $tagihan->total_netto,
            'nominal_dibayar' => 1000000,
            'status' => PaymentStatus::DIVERIFIKASI->value,
            'verified_by' => $bendahara->id,
            'verified_at' => now(),
        ]);

        $tagihan = $invoiceService->syncPaymentStatus($tagihan);
        $this->assertEquals(Tagihan::STATUS_CICILAN, $tagihan->status);
        $this->assertEquals((float) $tagihan->total_netto - 1000000, $invoiceService->getRemainingBalance($tagihan));

        // Simulasi pelunasan sisa tagihan
        $remaining = $invoiceService->getRemainingBalance($tagihan);
        PembayaranDaftarUlang::create([
            'calon_siswa_id' => $calonSiswa->id,
            'tagihan_id' => $tagihan->id,
            'nominal_tagihan' => $tagihan->total_netto,
            'nominal_dibayar' => $remaining,
            'status' => PaymentStatus::DIVERIFIKASI->value,
            'verified_by' => $bendahara->id,
            'verified_at' => now(),
        ]);

        $tagihan = $invoiceService->syncPaymentStatus($tagihan);
        $this->assertEquals(Tagihan::STATUS_LUNAS, $tagihan->status);
        $this->assertEquals(0, $invoiceService->getRemainingBalance($tagihan));
    }

    /**
     * Test 6: FileUploadService sanitizes filenames and validates security constraints.
     */
    public function test_file_upload_service_sanitizes_and_stores(): void
    {
        Storage::fake('public');
        $service = app(FileUploadService::class);

        // 1. Upload Bukti Transfer valid
        $file = UploadedFile::fake()->image('bukti_transfer_bahaya;rm-rf.png', 600, 800)->size(500);
        $path = $service->uploadPaymentProof($file, 'seleksi');

        $this->assertNotEmpty($path);
        // Nama file asli BERBAHAYA tidak boleh tersimpan, harus berupa UUID
        $this->assertStringNotContainsString('bukti_transfer_bahaya', $path);
        $this->assertStringStartsWith('bukti-bayar-seleksi/', $path);
        $this->assertStringEndsWith('.png', $path);
        Storage::disk('public')->assertExists($path);

        // 2. Upload Dokumen Siswa
        $doc = UploadedFile::fake()->create('kartu_keluarga.pdf', 300, 'application/pdf');
        $docPath = $service->uploadStudentDocument($doc, 99, 'kartu-keluarga');

        $this->assertStringStartsWith('dokumen-siswa/99/kartu-keluarga/', $docPath);
        Storage::disk('public')->assertExists($docPath);

        // 3. Rejection file tidak diizinkan (.exe / .php)
        $this->expectException(\InvalidArgumentException::class);
        $badFile = UploadedFile::fake()->create('script.php', 100, 'text/x-php');
        $service->uploadPaymentProof($badFile);
    }

    /**
     * Test 7: PdfService renders all official documents with official letterhead.
     */
    public function test_pdf_service_generates_documents_with_letterhead(): void
    {
        $calonSiswa = CalonSiswa::factory()->create([
            'nomor_pendaftaran' => '26AAY0001',
        ]);

        $pdfService = app(PdfService::class);

        // 1. Pastikan Kop Surat base64 terbaca
        $kopBase64 = $pdfService->getKopSuratBase64();
        $this->assertNotNull($kopBase64);
        $this->assertStringStartsWith('data:image/jpeg;base64,', $kopBase64);

        // 2. Render Kartu Pendaftaran
        $kartuPdf = $pdfService->generateKartuPendaftaran($calonSiswa);
        $outputKartu = $kartuPdf->output();
        $this->assertNotEmpty($outputKartu);
        $this->assertStringStartsWith('%PDF-', $outputKartu);

        // 3. Render Tagihan Daftar Ulang
        $invoiceService = app(InvoiceService::class);
        $tagihan = $invoiceService->generateInvoice($calonSiswa);
        $tagihanPdf = $pdfService->generateTagihan($tagihan);
        $outputTagihan = $tagihanPdf->output();
        $this->assertNotEmpty($outputTagihan);
        $this->assertStringStartsWith('%PDF-', $outputTagihan);

        // 4. Render EULA Kesepahaman
        $eulaPdf = $pdfService->generateEula($calonSiswa);
        $outputEula = $eulaPdf->output();
        $this->assertNotEmpty($outputEula);
        $this->assertStringStartsWith('%PDF-', $outputEula);

        // 5. Render Bukti Pembayaran Seleksi
        $pembayaranSeleksi = PembayaranSeleksi::create([
            'calon_siswa_id' => $calonSiswa->id,
            'nominal_tagihan' => 200000,
            'nominal_dibayar' => 200000,
            'metode_bayar' => 'transfer_bank',
            'bank_pengirim' => 'BCA',
            'nama_pengirim' => 'Budi Santoso',
            'nomor_referensi' => 'TRX-SEL-001',
            'status' => 'DIVERIFIKASI',
            'verified_at' => now(),
        ]);
        $kwitansiPdf = $pdfService->generateBuktiPembayaran($pembayaranSeleksi, 'Biaya Pendaftaran Seleksi');
        $outputKwitansi = $kwitansiPdf->output();
        $this->assertNotEmpty($outputKwitansi);
        $this->assertStringStartsWith('%PDF-', $outputKwitansi);

        // 6. Render Surat Keputusan Kelulusan
        $kelulusanPdf = $pdfService->generateKelulusan($calonSiswa, 'DITERIMA');
        $outputKelulusan = $kelulusanPdf->output();
        $this->assertNotEmpty($outputKelulusan);
        $this->assertStringStartsWith('%PDF-', $outputKelulusan);
    }
}
