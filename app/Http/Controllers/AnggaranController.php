<?php

namespace App\Http\Controllers;

use App\Models\AnggaranCsr as Anggaran;
use Illuminate\Http\Request;

class AnggaranController extends Controller
{
    public function index()
    {
        $anggaran = Anggaran::latest()->paginate(10);
        $totalAnggaran = $anggaran->sum('jumlah_anggaran');

        return view('anggaran.index', compact('anggaran', 'totalAnggaran'));
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
