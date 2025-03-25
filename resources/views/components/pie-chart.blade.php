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

    function loadChartFromTable() {
        let dataMap = {};
        let labelColumn = currentChartType === "pemegang_saham" ? 1 : 2;
        document.querySelectorAll("#csrTable tr").forEach(row => {
            let cells = row.getElementsByTagName("td");
            if (cells.length > 0) {
                let label = cells[labelColumn].innerText.trim();
                let value = parseInt(cells[5].innerText.replace(/\./g, "")) || 0;
                dataMap[label] = (dataMap[label] || 0) + value;
            }
        });
        updateChart(Object.values(dataMap), Object.keys(dataMap));
    }

    document.getElementById("togglePemegangSaham").addEventListener("click", function() {
        currentChartType = "pemegang_saham";
        this.classList.add("btn-primary");
        this.classList.remove("btn-outline-primary");
        document.getElementById("toggleBidangKegiatan").classList.remove("btn-primary");
        document.getElementById("toggleBidangKegiatan").classList.add("btn-outline-primary");
        loadChartFromTable();
    });

    document.getElementById("toggleBidangKegiatan").addEventListener("click", function() {
        currentChartType = "bidang_kegiatan";
        this.classList.add("btn-primary");
        this.classList.remove("btn-outline-primary");
        document.getElementById("togglePemegangSaham").classList.remove("btn-primary");
        document.getElementById("togglePemegangSaham").classList.add("btn-outline-primary");
        loadChartFromTable();
    });

    document.addEventListener("DOMContentLoaded", loadChartFromTable);
</script>
