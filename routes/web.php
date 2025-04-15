<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CSRController;
use App\Http\Controllers\AnggaranController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');

Route::post('/', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/kelola-user', [AuthController::class, 'kelolaUser'])->name('auth.kelola');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

Route::get('/profile', [AuthController::class, 'profile'])->name('auth.profile');
Route::put('/profile', [AuthController::class, 'updateProfile'])->name('auth.profile.update');



Route::get('/dashboard', [CsrController::class, 'index'])->name('dashboard');
Route::post('/csr/filter', [CsrController::class, 'filter'])->name('csr.filter');
Route::get('/api/realisasi_csr', [CsrController::class, 'getRealisasiCsr']);

Route::get('/hasil-filter', [CsrController::class, 'hasilFilter'])->name('csr.hasil_filter');
Route::get('/csr/create', [\App\Http\Controllers\CsrController::class, 'create'])->name('csr.create');
Route::post('/csr/store', [\App\Http\Controllers\CsrController::class, 'store'])->name('csr.store');
Route::get('csr/{csr}/edit', [\App\Http\Controllers\CsrController::class, 'edit'])->name('csr.edit');
Route::post('/csr/{csr}', [CsrController::class, 'update'])->name('csr.update');
Route::delete('csr/{csr}', [\App\Http\Controllers\CsrController::class, 'destroy'])->name('csr.destroy');
Route::get('/sisa-anggaran', [\App\Http\Controllers\CsrController::class, 'getSisaAnggaran'])->name('csr.sisa-anggaran');
Route::post('/csr/chart/bidang-kegiatan', [CsrController::class, 'chartByBidangKegiatan'])->name('csr.chart.bidang_kegiatan');

route::get('/csr/riwayat', [CsrController::class, 'riwayatCsr'])->name('csr.riwayat');
Route::get('/csr/riwayat/ajax', [CsrController::class, 'riwayatCsrAjax'])->name('csr.riwayat.ajax');



Route::get('/anggaran', [AnggaranController::class, 'index'])->name('anggaran.index');
Route::resource('anggaran', AnggaranController::class);

