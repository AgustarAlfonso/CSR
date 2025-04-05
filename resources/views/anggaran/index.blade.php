@extends('layouts.master')

@section('title', 'Data Anggaran CSR ' . ($anggaran->first()->pemegang_saham ?? '') . ' - ' . ($anggaran->first()->bulan ?? '') . ' ' . ($anggaran->first()->tahun ?? ''))

@section('content')
<div class="container mx-auto mt-10 p-6 bg-white shadow rounded-lg">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold text-gray-800">
            Data Anggaran CSR {{ $anggaran->first()->pemegang_saham ?? '' }} - {{ $anggaran->first()->bulan ?? '' }} {{ $anggaran->first()->tahun ?? '' }}
        </h3>
        <a href="{{ route('anggaran.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold text-sm">+ Tambah Anggaran</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y-2 divide-gray-200" id="anggaranTable" data-sort-col="0" data-sort-order="desc">
            <thead class="ltr:text-left rtl:text-right bg-yellow-200">
                <tr class="*:font-medium *:text-gray-900">
                    <th class="px-3 py-2">No</th>
                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(1)">Pemegang Saham <span class="sort-icon"></span></th>
                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(2)">Bulan <span class="sort-icon"></span></th>
                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(3)">Tahun <span class="sort-icon"></span></th>
                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(4)">Jumlah Anggaran <span class="sort-icon"></span></th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 *:even:bg-gray-50">
                @foreach($anggaran as $index => $row)
                    <tr class="*:text-gray-900 *:first:font-medium">
                        <td class="px-3 py-2">{{ $index + 1 }}</td>
                        <td class="px-3 py-2">{{ $row->pemegang_saham }}</td>
                        <td class="px-3 py-2">{{ $row->bulan }}</td>
                        <td class="px-3 py-2">{{ $row->tahun }}</td>
                        <td class="px-3 py-2 font-semibold text-blue-600">Rp{{ number_format($row->jumlah_anggaran, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <a href="{{ route('anggaran.edit', $row->id) }}" class="text-sm bg-yellow-400 hover:bg-yellow-500 text-white font-semibold px-3 py-1 rounded">Edit</a>
                            <form action="{{ route('anggaran.destroy', $row->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus?')" class="text-sm bg-red-500 hover:bg-red-600 text-white font-semibold px-3 py-1 rounded">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-green-200 font-semibold text-gray-800">
                <tr>
                    <td colspan="4" class="px-3 py-2 text-right">Total Anggaran:</td>
                    <td class="px-3 py-2 text-blue-600">Rp{{ number_format($totalAnggaran, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <div class="mt-4">
            {{ $anggaran->links('pagination::tailwind') }}
        </div>
    </div>

    <a href='{{ route("dashboard") }}' class="block w-full text-center mt-4 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 rounded">Kembali</a>
</div>

<script>
    function sortTable(columnIndex) {
        var table = document.getElementById("anggaranTable");
        var tbody = table.tBodies[0];
        var rows = Array.from(tbody.rows);

        var isAscending = table.dataset.sortCol == columnIndex ? table.dataset.sortOrder !== "asc" : true;
        table.dataset.sortCol = columnIndex;
        table.dataset.sortOrder = isAscending ? "asc" : "desc";

        document.querySelectorAll(".sort-icon").forEach(icon => {
            icon.innerHTML = "▲";
            icon.style.opacity = "0.3";
        });

        var headers = table.tHead.rows[0].cells;
        var icon = headers[columnIndex].querySelector(".sort-icon");
        icon.innerHTML = isAscending ? "▼" : "▲";
        icon.style.opacity = "1";

        rows.sort((a, b) => {
            var cellA = a.cells[columnIndex].textContent.trim();
            var cellB = b.cells[columnIndex].textContent.trim();

            if (!isNaN(cellA.replace(/[^0-9]/g, '')) && !isNaN(cellB.replace(/[^0-9]/g, ''))) {
                return isAscending
                    ? parseInt(cellA.replace(/[^0-9]/g, '')) - parseInt(cellB.replace(/[^0-9]/g, ''))
                    : parseInt(cellB.replace(/[^0-9]/g, '')) - parseInt(cellA.replace(/[^0-9]/g, ''));
            }

            return isAscending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
        });

        tbody.append(...rows);
    }

    document.addEventListener("DOMContentLoaded", function () {
        sortTable(0);
    });
</script>
@endsection
