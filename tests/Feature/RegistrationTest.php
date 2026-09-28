<?php

namespace Tests\Feature;

use App\Enums\SpmbStatus;
use App\Enums\UserRole;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\MasterSekolahAsal;
use App\Models\PembayaranSeleksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);
    }

    /**
     * Test 1: Halaman pendaftaran publik dapat diakses dan memuat master data.
     */
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/daftar');

        $response->assertStatus(200);
        $response->assertSee('Formulir Pendaftaran SPMB');
        $response->assertSee('Teknik Jaringan Komputer dan Telekomunikasi');
        $response->assertSee('Pengembangan Perangkat Lunak dan Gim');
    }

    /**
     * Test 2: Route /register otomatis mengarahkan (redirect) ke /daftar.
     */
    public function test_register_url_redirects_to_daftar(): void
    {
        $response = $this->get('/register');
        $response->assertRedirect('/daftar');
    }

    /**
     * Test 3: Registrasi calon siswa baru berhasil membuat data lengkap.
     */
    public function test_calon_siswa_can_register_successfully(): void
    {
        $program = MasterProgram::first();
        $jurusan = MasterJurusan::first();
        $gelombang = MasterGelombang::first();
        $sekolah = MasterSekolahAsal::firstOrCreate(
            ['nama_sekolah' => 'SMPN 1 Garut'],
            ['kabupaten' => 'Garut', 'aktif' => true]
        );

        $postData = [
            'nisn' => '0098765432',
            'nama_lengkap' => 'Ahmad Fathir Al-Faruq',
            'nama_panggilan' => 'Fathir',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2009-08-17',
            'no_hp_siswa' => '081234567890',
            'no_hp_ayah' => '081398765432',
            'email' => 'fathir@example.com',
            'program_id' => $program->id,
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'asal_sekolah_id' => $sekolah->id,
        ];

        $response = $this->post('/daftar', $postData);
        if ($response->exception) {
            throw $response->exception;
        }
        $response->assertSessionHasNoErrors();

        // 1. Pastikan Calon Siswa terdaftar
        $calonSiswa = CalonSiswa::where('nisn', '0098765432')->first();
        $this->assertNotNull($calonSiswa);
        $this->assertEquals('26AAY0001', $calonSiswa->nomor_pendaftaran);
        $this->assertEquals('Ahmad Fathir Al-Faruq', $calonSiswa->nama_lengkap);

        // 2. Status SPMB transisi ke MENUNGGU_PEMBAYARAN_SELEKSI
        $this->assertEquals(SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI, $calonSiswa->status_spmb);
        $this->assertDatabaseHas('riwayat_status_spmb', [
            'calon_siswa_id' => $calonSiswa->id,
            'status_baru' => SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI->value,
        ]);

        // 3. User login otomatis dibuat dengan NISN dan password ddmmyyyy (17082009)
        $user = User::where('username', '0098765432')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserRole::CALON_SISWA->value, $user->role);
        $this->assertTrue(Hash::check('17082009', $user->password));
        $this->assertEquals('6281234567890', $user->phone);

        // 4. Record pembayaran_seleksi awal dibuat
        $this->assertDatabaseHas('pembayaran_seleksi', [
            'calon_siswa_id' => $calonSiswa->id,
            'status' => 'PENDING',
        ]);

        // 5. Audit trail pencatatan registrasi
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'registration',
            'subject_id' => $calonSiswa->id,
        ]);

        // 6. Redirect ke halaman sukses
        $response->assertRedirect(route('pendaftaran.sukses', $calonSiswa->nomor_pendaftaran));
    }

    /**
     * Test 4: Registrasi dengan duplikasi NISN ditolak.
     */
    public function test_duplicate_nisn_is_rejected(): void
    {
        $program = MasterProgram::first();
        $jurusan = MasterJurusan::first();

        // Buat calon siswa pertama
        CalonSiswa::factory()->create(['nisn' => '0011223344']);

        // Coba daftarkan NISN yang sama
        $postData = [
            'nisn' => '0011223344',
            'nama_lengkap' => 'Nama Lain',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2009-05-10',
            'no_hp_siswa' => '082199887766',
            'program_id' => $program->id,
            'jurusan_id' => $jurusan->id,
        ];

        $response = $this->post('/daftar', $postData);
        $response->assertSessionHasErrors(['nisn']);
    }

    /**
     * Test 5: Registrasi dengan nomor HP tidak valid ditolak.
     */
    public function test_invalid_phone_number_is_rejected(): void
    {
        $program = MasterProgram::first();
        $jurusan = MasterJurusan::first();

        $postData = [
            'nisn' => '0055667788',
            'nama_lengkap' => 'Siswa HP Salah',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2009-01-01',
            'no_hp_siswa' => '021555123', // Nomor PSTN bukan seluler
            'program_id' => $program->id,
            'jurusan_id' => $jurusan->id,
        ];

        $response = $this->post('/daftar', $postData);
        $response->assertSessionHasErrors(['no_hp_siswa']);
    }

    /**
     * Test 6: Halaman sukses menampilkan data dan info akun.
     */
    public function test_registration_success_page_renders(): void
    {
        $calonSiswa = CalonSiswa::factory()->create([
            'nomor_pendaftaran' => '26AAY0055',
            'nama_lengkap' => 'Bintang Ramadhan',
        ]);

        $response = $this->get(route('pendaftaran.sukses', $calonSiswa->nomor_pendaftaran));
        $response->assertStatus(200);
        $response->assertSee('26AAY0055');
        $response->assertSee('Bintang Ramadhan');
        $response->assertSee('Unduh Kartu Registrasi (PDF)');
    }

    /**
     * Test 7: Download PDF bukti registrasi ber-KOP resmi berhasil.
     */
    public function test_download_registration_pdf_succeeds(): void
    {
        $calonSiswa = CalonSiswa::factory()->create([
            'nomor_pendaftaran' => '26AAY0077',
        ]);

        $response = $this->get(route('pendaftaran.cetak-akun', $calonSiswa->nomor_pendaftaran));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /**
     * Test 8: Login direct dari halaman registrasi sukses langsung menuju dashboard calon siswa.
     */
    public function test_direct_login_redirects_to_student_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::CALON_SISWA->value,
            'username' => '0099887766',
        ]);

        $calonSiswa = CalonSiswa::factory()->create([
            'user_id' => $user->id,
            'nomor_pendaftaran' => '26AAY0088',
        ]);

        $response = $this->get(route('pendaftaran.login-direct', $calonSiswa->nomor_pendaftaran));
        $response->assertRedirect(route('calon-siswa.dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}
