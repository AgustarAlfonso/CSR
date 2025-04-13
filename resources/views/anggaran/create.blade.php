@extends('layouts.master')

@section('title', 'Tambah Anggaran')

@section('content')


@if(session('confirm'))
<div class="modal fade show" id="konfirmasiModal" tabindex="-1" aria-labelledby="konfirmasiModalLabel" style="display:block;" aria-modal="true" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Penambahan Dana</h5>
            </div>
            <div class="modal-body">
                <p>
                    Sisa anggaran dari tahun sebelumnya sudah ada sebesar 
                    <strong>Rp {{ number_format(session('sisa_tahun_lalu')) }}</strong>.<br>
                    Jika kamu menambahkan dana baru sebesar 
                    <strong>Rp {{ number_format(session('request_data')['jumlah_anggaran']) }}</strong>, 
                    maka total anggaran untuk tahun 
                    <strong>{{ session('request_data')['tahun'] }}</strong> akan menjadi 
                    <strong>Rp {{ number_format(session('total_setelah_tambah')) }}</strong>.<br><br>
                    Yakin ingin menambahkan dana ini?
                </p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="{{ route('anggaran.store') }}">
                    @csrf
                    <input type="hidden" name="pemegang_saham" value="{{ session('request_data')['pemegang_saham'] }}">
                    <input type="hidden" name="tahun" value="{{ session('request_data')['tahun'] }}">
                    <input type="hidden" name="jumlah_anggaran" value="{{ session('request_data')['jumlah_anggaran'] }}">
                    <input type="hidden" name="konfirmasi_tambah" value="ya">
                    <input type="hidden" name="sisa_tahun_lalu" value="{{ session('sisa_tahun_lalu') }}">
                    <button type="submit" class="btn btn-primary">Ya, Tambahkan</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endif


@if (session('duplicate_error'))
<div 
    x-data="{ open: true }" 
    x-show="open"
    x-transition 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div 
        @click.away="open = false" 
        class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6"
    >
        <h2 class="text-xl font-bold text-red-600 mb-4">Anggaran Sudah Ada</h2>

        <p class="text-gray-700 mb-4">
            Data anggaran untuk 
            <strong>{{ session('request_data')['pemegang_saham'] }}</strong> tahun 
            <strong>{{ session('request_data')['tahun'] }}</strong> sudah pernah dibuat sebelumnya.
        </p>

        <div class="flex justify-end pt-4">
            <a href="{{ route('anggaran.index') }}"
               class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endif





<div class="max-w-2xl mx-auto mt-12 p-8 bg-white shadow-lg rounded-2xl border border-gray-200">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">📝 Tambah Anggaran CSR</h2>
    @if($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
        <strong>Terjadi kesalahan:</strong>
        <ul class="list-disc list-inside mt-2 text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


    <form method="POST" action="{{ route('anggaran.store') }}" class="space-y-5">
        @csrf

        <div x-data="{ open: false, selected: '', search: '' }" class="relative">
            <label for="pemegang_saham" class="block text-sm font-semibold text-gray-700 mb-1">Pemegang Saham</label>
        
            <div class="relative inline-flex w-full">
                <span class="inline-flex w-full divide-x divide-gray-300 overflow-hidden rounded border border-gray-300 bg-white shadow-sm">
                    <button
                        type="button"
                        @click="open = !open"
                        class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 text-left transition-colors hover:bg-gray-50 hover:text-gray-900 focus:relative"
                        x-text="selected || '-- Pilih Pemegang Saham --'"
                    ></button>
        
                    <button
                        type="button"
                        @click="open = !open"
                        aria-label="Menu"
                        class="px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-gray-900 focus:relative"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                </span>
        
                <input type="hidden" name="pemegang_saham" :value="selected" required>
        
                <div
                    x-show="open"
                    @click.away="open = false"
                    role="menu"
                    class="absolute z-10 mt-2 w-full max-h-60 overflow-auto rounded border border-gray-300 bg-white shadow-sm"
                >
                    <!-- Search input -->
                    <div class="p-2 border-b border-gray-200">
                        <input type="text" x-model="search" placeholder="Cari pemegang saham..."
                            class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
        
                    @php
                        $orderedPemegangSaham = [
                            'Provinsi Kepulauan Riau',
                            'Provinsi Riau',
                            'Kab. Bengkalis',
                            'Kab. Bintan',
                            'Kab. Indragiri Hilir',
                            'Kab. Indragiri Hulu',
                            'Kab. Kampar',
                            'Kab. Karimun',
                            'Kab. Kepulauan Anambas',
                            'Kab. Kuansing',
                            'Kab. Lingga',
                            'Kab. Meranti',
                            'Kab. Natuna',
                            'Kab. Pelalawan',
                            'Kab. Rokan Hilir',
                            'Kab. Rokan Hulu',
                            'Kab. Siak',
                            'Kota Batam',
                            'Kota Dumai',
                            'Kota Pekanbaru',
                            'Kota Tanjung Pinang'
                        ];
                    @endphp
        
                    @foreach($orderedPemegangSaham as $ps)
                        <a href="#"
                           @click.prevent="selected = '{{ $ps }}'; open = false"
                           x-show="search === '' || '{{ strtolower($ps) }}'.includes(search.toLowerCase())"
                           class="block px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-gray-900"
                           role="menuitem">
                            {{ $ps }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        
   

        

        <!-- Tahun -->
        <div>
            <label for="tahun" class="block text-sm font-semibold text-gray-700 mb-1">Tahun</label>
            <input type="number" name="tahun" id="tahun" required
                placeholder="Contoh: 2025"  value="2016"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
        </div>

       
        

        <!-- Jumlah Anggaran -->
        <div x-data="{
            display: '',
            actual: '',
            formatRupiah(value) {
                const numberString = value.replace(/\D/g, '');
                return numberString.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        }"
        x-init="display = formatRupiah(''); actual = ''"
    >
        <label for="jumlah_anggaran" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Anggaran (Rp)</label>
    
        <!-- Tampilan yang diformat -->
        <input type="text"
            x-model="display"
            @input="actual = display.replace(/\D/g, ''); display = formatRupiah(display)"
            placeholder="Contoh: 100000000"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
        >
    
        <!-- Input tersembunyi yang dikirim ke backend -->
        <input type="hidden" name="jumlah_anggaran" :value="actual">
    </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-4">
            <button type="submit"
                class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-lg transition shadow">
                Simpan
            </button>
            <a href="{{ route('anggaran.index') }}" class="text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
</div>



@endsection

@push('scripts')
@endpush
