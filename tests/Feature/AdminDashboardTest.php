<?php

namespace Tests\Feature;

use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\MasterSekolahAsal;
use App\Models\PembayaranSeleksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $kepalaSekolahUser;
    protected User $siswaUser;
    protected CalonSiswa $calonSiswa;
    protected MasterJurusan $jurusan;
    protected MasterProgram $program;
    protected MasterGelombang $gelombang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        $this->adminUser = User::where('role', User::ROLE_ADMIN)->first();
        $this->kepalaSekolahUser = User::where('role', User::ROLE_KEPALA_SEKOLAH)->first();
        $this->siswaUser = User::where('role', User::ROLE_CALON_SISWA)->first();

        $this->program = MasterProgram::first();
        $this->jurusan = MasterJurusan::first();
        $this->gelombang = MasterGelombang::first();

        $this->calonSiswa = CalonSiswa::factory()->create([
            'user_id' => $this->siswaUser->id,
            'nomor_pendaftaran' => '26AAY0001',
            'nisn' => '0099887766',
            'nama_lengkap' => 'Muhammad Rizky Pratama',
            'status_spmb' => SpmbStatus::DITERIMA,
            'program_id' => $this->program->id,
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $this->gelombang->id,
        ]);

        PembayaranSeleksi::create([
            'calon_siswa_id' => $this->calonSiswa->id,
            'nominal_tagihan' => 200000,
            'nominal_dibayar' => 200000,
            'status' => 'DIVERIFIKASI',
            'metode_bayar' => 'transfer_bank',
        ]);

        activity('test_log')
            ->causedBy($this->adminUser)
            ->performedOn($this->calonSiswa)
            ->log('Admin menguji sistem SPMB');
    }

    public function test_admin_can_view_dashboard_with_live_stats(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Panel Administrator');
        $response->assertSee('Total Pendaftar');
        $response->assertSee('Keterisian Kuota Kompetensi Keahlian');
        $response->assertSee($this->calonSiswa->nama_lengkap);
    }

    public function test_admin_can_view_calon_siswa_index_with_datatables_pagination(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.index'));

        $response->assertOk();
        $response->assertSee('Direktori Calon Peserta Didik Baru');
        $response->assertSee($this->calonSiswa->nama_lengkap);
        $response->assertSee($this->calonSiswa->nomor_pendaftaran);
        $response->assertSee('Export CSV');
        $response->assertSee('Export PDF');
    }

    public function test_admin_can_filter_calon_siswa_by_keyword_status_and_jurusan(): void
    {
        // Search by name match
        $responseMatch = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.index', ['q' => 'Rizky']));

        $responseMatch->assertOk();
        $responseMatch->assertSee($this->calonSiswa->nama_lengkap);

        // Search mismatch
        $responseMismatch = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.index', ['q' => 'NamaTidakAdaXYZ']));

        $responseMismatch->assertOk();
        $responseMismatch->assertDontSee($this->calonSiswa->nama_lengkap);

        // Filter by Status DITERIMA match
        $responseStatus = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.index', ['status_spmb' => 'DITERIMA']));

        $responseStatus->assertOk();
        $responseStatus->assertSee($this->calonSiswa->nama_lengkap);

        // Filter by Status DITOLAK mismatch
        $responseStatusMismatch = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.index', ['status_spmb' => 'DITOLAK']));

        $responseStatusMismatch->assertOk();
        $responseStatusMismatch->assertDontSee($this->calonSiswa->nama_lengkap);
    }

    public function test_admin_can_view_candidate_detail_profile(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.show', $this->calonSiswa));

        $response->assertOk();
        $response->assertSee($this->calonSiswa->nama_lengkap);
        $response->assertSee($this->calonSiswa->nomor_pendaftaran);
        $response->assertSee('Biodata Pribadi Calon Murid');
        $response->assertSee('Biaya Seleksi Pendaftaran');
    }

    public function test_admin_can_export_calon_siswa_to_csv(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.export.csv'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_export_calon_siswa_to_pdf(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.calon-siswa.export.pdf'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_view_laporan_dan_rekapitulasi(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.laporan.index'));

        $response->assertOk();
        $response->assertSee('Funnel Konversi Alur Pendaftaran');
        $response->assertSee('Rekapitulasi Keterisian Kuota');
        $response->assertSee('Total Potensi Tagihan Seleksi');
    }

    public function test_admin_can_export_laporan_rekap_pdf(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.laporan.rekap.export.pdf'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_view_audit_trail_logs(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.audit-trail.index'));

        $response->assertOk();
        $response->assertSee('Audit Trail Sistem SPMB');
        $response->assertSee('Admin menguji sistem SPMB');
    }

    public function test_non_admin_cannot_access_admin_dashboard_or_management(): void
    {
        // Student cannot access -> redirected to student dashboard
        $siswaResponse = $this->actingAs($this->siswaUser)
            ->get(route('admin.dashboard'));

        $siswaResponse->assertRedirect(route('calon-siswa.dashboard'));

        // Kepala sekolah cannot access admin-only routes -> redirected to kepsek dashboard
        $kepsekResponse = $this->actingAs($this->kepalaSekolahUser)
            ->get(route('admin.dashboard'));

        $kepsekResponse->assertRedirect(route('kepala-sekolah.dashboard'));
    }

    public function test_admin_can_manage_jurusan(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.jurusan.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Kompetensi Keahlian');

        // Admin can store new jurusan
        $storeResponse = $this->actingAs($this->adminUser)
            ->post(route('admin.jurusan.store'), [
                'kode' => 'ANIMASI',
                'nama' => 'Animasi dan 3D Art',
                'keterangan' => 'Kompetensi kejuruan animasi',
            ]);

        $storeResponse->assertRedirect(route('admin.jurusan.index'));
        $this->assertDatabaseHas('master_jurusan', ['kode' => 'ANIMASI']);
    }

    public function test_admin_can_manage_master_keuangan(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.keuangan.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Master Keuangan');

        // Admin can store new master fee
        $storeResponse = $this->actingAs($this->adminUser)
            ->post(route('admin.keuangan.store'), [
                'kode_biaya' => 'TEST-FEE-01',
                'nama_biaya' => 'Biaya Praktik Laboratorium',
                'kategori' => 'PRAKTIK',
                'nominal' => 500000,
            ]);

        $storeResponse->assertRedirect(route('admin.keuangan.index'));
        $this->assertDatabaseHas('master_biaya', ['kode_biaya' => 'TEST-FEE-01']);
    }

    public function test_admin_can_view_and_verify_pembayaran(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.pembayaran.seleksi'));

        $response->assertOk();
        $response->assertSee('Manajemen Pembayaran Seleksi');

        $pembayaran = PembayaranSeleksi::first();
        if ($pembayaran) {
            $pembayaran->update(['status' => 'PENDING']);
            $this->calonSiswa->update(['status_spmb' => SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI]);

            $verifyResponse = $this->actingAs($this->adminUser)
                ->post(route('admin.pembayaran.seleksi.verify', $pembayaran), [
                    'nominal_dibayar' => 200000,
                ]);

            $verifyResponse->assertRedirect();
            $this->assertEquals('DIVERIFIKASI', $pembayaran->fresh()->status);
        }
    }

    public function test_admin_can_alokasikan_pewawancara(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.alokasi-pewawancara.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Alokasi Pewawancara');

        $pewawancaraUser = User::where('role', User::ROLE_PEWAWANCARA)->first() ?? $this->adminUser;

        $alokasiResponse = $this->actingAs($this->adminUser)
            ->post(route('admin.alokasi-pewawancara.single', $this->calonSiswa), [
                'pewawancara_id' => $pewawancaraUser->id,
            ]);

        $alokasiResponse->assertRedirect();
        $this->assertDatabaseHas('wawancara', [
            'calon_siswa_id' => $this->calonSiswa->id,
            'pewawancara_id' => $pewawancaraUser->id,
        ]);
    }

    public function test_feeder_schools_aggregates_master_and_manual_input(): void
    {
        $sekolahMaster = MasterSekolahAsal::firstOrCreate(
            ['npsn' => '20209999'],
            [
                'nama_sekolah' => 'SMP Negeri 1 Garut',
                'kokab' => 'Garut',
                'jenis' => 'SMP',
                'status' => 'NEGERI',
            ]
        );

        // Siswa 1 dengan master sekolah
        $this->calonSiswa->update([
            'asal_sekolah_id' => $sekolahMaster->id,
            'asal_sekolah_lainnya' => null,
        ]);

        // Siswa 2 dengan input manual
        $user2 = User::factory()->create(['role' => User::ROLE_CALON_SISWA]);
        CalonSiswa::factory()->create([
            'user_id' => $user2->id,
            'nomor_pendaftaran' => '26AAY0002',
            'nisn' => '0099887755',
            'nama_lengkap' => 'Ahmad Fauzi',
            'program_id' => $this->program->id,
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $this->gelombang->id,
            'asal_sekolah_id' => null,
            'asal_sekolah_lainnya' => 'SMP Plus Al-Hikmah Tarogong',
            'kabupaten_nama' => 'Garut',
        ]);

        // Siswa 3 dengan nama manual yang sama
        $user3 = User::factory()->create(['role' => User::ROLE_CALON_SISWA]);
        CalonSiswa::factory()->create([
            'user_id' => $user3->id,
            'nomor_pendaftaran' => '26AAY0003',
            'nisn' => '0099887744',
            'nama_lengkap' => 'Budi Santoso',
            'program_id' => $this->program->id,
            'jurusan_id' => $this->jurusan->id,
            'gelombang_id' => $this->gelombang->id,
            'asal_sekolah_id' => null,
            'asal_sekolah_lainnya' => 'SMP Plus Al-Hikmah Tarogong',
            'kabupaten_nama' => 'Garut',
        ]);

        $metricsService = app(\App\Services\DashboardMetricsService::class);
        $topSchools = $metricsService->getTopAsalSekolah(5);

        $this->assertNotEmpty($topSchools['top']);
        $this->assertEquals('SMP Plus Al-Hikmah Tarogong', $topSchools['top'][0]['nama']);
        $this->assertEquals(2, $topSchools['top'][0]['total_siswa']);

        // Check Admin Dashboard sees both
        $dashboardResponse = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('SMP Plus Al-Hikmah Tarogong');
        $dashboardResponse->assertSee('SMP Negeri 1 Garut');

        // Check Laporan index sees both
        $laporanResponse = $this->actingAs($this->adminUser)->get(route('admin.laporan.index'));
        $laporanResponse->assertOk();
        $laporanResponse->assertSee('SMP Plus Al-Hikmah Tarogong');
        $laporanResponse->assertSee('SMP Negeri 1 Garut');
    }

    public function test_referensi_sekolah_endpoint_works_without_sql_errors(): void
    {
        MasterSekolahAsal::firstOrCreate(
            ['npsn' => '20208888'],
            [
                'nama_sekolah' => 'SMP IT Cendekia',
                'kokab' => 'Garut',
                'jenis' => 'SMP',
                'status' => 'SWASTA',
            ]
        );

        $response = $this->getJson(route('referensi.sekolah', ['q' => 'Cendekia']));

        $response->assertOk();
        $response->assertJsonFragment([
            'nama_sekolah' => 'SMP IT Cendekia',
            'kokab' => 'Garut',
        ]);
    }

    public function test_calon_siswa_search_handles_school_and_no_hp_without_sql_error(): void
    {
        $this->calonSiswa->update([
            'no_hp_siswa' => '081234567890',
            'asal_sekolah_lainnya' => 'SMP Pasundan Garut',
        ]);

        // Admin search by no_hp_siswa
        $adminHpSearch = $this->actingAs($this->adminUser)->get(route('admin.calon-siswa.index', ['q' => '081234']));
        $adminHpSearch->assertOk();
        $adminHpSearch->assertSee($this->calonSiswa->nama_lengkap);

        // Admin search by school
        $adminSchoolSearch = $this->actingAs($this->adminUser)->get(route('admin.calon-siswa.index', ['q' => 'Pasundan']));
        $adminSchoolSearch->assertOk();
        $adminSchoolSearch->assertSee($this->calonSiswa->nama_lengkap);

        // Kepala Sekolah search by no_hp_siswa
        $kepsekHpSearch = $this->actingAs($this->kepalaSekolahUser)->get(route('kepala-sekolah.calon-siswa.index', ['q' => '081234']));
        $kepsekHpSearch->assertOk();
        $kepsekHpSearch->assertSee($this->calonSiswa->nama_lengkap);

        // Kepala Sekolah search by school
        $kepsekSchoolSearch = $this->actingAs($this->kepalaSekolahUser)->get(route('kepala-sekolah.calon-siswa.index', ['q' => 'Pasundan']));
        $kepsekSchoolSearch->assertOk();
        $kepsekSchoolSearch->assertSee($this->calonSiswa->nama_lengkap);
    }

    public function test_timeline_metrics_returns_weekly_and_monthly_with_milestones(): void
    {
        $metricsService = app(\App\Services\DashboardMetricsService::class);
        $timeline = $metricsService->getTimelinePendaftar();

        // 1. Weekly dataset checks
        $this->assertArrayHasKey('mingguan', $timeline);
        $this->assertCount(49, $timeline['mingguan']['labels']);
        $this->assertCount(49, $timeline['mingguan']['ranges']);
        $this->assertCount(49, $timeline['mingguan']['pendaftar']);
        $this->assertEquals('M1 Sep', $timeline['mingguan']['labels'][0]);
        $this->assertEquals('M5 Jun', $timeline['mingguan']['labels'][48]);

        // 2. Monthly dataset checks
        $this->assertArrayHasKey('bulanan', $timeline);
        $this->assertCount(10, $timeline['bulanan']['labels']);
        $this->assertEquals('Sep 2026', $timeline['bulanan']['labels'][0]);
        $this->assertEquals('Jun 2027', $timeline['bulanan']['labels'][9]);

        // 3. Milestones checks
        $this->assertArrayHasKey('milestones', $timeline);
        $this->assertCount(4, $timeline['milestones']);
        $this->assertEquals('Pembukaan', $timeline['milestones'][0]['title']);
        $this->assertEquals('Gelombang 1', $timeline['milestones'][1]['title']);
        $this->assertEquals('Gelombang 2', $timeline['milestones'][2]['title']);
        $this->assertEquals('Gelombang 3', $timeline['milestones'][3]['title']);

        // 4. View renders timeline and milestones
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Timeline Tren Mingguan');
        $response->assertSee('Siklus 10 Bulan (49 Minggu)');
        $response->assertSee('Pembukaan');
        $response->assertSee('Gelombang 1');
        $response->assertSee('Gelombang 2');
        $response->assertSee('Gelombang 3');
        $response->assertSee('31 Des 2026');
        $response->assertSee('28 Feb 2027');
        $response->assertSee('30 Jun 2027');
    }
}
