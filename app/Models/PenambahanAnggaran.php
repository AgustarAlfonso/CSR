<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenambahanAnggaran extends Model
{
    use HasFactory;

    protected $table = 'penambahan_anggarans';

    protected $fillable = [
        'anggaran_csr_id',
        'dana_baru',
        'sisa_tahun_lalu',
        'total_anggaran_tahun_ini',
        'tanggal_input',
    ];

    public function anggaranCsr()
    {
        return $this->belongsTo(AnggaranCsr::class, 'anggaran_csr_id');
    }
}
