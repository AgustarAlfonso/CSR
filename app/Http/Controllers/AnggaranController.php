<?php

namespace App\Http\Controllers;

use App\Models\AnggaranCsr as Anggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\AnggaranCsr;
use App\Models\PenambahanAnggaran;
class AnggaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Anggaran::query();
    
        $tahunTerbaru = Anggaran::max('tahun');
    
        // Default ke tahun terbaru kalau belum dipilih
        if (!$request->filled('tahun')) {
            $request->merge(['tahun' => $tahunTerbaru]);
        }
    
        // Filter pemegang saham (bisa banyak)
        if ($request->filled('pemegang_saham')) {
            $query->whereIn('pemegang_saham', $request->pemegang_saham);
        }
    
        // Filter tahun (hanya satu)
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
    
        // Data paginasi dan total anggaran
        $anggaran = $query->paginate(21);
        $totalAnggaran = $query->sum('jumlah_anggaran');
    
        // Data untuk pilihan filter
        $daftarPemegangSaham = Anggaran::select('pemegang_saham')->distinct()->pluck('pemegang_saham');
        $daftarTahun = Anggaran::select('tahun')->distinct()->pluck('tahun');
    
        // Untuk AJAX render (jika pakai live reload atau Inertia)
        if ($request->ajax()) {
            return view('anggaran._table', compact('anggaran', 'totalAnggaran'))->render();
        }
    
        return view('anggaran.index', compact(
            'anggaran', 
            'totalAnggaran', 
            'daftarPemegangSaham', 
            'daftarTahun',
            'tahunTerbaru'
        ));
    }
    
    

    public function create()
    {
        return view('anggaran.create');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pemegang_saham' => 'required|string|max:255',
            'tahun' => 'required|integer',
            'jumlah_anggaran' => 'required|numeric',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $pemegangSaham = $request->pemegang_saham;
        $tahun = $request->tahun;
        $jumlahBaru = $request->jumlah_anggaran;
    
        // 🛑 Cek apakah anggaran untuk pemegang saham dan tahun tersebut SUDAH ADA
        $existingAnggaran = Anggaran::where('pemegang_saham', $pemegangSaham)
            ->where('tahun', $tahun)
            ->first();
    
        if ($existingAnggaran) {
            // Jika sudah ada dan tidak ada flag konfirmasi, tolak
            return redirect()->back()
                ->with('duplicate_error', true)
                ->with('request_data', $request->all());
        }
    
        // 🟢 Cek jika ini adalah permintaan dari konfirmasi modal
        if ($request->konfirmasi_tambah === 'ya') {
            $sisaTahunLalu = $request->sisa_tahun_lalu ?? 0;
            $totalTahunIni = $jumlahBaru + $sisaTahunLalu;
    
            // Simpan data anggaran
            $newAnggaran = Anggaran::create([
                'pemegang_saham' => $pemegangSaham,
                'tahun' => $tahun,
                'jumlah_anggaran' => $totalTahunIni, // ← pakai total hasil penjumlahan
            ]);
            
    
            // Simpan histori penambahan
            PenambahanAnggaran::create([
                'anggaran_csr_id' => $newAnggaran->id,
                'dana_baru' => $jumlahBaru,
                'sisa_tahun_lalu' => $sisaTahunLalu,
                'total_anggaran_tahun_ini' => $totalTahunIni,
                'tanggal_input' => now(),
            ]);
    
            return redirect()->route('anggaran.index')->with('success', 'Anggaran baru berhasil ditambahkan.');
        }
    
        // 🟡 Cek sisa dari tahun sebelumnya
        $tahunSebelumnya = $tahun - 1;
        $anggaranTahunLalu = Anggaran::where('pemegang_saham', $pemegangSaham)
            ->where('tahun', $tahunSebelumnya)
            ->first();
    
        $sisaTahunLalu = $anggaranTahunLalu ? $anggaranTahunLalu->hitungSisaAnggaranTotal() : 0;
        $totalTahunIni = $jumlahBaru + $sisaTahunLalu;
    
        // Kirim ke tampilan modal konfirmasi
        return redirect()->back()
            ->with('confirm', true)
            ->with('request_data', $request->all())
            ->with('sisa_tahun_lalu', $sisaTahunLalu)
            ->with('total_setelah_tambah', $totalTahunIni);
    }
    
    
    
    

    public function edit($id)
    {
        $anggaran = Anggaran::findOrFail($id);
        return view('anggaran.edit', compact('anggaran'));
    }

    public function update(Request $request, $id)
    {
        $messages = [
            'pemegang_saham.required' => 'Pemegang saham wajib diisi.',
            'pemegang_saham.string' => 'Pemegang saham harus berupa teks.',
            'pemegang_saham.max' => 'Pemegang saham maksimal :max karakter.',
            'tahun.required' => 'Tahun wajib diisi.',
            'tahun.integer' => 'Tahun harus berupa angka.',
            'jumlah_anggaran.required' => 'Jumlah anggaran wajib diisi.',
            'jumlah_anggaran.numeric' => 'Jumlah anggaran harus berupa angka.',
        ];
    
        $validatedData = $request->validate([
            'pemegang_saham' => 'required|string|max:255',
            'tahun' => 'required|integer',
            'jumlah_anggaran' => 'required|numeric',
        ], $messages);
    
        $anggaran = Anggaran::findOrFail($id);
        $anggaran->update($validatedData);
    
        return redirect()->route('anggaran.index')->with('success', 'Anggaran berhasil diperbarui.');
    }
    

    public function destroy($id)
    {
        $anggaran = Anggaran::findOrFail($id);
        $anggaran->delete();

        return redirect()->route('anggaran.index')->with('success', 'Anggaran berhasil dihapus.');
    }
}
