@extends('layouts.master')

@section('title', 'Edit Akun')

@section('content')
<div class="max-w-2xl mx-auto mt-12 p-8 bg-white shadow-lg rounded-2xl border border-gray-200">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">✏️ Edit Akun</h2>

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

    <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <!-- Nama -->
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" id="name" required value="{{ old('name', $user->name) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
            <input type="email" name="email" id="email" required value="{{ old('email', $user->email) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password Baru (opsional)</label>
            <input type="password" name="password" id="password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="Biarkan kosong jika tidak diubah">
        </div>

        <!-- Konfirmasi Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="Ulangi password baru">
        </div>

        <!-- Role -->
        <div x-data="{
            open: false,
            selected: '{{ old('role', $user->role) }}',
            selectedLabel: '{{ ['1' => 'Superadmin', '2' => 'Admin', '3' => 'User'][$user->role] }}',
            search: ''
        }" class="relative mt-3">
            <label for="role" class="block text-sm font-semibold text-gray-700 mb-1">Role</label>

            <div class="relative inline-flex w-full">
                <span class="inline-flex w-full divide-x divide-gray-300 overflow-hidden rounded border border-gray-300 bg-white shadow-sm">
                    <button
                        type="button"
                        @click="open = !open"
                        class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 text-left transition-colors hover:bg-gray-50 hover:text-gray-900 focus:relative"
                        x-text="selectedLabel || '-- Pilih Role --'"
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

                <input type="hidden" name="role" :value="selected" required>

                <div
                    x-show="open"
                    @click.away="open = false"
                    role="menu"
                    class="absolute z-10 mt-2 w-full max-h-60 overflow-auto rounded border border-gray-300 bg-white shadow-sm"
                >
                    <div class="p-2 border-b border-gray-200">
                        <input type="text" x-model="search" placeholder="Cari role..."
                               class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    @php
                        $roles = [
                            '1' => 'Superadmin',
                            '2' => 'Admin',
                            '3' => 'User'
                        ];
                    @endphp

                    @foreach($roles as $value => $label)
                        <a href="#"
                           @click.prevent="selected = '{{ $value }}'; selectedLabel = '{{ $label }}'; open = false"
                           x-show="search === '' || '{{ strtolower($label) }}'.includes(search.toLowerCase())"
                           class="block px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-gray-900"
                           role="menuitem">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-4">
            <button type="submit"
                class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg transition shadow">
                Update
            </button>
            <a href="{{ route('auth.kelola') }}" class="text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
</div>
@endsection
