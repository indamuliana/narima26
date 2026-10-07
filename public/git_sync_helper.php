<?php
/**
 * Helper Sinkronisasi Git untuk cPanel Hosting SPMB Nampi26
 * 
 * PERINGATAN KEAMANAN:
 * File ini hanya digunakan untuk sinkronisasi awal repo cPanel ke GitHub.
 * HAPUS file ini segera setelah sinkronisasi berhasil!
 */

$secret_token = 'nampi26_sync_secret';

// Validasi Token
if (!isset($_GET['token']) || $_GET['token'] !== $secret_token) {
    http_response_code(403);
    echo '<!DOCTYPE html><html><body style="font-family:sans-serif;text-align:center;padding:50px;">';
    echo '<h2 style="color:red;">403 - Akses Ditolak</h2>';
    echo '<p>Token autentikasi tidak valid atau belum disertakan di URL.</p>';
    echo '</body></html>';
    exit;
}

$repoPath = dirname(__DIR__); // Folder root project Laravel (/public_html/nampi26)
chdir($repoPath);

// Action: Hapus File Ini Sendiri
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    if (@unlink(__FILE__)) {
        echo '<!DOCTYPE html><html><body style="font-family:sans-serif;text-align:center;padding:50px;">';
        echo '<h2 style="color:green;">Berhasil Dihapus!</h2>';
        echo '<p>File <code>git_sync_helper.php</code> telah berhasil dihapus dari server.</p>';
        echo '<p><a href="/">Kembali ke Halaman Utama</a></p>';
        echo '</body></html>';
        exit;
    } else {
        echo '<p style="color:red;">Gagal menghapus file otomatis. Silakan hapus manual melalui cPanel File Manager.</p>';
        exit;
    }
}

// Function helper untuk eksekusi perintah shell
function run_cmd($cmd) {
    $output = [];
    $return_var = 0;
    if (function_exists('exec')) {
        exec($cmd . ' 2>&1', $output, $return_var);
        return ['output' => implode("\n", $output), 'code' => $return_var];
    } elseif (function_exists('shell_exec')) {
        $out = shell_exec($cmd . ' 2>&1');
        return ['output' => $out ?? '', 'code' => 0];
    } else {
        return ['output' => 'Fungsi exec() dan shell_exec() dinonaktifkan di hosting ini.', 'code' => 1];
    }
}

$action = $_GET['action'] ?? 'status';
$result = null;

if ($action === 'sync') {
    $commands = [
        'git config --global --add safe.directory ' . escapeshellarg($repoPath),
        'git remote set-url origin https://github.com/indamuliana/narima26.git || git remote add origin https://github.com/indamuliana/narima26.git',
        'git fetch origin main',
        'git checkout -B main origin/main',
        'php artisan config:clear',
        'php artisan view:clear',
    ];

    $log = [];
    foreach ($commands as $cmd) {
        $res = run_cmd($cmd);
        $log[] = "<b>$ " . htmlspecialchars($cmd) . "</b>\n" . htmlspecialchars($res['output']);
    }
    $result = implode("\n\n", $log);
} elseif ($action === 'migrate') {
    $commands = [
        'php artisan migrate --force',
        'php artisan config:clear',
        'php artisan view:clear',
    ];
    $log = [];
    foreach ($commands as $cmd) {
        $res = run_cmd($cmd);
        $log[] = "<b>$ " . htmlspecialchars($cmd) . "</b>\n" . htmlspecialchars($res['output']);
    }
    $result = implode("\n\n", $log);
} elseif ($action === 'zip_update') {
    $zipUrl = 'https://github.com/indamuliana/narima26/archive/refs/heads/main.zip';
    $tempZip = sys_get_temp_dir() . '/narima26_update_' . time() . '.zip';

    $fp = fopen($tempZip, 'w+');
    if (!$fp) {
        $result = "<b>Error:</b> Tidak dapat membuat file sementara di temp directory server.";
    } else {
        $ch = curl_init($zipUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $success = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);

        if (!$success || $httpCode !== 200 || !file_exists($tempZip) || filesize($tempZip) < 1000) {
            $result = "<b>Error:</b> Gagal mengunduh berkas ZIP dari GitHub (HTTP Status: $httpCode). Pastikan repositori dapat diakses.";
        } else {
            if (!class_exists('ZipArchive')) {
                $result = "<b>Error:</b> Ekstensi PHP ZipArchive tidak aktif di hosting ini.";
            } else {
                $zip = new ZipArchive();
                if ($zip->open($tempZip) === TRUE) {
                    $extracted = 0;
                    $skipped = 0;
                    $prefix = $zip->getNameIndex(0); // Biasanya 'narima26-main/'

                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $entryName = $zip->getNameIndex($i);
                        $relative = substr($entryName, strlen($prefix));
                        if (empty($relative)) continue;

                        // PROTEKSI MUTLAK: JANGAN PERNAH MENIMPA .env ATAU FOLDER storage/
                        if (
                            $relative === '.env' ||
                            str_starts_with($relative, 'storage/') ||
                            str_starts_with($relative, 'public/storage/') ||
                            $relative === 'public/storage'
                        ) {
                            $skipped++;
                            continue;
                        }

                        $target = $repoPath . '/' . $relative;

                        if (str_ends_with($relative, '/')) {
                            if (!is_dir($target)) {
                                mkdir($target, 0755, true);
                            }
                        } else {
                            $parentDir = dirname($target);
                            if (!is_dir($parentDir)) {
                                mkdir($parentDir, 0755, true);
                            }
                            file_put_contents($target, $zip->getFromIndex($i));
                            $extracted++;
                        }
                    }
                    $zip->close();
                    @unlink($tempZip);

                    // Bersihkan cache view dan route/config Laravel agar perubahan langsung aktif
                    $clearedViews = 0;
                    $viewFiles = glob($repoPath . '/storage/framework/views/*.php');
                    if ($viewFiles) {
                        foreach ($viewFiles as $vf) {
                            if (@unlink($vf)) $clearedViews++;
                        }
                    }
                    $clearedBoot = 0;
                    $bootFiles = glob($repoPath . '/bootstrap/cache/*.php');
                    if ($bootFiles) {
                        foreach ($bootFiles as $bf) {
                            if (@unlink($bf)) $clearedBoot++;
                        }
                    }

                    $result = "✅ <b>UPDATE BERHASIL!</b>\n\n" .
                              "• Total $extracted file kode (PHP, Blade, Assets, .cpanel.yml) berhasil disinkronkan ke server!\n" .
                              "• $skipped file/folder penting (.env dan public/storage berkas pendaftar) 100% AMAN dan TIDAK TERSENTUH.\n" .
                              "• $clearedBoot file cache bootstrap (routes & config) & $clearedViews cache tampilan berhasil dibersihkan otomatis.\n" .
                              "• Seluruh fitur baru sekarang sudah AKTIF di website Anda!";
                } else {
                    $result = "<b>Error:</b> Gagal mengekstrak berkas ZIP.";
                }
            }
        }
    }
} elseif ($action === 'clear_cache') {
    $clearedViews = 0;
    $viewFiles = glob($repoPath . '/storage/framework/views/*.php');
    if ($viewFiles) {
        foreach ($viewFiles as $vf) {
            if (@unlink($vf)) $clearedViews++;
        }
    }
    $clearedBoot = 0;
    $bootFiles = glob($repoPath . '/bootstrap/cache/*.php');
    if ($bootFiles) {
        foreach ($bootFiles as $bf) {
            if (@unlink($bf)) $clearedBoot++;
        }
    }
    $resArtisan = run_cmd('php artisan optimize:clear');
    $result = "✅ <b>PEMBERSIHAN CACHE BERHASIL!</b>\n\n" .
              "• $clearedBoot berkas cache route & config berhasil dibersihkan dari server.\n" .
              "• $clearedViews berkas cache view Blade berhasil dibersihkan.\n\n" .
              "<b>Log Artisan (jika CLI aktif):</b>\n" . htmlspecialchars($resArtisan['output']);
} elseif ($action === 'status') {
    $resGit = run_cmd('git status');
    $resRemote = run_cmd('git remote -v');
    $result = "<b>$ git status</b>\n" . htmlspecialchars($resGit['output']) . "\n\n<b>$ git remote -v</b>\n" . htmlspecialchars($resRemote['output']);
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Git Sync Helper - cPanel Nampi26</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 30px; }
        .card { max-width: 850px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h1 { font-size: 20px; margin-top: 0; color: #38bdf8; }
        .alert { background: #334155; border-left: 4px solid #38bdf8; padding: 12px; border-radius: 4px; font-size: 14px; margin-bottom: 20px; line-height: 1.5; }
        .alert-warning { border-left-color: #f59e0b; background: #451a03; color: #fef3c7; }
        .alert-info { border-left-color: #10b981; background: #064e3b; color: #d1fae5; }
        .btn { display: inline-block; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 13px; margin-right: 8px; margin-bottom: 8px; cursor: pointer; border: none; }
        .btn-primary { background: #0284c7; color: white; }
        .btn-primary:hover { background: #0369a1; }
        .btn-success { background: #059669; color: white; }
        .btn-success:hover { background: #047857; }
        .btn-danger { background: #dc2626; color: white; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-secondary { background: #475569; color: white; }
        pre { background: #020617; padding: 15px; border-radius: 8px; overflow-x: auto; color: #4ade80; font-family: monospace; font-size: 13px; line-height: 1.4; border: 1px solid #334155; }
        .meta { font-size: 12px; color: #94a3b8; margin-top: 15px; }
    </style>
</head>
<body>
<div class="card">
    <h1>🚀 Git Sync Helper: cPanel Nampi26</h1>
    <div class="alert">
        <strong>Path Repositori:</strong> <code><?= htmlspecialchars($repoPath) ?></code><br>
        <strong>Target Remote GitHub:</strong> <code>https://github.com/indamuliana/narima26.git</code> (Branch: <code>main</code>)
    </div>

    <div class="alert alert-info">
        💡 <strong>Catatan Database:</strong> Git hanya mengupdate kode program (PHP/Blade/CSS). Database tidak akan berubah otomatis saat Git pull. Untuk update saat ini tidak ada perubahan struktur tabel. Jika di masa depan ada migrasi database baru, Anda cukup klik tombol <strong>"🗄️ Jalankan Database Migration"</strong> di bawah.
    </div>

    <div style="margin-bottom: 20px;">
        <a href="?token=<?= $secret_token ?>&action=zip_update" class="btn btn-success" style="background:#16a34a;font-size:14px;padding:12px 20px;" onclick="return confirm('Mulai update file website dari GitHub sekarang? File .env dan public/storage TIDAK akan ditimpa/dihapus.');">📥 UPDATE KODE DARI GITHUB SEKARANG (Rekomendasi)</a>
        <a href="?token=<?= $secret_token ?>&action=clear_cache" class="btn" style="background:#f59e0b;color:#0f172a;font-weight:bold;">🧹 Bersihkan Cache Laravel</a>
        <a href="?token=<?= $secret_token ?>&action=status" class="btn btn-secondary">🔍 Cek Status Git</a>
        <a href="?token=<?= $secret_token ?>&action=sync" class="btn btn-primary" onclick="return confirm('Mulai sinkronisasi repository ke branch main GitHub? File .env dan public/storage TIDAK akan terhapus.');">⚡ Jalankan Git CLI (Jika aktif)</a>
        <a href="?token=<?= $secret_token ?>&action=migrate" class="btn btn-secondary" onclick="return confirm('Jalankan php artisan migrate pada database?');">🗄️ Database Migration</a>
        <a href="?token=<?= $secret_token ?>&action=delete" class="btn btn-danger" onclick="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus file helper ini dari server sekarang?');">🗑️ Hapus Helper Ini</a>
    </div>

    <?php if ($result): ?>
        <h3>Log Eksekusi:</h3>
        <pre><?= $result ?></pre>
    <?php endif; ?>

    <div class="alert alert-warning" style="margin-top: 25px;">
        ⚠️ <strong>PENTING:</strong> Setelah sinkronisasi berhasil (status menunjukkan <code>Already on 'main'</code> atau <code>Switched to a new branch 'main'</code>), segera klik tombol <strong>"Hapus Helper Ini"</strong> di atas atau hapus file <code>public/git_sync_helper.php</code> melalui File Manager cPanel demi keamanan website Anda.
    </div>
</div>
</body>
</html>
