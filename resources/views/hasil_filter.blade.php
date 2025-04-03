@extends('layouts.master')

@section('title', 'Data CSR ' . ($data->first()->pemegang_saham ?? '') . ' - ' . ($data->first()->bulan ?? '') . ' ' . ($data->first()->tahun ?? ''))

@section('content')
<div class="container mx-auto mt-10 p-6 bg-white shadow rounded-lg">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">
        Data CSR {{ $data->first()->pemegang_saham ?? '' }} - {{ $data->first()->bulan ?? '' }} {{ $data->first()->tahun ?? '' }}
    </h3>
    @include('components.table', [
        'headers' => ['No', 'Nama Program', 'Bidang Kegiatan', 'Pemegang Saham', 'Bulan', 'Tahun', 'Realisasi CSR', 'Keterangan'],
        'rows' => $data->map(function ($item, $index) {
            return [
                $index + 1,
                $item->nama_program,
                $item->bidang_kegiatan,
                $item->pemegang_saham,
                $item->bulan,
                $item->tahun,
                'Rp' . number_format($item->realisasi_csr, 0, ',', '.'),
                $item->ket == 'nan' ? '-' : $item->ket
            ];
        })
    ])
    
        
    </div>
    <a href='{{ route("dashboard") }}' class="block w-full text-center mt-4 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 rounded">Kembali</a>
</div>

<script>
    function sortTable(columnIndex) {
        var table = document.getElementById("csrTable");
        var tbody = table.tBodies[0];
        var rows = Array.from(tbody.rows);
        
        var isAscending = table.dataset.sortCol == columnIndex ? table.dataset.sortOrder !== "asc" : true;
        table.dataset.sortCol = columnIndex;
        table.dataset.sortOrder = isAscending ? "asc" : "desc";

        // Reset semua ikon sort
        document.querySelectorAll(".sort-icon").forEach(icon => {
            icon.innerHTML = "▲"; // Default ke panah naik
            icon.style.opacity = "0.3"; // Semua ikon dibuat kurang mencolok
        });

        // Menentukan ikon panah aktif dan menonjolkannya
        var headers = table.tHead.rows[0].cells;
        var icon = headers[columnIndex].querySelector(".sort-icon");
        icon.innerHTML = isAscending ? "▼" : "▲";
        icon.style.opacity = "1"; // Ikon aktif menjadi lebih jelas

        rows.sort((a, b) => {
            var cellA = a.cells[columnIndex].textContent.trim();
            var cellB = b.cells[columnIndex].textContent.trim();

            if (!isNaN(cellA) && !isNaN(cellB)) {
                return isAscending ? cellA - cellB : cellB - cellA;
            }
            return isAscending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
        });

        tbody.append(...rows);
    }

    document.addEventListener("DOMContentLoaded", function() {
        sortTable(0); // Memanggil fungsi sorting untuk kolom pertama saat halaman pertama kali dimuat
    });
</script>
@endsection
