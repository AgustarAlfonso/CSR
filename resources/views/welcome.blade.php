<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login CSR</title>
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

</body>
</html>
