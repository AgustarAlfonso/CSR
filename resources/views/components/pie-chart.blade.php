<div class="card mt-3">
    <div class="card-header">
        <h5>Distribusi CSR</h5>
    </div>
    <div class="card-body">
        <canvas id="csrPieChart"></canvas>
    </div>
</div>

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
                        formatter: (value) => ((value / total) * 100).toFixed(2) + "%",
                        color: "#fff",
                        font: { weight: "bold" }
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

    document.addEventListener("DOMContentLoaded", loadChartFromTable);
</script>
