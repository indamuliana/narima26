<?php

namespace Tests\Feature;

use App\Models\KesepahamanKelompok;
use App\Models\KesepahamanPoin;
use App\Models\KesepahamanProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class KesepahamanImportExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected KesepahamanProgram $program;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);
        $this->seed(\Database\Seeders\KesepahamanSeeder::class);

        $this->adminUser = User::where('role', 'admin')->first() 
            ?? User::factory()->create(['role' => 'admin', 'username' => 'admin_test']);

        $this->program = KesepahamanProgram::first();
    }

    public function test_guest_cannot_access_template_export_or_import(): void
    {
        $this->get(route('admin.kesepahaman.template', $this->program->id))
            ->assertRedirect(route('login'));

        $this->get(route('admin.kesepahaman.export', $this->program->id))
            ->assertRedirect(route('login'));

        $this->post(route('admin.kesepahaman.import', $this->program->id))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_download_template_xlsx_and_csv(): void
    {
        $responseXlsx = $this->actingAs($this->adminUser)
            ->get(route('admin.kesepahaman.template', [$this->program->id, 'format' => 'xlsx']));

        $responseXlsx->assertStatus(200);
        $responseXlsx->assertHeader('content-disposition');
        $this->assertStringContainsString('.xlsx', $responseXlsx->headers->get('content-disposition'));

        $responseCsv = $this->actingAs($this->adminUser)
            ->get(route('admin.kesepahaman.template', [$this->program->id, 'format' => 'csv']));

        $responseCsv->assertStatus(200);
        $this->assertStringContainsString('.csv', $responseCsv->headers->get('content-disposition'));
    }

    public function test_admin_can_export_program_xlsx_and_csv(): void
    {
        $responseXlsx = $this->actingAs($this->adminUser)
            ->get(route('admin.kesepahaman.export', [$this->program->id, 'format' => 'xlsx']));

        $responseXlsx->assertStatus(200);
        $this->assertStringContainsString('.xlsx', $responseXlsx->headers->get('content-disposition'));

        $responseCsv = $this->actingAs($this->adminUser)
            ->get(route('admin.kesepahaman.export', [$this->program->id, 'format' => 'csv']));

        $responseCsv->assertStatus(200);
        $this->assertStringContainsString('.csv', $responseCsv->headers->get('content-disposition'));
    }

    public function test_admin_can_import_with_replace_mode(): void
    {
        $tempPath = sys_get_temp_dir() . '/test_import_replace_' . uniqid() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['Kode Kelompok', 'Judul Kelompok', 'Urutan Kelompok', 'Nomor Poin', 'Uraian Poin', 'Kode Poin (ID)', 'Urutan Poin'],
            ['K1', 'Kelompok 1 Baru', 1, '1', 'Uraian Poin Pertama Baru', 'k1_p1_unique', 1],
            ['K1', 'Kelompok 1 Baru', 1, '2', 'Uraian Poin Kedua Baru', '', 2], // Auto-generated ID
            ['K2', 'Kelompok 2 Baru', 2, '1', 'Uraian Poin Kelompok Dua', '', 1],
        ]);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $file = new UploadedFile($tempPath, 'import_test.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.kesepahaman.import', $this->program->id), [
                'file' => $file,
                'mode' => 'replace',
            ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        // Check new data in database
        $this->assertDatabaseHas('kesepahaman_kelompoks', [
            'program_id' => $this->program->id,
            'kode' => 'K1',
            'judul' => 'Kelompok 1 Baru',
        ]);

        $this->assertDatabaseHas('kesepahaman_poins', [
            'kode_poin' => 'k1_p1_unique',
            'uraian' => 'Uraian Poin Pertama Baru',
        ]);

        // Auto-generated ID check
        $expectedGeneratedId = strtolower($this->program->kode . '_k1_2');
        $this->assertDatabaseHas('kesepahaman_poins', [
            'kode_poin' => $expectedGeneratedId,
            'uraian' => 'Uraian Poin Kedua Baru',
        ]);

        @unlink($tempPath);
    }

    public function test_admin_can_import_with_upsert_mode_and_csv(): void
    {
        $tempCsvPath = sys_get_temp_dir() . '/test_import_upsert_' . uniqid() . '.csv';

        $csvContent = implode("\n", [
            'Kode Kelompok,Judul Kelompok,Urutan Kelompok,Nomor Poin,Uraian Poin,Kode Poin (ID),Urutan Poin',
            'CSV1,"Kelompok CSV Pertama",1,1,"Uraian Butir CSV Pertama",csv1_p1,1',
            'CSV1,"Kelompok CSV Pertama",1,2,"Uraian Butir CSV Kedua",,2',
        ]);
        file_put_contents($tempCsvPath, $csvContent);

        $file = new UploadedFile($tempCsvPath, 'import_test.csv', 'text/csv', null, true);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.kesepahaman.import', $this->program->id), [
                'file' => $file,
                'mode' => 'upsert',
            ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $this->assertDatabaseHas('kesepahaman_kelompoks', [
            'program_id' => $this->program->id,
            'kode' => 'CSV1',
            'judul' => 'Kelompok CSV Pertama',
        ]);

        $this->assertDatabaseHas('kesepahaman_poins', [
            'kode_poin' => 'csv1_p1',
            'uraian' => 'Uraian Butir CSV Pertama',
        ]);

        @unlink($tempCsvPath);
    }

    public function test_admin_can_update_poin_with_multiline_text(): void
    {
        $poin = KesepahamanPoin::first();
        $multilineText = "Baris 1: Ketentuan umum\n- Poin a: Rincian pertama\n- Poin b: Rincian kedua";

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.kesepahaman.poin.update', $poin->id), [
                'kode_poin' => $poin->kode_poin,
                'nomor' => $poin->nomor,
                'uraian' => $multilineText,
                'urutan' => 99,
            ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $this->assertDatabaseHas('kesepahaman_poins', [
            'id' => $poin->id,
            'uraian' => $multilineText,
            'urutan' => 99,
        ]);
    }
}
