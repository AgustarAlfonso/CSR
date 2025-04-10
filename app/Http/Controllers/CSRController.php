<?php

namespace App\Http\Controllers;

use App\Models\AnggaranCsr;
use Illuminate\Http\Request;
use App\Models\Csr;
use Illuminate\Support\Facades\DB;

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
        $tahunSekarang = now()->year;
        $tahunFilter = !empty($request->tahun) ? (int)$request->tahun : $tahunSekarang;
    
        $anggaranQuery = AnggaranCsr::query()->where('tahun', $tahunFilter);
    
        // Filter pemegang saham
        if (!empty($request->pemegang_saham) && $request->pemegang_saham !== 'semua') {
            $anggaranQuery->whereIn('pemegang_saham', (array) $request->pemegang_saham);
        }
    
        $bidangKegiatan = !empty($request->bidang_kegiatan) ? (array) $request->bidang_kegiatan : null;
    
        $anggaranList = $anggaranQuery->get();
    
        // Cek fallback jika kosong dan tahun filter adalah tahun sekarang
        if ($anggaranList->isEmpty() && $tahunFilter === $tahunSekarang) {
            $tahunSebelumnya = $tahunSekarang - 1;
    
            // Ambil semua pemegang saham
            $semuaPemegangSaham = AnggaranCsr::select('pemegang_saham')->distinct()->pluck('pemegang_saham')->toArray();
    
            // Jika difilter, pakai filter itu
            $pemegangSahamFilter = !empty($request->pemegang_saham) && $request->pemegang_saham !== 'semua'
                ? (array) $request->pemegang_saham
                : $semuaPemegangSaham;
    
            // Ambil data dari tahun lalu
            $anggaranList = AnggaranCsr::where('tahun', $tahunSebelumnya)
                ->whereIn('pemegang_saham', $pemegangSahamFilter)
                ->get()
                ->filter(function ($item) {
                    return $item->hitungSisaAnggaranTotal() > 0;
                })
                ->map(function ($item) use ($tahunSekarang) {
                    $clone = clone $item;
                    $clone->tahun = $tahunSekarang;
                    $clone->jumlah_anggaran = $item->hitungSisaAnggaranTotal();
                    $clone->sisa_dari_tahun_lalu = true;
                    return $clone;
                });
        }
    
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
    
    public function chartByBidangKegiatan(Request $request)
{
    $query = Csr::query();

    if (!empty($request->pemegang_saham) && $request->pemegang_saham !== 'semua') {
        $query->whereIn('pemegang_saham', (array) $request->pemegang_saham);
    }

    if (!empty($request->tahun)) {
        $query->where('tahun', $request->tahun);
    }

    if (!empty($request->bulan)) {
        $query->where('bulan', $request->bulan);
    }

    if (!empty($request->bidang_kegiatan) && $request->bidang_kegiatan !== 'semua') {
        $query->whereIn('bidang_kegiatan', (array) $request->bidang_kegiatan);
    }

    $data = $query->select('bidang_kegiatan', DB::raw('SUM(realisasi_csr) as total'))
        ->groupBy('bidang_kegiatan')
        ->get();

    $labels = $data->pluck('bidang_kegiatan');
    $values = $data->pluck('total');

    return response()->json([
        'labels' => $labels,
        'data' => $values
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

    public function create(Request $request)
    {
        $availableYears = \App\Models\AnggaranCsr::select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');
    
        $pemegangSaham = $request->pemegang_saham ?? null;
        $tahun = $request->tahun ?? now()->year;
        $sisaAnggaran = null;
        $isFallback = false;
    
        if ($pemegangSaham) {
            $anggaran = \App\Models\AnggaranCsr::where('pemegang_saham', $pemegangSaham)
                        ->where('tahun', $tahun)
                        ->first();
    
            if (!$anggaran && $tahun == now()->year) {
                // fallback ke tahun sebelumnya
                $anggaranSebelumnya = \App\Models\AnggaranCsr::where('pemegang_saham', $pemegangSaham)
                    ->where('tahun', $tahun - 1)
                    ->first();
    
                if ($anggaranSebelumnya) {
                    $sisa = $anggaranSebelumnya->hitungSisaAnggaranTotal();
                    if ($sisa > 0) {
                        $sisaAnggaran = $sisa;
                        $isFallback = true;
                    }
                }
            } elseif ($anggaran) {
                $sisaAnggaran = $anggaran->hitungSisaAnggaranTotal();
            }
        }
    
        return view('csr.create', compact('availableYears', 'sisaAnggaran', 'pemegangSaham', 'tahun', 'isFallback'));
    }
    

    public function store(Request $request)
{
    // Bersihkan angka dari titik ribuan
    $request->merge([
        'realisasi_csr' => str_replace('.', '', $request->realisasi_csr),
    ]);

    // Validasi input
    $request->validate([
        'nama_program' => 'required|string|max:255',
        'pemegang_saham' => 'required|string',
        'tahun' => 'required|integer',
        'bulan' => 'required|integer|min:1|max:12',
        'bidang_kegiatan' => 'required|string',
        'realisasi_csr' => 'required|numeric|min:0',
        'ket' => 'nullable|string',
    ], [
        'nama_program.required' => 'Nama program wajib diisi.',
        'nama_program.max' => 'Nama program tidak boleh lebih dari 255 karakter.',
        'pemegang_saham.required' => 'Pemegang saham wajib dipilih.',
        'tahun.required' => 'Tahun wajib diisi.',
        'tahun.integer' => 'Tahun harus berupa angka.',
        'bulan.required' => 'Bulan wajib dipilih.',
        'bulan.integer' => 'Bulan harus berupa angka.',
        'bulan.min' => 'Bulan tidak valid (minimal Januari).',
        'bulan.max' => 'Bulan tidak valid (maksimal Desember).',
        'bidang_kegiatan.required' => 'Bidang kegiatan wajib diisi.',
        'realisasi_csr.required' => 'Realisasi CSR wajib diisi.',
        'realisasi_csr.numeric' => 'Realisasi CSR harus berupa angka.',
        'realisasi_csr.min' => 'Realisasi CSR tidak boleh negatif.',
        'ket.string' => 'Keterangan harus berupa teks.',
    ]);
    

    $pemegangSaham = $request->pemegang_saham;
    $tahun = $request->tahun;
    $realisasiBaru = (float) $request->realisasi_csr;

    // Coba cari anggaran tahun ini
    $anggaran = \App\Models\AnggaranCsr::where('pemegang_saham', $pemegangSaham)
                ->where('tahun', $tahun)
                ->first();

    $tahunFallback = $tahun;
    if (!$anggaran) {
        // Ambil tahun sebelumnya sebagai fallback
        $anggaran = \App\Models\AnggaranCsr::where('pemegang_saham', $pemegangSaham)
                    ->where('tahun', $tahun - 1)
                    ->first();

        $tahunFallback = $tahun - 1;
        if (!$anggaran) {
            return back()->withErrors(['pemegang_saham' => 'Tidak ada anggaran ditemukan, termasuk dari tahun sebelumnya.'])->withInput();
        }
    }

    $jumlahAnggaran = (float) $anggaran->jumlah_anggaran;

    // Hitung total realisasi untuk tahun ini
    $realisasiSebelumnya = \App\Models\Csr::where('pemegang_saham', $pemegangSaham)
                        ->where('tahun', $tahun)
                        ->sum('realisasi_csr');

    $totalRealisasiSetelahInput = $realisasiSebelumnya + $realisasiBaru;
    $sisaSetelahInput = $jumlahAnggaran - $totalRealisasiSetelahInput;

    // Debug sementara (hapus nanti kalau udah oke)
    // dd([
    //     'tahun_anggaran_diambil_dari' => $tahunFallback,
    //     'jumlah_anggaran' => $jumlahAnggaran,
    //     'realisasi_csr_di_tahun_ini' => $realisasiSebelumnya,
    //     'input_realisasi_baru' => $realisasiBaru,
    //     'total_realisasi_setelah_input' => $totalRealisasiSetelahInput,
    //     'sisa_setelah_input' => $sisaSetelahInput,
    // ]);

    if ($totalRealisasiSetelahInput > $jumlahAnggaran) {
        return back()
            ->with('csr_error', 'Realisasi melebihi anggaran yang tersedia (termasuk dari tahun sebelumnya).')
            ->with('request_data', $request->all())
            ->withInput();
    }
    

    // Simpan data
    \App\Models\Csr::create([
        'nama_program' => $request->nama_program,
        'pemegang_saham' => $pemegangSaham,
        'tahun' => $tahun,
        'bulan' => $request->bulan,
        'bidang_kegiatan' => $request->bidang_kegiatan,
        'realisasi_csr' => $realisasiBaru,
        'ket' => $request->ket,
    ]);

    return redirect()->route('dashboard')->with('success', 'Program CSR berhasil ditambahkan.');
}

    
    

    
public function edit(Request $request, \App\Models\Csr $csr)
{
    $availableYears = \App\Models\AnggaranCsr::select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

    $pemegangSaham = $csr->pemegang_saham;
    $tahun = $csr->tahun;
    $sisaAnggaran = null;
    $isFallback = false;

    // Cek apakah anggaran tahun ini ada
    $anggaran = \App\Models\AnggaranCsr::where('pemegang_saham', $pemegangSaham)
                ->where('tahun', $tahun)
                ->first();

    if (!$anggaran && $tahun == now()->year) {
        // fallback ke tahun sebelumnya
        $anggaranSebelumnya = \App\Models\AnggaranCsr::where('pemegang_saham', $pemegangSaham)
            ->where('tahun', $tahun - 1)
            ->first();

        if ($anggaranSebelumnya) {
            $sisa = $anggaranSebelumnya->hitungSisaAnggaranTotal($csr->id); // lewatkan ID untuk pengecualian
            if ($sisa > 0) {
                $sisaAnggaran = $sisa;
                $isFallback = true;
            }
        }
    } elseif ($anggaran) {
        $sisaAnggaran = $anggaran->hitungSisaAnggaranTotal($csr->id); // lewatkan ID untuk pengecualian
    }

    return view('csr.edit', compact('csr', 'availableYears', 'sisaAnggaran', 'pemegangSaham', 'tahun', 'isFallback'));
}


public function update(Request $request, \App\Models\Csr $csr)
{
    // Bersihkan angka dari titik ribuan
    $request->merge([
        'realisasi_csr' => str_replace('.', '', $request->realisasi_csr),
    ]);

    // Validasi input
    $request->validate([
        'nama_program' => 'required|string|max:255',
        'pemegang_saham' => 'required|string',
        'tahun' => 'required|integer',
        'bulan' => 'required|integer|min:1|max:12',
        'bidang_kegiatan' => 'required|string',
        'realisasi_csr' => 'required|numeric|min:0',
        'ket' => 'nullable|string',
    ], [
        'nama_program.required' => 'Nama program wajib diisi.',
        'nama_program.max' => 'Nama program tidak boleh lebih dari 255 karakter.',
        'pemegang_saham.required' => 'Pemegang saham wajib dipilih.',
        'tahun.required' => 'Tahun wajib diisi.',
        'tahun.integer' => 'Tahun harus berupa angka.',
        'bulan.required' => 'Bulan wajib dipilih.',
        'bulan.integer' => 'Bulan harus berupa angka.',
        'bulan.min' => 'Bulan tidak valid (minimal Januari).',
        'bulan.max' => 'Bulan tidak valid (maksimal Desember).',
        'bidang_kegiatan.required' => 'Bidang kegiatan wajib diisi.',
        'realisasi_csr.required' => 'Realisasi CSR wajib diisi.',
        'realisasi_csr.numeric' => 'Realisasi CSR harus berupa angka.',
        'realisasi_csr.min' => 'Realisasi CSR tidak boleh negatif.',
        'ket.string' => 'Keterangan harus berupa teks.',
    ]);

    $pemegangSaham = $request->pemegang_saham;
    $tahun = $request->tahun;
    $realisasiBaru = (float) $request->realisasi_csr;

    // Ambil anggaran tahun ini, atau fallback ke tahun sebelumnya
    $anggaran = \App\Models\AnggaranCsr::where('pemegang_saham', $pemegangSaham)
                ->where('tahun', $tahun)
                ->first();

    $tahunFallback = $tahun;
    if (!$anggaran) {
        $anggaran = \App\Models\AnggaranCsr::where('pemegang_saham', $pemegangSaham)
                    ->where('tahun', $tahun - 1)
                    ->first();
        $tahunFallback = $tahun - 1;

        if (!$anggaran) {
            return back()->withErrors(['pemegang_saham' => 'Tidak ada anggaran ditemukan, termasuk dari tahun sebelumnya.'])->withInput();
        }
    }

    $jumlahAnggaran = (float) $anggaran->jumlah_anggaran;

    // Hitung realisasi tahun ini dikurangi realisasi lama
    $totalRealisasi = \App\Models\Csr::where('pemegang_saham', $pemegangSaham)
                        ->where('tahun', $tahun)
                        ->where('id', '!=', $csr->id)
                        ->sum('realisasi_csr');

    $totalRealisasiSetelahInput = $totalRealisasi + $realisasiBaru;
    $sisaSetelahInput = $jumlahAnggaran - $totalRealisasiSetelahInput;

    if ($totalRealisasiSetelahInput > $jumlahAnggaran) {
        return back()
            ->with('csr_error', 'Realisasi melebihi anggaran yang tersedia (termasuk dari tahun sebelumnya).')
            ->with('request_data', $request->all())
            ->withInput();
    }

    // Update data
    $csr->update([
        'nama_program' => $request->nama_program,
        'pemegang_saham' => $pemegangSaham,
        'tahun' => $tahun,
        'bulan' => $request->bulan,
        'bidang_kegiatan' => $request->bidang_kegiatan,
        'realisasi_csr' => $realisasiBaru,
        'ket' => $request->ket,
    ]);

    return redirect()->route('dashboard')->with('success', 'Program CSR berhasil diperbarui.');
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

    $pemegangSaham = $request->pemegang_saham;
    $tahun = $request->tahun;
    $isFallback = false;

    // Cari anggaran untuk tahun ini
    $anggaran = \App\Models\AnggaranCsr::where('pemegang_saham', $pemegangSaham)
                ->where('tahun', $tahun)
                ->first();

    // Kalau tidak ada, cari tahun sebelumnya (fallback)
    if (!$anggaran) {
        $anggaran = \App\Models\AnggaranCsr::where('pemegang_saham', $pemegangSaham)
                    ->where('tahun', '<', $tahun)
                    ->orderByDesc('tahun')
                    ->first();

        if (!$anggaran) {
            return response()->json([
                'sisa' => 0,
                'message' => 'Tidak ada anggaran untuk data ini.',
                'fallback' => false
            ]);
        }

        $isFallback = true;
    }

    // Hitung realisasi tetap dari tahun yang diminta (bukan tahun fallback)
    $realisasi = \App\Models\Csr::where('pemegang_saham', $pemegangSaham)
                ->where('tahun', $tahun)
                ->sum('realisasi_csr');

    $sisa = $anggaran->jumlah_anggaran - $realisasi;

    return response()->json([
        'sisa' => $sisa,
        'jumlah_anggaran' => $anggaran->jumlah_anggaran,
        'realisasi_csr' => $realisasi,
        'message' => $isFallback 
            ? 'Sisa anggaran diambil dari tahun sebelumnya: ' . $anggaran->tahun
            : 'Sisa anggaran berhasil diambil.',
        'fallback' => $isFallback,
        'tahun_anggaran' => $anggaran->tahun
    ]);
}



public function riwayatCsr(Request $request)
{
    $tahun = $request->input('tahun', date('Y'));
    $pemegangSaham = $request->input('pemegang_saham', 'semua');

    $semuaPemegangSaham = \App\Models\AnggaranCsr::select('pemegang_saham')->distinct()->pluck('pemegang_saham');
    $riwayatPerSaham = $this->ambilRiwayatCsr($tahun, $pemegangSaham);

    $daftarTahun = \App\Models\AnggaranCsr::select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

    return view('csr.riwayat', compact('riwayatPerSaham', 'tahun', 'pemegangSaham', 'semuaPemegangSaham', 'daftarTahun'));
}

public function riwayatCsrAjax(Request $request)
{
    $tahun = $request->input('tahun', date('Y'));
    $pemegangSaham = $request->input('pemegang_saham', 'semua');

    $riwayatPerSaham = $this->ambilRiwayatCsr($tahun, $pemegangSaham);

    return view('csr.partials.riwayat-list', compact('riwayatPerSaham'))->render();
}

protected function ambilRiwayatCsr($tahun, $pemegangSaham)
{
    // === MODE: SEMUA PEMEGANG SAHAM ===
    if ($pemegangSaham === 'semua') {
        $dataTahunIni = \App\Models\AnggaranCsr::with('penambahan')
            ->where('tahun', $tahun)
            ->get();

        $semuaSaham = \App\Models\AnggaranCsr::select('pemegang_saham')->distinct()->pluck('pemegang_saham')->toArray();
        $sudahAda = $dataTahunIni->pluck('pemegang_saham')->toArray();
        $belumAda = array_diff($semuaSaham, $sudahAda);

        $fallbacks = collect();
        if (!empty($belumAda)) {
            $fallbacks = \App\Models\AnggaranCsr::with('penambahan')
                ->where('tahun', $tahun - 1)
                ->whereIn('pemegang_saham', $belumAda)
                ->get()
                ->filter(fn($item) => $item->hitungSisaAnggaranTotal() > 0)
                ->map(function ($item) use ($tahun) {
                    $clone = clone $item;
                    $clone->tahun = $tahun;
                    $clone->jumlah_anggaran = $item->hitungSisaAnggaranTotal();
                    $clone->is_fallback = true;
                    return $clone;
                });
        }

        $gabungan = $dataTahunIni->map(function ($item) {
            $item->is_fallback = false;
            return $item;
        })->concat($fallbacks);

        // Fallback penuh kalau kosong semua
        if ($gabungan->isEmpty()) {
            $gabungan = \App\Models\AnggaranCsr::with('penambahan')
                ->where('tahun', $tahun - 1)
                ->get()
                ->filter(fn($item) => $item->hitungSisaAnggaranTotal() > 0)
                ->map(function ($item) use ($tahun) {
                    $clone = clone $item;
                    $clone->tahun = $tahun;
                    $clone->jumlah_anggaran = $item->hitungSisaAnggaranTotal();
                    $clone->is_fallback = true;
                    return $clone;
                });
        }

        // === Proses Agregasi Data Gabungan ===
        $totalSisaTahunLalu = 0;
        $totalPenambahan = 0;
        $totalRealisasiBulanan = array_fill(1, 12, 0);

        foreach ($gabungan as $item) {
            $detail = $item->getDetailRiwayatCsr();

            // Jika fallback dan tidak punya record penambahan, masukkan ke sisa tahun lalu
            if ($item->is_fallback && !$item->penambahan) {
                $totalSisaTahunLalu += $item->jumlah_anggaran;
            } else {
                $totalSisaTahunLalu += $detail['sisa_tahun_lalu'];
                $totalPenambahan += $detail['penambahan_tahun_ini'];
            }

            foreach ($detail['bulan_realisasi'] as $bulan => $data) {
                $totalRealisasiBulanan[$bulan] += $data['realisasi'];
            }
        }

        $totalRealisasi = array_sum($totalRealisasiBulanan);
        $sisaAkhirTahun = max(($totalSisaTahunLalu + $totalPenambahan) - $totalRealisasi, 0);

        $dataBulan = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $dataBulan[$bulan] = [
                'bulan' => $bulan,
                'realisasi' => $totalRealisasiBulanan[$bulan],
            ];
        }

        return [[
            'pemegang_saham' => 'Semua Pemegang Saham',
            'detail' => [
                'sisa_tahun_lalu' => $totalSisaTahunLalu,
                'penambahan_tahun_ini' => $totalPenambahan,
                'bulan_realisasi' => $dataBulan,
                'sisa_akhir_tahun' => $sisaAkhirTahun,
            ]
        ]];
    }

    // === MODE: PEMEGANG SAHAM SPESIFIK ===
    $anggarans = \App\Models\AnggaranCsr::with('penambahan')
        ->where('tahun', $tahun)
        ->where('pemegang_saham', $pemegangSaham)
        ->get();

    if ($anggarans->isEmpty()) {
        $anggarans = \App\Models\AnggaranCsr::with('penambahan')
            ->where('tahun', $tahun - 1)
            ->where('pemegang_saham', $pemegangSaham)
            ->get()
            ->filter(fn($item) => $item->hitungSisaAnggaranTotal() > 0)
            ->map(function ($item) use ($tahun) {
                $clone = clone $item;
                $clone->tahun = $tahun;
                $clone->jumlah_anggaran = $item->hitungSisaAnggaranTotal();
                return $clone;
            });
    }

    $riwayatPerSaham = [];
    foreach ($anggarans as $anggaran) {
        $riwayatPerSaham[] = [
            'pemegang_saham' => $anggaran->pemegang_saham,
            'detail' => $anggaran->getDetailRiwayatCsr()
        ];
    }

    return $riwayatPerSaham;
}








}

