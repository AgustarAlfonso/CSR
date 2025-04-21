@forelse($riwayatPerSaham as $data)
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">{{ $data['pemegang_saham'] }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
    
            {{-- Card: Sisa Tahun Lalu --}}
            <div class="bg-white rounded-xl shadow-md border p-4 hover:shadow-lg transition">
                <h3 class="text-base font-semibold text-gray-700 mb-2">Sisa Tahun Lalu</h3>
                <p class="text-blue-600 text-lg font-bold">
                    Rp{{ number_format($data['detail']['sisa_tahun_lalu'], 0, ',', '.') }}
                </p>
            </div>
        
            {{-- Card: Penambahan Tahun Ini --}}
            <div class="bg-white rounded-xl shadow-md border p-4 hover:shadow-lg transition">
                <h3 class="text-base font-semibold text-gray-700 mb-2">Penambahan Tahun Ini</h3>
                <p class="text-green-600 text-lg font-bold">
                    Rp{{ number_format($data['detail']['penambahan_tahun_ini'], 0, ',', '.') }}
                </p>
            </div>
        
            {{-- Card: Total Realisasi Tahun Ini --}}
            <div class="bg-white rounded-xl shadow-md border p-4 hover:shadow-lg transition">
                <h3 class="text-base font-semibold text-gray-700 mb-2">Total Realisasi Tahun Ini</h3>
                <p class="text-red-600 text-lg font-bold">
                    Rp{{ number_format($data['detail']['total_realisasi_tahun_ini'], 0, ',', '.') }}
                </p>
            </div>
        
            {{-- Card: Sisa Akhir Tahun --}}
            <div class="bg-white rounded-xl shadow-md border p-4 hover:shadow-lg transition">
                <h3 class="text-base font-semibold text-gray-700 mb-2">Sisa Akhir Tahun</h3>
                <p class="text-purple-600 text-lg font-bold">
                    Rp{{ number_format($data['detail']['sisa_akhir_tahun'], 0, ',', '.') }}
                </p>
            </div>
        </div>
        

        {{-- Card: Realisasi per Bulan --}}
        <div class="mt-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @php
    $pemegangSahamQuery = $data['pemegang_saham'] === 'Semua Pemegang Saham' ? '' : urlencode($data['pemegang_saham']);
@endphp

@foreach($data['detail']['bulan_realisasi'] as $bulan => $item)
    @if($item['realisasi'] > 0)
        <a href="{{ url('/hasil-filter') }}?pemegang_saham={{ $pemegangSahamQuery }}&tahun={{ request('tahun') }}&bulan={{ $bulan }}&bidang_kegiatan={{ request('bidang_kegiatan') }}" class="block bg-white rounded-xl shadow-sm border p-4 hover:shadow-md transition">
            <h4 class="text-sm font-semibold text-gray-600 mb-1">
                {{ \Carbon\Carbon::create()->month($bulan)->locale('id')->translatedFormat('F') }}
            </h4>
            <p class="text-red-600 font-semibold">
                - Rp{{ number_format($item['realisasi'], 0, ',', '.') }}
            </p>
        </a>
    @else
        <div class="bg-white rounded-xl shadow-sm border p-4 opacity-60">
            <h4 class="text-sm font-semibold text-gray-600 mb-1">
                {{ \Carbon\Carbon::create()->month($bulan)->locale('id')->translatedFormat('F') }}
            </h4>
            <p class="text-gray-500 font-semibold">
                - Rp0
            </p>
        </div>
    @endif
@endforeach

        </div>
    </div>
@empty
    <div class="text-center text-gray-500 py-20">
        <p class="text-lg">Data tidak ditemukan untuk filter yang dipilih.</p>
    </div>
@endforelse
