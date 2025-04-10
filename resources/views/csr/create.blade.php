@extends('layouts.master')

@section('title', 'Tambah Program CSR')

@section('content')

@if (session('csr_error'))
<div 
    x-data="{ open: true }" 
    x-show="open"
    x-transition 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
>
    <div 
        @click.away="open = false" 
        class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6"
    >
        <h2 class="text-xl font-bold text-red-600 mb-4">Gagal Menyimpan Program CSR</h2>

        <p class="text-gray-700 mb-4">
            <strong>Alasan:</strong> {{ session('csr_error') }}
        </p>

        @if (session('request_data'))
        <ul class="text-sm text-gray-600 mb-4">
            <li><strong>Pemegang Saham:</strong> {{ session('request_data')['pemegang_saham'] }}</li>
            <li><strong>Tahun:</strong> {{ session('request_data')['tahun'] }}</li>
            <li><strong>Realisasi Diinput:</strong> Rp {{ number_format(session('request_data')['realisasi_csr'], 0, ',', '.') }}</li>
        </ul>
        @endif

        <div class="flex justify-end pt-4">
            <button 
                @click="open = false" 
                class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                Kembali
            </button>
        </div>
    </div>
</div>
@endif

@if ($errors->any())
<div 
    x-data="{ open: true }" 
    x-show="open"
    x-transition 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
>
    <div 
        @click.away="open = false" 
        class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6"
    >
        <h2 class="text-xl font-bold text-red-600 mb-4">Terjadi Kesalahan Validasi</h2>

        <p class="text-gray-700 mb-4">
            Mohon periksa kembali inputan kamu. Berikut detail error yang ditemukan:
        </p>

        <ul class="list-disc list-inside text-sm text-gray-600 mb-4 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

        <div class="flex justify-end pt-4">
            <button 
                @click="open = false" 
                class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                Oke, Saya Mengerti
            </button>
        </div>
    </div>
</div>
@endif



<div class="max-w-5xl mx-auto mt-12 bg-white shadow-lg rounded-2xl border border-gray-200 p-8" x-data="formCSR()">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">➕ Tambah Program CSR</h2>

    <div class="grid md:grid-cols-3 gap-6">
        <!-- Form Input -->
        <form method="POST" action="{{ route('csr.store') }}" class="space-y-5 md:col-span-2">
            @csrf

            <!-- Nama Program -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Program</label>
                <input type="text" name="nama_program" class="w-full px-4 py-2 border rounded-lg" required>
            </div>

            <!-- Pemegang Saham -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Pemegang Saham</label>
<!-- Pemegang Saham -->
<div class="relative" x-data="{ open: false }">


    <div class="relative inline-flex w-full">
        <span class="inline-flex w-full divide-x divide-gray-300 overflow-hidden rounded border border-gray-300 bg-white shadow-sm">
            <button
                type="button"
                @click="open = !open"
                class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 text-left transition-colors hover:bg-gray-50 hover:text-gray-900 focus:relative"
                x-text="pemegang_saham || '-- Pilih Pemegang Saham --'">
            </button>

            <button
                type="button"
                @click="open = !open"
                aria-label="Menu"
                class="px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-gray-900 focus:relative">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
        </span>

        <!-- Hidden input for form submission -->
        <input type="hidden" name="pemegang_saham" :value="pemegang_saham" required>

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
                    @click.prevent="pemegang_saham = '{{ $ps }}'; open = false; cekSisaAnggaran()"
                    class="block px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-gray-900"
                    role="menuitem">
                    {{ $ps }}
                </a>
            @endforeach
        </div>
    </div>
</div>

            </div>

            <!-- Tahun -->
            <div>
                <div x-data="{ open: false, selectedTahun: '' }" class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun</label>
                    <div class="relative inline-flex w-full">
                        <span class="inline-flex w-full divide-x divide-gray-300 overflow-hidden rounded border bg-white shadow-sm">
                            <button type="button" @click="open = !open"
                                    class="flex-1 px-3 py-2 text-sm text-left text-gray-700 hover:bg-gray-50"
                                    x-text="selectedTahun || '-- Pilih Tahun --'"></button>
                            <button type="button" @click="open = !open" class="px-3 py-2 text-sm text-gray-700">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </span>
                        <input type="hidden" name="tahun" :value="selectedTahun" required>
                        <div x-show="open" @click.away="open = false"
                             class="absolute z-10 mt-2 w-full max-h-60 overflow-auto rounded border bg-white shadow-sm">
                            @foreach($availableYears as $year)
                                <a href="#" @click.prevent="selectedTahun = '{{ $year }}'; tahun = '{{ $year }}'; cekSisaAnggaran(); open = false"
                                   class="block px-3 py-2 text-sm hover:bg-gray-50">
                                    {{ $year }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                
            </div>

            <!-- Bidang Kegiatan -->
            <div>
               <div x-data="{ open: false, selectedBidang: '' }" class="relative">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Bidang Kegiatan</label>
    <div class="relative inline-flex w-full">
        <span class="inline-flex w-full divide-x divide-gray-300 overflow-hidden rounded border border-gray-300 bg-white shadow-sm">
            <button
                type="button"
                @click="open = !open"
                class="flex-1 px-3 py-2 text-sm text-left text-gray-700 hover:bg-gray-50"
                x-text="selectedBidang || '-- Pilih Bidang --'"
            ></button>
            <button type="button" @click="open = !open" class="px-3 py-2 text-sm text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
            </button>
        </span>
        <input type="hidden" name="bidang_kegiatan" :value="selectedBidang" required>
        <div x-show="open" @click.away="open = false"
             class="absolute z-10 mt-2 w-full max-h-60 overflow-auto rounded border bg-white shadow-sm">
            @foreach([
                'Pendidikan','Keagamaan','Kesejahteraan Sosial','Penghargaan','Olahraga','Seni & Budaya',
                'Kewirausahaan','Dukungan Kegiatan Pemerintah','Lingkungan','Kesehatan','Bencana Alam','Sosial'
            ] as $bidang)
                <a href="#" @click.prevent="selectedBidang = '{{ $bidang }}'; open = false"
                   class="block px-3 py-2 text-sm hover:bg-gray-50">
                    {{ $bidang }}
                </a>
            @endforeach
        </div>
    </div>
</div>

            </div>

            <!-- Bulan -->
            <div>
                <div x-data="{ open: false, selectedBulan: '' }" class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bulan</label>
                    <div class="relative inline-flex w-full">
                        <span class="inline-flex w-full divide-x divide-gray-300 overflow-hidden rounded border bg-white shadow-sm">
                            <button type="button" @click="open = !open"
                                    class="flex-1 px-3 py-2 text-sm text-left text-gray-700 hover:bg-gray-50"
                                    x-text="selectedBulan || '-- Pilih Bulan --'"></button>
                            <button type="button" @click="open = !open" class="px-3 py-2 text-sm text-gray-700">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </span>
                        <input type="hidden" name="bulan" :value="selectedBulan" required>
                        <div x-show="open" @click.away="open = false"
                             class="absolute z-10 mt-2 w-full max-h-60 overflow-auto rounded border bg-white shadow-sm">
                            @foreach(range(1, 12) as $b)
                                <a href="#" @click.prevent="selectedBulan = '{{ $b }}'; open = false"
                                   class="block px-3 py-2 text-sm hover:bg-gray-50">
                                    {{ DateTime::createFromFormat('!m', $b)->format('F') }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                
            </div>

            <!-- Realisasi -->
<!-- Realisasi -->
<div x-data="{ realisasi_csr: '', formatted: '' }" x-init="$watch('realisasi_csr', value => {
    let angka = value.replace(/\D/g, '');
    formatted = new Intl.NumberFormat('id-ID').format(angka);
})">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Realisasi Dana (Rp)</label>
    
    <input type="text" 
        name="realisasi_csr"
        x-model="realisasi_csr"
        class="w-full px-4 py-2 border rounded-lg" 
        required
        @input="realisasi_csr = $event.target.value.replace(/\D/g, '')" 
        :value="formatted">
</div>


            <!-- Keterangan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                <textarea name="ket" rows="3" class="w-full px-4 py-2 border rounded-lg"></textarea>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center pt-4">
                <button type="submit"
                    class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition">
                    Simpan
                </button>
                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:underline">Batal</a>
            </div>
        </form>

        <!-- Info Box -->
<!-- Info Box -->
<div 
    class="rounded-xl p-5 shadow-sm h-fit border"
    x-show="true"
    :class="(realisasi_csr && parseInt(realisasi_csr) > sisaAnggaran) ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50'"
>
    <h3 class="text-lg font-semibold mb-2"
        :class="(realisasi_csr && parseInt(realisasi_csr) > sisaAnggaran) ? 'text-red-800' : 'text-gray-800'">
        ℹ️ Info Anggaran
    </h3>

    <template x-if="!(pemegang_saham && tahun)">
        <p class="text-sm text-gray-500">Silakan pilih pemegang saham dan tahun terlebih dahulu untuk melihat sisa anggaran.</p>
    </template>
    
    <template x-if="pemegang_saham && tahun && sisaAnggaran !== null">
        <div>
            <p class="text-sm" :class="(realisasi_csr && parseInt(realisasi_csr) > sisaAnggaran) ? 'text-red-700' : 'text-gray-700'">
                Pemegang Saham: <span class="font-medium" x-text="pemegang_saham"></span>
            </p>
            <p class="text-sm" :class="(realisasi_csr && parseInt(realisasi_csr) > sisaAnggaran) ? 'text-red-700' : 'text-gray-700'">
                Tahun: <span class="font-medium" x-text="tahun"></span>
            </p>

            <template x-if="isFallback">
                <p class="text-xs text-yellow-600 bg-yellow-100 inline-block px-2 py-1 rounded mt-1">
                    ⚠️ Data anggaran dari tahun sebelumnya
                </p>
            </template>
            
    
            <div class="mt-4 p-4 rounded-lg shadow-sm border"
                :class="(realisasi_csr && parseInt(realisasi_csr) > sisaAnggaran) ? 'border-red-400 bg-red-100' : 'border-green-300 bg-white'">
                <template x-if="realisasi_csr && parseInt(realisasi_csr) > sisaAnggaran">
                    <p class="text-sm font-semibold text-red-700">
                        ⚠️ Realisasi melebihi sisa anggaran! Harap periksa kembali.
                    </p>
                </template>
    
                <template x-if="!(realisasi_csr && parseInt(realisasi_csr) > sisaAnggaran)">
                    <div>
                        <p class="text-sm text-gray-600">Sisa anggaran yang tersedia:</p>
                        <p class="text-xl font-bold text-green-700 mt-1" x-text="formatRupiah(sisaAnggaran)"></p>
                    </div>
                </template>
            </div>
        </div>
    </template>
    
</div>


    </div>
</div>

<script>
function formCSR() {
    return {
        pemegang_saham: '',
        tahun: '',
        selectedBulan: '',
        selectedBidang: '',
        selectedTahun: '',
        sisaAnggaran: null,
        isFallback: false,
        realisasi_csr: '',
        openDropdown: {
            pemegang: false,
            tahun: false,
            bulan: false,
            bidang: false
        },
        formatRupiah(angka) {
            if (angka === null || isNaN(angka)) return 'Rp 0';
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },
        cekSisaAnggaran() {
    if (this.pemegang_saham && this.tahun) {
        fetch(`{{ route('csr.sisa-anggaran') }}?pemegang_saham=${this.pemegang_saham}&tahun=${this.tahun}`)
            .then(res => res.json())
            .then(data => {
                this.sisaAnggaran = data.sisa;
                this.isFallback = data.fallback ?? false;
            })
            .catch(err => {
                console.error(err);
                this.sisaAnggaran = null;
                this.isFallback = false;
            });
    }
}

    }
}

</script>

@endsection