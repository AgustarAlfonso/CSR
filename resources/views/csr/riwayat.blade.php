@extends('layouts.master')

@section('title', 'Data Anggaran CSR ')

@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">
    <h1 class="text-2xl font-bold mb-6">Riwayat CSR</h1>

    <form method="GET" class="mb-8 p-4 bg-white rounded-xl shadow-md flex flex-wrap items-end gap-6">
        <div class="w-full sm:w-auto">
            <label class="block text-sm font-semibold text-gray-600 mb-1">Tahun</label>
            <select name="tahun" class="w-full sm:w-36 rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @foreach($daftarTahun as $th)
                    <option value="{{ $th }}" {{ $tahun == $th ? 'selected' : '' }}>{{ $th }}</option>
                @endforeach
            </select>
        </div>
    
        <div class="w-full sm:w-auto">
            <label class="block text-sm font-semibold text-gray-600 mb-1">Pemegang Saham</label>
            <select name="pemegang_saham" class="w-full sm:w-64 rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="semua" {{ $pemegangSaham == 'semua' || !$pemegangSaham ? 'selected' : '' }}>Semua Pemegang Saham</option>
                @foreach($semuaPemegangSaham as $ps)
                    <option value="{{ $ps }}" {{ $pemegangSaham == $ps ? 'selected' : '' }}>{{ $ps }}</option>
                @endforeach
            </select>
        </div>
    
        <div class="w-full sm:w-auto">
            <button type="submit" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg transition duration-150 shadow-sm">
                Filter
            </button>
        </div>
    </form>
    

    @forelse($riwayatPerSaham as $data)
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">{{ $data['pemegang_saham'] }}</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($data['riwayat'] as $bulan => $item)
                <div class="bg-white rounded-2xl shadow-md border hover:shadow-lg transition-all duration-200 p-5">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">
                        {{ \Carbon\Carbon::create()->month($bulan)->locale('id')->translatedFormat('F') }}
                    </h3>

                    <div class="space-y-1 text-sm">
                        @if($bulan == 1)
                            <p class="text-gray-500">Sisa Tahun Lalu: 
                                <span class="font-medium text-gray-800">Rp{{ number_format($item['sisa_anggaran_awal'], 0, ',', '.') }}</span>
                            </p>
                        @else
                            <p class="text-gray-500">Sisa Awal:
                                <span class="font-medium text-gray-800">Rp{{ number_format($item['sisa_anggaran_awal'], 0, ',', '.') }}</span>
                            </p>
                        @endif

                        @if($item['penambahan_anggaran'] > 0)
                            <p class="text-green-600">+ Penambahan Anggaran:
                                <span class="font-medium">Rp{{ number_format($item['penambahan_anggaran'], 0, ',', '.') }}</span>
                            </p>
                        @endif

                        <p class="text-red-600">- Realisasi:
                            <span class="font-medium">Rp{{ number_format($item['realisasi'], 0, ',', '.') }}</span>
                        </p>
                    </div>

                    <hr class="my-3">

                    <p class="text-sm font-semibold text-gray-600">
                        Sisa Akhir Bulan: 
                        <span class="text-blue-600">Rp{{ number_format($item['sisa_anggaran_akhir'], 0, ',', '.') }}</span>
                    </p>
                </div>
            @endforeach
        </div>
    </div>
@empty
    <div class="text-center text-gray-500 py-20">
        <p class="text-lg">Data tidak ditemukan untuk filter yang dipilih.</p>
    </div>
@endforelse

</div>
@endsection
