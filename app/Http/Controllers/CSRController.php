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
    
        // Ambil semua pemegang saham terkait filter
        $pemegangSahamFilter = !empty($request->pemegang_saham) && $request->pemegang_saham !== 'semua'
            ? (array) $request->pemegang_saham
            : AnggaranCsr::select('pemegang_saham')->distinct()->pluck('pemegang_saham')->toArray();
    
        // Cari pemegang saham yang belum punya data di tahunFilter
        $sahamYangSudahAda = $anggaranList->pluck('pemegang_saham')->toArray();
        $sahamYangBelumAda = array_diff($pemegangSahamFilter, $sahamYangSudahAda);
    
        // Tambahkan data fallback dari tahun sebelumnya jika belum ada
        if (!empty($sahamYangBelumAda)) {
            $tahunSebelumnya = $tahunFilter - 1;
            $fallbacks = AnggaranCsr::whereIn('pemegang_saham', $sahamYangBelumAda)
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
    
            $anggaranList = $anggaranList->concat($fallbacks);
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
    
        $totalAnggaran = $anggaranList->sum(function ($item) {
            return $item->getTotalAnggaranTampilan();
        });
    
        $totalRealisasi = array_sum(array_column($result, 'realisasi'));
    
        if (!empty($request->bulan)) {
            $sisaCsr = array_sum(array_column($result, 'sisa_anggaran'));
        } else {
            $sisaCsr = $anggaranList->sum(function ($item) {
                return $item->getSisaAnggaranTampilan();
            });
        }

        if (!empty($request->bulan)) {
            $bulan = (int) $request->bulan;
        
            $totalAnggaran = array_sum(array_map(function ($item) use ($bulan) {
                $perBulan = $item->getSisaAnggaranPerBulan();
                return $perBulan[$bulan - 1]['sisa_anggaran'] ?? $item->getTotalAnggaranTampilan();
            }, $anggaranList->all()));
        
            $sisaCsr = array_sum(array_column($result, 'sisa_anggaran'));
        } else {
            $totalAnggaran = $anggaranList->sum(function ($item) {
                return $item->getTotalAnggaranTampilan();
            });
        
            $sisaCsr = $anggaranList->sum(function ($item) {
                return $item->getSisaAnggaranTampilan();
            });
        }
        
    
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
        $availableYears = \App\Models\AnggaranCsr::select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun')->toArray();

        $currentYear = now()->year;
        
        if (!in_array($currentYear, $availableYears)) {
            $availableYears[] = $currentYear;
            rsort($availableYears); // biar tetap urut dari terbesar
        }    
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

    $sisaAnggaran = $anggaran->getSisaAnggaranTampilan();

    if ($realisasiBaru > $sisaAnggaran) {
        return back()
            ->with('csr_error', 'Realisasi melebihi sisa anggaran yang tersedia (termasuk fallback jika ada).')
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

    return redirect()->route('csr.hasil_filter', [
        'pemegang_saham' => $pemegangSaham,
        'tahun' => $tahun,
    ])->with('success', 'Program CSR berhasil ditambahkan dan difilter berdasarkan input.');
}

    
    

    
public function edit(Request $request, \App\Models\Csr $csr)
{
    $availableYears = \App\Models\AnggaranCsr::select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun')->toArray();

    $currentYear = now()->year;
    
    if (!in_array($currentYear, $availableYears)) {
        $availableYears[] = $currentYear;
        rsort($availableYears); // biar tetap urut dari terbesar
    }
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

    $sisaAnggaran = $anggaran->getSisaAnggaranTampilan();

    // Tambahkan kembali realisasi lama, lalu bandingkan dengan yang baru
    $sisaAnggaran += $csr->realisasi_csr;

    if ($realisasiBaru > $sisaAnggaran) {
        return back()
            ->with('csr_error', 'Realisasi melebihi sisa anggaran yang tersedia (termasuk fallback jika ada).')
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

    return redirect()->route('csr.hasil_filter', [
        'pemegang_saham' => $pemegangSaham,
        'tahun' => $tahun,
    ])->with('success', 'Program CSR berhasil ditambahkan dan difilter berdasarkan input.');
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

    $anggaran = \App\Models\AnggaranCsr::where('pemegang_saham', $pemegangSaham)
                ->where('tahun', $tahun)
                ->first();

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

    // Hanya set tahun jika BUKAN fallback
    if (!$isFallback) {
        $anggaran->tahun = $tahun;
    }

    return response()->json([
        'sisa' => $anggaran->getSisaAnggaranTampilan(),
        'jumlah_anggaran' => $anggaran->jumlah_anggaran,
        'realisasi_csr' => \App\Models\Csr::where('pemegang_saham', $pemegangSaham)->where('tahun', $isFallback ? $anggaran->tahun : $tahun)->sum('realisasi_csr'),
        'message' => $isFallback 
            ? 'Sisa anggaran diambil dari tahun sebelumnya: ' . $anggaran->tahun
            : 'Sisa anggaran berhasil diambil.',
        'fallback' => $isFallback,
        'tahun_anggaran' => $anggaran->tahun
    ]);
}





public function riwayatCsr(Request $request)
{
    // Ambil daftar tahun yang tersedia di database, urut dari yang terbaru
    $daftarTahun = \App\Models\AnggaranCsr::select('tahun')
    ->distinct()
    ->orderByDesc('tahun')
    ->pluck('tahun');

// Tambahkan tahun sekarang kalau belum ada
$tahunSekarang = date('Y');
if (!$daftarTahun->contains($tahunSekarang)) {
    $daftarTahun->prepend($tahunSekarang);
}
    // Ambil tahun dari request, jika tidak ada gunakan tahun terbaru yang tersedia
    $tahun = $request->input('tahun', $daftarTahun->first());

    // Ambil pemegang saham dari request, default ke "semua"
    $pemegangSaham = $request->input('pemegang_saham', 'semua');

    // Urutan prioritas saham untuk penyortiran
    $urutanSaham = [
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
        'Kota Tanjung Pinang'
    ];

    // Ambil daftar pemegang saham unik dan urutkan sesuai prioritas
    $semuaPemegangSaham = \App\Models\AnggaranCsr::select('pemegang_saham')
        ->distinct()
        ->pluck('pemegang_saham')
        ->filter() // buang null/null string
        ->unique()
        ->sortBy(function ($item) use ($urutanSaham) {
            return array_search($item, $urutanSaham) !== false ? array_search($item, $urutanSaham) : PHP_INT_MAX;
        })
        ->values();

    // Ambil data riwayat CSR
    $riwayatPerSaham = $this->ambilRiwayatCsr($tahun, $pemegangSaham);

    return view('csr.riwayat', compact(
        'riwayatPerSaham',
        'tahun',
        'pemegangSaham',
        'semuaPemegangSaham',
        'daftarTahun'
    ));
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
    if ($pemegangSaham === 'semua') {
        $dataTahunIni = AnggaranCsr::where('tahun', $tahun)->get();

        $semuaSaham = AnggaranCsr::select('pemegang_saham')->distinct()->pluck('pemegang_saham')->toArray();
        $sudahAda = $dataTahunIni->pluck('pemegang_saham')->toArray();
        $belumAda = array_diff($semuaSaham, $sudahAda);

        $fallbackCollection = collect();

        if (!empty($belumAda)) {
            $tahunSebelumnya = $tahun - 1;

            $fallbacks = AnggaranCsr::where('tahun', $tahunSebelumnya)
                ->whereIn('pemegang_saham', $belumAda)
                ->get()
                ->filter(fn($item) => $item->getSisaAnggaranTampilan() > 0)
                ->map(function ($item) use ($tahun) {
                    $sisa = $item->getSisaAnggaranTampilan();
                    $clone = clone $item;
                    $clone->tahun = $tahun;
                    $clone->jumlah_anggaran = $sisa;
                    $clone->sisa_dari_tahun_lalu = true;
                    return $clone;
                });

            $fallbackCollection = $fallbacks;
        }

        // Fallback total kalau data kosong semua
        $gabungan = $dataTahunIni->concat($fallbackCollection);

        if ($gabungan->isEmpty()) {
            $semuaSaham = AnggaranCsr::select('pemegang_saham')->where('tahun', $tahun - 1)->distinct()->pluck('pemegang_saham')->toArray();

            $gabungan = AnggaranCsr::where('tahun', $tahun - 1)
                ->whereIn('pemegang_saham', $semuaSaham)
                ->get()
                ->filter(fn($item) => $item->getSisaAnggaranTampilan() > 0)
                ->map(function ($item) use ($tahun) {
                    $sisa = $item->getSisaAnggaranTampilan();
                    $clone = clone $item;
                    $clone->tahun = $tahun;
                    $clone->jumlah_anggaran = $sisa;
                    $clone->sisa_dari_tahun_lalu = true;
                    return $clone;
                });
        }

        // Akumulasi semua data
        $totalSisaTahunLalu = 0;
        $totalPenambahan = 0;
        $totalRealisasiBulanan = array_fill(1, 12, 0);

        foreach ($gabungan as $item) {
            $detail = $item->getDetailRiwayatCsr();
            $totalSisaTahunLalu += $detail['sisa_tahun_lalu'];
            $totalPenambahan += $detail['penambahan_tahun_ini'];
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
                'total_realisasi_tahun_ini' => $totalRealisasi,
                'bulan_realisasi' => $dataBulan,
                'sisa_akhir_tahun' => $sisaAkhirTahun,
            ]
        ]];
    }

    // Mode pemegang saham spesifik
    $anggarans = AnggaranCsr::where('tahun', $tahun)
        ->where('pemegang_saham', $pemegangSaham)
        ->get();

    if ($anggarans->isEmpty()) {
        $anggarans = AnggaranCsr::where('tahun', $tahun - 1)
            ->where('pemegang_saham', $pemegangSaham)
            ->get()
            ->filter(fn($item) => $item->getSisaAnggaranTampilan() > 0)
            ->map(function ($item) use ($tahun) {
                $sisa = $item->getSisaAnggaranTampilan();
                $clone = clone $item;
                $clone->tahun = $tahun;
                $clone->jumlah_anggaran = $sisa;
                $clone->sisa_dari_tahun_lalu = true;
                return $clone;
            });
    }

    $riwayatPerSaham = [];
    foreach ($anggarans as $anggaran) {
        $riwayatPerSaham[] = [
            'pemegang_saham' => $anggaran->pemegang_saham,
            'detail' => $anggaran->getDetailRiwayatCsr(),
        ];
    }

    return $riwayatPerSaham;
}











}

