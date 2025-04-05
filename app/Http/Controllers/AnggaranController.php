<?php

namespace App\Http\Controllers;

use App\Models\AnggaranCsr as Anggaran;
use Illuminate\Http\Request;

class AnggaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Anggaran::query();
    
        if ($request->filled('pemegang_saham')) {
            $query->whereIn('pemegang_saham', $request->pemegang_saham);
        }
    
        if ($request->filled('tahun')) {
            $query->whereIn('tahun', $request->tahun);
        }
    
        $anggaran = $query->paginate(10);
        $totalAnggaran = $query->sum('jumlah_anggaran');
    
    
        $daftarPemegangSaham = Anggaran::select('pemegang_saham')->distinct()->pluck('pemegang_saham');
        $daftarTahun = Anggaran::select('tahun')->distinct()->pluck('tahun');
    
        if ($request->ajax()) {
            return view('anggaran._table', compact('anggaran', 'totalAnggaran'))->render();
        }
        
        return view('anggaran.index', compact('anggaran', 'totalAnggaran', 'daftarPemegangSaham', 'daftarTahun'));
    }
    

    public function create()
    {
        return view('anggaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pemegang_saham' => 'required|string|max:255',
            'tahun' => 'required|integer',
            'bulan' => 'required|string|max:20',
            'jumlah_anggaran' => 'required|numeric',
        ]);

        Anggaran::create($request->all());

        return redirect()->route('anggaran.index')->with('success', 'Anggaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $anggaran = Anggaran::findOrFail($id);
        return view('anggaran.edit', compact('anggaran'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pemegang_saham' => 'required|string|max:255',
            'tahun' => 'required|integer',
            'bulan' => 'required|string|max:20',
            'jumlah_anggaran' => 'required|numeric',
        ]);

        $anggaran = Anggaran::findOrFail($id);
        $anggaran->update($request->all());

        return redirect()->route('anggaran.index')->with('success', 'Anggaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $anggaran = Anggaran::findOrFail($id);
        $anggaran->delete();

        return redirect()->route('anggaran.index')->with('success', 'Anggaran berhasil dihapus.');
    }
}
