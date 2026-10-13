<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MitraDashboardController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

// Halaman Publik
Route::get('/', fn () => view('index'))->name('home');

// Rute Tamu (Hanya dapat diakses jika BELUM login)
Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('login'))->name('login');
    Route::get('/register', fn () => view('register'))->name('register');
    Route::get('/auth/callback', fn () => view('auth.callback'))->name('auth.callback');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.submit');
});

// OAuth Callback Session Handler
Route::post('/auth/session', [AuthController::class, 'session'])
    ->middleware('throttle:10,1')
    ->name('auth.session');

// Rute Terproteksi (SUDAH login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // PEMILIK KUCING (role: pemilik_kucing)
    Route::prefix('owner')->name('owner.')->middleware('role:pemilik_kucing')->group(function () {
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    });

    // MITRA PETSHOP (role: mitra)
    Route::prefix('mitra')->name('mitra.')->middleware('role:mitra')->group(function () {
        Route::get('/dashboard', [MitraDashboardController::class, 'index'])->name('dashboard');
    });

    // ADMIN (role: admin)
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });

    // SUPERADMIN (role: super_admin)
    Route::prefix('superadmin')->name('superadmin.')->middleware('role:super_admin')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/petshops', [SuperAdminController::class, 'petshops'])->name('petshops');
        Route::post('/petshops/{id}/status', [SuperAdminController::class, 'updatePetshopStatus'])->name('petshops.status');

        Route::get('/admins', [SuperAdminController::class, 'admins'])->name('admins');
        Route::post('/admins', [SuperAdminController::class, 'storeAdmin'])->name('admins.store');
        Route::post('/admins/{id}/action', [SuperAdminController::class, 'adminAction'])->name('admins.action');
    });
});
