@forelse($riwayatPerSaham as $data)
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">{{ $data['pemegang_saham'] }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            
            {{-- Card: Sisa Tahun Lalu --}}
            <div class="bg-white rounded-2xl shadow-md border p-5 hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Sisa Tahun Lalu</h3>
                <p class="text-blue-600 text-xl font-bold">
                    Rp{{ number_format($data['detail']['sisa_tahun_lalu'], 0, ',', '.') }}
                </p>
            </div>

            {{-- Card: Penambahan Tahun Ini --}}
            <div class="bg-white rounded-2xl shadow-md border p-5 hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Penambahan Tahun Ini</h3>
                <p class="text-green-600 text-xl font-bold">
                    Rp{{ number_format($data['detail']['penambahan_tahun_ini'], 0, ',', '.') }}
                </p>
            </div>

            {{-- Card: Sisa Akhir Tahun --}}
            <div class="bg-white rounded-2xl shadow-md border p-5 hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Sisa Akhir Tahun</h3>
                <p class="text-purple-600 text-xl font-bold">
                    Rp{{ number_format($data['detail']['sisa_akhir_tahun'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Card: Realisasi per Bulan --}}
        <div class="mt-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($data['detail']['bulan_realisasi'] as $bulan => $item)
                <div class="bg-white rounded-xl shadow-sm border p-4 hover:shadow-md transition">
                    <h4 class="text-sm font-semibold text-gray-600 mb-1">
                        {{ \Carbon\Carbon::create()->month($bulan)->locale('id')->translatedFormat('F') }}
                    </h4>
                    <p class="text-red-600 font-semibold">
                        - Rp{{ number_format($item['realisasi'], 0, ',', '.') }}
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
