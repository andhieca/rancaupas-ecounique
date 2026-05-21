<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ranca Upas</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <!-- Vite Assets (Tailwind CSS + JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(20px, -30px) scale(1.1); }
            50% { transform: translate(-10px, 15px) scale(0.95); }
            75% { transform: translate(15px, 10px) scale(1.05); }
        }
        .animate-blob { animation: blob 8s infinite ease-in-out; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
    </style>
</head>

<body class="bg-forest-50 font-sans antialiased min-h-screen flex items-center justify-center relative overflow-hidden">

    <!-- Background Decor - hidden on very small screens for performance -->
    <div class="hidden sm:block absolute -top-20 -left-20 sm:-top-40 sm:-left-40 w-60 h-60 sm:w-96 sm:h-96 bg-forest-200 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
    <div class="hidden sm:block absolute -bottom-20 -right-20 sm:-bottom-40 sm:-right-40 w-60 h-60 sm:w-96 sm:h-96 bg-earth-500/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
    <div class="hidden sm:block absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-forest-300/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-4000"></div>

    <div class="relative w-full max-w-md px-4 sm:px-6 py-6 sm:py-0">
        <!-- Card -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl sm:rounded-3xl p-6 sm:p-8 md:p-10 shadow-2xl border border-white/40">
            <!-- Header with Logo -->
            <div class="text-center mb-6 sm:mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-forest-50 mb-3 sm:mb-4 shadow-inner border border-forest-100">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Ranca Upas" class="w-12 h-12 sm:w-16 sm:h-16 object-contain">
                </div>
                <h2 class="text-2xl sm:text-3xl font-display font-bold text-forest-900 tracking-tight">Login Akun</h2>
                <p class="text-xs sm:text-sm text-forest-500 mt-1 sm:mt-2">Wisata Ranca Upas</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-6">
                @csrf

                @if($errors->any())
                    <div class="bg-red-50 text-red-600 p-3 sm:p-4 rounded-xl border border-red-100 text-xs sm:text-sm flex items-start">
                        <i data-lucide="alert-circle" class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3 shrink-0 mt-0.5"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs sm:text-sm font-medium text-forest-800 mb-1.5 sm:mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="h-4 w-4 sm:h-5 sm:w-5 text-forest-400"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            value="{{ old('email') }}"
                            class="appearance-none block w-full pl-10 sm:pl-11 pr-4 py-2.5 sm:py-3 bg-forest-50 border border-forest-200 rounded-lg sm:rounded-xl text-sm sm:text-base text-forest-900 placeholder-forest-400 focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-transparent transition-all shadow-sm"
                            placeholder="nama@email.com">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs sm:text-sm font-medium text-forest-800 mb-1.5 sm:mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-4 w-4 sm:h-5 sm:w-5 text-forest-400"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="appearance-none block w-full pl-10 sm:pl-11 pr-4 py-2.5 sm:py-3 bg-forest-50 border border-forest-200 rounded-lg sm:rounded-xl text-sm sm:text-base text-forest-900 placeholder-forest-400 focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-transparent transition-all shadow-sm"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox"
                            class="h-4 w-4 text-forest-600 focus:ring-forest-500 border-forest-300 rounded cursor-pointer">
                        <label for="remember_me" class="ml-2 block text-xs sm:text-sm text-forest-600 cursor-pointer">
                            Ingat saya
                        </label>
                    </div>
                    <div class="text-xs sm:text-sm">
                        <a href="{{ route('password.request') }}" class="font-medium text-earth-600 hover:text-earth-500">
                            Lupa password?
                        </a>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full flex justify-center py-3 sm:py-3.5 px-4 border border-transparent rounded-lg sm:rounded-xl shadow-lg shadow-forest-600/30 text-sm sm:text-base font-semibold text-white bg-forest-600 hover:bg-forest-700 hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-forest-500 mt-2">
                    Masuk
                </button>
            </form>

            <!-- Register Link -->
            <div class="mt-5 sm:mt-6 pt-5 sm:pt-6 border-t border-gray-100 text-center">
                <p class="text-xs sm:text-sm text-forest-600">Belum punya akun pengunjung?</p>
                <a href="{{ route('register') }}"
                    class="text-earth-600 font-semibold hover:text-earth-700 transition-colors inline-block mt-1 text-sm">Daftar
                    Sekarang</a>
            </div>

            <!-- Back to Home -->
            <div class="mt-4 sm:mt-6 text-center">
                <a href="{{ route('home') }}"
                    class="text-xs sm:text-sm font-medium text-forest-500 hover:text-earth-600 transition-colors flex items-center justify-center">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>