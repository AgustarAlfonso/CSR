<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Csr extends Model
{
    use HasFactory;

    protected $table = 'csrs';
    protected $fillable = [
        'nama_program', 
        'bidang_kegiatan', 
        'pemegang_saham', 
        'bulan', 
        'tahun', 
        'realisasi_csr', 
        'ket'
    ];

    public function anggaran()
{
    return $this->belongsTo(AnggaranCsr::class, 'pemegang_saham', 'pemegang_saham')
                ->where('tahun', $this->tahun);
}
}