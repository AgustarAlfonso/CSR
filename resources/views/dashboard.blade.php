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
                        <h6>Sponsor</h6>
                        @foreach($sponsors as $sponsor)
                            <div class="form-check">
                                <input class="form-check-input filter-checkbox" type="checkbox" value="{{ $sponsor }}" data-filter="sponsor">
                                <label class="form-check-label">{{ $sponsor }}</label>
                            </div>
                        @endforeach

                        <h6 class="mt-3">Tahun</h6>
                        @foreach($years as $year)
                            <div class="form-check">
                                <input class="form-check-input filter-checkbox" type="checkbox" value="{{ $year }}" data-filter="tahun">
                                <label class="form-check-label">{{ $year }}</label>
                            </div>
                        @endforeach

                        <h6 class="mt-3">Bulan</h6>
                        @foreach($months as $month)
                            <div class="form-check">
                                <input class="form-check-input filter-checkbox" type="checkbox" value="{{ $month }}" data-filter="bulan">
                                <label class="form-check-label">{{ $month }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tabel CSR -->
            <div class="col-md-9">
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Data CSR</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Program</th>
                                    <th>Sponsor</th>
                                    <th>Tahun</th>
                                    <th>Bulan</th>
                                    <th>Modal CSR (Rp)</th>
                                    <th>Realisasi CSR (Rp)</th>
                                    <th>Sisa CSR (Rp)</th>
                                </tr>
                            </thead>
                            <tbody id="csrTable">
                                @foreach($csrs as $csr)
                                    <tr>
                                        <td>{{ $csr->nama_program }}</td>
                                        <td>{{ $csr->sponsor }}</td>
                                        <td>{{ $csr->tahun }}</td>
                                        <td>{{ $csr->bulan }}</td>
                                        <td>{{ number_format($csr->modal_csr, 0, ',', '.') }}</td>
                                        <td>{{ number_format($csr->realisasi_csr, 0, ',', '.') }}</td>
                                        <td>{{ number_format($csr->sisa_csr, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Pie Chart -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Distribusi CSR</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="csrPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateChart(data) {
            let ctx = document.getElementById("csrPieChart").getContext("2d");
            let total = data.modal + data.realisasi + data.sisa;
            if (window.csrChart) {
                window.csrChart.destroy();
            }
            window.csrChart = new Chart(ctx, {
                type: "pie",
                data: {
                    labels: ["Modal CSR", "Realisasi CSR", "Sisa CSR"],
                    datasets: [{
                        data: [data.modal, data.realisasi, data.sisa],
                        backgroundColor: ["#ff6384", "#36a2eb", "#ffce56"]
                    }]
                },
                options: {
                    plugins: {
                        datalabels: {
                            formatter: (value, ctx) => {
                                let percentage = ((value / total) * 100).toFixed(2) + "%";
                                return percentage;
                            },
                            color: "#fff",
                            font: {
                                weight: "bold"
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        function loadChartFromTable() {
            let totalModal = 0, totalRealisasi = 0, totalSisa = 0;
            document.querySelectorAll("#csrTable tr").forEach(row => {
                let cells = row.getElementsByTagName("td");
                if (cells.length > 0) {
                    totalModal += parseInt(cells[4].innerText.replace(/\./g, "")) || 0;
                    totalRealisasi += parseInt(cells[5].innerText.replace(/\./g, "")) || 0;
                    totalSisa += parseInt(cells[6].innerText.replace(/\./g, "")) || 0;
                }
            });
            updateChart({ modal: totalModal, realisasi: totalRealisasi, sisa: totalSisa });
        }

        document.addEventListener("DOMContentLoaded", function() {
            loadChartFromTable();
            document.querySelectorAll(".filter-checkbox").forEach(checkbox => {
                checkbox.addEventListener("change", function() {
                    let filters = { sponsor: [], tahun: [], bulan: [] };
                    document.querySelectorAll(".filter-checkbox:checked").forEach(checkedBox => {
                        filters[checkedBox.dataset.filter].push(checkedBox.value);
                    });
                    fetch("{{ route('csr.filter') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                        },
                        body: JSON.stringify(filters)
                    })
                    .then(response => response.json())
                    .then(data => {
                        let tbody = document.getElementById("csrTable");
                        tbody.innerHTML = "";
                        data.forEach(csr => {
                            tbody.innerHTML += `<tr>
                                <td>${csr.nama_program}</td>
                                <td>${csr.sponsor}</td>
                                <td>${csr.tahun}</td>
                                <td>${csr.bulan}</td>
                                <td>${csr.modal_csr.toLocaleString('id-ID')}</td>
                                <td>${csr.realisasi_csr.toLocaleString('id-ID')}</td>
                                <td>${csr.sisa_csr.toLocaleString('id-ID')}</td>
                            </tr>`;
                        });
                        loadChartFromTable();
                    });
                });
            });
        });
    </script>
</body>
</html>
