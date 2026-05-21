<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wisata Ranca Upas</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <!-- Vite Assets (Tailwind CSS + JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-forest-50 text-gray-800 font-sans antialiased selection:bg-forest-500 selection:text-white">

    <!-- Navigation -->
    <header class="fixed w-full top-0 z-50 glass-panel">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 w-16">
                </div>
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="#beranda"
                        class="text-forest-800 hover:text-earth-600 font-medium transition-colors">Beranda</a>
                    <a href="#informasi-wisata"
                        class="text-forest-800 hover:text-earth-600 font-medium transition-colors">Informasi Wisata</a>

                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="/admin"
                                class="border flex items-center border-forest-800 text-forest-800 hover:bg-forest-800 hover:text-white px-5 py-1.5 rounded transition-all font-medium">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 mr-2"></i>Dashboard
                            </a>
                        @else
                            <a href="{{ route('visitor.dashboard') }}"
                                class="border flex items-center border-forest-800 text-forest-800 hover:bg-forest-800 hover:text-white px-5 py-1.5 rounded transition-all font-medium">
                                <i data-lucide="user" class="w-4 h-4 mr-2"></i>{{ auth()->user()->name }}
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                            class="border flex items-center border-forest-800 text-forest-800 hover:bg-forest-800 hover:text-white px-5 py-1.5 rounded transition-all font-medium">
                            <i data-lucide="log-in" class="w-4 h-4 mr-2"></i>Login Akses
                        </a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-forest-900 text-forest-100 py-12 mt-20 border-t border-forest-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 mb-8 text-left">
                <!-- Branding & Address -->
                <div class="col-span-1">
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Ranca Upas" class="h-10 w-10 opacity-90 bg-white/10 rounded-full p-1">
                        <h3 class="font-display font-semibold text-xl tracking-wide text-white">Ranca Upas</h3>
                    </div>
                    <p class="text-forest-300 text-sm leading-relaxed mb-4 flex items-start">
                        <i data-lucide="map-pin" class="w-4 h-4 mr-2 shrink-0 mt-1 text-forest-400"></i>
                        Jalan Raya Ciwidey - Patengan KM. 11, Alam Endah, Kecamatan Rancabali, Kabupaten Bandung, Jawa Barat.
                    </p>
                </div>

                <!-- Contact -->
                <div class="col-span-1">
                    <h4 class="text-white font-semibold mb-4 text-sm tracking-wider uppercase">Kontak Kami</h4>
                    <ul class="space-y-3 text-sm text-forest-300">
                        <li class="flex items-start">
                            <i data-lucide="phone" class="w-4 h-4 mr-3 mt-0.5 text-forest-400 shrink-0"></i>
                            <span>081234867880<br>081586967282</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="mail" class="w-4 h-4 mr-3 text-forest-400 shrink-0"></i>
                            <a href="mailto:rancaupasofficial03@gmail.com" class="hover:text-white transition-colors">rancaupasofficial03@gmail.com</a>
                        </li>
                    </ul>
                </div>

                <!-- Social Media -->
                <div class="col-span-1">
                    <h4 class="text-white font-semibold mb-4 text-sm tracking-wider uppercase">Sosial Media</h4>
                    <ul class="space-y-3 text-sm text-forest-300">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-3 text-forest-400 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                            </svg>
                            <a href="https://instagram.com/rancaupas.econique" target="_blank" class="hover:text-white transition-colors">@rancaupas.econique</a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-3 text-forest-400 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                            </svg>
                            <a href="https://tiktok.com/@rancaupas.econique" target="_blank" class="hover:text-white transition-colors">rancaupas.econique</a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-forest-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-forest-400">
                <p>&copy; <span id="tahun"></span> Wisata Kawasan Ranca Upas. All rights reserved.</p>
                <p>Designed with <i data-lucide="heart" class="w-3 h-3 inline-block text-red-500 mx-0.5 fill-red-500"></i> by Abelia Sapitri</p>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
        document.getElementById("tahun").innerHTML = new Date().getFullYear();
    </script>
</body>

</html>