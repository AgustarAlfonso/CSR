@extends('layouts.master')

@section('content')
<div class="max-w-6xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-xl grid grid-cols-1 md:grid-cols-2 gap-12 transition-all duration-300">

{{-- KIRI - Info User --}}
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <div class="w-16 h-16 bg-blue-100 text-blue-600 flex items-center justify-center rounded-full text-3xl font-bold">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Halo, {{ $user->name }} 👋</h2>
            <p class="text-sm text-gray-500">Senang melihatmu kembali! Kelola informasi akunmu dengan mudah di sini.</p>
        </div>
    </div>

    <div class="bg-gradient-to-r from-blue-100 to-blue-50 border border-blue-200 rounded-xl p-6 shadow-sm space-y-3">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M5.121 17.804A13.937 13.937 0 0112 15c2.21 0 4.29.537 6.121 1.488M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="text-gray-700 font-medium">Nama Pengguna:</span>
        </div>
        <p class="text-lg text-gray-900 font-semibold">{{ $user->name }}</p>

        <div class="flex items-center gap-2 mt-4">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16 12A4 4 0 118 12a4 4 0 018 0z" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 14v4m0 0h4m-4 0H8" />
            </svg>
            <span class="text-gray-700 font-medium">Peran:</span>
        </div>
        <p class="text-blue-700 text-lg mt-1 font-semibold">
            @if ($user->isSuperadmin())
                👑 Superadmin
            @elseif ($user->isAdmin())
                🛠️ Admin
            @else
                👤 Pengguna
            @endif
        </p>
    </div>

    <div class="text-sm text-gray-400 pt-2">
        Terakhir diperbarui: {{ $user->updated_at->diffForHumans() }}
    </div>
</div>


    {{-- KANAN - Form Edit --}}
    <div class="space-y-10">

        {{-- Form Edit Profil --}}
        <div class="space-y-6">
            <h3 class="text-2xl font-semibold text-gray-800 border-b pb-3">Edit Profil</h3>

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <div class="relative">
                    <input type="text" id="nameInput" class="w-full border rounded-lg px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-300" value="{{ $user->name }}" disabled>
                    <button onclick="enableEdit('nameInput')" class="absolute right-2 top-2.5 text-blue-500 hover:text-blue-700 transition">
                        ✏️
                    </button>
                </div>
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <input type="email" id="emailInput" class="w-full border rounded-lg px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-300" value="{{ $user->email }}" disabled>
                    <button onclick="enableEdit('emailInput')" class="absolute right-2 top-2.5 text-blue-500 hover:text-blue-700 transition">
                        ✏️
                    </button>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div id="saveProfileWrapper" class="hidden">
                <button onclick="saveProfile()" class="bg-green-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-green-700 transition">
                    💾 Simpan Profil
                </button>
            </div>
        </div>

        {{-- Form Ganti Password --}}
        <div class="border-t pt-6 space-y-6">
            <h3 class="text-2xl font-semibold text-gray-800 border-b pb-3">Ganti Password</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" id="newPassword" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" id="confirmPassword" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>

            <button onclick="changePassword()" class="bg-yellow-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-yellow-700 transition">
                🔐 Ubah Password
            </button>
        </div>
    </div>
</div>

<script>
    function enableEdit(inputId) {
        const input = document.getElementById(inputId);
        input.disabled = false;
        input.classList.add('border-blue-400', 'ring-2', 'ring-blue-200');
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
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Profil berhasil diperbarui!');
            document.getElementById('nameInput').disabled = true;
            document.getElementById('emailInput').disabled = true;
            document.getElementById('saveProfileWrapper').classList.add('hidden');
        })
        .catch(() => alert('Terjadi kesalahan saat menyimpan profil.'));
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
        .then(response => response.json())
        .then(() => {
            alert('Password berhasil diperbarui!');
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
        })
        .catch(() => alert('Gagal memperbarui password.'));
    }
</script>
@endsection
