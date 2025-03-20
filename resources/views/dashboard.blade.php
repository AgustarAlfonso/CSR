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
                          <h6>Sponsor</h6>
                          @foreach($sponsors as $sponsor)
                              <button type="button" class="btn btn-outline-primary filter-toggle" data-filter="sponsor" data-value="{{ $sponsor }}" data-active="false">
                                  {{ $sponsor }}
                              </button>
                          @endforeach
  
                          <h6 class="mt-3">Tahun</h6>
                          @foreach($years as $year)
                              <button type="button" class="btn btn-outline-primary filter-toggle" data-filter="tahun" data-value="{{ $year }}" data-active="false">
                                  {{ $year }}
                              </button>
                          @endforeach

                          @php
                        $monthOrder = [
                            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                        ];
                        $months = collect($months)->sortBy(fn($month) => array_search($month, $monthOrder))->toArray();
                        @endphp
  
                          <h6 class="mt-3">Bulan</h6>
                          @foreach($months as $month)
                              <button type="button" class="btn btn-outline-primary filter-toggle" data-filter="bulan" data-value="{{ $month }}" data-active="false">
                                  {{ $month }}
                              </button>
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
                                        <td class="modal-csr">{{ number_format($csr->modal_csr, 0, ',', '.') }}</td>
                                        <td class="realisasi-csr">{{ number_format($csr->realisasi_csr, 0, ',', '.') }}</td>
                                        <td class="sisa-csr">{{ number_format($csr->sisa_csr, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total</th>
                                    <th id="totalModal">0</th>
                                    <th id="totalRealisasi">0</th>
                                    <th id="totalSisa">0</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Gunakan Komponen Pie Chart -->
                <x-pie-chart />
                <x-bar-chart />
            </div>
        </div>
    </div>

    <script>
        function updateTotals() {
            let totalModal = 0, totalRealisasi = 0, totalSisa = 0;
            document.querySelectorAll(".modal-csr").forEach(el => totalModal += parseInt(el.textContent.replace(/\./g, '')) || 0);
            document.querySelectorAll(".realisasi-csr").forEach(el => totalRealisasi += parseInt(el.textContent.replace(/\./g, '')) || 0);
            document.querySelectorAll(".sisa-csr").forEach(el => totalSisa += parseInt(el.textContent.replace(/\./g, '')) || 0);
            document.getElementById("totalModal").textContent = totalModal.toLocaleString('id-ID');
            document.getElementById("totalRealisasi").textContent = totalRealisasi.toLocaleString('id-ID');
            document.getElementById("totalSisa").textContent = totalSisa.toLocaleString('id-ID');
        }

        document.addEventListener("DOMContentLoaded", function() {
            updateTotals();
              document.querySelectorAll(".filter-toggle").forEach(button => {
                button.addEventListener("click", function() {
                    let isActive = this.getAttribute("data-active") === "true";
                    this.setAttribute("data-active", isActive ? "false" : "true");
                    this.classList.toggle("btn-primary", !isActive);
                    this.classList.toggle("btn-outline-primary", isActive);

                    let filters = { sponsor: [], tahun: [], bulan: [] };
                    document.querySelectorAll(".filter-toggle[data-active='true']").forEach(activeButton => {
                        filters[activeButton.dataset.filter].push(activeButton.dataset.value);
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
                                <td class="modal-csr">${csr.modal_csr.toLocaleString('id-ID')}</td>
                                <td class="realisasi-csr">${csr.realisasi_csr.toLocaleString('id-ID')}</td>
                                <td class="sisa-csr">${csr.sisa_csr.toLocaleString('id-ID')}</td>
                            </tr>`;
                        });
                        updateTotals();
                        loadChartFromTable();
                        loadBarChartFromTable();
                    });
                });
            });
        });
    </script>
</body>
</html>
