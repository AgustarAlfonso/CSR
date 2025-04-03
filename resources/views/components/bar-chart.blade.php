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

    // Hapus chart sebelumnya kalau ada
    if (csrBarChart instanceof Chart) {
        csrBarChart.destroy();
    }

    // Buat chart baru
    csrBarChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                label: "CSR (Rp)",
                data: data,
                backgroundColor: ["#36a2eb", "#4bc0c0", "#ff6384"], // Warna sesuai kategori
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    formatter: (value) => value.toLocaleString("id-ID"), // Format angka jadi rupiah
                    color: "#000",
                    font: { weight: "bold" }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: (value) => value.toLocaleString("id-ID") }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
}


    function loadBarChartFromAPI() {
    let filters = {
        pemegang_saham: $('#pemegang_saham').val(),
        bidang_kegiatan: $('#bidang_kegiatan').val(),
        tahun: $('#tahun').val(),
        bulan: $('#bulan').val(),
    };

    $.ajax({
        url: '{{ route("csr.filter") }}',
        type: 'POST',
        data: filters,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            console.log("Filtered Bar Chart Data:", response);

            // Konversi ke number biar nggak ada NaN
            let jumlahAnggaran = parseFloat(response.jumlah_anggaran) || 0;
            let realisasiCsr = parseFloat(response.realisasi_csr) || 0;
            let sisaCsr = parseFloat(response.sisa_csr) || 0;

            let data = [jumlahAnggaran, realisasiCsr, sisaCsr];
            let labels = ["Jumlah Anggaran", "Realisasi CSR", "Sisa CSR"];

            console.log("Final Data for Bar Chart:", data);
            updateBarChart(data, labels);
        },
        error: function(xhr) {
            console.error(xhr.responseText);
        }
    });
}


    document.getElementById("toggleBarPemegangSaham").addEventListener("click", function() {
        currentBarChartType = "pemegang_saham";
        this.classList.add("btn-primary");
        this.classList.remove("btn-outline-primary");
        document.getElementById("toggleBarBidangKegiatan").classList.remove("btn-primary");
        document.getElementById("toggleBarBidangKegiatan").classList.add("btn-outline-primary");
        loadBarChartFromAPI();
    });

    document.getElementById("toggleBarBidangKegiatan").addEventListener("click", function() {
        currentBarChartType = "bidang_kegiatan";
        this.classList.add("btn-primary");
        this.classList.remove("btn-outline-primary");
        document.getElementById("toggleBarPemegangSaham").classList.remove("btn-primary");
        document.getElementById("toggleBarPemegangSaham").classList.add("btn-outline-primary");
        loadBarChartFromAPI();
    });

    // Event listener untuk filter agar data otomatis terupdate
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll("#pemegang_saham, #bidang_kegiatan, #tahun, #bulan").forEach(element => {
            element.addEventListener("change", loadBarChartFromAPI);
        });

        // Load pertama kali
        loadBarChartFromAPI();
    });
</script>
