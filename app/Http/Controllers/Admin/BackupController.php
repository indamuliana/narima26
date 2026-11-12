<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class BackupController extends Controller
{
    public function __construct(
        protected BackupService $backupService
    ) {}

    /**
     * Tampilkan halaman manajemen Backup & Restore.
     */
    public function index(): View
    {
        $backups = $this->backupService->listBackups();
        $totalBytes = array_sum(array_column($backups, 'size'));
        $totalSizeHuman = $this->backupService->formatBytes($totalBytes);

        $freeBytes = @disk_free_space(storage_path()) ?: 0;
        $freeSpaceHuman = $this->backupService->formatBytes((int) $freeBytes);

        $latestLogs = Activity::where('log_name', 'backup')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.backup.index', compact(
            'backups',
            'totalSizeHuman',
            'freeSpaceHuman',
            'latestLogs'
        ));
    }

    /**
     * Buat cadangan Database MySQL/SQLite (.sql).
     */
    public function createDatabase(): RedirectResponse
    {
        try {
            $filename = $this->backupService->createDatabaseBackup();

            activity('backup')
                ->causedBy(auth()->user())
                ->log("Membuat backup database: {$filename}");

            return redirect()->route('admin.backup.index')
                ->with('success', "Backup database berhasil dibuat: {$filename}");
        } catch (Throwable $e) {
            Log::error('Backup Database Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.backup.index')
                ->with('error', 'Gagal membuat backup database: ' . $e->getMessage());
        }
    }

    /**
     * Buat cadangan Berkas/Dokumen Siswa Storage (.zip).
     */
    public function createStorage(): RedirectResponse
    {
        try {
            $filename = $this->backupService->createStorageBackup();

            activity('backup')
                ->causedBy(auth()->user())
                ->log("Membuat backup berkas storage: {$filename}");

            return redirect()->route('admin.backup.index')
                ->with('success', "Backup berkas storage berhasil dibuat: {$filename}");
        } catch (Throwable $e) {
            Log::error('Backup Storage Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.backup.index')
                ->with('error', 'Gagal membuat backup storage: ' . $e->getMessage());
        }
    }

    /**
     * Unduh file cadangan secara aman.
     */
    public function download(string $filename): BinaryFileResponse|RedirectResponse
    {
        try {
            $filepath = $this->backupService->resolveSafePath($filename);
            $cleanName = basename($filepath);

            activity('backup')
                ->causedBy(auth()->user())
                ->log("Mengunduh file backup: {$cleanName}");

            return response()->download($filepath, $cleanName);
        } catch (Throwable $e) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'Gagal mengunduh file: ' . $e->getMessage());
        }
    }

    /**
     * Hapus file cadangan secara aman.
     */
    public function destroy(string $filename): RedirectResponse
    {
        try {
            $cleanName = basename($filename);
            $deleted = $this->backupService->deleteBackup($filename);

            if ($deleted) {
                activity('backup')
                    ->causedBy(auth()->user())
                    ->log("Menghapus file backup: {$cleanName}");

                return redirect()->route('admin.backup.index')
                    ->with('success', "File backup '{$cleanName}' berhasil dihapus.");
            }

            return redirect()->route('admin.backup.index')
                ->with('error', "File backup tidak ditemukan atau sudah dihapus.");
        } catch (Throwable $e) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'Gagal menghapus file: ' . $e->getMessage());
        }
    }

    /**
     * Pulihkan (Restore) database atau storage dari file yang dipilih / diunggah.
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm_text' => ['required', 'string'],
            'filename' => ['nullable', 'string'],
            'backup_file' => ['nullable', 'file', 'max:153600'], // max 150MB
        ]);

        if (trim((string) $request->input('confirm_text')) !== 'RESTORE') {
            return redirect()->route('admin.backup.index')
                ->with('error', 'Konfirmasi keamanan tidak sesuai. Anda wajib mengetik kata "RESTORE" dengan huruf kapital untuk menjalankan pemulihan data.');
        }

        try {
            $uploadedFile = $request->file('backup_file');
            $filename = $request->input('filename');

            if ($uploadedFile) {
                $ext = strtolower($uploadedFile->getClientOriginalExtension());
                if (!in_array($ext, ['sql', 'zip'])) {
                    return redirect()->route('admin.backup.index')
                        ->with('error', 'Ekstensi file tidak valid. Hanya file .sql (database) atau .zip (storage) yang diizinkan.');
                }

                $targetFilename = 'uploaded_restore_' . date('Ymd_His') . '.' . $ext;
                $uploadedFile->move($this->backupService->getBackupDir(), $targetFilename);
                $filePath = $this->backupService->getBackupDir() . DIRECTORY_SEPARATOR . $targetFilename;
            } elseif ($filename) {
                $filePath = $this->backupService->resolveSafePath($filename);
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            } else {
                return redirect()->route('admin.backup.index')
                    ->with('error', 'Pilih file backup dari daftar riwayat atau unggah file backup Anda.');
            }

            if ($ext === 'sql') {
                // Buat safety backup database aktif terlebih dahulu sebelum ditimpa
                $safetyFile = $this->backupService->createDatabaseBackup('safety_pre_restore');

                $this->backupService->restoreDatabase($filePath);

                activity('backup')
                    ->causedBy(auth()->user())
                    ->log("Melakukan RESTORE database dari file: " . basename($filePath) . " (Safety snapshot dibuat: {$safetyFile})");

                return redirect()->route('admin.backup.index')
                    ->with('success', "Database berhasil dipulihkan dari " . basename($filePath) . ". Safety snapshot otomatis telah disimpan dengan nama: {$safetyFile}");
            } elseif ($ext === 'zip') {
                $this->backupService->restoreStorage($filePath);

                activity('backup')
                    ->causedBy(auth()->user())
                    ->log("Melakukan RESTORE berkas dokumen storage dari file: " . basename($filePath));

                return redirect()->route('admin.backup.index')
                    ->with('success', "Berkas dokumen storage berhasil dipulihkan dari file " . basename($filePath) . ".");
            }

            return redirect()->route('admin.backup.index')
                ->with('error', 'Format file cadangan tidak dikenali.');
        } catch (Throwable $e) {
            Log::error('Restore Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.backup.index')
                ->with('error', 'Proses restore gagal: ' . $e->getMessage());
        }
    }
}
