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
    
public function penambahan()
{
    return $this->hasOne(PenambahanAnggaran::class, 'anggaran_csr_id');
}


public function getDetailRiwayatCsr($filterBidangKegiatan = null)
{
    $realisasi = Csr::where('pemegang_saham', $this->pemegang_saham)
        ->where('tahun', $this->tahun);

    if ($filterBidangKegiatan) {
        $realisasi->whereIn('bidang_kegiatan', (array) $filterBidangKegiatan);
    }

    $realisasi = $realisasi->orderBy('bulan')->get()->groupBy('bulan');

    $penambahan = $this->penambahan;
    $sisaTahunLalu = $penambahan->sisa_tahun_lalu ?? 0;
    $penambahanTahunIni = $penambahan->dana_baru ?? 0;
    $bulanPenambahan = $penambahan ? \Carbon\Carbon::parse($penambahan->tanggal_input)->month : null;
    
    if (!$penambahan) {
        if (property_exists($this, 'adalah_fallback') && $this->adalah_fallback) {
            // Jika data ini fallback dan tidak ada penambahan, artinya dari tahun lalu
            $sisaTahunLalu = $this->jumlah_anggaran;
        } else {
            // Jika bukan fallback, maka itu data murni tahun ini
            $penambahanTahunIni = $this->jumlah_anggaran;
        }
    }
    

    $dataBulan = [];
    $saldo = $sisaTahunLalu + $penambahanTahunIni;
    $totalRealisasi = 0;

    for ($bulan = 1; $bulan <= 12; $bulan++) {
        $realisasiBulanIni = isset($realisasi[$bulan]) ? $realisasi[$bulan]->sum('realisasi_csr') : 0;

        $dataBulan[$bulan] = [
            'bulan' => $bulan,
            'realisasi' => $realisasiBulanIni,
        ];

        $totalRealisasi += $realisasiBulanIni;
    }

    return [
        'sisa_tahun_lalu' => $sisaTahunLalu,
        'penambahan_tahun_ini' => $penambahanTahunIni,
        'bulan_realisasi' => $dataBulan,
        'sisa_akhir_tahun' => max($saldo - $totalRealisasi, 0),
    ];
}




    
}
