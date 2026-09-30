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
        $jurusans = MasterJurusan::take(2)->get();
        $jurusan = $jurusans[0];
        $jurusan2 = $jurusans[1] ?? $jurusans[0];
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
            'jurusan_id_2' => $jurusan2->id,
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
        $this->assertNotEmpty($calonSiswa->nomor_pendaftaran);
        $this->assertEquals('Ahmad Fathir Al-Faruq', $calonSiswa->nama_lengkap);

        // 2. Status SPMB transisi ke MENUNGGU_PEMBAYARAN_SELEKSI
        $this->assertEquals(SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI, $calonSiswa->status_spmb);
        $this->assertDatabaseHas('riwayat_status_spmb', [
            'calon_siswa_id' => $calonSiswa->id,
            'status_baru' => SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI->value,
        ]);

        // 3. User login otomatis dibuat dengan NISN dan password = Nomor Pendaftaran
        $user = User::where('username', '0098765432')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserRole::CALON_SISWA->value, $user->role);
        $this->assertTrue(Hash::check($calonSiswa->nomor_pendaftaran, $user->password));
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
        $jurusans = MasterJurusan::take(2)->get();

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
            'no_hp_ayah' => '081398765432',
            'email' => 'nama.lain@example.com',
            'program_id' => $program->id,
            'jurusan_id' => $jurusans[0]->id,
            'jurusan_id_2' => $jurusans[1]->id,
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
        $jurusans = MasterJurusan::take(2)->get();

        $postData = [
            'nisn' => '0055667788',
            'nama_lengkap' => 'Siswa HP Salah',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2009-01-01',
            'no_hp_siswa' => '021555123', // Nomor PSTN bukan seluler
            'no_hp_ayah' => '081398765432',
            'email' => 'hp.salah@example.com',
            'program_id' => $program->id,
            'jurusan_id' => $jurusans[0]->id,
            'jurusan_id_2' => $jurusans[1]->id,
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
            'nomor_pendaftaran' => 'A16260055',
            'nama_lengkap' => 'Bintang Ramadhan',
        ]);

        $response = $this->get(route('pendaftaran.sukses', $calonSiswa->nomor_pendaftaran));
        $response->assertStatus(200);
        $response->assertSee('A16260055');
        $response->assertSee('Bintang Ramadhan');
        $response->assertSee('Unduh Kartu Registrasi (PDF)');
    }

    /**
     * Test 7: Download PDF bukti registrasi ber-KOP resmi berhasil.
     */
    public function test_download_registration_pdf_succeeds(): void
    {
        $calonSiswa = CalonSiswa::factory()->create([
            'nomor_pendaftaran' => 'A16260077',
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
            'nomor_pendaftaran' => 'A16260088',
        ]);

        $response = $this->get(route('pendaftaran.login-direct', $calonSiswa->nomor_pendaftaran));
        $response->assertRedirect(route('calon-siswa.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test 9: Registrasi dengan nomor HP Ayah & Ibu serta referensi promotor berhasil disimpan.
     */
    public function test_registration_with_parent_phones_and_referensi_succeeds(): void
    {
        $program = MasterProgram::first();
        $jurusans = MasterJurusan::take(2)->get();
        $gelombang = MasterGelombang::first();

        $postData = [
            'nisn' => '0088991122',
            'nama_lengkap' => 'Putra Mahardika',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2009-04-12',
            'no_hp_siswa' => '081223344111',
            'no_hp_ayah' => '081223344556',
            'no_hp_ibu' => '081334455667',
            'email' => 'putra.m@example.com',
            'program_id' => $program->id,
            'jurusan_id' => $jurusans[0]->id,
            'jurusan_id_2' => $jurusans[1]->id,
            'gelombang_id' => $gelombang->id,
            'referensi_jenis' => 'GURU_WIKRAMA_GARUT',
            'referensi_nama' => 'Pak Budi Santoso',
        ];

        $response = $this->post('/daftar', $postData);
        $response->assertSessionHasNoErrors();

        $calonSiswa = CalonSiswa::where('nisn', '0088991122')->first();
        $this->assertNotNull($calonSiswa);
        $this->assertEquals('GURU_WIKRAMA_GARUT', $calonSiswa->referensi_jenis);
        $this->assertEquals('Pak Budi Santoso', $calonSiswa->referensi_nama);
        $this->assertEquals('6281223344111', $calonSiswa->no_hp_siswa);
        $this->assertEquals('6281223344556', $calonSiswa->no_hp_ayah);
        $this->assertEquals('6281334455667', $calonSiswa->no_hp_ibu);

        $user = User::where('username', '0088991122')->first();
        $this->assertNotNull($user);
        $this->assertEquals('putra.m@example.com', $user->email);
        $this->assertEquals('6281223344111', $user->phone);
    }

    /**
     * Test 10: Email wajib diisi pada formulir pendaftaran.
     */
    public function test_registration_requires_email(): void
    {
        $program = MasterProgram::first();
        $jurusans = MasterJurusan::take(2)->get();

        $postData = [
            'nisn' => '0012345679',
            'nama_lengkap' => 'Calon Siswa Tanpa Email',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2009-02-14',
            'no_hp_siswa' => '081223344112',
            'no_hp_ayah' => '081223344999',
            'email' => '', // kosong
            'program_id' => $program->id,
            'jurusan_id' => $jurusans[0]->id,
            'jurusan_id_2' => $jurusans[1]->id,
        ];

        $response = $this->post('/daftar', $postData);
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test 11: Registrasi dengan input manual asal sekolah tersimpan ke database.
     */
    public function test_registration_with_manual_school_name_saves_to_database(): void
    {
        $program = MasterProgram::first();
        $jurusans = MasterJurusan::take(2)->get();
        $gelombang = MasterGelombang::first();

        $postData = [
            'nisn' => '0099112233',
            'nama_lengkap' => 'Santika Dewi',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2009-07-20',
            'no_hp_siswa' => '085211223344',
            'no_hp_ayah' => '085299887766',
            'email' => 'santika@example.com',
            'program_id' => $program->id,
            'jurusan_id' => $jurusans[0]->id,
            'jurusan_id_2' => $jurusans[1]->id,
            'gelombang_id' => $gelombang->id,
            'asal_sekolah_lainnya' => 'SMP Negeri 1 Tarogong Kidul',
        ];

        $response = $this->post('/daftar', $postData);
        $response->assertSessionHasNoErrors();

        $calonSiswa = CalonSiswa::where('nisn', '0099112233')->first();
        $this->assertNotNull($calonSiswa);
        $this->assertEquals('SMP Negeri 1 Tarogong Kidul', $calonSiswa->asal_sekolah_lainnya);
    }
}


