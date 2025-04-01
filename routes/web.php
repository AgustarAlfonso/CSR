<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CSRController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', [CsrController::class, 'index'])->name('dashboard');
Route::post('/csr/filter', [CsrController::class, 'filter'])->name('csr.filter');
Route::get('/api/realisasi_csr', [CsrController::class, 'getRealisasiCsr']);
