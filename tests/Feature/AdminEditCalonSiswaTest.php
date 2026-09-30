<?php

namespace Tests\Feature;

use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\DataOrangtua;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\MasterSeragam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEditCalonSiswaTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $siswaUser;
    protected User $guruUser;
    protected CalonSiswa $calonSiswa;
    protected MasterJurusan $jurusan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        $this->adminUser = User::where('role', User::ROLE_ADMIN)->first();
        $this->siswaUser = User::where('role', User::ROLE_CALON_SISWA)->first();
        $this->guruUser = User::where('role', User::ROLE_GURU)->first();

        $program = MasterProgram::first();
        $this->jurusan = MasterJurusan::first();
        $gelombang = MasterGelombang::first();

        $this->calonSiswa = CalonSiswa::factory()->create([
            'user_id' => $this->siswaUser->id,
            'nomor_pendaftaran' => '26AAY0888',
            'nisn' => '0012345678',
            'nama_lengkap' => 'Ahmad Fathan',
            'status_spmb' => SpmbStatus::MELENGKAPI_DATA,
            'program_id' => $program->id,
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);
    }

    public function test_admin_can_view_edit_data_screen(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.edit-data', $this->calonSiswa));

        $response->assertStatus(200);
        $response->assertSee('Edit Data Calon Murid');
        $response->assertSee($this->calonSiswa->nama_lengkap);
        $response->assertSee('Formulir Biodata Calon Siswa');
    }

    public function test_admin_can_update_biodata_of_calon_siswa(): void
    {
        $payload = [
            'nama_lengkap' => 'Ahmad Fathan Al-Ghifari',
            'nama_panggilan' => 'Fathan',
            'jenis_kelamin' => 'L',
            'nisn' => '0098765432',
            'jurusan_id' => $this->jurusan->id,
            'nik' => '3205011234567890',
            'no_kk' => '3205019876543210',
            'agama' => 'Islam',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2008-05-15',
            'alamat_lengkap' => 'Jl. Pembangunan No. 123',
            'rt' => '003',
            'rw' => '005',
            'provinsi_nama' => 'Jawa Barat',
            'kabupaten_nama' => 'Garut',
            'kecamatan_nama' => 'Tarogong Kidul',
            'desa_nama' => 'Sukagalih',
            'no_hp_siswa' => '081234567890',
            'tahun_lulus' => '2026',
            'action' => 'save',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.calon-siswa.update-biodata', $this->calonSiswa), $payload);

        $response->assertRedirect(route('admin.calon-siswa.edit-data', [$this->calonSiswa, 'tab' => 'biodata']));
        $response->assertSessionHas('success');

        $this->calonSiswa->refresh();
        $this->assertEquals('Ahmad Fathan Al-Ghifari', $this->calonSiswa->nama_lengkap);
        $this->assertEquals('0098765432', $this->calonSiswa->nisn);
        $this->assertEquals('3205011234567890', $this->calonSiswa->nik);
    }

    public function test_admin_can_update_orang_tua_of_calon_siswa(): void
    {
        $payload = [
            'status_ayah' => 'MASIH_HIDUP',
            'nama_ayah' => 'Deden Sudrajat',
            'nik_ayah' => '3205011111222233',
            'no_hp_ayah' => '081399887766',
            'pekerjaan_ayah' => 'PNS',
            'pendidikan_ayah' => 'D4/S1',
            'penghasilan_ayah' => 'Rp 5.000.000 - Rp 10.000.000',
            'status_ibu' => 'MASIH_HIDUP',
            'nama_ibu' => 'Siti Aminah',
            'nik_ibu' => '3205014444555566',
            'no_hp_ibu' => '081322334455',
            'pekerjaan_ibu' => 'Guru',
            'pendidikan_ibu' => 'D4/S1',
            'penghasilan_ibu' => 'Rp 2.500.000 - Rp 5.000.000',
            'action' => 'save',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.calon-siswa.update-orang-tua', $this->calonSiswa), $payload);

        $response->assertRedirect(route('admin.calon-siswa.edit-data', [$this->calonSiswa, 'tab' => 'orang_tua']));
        $response->assertSessionHas('success');

        $ortu = DataOrangtua::where('calon_siswa_id', $this->calonSiswa->id)->first();
        $this->assertNotNull($ortu);
        $this->assertEquals('Deden Sudrajat', $ortu->nama_ayah);
        $this->assertEquals('Siti Aminah', $ortu->nama_ibu);
    }

    public function test_admin_can_update_akademik_of_calon_siswa(): void
    {
        $payload = [
            'mtk_sem1' => 85,
            'mtk_sem2' => 88,
            'mtk_sem3' => 90,
            'mtk_sem4' => 87,
            'mtk_sem5' => 92,
            'ind_sem1' => 80,
            'ind_sem2' => 82,
            'ind_sem3' => 84,
            'ind_sem4' => 85,
            'ind_sem5' => 86,
            'eng_sem1' => 78,
            'eng_sem2' => 80,
            'eng_sem3' => 82,
            'eng_sem4' => 85,
            'eng_sem5' => 88,
            'pai_sem1' => 90,
            'pai_sem2' => 90,
            'pai_sem3' => 92,
            'pai_sem4' => 92,
            'pai_sem5' => 95,
            'nilai_rata_rata' => 86.4,
            'action' => 'save',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.calon-siswa.update-akademik', $this->calonSiswa), $payload);

        $response->assertRedirect(route('admin.calon-siswa.edit-data', [$this->calonSiswa, 'tab' => 'akademik']));
        $response->assertSessionHas('success');

        $this->calonSiswa->refresh();
        $this->assertNotNull($this->calonSiswa->dataAkademik);
        $this->assertEquals(86.4, (float) $this->calonSiswa->dataAkademik->nilai_rata_rata);
    }

    public function test_admin_can_update_kesehatan_of_calon_siswa(): void
    {
        $payload = [
            'tinggi_badan' => 170,
            'berat_badan' => 60,
            'golongan_darah' => 'O+',
            'buta_warna' => 'Tidak buta warna',
            'kesehatan_mata' => 'Normal',
            'penyakit_pernah_diderita' => 'Tidak ada',
            'penyakit_sedang_diderita' => 'Tidak ada',
            'action' => 'save',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.calon-siswa.update-kesehatan', $this->calonSiswa), $payload);

        $response->assertRedirect(route('admin.calon-siswa.edit-data', [$this->calonSiswa, 'tab' => 'kesehatan']));
        $response->assertSessionHas('success');

        $this->calonSiswa->refresh();
        $this->assertNotNull($this->calonSiswa->dataKesehatan);
        $this->assertEquals(170, $this->calonSiswa->dataKesehatan->tinggi_badan);
        $this->assertEquals(60, $this->calonSiswa->dataKesehatan->berat_badan);
    }

    public function test_non_admin_cannot_access_edit_data(): void
    {
        $response = $this->actingAs($this->guruUser)
            ->get(route('admin.calon-siswa.edit-data', $this->calonSiswa));

        $response->assertRedirect(route($this->guruUser->getDashboardRoute()));
    }
}
