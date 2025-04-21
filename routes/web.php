<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CSRController;
use App\Http\Controllers\AnggaranController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Route Auth
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Error Pages
Route::view('/errors/403', 'errors.403');

// Route untuk role 1 dan 2 (contoh: Kelola CSR)
Route::middleware(['auth', 'role:1,2'])->group(function () {
    Route::prefix('csr')->name('csr.')->group(function () {
        Route::get('/create', [CSRController::class, 'create'])->name('create');
        Route::post('/store', [CSRController::class, 'store'])->name('store');
        Route::get('/{csr}/edit', [CSRController::class, 'edit'])->name('edit');
        Route::post('/{csr}', [CSRController::class, 'update'])->name('update');
        Route::delete('/{csr}', [CSRController::class, 'destroy'])->name('destroy');
    });
});

// Route untuk role 1, 2, 3 (contoh: Semua akses umum)
Route::middleware(['auth', 'role:1,2,3'])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('auth.profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('auth.profile.update');

    Route::prefix('csr')->name('csr.')->group(function () {
        Route::get('/riwayat', [CSRController::class, 'riwayatCsr'])->name('riwayat');
        Route::get('/riwayat/ajax', [CSRController::class, 'riwayat.ajax'])->name('riwayat.ajax');
        Route::post('/filter', [CSRController::class, 'filter'])->name('filter');
        Route::post('/chart/bidang-kegiatan', [CSRController::class, 'chartByBidangKegiatan'])->name('chart.bidang_kegiatan');
    });

    Route::get('/dashboard', [CSRController::class, 'index'])->name('dashboard');
    Route::get('/api/realisasi_csr', [CSRController::class, 'getRealisasiCsr'])->name('api.realisasi_csr');
    Route::get('/hasil-filter', [CSRController::class, 'hasilFilter'])->name('csr.hasil_filter');
    Route::get('/sisa-anggaran', [CSRController::class, 'getSisaAnggaran'])->name('csr.sisa_anggaran');
});

// Route untuk role 1 aja (contoh: Kelola User)
Route::middleware(['auth', 'role:1'])->group(function () {
    Route::get('/kelola-user', [AuthController::class, 'kelolaUser'])->name('auth.kelola');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });
});

// Route Anggaran (tanpa auth/middleware khusus)
// Route Anggaran: index (1,2,3) & lainnya (1,2)
Route::middleware(['auth', 'role:1,2,3'])->group(function () {
    Route::get('/anggaran', [AnggaranController::class, 'index'])->name('anggaran.index');
});

Route::middleware(['auth', 'role:1,2'])->group(function () {
    Route::prefix('anggaran')->name('anggaran.')->group(function () {
        Route::get('/create', [AnggaranController::class, 'create'])->name('create');
        Route::post('/', [AnggaranController::class, 'store'])->name('store');
        Route::get('/{anggaran}/edit', [AnggaranController::class, 'edit'])->name('edit');
        Route::put('/{anggaran}', [AnggaranController::class, 'update'])->name('update');
        Route::delete('/{anggaran}', [AnggaranController::class, 'destroy'])->name('destroy');
        Route::get('/{anggaran}', [AnggaranController::class, 'show'])->name('show');
    });
});

