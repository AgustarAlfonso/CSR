<div class="card mt-3">
    <div class="card-header">
        <h5>Diagram Batang CSR</h5>
    </div>
    <div class="card-body">
        <canvas id="csrBarChart"></canvas>
    </div>
</div>

<script>
    function updateBarChart(data) {
        let ctx = document.getElementById("csrBarChart").getContext("2d");

        // Cek apakah chart sudah ada sebelum destroy
        if (window.csrBarChart instanceof Chart) {
            window.csrBarChart.destroy();
        }

        window.csrBarChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: data.labels,
                datasets: [{
                    label: "Realisasi CSR (Rp)",
                    data: data.values,
                    backgroundColor: "#36a2eb",
                    borderColor: "#1e88e5",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function loadBarChartFromTable() {
        let dataLabels = [];
        let dataValues = [];

        document.querySelectorAll("#csrTable tr").forEach(row => {
            let cells = row.getElementsByTagName("td");
            if (cells.length > 0) {
                let sponsor = cells[1].innerText.trim();
                let realisasi = parseInt(cells[5].innerText.replace(/\./g, "")) || 0;

                let index = dataLabels.indexOf(sponsor);
                if (index === -1) {
                    dataLabels.push(sponsor);
                    dataValues.push(realisasi);
                } else {
                    dataValues[index] += realisasi;
                }
            }
        });

        console.log("Data Labels:", dataLabels);
        console.log("Data Values:", dataValues);

        if (dataLabels.length === 0 || dataValues.length === 0) {
            console.warn("Data kosong! Pastikan tabel memiliki isi.");
        } else {
            updateBarChart({ labels: dataLabels, values: dataValues });
        }
    }

    window.onload = function () {
        loadBarChartFromTable();
    };
</script>
