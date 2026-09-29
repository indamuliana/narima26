<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\DataKesehatan;
use App\Models\DataOrangtua;
use App\Models\KesepahamanEula;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\PembayaranSeleksi;
use App\Models\Tagihan;
use App\Models\User;
use App\Models\WawancaraSiswa;
use App\Models\WawancaraOrangTua;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppSidebarAndCsvExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guru;
    protected User $kepalaSekolah;
    protected User $bendahara;
    protected CalonSiswa $candidate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->guru = User::factory()->create([
            'role' => User::ROLE_GURU,
            'is_active' => true,
        ]);

        $this->kepalaSekolah = User::factory()->create([
            'role' => User::ROLE_KEPALA_SEKOLAH,
            'is_active' => true,
        ]);

        $this->bendahara = User::factory()->create([
            'role' => User::ROLE_BENDAHARA,
            'is_active' => true,
        ]);

        $program = MasterProgram::first() ?? MasterProgram::create([
            'kode' => 'REG',
            'nama' => 'Reguler',
            'aktif' => true,
        ]);

        $jurusan = MasterJurusan::first() ?? MasterJurusan::create([
            'kode' => 'PPLG',
            'nama' => 'Pengembangan Perangkat Lunak dan Gim',
            'aktif' => true,
        ]);

        $gelombang = MasterGelombang::first() ?? MasterGelombang::create([
            'kode' => 'GEL1',
            'nama' => 'Gelombang 1',
            'aktif' => true,
            'periode_mulai' => now(),
            'periode_selesai' => now()->addMonths(3),
        ]);

        $studentUser = User::factory()->create([
            'role' => User::ROLE_CALON_SISWA,
            'is_active' => true,
        ]);

        $this->candidate = CalonSiswa::create([
            'user_id' => $studentUser->id,
            'nomor_pendaftaran' => '26REG0099',
            'nisn' => '0098765432',
            'nama_lengkap' => 'Muhammad Bintang Pratama',
            'jenis_kelamin' => 'L',
            'no_hp_siswa' => '081234567890',
            'no_hp_ayah' => '081298765432',
            'no_hp_ibu' => '081311223344',
            'program_id' => $program->id,
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'status_spmb' => \App\Enums\SpmbStatus::DATA_LENGKAP->value,
            'anak_ke' => 2,
            'jumlah_saudara' => 3,
            'tahun_lulus' => 2026,
        ]);

        DataOrangtua::create([
            'calon_siswa_id' => $this->candidate->id,
            'status_ayah' => 'MASIH_HIDUP',
            'nama_ayah' => 'Rahmat Pratama',
            'no_hp_ayah' => '081298765432',
            'status_ibu' => 'MASIH_HIDUP',
            'nama_ibu' => 'Siti Aminah',
            'no_hp_ibu' => '081311223344',
        ]);

        DataKesehatan::create([
            'calon_siswa_id' => $this->candidate->id,
            'tinggi_badan' => 170,
            'berat_badan' => 60,
            'golongan_darah' => 'B+',
            'buta_warna' => 'Tidak buta warna',
            'penyakit_pernah_diderita' => 'Tipes',
            'penyakit_sedang_diderita' => 'tidak ada',
            'kesehatan_mata' => 'normal',
            'jenis_alergi' => 'Debu',
        ]);
    }

    public function test_sidebar_includes_collapsible_toggle_elements_and_shortcut(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.calon-siswa.index'));

        $response->assertStatus(200);
        $response->assertSee('toggleSidebar()', false);
        $response->assertSee('sidebar_collapsed', false);
        $response->assertSee('Ctrl+B', false);
    }

    public function test_calon_siswa_table_renders_whatsapp_dropdown_with_contact_links(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.calon-siswa.index'));

        $response->assertStatus(200);
        $response->assertSee('Muhammad Bintang Pratama');
        $response->assertSee('Hubungi Siswa');
        $response->assertSee('Hubungi Ayah');
        $response->assertSee('Hubungi Ibu');
        $response->assertSee('wa.me/6281234567890', false);
        $response->assertSee('wa.me/6281298765432', false);
        $response->assertSee('wa.me/6281311223344', false);
    }

    public function test_kepala_sekolah_calon_siswa_table_renders_whatsapp_dropdown(): void
    {
        $response = $this->actingAs($this->kepalaSekolah)->get(route('kepala-sekolah.calon-siswa.index'));

        $response->assertStatus(200);
        $response->assertSee('Muhammad Bintang Pratama');
        $response->assertSee('Hubungi Siswa');
        $response->assertSee('wa.me/6281234567890', false);
    }

    public function test_bendahara_pembayaran_seleksi_table_renders_whatsapp_dropdown(): void
    {
        PembayaranSeleksi::create([
            'calon_siswa_id' => $this->candidate->id,
            'nominal_tagihan' => 200000,
            'nominal_dibayar' => 200000,
            'status' => 'PENDING',
        ]);

        $response = $this->actingAs($this->bendahara)->get(route('bendahara.pembayaran-seleksi.index'));

        $response->assertStatus(200);
        $response->assertSee('Muhammad Bintang Pratama');
        $response->assertSee('Hubungi Siswa');
        $response->assertSee('wa.me/6281234567890', false);
    }

    public function test_export_csv_simple_mode(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.calon-siswa.export.csv', ['mode' => 'simple']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Nomor Pendaftaran', $content);
        $this->assertStringContainsString('Nama Lengkap', $content);
        $this->assertStringContainsString('Muhammad Bintang Pratama', $content);
        $this->assertStringContainsString('26REG0099', $content);
    }

    public function test_export_csv_master_full_mode(): void
    {
        // Add EULA, Wawancara, Tagihan
        KesepahamanEula::create([
            'calon_siswa_id' => $this->candidate->id,
            'versi_dokumen' => 'v1.0',
            'setuju' => true,
            'agreed_at' => now(),
            'poin_disetujui' => ['poin_1', 'poin_2', 'poin_3'],
        ]);

        WawancaraSiswa::create([
            'calon_siswa_id' => $this->candidate->id,
            'pewawancara_id' => $this->admin->id,
            'nama_petugas' => 'Petugas Pewawancara',
            'tanggal_wawancara' => now(),
            'status' => 'SELESAI',
            'rekomendasi' => 'TERIMA',
            'baca_quran' => 'LANCAR',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.calon-siswa.export.csv', ['mode' => 'full']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        // Check key comprehensive columns
        $this->assertStringContainsString('Status Pakta Integritas (EULA)', $content);
        $this->assertStringContainsString('Wawancara Siswa', $content);
        $this->assertStringContainsString('Nama Ayah', $content);
        $this->assertStringContainsString('Nama Ibu', $content);
        $this->assertStringContainsString('Status Tagihan Daftar Ulang', $content);
        $this->assertStringContainsString('Tinggi Badan (cm)', $content);
        $this->assertStringContainsString('Golongan Darah', $content);
        $this->assertStringContainsString('Tahun Lulus SMP', $content);

        // Check data values
        $this->assertStringContainsString('Muhammad Bintang Pratama', $content);
        $this->assertStringContainsString('Pengembangan Perangkat Lunak dan Gim', $content);
        $this->assertStringContainsString('Gelombang 1', $content);
        $this->assertStringContainsString('Rahmat Pratama', $content);
        $this->assertStringContainsString('Siti Aminah', $content);
        $this->assertStringContainsString('081234567890', $content);
        $this->assertStringContainsString('SETUJU', $content);
        $this->assertStringContainsString('TERIMA', $content);
        $this->assertStringContainsString('Tipes', $content);
        $this->assertStringContainsString('Debu', $content);
    }

    public function test_guru_can_access_and_download_csv_export(): void
    {
        $response = $this->actingAs($this->guru)->get(route('admin.calon-siswa.export.csv', ['mode' => 'full']));

        $response->assertStatus(200);
        $this->assertStringContainsString('Muhammad Bintang Pratama', $response->streamedContent());
    }

    public function test_export_xls_simple_mode(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.calon-siswa.export.xls', ['mode' => 'simple']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('<html xmlns:o="urn:schemas-microsoft-com:office:office"', $content);
        $this->assertStringContainsString('mso-number-format', $content);
        $this->assertStringContainsString('Nomor Pendaftaran', $content);
        $this->assertStringContainsString('Muhammad Bintang Pratama', $content);
        $this->assertStringContainsString('26REG0099', $content);
    }

    public function test_export_xls_master_full_mode(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.calon-siswa.export.xls', ['mode' => 'full']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('<html xmlns:o="urn:schemas-microsoft-com:office:office"', $content);
        $this->assertStringContainsString('Tinggi Badan (cm)', $content);
        $this->assertStringContainsString('Golongan Darah', $content);
        $this->assertStringContainsString('Tahun Lulus SMP', $content);
        $this->assertStringContainsString('Muhammad Bintang Pratama', $content);
        $this->assertStringContainsString('Pengembangan Perangkat Lunak dan Gim', $content);
        $this->assertStringContainsString('Gelombang 1', $content);
        $this->assertStringContainsString('Tipes', $content);
        $this->assertStringContainsString('Debu', $content);
    }

    public function test_guru_can_access_and_download_xls_export(): void
    {
        $response = $this->actingAs($this->guru)->get(route('admin.calon-siswa.export.xls', ['mode' => 'full']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
        $this->assertStringContainsString('Muhammad Bintang Pratama', $response->streamedContent());
    }
}
