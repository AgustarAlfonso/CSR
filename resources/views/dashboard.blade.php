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
                        <h6>Pemegang Saham</h6>
                        @foreach($pemegang_saham as $saham)
                            <button type="button" class="btn btn-outline-primary filter-toggle" data-filter="pemegang_saham" data-value="{{ $saham }}" data-active="false">
                                {{ $saham }}
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
                                    <th onclick="sortTable(0, this)" data-default="true">Nama Program <span class="sort-icon"> 🔽</span></th>
                                    <th onclick="sortTable(1, this)">Pemegang Saham <span class="sort-icon"></span></th>
                                    <th onclick="sortTable(2, this)">Tahun <span class="sort-icon"></span></th>
                                    <th onclick="sortTable(3, this)">Bulan <span class="sort-icon"></span></th>
                                    <th onclick="sortTable(4, this)">Realisasi CSR (Rp) <span class="sort-icon"></span></th>
                                </tr>
                            </thead>
                            <tbody id="csrTable">
                                @foreach($csrs as $csr)
                                    <tr>
                                        <td>{{ $csr->nama_program }}</td>
                                        <td>{{ $csr->pemegang_saham }}</td>
                                        <td>{{ $csr->tahun }}</td>
                                        <td>{{ $csr->bulan }}</td>
                                        <td class="realisasi-csr">{{ number_format($csr->realisasi_csr, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total</th>
                                    <th id="totalRealisasi">0</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

                <!-- Gunakan Komponen Pie Chart -->
                <x-pie-chart />
                <x-bar-chart />
            </div>
        </div>
    </div>

    <script>

        let sortDirection = {}; // Simpan status sorting tiap kolom

        function sortTable(columnIndex, thElement = null) {
            let table = document.getElementById("csrTable");
            let rows = Array.from(table.rows);
            let isNumeric = columnIndex >= 4; // Kolom ke-4 ke atas adalah angka

            // Toggle sorting direction
            sortDirection[columnIndex] = sortDirection[columnIndex] === undefined ? true : !sortDirection[columnIndex];

            rows.sort((rowA, rowB) => {
                let cellA = rowA.cells[columnIndex].textContent.trim();
                let cellB = rowB.cells[columnIndex].textContent.trim();

                if (isNumeric) {
                    cellA = parseInt(cellA.replace(/\./g, '')) || 0;
                    cellB = parseInt(cellB.replace(/\./g, '')) || 0;
                }

                return sortDirection[columnIndex] ? (cellA > cellB ? 1 : -1) : (cellA < cellB ? 1 : -1);
            });

            // Update tabel dengan data yang sudah diurutkan
            table.innerHTML = "";
            rows.forEach(row => table.appendChild(row));

            // Update total setelah sorting
            updateTotals();

            // Reset semua ikon sorting
            document.querySelectorAll(".sort-icon").forEach(icon => {
                icon.textContent = ""; // Kosongkan semua ikon
            });

            // Tambahkan ikon pada kolom yang sedang di-sort
            if (thElement) {
                thElement.querySelector(".sort-icon").textContent = sortDirection[columnIndex] ? " 🔽" : " 🔼";
            }
        }

        // **3️⃣ Sort otomatis saat halaman pertama kali dimuat**
        document.addEventListener("DOMContentLoaded", function() {
            let defaultColumn = document.querySelector("th[data-default='true']");
            if (defaultColumn) {
                sortTable(0, defaultColumn); // Urutkan kolom pertama secara default
            }
        });



        function updateTotals() {
            let totalModal = 0, totalRealisasi = 0, totalSisa = 0;
            document.querySelectorAll(".realisasi-csr").forEach(el => totalRealisasi += parseInt(el.textContent.replace(/\./g, '')) || 0);
            document.getElementById("totalRealisasi").textContent = totalRealisasi.toLocaleString('id-ID');

        }

        document.addEventListener("DOMContentLoaded", function() {
            updateTotals();
              document.querySelectorAll(".filter-toggle").forEach(button => {
                button.addEventListener("click", function() {
                    let isActive = this.getAttribute("data-active") === "true";
                    this.setAttribute("data-active", isActive ? "false" : "true");
                    this.classList.toggle("btn-primary", !isActive);
                    this.classList.toggle("btn-outline-primary", isActive);

                    let filters = { pemegang_saham: [], tahun: [], bulan: [] };
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
                                <td>${csr.pemegang_saham}</td>
                                <td>${csr.tahun}</td>
                                <td>${csr.bulan}</td>
                                <td class="realisasi-csr">${csr.realisasi_csr.toLocaleString('id-ID')}</td>
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
