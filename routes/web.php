<?php

use App\Http\Controllers\AdminBackupController;
use App\Http\Controllers\AdminArticleController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\MitraDashboardController;
use App\Http\Controllers\MitraProfileController;
use App\Http\Controllers\OwnerBookingController;
use App\Http\Controllers\OwnerCatController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\OwnerMitraController;
use App\Http\Controllers\PetshopController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\KalkulatorBbController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

if (!defined('UUID_PATTERN')) {
    define('UUID_PATTERN', '[0-9a-fA-F-]{36}');
}

// Halaman Publik
Route::get('/', fn() => view('index'))->name('home');

    // KALKULATOR BERAT BADAN (cek status obesitas)
    Route::get('/kalkulator-bb', [KalkulatorBbController::class, 'index'])
        ->name('kalkulator_bb.index');
    Route::post('/kalkulator-bb', [KalkulatorBbController::class, 'hitung'])
        ->middleware('throttle:30,1')
        ->name('kalkulator_bb.hitung');

// Cari Petshop (publik). Hanya Mitra berstatus approved; jam operasional & ulasan khusus user login (dijaga di controller).
Route::get('/petshops', [PetshopController::class, 'index'])->name('petshops.index');
Route::get('/petshops/{mitra}', [PetshopController::class, 'show'])
    ->where('mitra', UUID_PATTERN)
    ->name('petshops.show');

// Rute Tamu (Hanya dapat diakses jika BELUM login)
Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => view('login'))->name('login');
    Route::get('/register', fn() => view('register'))->name('register');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.submit');
});

// OAuth Callback — tanpa middleware guest agar dapat diakses kapan saja
// (token dikirim lewat URL fragment #access_token, bukan via session)
Route::get('/auth/callback', fn() => view('auth.callback'))->name('auth.callback');

// OAuth Callback Session Handler
Route::post('/auth/session', [AuthController::class, 'session'])
    ->middleware('throttle:10,1')
    ->name('auth.session');

// Rute Terproteksi (SUDAH login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // PEMILIK KUCING & MITRA (role: pemilik_kucing, mitra)
    Route::prefix('owner')->name('owner.')->middleware('role:pemilik_kucing,mitra')->group(function () {
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/vaccine-calculator', [OwnerDashboardController::class, 'vaccineCalculator'])->name('vaccine.calculator');

        // RIWAYAT BOOKING (owner_id = user session, diverifikasi di controller)
        Route::get('/bookings', [OwnerBookingController::class, 'index'])->name('bookings.index');

        // PROFIL KUCING (kepemilikan diverifikasi di controller: owner_id = user session)
        Route::prefix('cats')->name('cats.')->group(function () {
            Route::get('/', [OwnerCatController::class, 'index'])->name('index');
            Route::get('/create', [OwnerCatController::class, 'create'])->name('create');
            Route::post('/', [OwnerCatController::class, 'store'])->middleware('throttle:20,1')->name('store');
            Route::get('/{cat}/edit', [OwnerCatController::class, 'edit'])
                ->where('cat', UUID_PATTERN)->name('edit');
            Route::put('/{cat}', [OwnerCatController::class, 'update'])
                ->where('cat', UUID_PATTERN)->middleware('throttle:20,1')->name('update');
            Route::delete('/{cat}', [OwnerCatController::class, 'destroy'])
                ->where('cat', UUID_PATTERN)->middleware('throttle:20,1')->name('destroy');
        });
    });

    // MENU MITRA di sisi owner (daftar, status, dashboard, aksi booking)
    // Kepemilikan mitra diverifikasi langsung di controller (owner_id = user session),
    // sehingga TIDAK memerlukan middleware 'mitra.owner'.
    Route::prefix('owner')->name('owner.')->middleware('role:pemilik_kucing,mitra')->group(function () {
        Route::get('/mitra', [OwnerMitraController::class, 'index'])->name('mitra');

        // Dashboard mitra default (tanpa parameter UUID di URL)
        Route::get('/mitra/dashboard', [MitraDashboardController::class, 'index'])->name('mitra.dashboard');

        Route::get('/mitra/{mitra}/status', [OwnerMitraController::class, 'status'])
            ->where('mitra', UUID_PATTERN)
            ->name('mitra.status');

        // -> route('owner.mitra.dashboard.unit', ['mitra' => $id, 'tab' => 'booking'])
        Route::get('/mitra/{mitra}/dashboard', [MitraDashboardController::class, 'index'])
            ->where('mitra', UUID_PATTERN)
            ->name('mitra.dashboard.unit');

        // -> route('owner.mitra.booking.status', ['mitra' => $id, 'booking' => $bookingId])
        Route::post('/mitra/{mitra}/bookings/{booking}/status', [MitraDashboardController::class, 'updateBookingStatus'])
            ->where('mitra', UUID_PATTERN)
            ->name('mitra.booking.status');

        // Kelola layanan mitra
        Route::post('/mitra/{mitra}/services', [MitraDashboardController::class, 'storeService'])
            ->where('mitra', UUID_PATTERN)
            ->name('mitra.service.store');
        Route::put('/mitra/{mitra}/services/{service}', [MitraDashboardController::class, 'updateService'])
            ->where(['mitra' => UUID_PATTERN, 'service' => UUID_PATTERN])
            ->name('mitra.service.update');
        Route::delete('/mitra/{mitra}/services/{service}', [MitraDashboardController::class, 'destroyService'])
            ->where(['mitra' => UUID_PATTERN, 'service' => UUID_PATTERN])
            ->name('mitra.service.destroy');
        Route::patch('/mitra/{mitra}/services/{service}/toggle', [MitraDashboardController::class, 'toggleService'])
            ->where(['mitra' => UUID_PATTERN, 'service' => UUID_PATTERN])
            ->name('mitra.service.toggle');

        // Profil mitra: ganti/hapus foto profil (logo_url) dan ubah nama + deskripsi
        Route::put('/mitra/{mitra}/profile', [MitraProfileController::class, 'updateInfo'])
            ->where('mitra', UUID_PATTERN)->middleware('throttle:20,1')
            ->name('mitra.profile.update');
        Route::post('/mitra/{mitra}/profile/logo', [MitraProfileController::class, 'updateLogo'])
            ->where('mitra', UUID_PATTERN)->middleware('throttle:10,1')
            ->name('mitra.profile.logo.update');
        Route::delete('/mitra/{mitra}/profile/logo', [MitraProfileController::class, 'removeLogo'])
            ->where('mitra', UUID_PATTERN)->middleware('throttle:10,1')
            ->name('mitra.profile.logo.destroy');

        Route::get('/mitra/create', [OwnerMitraController::class, 'create'])->name('mitra.create');
        Route::post('/mitra', [OwnerMitraController::class, 'store'])->name('mitra.store');

        Route::get('/mitra/{mitra}/edit', [OwnerMitraController::class, 'edit'])
            ->where('mitra', UUID_PATTERN)->name('mitra.edit');
        Route::put('/mitra/{mitra}', [OwnerMitraController::class, 'update'])
            ->where('mitra', UUID_PATTERN)->name('mitra.update');
    });

    // ALIAS & PENGALIHAN RUTE MITRA
    Route::get('/mitra', fn() => redirect()->route('owner.mitra.dashboard'));
    Route::get('/mitra/dashboard', fn() => redirect()->route('owner.mitra.dashboard'))->name('mitra.legacy');

    Route::prefix('mitra/{mitra}')->as('mitra.')->where(['mitra' => UUID_PATTERN])->group(function () {
        Route::get('/dashboard', fn(string $mitra) => redirect()->route('owner.mitra.dashboard.unit', ['mitra' => $mitra]))->name('dashboard');
        Route::get('/bookings', fn(string $mitra) => redirect()->route('owner.mitra.dashboard.unit', ['mitra' => $mitra, 'tab' => 'booking']))->name('bookings');
        Route::get('/services', fn(string $mitra) => redirect()->route('owner.mitra.dashboard.unit', ['mitra' => $mitra, 'tab' => 'layanan']))->name('services');
        Route::get('/profile', fn(string $mitra) => redirect()->route('owner.mitra.dashboard.unit', ['mitra' => $mitra, 'tab' => 'profil']))->name('profile');
    });

    // ADMIN (role: admin)
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Persetujuan Petshop & Log Aktivitas
        Route::get('/petshops', [AdminDashboardController::class, 'petshops'])->name('petshops');
        Route::post('/petshops/{id}/status', [AdminDashboardController::class, 'updatePetshopStatus'])->name('petshops.status');
        Route::get('/activity', [AdminDashboardController::class, 'activity'])->name('activity');

        Route::view('/pricing', 'admin.pricing')->name('pricing');
        Route::view('/banner', 'admin.banner-promo')->name('banner');

        //Kelola Article
        Route::get('/artikel', [AdminArticleController::class, 'index'])->name('articles');
        Route::post('/artikel', [AdminArticleController::class, 'store'])->name('articles.store');
        Route::put('/artikel/{id}', [AdminArticleController::class, 'update'])->name('articles.update');
        Route::delete('/artikel/{id}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');

        // Backup Data
        Route::get('/backup', [AdminBackupController::class, 'index'])->name('backup');
        Route::post('/backup/download', [AdminBackupController::class, 'download'])
            ->middleware('throttle:5,1')
            ->name('backup.download');
    });

    // SUPERADMIN (role: super_admin)
    Route::prefix('superadmin')->name('superadmin.')->middleware('role:super_admin')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

        Route::view('/pendapatan', 'superadmin.pendapatan')->name('pendapatan');

        Route::get('/petshops', [SuperAdminController::class, 'petshops'])->name('petshops');
        Route::post('/petshops/{id}/status', [SuperAdminController::class, 'updatePetshopStatus'])->name('petshops.status');

        Route::get('/admins', [SuperAdminController::class, 'admins'])->name('admins');
        Route::post('/admins', [SuperAdminController::class, 'storeAdmin'])->name('admins.store');
        Route::post('/admins/{id}/action', [SuperAdminController::class, 'adminAction'])->name('admins.action');
    });
});

// DOKTER (Prototipe / Bebas akses)
Route::prefix('dokter')
    ->name('dokter.')
    ->group(function () {
        Route::get('/', [DokterController::class, 'dashboard'])->name('dashboard');

        Route::get('/pasien', [DokterController::class, 'pasien'])->name('pasien');
        Route::get('/pasien/{id}', [DokterController::class, 'pasienShow'])->name('pasien.show');

        Route::get('/pemeriksaan/baru', [DokterController::class, 'pemeriksaanCreate'])->name('pemeriksaan.create');
        Route::post('/pemeriksaan', [DokterController::class, 'pemeriksaanStore'])->name('pemeriksaan.store');

        Route::get('/rekam-medis', [DokterController::class, 'rekamMedis'])->name('rekam-medis');

        Route::get('/profil', [DokterController::class, 'profil'])->name('profil');
        Route::put('/profil', [DokterController::class, 'profilUpdate'])->name('profil.update');

        Route::post('/klinik/ganti', [DokterController::class, 'switchKlinik'])->name('klinik.switch');
    });