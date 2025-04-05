@extends('layouts.master')

@section('title', 'Edit Anggaran')

@section('content')
<div class="container mx-auto mt-10 p-6 bg-white shadow rounded-lg">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Edit Anggaran CSR</h3>

    <form method="POST" action="{{ route('anggaran.update', $anggaran->id) }}">
        @csrf
        @method('PUT')

<!-- Ganti input pemegang_saham jadi dropdown -->
<div class="mb-4">
    <label for="pemegang_saham" class="block text-sm font-medium text-gray-700">Pemegang Saham</label>
    <select name="pemegang_saham" id="pemegang_saham" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <option value="">-- Pilih Pemegang Saham --</option>
        @foreach([
            'Kab. Kepulauan Anambas','Kab. Indragiri Hulu','Kota Batam','Kab. Indragiri Hilir','Provinsi Riau','Kab. Kampar','Kab. Bintan',
            'Kab. Bengkalis','Kab. Rokan Hilir','Kab. Meranti','Kab. Natuna','Kab. Siak','Kab. Pelalawan','Kota Dumai','Kota Pekanbaru',
            'Provinsi Kepulauan Riau','Kab. Rokan Hulu','Kab. Lingga','Kab. Karimun','Kota Tanjung Pinang','Kab. Kuansing'
        ] as $ps)
            <option value="{{ $ps }}" {{ $anggaran->pemegang_saham == $ps ? 'selected' : '' }}>{{ $ps }}</option>
        @endforeach
    </select>
</div>

<!-- Bulan (opsional) -->
<div class="mb-4">
    <label for="bulan" class="block text-sm font-medium text-gray-700">Bulan <span class="text-gray-400 text-sm">(Opsional)</span></label>
    <input type="text" name="bulan" id="bulan" value="{{ $anggaran->bulan }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
</div>


        <div class="mb-4">
            <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
            <input type="number" name="tahun" id="tahun" value="{{ $anggaran->tahun }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div class="mb-4">
            <label for="jumlah_anggaran" class="block text-sm font-medium text-gray-700">Jumlah Anggaran (Rp)</label>
            <input type="number" name="jumlah_anggaran" id="jumlah_anggaran" value="{{ $anggaran->jumlah_anggaran }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">Update</button>
        <a href="{{ route('anggaran.index') }}" class="ml-2 text-gray-600 hover:underline">Batal</a>
    </form>
</div>
@endsection
