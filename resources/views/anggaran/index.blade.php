@extends('layouts.master')

@section('title', 'Data Anggaran CSR ')

@section('content')




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


<div class="flex justify-between items-center mb-4">
    <h3 class="text-xl font-semibold text-gray-800">
        Data Anggaran CSR 
    </h3>
    
    @if(in_array(auth()->user()->role, [1, 2]))
        <a href="{{ route('anggaran.create') }}"
           class="inline-flex items-center gap-2 bg-green-300 border border-gray-300 text-gray-800 hover:bg-gray-100 hover:border-gray-400 px-4 py-2 rounded-lg shadow-sm transition-all duration-200 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            Tambah
        </a>
    @endif
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
                <span class="text-sm bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium" data-count-label="tahun">(1 dipilih)</span>
            </summary>
            <fieldset>
                <legend class="sr-only">Filter Tahun</legend>
                <div class="p-3 grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($daftarTahun as $tahun)
                        <label class="inline-flex items-center gap-3">
                            <input 
                                type="radio" 
                                name="tahun" 
                                value="{{ $tahun }}" 
                                class="size-5 rounded border-gray-300 shadow-sm"
                                {{ (int) request('tahun', $tahunFilter) === (int) $tahun ? 'checked' : '' }} />
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

{{-- <script>
  document.querySelector('form#filterForm').addEventListener('submit', function (e) {
    const tahunCheckboxes = document.querySelectorAll('input[name="tahun[]"]:checked');
    if (tahunCheckboxes.length === 0) {
      e.preventDefault();
      alert('Pilih minimal satu tahun dulu ya sayang 🤍');
    }
  });
</script> --}}


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
