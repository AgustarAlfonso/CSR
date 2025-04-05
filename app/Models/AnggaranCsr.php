<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggaranCsr extends Model
{
    use HasFactory;

    protected $table = 'anggaran_csrs';

    protected $fillable = [
        'pemegang_saham',  // Nama pemegang saham
        'tahun',           // Tahun anggaran
        'bulan',           // Bulan anggaran
        'jumlah_anggaran'  // Total dana yang diberikan
    ];

    public function realisasi()
    {
        return $this->hasMany(Csr::class, 'pemegang_saham', 'pemegang_saham')
                    ->where('tahun', $this->tahun);
    }

    public function getSisaAnggaranPerBulan($filterBidangKegiatan = null)
{
    // Ambil semua realisasi (tanpa filter) untuk menghitung sisa kronologis
    $allRealisasiData = Csr::where('pemegang_saham', $this->pemegang_saham)
        ->where('tahun', $this->tahun)
        ->orderBy('bulan')
        ->get()
        ->groupBy('bulan');

    // Ambil realisasi hanya dari bidang yang difilter untuk laporan tampil
    $filteredRealisasiData = Csr::where('pemegang_saham', $this->pemegang_saham)
        ->where('tahun', $this->tahun);

    if ($filterBidangKegiatan) {
        $filteredRealisasiData->whereIn('bidang_kegiatan', (array) $filterBidangKegiatan);
    }

    $filteredRealisasiData = $filteredRealisasiData->orderBy('bulan')->get()->groupBy('bulan');

    $sisaAnggaran = $this->jumlah_anggaran;
    $result = [];

    for ($bulan = 1; $bulan <= 12; $bulan++) {
        $realisasiSemuaBidang = isset($allRealisasiData[$bulan]) ? $allRealisasiData[$bulan]->sum('realisasi_csr') : 0;
        $realisasiTerfilter = isset($filteredRealisasiData[$bulan]) ? $filteredRealisasiData[$bulan]->sum('realisasi_csr') : 0;

        $result[$bulan] = [
            'bulan' => $bulan,
            'total_anggaran' => $sisaAnggaran,
            'realisasi' => $realisasiTerfilter,
            'sisa_anggaran' => max($sisaAnggaran - $realisasiTerfilter, 0)
        ];

        $sisaAnggaran = max($sisaAnggaran - $realisasiSemuaBidang, 0);
    }

    return $result;
}

public function hitungSisaAnggaranTotal()
{
    // Ambil total realisasi CSR dari program yang match dengan pemegang saham dan tahun
    $totalRealisasi = \App\Models\Csr::where('pemegang_saham', $this->pemegang_saham)
        ->where('tahun', $this->tahun)
        ->sum('realisasi_csr');

    // Hitung sisa
    return max($this->jumlah_anggaran - $totalRealisasi, 0);
}
    

    

    
}
