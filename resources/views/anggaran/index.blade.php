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

    <form id="filterForm" class="mb-6">
        <div class="space-y-4">
          {{-- Filter Pemegang Saham --}}
          <details class="group rounded border border-gray-300 shadow-sm overflow-hidden">
            <summary class="flex justify-between p-3 text-sm font-medium text-gray-700 cursor-pointer">
                <span>Pemegang Saham</span>
                <span class="text-sm bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium" data-count-label="pemegang_saham">(0 dipilih)</span>
            </summary>
            <div class="p-3 grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($daftarPemegangSaham as $saham)
                  <label class="inline-flex items-center gap-3">
                    <input 
                      type="checkbox" 
                      name="pemegang_saham[]" 
                      value="{{ $saham }}" 
                      class="size-5 rounded border-gray-300 shadow-sm"
                      id="checkbox-{{ Str::slug($saham) }}"
                    />
              
                    <span class="font-medium text-gray-700">{{ $saham }}</span>
                  </label>
                @endforeach
              </div>
              
          </details>
      
          {{-- Filter Tahun --}}
          <details class="group rounded border border-gray-300 shadow-sm overflow-hidden">
            <summary class="flex justify-between p-3 text-sm font-medium text-gray-700 cursor-pointer">
                <span>Tahun</span>
                <span class="text-sm bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium" data-count-label="tahun">(0 dipilih)</span>            </summary>
                <fieldset>
                    <legend class="sr-only">Filter Tahun</legend>
                  
                    <div class="p-3 grid grid-cols-2 md:grid-cols-3 gap-3">
                      @foreach($daftarTahun as $tahun)
                        <label class="inline-flex items-center gap-3">
                          <input 
                            type="checkbox" 
                            name="tahun[]" 
                            value="{{ $tahun }}" 
                            class="size-5 rounded border-gray-300 shadow-sm"
                            id="checkbox-tahun-{{ $tahun }}"
                          />
                          <span class="font-medium text-gray-700">{{ $tahun }}</span>
                        </label>
                      @endforeach
                    </div>
                  </fieldset>
                  
          </details>
      
          <button type="submit" class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">Terapkan Filter</button>
        </div>
      </form>
       

      
        @include('anggaran._table', ['anggaran' => $anggaran, 'totalAnggaran' => $totalAnggaran])

</div>

    
@endsection

@push('scripts')
<script>
    function updateDetailCounts() {
    const form = document.getElementById('filterForm');

    const categories = ['pemegang_saham', 'tahun'];
    categories.forEach(category => {
        const checkboxes = form.querySelectorAll(`input[name="${category}[]"]`);
        let count = 0;
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) count++;
        });

        const label = form.querySelector(`[data-count-label="${category}"]`);
        if (label) {
            label.textContent = `(${count} dipilih)`;
        }
    });
}

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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById('filterForm');
        updateDetailCounts();
    
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
    
            fetch(`{{ route('anggaran.index') }}?${params}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, 'text/html');
                const newTable = newDoc.getElementById('tableWrapper');
                document.getElementById('tableWrapper').innerHTML = newTable.innerHTML;
                updateDetailCounts(); 
            });
        });

        form.querySelectorAll('input[type="checkbox"]').forEach(input => {
        input.addEventListener('change', updateDetailCounts);
    });
    
        // AJAX for pagination
        document.addEventListener('click', function (e) {
            if (e.target.closest('.pagination a')) {
                e.preventDefault();
                const url = e.target.closest('a').href;
    
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const newDoc = parser.parseFromString(html, 'text/html');
                    const newTable = newDoc.getElementById('tableWrapper');
                    document.getElementById('tableWrapper').innerHTML = newTable.innerHTML;
                });
            }
        });
    });
    </script>
    @endpush
