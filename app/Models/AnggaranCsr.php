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

    public function getSisaRealisasiAttribute()
    {
        $totalRealisasi = $this->realisasi()->sum('realisasi_csr');
        return $this->jumlah_anggaran - $totalRealisasi;
    }
}
