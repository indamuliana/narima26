<?php

namespace Tests\Feature;

use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\MasterDesa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterPekerjaan;
use App\Models\MasterProgram;
use App\Models\MasterProvinsi;
use App\Models\MasterSeragam;
use App\Models\PembayaranSeleksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LengkapiDataTest extends TestCase
{
    use RefreshDatabase;

    protected User $siswaUser;
    protected CalonSiswa $calonSiswa;
    protected MasterProvinsi $provinsi;
    protected MasterKabupaten $kabupaten;
    protected MasterKecamatan $kecamatan;
    protected MasterDesa $desa;
    protected MasterPekerjaan $pekerjaan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        $this->siswaUser = User::where('role', 'calon_siswa')->first();
        $program = MasterProgram::first();
        $jurusan = MasterJurusan::first();
        $gelombang = MasterGelombang::first();

        $this->calonSiswa = CalonSiswa::factory()->create([
            'user_id' => $this->siswaUser->id,
            'nomor_pendaftaran' => '26AAY0025',
            'nisn' => $this->siswaUser->username,
            'nama_lengkap' => $this->siswaUser->name,
            'status_spmb' => SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI,
            'status_data' => 'BELUM_LENGKAP',
            'program_id' => $program->id,
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);

        $this->provinsi = MasterProvinsi::first();
        $this->kabupaten = MasterKabupaten::where('provinsi_id', $this->provinsi->id)->first();
        $this->kecamatan = MasterKecamatan::where('kabupaten_id', $this->kabupaten->id)->first();
        $this->desa = MasterDesa::where('kecamatan_id', $this->kecamatan->id)->first();
        $this->pekerjaan = MasterPekerjaan::first();
    }

    public function test_unverified_candidate_cannot_access_lengkapi_data(): void
    {
        // Status is MENUNGGU_PEMBAYARAN_SELEKSI
        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.lengkapi-data.index'));

        $response->assertRedirect(route('calon-siswa.pembayaran-seleksi.index'));
        $response->assertSessionHas('error');
    }

    public function test_verified_candidate_can_access_lengkapi_data_and_transitions_to_melengkapi_data(): void
    {
        $this->calonSiswa->update([
            'status_spmb' => SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI,
        ]);

        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.lengkapi-data.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelengkapan Data & Berkas Persyaratan', false);
        $response->assertSee('Kemajuan Pengisian Data');

        $this->calonSiswa->refresh();
        $this->assertEquals(SpmbStatus::MELENGKAPI_DATA, $this->calonSiswa->status_spmb);
    }

    public function test_cascading_wilayah_api_endpoints(): void
    {
        // 1. Provinsi
        $resProv = $this->getJson('/api/internal/wilayah/provinsi');
        $resProv->assertStatus(200);
        $resProv->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'kode', 'nama'],
            ],
        ]);

        // 2. Kabupaten
        $resKab = $this->getJson("/api/internal/wilayah/kabupaten/{$this->provinsi->id}");
        $resKab->assertStatus(200);
        $resKab->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'provinsi_id', 'kode', 'nama'],
            ],
        ]);

        // 3. Kecamatan
        $resKec = $this->getJson("/api/internal/wilayah/kecamatan/{$this->kabupaten->id}");
        $resKec->assertStatus(200);
        $resKec->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'kabupaten_id', 'kode', 'nama'],
            ],
        ]);

        // 4. Desa
        $resDesa = $this->getJson("/api/internal/wilayah/desa/{$this->kecamatan->id}");
        $resDesa->assertStatus(200);
        $resDesa->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'kecamatan_id', 'kode', 'nama', 'kode_pos'],
            ],
        ]);

        // Public / Aliases
        $resAlias = $this->getJson('/api/wilayah/provinsi');
        $resAlias->assertStatus(200);
    }

    public function test_candidate_can_save_biodata_and_wilayah(): void
    {
        $this->calonSiswa->update([
            'status_spmb' => SpmbStatus::MELENGKAPI_DATA,
        ]);

        $payload = [
            'nama_lengkap' => 'Muhammad Rizki Pratama',
            'nama_panggilan' => 'Rizki',
            'jenis_kelamin' => 'L',
            'nik' => '3205011234560001',
            'no_kk' => '3205011234560002',
            'agama' => 'Islam',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2010-05-15',
            'alamat_lengkap' => 'Jl. Pembangunan No. 88, Tarogong Kidul',
            'rt' => '02',
            'rw' => '07',
            'kode_pos' => '44151',
            'provinsi_id' => $this->provinsi->id,
            'kabupaten_id' => $this->kabupaten->id,
            'kecamatan_id' => $this->kecamatan->id,
            'desa_id' => $this->desa->id,
            'no_hp_siswa' => '081234567890',
            'email' => 'rizki@gmail.com',
        ];

        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.lengkapi-data.biodata'), $payload);

        $response->assertRedirect(route('calon-siswa.lengkapi-data.index', ['tab' => 'biodata']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('calon_siswa', [
            'id' => $this->calonSiswa->id,
            'nik' => '3205011234560001',
            'no_kk' => '3205011234560002',
            'desa_id' => $this->desa->id,
            'rt' => '02',
            'rw' => '07',
        ]);
    }

    public function test_biodata_validation_rejects_invalid_nik(): void
    {
        $this->calonSiswa->update([
            'status_spmb' => SpmbStatus::MELENGKAPI_DATA,
        ]);

        $payload = [
            'nama_lengkap' => 'Rizki',
            'jenis_kelamin' => 'L',
            'nik' => '12345', // Kurang dari 16 digit
            'no_kk' => '12345',
            'agama' => 'Islam',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2010-05-15',
            'alamat_lengkap' => 'Jl. Pembangunan',
            'rt' => '01',
            'rw' => '01',
            'provinsi_id' => $this->provinsi->id,
            'kabupaten_id' => $this->kabupaten->id,
            'kecamatan_id' => $this->kecamatan->id,
            'desa_id' => $this->desa->id,
        ];

        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.lengkapi-data.biodata'), $payload);

        $response->assertSessionHasErrors(['nik', 'no_kk']);
    }

    public function test_candidate_can_save_orang_tua(): void
    {
        $this->calonSiswa->update([
            'status_spmb' => SpmbStatus::MELENGKAPI_DATA,
        ]);

        $payload = [
            'nama_ayah' => 'Ahmad Suhendar',
            'nik_ayah' => '3205010101800001',
            'tahun_lahir_ayah' => '1980',
            'pekerjaan_ayah_id' => $this->pekerjaan->id,
            'penghasilan_ayah' => 'Rp 2.500.000 - Rp 5.000.000',
            'pendidikan_ayah' => 'SMA/SMK',
            'no_hp_ayah' => '081299887766',
            'alamat_ayah' => 'Jl. Pembangunan No. 88 Garut',

            'nama_ibu' => 'Siti Rohayati',
            'nik_ibu' => '3205010101820002',
            'tahun_lahir_ibu' => '1982',
            'pekerjaan_ibu_id' => $this->pekerjaan->id,
            'penghasilan_ibu' => '< Rp 1.000.000',
            'pendidikan_ibu' => 'SMA/SMK',
            'no_hp_ibu' => '081299887755',
            'alamat_ibu' => 'Jl. Pembangunan No. 88 Garut',

            'nama_wali' => null,
        ];

        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.lengkapi-data.orang-tua'), $payload);

        $response->assertRedirect(route('calon-siswa.lengkapi-data.index', ['tab' => 'orang_tua']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('data_orangtua', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'nama_ayah' => 'Ahmad Suhendar',
            'nama_ibu' => 'Siti Rohayati',
            'pekerjaan_ayah_id' => $this->pekerjaan->id,
        ]);

        // Nomor HP ayah tersinkron ke calon_siswa
        $this->calonSiswa->refresh();
        $this->assertEquals('081299887766', $this->calonSiswa->no_hp_ayah);
    }

    public function test_candidate_can_save_akademik_and_prestasi(): void
    {
        $this->calonSiswa->update([
            'status_spmb' => SpmbStatus::MELENGKAPI_DATA,
        ]);

        $payload = [
            'nama_sekolah' => 'SMP Negeri 1 Garut',
            'npsn' => '20225901',
            'nisn' => $this->calonSiswa->nisn,
            'nilai_rata_rata' => 88.50,
            'nilai_bahasa_indonesia' => 90.00,
            'nilai_matematika' => 85.00,
            'nilai_bahasa_inggris' => 88.00,
            'nilai_ipa' => 87.00,
            'nilai_lainnya' => 89.00,
            'catatan' => 'Peringkat 3 umum di kelas IX',
            'prestasi' => [
                [
                    'jenis_prestasi' => 'non-akademik',
                    'tingkat' => 'kabupaten/kota',
                    'nama_prestasi' => 'Juara 1 FLS2N Seni Kriya',
                    'tahun' => '2025',
                    'peringkat' => 'Juara 1',
                    'keterangan' => 'Tingkat Kabupaten Garut',
                ],
            ],
        ];

        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.lengkapi-data.akademik'), $payload);

        $response->assertRedirect(route('calon-siswa.lengkapi-data.index', ['tab' => 'akademik']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('data_akademik', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'nama_sekolah' => 'SMP Negeri 1 Garut',
            'nilai_rata_rata' => 88.50,
            'nilai_matematika' => 85.00,
        ]);

        $this->assertDatabaseHas('prestasi', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'nama_prestasi' => 'Juara 1 FLS2N Seni Kriya',
            'peringkat' => 'Juara 1',
        ]);
    }

    public function test_candidate_can_save_seragam(): void
    {
        $this->calonSiswa->update([
            'status_spmb' => SpmbStatus::MELENGKAPI_DATA,
        ]);

        $masterSeragams = MasterSeragam::aktif()->get()->unique('nama_jenis');
        $seragamPayload = [];
        $i = 0;
        foreach ($masterSeragams as $ms) {
            $seragamPayload[$i++] = [
                'jenis_seragam_id' => $ms->id,
                'ukuran' => 'L',
                'jumlah' => 1,
            ];
        }

        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.lengkapi-data.seragam'), [
                'seragam' => $seragamPayload,
            ]);

        $response->assertRedirect(route('calon-siswa.lengkapi-data.index', ['tab' => 'seragam']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ukuran_seragam', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'ukuran' => 'L',
        ]);
    }

    public function test_candidate_can_upload_dokumen(): void
    {
        Storage::fake('public');

        $this->calonSiswa->update([
            'status_spmb' => SpmbStatus::MELENGKAPI_DATA,
        ]);

        $kkFile = UploadedFile::fake()->create('kartu_keluarga.pdf', 500, 'application/pdf');
        $aktaFile = UploadedFile::fake()->create('akta_kelahiran.jpg', 600, 'image/jpeg');
        $sklFile = UploadedFile::fake()->create('skl_smp.pdf', 700, 'application/pdf');
        $fotoFile = UploadedFile::fake()->image('pas_foto_3x4.jpg', 300, 400);

        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.lengkapi-data.dokumen'), [
                'file_kartu_keluarga' => $kkFile,
                'file_akta_kelahiran' => $aktaFile,
                'file_ijazah_atau_skl' => $sklFile,
                'file_pas_foto' => $fotoFile,
            ]);

        $response->assertRedirect(route('calon-siswa.lengkapi-data.index', ['tab' => 'dokumen']));
        $response->assertSessionHas('success');

        $dokumen = $this->calonSiswa->dokumenPendaftaran()->first();
        $this->assertNotNull($dokumen);
        $this->assertNotNull($dokumen->kk_path);
        $this->assertNotNull($dokumen->akta_path);
        $this->assertNotNull($dokumen->ijazah_skl_path);
        $this->assertNotNull($dokumen->pas_foto_path);

        Storage::disk('public')->assertExists($dokumen->kk_path);
        Storage::disk('public')->assertExists($dokumen->akta_path);
        Storage::disk('public')->assertExists($dokumen->ijazah_skl_path);
        Storage::disk('public')->assertExists($dokumen->pas_foto_path);
    }

    public function test_candidate_cannot_finalize_if_data_incomplete(): void
    {
        $this->calonSiswa->update([
            'status_spmb' => SpmbStatus::MELENGKAPI_DATA,
        ]);

        // Attempt finalize without filling anything
        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.lengkapi-data.finalize'));

        $response->assertRedirect(route('calon-siswa.lengkapi-data.index'));
        $response->assertSessionHas('error');

        $this->calonSiswa->refresh();
        $this->assertEquals('BELUM_LENGKAP', $this->calonSiswa->status_data);
        $this->assertEquals(SpmbStatus::MELENGKAPI_DATA, $this->calonSiswa->status_spmb);
    }

    public function test_candidate_can_finalize_when_all_data_is_complete(): void
    {
        Storage::fake('public');

        $this->calonSiswa->update([
            'status_spmb' => SpmbStatus::MELENGKAPI_DATA,
            'nik' => '3205011234560001',
            'no_kk' => '3205011234560002',
            'agama' => 'Islam',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2010-05-15',
            'alamat_lengkap' => 'Jl. Pembangunan No. 88, Tarogong Kidul',
            'rt' => '02',
            'rw' => '07',
            'kode_pos' => '44151',
            'provinsi_id' => $this->provinsi->id,
            'kabupaten_id' => $this->kabupaten->id,
            'kecamatan_id' => $this->kecamatan->id,
            'desa_id' => $this->desa->id,
        ]);

        // 2. Data Orang Tua
        $this->calonSiswa->dataOrangtua()->create([
            'nama_ayah' => 'Ahmad Suhendar',
            'pekerjaan_ayah_id' => $this->pekerjaan->id,
            'no_hp_ayah' => '081299887766',
            'nama_ibu' => 'Siti Rohayati',
            'pekerjaan_ibu_id' => $this->pekerjaan->id,
            'no_hp_ibu' => '081299887755',
        ]);

        // 3. Data Akademik
        $this->calonSiswa->dataAkademik()->create([
            'nilai_rata_rata' => 88.50,
            'nilai_bahasa_indonesia' => 90.00,
            'nilai_matematika' => 85.00,
            'nilai_bahasa_inggris' => 88.00,
            'nilai_ipa' => 87.00,
        ]);

        // 4. Ukuran Seragam (semua jenis unik)
        $masterSeragams = MasterSeragam::aktif()->get()->unique('nama_jenis');
        foreach ($masterSeragams as $ms) {
            $this->calonSiswa->ukuranSeragam()->create([
                'jenis_seragam_id' => $ms->id,
                'ukuran' => 'L',
                'jumlah' => 1,
            ]);
        }

        // 5. Dokumen Persyaratan
        $this->calonSiswa->dokumenPendaftaran()->create([
            'kk_path' => 'dokumen-siswa/1/kk.pdf',
            'akta_path' => 'dokumen-siswa/1/akta.pdf',
            'ijazah_skl_path' => 'dokumen-siswa/1/ijazah.pdf',
            'pas_foto_path' => 'dokumen-siswa/1/foto.jpg',
        ]);

        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.lengkapi-data.finalize'));

        $response->assertRedirect(route('calon-siswa.lengkapi-data.index'));
        $response->assertSessionHas('success');

        $this->calonSiswa->refresh();
        $this->assertEquals('LENGKAP', $this->calonSiswa->status_data);
        $this->assertEquals(SpmbStatus::DATA_LENGKAP, $this->calonSiswa->status_spmb);

        // Riwayat status & Audit trail created
        $this->assertDatabaseHas('riwayat_status_spmb', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'status_sebelumnya' => 'MELENGKAPI_DATA',
            'status_baru' => 'DATA_LENGKAP',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'spmb_data',
            'subject_id' => $this->calonSiswa->id,
        ]);
    }
}
