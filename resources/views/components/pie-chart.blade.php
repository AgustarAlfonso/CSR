<div class="card mt-3">
    <div class="card-header">
        <h5>Distribusi CSR per Pemegang Saham</h5>
    </div>
    <div class="card-body">
        <canvas id="csrPieChart"></canvas>
    </div>
</div>

<script>
    function updateChart(data) {
        let ctx = document.getElementById("csrPieChart").getContext("2d");
        let labels = data.map(item => item.pemegang_saham);
        let values = data.map(item => item.total);
        let backgroundColors = [
            "#ff6384", "#36a2eb", "#ffce56", "#4bc0c0", "#9966ff", "#ff9f40",
            "#c9cbcf", "#ff4500", "#32cd32", "#8a2be2", "#00ced1", "#dc143c",
            "#ffa500", "#228b22", "#1e90ff", "#ff1493", "#8b0000", "#20b2aa",
            "#d2691e", "#6495ed", "#7f8c8d"
        ];

        if (window.csrChart) {
            window.csrChart.destroy();
        }
        window.csrChart = new Chart(ctx, {
            type: "pie",
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: backgroundColors.slice(0, labels.length)
                }]
            },
            options: {
                plugins: {
                    datalabels: {
                        formatter: (value, ctx) => {
                            let total = values.reduce((acc, val) => acc + val, 0);
                            return ((value / total) * 100).toFixed(2) + "%";
                        },
                        color: "#fff",
                        font: { weight: "bold" }
                    }
                }
            }
        });
    }

    function loadChartFromTable() {
        let pemegangSahamTotals = {};

        document.querySelectorAll("#csrTable tr").forEach(row => {
            let cells = row.getElementsByTagName("td");
            if (cells.length > 0) {
                let pemegangSaham = cells[1].innerText.trim();
                let realisasi = parseInt(cells[5].innerText.replace(/\./g, "")) || 0;
                if (!pemegangSahamTotals[pemegangSaham]) {
                    pemegangSahamTotals[pemegangSaham] = 0;
                }
                pemegangSahamTotals[pemegangSaham] += realisasi;
            }
        });

        let chartData = Object.keys(pemegangSahamTotals).map(key => ({
            pemegang_saham: key,
            total: pemegangSahamTotals[key]
        }));

        updateChart(chartData);
    }

    document.addEventListener("DOMContentLoaded", loadChartFromTable);
</script>