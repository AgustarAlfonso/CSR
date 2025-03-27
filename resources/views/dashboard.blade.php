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
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
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

                        @php
                        $monthOrder = [
                            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                        ];
                        $months = collect($months)->sortBy(fn($month) => array_search($month, $monthOrder))->toArray();
                        @endphp

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

            <x-pie-chart />
            <x-bar-chart />
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
</body>
</html>
