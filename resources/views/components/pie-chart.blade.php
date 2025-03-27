<div class="card mt-3">
    <div class="card-header">
        <h5>Distribusi CSR</h5>
        <div class="btn-group" role="group" aria-label="Chart Toggle">
            <button type="button" class="btn btn-primary" id="togglePemegangSaham">Pemegang Saham</button>
            <button type="button" class="btn btn-outline-primary" id="toggleBidangKegiatan">Bidang Kegiatan</button>
        </div>
    </div>
    <div class="card-body">
        <canvas id="csrPieChart"></canvas>
    </div>
</div>

<script>
    let currentChartType = "pemegang_saham";

    function updateChart(data, labels) {
        let ctx = document.getElementById("csrPieChart").getContext("2d");
        if (window.csrChart) {
            window.csrChart.destroy();
        }
        window.csrChart = new Chart(ctx, {
            type: "pie",
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        "#ff6384", "#36a2eb", "#ffce56", "#8e5ea2", "#3cba9f",
                        "#e8c3b9", "#c45850", "#ff9f40", "#4bc0c0", "#9966ff",
                        "#ff6384", "#36a2eb", "#ffce56", "#8e5ea2", "#3cba9f",
                        "#e8c3b9", "#c45850", "#ff9f40", "#4bc0c0", "#9966ff", "#ffcd56"
                    ]
                }]
            },
            options: {
                plugins: {
                    datalabels: {
                        formatter: (value, ctx) => ((value / ctx.dataset.data.reduce((a, b) => a + b, 0)) * 100).toFixed(2) + "%",
                        color: "#fff",
                        font: { weight: "bold" }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    function fetchFilteredData() {
        let filters = {
            pemegang_saham: document.getElementById("pemegang_saham").value,
            bidang_kegiatan: document.getElementById("bidang_kegiatan").value,
            tahun: document.getElementById("tahun").value,
            bulan: document.getElementById("bulan").value
        };

        $.ajax({
            url: "{{ route('csr.filter') }}",
            type: "POST",
            data: filters,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            success: function(response) {
                console.log("Filtered Data Response:", response);
                let dataMap = {};
                response.forEach(item => {
                    let key = currentChartType === "pemegang_saham" ? item.pemegang_saham : item.bidang_kegiatan;
                    dataMap[key] = (dataMap[key] || 0) + (parseFloat(item.realisasi_csr) || 0);
                });
                console.log("DataMap:", dataMap);
                console.log("Labels:", Object.keys(dataMap));
                console.log("Data:", Object.values(dataMap));
                
                if (Object.keys(dataMap).length === 0) {
                    updateChart([1], ["Tidak ada data"]);
                } else {
                    updateChart(Object.values(dataMap), Object.keys(dataMap));
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    }

    document.getElementById("togglePemegangSaham").addEventListener("click", function() {
        currentChartType = "pemegang_saham";
        this.classList.add("btn-primary");
        this.classList.remove("btn-outline-primary");
        document.getElementById("toggleBidangKegiatan").classList.remove("btn-primary");
        document.getElementById("toggleBidangKegiatan").classList.add("btn-outline-primary");
        fetchFilteredData();
    });

    document.getElementById("toggleBidangKegiatan").addEventListener("click", function() {
        currentChartType = "bidang_kegiatan";
        this.classList.add("btn-primary");
        this.classList.remove("btn-outline-primary");
        document.getElementById("togglePemegangSaham").classList.remove("btn-primary");
        document.getElementById("togglePemegangSaham").classList.add("btn-outline-primary");
        fetchFilteredData();
    });

    document.getElementById("pemegang_saham").addEventListener("change", fetchFilteredData);
    document.getElementById("bidang_kegiatan").addEventListener("change", fetchFilteredData);
    document.getElementById("tahun").addEventListener("change", fetchFilteredData);
    document.getElementById("bulan").addEventListener("change", fetchFilteredData);

    document.addEventListener("DOMContentLoaded", fetchFilteredData);
</script>
