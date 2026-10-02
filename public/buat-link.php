<?php
$basePath = explode('/public_html', __DIR__)[0];
$target = $basePath . '/public_html/nampi26/storage/app/public';
$shortcut = __DIR__ . '/storage';

// 1. Cek dan paksa hapus jika ada file / symlink rusak bernama 'storage'
if (file_exists($shortcut) || is_link($shortcut)) {
    unlink($shortcut);
}

// 2. Buat symlink baru yang benar
if (symlink($target, $shortcut)) {
    echo "SUKSES! Symlink berhasil dibuat dan gambar sudah terhubung.";
} else {
    echo "GAGAL membuat symlink. Silakan hubungi CS Hosting.";
}
?>