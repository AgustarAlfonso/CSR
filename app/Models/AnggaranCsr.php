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

    public function getSisaAnggaranPerBulanAttribute()
    {
        $realisasiData = Csr::where('pemegang_saham', $this->pemegang_saham)
            ->where('tahun', $this->tahun)
            ->orderBy('bulan')
            ->get()
            ->groupBy('bulan');
    
        $sisaAnggaran = $this->jumlah_anggaran;
        $result = [];
    
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $realisasi = isset($realisasiData[$bulan]) ? $realisasiData[$bulan]->sum('realisasi_csr') : 0;
            
            $result[$bulan] = [
                'bulan' => $bulan,
                'total_anggaran' => $sisaAnggaran,
                'realisasi' => $realisasi,
                'sisa_anggaran' => max($sisaAnggaran - $realisasi, 0)
            ];
    
            $sisaAnggaran = max($sisaAnggaran - $realisasi, 0);
        }
    
        return $result;
    }
    

    

    
}
