<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — SPMB Nampi (SMK Wikrama 1 Garut)
|--------------------------------------------------------------------------
*/

// Public / Landing Page
Route::get('/', function () {
    $gelombangAktif = null;
    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('master_gelombang')) {
            $gelombangAktif = \App\Models\MasterGelombang::aktif()->first() ?? \App\Models\MasterGelombang::first();
        }
    } catch (\Throwable $e) {
        $gelombangAktif = null;
    }

    return view('welcome', compact('gelombangAktif'));
})->name('home');

// Modul Registrasi Calon Siswa Baru (Fase 6)
Route::redirect('/register', '/daftar');
Route::controller(RegistrationController::class)->group(function () {
    Route::get('/daftar', 'create')->name('pendaftaran.index');
    Route::post('/daftar', 'store')->name('pendaftaran.store');
    Route::get('/daftar/sukses/{nomorPendaftaran}', 'sukses')->name('pendaftaran.sukses');
    Route::get('/daftar/cetak-akun/{nomorPendaftaran}', 'cetakAkun')->name('pendaftaran.cetak-akun');
    Route::get('/daftar/login/{nomorPendaftaran}', 'loginDirect')->name('pendaftaran.login-direct');
    
    // Endpoint AJAX Pencarian Sekolah
    Route::get('/referensi/sekolah', 'searchSekolah')->name('referensi.sekolah');
});

// Verifikasi Keabsahan Dokumen Publik TTE (QR Code)
Route::get('/verifikasi-dokumen/{kode}', [\App\Http\Controllers\VerifikasiDokumenController::class, 'show'])
    ->name('dokumen.verifikasi');

// Authentication Routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::match(['GET', 'POST'], '/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::post('/impersonate/leave', [\App\Http\Controllers\Auth\ImpersonateController::class, 'leave'])->middleware('auth')->name('impersonate.leave');

// Universal Dashboard Route (redirects authenticated users to their specific role dashboard)
Route::middleware('auth')->get('/dashboard', function () {
    return redirect()->route(auth()->user()->getDashboardRoute());
})->name('dashboard');

// Admin & Guru Shared Area (Executive & Read-Only Directory Access)
Route::middleware(['auth', 'role:admin,guru'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Manajemen Calon Siswa (DataTables & Export)
        Route::controller(\App\Http\Controllers\Admin\CalonSiswaController::class)
            ->prefix('calon-siswa')
            ->name('calon-siswa.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/export/csv', 'exportCsv')->name('export.csv');
                Route::get('/export/xls', 'exportXls')->name('export.xls');
                Route::get('/export/pdf', 'exportPdf')->name('export.pdf');
                Route::get('/{calonSiswa}/cetak-pdf', 'cetakPdf')->name('cetak-pdf');
                Route::get('/{calonSiswa}', 'show')->name('show');
            });

        // Laporan & Rekapitulasi Eksekutif
        Route::controller(\App\Http\Controllers\Admin\LaporanController::class)
            ->prefix('laporan')
            ->name('laporan.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/export/pdf', 'exportRekapPdf')->name('rekap.export.pdf');
            });
    });

// Admin Exclusive Area (Fase 13 - User Management & Master Configurations)
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Manajemen Pengguna (User Management)
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);
        Route::patch('/users/{user}/toggle', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle');

        // Edit Data Calon Siswa (Admin Direct Edit)
        Route::controller(\App\Http\Controllers\Admin\CalonSiswaDataEditController::class)
            ->prefix('calon-siswa/{calonSiswa}')
            ->name('calon-siswa.')
            ->group(function () {
                Route::get('/edit-data', 'edit')->name('edit-data');
                Route::put('/update-biodata', 'updateBiodata')->name('update-biodata');
                Route::put('/update-orang-tua', 'updateOrangTua')->name('update-orang-tua');
                Route::put('/update-akademik', 'updateAkademik')->name('update-akademik');
                Route::put('/update-kesehatan', 'updateKesehatan')->name('update-kesehatan');
                Route::put('/update-seragam', 'updateSeragam')->name('update-seragam');
            });

        // Tagging Calon Siswa (Admin Only)
        Route::put('/calon-siswa/{calonSiswa}/tags', [\App\Http\Controllers\Admin\CalonSiswaController::class, 'updateTags'])
            ->name('calon-siswa.update-tags');

        // Impersonasi Calon Siswa (Masuk Sebagai Calon Siswa)
        Route::post('/calon-siswa/{calonSiswa}/impersonate', [\App\Http\Controllers\Auth\ImpersonateController::class, 'start'])
            ->name('calon-siswa.impersonate');

        // 1. Manajemen Jurusan (Kompetensi Keahlian)
        Route::controller(\App\Http\Controllers\Admin\JurusanController::class)
            ->prefix('jurusan')
            ->name('jurusan.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/{jurusan}', 'update')->name('update');
                Route::patch('/{jurusan}/toggle', 'toggle')->name('toggle');
            });

        // 2. Manajemen Master Keuangan (Tarif Biaya & Diskon)
        Route::controller(\App\Http\Controllers\Admin\MasterKeuanganController::class)
            ->prefix('keuangan')
            ->name('keuangan.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/{biaya}', 'update')->name('update');
                Route::patch('/{biaya}/toggle', 'toggle')->name('toggle');
            });

        // 3. Manajemen Master Pembayaran (Seleksi & Daftar Ulang)
        Route::controller(\App\Http\Controllers\Admin\PembayaranController::class)
            ->prefix('pembayaran')
            ->name('pembayaran.')
            ->group(function () {
                Route::get('/seleksi', 'seleksi')->name('seleksi');
                Route::post('/seleksi/{pembayaranSeleksi}/verify', 'verifySeleksi')->name('seleksi.verify');
                Route::post('/seleksi/{pembayaranSeleksi}/reject', 'rejectSeleksi')->name('seleksi.reject');
                Route::get('/daftar-ulang', 'daftarUlang')->name('daftar-ulang');
            });

        // 4. Manajemen Alokasi Pewawancara
        Route::controller(\App\Http\Controllers\Admin\AlokasiPewawancaraController::class)
            ->prefix('alokasi-pewawancara')
            ->name('alokasi-pewawancara.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/batch', 'alokasiBatch')->name('batch');
                Route::post('/{calonSiswa}', 'alokasikan')->name('single');
            });

        // Log Audit Trail
        Route::get('/audit-trail', [\App\Http\Controllers\Admin\AuditTrailController::class, 'index'])->name('audit-trail.index');
    });

// Bendahara Area
// Bendahara Area (Fase 7 & Fase 11)
Route::middleware(['auth', 'role:bendahara,admin'])
    ->prefix('bendahara')
    ->name('bendahara.')
    ->group(function () {
        Route::get('/dashboard', function () {
            $stats = [
                'seleksi_masuk' => (float) \App\Models\PembayaranSeleksi::where('status', 'DIVERIFIKASI')->sum('nominal_dibayar'),
                'seleksi_pending' => \App\Models\PembayaranSeleksi::where('status', 'PENDING')->count(),
                'daftar_ulang_masuk' => (float) \App\Models\PembayaranDaftarUlang::where('status', 'DIVERIFIKASI')->sum('nominal_dibayar'),
                'daftar_ulang_pending' => \App\Models\PembayaranDaftarUlang::where('status', 'PENDING')->count(),
                'total_tagihan' => \App\Models\Tagihan::count(),
            ];
            $recentSeleksi = \App\Models\PembayaranSeleksi::with('calonSiswa')->latest('id')->take(5)->get();
            $recentDaftarUlang = \App\Models\PembayaranDaftarUlang::with(['calonSiswa', 'tagihan'])->latest('id')->take(5)->get();
            return view('bendahara.dashboard', compact('stats', 'recentSeleksi', 'recentDaftarUlang'));
        })->name('dashboard');

        // Pembayaran Seleksi (Fase 7)
        Route::controller(\App\Http\Controllers\Bendahara\PembayaranSeleksiController::class)
            ->prefix('pembayaran-seleksi')
            ->name('pembayaran-seleksi.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{pembayaranSeleksi}', 'show')->name('show');
                Route::post('/{pembayaranSeleksi}/verify', 'verify')->name('verify');
                Route::post('/{pembayaranSeleksi}/reject', 'reject')->name('reject');
                Route::get('/{pembayaranSeleksi}/cetak', 'cetakKwitansi')->name('cetak');
            });

        // Tagihan Daftar Ulang (Fase 11)
        Route::controller(\App\Http\Controllers\Bendahara\TagihanController::class)
            ->prefix('tagihan')
            ->name('tagihan.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{tagihan}', 'show')->name('show');
                Route::get('/{tagihan}/cetak', 'cetakPdf')->name('cetak');
            });

        // Kelola Diskon (Fase 11)
        Route::controller(\App\Http\Controllers\Bendahara\DiskonController::class)
            ->prefix('diskon')
            ->name('diskon.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/search-siswa', 'searchSiswa')->name('search-siswa');
                Route::post('/tagihan/{tagihan}', 'store')->name('store');
                Route::post('/', 'storeFromIndex')->name('store-from-index');
                Route::delete('/{diskon}', 'destroy')->name('destroy');
            });

        // Pembayaran Daftar Ulang (Fase 11)
        Route::controller(\App\Http\Controllers\Bendahara\PembayaranDaftarUlangController::class)
            ->prefix('pembayaran-daftar-ulang')
            ->name('pembayaran-daftar-ulang.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{pembayaranDaftarUlang}', 'show')->name('show');
                Route::post('/{pembayaranDaftarUlang}/verify', 'verify')->name('verify');
                Route::post('/{pembayaranDaftarUlang}/reject', 'reject')->name('reject');
                Route::get('/{pembayaranDaftarUlang}/cetak', 'cetakKwitansi')->name('cetak-kwitansi');
                Route::get('/{pembayaranDaftarUlang}/kwitansi', 'cetakKwitansi')->name('cetak');
            });

        // Master Biaya (Fase 11)
        Route::controller(\App\Http\Controllers\Bendahara\MasterBiayaController::class)
            ->prefix('master-biaya')
            ->name('master-biaya.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/{masterBiaya}', 'update')->name('update');
                Route::patch('/{masterBiaya}/toggle', 'toggle')->name('toggle');
            });

        // Master Diskon
        Route::controller(\App\Http\Controllers\Bendahara\MasterDiskonController::class)
            ->prefix('master-diskon')
            ->name('master-diskon.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/{masterDiskon}', 'update')->name('update');
                Route::delete('/{masterDiskon}', 'destroy')->name('destroy');
            });
    });

// Pewawancara Area (Fase 10)
Route::middleware(['auth', 'role:pewawancara,admin'])
    ->prefix('pewawancara')
    ->name('pewawancara.')
    ->group(function () {
        Route::controller(\App\Http\Controllers\Pewawancara\WawancaraController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/antrian', 'index')->name('antrian');
            Route::get('/wawancara', 'index')->name('wawancara.index');
            Route::get('/wawancara/{calonSiswa}', 'hub')->name('wawancara.hub');
            Route::get('/wawancara/{calonSiswa}/siswa', 'formSiswa')->name('wawancara.form-siswa');
            Route::post('/wawancara/{calonSiswa}/siswa', 'saveSiswa')->name('wawancara.save-siswa');
            Route::get('/wawancara/{calonSiswa}/orang-tua', 'formOrangTua')->name('wawancara.form-orang-tua');
            Route::post('/wawancara/{calonSiswa}/orang-tua', 'saveOrangTua')->name('wawancara.save-orang-tua');
            Route::get('/wawancara/{calonSiswa}/detail', 'show')->name('wawancara.show');
            Route::get('/riwayat', 'riwayat')->name('riwayat');
            Route::get('/instrumen', 'instrumen')->name('instrumen');
        });
    });

// Kepala Sekolah & Admin Sidang Area (Fase 12)
Route::middleware(['auth', 'role:kepala_sekolah,admin'])
    ->prefix('kepala-sekolah')
    ->name('kepala-sekolah.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\KepalaSekolah\DashboardController::class, 'index'])->name('dashboard');

        // Sidang Pleno Kelulusan (Fase 12)
        Route::controller(\App\Http\Controllers\KepalaSekolah\SidangKelulusanController::class)
            ->prefix('sidang-kelulusan')
            ->name('sidang-kelulusan.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/batch', 'batch')->name('batch');
                Route::get('/{calonSiswa}', 'show')->name('show');
                Route::post('/{calonSiswa}/putuskan', 'putuskan')->name('putuskan');
                Route::get('/{calonSiswa}/cetak-sk', 'cetakSk')->name('cetak-sk');
            });

        // Pengunduran Diri & Restorasi (Fase 12)
        Route::controller(\App\Http\Controllers\KepalaSekolah\PengunduranDiriController::class)
            ->prefix('pengunduran-diri')
            ->name('pengunduran-diri.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/{calonSiswa}', 'store')->name('store');
                Route::post('/{calonSiswa}/restore', 'restore')->name('restore');
            });

        // Kelola Diskon & Keringanan (Otoritas Kepala Sekolah)
        Route::controller(\App\Http\Controllers\KepalaSekolah\DiskonController::class)
            ->prefix('diskon')
            ->name('diskon.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/{diskon}', 'destroy')->name('destroy');
                Route::get('/search-siswa', 'searchSiswa')->name('search-siswa');
                Route::post('/master', 'storeMaster')->name('master.store');
                Route::put('/master/{masterDiskon}', 'updateMaster')->name('master.update');
                Route::delete('/master/{masterDiskon}', 'destroyMaster')->name('master.destroy');
            });

        // Direktori Data Calon Siswa (Eksekutif)
        Route::controller(\App\Http\Controllers\KepalaSekolah\CalonSiswaController::class)
            ->prefix('calon-siswa')
            ->name('calon-siswa.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{calonSiswa}', 'show')->name('show');
                Route::get('/{calonSiswa}/cetak-pdf', 'cetakPdf')->name('cetak-pdf');
            });
    });

// Calon Siswa Area
Route::middleware(['auth', 'role:calon_siswa'])
    ->prefix('calon-siswa')
    ->name('calon-siswa.')
    ->group(function () {
        Route::get('/', function () {
            return redirect()->route('calon-siswa.dashboard');
        })->name('index');

        Route::get('/dashboard', function () {
            $calonSiswa = auth()->user()->calonSiswa;
            return view('calon-siswa.dashboard', compact('calonSiswa'));
        })->name('dashboard');

        // Pembayaran Seleksi (Fase 7)
        Route::controller(\App\Http\Controllers\CalonSiswa\PembayaranSeleksiController::class)
            ->prefix('pembayaran-seleksi')
            ->name('pembayaran-seleksi.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/cetak', 'cetakKwitansi')->name('cetak');
            });

        // Modul Lengkapi Data & Upload Persyaratan (Fase 8)
        Route::controller(\App\Http\Controllers\CalonSiswa\LengkapiDataController::class)
            ->prefix('lengkapi-data')
            ->name('lengkapi-data.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/biodata', 'updateBiodata')->name('biodata');
                Route::post('/orang-tua', 'updateOrangTua')->name('orang-tua');
                Route::post('/akademik', 'updateAkademik')->name('akademik');
                Route::post('/seragam', 'updateSeragam')->name('seragam');
                Route::post('/dokumen', 'uploadDokumen')->name('dokumen');
                Route::post('/kesehatan', 'updateKesehatan')->name('kesehatan');
                Route::post('/finalize', 'finalize')->name('finalize');
            });

        // Modul Kesepahaman / EULA (Fase 9)
        Route::controller(\App\Http\Controllers\CalonSiswa\KesepahamanController::class)
            ->prefix('kesepahaman')
            ->name('kesepahaman.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/cetak', 'cetakPdf')->name('cetak');
            });

        // Hub Dokumen & Cetak PDF (Fase 9)
        Route::controller(\App\Http\Controllers\CalonSiswa\DokumenPdfController::class)
            ->prefix('dokumen')
            ->name('dokumen.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/kartu', 'cetakKartu')->name('kartu');
                Route::get('/kesepahaman', 'cetakKesepahaman')->name('kesepahaman');
                Route::get('/akun', 'cetakAkun')->name('akun');
                Route::get('/kelulusan', 'cetakKelulusan')->name('kelulusan');
            });

        // Daftar Ulang & Tagihan (Fase 11)
        Route::controller(\App\Http\Controllers\CalonSiswa\DaftarUlangController::class)
            ->prefix('daftar-ulang')
            ->name('daftar-ulang.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/bayar', 'bayar')->name('bayar');
                Route::post('/bayar', 'storeBayar')->name('store-bayar');
                Route::get('/cetak-tagihan', 'cetakTagihan')->name('cetak-tagihan');
                Route::get('/cetak-kwitansi/{pembayaranDaftarUlang}', 'cetakKwitansi')->name('cetak-kwitansi');
                Route::post('/aktivasi-seragam', 'aktivasiSeragam')->name('aktivasi-seragam');
            });
    });

// Cascading Wilayah API (Fase 8)
Route::prefix('api/internal/wilayah')->name('api.wilayah.')->group(function () {
    Route::get('/provinsi', [\App\Http\Controllers\Api\WilayahController::class, 'provinsi'])->name('provinsi');
    Route::get('/kabupaten/{id}', [\App\Http\Controllers\Api\WilayahController::class, 'kabupaten'])->name('kabupaten');
    Route::get('/kecamatan/{id}', [\App\Http\Controllers\Api\WilayahController::class, 'kecamatan'])->name('kecamatan');
    Route::get('/desa/{id}', [\App\Http\Controllers\Api\WilayahController::class, 'desa'])->name('desa');
});
Route::prefix('api/wilayah')->group(function () {
    Route::get('/provinsi', [\App\Http\Controllers\Api\WilayahController::class, 'provinsi']);
    Route::get('/kabupaten/{id}', [\App\Http\Controllers\Api\WilayahController::class, 'kabupaten']);
    Route::get('/kecamatan/{id}', [\App\Http\Controllers\Api\WilayahController::class, 'kecamatan']);
    Route::get('/desa/{id}', [\App\Http\Controllers\Api\WilayahController::class, 'desa']);
});
