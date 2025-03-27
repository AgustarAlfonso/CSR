<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard CSR</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Filter -->
            <div class="col-md-3">
                <div class="card mt-3">
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
    
                        <div class="mb-3">
                            <label for="bulan" class="form-label">Bulan</label>
                            <select id="bulan" class="form-select">
                                <option value="">Semua</option>
                                @foreach($months as $month)
                                    <option value="{{ $month }}">{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
    
            <!-- Peta Interaktif -->
            <div class="col-md-9">
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Peta Interaktif</h5>
                    </div>
                    <div class="card-body">
                        <div id="map" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Pie Chart & Bar Chart -->
        <div class="row mt-3">
            <div class="col-md-6">
                <x-pie-chart />
            </div>
            <div class="col-md-6">
                <x-bar-chart />
            </div>
        </div>
    </div>
    

    <script>
        $(document).ready(function() {
            function applyFilter() {
                let data = {
                    pemegang_saham: $('#pemegang_saham').val(),
                    bidang_kegiatan: $('#bidang_kegiatan').val(),
                    tahun: $('#tahun').val(),
                    bulan: $('#bulan').val(),
                };

                $.ajax({
                    url: '{{ route("csr.filter") }}',
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log(response);
                        // Tambahkan kode untuk memperbarui grafik jika diperlukan
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }

            $('select').change(applyFilter);
        });
    </script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var map = L.map('map').setView([0.5, 102.0], 7); // Pusat peta ke Riau
        var geojsonLayer = L.layerGroup().addTo(map);

        // Tambahkan peta dasar
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Mapping ENUM dengan file GeoJSON
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
            geojsonLayer.clearLayers(); // Hapus layer sebelum memuat yang baru

            if (!geojsonFiles[region]) return;

            fetch('/geojson/' + geojsonFiles[region])
                .then(response => response.json())
                .then(data => {
                    var layer = L.geoJSON(data, {
                        style: function (feature) {
                            return {
                                color: "#ff7800", // Warna batas
                                weight: 2,
                                fillColor: "#ffcc00", // Warna area
                                fillOpacity: 0.5
                            };
                        },
                        onEachFeature: function (feature, layer) {
                            let wilayah = region;
                            
                            // Ambil data realisasi_csr dari API backend
                            fetch('{{ route("csr.getRealisasi") }}?wilayah=' + wilayah)
                                .then(response => response.json())
                                .then(csrData => {
                                    let realisasiCsr = csrData.realisasi_csr || "Data tidak tersedia";

                                    let popupContent = `<strong>${wilayah}</strong><br>
                                        Realisasi CSR: <b>Rp ${realisasiCsr.toLocaleString()}</b>`;
                                    
                                    layer.bindPopup(popupContent);
                                })
                                .catch(error => console.error("Error fetching CSR data:", error));

                            // Hover efek untuk highlight wilayah
                            layer.on("mouseover", function () {
                                this.setStyle({
                                    fillColor: "#ff3300",
                                    fillOpacity: 0.7
                                });
                            });

                            layer.on("mouseout", function () {
                                this.setStyle({
                                    fillColor: "#ffcc00",
                                    fillOpacity: 0.5
                                });
                            });
                        }
                    }).addTo(geojsonLayer);

                    map.fitBounds(layer.getBounds());
                })
                .catch(error => console.error("Error loading GeoJSON:", error));
        }

        // Event listener saat filter berubah
        $('#pemegang_saham, #bidang_kegiatan, #tahun, #bulan').change(function () {
            let selectedRegion = $('#pemegang_saham').val();
            loadGeoJSON(selectedRegion);
        });

    });
</script>

</body>
</html>
