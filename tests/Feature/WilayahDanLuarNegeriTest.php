<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\MasterDesa;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterProvinsi;
use App\Models\User;
use App\Services\LengkapiDataService;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\WilayahIndonesiaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WilayahDanLuarNegeriTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(MasterDataSeeder::class);
    }

    protected function getCalonSiswaUser(): array
    {
        $calonSiswa = CalonSiswa::with('user')->first();
        if (!$calonSiswa) {
            $user = User::factory()->create(['role' => 'calon_siswa']);
            $calonSiswa = CalonSiswa::create([
                'nomor_pendaftaran' => 'TEST-001',
                'user_id' => $user->id,
                'nisn' => '1234567890',
                'nama_lengkap' => 'Siswa Pengujian',
                'jenis_kelamin' => 'L',
                'status_spmb' => \App\Enums\SpmbStatus::MELENGKAPI_DATA,
            ]);
        }
        return [$calonSiswa->user, $calonSiswa];
    }

    public function test_api_provinsi_returns_38_provinces(): void
    {
        $response = $this->getJson('/api/internal/wilayah/provinsi');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertCount(38, $data);
    }

    public function test_api_kabupaten_returns_valid_data(): void
    {
        $prov = MasterProvinsi::where('kode', '32')->first();
        $this->assertNotNull($prov);

        $response = $this->getJson("/api/internal/wilayah/kabupaten/{$prov->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertCount(27, $data, 'Jawa Barat must contain 27 regencies and cities.');
    }

    public function test_api_kecamatan_garut_returns_all_42_districts(): void
    {
        $kabGarut = MasterKabupaten::where('kode', '3205')->first();
        $this->assertNotNull($kabGarut);

        $response = $this->getJson("/api/internal/wilayah/kecamatan/{$kabGarut->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertCount(42, $data, 'Kabupaten Garut must contain 42 kecamatan.');
    }

    public function test_api_desa_tarogong_kidul_returns_real_villages(): void
    {
        $kec = MasterKecamatan::where('kode', '3205181')->first();
        $this->assertNotNull($kec);

        $response = $this->getJson("/api/internal/wilayah/desa/{$kec->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertCount(12, $data, 'Tarogong Kidul must contain 12 desa/kelurahan.');
        $names = collect($data)->pluck('nama')->toArray();
        $this->assertContains('Sukagalih', $names);
        $this->assertContains('Jayaraga', $names);
        $this->assertContains('Haurpanggung', $names);
    }


    public function test_saving_luar_negeri_biodata_and_completion_calculation(): void
    {
        [$user, $calonSiswa] = $this->getCalonSiswaUser();
        $service = app(LengkapiDataService::class);

        $foreignData = [
            'nama_lengkap' => 'Muhammad Luqman',
            'nama_panggilan' => 'Luqman',
            'jenis_kelamin' => 'L',
            'nik' => '3205011234560001',
            'no_kk' => '3205011234560002',
            'agama' => 'Islam',
            'tempat_lahir' => 'Kuala Lumpur',
            'tanggal_lahir' => '2008-01-10',
            'alamat_lengkap' => 'Jalan Ampang No. 88, Menara 2',
            'kode_pos' => '50450',
            'is_luar_negeri' => '1',
            'negara' => 'Malaysia',
            'provinsi_luar_negeri' => 'Wilayah Persekutuan',
            'kabupaten_luar_negeri' => 'Kuala Lumpur',
            'kecamatan_luar_negeri' => 'Ampang',
            'desa_luar_negeri' => 'Kampung Baru',
            'anak_ke' => 1,
            'jumlah_saudara' => 2,
            'tahun_lulus' => '2024',
        ];

        $updated = $service->saveBiodata($calonSiswa, $foreignData);

        $this->assertTrue($updated->is_luar_negeri);
        $this->assertEquals('Malaysia', $updated->negara);
        $this->assertEquals('Wilayah Persekutuan', $updated->provinsi_luar_negeri);
        $this->assertEquals('Kuala Lumpur', $updated->kabupaten_luar_negeri);
        $this->assertNull($updated->provinsi_id);
        $this->assertEquals('Wilayah Persekutuan', $updated->nama_provinsi);
        $this->assertEquals('Kuala Lumpur', $updated->nama_kabupaten);
        $this->assertStringContainsString('Malaysia', $updated->alamat_domisili_lengkap);

        $completion = $service->calculateCompletion($updated);
        $this->assertEquals(100, $completion['biodata']['percent']);
        $this->assertTrue($completion['biodata']['is_complete']);
    }

    public function test_saving_domestic_biodata_and_completion_calculation(): void
    {
        [$user, $calonSiswa] = $this->getCalonSiswaUser();
        $service = app(LengkapiDataService::class);

        $prov = MasterProvinsi::where('kode', '32')->first();
        $kab = MasterKabupaten::where('provinsi_id', $prov->id)->first();
        $kec = MasterKecamatan::first();
        $desa = MasterDesa::first();

        $domesticData = [
            'nama_lengkap' => 'Ahmad Fauzi',
            'nama_panggilan' => 'Fauzi',
            'jenis_kelamin' => 'L',
            'nik' => '3205011234560001',
            'no_kk' => '3205011234560002',
            'agama' => 'Islam',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2008-01-10',
            'alamat_lengkap' => 'Jl. Pembangunan No. 12',
            'rt' => '03',
            'rw' => '04',
            'kode_pos' => '44151',
            'is_luar_negeri' => '0',
            'provinsi_id' => $prov->id,
            'kabupaten_id' => $kab->id,
            'kecamatan_id' => $kec->id,
            'desa_id' => $desa->id,
            'anak_ke' => 1,
            'jumlah_saudara' => 2,
            'tahun_lulus' => '2024',
        ];

        $updated = $service->saveBiodata($calonSiswa, $domesticData);

        $this->assertFalse($updated->is_luar_negeri);
        $this->assertEquals('Indonesia', $updated->negara);
        $this->assertEquals($prov->id, $updated->provinsi_id);
        $this->assertEquals($prov->nama, $updated->nama_provinsi);
        $this->assertStringContainsString('RT 03/RW 04', $updated->alamat_domisili_lengkap);

        $completion = $service->calculateCompletion($updated);
        $this->assertEquals(100, $completion['biodata']['percent']);
        $this->assertTrue($completion['biodata']['is_complete']);
    }

    public function test_saving_manual_domestic_biodata_and_completion_calculation(): void
    {
        [$user, $calonSiswa] = $this->getCalonSiswaUser();
        $service = app(LengkapiDataService::class);

        $manualDomesticData = [
            'nama_lengkap' => 'Budi Santoso',
            'nama_panggilan' => 'Budi',
            'jenis_kelamin' => 'L',
            'nik' => '3205011234560001',
            'no_kk' => '3205011234560002',
            'agama' => 'Islam',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2008-01-10',
            'alamat_lengkap' => 'Jl. Patriot No. 45',
            'rt' => '02',
            'rw' => '07',
            'kode_pos' => '44151',
            'is_luar_negeri' => '0',
            'provinsi_nama' => 'Jawa Barat',
            'kabupaten_nama' => 'Kabupaten Garut',
            'kecamatan_nama' => 'Tarogong Kidul',
            'desa_nama' => 'Sukagalih',
            'anak_ke' => 1,
            'jumlah_saudara' => 2,
            'tahun_lulus' => '2024',
        ];

        $updated = $service->saveBiodata($calonSiswa, $manualDomesticData);

        $this->assertFalse($updated->is_luar_negeri);
        $this->assertEquals('Indonesia', $updated->negara);
        $this->assertEquals('Jawa Barat', $updated->nama_provinsi);
        $this->assertEquals('Kabupaten Garut', $updated->nama_kabupaten);
        $this->assertEquals('Tarogong Kidul', $updated->nama_kecamatan);
        $this->assertEquals('Sukagalih', $updated->nama_desa);
        $this->assertStringContainsString('Jawa Barat', $updated->alamat_domisili_lengkap);
        $this->assertStringContainsString('Kabupaten Garut', $updated->alamat_domisili_lengkap);

        $completion = $service->calculateCompletion($updated);
        $this->assertEquals(100, $completion['biodata']['percent']);
        $this->assertTrue($completion['biodata']['is_complete']);
    }
}
