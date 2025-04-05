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
                <div class="mb-3">
                    <label for="pemegang_saham" class="form-label">Pemegang Saham</label>
                    <select id="pemegang_saham" class="form-select">
                        <option value="">Semua</option>
                        @foreach($pemegang_saham as $saham)
                            <option value="{{ $saham }}">{{ $saham }}</option>
                        @endforeach
                    </select>
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
                        <option value="">Semua</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
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
    });

    document.addEventListener("DOMContentLoaded", function () {
        var map = L.map('map').setView([0.5, 102.0], 7);
        var geojsonLayer = L.layerGroup().addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

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

                                        let info = `
                                            <b>${region}</b><br>
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

        loadGeoJSON(null);
    });
</script>
@endpush
