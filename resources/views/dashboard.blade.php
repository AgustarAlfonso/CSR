@extends('layouts.master')

@section('title', 'Dashboard CSR')

@section('content')
<div class="row">
    <!-- Sidebar Filter -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5>Filter CSR</h5>
            </div>
            <div class="card-body">
                <div x-data="dropdownData()" class="relative mb-3" >
                    <label for="pemegang_saham" class="form-label">Pemegang Saham</label>
                    <button @click="open = !open"
                            class="form-select w-full text-left"
                            type="button">
                        <span x-text="selectedText || 'Semua'"></span>
                    </button>
                    
                    <div x-show="open" @click.away="open = false"
                    class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded shadow-md max-h-60 overflow-y-auto">
                   <ul class="text-sm">
                       <!-- Semua -->
                       <li @click="select(null, 'Semua')"
                           class="px-4 py-2 hover:bg-blue-50 hover:text-blue-700 cursor-pointer border-b">
                           Semua
                       </li>
               
                       <!-- Provinsi dan kabupaten/kota -->
                       <template x-for="(cities, province) in options" :key="province">
                           <li class="border-b">
                               <!-- Provinsi line -->
                               <div class="flex items-center hover:bg-gray-100 cursor-pointer px-4 py-2">
                                   <!-- Expand toggle area -->
                                   <div class="w-4 h-4 flex items-center justify-center text-gray-600 mr-2"
                                        @click.stop="toggleProvince(province)">
                                       <span x-text="openedProvince === province ? '−' : '+'"></span>
                                   </div>
                                   <!-- Select provinsi (klik nama) -->
                                   <span @click.stop="select(province, province)" class="flex-1" x-text="province"></span>
                               </div>
               
                               <!-- Sub kota/kabupaten -->
                               <ul x-show="openedProvince === province" class="pl-10 bg-gray-50">
                                   <template x-for="city in cities" :key="city">
                                       <li @click="select(city, city)"
                                           class="px-4 py-2 hover:bg-blue-100 hover:text-blue-700 cursor-pointer">
                                           <span x-text="city"></span>
                                       </li>
                                   </template>
                               </ul>
                           </li>
                       </template>
                   </ul>
               </div>
               
                
                    <input type="hidden" name="pemegang_saham" :value="selectedValue" id="pemegang_saham">
                </div>
                

                <div class="mb-3">
                    <label for="bidang_kegiatan" class="form-label">Bidang Kegiatan</label>
                    <select id="bidang_kegiatan" class="form-select">
                        <option value="">Semua</option>
                        @foreach($bidang_kegiatan as $kegiatan)
                            <option value="{{ $kegiatan }}">{{ $kegiatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="tahun" class="form-label">Tahun</label>
                    <select id="tahun" class="form-select">
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                

                @php
                $namaBulan = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember',
                ];
            @endphp
            
            <div class="mb-3">
                <label for="bulan" class="form-label">Bulan</label>
                <select id="bulan" class="form-select">
                    <option value="">Semua</option>
                    @foreach(collect($months)->sort()->values() as $month)
                    <option value="{{ $month }}">{{ $namaBulan[$month] }}</option>
                @endforeach
                </select>
            </div>

                <div class="d-grid">
                    <a href="#" id="lihat_selengkapnya" class="btn btn-primary">Lihat Selengkapnya</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Peta Interaktif -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h5>Peta Interaktif</h5>
            </div>
            <div class="card-body">
                <div id="map" style="height: 400px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Row -->
<div class="row mt-3">
    <div class="col-md-6">
        <x-pie-chart />
    </div>
    <div class="col-md-6">
        <x-bar-chart />
    </div>
</div>
@endsection

@push('scripts')


<script>
        var map;
        var geojsonLayer;
        var geojsonFiles = {
            'Kab. Kepulauan Anambas': 'anambas.geojson',
            'Kab. Indragiri Hulu': 'indragiri_hulu.geojson',
            'Kota Batam': 'batam.geojson',
            'Kab. Indragiri Hilir': 'indragiri_hilir.geojson',
            'Provinsi Riau': 'riau.geojson',
            'Kab. Kampar': 'kampar.geojson',
            'Kab. Bintan': 'bintan.geojson',
            'Kab. Bengkalis': 'bengkalis.geojson',
            'Kab. Rokan Hilir': 'rokan_hilir.geojson',
            'Kab. Meranti': 'meranti.geojson',
            'Kab. Natuna': 'natuna.geojson',
            'Kab. Siak': 'siak.geojson',
            'Kab. Pelalawan': 'pelalawan.geojson',
            'Kota Dumai': 'dumai.geojson',
            'Kota Pekanbaru': 'pekanbaru.geojson',
            'Provinsi Kepulauan Riau': 'kepulauan_riau.geojson',
            'Kab. Rokan Hulu': 'rokan_hulu.geojson',
            'Kab. Lingga': 'lingga.geojson',
            'Kab. Karimun': 'karimun.geojson',
            'Kota Tanjung Pinang': 'tanjung_pinang.geojson',
            'Kab. Kuansing': 'kuantan_singingi.geojson'
        };

        $(document).ready(function () {
        $('#lihat_selengkapnya').click(function (e) {
            e.preventDefault();
            let params = new URLSearchParams({
                pemegang_saham: $('#pemegang_saham').val(),
                bidang_kegiatan: $('#bidang_kegiatan').val(),
                tahun: $('#tahun').val(),
                bulan: $('#bulan').val()
            }).toString();

            window.location.href = '/hasil-filter?' + params;
        });

        $('select').change(function () {
            applyFilter();
            loadGeoJSON($('#pemegang_saham').val());
        });


    });

    function applyFilter() {
            $.ajax({
                url: '{{ route("csr.filter") }}',
                type: 'POST',
                data: {
                    pemegang_saham: $('#pemegang_saham').val(),
                    bidang_kegiatan: $('#bidang_kegiatan').val(),
                    tahun: $('#tahun').val(),
                    bulan: $('#bulan').val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    console.log('Data filter berhasil:', response);
                    // Bisa tambahkan logika untuk update grafik/chart di sini
                },
                error: function (xhr) {
                    console.error('Gagal ambil data filter:', xhr.responseText);
                }
            });
        }

    function loadGeoJSON(region) {
            geojsonLayer.clearLayers();
            let file = region ? geojsonFiles[region] : 'semua.geojson';

            fetch('/geojson/' + file)
        .then(response => response.json())
        .then(data => {
            var layer = L.geoJSON(data, {
                style: {
                    color: "#ff7800",
                    weight: 2,
                    fillColor: "#ffcc00",
                    fillOpacity: 0.5
                },
                onEachFeature: function (feature, layer) {
                    layer.on("mouseover", function () {
                        this.setStyle({ fillColor: "#ff4500", fillOpacity: 0.7 });
                    });
                    layer.on("mouseout", function () {
                        this.setStyle({ fillColor: "#ffcc00", fillOpacity: 0.5 });
                    });
                    layer.on("click", function () {
                        $.ajax({
                            url: '{{ route("csr.filter") }}',
                            type: 'POST',
                            data: {
                                pemegang_saham: region,
                                bidang_kegiatan: $('#bidang_kegiatan').val(),
                                tahun: $('#tahun').val(),
                                bulan: $('#bulan').val(),
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                function formatRupiah(angka) {
                                    return new Intl.NumberFormat('id-ID', {
                                        style: 'decimal',
                                        maximumFractionDigits: 0
                                    }).format(angka);
                                }
                                let label = region ? region : 'Semua Wilayah';

                                let info = `
                                    <b>${label}</b><br>
                                    Jumlah Anggaran: Rp ${formatRupiah(response.jumlah_anggaran)}<br>
                                    Realisasi CSR: Rp ${formatRupiah(response.realisasi_csr)}<br>
                                    Sisa CSR: Rp ${formatRupiah(response.sisa_csr)}
                                `;

                                layer.bindPopup(info).openPopup();
                            },
                            error: function () {
                                layer.bindPopup(`<b>${region}</b><br>Data tidak tersedia`).openPopup();
                            }
                        });
                    });
                }
            }).addTo(geojsonLayer);

            map.fitBounds(layer.getBounds());
        })
        .catch(error => console.error("Error loading GeoJSON:", error));
}
    window.applyFilter = applyFilter;
    window.loadGeoJSON = loadGeoJSON;

    function dropdownData() {
        return {
            open: false,
            openedProvince: null,
            selectedValue: '',
            selectedText: '',
            options: {
'Provinsi Riau': [
    'Kab. Bengkalis',
    'Kab. Indragiri Hilir',
    'Kab. Indragiri Hulu',
    'Kab. Kampar',
    'Kab. Kepulauan Meranti',
    'Kab. Kuantan Singingi',
    'Kab. Pelalawan',
    'Kab. Rokan Hilir',
    'Kab. Rokan Hulu',
    'Kab. Siak',
    'Kota Dumai',
    'Kota Pekanbaru'
],
'Provinsi Kepulauan Riau': [
    'Kab. Bintan',
    'Kab. Karimun',
    'Kab. Kepulauan Anambas',
    'Kab. Lingga',
    'Kab. Natuna',
    'Kota Batam',
    'Kota Tanjung Pinang'
]
            },
            toggleProvince(province) {
                this.openedProvince = this.openedProvince === province ? null : province;
            },
            select(value, text) {
    this.selectedValue = value || '';
    this.selectedText = text;
    this.open = false;

    const pemegangSelect = document.getElementById('pemegang_saham');
    if (pemegangSelect) {
        pemegangSelect.value = value;
    }

    if (typeof window.applyFilter === 'function') {
        window.applyFilter();
    }

    if (typeof window.loadGeoJSON === 'function') {
        window.loadGeoJSON(this.selectedValue);
    }

    // Ini satu kali panggil, semua update
    if (typeof window.updateAllCharts === 'function') {
        window.updateAllCharts();
    }
}

                    };
                }

                function updateAllCharts() {
    if (typeof window.fetchFilteredData === 'function') {
        window.fetchFilteredData();
    }

    if (window.currentChartType === "bidang_kegiatan") {
        if (typeof window.loadBarChartByBidangKegiatan === 'function') {
            window.loadBarChartByBidangKegiatan();
        }
    } else {
        if (typeof window.loadBarChartFromAPI === 'function') {
            window.loadBarChartFromAPI();
        }
    }
}


    document.addEventListener("DOMContentLoaded", function () {
        map = L.map('map').setView([0.5, 102.0], 7);
        geojsonLayer = L.layerGroup().addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);


      


        loadGeoJSON(null);
    });
</script>
@endpush
