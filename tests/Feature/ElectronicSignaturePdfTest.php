<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\DokumenVerifikasi;
use App\Models\KeputusanKelulusan;
use App\Models\KesepahamanEula;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\MasterSekolahAsal;
use App\Models\PembayaranSeleksi;
use App\Models\Tagihan;
use App\Models\TagihanDetail;
use App\Models\User;
use App\Services\ElectronicSignatureService;
use App\Services\PdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ElectronicSignaturePdfTest extends TestCase
{
    use RefreshDatabase;

    protected CalonSiswa $calonSiswa;
    protected User $bendahara;
    protected User $kepalaSekolah;

    protected function setUp(): void
    {
        parent::setUp();

        $jurusan = MasterJurusan::firstOrCreate(
            ['kode' => 'PPLG'],
            ['nama' => 'Pengembangan Perangkat Lunak dan Gim', 'aktif' => true]
        );

        $program = MasterProgram::firstOrCreate(
            ['kode' => 'REG'],
            ['nama' => 'Reguler', 'aktif' => true]
        );

        $gelombang = MasterGelombang::firstOrCreate(
            ['kode' => 'GEL1'],
            [
                'nama' => 'Gelombang 1',
                'periode_mulai' => now()->subDays(10),
                'periode_selesai' => now()->addDays(20),
                'aktif' => true,
            ]
        );

        $sekolah = MasterSekolahAsal::firstOrCreate(
            ['npsn' => '20200001'],
            ['nama_sekolah' => 'SMP Negeri 1 Garut', 'kabupaten_kota' => 'Garut', 'aktif' => true]
        );

        $userCalon = User::factory()->create([
            'role' => User::ROLE_CALON_SISWA,
        ]);

        $this->calonSiswa = CalonSiswa::create([
            'user_id' => $userCalon->id,
            'jurusan_id' => $jurusan->id,
            'program_id' => $program->id,
            'gelombang_id' => $gelombang->id,
            'asal_sekolah_id' => $sekolah->id,
            'nomor_pendaftaran' => 'SPMB260001',
            'nisn' => '0098765432',
            'nama_lengkap' => 'Ahmad Fathir Al-Faruq',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2010-05-15',
            'status_spmb' => 'DITERIMA',
            'status_data' => 'LENGKAP',
        ]);

        $this->bendahara = User::factory()->create([
            'name' => 'Fitria Amalia, S.Pd.',
            'role' => User::ROLE_BENDAHARA,
        ]);

        $this->kepalaSekolah = User::factory()->create([
            'name' => 'Kunedi, S.Si., Gr.',
            'role' => User::ROLE_KEPALA_SEKOLAH,
        ]);
    }

    public function test_electronic_signature_service_generates_valid_qr_and_verification_record(): void
    {
        $service = app(ElectronicSignatureService::class);

        $data = $service->prepareSignatureData(
            jenisDokumen: DokumenVerifikasi::JENIS_KWITANSI_SELEKSI,
            nomorDokumen: 'TRX-SEL-001',
            calonSiswa: $this->calonSiswa,
            penandatanganRole: DokumenVerifikasi::ROLE_BENDAHARA,
            penandatanganNama: 'Fitria Amalia, S.Pd.',
            penandatanganJabatan: 'Bendahara Penerimaan Sekolah',
            metadata: ['nominal' => 200000]
        );

        $this->assertNotEmpty($data['qrCodeBase64']);
        $this->assertStringStartsWith('data:image/png;base64,', $data['qrCodeBase64']);
        $this->assertNotEmpty($data['verificationUrl']);
        $this->assertNotEmpty($data['kodeVerifikasi']);

        $this->assertDatabaseHas('dokumen_verifikasi', [
            'kode_verifikasi' => $data['kodeVerifikasi'],
            'jenis_dokumen' => DokumenVerifikasi::JENIS_KWITANSI_SELEKSI,
            'nomor_dokumen' => 'TRX-SEL-001',
            'calon_siswa_id' => $this->calonSiswa->id,
            'penandatangan_nama' => 'Fitria Amalia, S.Pd.',
        ]);
    }

    public function test_pdf_bukti_pembayaran_contains_single_qr_and_creates_verification(): void
    {
        $pembayaran = PembayaranSeleksi::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'nominal_tagihan' => 200000,
            'nominal_dibayar' => 200000,
            'tanggal_bayar' => now(),
            'metode_bayar' => 'transfer_bank',
            'bank_pengirim' => 'BNI',
            'nama_pengirim' => 'Ahmad Fathir',
            'nomor_referensi' => 'TRX-2026-SEL-001',
            'status' => 'DIVERIFIKASI',
            'verified_by' => $this->bendahara->id,
            'verified_at' => now(),
        ]);

        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generateBuktiPembayaran($pembayaran);
        $output = $pdf->output();

        $this->assertNotEmpty($output);
        $this->assertDatabaseHas('dokumen_verifikasi', [
            'nomor_dokumen' => 'TRX-2026-SEL-001',
            'jenis_dokumen' => DokumenVerifikasi::JENIS_KWITANSI_SELEKSI,
            'penandatangan_role' => DokumenVerifikasi::ROLE_BENDAHARA,
        ]);
    }

    public function test_pdf_tagihan_daftar_ulang_contains_tte_and_creates_verification(): void
    {
        $tagihan = Tagihan::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'nomor_tagihan' => 'TGH-2026-DU-001',
            'program_snapshot' => 'Reguler',
            'gelombang_snapshot' => 'Gelombang 1',
            'total_bruto' => 3500000,
            'total_diskon' => 500000,
            'total_netto' => 3000000,
            'status' => 'BELUM_LUNAS',
        ]);

        TagihanDetail::create([
            'tagihan_id' => $tagihan->id,
            'kode_biaya_snapshot' => 'DSP',
            'nama_biaya_snapshot' => 'Dana Sumbangan Pendidikan',
            'kategori_snapshot' => 'DSP',
            'nominal_snapshot' => 3500000,
            'jumlah' => 1,
            'subtotal' => 3500000,
        ]);

        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generateTagihan($tagihan);
        $output = $pdf->output();

        $this->assertNotEmpty($output);
        $this->assertDatabaseHas('dokumen_verifikasi', [
            'nomor_dokumen' => 'TGH-2026-DU-001',
            'jenis_dokumen' => DokumenVerifikasi::JENIS_TAGIHAN_DAFTAR_ULANG,
            'penandatangan_role' => DokumenVerifikasi::ROLE_BENDAHARA,
        ]);
    }

    public function test_pdf_keputusan_kelulusan_contains_kepala_sekolah_tte(): void
    {
        KeputusanKelulusan::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'keputusan' => 'DITERIMA',
            'alasan_catatan' => 'Lulus tes seleksi & wawancara.',
            'ditetapkan_oleh' => $this->kepalaSekolah->id,
            'ditetapkan_at' => now(),
        ]);

        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generateKelulusan($this->calonSiswa, 'DITERIMA');
        $output = $pdf->output();

        $this->assertNotEmpty($output);
        $this->assertDatabaseHas('dokumen_verifikasi', [
            'jenis_dokumen' => DokumenVerifikasi::JENIS_SK_KELULUSAN,
            'penandatangan_role' => DokumenVerifikasi::ROLE_KEPALA_SEKOLAH,
            'penandatangan_nama' => 'Kunedi, S.Si., Gr.',
        ]);
    }

    public function test_pdf_kesepahaman_eula_contains_kepala_sekolah_tte(): void
    {
        KesepahamanEula::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'versi_dokumen' => 'v1.0',
            'setuju' => true,
            'agreed_at' => now(),
            'agreed_by' => $this->calonSiswa->user_id,
        ]);

        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generateEula($this->calonSiswa);
        $output = $pdf->output();

        $this->assertNotEmpty($output);
        $this->assertDatabaseHas('dokumen_verifikasi', [
            'jenis_dokumen' => DokumenVerifikasi::JENIS_KESEPAHAMAN_EULA,
            'penandatangan_role' => DokumenVerifikasi::ROLE_KEPALA_SEKOLAH,
            'penandatangan_nama' => 'Kunedi, S.Si., Gr.',
        ]);
    }

    public function test_public_verification_url_displays_verified_certificate(): void
    {
        $service = app(ElectronicSignatureService::class);
        $tte = $service->prepareSignatureData(
            jenisDokumen: DokumenVerifikasi::JENIS_KWITANSI_SELEKSI,
            nomorDokumen: 'TRX-VERIFY-TEST',
            calonSiswa: $this->calonSiswa,
            penandatanganRole: DokumenVerifikasi::ROLE_BENDAHARA,
            penandatanganNama: 'Fitria Amalia, S.Pd.',
            penandatanganJabatan: 'Bendahara Penerimaan Sekolah',
            metadata: ['nominal' => 200000, 'metode_bayar' => 'transfer_bank']
        );

        $response = $this->get('/verifikasi-dokumen/' . $tte['kodeVerifikasi']);

        $response->assertStatus(200);
        $response->assertSee('DOKUMEN ASLI & TERVERIFIKASI', false);
        $response->assertSee('AHMAD FATHIR AL-FARUQ', false);
        $response->assertSee('Fitria Amalia, S.Pd.');
        $response->assertSee('Bendahara Penerimaan Sekolah');
        $response->assertSee('200.000');

        // Pastikan scan count bertambah
        $dokumen = DokumenVerifikasi::where('kode_verifikasi', $tte['kodeVerifikasi'])->first();
        $this->assertEquals(1, $dokumen->scan_count);
        $this->assertNotNull($dokumen->last_scanned_at);
    }

    public function test_public_verification_url_displays_warning_for_invalid_code(): void
    {
        $response = $this->get('/verifikasi-dokumen/SPMB-INVALID-CODE-999');

        $response->assertStatus(200);
        $response->assertSee('DOKUMEN TIDAK DITEMUKAN / TIDAK VALID');
        $response->assertSee('SPMB-INVALID-CODE-999');
    }
}
