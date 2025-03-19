<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Csr extends Model
{
    use HasFactory;

    protected $table = 'csrs';
    protected $fillable = ['nama_program', 'sponsor', 'tahun', 'bulan', 'modal_csr', 'realisasi_csr', 'sisa_csr'];
}
