<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CSRController;
use App\Http\Controllers\AnggaranController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', [CsrController::class, 'index'])->name('dashboard');
Route::post('/csr/filter', [CsrController::class, 'filter'])->name('csr.filter');
Route::get('/api/realisasi_csr', [CsrController::class, 'getRealisasiCsr']);

Route::get('/hasil-filter', [CsrController::class, 'hasilFilter'])->name('csr.hasil_filter');
Route::get('/csr/create', [\App\Http\Controllers\CsrController::class, 'create'])->name('csr.create');
Route::post('/csr/store', [\App\Http\Controllers\CsrController::class, 'store'])->name('csr.store');
Route::get('csr/{csr}/edit', [\App\Http\Controllers\CsrController::class, 'edit'])->name('csr.edit');
Route::delete('csr/{csr}', [\App\Http\Controllers\CsrController::class, 'destroy'])->name('csr.destroy');
Route::get('/sisa-anggaran', [\App\Http\Controllers\CsrController::class, 'getSisaAnggaran'])->name('csr.sisa-anggaran');
Route::post('/csr/chart/bidang-kegiatan', [CsrController::class, 'chartByBidangKegiatan'])->name('csr.chart.bidang_kegiatan');


Route::get('/anggaran', [AnggaranController::class, 'index'])->name('anggaran.index');
Route::resource('anggaran', AnggaranController::class);

