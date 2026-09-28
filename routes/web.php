<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
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
Route::middleware(['auth', 'role:bendahara'])
    ->prefix('bendahara')
    ->name('bendahara.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('bendahara.dashboard');
        })->name('dashboard');
    });

// Pewawancara Area
Route::middleware(['auth', 'role:pewawancara'])
    ->prefix('pewawancara')
    ->name('pewawancara.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('pewawancara.dashboard');
        })->name('dashboard');
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
            return view('calon-siswa.dashboard');
        })->name('dashboard');
    });
