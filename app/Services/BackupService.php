<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class BackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        File::ensureDirectoryExists($this->backupDir);
    }

    public function getBackupDir(): string
    {
        return $this->backupDir;
    }

    /**
     * Dapatkan daftar seluruh file backup beserta metadata.
     */
    public function listBackups(): array
    {
        File::ensureDirectoryExists($this->backupDir);
        $files = File::files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            // Abaikan file tersembunyi
            if (str_starts_with($filename, '.')) {
                continue;
            }

            $extension = strtolower($file->getExtension());
            if (!in_array($extension, ['sql', 'zip'])) {
                continue;
            }

            $type = $extension === 'sql' ? 'database' : 'storage';
            $size = $file->getSize();

            $backups[] = [
                'filename' => $filename,
                'type' => $type,
                'extension' => $extension,
                'size' => $size,
                'size_human' => $this->formatBytes($size),
                'created_at' => $file->getMTime(),
                'created_at_human' => date('d M Y, H:i', $file->getMTime()),
                'path' => $file->getPathname(),
            ];
        }

        // Urutkan dari yang paling baru
        usort($backups, fn($a, $b) => $b['created_at'] <=> $a['created_at']);

        return $backups;
    }

    /**
     * Buat file SQL Dump dari Database aktif.
     */
    public function createDatabaseBackup(?string $customPrefix = null): string
    {
        File::ensureDirectoryExists($this->backupDir);
        $prefix = $customPrefix ? rtrim($customPrefix, '_') . '_' : 'spmb_backup_db_';
        $filename = $prefix . date('Ymd_His') . '.sql';
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        $handle = fopen($filepath, 'w');
        if (!$handle) {
            throw new Exception("Gagal membuat file backup database di {$filepath}");
        }

        $now = date('Y-m-d H:i:s');
        $driver = DB::getDriverName();

        fwrite($handle, "-- ==========================================================\n");
        fwrite($handle, "-- SPMB SMK WIKRAMA 1 GARUT - DATABASE BACKUP\n");
        fwrite($handle, "-- Driver: {$driver}\n");
        fwrite($handle, "-- Waktu Dibuat: {$now}\n");
        fwrite($handle, "-- ==========================================================\n\n");

        if ($driver === 'mysql') {
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
            fwrite($handle, "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n");
            fwrite($handle, "SET NAMES utf8mb4;\n\n");

            $tables = DB::select('SHOW TABLES');
            $databaseName = DB::getDatabaseName();
            $tableKey = "Tables_in_" . $databaseName;

            foreach ($tables as $t) {
                $tableName = $t->$tableKey ?? current((array)$t);
                $this->dumpMySqlTable($handle, $tableName);
            }

            fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");
        } elseif ($driver === 'sqlite') {
            fwrite($handle, "PRAGMA foreign_keys = OFF;\n\n");

            $tables = DB::select("SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            foreach ($tables as $t) {
                $this->dumpSqliteTable($handle, $t->name, $t->sql);
            }

            fwrite($handle, "\nPRAGMA foreign_keys = ON;\n");
        } else {
            throw new Exception("Driver database '{$driver}' belum didukung untuk backup.");
        }

        fclose($handle);

        return $filename;
    }

    protected function dumpMySqlTable($handle, string $tableName): void
    {
        fwrite($handle, "-- ----------------------------------------------------------\n");
        fwrite($handle, "-- Struktur Tabel `{$tableName}`\n");
        fwrite($handle, "-- ----------------------------------------------------------\n");
        fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");

        $create = DB::select("SHOW CREATE TABLE `{$tableName}`");
        $createSql = $create[0]->{'Create Table'} ?? current((array)$create[0]);
        fwrite($handle, $createSql . ";\n\n");

        $count = DB::table($tableName)->count();
        if ($count === 0) {
            return;
        }

        fwrite($handle, "-- Data untuk `{$tableName}` ({$count} baris)\n");
        foreach (DB::table($tableName)->cursor() as $row) {
            $rowArray = (array)$row;
            $columns = array_map(fn($col) => "`{$col}`", array_keys($rowArray));
            $values = array_map(function ($val) {
                if ($val === null) {
                    return 'NULL';
                }
                return DB::getPdo()->quote((string)$val);
            }, array_values($rowArray));

            $sql = "INSERT INTO `{$tableName}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
            fwrite($handle, $sql);
        }
        fwrite($handle, "\n");
    }

    protected function dumpSqliteTable($handle, string $tableName, ?string $createSql): void
    {
        fwrite($handle, "DROP TABLE IF EXISTS \"{$tableName}\";\n");
        if ($createSql) {
            fwrite($handle, $createSql . ";\n");
        }

        $count = DB::table($tableName)->count();
        if ($count === 0) {
            return;
        }

        fwrite($handle, "-- Data untuk \"{$tableName}\" ({$count} baris)\n");
        foreach (DB::table($tableName)->cursor() as $row) {
            $rowArray = (array)$row;
            $columns = array_map(fn($col) => "\"{$col}\"", array_keys($rowArray));
            $values = array_map(function ($val) {
                if ($val === null) {
                    return 'NULL';
                }
                return DB::getPdo()->quote((string)$val);
            }, array_values($rowArray));

            $sql = "INSERT INTO \"{$tableName}\" (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
            fwrite($handle, $sql);
        }
        fwrite($handle, "\n");
    }

    /**
     * Buat arsip ZIP dari direktori storage/app/public.
     */
    public function createStorageBackup(): string
    {
        if (!class_exists('ZipArchive')) {
            throw new Exception("Ekstensi PHP ZipArchive tidak aktif pada server ini.");
        }

        File::ensureDirectoryExists($this->backupDir);
        $filename = 'spmb_backup_storage_' . date('Ymd_His') . '.zip';
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        $sourcePath = storage_path('app/public');
        File::ensureDirectoryExists($sourcePath);

        $zip = new ZipArchive();
        if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Gagal membuat arsip ZIP di {$filepath}");
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourcePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($sourcePath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        return $filename;
    }

    /**
     * Restore database dari file SQL.
     */
    public function restoreDatabase(string $filePath): void
    {
        if (!File::exists($filePath)) {
            throw new Exception("File backup database tidak ditemukan.");
        }

        $sql = File::get($filePath);
        if (empty(trim($sql))) {
            throw new Exception("File backup database kosong.");
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::unprepared($sql);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            DB::unprepared($sql);
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::unprepared($sql);
        }
    }

    /**
     * Restore direktori storage dari file ZIP.
     */
    public function restoreStorage(string $filePath): void
    {
        if (!class_exists('ZipArchive')) {
            throw new Exception("Ekstensi PHP ZipArchive tidak aktif pada server ini.");
        }

        if (!File::exists($filePath)) {
            throw new Exception("File arsip ZIP tidak ditemukan.");
        }

        $targetPath = storage_path('app/public');
        File::ensureDirectoryExists($targetPath);

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new Exception("Gagal membaca arsip ZIP.");
        }

        $zip->extractTo($targetPath);
        $zip->close();
    }

    /**
     * Hapus file backup secara aman.
     */
    public function deleteBackup(string $filename): bool
    {
        $filepath = $this->resolveSafePath($filename);
        if (File::exists($filepath)) {
            return File::delete($filepath);
        }
        return false;
    }

    /**
     * Dapatkan path file secara aman dengan validasi anti-path traversal.
     */
    public function resolveSafePath(string $filename): string
    {
        $cleanName = basename($filename);
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $cleanName;

        if (!File::exists($filepath)) {
            throw new Exception("File backup '{$cleanName}' tidak ditemukan.");
        }

        return $filepath;
    }

    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
