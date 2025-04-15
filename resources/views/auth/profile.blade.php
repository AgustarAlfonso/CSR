@extends('layouts.master')

@section('content')
<div class="max-w-6xl mx-auto mt-10 p-6 bg-gray-100 rounded-lg shadow-inner grid grid-cols-1 md:grid-cols-2 gap-10">

    {{-- KIRI - User Info --}}
    <div class="flex flex-col items-center md:items-start text-center md:text-left space-y-4">
        {{-- Avatar --}}
        <div class="w-32 h-32 rounded-full bg-blue-200 text-blue-800 flex items-center justify-center text-5xl font-bold shadow-md">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        <div>
            <h2 class="text-3xl font-bold text-gray-800">Halo, <span class="text-blue-600">{{ $user->name }}</span> 👋</h2>
            <p class="text-gray-600 mt-1">Selamat datang kembali di halaman profilmu.</p>
        </div>

        <div class="mt-4 p-4 w-full bg-white border border-gray-200 rounded-lg shadow-sm">
            <p class="font-semibold text-gray-700">Peran:</p>
            <p class="text-blue-700 mt-1">
                @if ($user->isSuperadmin())
                    👑 Superadmin
                @elseif ($user->isAdmin())
                    🛠️ Admin
                @else
                    👤 Pengguna
                @endif
            </p>
        </div>
    </div>

    {{-- KANAN - Edit Form --}}
    <div class="space-y-6">
        {{-- Edit Info --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">📝 Edit Informasi</h3>

            {{-- Nama --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <div class="relative">
                    <input type="text" id="nameInput" class="peer w-full border-gray-300 rounded px-4 py-2 pr-10 transition focus:outline-none focus:ring focus:border-blue-300" value="{{ $user->name }}" disabled>
                    <button onclick="enableEdit('nameInput')" class="absolute right-2 top-2 text-blue-500 hover:text-blue-700">
                        <i class="fas fa-pen"></i>
                    </button>
                </div>
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <input type="email" id="emailInput" class="peer w-full border-gray-300 rounded px-4 py-2 pr-10 transition focus:outline-none focus:ring focus:border-blue-300" value="{{ $user->email }}" disabled>
                    <button onclick="enableEdit('emailInput')" class="absolute right-2 top-2 text-blue-500 hover:text-blue-700">
                        <i class="fas fa-pen"></i>
                    </button>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div id="saveProfileWrapper" class="hidden mt-4 text-right">
                <button onclick="saveProfile()" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 transition">
                    💾 Simpan Perubahan
                </button>
            </div>
        </div>

        {{-- Ganti Password --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">🔐 Ganti Password</h3>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" id="newPassword" placeholder="Minimal 6 karakter" class="w-full border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" id="confirmPassword" placeholder="Ketik ulang password" class="w-full border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <div class="text-right">
                <button onclick="changePassword()" class="bg-yellow-500 text-white px-4 py-2 rounded shadow hover:bg-yellow-600 transition">
                    🔄 Ubah Password
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Script --}}
<script>
    function enableEdit(id) {
        const input = document.getElementById(id);
        input.disabled = false;
        input.classList.add('border-blue-400', 'ring-1', 'ring-blue-200');
        document.getElementById('saveProfileWrapper').classList.remove('hidden');
    }

    function saveProfile() {
        const name = document.getElementById('nameInput').value;
        const email = document.getElementById('emailInput').value;

        fetch('{{ route("auth.profile.update") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name, email })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            document.getElementById('nameInput').disabled = true;
            document.getElementById('emailInput').disabled = true;
            document.getElementById('saveProfileWrapper').classList.add('hidden');
        });
    }

    function changePassword() {
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (newPassword !== confirmPassword) {
            alert('Password tidak cocok.');
            return;
        }

        fetch('{{ route("auth.profile.update") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ password: newPassword })
        })
        .then(res => res.json())
        .then(data => {
            alert('Password berhasil diperbarui!');
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
        });
    }
</script>
@endsection
