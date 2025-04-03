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
        <div id="chartInfo" class="mt-3 text-center"></div>

    </div>
</div>

<script>
    let currentChartType = "pemegang_saham";

    function updateChart(data, labels) {
    let ctx = document.getElementById("csrPieChart").getContext("2d");
    if (window.csrChart) {
        window.csrChart.destroy();
    }

    let total = data.reduce((a, b) => a + b, 0);

    console.log("Total CSR Data:", total);
    console.log("Pie Chart Data:", data);
    console.log("Labels:", labels);

    window.csrChart = new Chart(ctx, {
        type: "pie",
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ["#ff6384", "#36a2eb", "#ffce56"]
            }]
        },
        options: {
            plugins: {
                datalabels: {
                    formatter: (value) => {
                        if (total === 0) return "0%";  // Cegah NaN%
                        return ((value / total) * 100).toFixed(2) + "%"; // Pastikan persen dihitung dengan angka
                    },
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

        let jumlahAnggaran = parseFloat(response.jumlah_anggaran) || 0;
        let realisasiCsr = parseFloat(response.realisasi_csr) || 0;
        let sisaCsr = parseFloat(response.sisa_csr) || 0;

        let data = [jumlahAnggaran, realisasiCsr, sisaCsr];
        let labels = ["Jumlah Anggaran", "Realisasi CSR", "Sisa CSR"];

        console.log("Final Data for Chart:", data);
        updateChart(data, labels);
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
