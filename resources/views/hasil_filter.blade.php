@extends('layouts.master')

@section('title', 'Data CSR ' . ($data->first()->pemegang_saham ?? '') . ' - ' . ($data->first()->bulan ?? '') . ' ' . ($data->first()->tahun ?? ''))

@section('content')
<div class="container mx-auto mt-10 p-6 bg-white shadow rounded-lg">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">
        Data CSR {{ $data->first()->pemegang_saham ?? '' }} - {{ $data->first()->bulan ?? '' }} {{ $data->first()->tahun ?? '' }}
    </h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y-2 divide-gray-200" id="csrTable" data-sort-col="0" data-sort-order="desc">
            <thead class="ltr:text-left rtl:text-right bg-yellow-200">
                <tr class="*:font-medium *:text-gray-900">
                    <th class="px-3 py-2 whitespace-nowrap cursor-pointer" onclick="sortTable(0)">No <span class="sort-icon"></span></th>
                    <th class="px-3 py-2 whitespace-normal break-words max-w-xs cursor-pointer" onclick="sortTable(1)">Nama Program <span class="sort-icon"></span></th>
                    <th class="px-3 py-2 whitespace-nowrap cursor-pointer" onclick="sortTable(2)">Bidang Kegiatan <span class="sort-icon"></span></th>
                    <th class="px-3 py-2 whitespace-nowrap cursor-pointer" onclick="sortTable(3)">Pemegang Saham <span class="sort-icon"></span></th>
                    <th class="px-3 py-2 whitespace-nowrap cursor-pointer" onclick="sortTable(4)">Bulan <span class="sort-icon"></span></th>
                    <th class="px-3 py-2 whitespace-nowrap cursor-pointer" onclick="sortTable(5)">Tahun <span class="sort-icon"></span></th>
                    <th class="px-3 py-2 whitespace-nowrap cursor-pointer" onclick="sortTable(6)">Realisasi CSR <span class="sort-icon"></span></th>
                    <th class="px-3 py-2 whitespace-nowrap">Keterangan</th>
                    
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 *:even:bg-gray-50">
                @foreach($data as $index => $row)
                    <tr class="*:text-gray-900 *:first:font-medium">
                        <td class="px-3 py-2 whitespace-nowrap">{{ $index + 1 }}</td>
                        <td class="px-3 py-2 whitespace-normal break-words max-w-xs">{{ $row->nama_program }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">{{ $row->bidang_kegiatan }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">{{ $row->pemegang_saham }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">{{ $row->bulan }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">{{ $row->tahun }}</td>
                        <td class="px-3 py-2 whitespace-nowrap font-semibold text-green-600">Rp{{ number_format($row->realisasi_csr, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">{{ $row->ket == 'nan' ? '-' : $row->ket }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-green-200 font-semibold text-gray-800">
                <tr>
                    <td colspan="6" class="px-3 py-2 text-right">Total Realisasi CSR:</td>
                    <td class="px-3 py-2 text-green-600">Rp{{ number_format($data->sum('realisasi_csr'), 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-4">
            {{ $data->links('pagination::tailwind') }}
        </div>

        
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
