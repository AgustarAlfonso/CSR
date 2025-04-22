<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login CSR</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <style>
        body {
            background-image: url('/images/background.jpg');
            background-size: cover;
            background-position: center;
        }

        .glass {
            backdrop-filter: blur(16px);
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 py-8">

    <div class="w-full max-w-md p-8 rounded-2xl glass shadow-xl text-white">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold">Masuk ke Aplikasi Kemitraan</h2>
            <p class="text-sm text-gray-200 mt-2">Silakan login untuk melanjutkan</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-100 mb-1">Email</label>
                <input type="email" name="email" id="email" required
                    class="w-full px-4 py-2 rounded-lg bg-white/80 text-gray-800 border border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-100 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 rounded-lg bg-white/80 text-gray-800 border border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-all duration-200 shadow-lg">
                Login
            </button>
        </form>
    </div>
    <div
    x-data="{ showModal: {{ $errors->any() ? 'true' : 'false' }} }"
    x-show="showModal"
    x-transition.opacity.duration.300ms
    class="fixed inset-0 flex items-center justify-center bg-black/40 z-50"
>
    <div
        @click.away="showModal = false"
        x-transition.scale.duration.300ms
        class="bg-white text-gray-800 rounded-2xl p-6 w-full max-w-sm shadow-2xl relative"
    >
        <div class="flex items-center space-x-3 mb-3">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h2 class="text-lg font-semibold text-red-600">Login Gagal</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">
            {{ $errors->first() }}
        </p>
        <div class="text-right">
            <button @click="showModal = false"
                    class="px-4 py-1.5 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm transition-all">
                Tutup
            </button>
        </div>
    </div>
</div>


</body>
</html>
