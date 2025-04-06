<?php

namespace App\Http\Controllers;

use App\Models\AnggaranCsr as Anggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        $messages = [
            'pemegang_saham.required' => 'Pemegang saham wajib diisi.',
            'pemegang_saham.string' => 'Pemegang saham harus berupa teks.',
            'pemegang_saham.max' => 'Pemegang saham maksimal :max karakter.',
            'tahun.required' => 'Tahun wajib diisi.',
            'tahun.integer' => 'Tahun harus berupa angka.',
            'bulan.required' => 'Bulan wajib diisi.',
            'bulan.string' => 'Bulan harus berupa teks.',
            'bulan.max' => 'Bulan maksimal :max karakter.',
            'jumlah_anggaran.required' => 'Jumlah anggaran wajib diisi.',
            'jumlah_anggaran.numeric' => 'Jumlah anggaran harus berupa angka.',
        ];
    
        $validator = Validator::make($request->all(), [
            'pemegang_saham' => 'required|string|max:255',
            'tahun' => 'required|integer',
            'bulan' => 'required|string|max:20',
            'jumlah_anggaran' => 'required|numeric',
            'konfirmasi_tambah' => 'nullable|string',
        ], $messages);
    
        if ($validator->fails()) {
            $existing = Anggaran::where('pemegang_saham', $request->pemegang_saham)
                ->where('tahun', $request->tahun)
                ->first();
    
            if ($existing) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors($validator)
                    ->with([
                        'confirm' => true,
                        'existing_data' => $existing,
                        'request_data' => $request->all(),
                    ]);
            }
    
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }
    
        $existing = Anggaran::where('pemegang_saham', $request->pemegang_saham)
            ->where('tahun', $request->tahun)
            ->first();
    
        // Kalau udah ada
        if ($existing) {
            if ($request->input('konfirmasi_tambah') === 'ya') {
                $jumlahLama = $existing->jumlah_anggaran;
                $jumlahBaru = $request->jumlah_anggaran;
                $totalBaru = $jumlahLama + $jumlahBaru;
    
                $createdDate = $existing->created_at->format('d M Y');
                

                $keterangan = "Anggaran awal: Rp " . number_format($jumlahLama, 0, ',', '.') .
                    " (dibuat pada " . $createdDate . ")" .
                    "\nPenambahan tanggal " . now()->format('d M Y') . ": Rp " . number_format($jumlahBaru, 0, ',', '.') .
                    "\nTotal anggaran sekarang: Rp " . number_format($totalBaru, 0, ',', '.');
                
                // Update
                $existing->jumlah_anggaran = $totalBaru;
                $existing->keterangan_penambahan = $keterangan;
                $existing->save();
    
                return redirect()->route('anggaran.index')->with('success', 'Anggaran berhasil ditambahkan ke data yang sudah ada.');
            }
    
            // Kalau belum ada konfirmasi, kirim ke view buat munculin modal
            return redirect()->back()->with([
                'confirm' => true,
                'existing_data' => $existing,
                'request_data' => $request->all()
            ]);
        }
    
        // Kalau gak ada data duplikat → langsung insert
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
        $messages = [
            'pemegang_saham.required' => 'Pemegang saham wajib diisi.',
            'pemegang_saham.string' => 'Pemegang saham harus berupa teks.',
            'pemegang_saham.max' => 'Pemegang saham maksimal :max karakter.',
            'tahun.required' => 'Tahun wajib diisi.',
            'tahun.integer' => 'Tahun harus berupa angka.',
            'bulan.required' => 'Bulan wajib diisi.',
            'bulan.string' => 'Bulan harus berupa teks.',
            'bulan.max' => 'Bulan maksimal :max karakter.',
            'jumlah_anggaran.required' => 'Jumlah anggaran wajib diisi.',
            'jumlah_anggaran.numeric' => 'Jumlah anggaran harus berupa angka.',
        ];
    
        $validatedData = $request->validate([
            'pemegang_saham' => 'required|string|max:255',
            'tahun' => 'required|integer',
            'bulan' => 'required|string|max:20',
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
