<?php

namespace App\Http\Controllers;

use App\Models\AnggaranCsr;
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
        $anggaranQuery = AnggaranCsr::query();
    
        if (!empty($request->pemegang_saham) && $request->pemegang_saham !== 'semua') {
            $anggaranQuery->whereIn('pemegang_saham', (array) $request->pemegang_saham);
        }
    
        if (!empty($request->tahun)) {
            $anggaranQuery->whereIn('tahun', (array) $request->tahun);
        }
    
        // Ambil filter bidang kegiatan jika ada
        $bidangKegiatan = !empty($request->bidang_kegiatan) ? (array) $request->bidang_kegiatan : null;
    
        $anggaranList = $anggaranQuery->get();
        $result = [];
    
        foreach ($anggaranList as $anggaran) {
            $dataPerBulan = $anggaran->getSisaAnggaranPerBulan($bidangKegiatan);
    
            if (!empty($request->bulan)) {
                $bulan = (int) $request->bulan;
                $result[] = $dataPerBulan[$bulan] ?? [
                    'bulan' => $bulan,
                    'total_anggaran' => $anggaran->jumlah_anggaran,
                    'realisasi' => 0,
                    'sisa_anggaran' => $anggaran->jumlah_anggaran
                ];
            } else {
                $result = array_merge($result, array_values($dataPerBulan));
            }
        }
    
        $totalAnggaran = !empty($request->bulan) 
            ? array_sum(array_column($result, 'total_anggaran'))
            : $anggaranList->sum('jumlah_anggaran');
            
        $totalRealisasi = array_sum(array_column($result, 'realisasi'));
        $sisaCsr = max($totalAnggaran - $totalRealisasi, 0);
    
        return response()->json([
            'jumlah_anggaran' => $totalAnggaran,
            'realisasi_csr' => $totalRealisasi,
            'sisa_csr' => $sisaCsr
        ]);
    }
    


    public function getRealisasiCsr(Request $request)
    {
        $query = Csr::query();
    
        if (!empty($request->pemegang_saham)) {
            $query->whereIn('pemegang_saham', (array) $request->pemegang_saham);
        }
        
        if (!empty($request->tahun)) {
            $query->whereIn('tahun', (array) $request->tahun);
        }
        
        if (!empty($request->bulan)) {
            $query->whereIn('bulan', (array) $request->bulan);
        }
        
        if (!empty($request->bidang_kegiatan)) {
            $query->whereIn('bidang_kegiatan', (array) $request->bidang_kegiatan);
        }
    
        $totalRealisasi = $query->sum('realisasi_csr');
    
        return response()->json([
            'pemegang_saham' => $request->pemegang_saham,
            'realisasi_csr' => $totalRealisasi
        ]);
    }

    public function hasilFilter(Request $request)
    {
        $query = Csr::query();
    
        if ($request->pemegang_saham) {
            $query->where('pemegang_saham', $request->pemegang_saham);
        }
    
        if ($request->bidang_kegiatan) {
            $query->where('bidang_kegiatan', $request->bidang_kegiatan);
        }
    
        if ($request->tahun) {
            $query->where('tahun', $request->tahun);
        }
    
        if ($request->bulan) {
            $query->where('bulan', $request->bulan);
        }
    
        // Hitung total sebelum paginasi
        $totalRealisasi = $query->sum('realisasi_csr');
    
        // Urutkan dan paginasi
        $data = $query->orderBy('id', 'desc')->paginate(10);
    
        return view('hasil_filter', compact('data', 'totalRealisasi'))
        ->with([
            'pemegang_saham' => $request->pemegang_saham,
            'bidang_kegiatan' => $request->bidang_kegiatan,
            'tahun' => $request->tahun,
            'bulan' => $request->bulan,
        ]);
    }

    public function create()
{
    $availableYears = \App\Models\AnggaranCsr::select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');
    return view('csr.create', compact('availableYears'));
     // Asumsinya file blade kamu di resources/views/csr/create.blade.php
}

public function store(Request $request)
{
    // Validasi input awal
    $request->validate([
        'nama_program' => 'required|string|max:255',
        'pemegang_saham' => 'required|string',
        'tahun' => 'required|integer',
        'bulan' => 'required|integer|min:1|max:12',
        'bidang_kegiatan' => 'required|string',
        'realisasi_csr' => 'required|numeric|min:0',
        'ket' => 'nullable|string',
    ]);

    // Cek sisa anggaran
    $anggaran = \App\Models\AnggaranCsr::where('pemegang_saham', $request->pemegang_saham)
                ->where('tahun', $request->tahun)
                ->first();

    if (!$anggaran) {
        return back()->withErrors(['pemegang_saham' => 'Anggaran untuk pemegang saham dan tahun ini tidak ditemukan.'])->withInput();
    }

    $sisa = $anggaran->hitungSisaAnggaranTotal();

    if ($request->realisasi_csr > $sisa) {
        return back()->withErrors(['realisasi_csr' => 'Realisasi dana melebihi sisa anggaran yang tersedia.'])->withInput();
    }

    // Simpan data CSR
    \App\Models\Csr::create([
        'nama_program' => $request->nama_program,
        'pemegang_saham' => $request->pemegang_saham,
        'tahun' => $request->tahun,
        'bulan' => $request->bulan,
        'bidang_kegiatan' => $request->bidang_kegiatan,
        'realisasi_csr' => $request->realisasi_csr,
        'ket' => $request->ket,
    ]);

    return redirect()->route('dashboard')->with('success', 'Program CSR berhasil ditambahkan.');
}

    
    public function edit($id)
{
    $csr = CSR::findOrFail($id);
    return view('csr.edit', compact('csr'));
}

public function destroy($id)
{
    $csr = CSR::findOrFail($id);
    $csr->delete();

    return redirect()->back()->with('success', 'Data CSR berhasil dihapus.');


}

public function getSisaAnggaran(Request $request)
{
    $request->validate([
        'pemegang_saham' => 'required|string',
        'tahun' => 'required|integer',
    ]);

    $anggaran = \App\Models\AnggaranCsr::where('pemegang_saham', $request->pemegang_saham)
                ->where('tahun', $request->tahun)
                ->first();

    if (!$anggaran) {
        return response()->json([
            'sisa' => 0,
            'message' => 'Tidak ada anggaran untuk data ini.'
        ]);
    }

    return response()->json([
        'sisa' => $anggaran->hitungSisaAnggaranTotal(),
        'message' => 'Sisa anggaran berhasil diambil.'
    ]);
}




}

