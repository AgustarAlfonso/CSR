<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Csr;

class CsrController extends Controller
{
    public function index()
    {
        $csrs = Csr::all();
        $pemegang_saham = Csr::distinct()->pluck('pemegang_saham');
        $years = Csr::distinct()->pluck('tahun');
        $months = Csr::distinct()->pluck('bulan');
        $bidang_kegiatan = Csr::distinct()->pluck('bidang_kegiatan'); // Tambahkan bidang kegiatan
    
        return view('dashboard', compact('csrs', 'pemegang_saham', 'years', 'months', 'bidang_kegiatan'));
    }
    
    public function filter(Request $request)
    {
        $query = Csr::query();
    
        if (!empty($request->pemegang_saham)) {
            $query->whereIn('pemegang_saham', $request->pemegang_saham);
        }
    
        if (!empty($request->tahun)) {
            $query->whereIn('tahun', $request->tahun);
        }
    
        if (!empty($request->bulan)) {
            $query->whereIn('bulan', $request->bulan);
        }
    
        if (!empty($request->bidang_kegiatan)) { // Tambahkan filter bidang_kegiatan
            $query->whereIn('bidang_kegiatan', $request->bidang_kegiatan);
        }
    
        return response()->json($query->get());
    }
}
