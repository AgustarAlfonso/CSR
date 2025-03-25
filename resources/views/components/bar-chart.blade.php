<div class="card mt-3">
    <div class="card-header">
        <h5>Distribusi Realisasi CSR</h5>
        <div class="btn-group" role="group" aria-label="Chart Toggle">
            <button type="button" class="btn btn-primary" id="toggleBarPemegangSaham">Pemegang Saham</button>
            <button type="button" class="btn btn-outline-primary" id="toggleBarBidangKegiatan">Bidang Kegiatan</button>
        </div>
    </div>
    <div class="card-body">
        <canvas id="csrBarChart"></canvas>
    </div>
</div>

<script>
    let currentBarChartType = "pemegang_saham";
    let csrBarChart = null; // Simpan instance chart secara global

    function updateBarChart(data, labels) {
        let ctx = document.getElementById("csrBarChart").getContext("2d");

        // Hapus chart sebelumnya jika sudah ada
        if (csrBarChart instanceof Chart) {
            csrBarChart.destroy();
        }

        // Buat chart baru
        csrBarChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                    label: "Realisasi CSR (Rp)",
                    data: data,
                    backgroundColor: "#36a2eb"
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: (value) => value.toLocaleString("id-ID") }
                    }
                }
            }
        });
    }

    function loadBarChartFromTable() {
        let filters = { pemegang_saham: [], bidang_kegiatan: [], tahun: [], bulan: [] };
        document.querySelectorAll(".filter-toggle[data-active='true']").forEach(button => {
            filters[button.dataset.filter].push(button.dataset.value);
        });

        let dataMap = {};
        let labelColumn = currentBarChartType === "pemegang_saham" ? 1 : 2;
        document.querySelectorAll("#csrTable tr").forEach(row => {
            let cells = row.getElementsByTagName("td");
            if (cells.length > 0) {
                let label = cells[labelColumn].innerText.trim();
                let tahun = cells[3].innerText.trim();
                let bulan = cells[4].innerText.trim();
                let value = parseInt(cells[5].innerText.replace(/\./g, "")) || 0;

                if (
                    (filters.pemegang_saham.length === 0 || filters.pemegang_saham.includes(cells[1].innerText.trim())) &&
                    (filters.bidang_kegiatan.length === 0 || filters.bidang_kegiatan.includes(cells[2].innerText.trim())) &&
                    (filters.tahun.length === 0 || filters.tahun.includes(tahun)) &&
                    (filters.bulan.length === 0 || filters.bulan.includes(bulan))
                ) {
                    dataMap[label] = (dataMap[label] || 0) + value;
                }
            }
        });

        updateBarChart(Object.values(dataMap), Object.keys(dataMap));
    }

    document.getElementById("toggleBarPemegangSaham").addEventListener("click", function() {
        currentBarChartType = "pemegang_saham";
        this.classList.add("btn-primary");
        this.classList.remove("btn-outline-primary");
        document.getElementById("toggleBarBidangKegiatan").classList.remove("btn-primary");
        document.getElementById("toggleBarBidangKegiatan").classList.add("btn-outline-primary");
        loadBarChartFromTable();
    });

    document.getElementById("toggleBarBidangKegiatan").addEventListener("click", function() {
        currentBarChartType = "bidang_kegiatan";
        this.classList.add("btn-primary");
        this.classList.remove("btn-outline-primary");
        document.getElementById("toggleBarPemegangSaham").classList.remove("btn-primary");
        document.getElementById("toggleBarPemegangSaham").classList.add("btn-outline-primary");
        loadBarChartFromTable();
    });

    document.addEventListener("DOMContentLoaded", loadBarChartFromTable);
</script>


