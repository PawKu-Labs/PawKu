<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MitraDashboardController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\OwnerMitraController;
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
        Route::get('/vaccine-calculator', [OwnerDashboardController::class, 'vaccineCalculator'])->name('vaccine.calculator');
    });

    // MENU MITRA di sisi owner (role lama 'mitra' tetap boleh agar tidak 403)
    Route::prefix('owner')->name('owner.')->middleware('role:pemilik_kucing,mitra')->group(function () {
        Route::get('/mitra', [OwnerMitraController::class, 'index'])->name('mitra');
        Route::get('/mitra/{mitra}/status', [OwnerMitraController::class, 'status'])
            ->where('mitra', '[0-9a-fA-F-]{36}')->name('mitra.status');
    });

    // DASHBOARD MITRA: akses ditentukan kepemilikan, bukan role
    Route::get('/mitra/dashboard', fn () => redirect()->route('owner.mitra'))->name('mitra.legacy');

    Route::prefix('mitra/{mitra}')->name('mitra.')
        ->where(['mitra' => '[0-9a-fA-F-]{36}'])
        ->middleware('mitra.owner')
        ->group(function () {
            Route::get('/dashboard', [MitraDashboardController::class, 'index'])->name('dashboard');
            Route::get('/bookings', [MitraDashboardController::class, 'soon'])->defaults('tab', 'Booking')->name('bookings');
            Route::get('/services', [MitraDashboardController::class, 'soon'])->defaults('tab', 'Layanan')->name('services');
            Route::get('/profile', [MitraDashboardController::class, 'soon'])->defaults('tab', 'Profil')->name('profile');
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