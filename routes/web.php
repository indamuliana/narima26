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
    return view('welcome');
})->name('home');

// Modul Registrasi Calon Siswa Baru (Fase 6)
Route::redirect('/register', '/daftar');
Route::controller(RegistrationController::class)->group(function () {
    Route::get('/daftar', 'create')->name('pendaftaran.index');
    Route::post('/daftar', 'store')->name('pendaftaran.store');
    Route::get('/daftar/sukses/{nomorPendaftaran}', 'sukses')->name('pendaftaran.sukses');
    Route::get('/daftar/cetak-akun/{nomorPendaftaran}', 'cetakAkun')->name('pendaftaran.cetak-akun');
    Route::get('/daftar/login/{nomorPendaftaran}', 'loginDirect')->name('pendaftaran.login-direct');
});

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// Authenticated General Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Admin Area
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
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
                Route::post('/tagihan/{tagihan}', 'store')->name('store');
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
            Route::get('/wawancara/{calonSiswa}', 'form')->name('wawancara.form');
            Route::post('/wawancara/{calonSiswa}', 'store')->name('wawancara.store');
            Route::get('/wawancara/{calonSiswa}/detail', 'show')->name('wawancara.show');
            Route::get('/riwayat', 'riwayat')->name('riwayat');
            Route::get('/instrumen', 'instrumen')->name('instrumen');
        });
    });

// Kepala Sekolah Area
Route::middleware(['auth', 'role:kepala_sekolah'])
    ->prefix('kepala-sekolah')
    ->name('kepala-sekolah.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('kepala-sekolah.dashboard');
        })->name('dashboard');
    });

// Calon Siswa Area
Route::middleware(['auth', 'role:calon_siswa'])
    ->prefix('calon-siswa')
    ->name('calon-siswa.')
    ->group(function () {
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
