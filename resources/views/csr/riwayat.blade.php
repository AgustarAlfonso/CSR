@extends('layouts.master')

@section('title', 'Data Anggaran CSR ')

@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">
    <h1 class="text-2xl font-bold mb-6">Riwayat CSR</h1>

    <form method="GET" id="filterForm" class="mb-8 p-4 bg-white rounded-xl shadow-md flex flex-wrap items-end gap-6">
        <div class="w-full sm:w-auto">
            <label class="block text-sm font-semibold text-gray-600 mb-1">Tahun</label>
            <select name="tahun" class="w-full sm:w-36 rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @foreach($daftarTahun as $th)
                    <option value="{{ $th }}" {{ $tahun == $th ? 'selected' : '' }}>{{ $th }}</option>
                @endforeach
            </select>
        </div>
    
        <div class="w-full sm:w-auto">
            <label class="block text-sm font-semibold text-gray-600 mb-1">Pemegang Saham</label>
            <select name="pemegang_saham" class="w-full sm:w-64 rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="semua" {{ $pemegangSaham == 'semua' || !$pemegangSaham ? 'selected' : '' }}>Semua Pemegang Saham</option>
                @foreach($semuaPemegangSaham as $ps)
                    <option value="{{ $ps }}" {{ $pemegangSaham == $ps ? 'selected' : '' }}>{{ $ps }}</option>
                @endforeach
            </select>
        </div>
    

    </form>
    
    <div id="riwayatContainer">
        @include('csr.partials.riwayat-list', ['riwayatPerSaham' => $riwayatPerSaham])
    </div>


</div>
@endsection
@push('scripts')
<script>
    document.getElementById('filterForm').addEventListener('change', function(e) {
        e.preventDefault();

        const form = this;
        const params = new URLSearchParams(new FormData(form)).toString();

        fetch(`{{ route('csr.riwayat.ajax') }}?${params}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('riwayatContainer').innerHTML = html;
        });
    });
</script>
@endpush

