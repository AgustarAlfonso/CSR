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


    
public function penambahan()
{
    return $this->hasOne(PenambahanAnggaran::class, 'anggaran_csr_id');
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

public function getTotalAnggaranTampilan()
{
    if (isset($this->total_anggaran_tampilan)) {
        return $this->total_anggaran_tampilan;
    }

    // 🛑 Tambahkan pengecekan kalau data ini fallback
    if (isset($this->sisa_dari_tahun_lalu) && $this->sisa_dari_tahun_lalu) {
        return $this->jumlah_anggaran;
    }

    $tahunSebelumnya = self::where('pemegang_saham', $this->pemegang_saham)
        ->where('tahun', $this->tahun - 1)
        ->first();

    $sisaTahunLalu = 0;

    if ($tahunSebelumnya) {
        $jumlahAnggaranTahunLalu = $tahunSebelumnya->jumlah_anggaran;
        $totalAnggaranTahunLalu = $tahunSebelumnya->getTotalAnggaranTampilan();
        $realisasiTahunLalu = \App\Models\Csr::where('pemegang_saham', $this->pemegang_saham)
            ->where('tahun', $this->tahun - 1)
            ->sum('realisasi_csr');

        $sisaTahunLalu = $totalAnggaranTahunLalu - $realisasiTahunLalu;
    }

    return $this->jumlah_anggaran + $sisaTahunLalu;
}





public function getSisaAnggaranTampilan()
{
    // Jika sudah ada properti sisa_anggaran_tampilan (misalnya dari controller), pakai itu
    if (isset($this->sisa_anggaran_tampilan)) {
        return $this->sisa_anggaran_tampilan;
    }

    $total = $this->getTotalAnggaranTampilan();

    $realisasi = \App\Models\Csr::where('pemegang_saham', $this->pemegang_saham)
        ->where('tahun', $this->tahun)
        ->sum('realisasi_csr');

    return $total - $realisasi;
}



public function getDetailRiwayatCsr()
{
    $realisasi = Csr::where('pemegang_saham', $this->pemegang_saham)
        ->where('tahun', $this->tahun)
        ->orderBy('bulan')
        ->get()
        ->groupBy('bulan');

    $totalAnggaran = $this->getTotalAnggaranTampilan();
    $sisaAkhirTahun = $this->getSisaAnggaranTampilan();
    $penambahanTahunIni = $this->jumlah_anggaran ?: 0;

    // Jika fallback, berarti penambahan tahun ini = 0, dan total anggaran = sisa dari tahun lalu
    if (!empty($this->sisa_dari_tahun_lalu) || !empty($this->is_fallback)) {
        $penambahanTahunIni = 0;
    }

    // Ambil sisa tahun lalu sebagai (total - penambahan tahun ini)
    $sisaTahunLalu = $totalAnggaran - $penambahanTahunIni;

    // Realisasi per bulan dan total
    $dataBulan = [];
    $totalRealisasi = 0;

    for ($bulan = 1; $bulan <= 12; $bulan++) {
        $realisasiBulan = isset($realisasi[$bulan]) ? $realisasi[$bulan]->sum('realisasi_csr') : 0;
        $dataBulan[$bulan] = [
            'bulan' => $bulan,
            'realisasi' => $realisasiBulan,
        ];
        $totalRealisasi += $realisasiBulan;
    }

    return [
        'sisa_tahun_lalu' => $sisaTahunLalu,
        'penambahan_tahun_ini' => $penambahanTahunIni,
        'total_realisasi_tahun_ini' => $totalRealisasi,
        'bulan_realisasi' => $dataBulan,
        'sisa_akhir_tahun' => $sisaAkhirTahun,
    ];
}









    
}
