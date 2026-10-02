<?php
// Mendapatkan path dasar cPanel secara otomatis (misal: /home/username)
$basePath = explode('/public_html', __DIR__)[0];

// Target folder asli gambar berada (karena Anda menaruhnya di /public_html/nampi26)
$target = $basePath . '/public_html/nampi26/storage/app/public';

// Lokasi shortcut (symlink) yang akan dibuat
$shortcut = __DIR__ . '/storage';

if (!is_dir($target)) {
    die("ERROR: Folder sumber ($target) tidak ditemukan! Pastikan folder nampi26 benar-benar ada di public_html.");
}

if (file_exists($shortcut)) {
    echo "ERROR: File/Folder 'storage' sudah ada di sini. Silakan hapus dulu via File Manager!";
} else if (symlink($target, $shortcut)) {
    echo "SUKSES! Symlink berhasil dibuat. Gambar sudah bisa diakses.";
} else {
    echo "GAGAL: Hosting Anda memblokir fungsi symlink.";
}
?>