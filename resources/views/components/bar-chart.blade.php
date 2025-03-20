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
                datasets: [
                    {
                        label: "Modal CSR (Rp)",
                        data: data.modal,
                        backgroundColor: "#ff6384",
                        borderColor: "#d32f2f",
                        borderWidth: 1
                    },
                    {
                        label: "Realisasi CSR (Rp)",
                        data: data.realisasi,
                        backgroundColor: "#36a2eb",
                        borderColor: "#1e88e5",
                        borderWidth: 1
                    },
                    {
                        label: "Sisa CSR (Rp)",
                        data: data.sisa,
                        backgroundColor: "#ffce56",
                        borderColor: "#ffce56",
                        borderWidth: 1
                    }
                ]
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
    let dataRealisasi = [];
    let dataModal = [];
    let dataSisa = [];

    document.querySelectorAll("#csrTable tr").forEach(row => {
        let cells = row.getElementsByTagName("td");
        if (cells.length > 0) {
            let sponsor = cells[1].innerText.trim();
            let realisasi = parseInt(cells[5].innerText.replace(/\./g, "")) || 0;
            let modal = parseInt(cells[4].innerText.replace(/\./g, "")) || 0;
            let sisa = parseInt(cells[6].innerText.replace(/\./g, "")) || 0;

            let index = dataLabels.indexOf(sponsor);
            if (index === -1) {
                dataLabels.push(sponsor);
                dataRealisasi.push(realisasi);
                dataModal.push(modal);
                dataSisa.push(sisa);
            } else {
                dataRealisasi[index] += realisasi;
                dataModal[index] += modal;
                dataSisa[index] += sisa;
            }
        }
    });

    if (dataLabels.length === 0) {
        console.warn("Data kosong! Menghapus chart.");
        if (window.csrBarChart instanceof Chart) {
            window.csrBarChart.destroy();
            window.csrBarChart = null;
        }
        return;
    }

    updateBarChart({ labels: dataLabels, realisasi: dataRealisasi, modal: dataModal, sisa: dataSisa });
}


    window.onload = function () {
        loadBarChartFromTable();
    };
</script>
