<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BackupRestoreTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guru;
    protected User $bendahara;
    protected BackupService $backupService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::where('role', User::ROLE_ADMIN)->first();
        $this->guru = User::where('role', User::ROLE_GURU)->first();
        $this->bendahara = User::where('role', User::ROLE_BENDAHARA)->first();

        $this->backupService = app(BackupService::class);
    }

    protected function tearDown(): void
    {
        // Bersihkan file backup hasil pengujian
        $dir = $this->backupService->getBackupDir();
        if (File::exists($dir)) {
            foreach (File::files($dir) as $f) {
                File::delete($f->getPathname());
            }
        }

        parent::tearDown();
    }

    public function test_guest_and_non_admin_cannot_access_backup_routes(): void
    {
        // 1. Guest diarahkan ke login
        $this->get(route('admin.backup.index'))
            ->assertRedirect(route('login'));

        // 2. Guru ditolak (redirect ke guru dashboard)
        $this->actingAs($this->guru)
            ->get(route('admin.backup.index'))
            ->assertRedirect(route('admin.dashboard'));

        // 3. Bendahara ditolak (redirect ke bendahara dashboard)
        $this->actingAs($this->bendahara)
            ->get(route('admin.backup.index'))
            ->assertRedirect(route('bendahara.dashboard'));
    }

    public function test_admin_can_view_backup_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.backup.index'));

        $response->assertOk();
        $response->assertSee('Backup & Restore Data Sistem', false);
        $response->assertSee('Cadangan Database (.sql)');
        $response->assertSee('Cadangan Berkas Siswa (.zip)');
        $response->assertSee('Unggah & Pulihkan (Restore)', false);
    }

    public function test_admin_can_create_database_backup(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.backup.database'));

        $response->assertRedirect(route('admin.backup.index'));
        $response->assertSessionHas('success');

        $backups = $this->backupService->listBackups();
        $this->assertNotEmpty($backups);
        $this->assertEquals('database', $backups[0]['type']);
        $this->assertEquals('sql', $backups[0]['extension']);
        $this->assertFileExists($backups[0]['path']);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'backup',
            'causer_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_create_storage_backup(): void
    {
        // Buat dummy file di storage public
        $dummyFile = storage_path('app/public/test_proof.txt');
        File::ensureDirectoryExists(dirname($dummyFile));
        File::put($dummyFile, 'Test bukti pembayaran');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.backup.storage'));

        $response->assertRedirect(route('admin.backup.index'));
        $response->assertSessionHas('success');

        $backups = $this->backupService->listBackups();
        $storageBackups = array_filter($backups, fn($b) => $b['type'] === 'storage');
        $this->assertNotEmpty($storageBackups);

        if (File::exists($dummyFile)) {
            File::delete($dummyFile);
        }
    }

    public function test_admin_can_download_backup(): void
    {
        $filename = $this->backupService->createDatabaseBackup();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.backup.download', $filename));

        $response->assertOk();
        $this->assertTrue(str_contains((string) $response->headers->get('content-disposition'), $filename));
    }

    public function test_path_traversal_is_prevented_on_download(): void
    {
        // 1. Path traversal via URL dot segments returns 404
        $this->actingAs($this->admin)
            ->get('/admin/backup/download/../../.env')
            ->assertNotFound();

        // 2. Non-existent file redirects with error message
        $response = $this->actingAs($this->admin)
            ->get(route('admin.backup.download', 'file_palsu.sql'));

        $response->assertRedirect(route('admin.backup.index'));
        $response->assertSessionHas('error');
    }

    public function test_admin_can_delete_backup(): void
    {
        $filename = $this->backupService->createDatabaseBackup();
        $filepath = $this->backupService->getBackupDir() . DIRECTORY_SEPARATOR . $filename;
        $this->assertFileExists($filepath);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.backup.destroy', $filename));

        $response->assertRedirect(route('admin.backup.index'));
        $response->assertSessionHas('success');
        $this->assertFileDoesNotExist($filepath);
    }

    public function test_admin_can_restore_database_with_valid_confirmation(): void
    {
        $filename = $this->backupService->createDatabaseBackup();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.backup.restore'), [
                'filename' => $filename,
                'confirm_text' => 'RESTORE',
            ]);

        $response->assertRedirect(route('admin.backup.index'));
        $response->assertSessionHas('success');

        // Pastikan safety snapshot otomatis terbuat
        $backups = $this->backupService->listBackups();
        $safetySnapshots = array_filter($backups, fn($b) => str_starts_with($b['filename'], 'safety_pre_restore'));
        $this->assertNotEmpty($safetySnapshots);
    }

    public function test_restore_fails_without_exact_confirmation(): void
    {
        $filename = $this->backupService->createDatabaseBackup();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.backup.restore'), [
                'filename' => $filename,
                'confirm_text' => 'SALAH',
            ]);

        $response->assertRedirect(route('admin.backup.index'));
        $response->assertSessionHas('error');
    }

    public function test_admin_can_upload_and_restore_sql_file(): void
    {
        $fakeSql = "-- Fake SQL Dump\nCREATE TABLE test_table (id INT);\n";
        $file = UploadedFile::fake()->createWithContent('custom_restore.sql', $fakeSql);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.backup.restore'), [
                'backup_file' => $file,
                'confirm_text' => 'RESTORE',
            ]);

        $response->assertRedirect(route('admin.backup.index'));
        $response->assertSessionHas('success');
    }
}
