<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Pengunjung - SPK Ranca Upas</title>
    <!-- Vite Assets (Tailwind CSS + JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-forest-50 font-sans antialiased min-h-screen flex items-center justify-center relative overflow-x-hidden py-10">
    
    <!-- Background Decor -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-forest-200 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-earth-500/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>

    <div class="relative w-full max-w-md px-6">
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl p-8 sm:p-10 shadow-2xl border border-white/40">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-forest-100 mb-4 shadow-inner">
                    <i data-lucide="user-plus" class="w-8 h-8 text-forest-600"></i>
                </div>
                <h2 class="text-3xl font-display font-bold text-forest-900 tracking-tight">Daftar Akun</h2>
                <p class="text-sm text-forest-500 mt-2">Dapatkan akses ke fitur rekomendasi SAW</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                
                @if($errors->any())
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl border border-red-100 text-sm flex items-start">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-3 shrink-0 mt-0.5"></i>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="name" class="block text-sm font-medium text-forest-800 mb-2">Nama Lengkap</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="user" class="h-5 w-5 text-forest-400"></i>
                        </div>
                        <input id="name" name="name" type="text" required value="{{ old('name') }}"
                            class="appearance-none block w-full pl-11 pr-4 py-3 bg-forest-50 border border-forest-200 rounded-xl text-forest-900 placeholder-forest-400 focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-transparent transition-all shadow-sm"
                            placeholder="Tulis nama anda">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-forest-800 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="h-5 w-5 text-forest-400"></i>
                        </div>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}"
                            class="appearance-none block w-full pl-11 pr-4 py-3 bg-forest-50 border border-forest-200 rounded-xl text-forest-900 placeholder-forest-400 focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-transparent transition-all shadow-sm"
                            placeholder="nama@email.com">
                    </div>
                </div>

                <div>
                    <label for="origin" class="block text-sm font-medium text-forest-800 mb-2">Asal Pengunjung</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="map" class="h-5 w-5 text-forest-400"></i>
                        </div>
                        <select id="origin" name="origin" required
                            class="appearance-none block w-full pl-11 pr-4 py-3 bg-forest-50 border border-forest-200 rounded-xl text-forest-900 placeholder-forest-400 focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-transparent transition-all shadow-sm">
                            <option value="domestik" {{ old('origin') == 'domestik' ? 'selected' : '' }}>Domestik (WNI)</option>
                            <option value="mancanegara" {{ old('origin') == 'mancanegara' ? 'selected' : '' }}>Mancanegara (WNA)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-forest-800 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-5 w-5 text-forest-400"></i>
                        </div>
                        <input id="password" name="password" type="password" required
                            class="appearance-none block w-full pl-11 pr-4 py-3 bg-forest-50 border border-forest-200 rounded-xl text-forest-900 placeholder-forest-400 focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-transparent transition-all shadow-sm"
                            placeholder="Minimal 6 karakter">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-forest-800 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-5 w-5 text-forest-400"></i>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="appearance-none block w-full pl-11 pr-4 py-3 bg-forest-50 border border-forest-200 rounded-xl text-forest-900 placeholder-forest-400 focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-transparent transition-all shadow-sm"
                            placeholder="Ulangi password">
                    </div>
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-forest-600/30 text-base font-semibold text-white bg-forest-600 hover:bg-forest-700 hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-forest-500 mt-6">
                    Buat Akun
                </button>
            </form>
            
            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <p class="text-sm text-forest-600">Sudah punya akun?</p>
                <a href="{{ route('login') }}" class="text-earth-600 font-semibold hover:text-earth-700 transition-colors inline-block mt-1">Masuk Sekarang</a>
            </div>
            
            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-sm font-medium text-forest-500 hover:text-earth-600 transition-colors flex items-center justify-center">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
