<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - POS MLK</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-soft-green">
    <div class="min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md">
            <!-- Brand Section -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center mb-4">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-accent-green to-emerald-600 flex items-center justify-center text-white font-bold text-2xl">
                        POS
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-dark-gray">POS MLK</h1>
                <p class="text-gray-600 mt-2">Sistem Penjualan Terpadu</p>
            </div>

            <!-- Login Card -->
            <div class="card-elevated">
                <h2 class="text-2xl font-bold mb-6 text-center">Masuk ke Sistem</h2>

                @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h3 class="font-bold text-red-800 mb-2">Terjadi Kesalahan</h3>
                    <ul class="text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 font-medium">
                    ✓ {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('auth.login') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input 
                            type="email" 
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Contoh: owner@pos-mlk.test"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-accent-green focus:outline-none transition-colors @error('email') border-red-500 @enderror"
                        />
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input 
                            type="password" 
                            name="password"
                            placeholder="Masukkan password Anda"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-accent-green focus:outline-none transition-colors @error('password') border-red-500 @enderror"
                        />
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="remember" 
                            name="remember"
                            class="w-4 h-4 border-gray-300 rounded text-accent-green focus:ring-accent-green"
                        />
                        <label for="remember" class="ml-2 text-sm text-gray-600">Ingat saya</label>
                    </div>

                    <!-- Login Button -->
                    <button 
                        type="submit"
                        class="w-full btn btn-primary text-lg font-bold py-3 mt-6 no-underline"
                    >
                        ✓ Masuk
                    </button>
                </form>

                <!-- Info Section -->
                <div class="mt-6 pt-6 border-t-2 border-gray-200">
                    <p class="text-center text-sm text-gray-600">Akun Demo:</p>
                    <div class="mt-3 space-y-2 text-xs text-gray-600 bg-gray-50 p-3 rounded-lg">
                        <p><strong>Email:</strong> owner@pos-mlk.test</p>
                        <p><strong>Password:</strong> Gunakan password demo yang diberikan</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-sm text-gray-500">
                © {{ date('Y') }} POS MLK — Sistem Penjualan Profesional
            </div>
        </div>
    </div>
</body>
</html>
