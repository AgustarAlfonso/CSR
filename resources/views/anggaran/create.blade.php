@extends('layouts.master')

@section('title', 'Tambah Anggaran')

@section('content')
<div class="max-w-2xl mx-auto mt-12 p-8 bg-white shadow-lg rounded-2xl border border-gray-200">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">📝 Tambah Anggaran CSR</h2>

    <form method="POST" action="{{ route('anggaran.store') }}" class="space-y-5">
        @csrf

        <!-- Dropdown Pemegang Saham -->
        <div x-data="{ open: false, selected: '' }" class="relative">
            <label for="pemegang_saham" class="block text-sm font-semibold text-gray-700 mb-1">Pemegang Saham</label>

            <div class="relative inline-flex w-full">
                <span
                    class="inline-flex w-full divide-x divide-gray-300 overflow-hidden rounded border border-gray-300 bg-white shadow-sm"
                >
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

                <!-- Hidden input for form submission -->
                <input type="hidden" name="pemegang_saham" :value="selected" required>

                <!-- Dropdown menu -->
                <div
                    x-show="open"
                    @click.away="open = false"
                    role="menu"
                    class="absolute z-10 mt-2 w-full max-h-60 overflow-auto rounded border border-gray-300 bg-white shadow-sm"
                >
                    @foreach([
                        'Kab. Kepulauan Anambas','Kab. Indragiri Hulu','Kota Batam','Kab. Indragiri Hilir','Provinsi Riau','Kab. Kampar','Kab. Bintan',
                        'Kab. Bengkalis','Kab. Rokan Hilir','Kab. Meranti','Kab. Natuna','Kab. Siak','Kab. Pelalawan','Kota Dumai','Kota Pekanbaru',
                        'Provinsi Kepulauan Riau','Kab. Rokan Hulu','Kab. Lingga','Kab. Karimun','Kota Tanjung Pinang','Kab. Kuansing'
                    ] as $ps)
                        <a href="#"
                           @click.prevent="selected = '{{ $ps }}'; open = false"
                           class="block px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-gray-900"
                           role="menuitem">
                            {{ $ps }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Bulan -->
        <!-- Bungkus dengan div untuk kelompok form field -->
   

        

        <!-- Tahun -->
        <div>
            <label for="tahun" class="block text-sm font-semibold text-gray-700 mb-1">Tahun</label>
            <input type="number" name="tahun" id="tahun" required
                placeholder="Contoh: 2025"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
        </div>

        <div x-data="bulanDropdown" class="relative w-full mb-4">
            <label for="bulan" class="block text-sm font-semibold text-gray-700 mb-1">Bulan <i> (Opsional) </i></label>
        
            <div class="inline-flex w-full divide-x divide-gray-300 overflow-hidden rounded border border-gray-300 bg-white shadow-sm">
                <button
                    type="button"
                    @click="open = !open"
                    class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 text-left transition-colors hover:bg-gray-50 hover:text-gray-900 focus:relative"
                    x-text="selected ? bulanList[selected] : '-- Pilih Bulan --'"
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
            </div>
        
            <input type="hidden" name="bulan" :value="selected">
        
            <div
                x-show="open"
                @click.away="open = false"
                role="menu"
                class="absolute z-10 mt-2 w-full max-h-60 overflow-auto rounded border border-gray-300 bg-white shadow-sm"
            >
                <!-- Opsi kosongkan pilihan -->
                <a href="#"
                    @click.prevent="selected = ''; open = false"
                    class="block px-3 py-2 text-sm font-medium text-red-500 hover:bg-red-50 hover:text-red-600 transition"
                    role="menuitem">
                    ❌ Kosongkan Pilihan
                </a>
        
                <!-- Daftar bulan -->
                <template x-for="(nama, angka) in bulanList" :key="angka">
                    <a href="#"
                        @click.prevent="selected = angka; open = false"
                        class="block px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-gray-900"
                        role="menuitem"
                        x-text="nama">
                    </a>
                </template>
            </div>
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
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('bulanDropdown', () => ({
            open: false,
            selected: '',
            bulanList: {
                1: 'Januari',
                2: 'Februari',
                3: 'Maret',
                4: 'April',
                5: 'Mei',
                6: 'Juni',
                7: 'Juli',
                8: 'Agustus',
                9: 'September',
                10: 'Oktober',
                11: 'November',
                12: 'Desember'
            }
        }));
    });
</script>
@endpush
