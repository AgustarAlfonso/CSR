<?php

namespace App\Http\Controllers;

use App\Models\AnggaranCsr as Anggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\AnggaranCsr;
use App\Models\PenambahanAnggaran;
use Carbon\Carbon;

class AnggaranController extends Controller
{


    public function index(Request $request)
    {
        $tahunSekarang = now()->year;
        $tahunFilter = $request->filled('tahun') ? (int)$request->tahun : $tahunSekarang;
    
        $query = AnggaranCsr::query()->where('tahun', $tahunFilter);
    
        if ($request->filled('pemegang_saham')) {
            $query->whereIn('pemegang_saham', $request->pemegang_saham);
        }
    
        $anggaranTahunIni = $query->get();
        $fallback = false;
    
        // Ambil semua pemegang saham
        $semuaPemegangSahamQuery = AnggaranCsr::select('pemegang_saham')->distinct();
        if ($request->filled('pemegang_saham')) {
            $semuaPemegangSahamQuery->whereIn('pemegang_saham', $request->pemegang_saham);
        }
        $semuaPemegangSaham = $semuaPemegangSahamQuery->pluck('pemegang_saham')->toArray();
            
        // Pemegang saham yang sudah punya data tahun ini
        $sudahAda = $anggaranTahunIni->pluck('pemegang_saham')->toArray();
    
        // Cari pemegang saham yang belum ada datanya
        $belumAda = array_diff($semuaPemegangSaham, $sudahAda);
    
        $fallbackCollection = collect();
    
        if (!empty($belumAda)) {
            $tahunSebelumnya = $tahunFilter - 1;
    
            $fallbacks = AnggaranCsr::whereIn('pemegang_saham', $belumAda)
            ->where('tahun', $tahunSebelumnya)
            ->get()
            ->filter(function ($item) {
                return $item->getSisaAnggaranTampilan() > 0;
            })
            ->map(function ($item) use ($tahunFilter) {
                $sisa = $item->getSisaAnggaranTampilan(); // Ambil sekali saja
                $clone = clone $item;
                $clone->tahun = $tahunFilter;
                $clone->jumlah_anggaran = $sisa; // Gunakan hasil yang udah fix
                $clone->sisa_dari_tahun_lalu = true;
                return $clone;
            });
        
    
            if ($fallbacks->isNotEmpty()) {
                $fallback = true;
                $fallbackCollection = $fallbacks;
            }
        }
    
        // Gabungkan data asli + fallback
        $anggaran = $anggaranTahunIni->concat($fallbackCollection)->map(function ($data) {
            $data->total_anggaran_tampilan = $data->getTotalAnggaranTampilan();
            $data->sisa_anggaran_tampilan = $data->getSisaAnggaranTampilan();
            return $data;
        });
    
        $totalAnggaran = $anggaran->sum('total_anggaran_tampilan');
    
        // Daftar tahun termasuk tahun sekarang jika belum ada di DB
        $daftarTahun = AnggaranCsr::select('tahun')->distinct()->pluck('tahun');
        if (!$daftarTahun->contains($tahunSekarang)) {
            $daftarTahun->push($tahunSekarang);
        }
        $daftarTahun = $daftarTahun->sort()->values();
    
        $desiredOrder = [
            'Provinsi Kepulauan Riau',
            'Provinsi Riau',
            'Kab. Bengkalis',
            'Kab. Bintan',
            'Kab. Indragiri Hilir',
            'Kab. Indragiri Hulu',
            'Kab. Kampar',
            'Kab. Karimun',
            'Kab. Kepulauan Anambas',
            'Kab. Kuansing',
            'Kab. Lingga',
            'Kab. Meranti',
            'Kab. Natuna',
            'Kab. Pelalawan',
            'Kab. Rokan Hilir',
            'Kab. Rokan Hulu',
            'Kab. Siak',
            'Kota Batam',
            'Kota Dumai',
            'Kota Pekanbaru',
            'Kota Tanjung Pinang',
        ];
        
        $daftarPemegangSaham = AnggaranCsr::select('pemegang_saham')
            ->distinct()
            ->pluck('pemegang_saham')
            ->filter() // Buat jaga-jaga kalau ada null
            ->sortBy(function($item) use ($desiredOrder) {
                return array_search($item, $desiredOrder);
            })
            ->values();

            $customOrder = [
                'Provinsi Kepulauan Riau',
                'Provinsi Riau',
                'Kab. Bengkalis',
                'Kab. Bintan',
                'Kab. Indragiri Hilir',
                'Kab. Indragiri Hulu',
                'Kab. Kampar',
                'Kab. Karimun',
                'Kab. Kepulauan Anambas',
                'Kab. Kuansing',
                'Kab. Lingga',
                'Kab. Meranti',
                'Kab. Natuna',
                'Kab. Pelalawan',
                'Kab. Rokan Hilir',
                'Kab. Rokan Hulu',
                'Kab. Siak',
                'Kota Batam',
                'Kota Dumai',
                'Kota Pekanbaru',
                'Kota Tanjung Pinang',
            ];
            
            $anggaran = $anggaran->sortBy(function ($item) use ($customOrder) {
                $index = array_search($item->pemegang_saham, $customOrder);
                return $index === false ? PHP_INT_MAX : $index;
            })->values(); 

            

            return view('anggaran.index', compact(
            'anggaran',
            'totalAnggaran',
            'daftarTahun',
            'daftarPemegangSaham',
            'tahunFilter',
            'fallback'
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
                'jumlah_anggaran' => $jumlahBaru, // ← pakai total hasil penjumlahan
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
