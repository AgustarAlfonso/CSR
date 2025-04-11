@extends('layouts.master')
@php
    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $bulanTampil = $bulan ? ($namaBulan[(int) $bulan] ?? $bulan) : 'Semua Bulan';
@endphp

@section('title', 'Data CSR ' . ($pemegang_saham ?? 'Semua Pemegang Saham') . ' - ' . $bulanTampil . ' ' . ($tahun ?? 'Semua Tahun'))

@section('content')

@if (session('success'))
<div 
    x-data="{ show: true, percent: 100 }"
    x-init="
        let interval = setInterval(() => {
            percent -= 1;
            if (percent <= 0) {
                clearInterval(interval);
                show = false;
            }
        }, 30);
    "
    x-show="show"
    x-transition
    x-cloak
    class="fixed top-5 right-5 w-[300px] bg-green-500 text-white rounded-lg shadow-lg z-50 overflow-hidden"
>
    <div class="flex items-center p-3 space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span class="text-sm">{{ session('success') }}</span>
    </div>
    <div class="h-1 bg-white/40">
        <div 
            class="h-full bg-white transition-all duration-75"
            :style="{ width: percent + '%' }">
        </div>
    </div>
</div>
@endif




<div class="container mx-auto mt-10 p-6 bg-white shadow rounded-lg">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">
        Data CSR {{ $pemegang_saham ?? 'Semua Pemegang Saham' }} - {{ $bulanTampil }} {{ $tahun ?? 'Semua Tahun' }}
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
    <div class="container mx-auto mt-10 p-6 bg-white shadow rounded-lg">

        @if (session('success'))
    <div 
        x-data="{ show: true, percent: 100 }"
        x-init="
            let interval = setInterval(() => {
                percent -= 1;
                if (percent <= 0) {
                    clearInterval(interval);
                    show = false;
                }
            }, 30);
        "
        x-show="show"
        x-transition
        x-cloak
        class="fixed top-5 right-5 w-[300px] bg-green-500 text-white rounded-lg shadow-lg z-50 overflow-hidden"
    >
        <div class="flex items-center p-3 space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
        <div class="h-1 bg-white/40">
            <div 
                class="h-full bg-white transition-all duration-75"
                :style="{ width: percent + '%' }">
            </div>
        </div>
    </div>
    @endif
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
