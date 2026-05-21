<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Pengunjung - Wisata Ranca Upas</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <!-- Vite Assets (Tailwind CSS + JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        window._tourismData = @json($tourisms);
        window._basePath = '{{ url('/') }}';
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-forest-50 text-gray-800 font-sans antialiased selection:bg-forest-500 selection:text-white overflow-x-hidden" x-data="visitorDashboard()">

    <!-- Header / Navbar -->
    <header class="fixed w-full top-0 z-50 glass-panel">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20">
                <div class="flex items-center space-x-2 sm:space-x-3 shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center group">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Ranca Upas" class="h-9 w-9 sm:h-11 sm:w-11 object-contain">
                    </a>
                    <span class="hidden sm:block text-forest-200 text-lg font-light shrink-0">|</span>
                    <span class="hidden md:block text-forest-700 font-semibold text-sm tracking-wide truncate">Dashboard Pengunjung</span>
                </div>
                <nav class="flex items-center space-x-2 sm:space-x-5 min-w-0">
                    <a href="{{ route('home') }}" class="text-forest-700 hover:text-earth-600 font-medium transition-colors hidden md:flex items-center text-sm shrink-0">
                        <i data-lucide="home" class="w-4 h-4 mr-1.5"></i>Beranda
                    </a>
                    <div class="hidden md:block w-px h-5 bg-forest-200 shrink-0"></div>
                    <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                        <a href="{{ route('visitor.dashboard') }}?tab=profil" class="text-forest-700 hover:text-earth-600 font-medium hidden sm:flex items-center text-sm transition-colors truncate max-w-[120px] lg:max-w-[200px]">
                            <i data-lucide="user" class="w-4 h-4 mr-1.5 text-forest-400 shrink-0"></i><span class="truncate">{{ auth()->user()->name }}</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-red-500 hover:text-red-700 font-medium flex items-center transition-colors bg-red-50 hover:bg-red-100 px-2.5 sm:px-3 py-1.5 rounded-lg text-sm">
                                <i data-lucide="log-out" class="w-4 h-4 sm:mr-1.5"></i><span class="hidden sm:inline">Keluar</span>
                            </button>
                        </form>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen pt-20 sm:pt-28 pb-20">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">

            <!-- Welcome Banner -->
            <div class="mb-6 sm:mb-8 bg-gradient-to-br from-forest-600 via-forest-700 to-forest-800 rounded-2xl sm:rounded-3xl p-5 sm:p-8 md:p-10 lg:p-12 relative overflow-hidden shadow-2xl shadow-forest-900/20">
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-72 h-72 bg-white/5 rounded-full"></div>
                <div class="absolute bottom-0 left-1/3 -mb-20 w-56 h-56 bg-earth-500/10 rounded-full"></div>
                <div class="absolute top-1/2 right-1/4 w-32 h-32 bg-white/[0.03] rounded-full"></div>
                <div class="relative z-10">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-display font-bold text-white mb-2 sm:mb-3">
                        Selamat Datang, {{ auth()->user()->name }}! 👋
                    </h1>
                    <p class="text-sm sm:text-base lg:text-lg max-w-3xl leading-relaxed text-white mt-2">
                        Jelajahi dan Temukan paket wisata terbaik kami dan buat liburan anda tak terlupakan.
                    </p>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="mb-6 sm:mb-8 overflow-x-auto pb-2 -mx-3 px-3 sm:mx-0 sm:px-0 scrollbar-hide">
                <div class="inline-flex bg-white rounded-xl sm:rounded-2xl shadow-md border border-forest-100 p-1 sm:p-1.5 w-max">
                    <button @click="activeTab = 'katalog'" :class="activeTab === 'katalog' ? 'bg-forest-600 text-white shadow-lg shadow-forest-600/30' : 'text-forest-600 hover:bg-forest-50'" class="flex items-center px-4 sm:px-7 py-2.5 sm:py-3 text-xs sm:text-sm rounded-lg sm:rounded-xl font-semibold transition-all duration-300 justify-center whitespace-nowrap">
                        <i data-lucide="layout-grid" class="w-4 h-4 sm:w-[18px] sm:h-[18px] mr-1.5 sm:mr-2 shrink-0"></i> Katalog Wisata
                    </button>
                    <button @click="activeTab = 'rekomendasi'" :class="activeTab === 'rekomendasi' ? 'bg-forest-600 text-white shadow-lg shadow-forest-600/30' : 'text-forest-600 hover:bg-forest-50'" class="flex items-center px-4 sm:px-7 py-2.5 sm:py-3 text-xs sm:text-sm rounded-lg sm:rounded-xl font-semibold transition-all duration-300 justify-center whitespace-nowrap">
                        <i data-lucide="sparkles" class="w-4 h-4 sm:w-[18px] sm:h-[18px] mr-1.5 sm:mr-2 shrink-0"></i> Rekomendasi
                    </button>
                    <button @click="activeTab = 'profil'" :class="activeTab === 'profil' ? 'bg-forest-600 text-white shadow-lg shadow-forest-600/30' : 'text-forest-600 hover:bg-forest-50'" class="flex items-center px-4 sm:px-7 py-2.5 sm:py-3 text-xs sm:text-sm rounded-lg sm:rounded-xl font-semibold transition-all duration-300 justify-center whitespace-nowrap">
                        <i data-lucide="user-cog" class="w-4 h-4 sm:w-[18px] sm:h-[18px] mr-1.5 sm:mr-2 shrink-0"></i> Profil
                    </button>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 1: KATALOG WISATA                          -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'katalog'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

                <!-- Filter & Search Bar -->
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-md border border-forest-100 p-3 sm:p-5 mb-6 sm:mb-8">
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-3 sm:gap-4">
                        <!-- Search -->
                        <div class="relative flex-1 w-full min-w-0">
                            <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 sm:w-5 sm:h-5 text-forest-400 pointer-events-none"></i>
                            <input x-model="searchQuery" type="text" placeholder="Cari wisata berdasarkan nama..."
                                class="w-full pl-11 sm:pl-12 pr-3 sm:pr-4 py-2.5 sm:py-3 bg-forest-50/50 border border-forest-200 rounded-xl text-sm text-forest-900 placeholder-forest-400 focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-forest-500 transition-all">
                        </div>
                        <!-- Sort -->
                        <div class="flex items-center space-x-2 w-full md:w-auto min-w-0">
                            <label class="text-xs sm:text-sm font-medium text-forest-600 whitespace-nowrap shrink-0">
                                <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 sm:w-4 sm:h-4 inline mr-1 -mt-0.5"></i>Urutkan:
                            </label>
                            <select x-model="sortBy" class="bg-forest-50/50 border border-forest-200 text-forest-800 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 text-sm focus:outline-none focus:ring-2 focus:ring-forest-500 font-medium flex-1 sm:flex-auto min-w-0">
                                <option value="rating">⭐ Rating Tertinggi</option>
                                <option value="popular">🔥 Terpopuler</option>
                                <option value="price_low">💰 Harga Termurah</option>
                                <option value="price_high">💎 Harga Termahal</option>
                                <option value="name">🔤 Nama A-Z</option>
                            </select>
                        </div>
                    </div>
                    <!-- Results count -->
                    <div class="mt-3 flex items-center text-xs sm:text-sm text-forest-500">
                        <i data-lucide="info" class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5"></i>
                        Menampilkan <span class="font-bold text-forest-700 mx-1" x-text="sortedTourisms.length"></span> destinasi wisata
                    </div>
                </div>

                <!-- Grid Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 xl:gap-8">
                    <template x-for="(wisata, idx) in sortedTourisms" :key="wisata.id">
                        <div class="bg-white rounded-2xl sm:rounded-[2rem] border border-forest-100 shadow-md hover:shadow-2xl hover:shadow-forest-900/10 flex flex-col justify-between transform hover:-translate-y-3 transition-all duration-500 group relative overflow-hidden cursor-pointer" @click="openDetail(wisata.id)">
                            <!-- Image -->
                            <div class="relative w-full shrink-0 overflow-hidden rounded-t-2xl sm:rounded-t-[2rem]" style="height: 200px;">
                                <img :src="getImageUrl(wisata.image)" :alt="wisata.name" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-forest-950/60 via-transparent to-transparent"></div>
                                <!-- Category Badge -->
                                <span x-show="wisata.category" x-text="wisata.category" class="absolute top-3 left-3 sm:top-4 sm:left-4 bg-forest-600/80 backdrop-blur text-white text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full"></span>
                                <!-- Rank Badge -->
                                <div class="absolute bottom-3 left-3 sm:bottom-4 sm:left-4 flex items-center space-x-1.5 sm:space-x-2">
                                    <span class="inline-flex items-center bg-white/90 backdrop-blur-sm px-2 sm:px-3 py-1 rounded-full text-[11px] sm:text-xs font-bold text-forest-800 shadow-sm">
                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 mr-1 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                        <span x-text="wisata.ratings_avg_rating ? Number(wisata.ratings_avg_rating).toFixed(1) : '0.0'"></span>
                                    </span>
                                    <span class="inline-flex items-center bg-white/90 backdrop-blur-sm px-2 sm:px-3 py-1 rounded-full text-[11px] sm:text-xs font-bold text-forest-600 shadow-sm">
                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 mr-1 text-forest-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        <span x-text="wisata.ratings_count || 0"></span> ulasan
                                    </span>
                                </div>
                            </div>
                            <!-- Content -->
                            <div class="p-4 sm:p-5 flex flex-col flex-1">
                                <h3 class="text-base sm:text-lg font-display font-bold text-forest-900 mb-1.5 group-hover:text-forest-700 transition-colors line-clamp-1" x-text="wisata.name"></h3>
                                <p class="text-forest-500 text-sm leading-relaxed mb-4 line-clamp-2" x-text="wisata.description || 'Belum ada deskripsi.'"></p>

                                <!-- Price & Star Rating -->
                                <div class="mt-auto flex items-center justify-between">
                                    <div class="inline-flex items-center bg-forest-50 border border-forest-200 px-3 py-1.5 rounded-full text-xs sm:text-sm font-bold text-forest-800">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5 text-earth-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                        Rp <span x-text="formatPrice(wisata.price_wni)"></span>
                                    </div>
                                    <div class="flex items-center space-x-0.5">
                                        <template x-for="s in 5" :key="s">
                                            <svg :class="s <= Math.round(wisata.ratings_avg_rating || 0) ? 'fill-amber-400 text-amber-400' : 'text-gray-200'" class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                        </template>
                                    </div>
                                </div>

                                <!-- CTA -->
                                <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-forest-100">
                                    <button class="w-full inline-flex items-center justify-center text-forest-700 font-bold hover:text-earth-600 transition-colors group/link text-xs sm:text-sm">
                                        Lihat Detail
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 ml-2 group-hover/link:translate-x-1 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="sortedTourisms.length === 0" class="text-center py-20">
                    <div class="w-24 h-24 bg-forest-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="search-x" class="w-12 h-12 text-forest-400"></i>
                    </div>
                    <h3 class="text-2xl font-display font-bold text-forest-900 mb-2">Tidak Ditemukan</h3>
                    <p class="text-forest-500 max-w-md mx-auto">Tidak ada wisata yang cocok dengan pencarian Anda. Coba kata kunci lain.</p>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 2: REKOMENDASI (SAW)                       -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'rekomendasi'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;">

                <div class="flex flex-col gap-4 sm:gap-6">
                    
                    <!-- Preference Form -->
                    <div class="w-full">
                        <div class="bg-white p-4 sm:p-6 md:p-7 rounded-2xl sm:rounded-[1.5rem] shadow-sm border border-forest-100">
                            <h2 class="text-xl sm:text-2xl font-display font-bold text-forest-900 mb-1">Form Rekomendasi</h2>
                            <p class="text-xs sm:text-sm text-forest-500 mb-5 sm:mb-6">Isi preferensi Anda untuk mendapatkan rekomendasi wisata di Ranca Upas.</p>

                            <form action="{{ route('visitor.dashboard') }}?tab=rekomendasi" method="POST" class="space-y-4 sm:space-y-6">
                                @csrf

                                <!-- 1. Jenis Wisata -->
                                <div>
                                    <label class="block text-sm font-bold text-forest-900 mb-3">1. Jenis Wisata</label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="jenis_wisata" value="" class="peer sr-only" {{ empty(request('jenis_wisata')) ? 'checked' : '' }}>
                                            <div class="px-4 py-3 rounded-xl border border-gray-200 bg-white peer-checked:border-forest-500 peer-checked:bg-forest-50 peer-checked:text-forest-700 hover:bg-gray-50 flex items-center transition-all h-full">
                                                <i data-lucide="layout-grid" class="w-5 h-5 mr-3 text-forest-600"></i>
                                                <span class="text-sm font-bold">Semua Jenis</span>
                                                <div class="ml-auto w-5 h-5 rounded-full bg-forest-600 flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity">
                                                    <i data-lucide="check" class="w-3 h-3 text-white"></i>
                                                </div>
                                            </div>
                                        </label>
                                        @foreach($jenisWisata as $jw)
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="jenis_wisata" value="{{ $jw->label }}" class="peer sr-only" {{ request('jenis_wisata') == $jw->label ? 'checked' : '' }}>
                                            <div class="px-4 py-3 rounded-xl border border-gray-200 bg-white peer-checked:border-forest-500 peer-checked:bg-forest-50 peer-checked:text-forest-700 hover:bg-gray-50 flex items-center transition-all h-full">
                                                <i data-lucide="{{ $jw->icon ?: 'circle' }}" class="w-5 h-5 mr-3 text-forest-600"></i>
                                                <span class="text-sm font-bold">{{ $jw->label }}</span>
                                                <div class="ml-auto w-5 h-5 rounded-full bg-forest-600 flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity">
                                                    <i data-lucide="check" class="w-3 h-3 text-white"></i>
                                                </div>
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- 2. Budget -->
                                <div>
                                    <label class="block text-sm font-bold text-forest-900 mb-2">2. Budget / Biaya per orang</label>
                                    <select name="budget" class="w-full bg-white border border-gray-200 text-forest-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-forest-500 text-sm font-medium shadow-sm">
                                        <option value="semua" {{ request('budget') == 'semua' ? 'selected' : '' }}>Semua Harga</option>
                                        <option value="under_50" {{ request('budget') == 'under_50' ? 'selected' : '' }}>< Rp 50.000</option>
                                        <option value="50_100" {{ request('budget') == '50_100' ? 'selected' : '' }}>Rp 50.000 - Rp 100.000</option>
                                        <option value="over_100" {{ request('budget') == 'over_100' ? 'selected' : '' }}>> Rp 100.000</option>
                                    </select>
                                </div>

                                <!-- 3. Fasilitas -->
                                <div>
                                    <label class="block text-sm font-bold text-forest-900 mb-2">3. Fasilitas yang diinginkan <span class="text-xs font-normal text-gray-500 ml-1">(Pilih lebih dari satu)</span></label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2">
                                        @php
                                            $reqFasilitas = request('fasilitas', []);
                                            $fasilitasList = ['Toilet', 'Air Panas', 'Parkir', 'Tempat Makan', 'Mushola'];
                                        @endphp
                                        @foreach($fasilitasList as $fas)
                                        <label class="relative flex items-center px-3 py-2.5 rounded-lg border border-gray-200 cursor-pointer hover:bg-forest-50 transition-colors">
                                            <input type="checkbox" name="fasilitas[]" value="{{ $fas }}" class="w-4 h-4 text-forest-600 rounded bg-white border-gray-300 focus:ring-forest-500" {{ in_array($fas, $reqFasilitas) ? 'checked' : '' }}>
                                            <span class="ml-2 text-xs text-forest-900 font-medium">{{ $fas }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- 4. Jarak -->
                                <div>
                                    <label class="block text-sm font-bold text-forest-900 mb-2">4. Jarak dari lokasi Wisata Rancaupas</label>
                                    <select name="jarak" class="w-full bg-white border border-gray-200 text-forest-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-forest-500 text-sm font-medium shadow-sm">
                                        <option value="semua" {{ request('jarak') == 'semua' ? 'selected' : '' }}>Semua Jarak</option>
                                        <option value="dekat" {{ request('jarak') == 'dekat' ? 'selected' : '' }}>Dekat (< 5 km)</option>
                                        <option value="menengah" {{ request('jarak') == 'menengah' ? 'selected' : '' }}>Menengah (5 - 15 km)</option>
                                        <option value="jauh" {{ request('jarak') == 'jauh' ? 'selected' : '' }}>Jauh (> 15 km)</option>
                                    </select>
                                </div>

                                <!-- 5. Rating Minimum -->
                                <div x-data="{ rating: {{ request('rating_min', 0) }} }">
                                    <label class="block text-sm font-bold text-forest-900 mb-2">5. Rating Minimum <span class="text-xs font-normal text-gray-500 ml-1">(opsional)</span></label>
                                    <div class="flex items-center space-x-1 mt-1">
                                        <input type="hidden" name="rating_min" x-model="rating">
                                        <template x-for="i in 5">
                                            <button type="button" @click="rating = i" class="focus:outline-none transition-transform hover:scale-110 p-0.5">
                                                <svg :class="i <= rating ? 'fill-amber-400 text-amber-400' : 'text-gray-200'" class="w-7 h-7" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                            </button>
                                        </template>
                                        <span class="text-sm text-forest-800 ml-4 font-medium" x-show="rating > 0"><span x-text="rating"></span> ke atas</span>
                                    </div>
                                    <div class="mt-1 flex text-xs">
                                        <button type="button" x-show="rating > 0" @click="rating = 0" class="text-red-500 hover:text-red-700 hover:underline">Reset rating</button>
                                    </div>
                                </div>

                                <button type="submit" name="calculate" value="1" class="w-full flex items-center justify-center py-3.5 bg-forest-700 hover:bg-forest-800 font-bold text-white rounded-xl transition-all shadow-lg shadow-forest-700/20 mt-6 text-sm">
                                    <i data-lucide="search" class="w-4 h-4 sm:w-5 sm:h-5 mr-2"></i> Cari Rekomendasi
                                </button>
                                
                                <div class="px-4 py-3 bg-blue-50/50 rounded-xl flex items-start text-xs text-forest-600 mt-6 border border-blue-100/50 leading-relaxed">
                                    <i data-lucide="info" class="w-4 h-4 mr-2 shrink-0 mt-0.5 text-blue-400"></i>
                                    Sistem menggunakan filter terstruktur untuk mencari dan mengurutkan rekomendasi wisata sesuai preferensi Anda.
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Hasil Rekomendasi Area -->
                    <div class="w-full bg-white rounded-2xl sm:rounded-[1.5rem] border border-forest-100 shadow-sm p-4 sm:p-6 lg:p-7">
                        @if(!$isCalculated)
                        <div class="h-full flex flex-col items-center justify-center text-center p-6 sm:p-12 opacity-60">
                            <i data-lucide="list-filter" class="w-16 h-16 sm:w-20 sm:h-20 text-forest-300 mb-4"></i>
                            <h3 class="text-lg sm:text-xl font-bold text-forest-900 mb-2">Belum Ada Rekomendasi</h3>
                            <p class="text-forest-600 max-w-sm text-sm">Isi form preferensi di atas dan klik tombol "Cari Rekomendasi" untuk menampilkan destinasi terbaik Anda.</p>
                        </div>
                        @else
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end mb-5 sm:mb-6 gap-1">
                            <div>
                                <h2 class="text-xl sm:text-2xl font-display font-bold text-forest-900 mb-0.5">Hasil Rekomendasi</h2>
                                <p class="text-xs sm:text-sm text-forest-500">Hasil perhitungan berdasarkan preferensi Anda</p>
                            </div>
                        </div>

                        <div class="flex flex-col space-y-4">
                            @foreach($results as $index => $wisata)
                            <div class="bg-white rounded-xl sm:rounded-2xl border border-gray-200 overflow-hidden flex flex-col sm:flex-row hover:border-forest-400 hover:shadow-lg transition-all duration-300 cursor-pointer p-3 sm:p-4 gap-3 sm:gap-4" @click="openDetail({{ $wisata->id }})">
                                
                                <!-- Rank Square Info Left -->
                                <div class="hidden sm:flex w-10 h-10 sm:w-12 sm:h-12 shrink-0 bg-forest-500 text-white rounded-xl items-center justify-center font-bold text-lg sm:text-xl font-display mb-auto">
                                    {{ $index + 1 }}
                                </div>

                                <!-- Image (Middle-Left) -->
                                <div class="relative shrink-0 rounded-xl overflow-hidden" style="width: 180px; height: 130px;">
                                    <img :src="getImageUrl('{{ $wisata->image }}')" alt="{{ $wisata->name }}" class="w-full h-full object-cover">
                                    <div class="sm:hidden absolute top-2 left-2 w-7 h-7 rounded-lg bg-forest-500 text-white flex items-center justify-center font-bold text-sm shadow-md">{{ $index + 1 }}</div>
                                </div>

                                <!-- Center Info (Title, Loc, Desc, Tags) -->
                                <div class="flex-1 flex flex-col min-w-0 pr-0 sm:pr-3">
                                    <h3 class="text-base sm:text-lg font-bold text-forest-900 mb-0.5 truncate">{{ $wisata->name }}</h3>
                                    <div class="flex items-center text-xs text-forest-500 mb-1.5">
                                        <i data-lucide="map-pin" class="w-3 h-3 sm:w-3.5 sm:h-3.5 mr-1 text-earth-500"></i> Ranca Upas, Ciwidey
                                    </div>
                                    <p class="text-xs sm:text-sm text-gray-500 line-clamp-2 mb-3 leading-snug">{{ $wisata->description }}</p>
                                    
                                    @if($wisata->facilities_list)
                                    <!-- Facility Badges (like Toliet, Air Panas) -->
                                    <div class="flex flex-wrap gap-2 mt-auto">
                                        @php
                                            // Mock splitting the facilities list comma to simulate UI
                                            $tags = array_map('trim', explode(',', $wisata->facilities_list));
                                            $tags = array_slice($tags, 0, 4); // show max 4
                                        @endphp
                                        @foreach($tags as $tag)
                                        <span class="inline-flex items-center px-2 py-1 text-[10px] font-medium text-gray-600 border border-gray-200 rounded-md bg-gray-50">
                                            <i data-lucide="check" class="w-3 h-3 mr-1"></i> {{ $tag }}
                                        </span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>

                                <!-- Right Scores Panel -->
                                <div class="shrink-0 flex sm:flex-col items-center sm:items-end justify-between sm:justify-start border-t sm:border-t-0 sm:border-l border-gray-100 pt-3 sm:pt-0 sm:pl-4 mt-2 sm:mt-0 w-full sm:w-36 md:w-40">
                                    <div class="flex flex-col items-center sm:items-end w-full">
                                        <!-- Price Badge -->
                                        <div class="bg-forest-50 px-3 sm:px-4 py-2 rounded-xl text-center border border-forest-100 w-full sm:w-auto">
                                            <span class="block text-[10px] font-bold text-forest-600 uppercase mb-0.5">Biaya Tiket</span>
                                            <span class="block text-base sm:text-xl font-bold text-forest-800" style="line-height:1;">
                                                Rp {{ number_format($wisata->price_wni, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        
                                        <!-- Ratings -->
                                        <div class="mt-3 flex flex-col items-end sm:items-center w-full">
                                            <div class="flex space-x-0.5 mb-1">
                                                @php $starRating = round($wisata->ratings_avg_rating ?? 0); @endphp
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i data-lucide="star" class="w-3.5 h-3.5 {{ $i <= $starRating ? 'fill-amber-400 text-amber-400' : 'text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="text-xs text-gray-500 text-right w-full sm:text-center">{{ number_format($wisata->ratings_avg_rating ?? 0, 1) }} ({{ $wisata->ratings_count ?? 0 }} ulasan)</span>
                                        </div>

                                        <button class="mt-3 sm:mt-auto py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-forest-600 hover:text-earth-600 w-full text-right sm:text-center group transition-colors">
                                            Lihat Detail <i data-lucide="arrow-right" class="w-3 h-3 sm:w-3.5 sm:h-3.5 inline ml-0.5 group-hover:translate-x-1 transition-transform"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Footer Info Filter -->
                        @if(count($results) > 0)
                        <div class="p-4 bg-blue-50/50 rounded-xl flex items-start text-xs text-blue-800 mt-6 border border-blue-100 leading-relaxed font-medium">
                            <i data-lucide="info" class="w-4 h-4 mr-2 shrink-0 mt-0.5 text-blue-500"></i>
                            Ditemukan {{ count($results) }} tempat wisata yang sesuai dengan filter dan preferensi Anda.
                        </div>
                        @else
                        <div class="p-4 bg-amber-50/50 rounded-xl flex items-start text-xs text-amber-800 mt-6 border border-amber-100 leading-relaxed font-medium">
                            <i data-lucide="alert-circle" class="w-4 h-4 mr-2 shrink-0 mt-0.5 text-amber-500"></i>
                            Tidak ada tempat wisata yang sesuai dengan filter Anda. Silakan longgarkan kriteria pencarian.
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 3: PROFIL                                  -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'profil'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;">
                <div class="max-w-2xl mx-auto">
                    <div class="bg-white rounded-2xl sm:rounded-[1.5rem] shadow-md border border-forest-100 overflow-hidden">
                        <!-- Profile Header -->
                        <div class="bg-gradient-to-br from-forest-600 via-forest-700 to-forest-800 px-6 sm:px-10 py-10 sm:py-12 relative overflow-hidden">
                            <div class="absolute top-0 right-0 -mt-12 -mr-12 w-56 h-56 bg-white/5 rounded-full"></div>
                            <div class="absolute bottom-0 left-1/4 -mb-14 w-40 h-40 bg-earth-500/10 rounded-full"></div>
                            <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-center gap-5 sm:gap-6">
                                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center text-white text-3xl sm:text-4xl font-display font-bold border border-white/20 shadow-xl shrink-0">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div class="text-center sm:text-left">
                                    <h2 class="text-2xl sm:text-3xl font-display font-bold text-white leading-tight">{{ auth()->user()->name }}</h2>
                                    <p class="text-forest-200 text-sm mt-1.5 flex items-center justify-center sm:justify-start">
                                        <i data-lucide="mail" class="w-3.5 h-3.5 mr-1.5 shrink-0"></i>{{ auth()->user()->email }}
                                    </p>
                                    <span class="inline-flex items-center mt-3 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest {{ auth()->user()->origin === 'domestik' ? 'bg-forest-500/30 text-forest-100 border border-forest-400/30' : 'bg-earth-500/30 text-earth-100 border border-earth-400/30' }}">
                                        <i data-lucide="{{ auth()->user()->origin === 'domestik' ? 'flag' : 'globe' }}" class="w-3 h-3 mr-1.5"></i>
                                        {{ auth()->user()->origin === 'domestik' ? 'Domestik (WNI)' : 'Mancanegara (WNA)' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Form -->
                        <div class="px-6 sm:px-8 py-6 sm:py-8">
                            @if(session('profile_success'))
                                <div class="bg-forest-50 border border-forest-200 text-forest-700 px-4 py-3 rounded-xl flex items-center mb-6 text-sm font-medium shadow-sm" x-data="{ show: true }" x-show="show" x-transition>
                                    <i data-lucide="check-circle-2" class="w-5 h-5 mr-3 text-forest-500 shrink-0"></i>
                                    {{ session('profile_success') }}
                                    <button @click="show = false" class="ml-auto text-forest-400 hover:text-forest-600"><i data-lucide="x" class="w-4 h-4"></i></button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 text-sm">
                                    <div class="flex items-center mb-2 font-bold"><i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i> Terjadi kesalahan:</div>
                                    <ul class="list-disc list-inside space-y-1 ml-7">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('visitor.profile.update') }}" class="space-y-5">
                                @csrf
                                @method('PUT')

                                <div class="flex items-center space-x-3 mb-2">
                                    <div class="w-8 h-8 rounded-lg bg-forest-100 flex items-center justify-center">
                                        <i data-lucide="edit-3" class="w-4 h-4 text-forest-600"></i>
                                    </div>
                                    <h3 class="text-lg font-display font-bold text-forest-900">Edit Profil</h3>
                                </div>

                                <!-- Nama -->
                                <div>
                                    <label for="profile_name" class="block text-sm font-bold text-forest-800 mb-1.5">Nama Lengkap</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i data-lucide="user" class="h-4 w-4 text-forest-400"></i>
                                        </div>
                                        <input id="profile_name" name="name" type="text" required value="{{ old('name', auth()->user()->name) }}"
                                            class="w-full pl-11 pr-4 py-3 bg-forest-50/50 border border-forest-200 rounded-xl text-forest-900 text-sm focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-forest-500 transition-all">
                                    </div>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="profile_email" class="block text-sm font-bold text-forest-800 mb-1.5">Email Address</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i data-lucide="mail" class="h-4 w-4 text-forest-400"></i>
                                        </div>
                                        <input id="profile_email" name="email" type="email" required value="{{ old('email', auth()->user()->email) }}"
                                            class="w-full pl-11 pr-4 py-3 bg-forest-50/50 border border-forest-200 rounded-xl text-forest-900 text-sm focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-forest-500 transition-all">
                                    </div>
                                </div>

                                <!-- Asal -->
                                <div>
                                    <label for="profile_origin" class="block text-sm font-bold text-forest-800 mb-1.5">Asal Pengunjung</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i data-lucide="map" class="h-4 w-4 text-forest-400"></i>
                                        </div>
                                        <select id="profile_origin" name="origin" required
                                            class="w-full pl-11 pr-4 py-3 bg-forest-50/50 border border-forest-200 rounded-xl text-forest-900 text-sm focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-forest-500 transition-all appearance-none">
                                            <option value="domestik" {{ old('origin', auth()->user()->origin) == 'domestik' ? 'selected' : '' }}>Domestik (WNI)</option>
                                            <option value="mancanegara" {{ old('origin', auth()->user()->origin) == 'mancanegara' ? 'selected' : '' }}>Mancanegara (WNA)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Divider -->
                                <div class="border-t border-forest-100 pt-5">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                                            <i data-lucide="lock" class="w-4 h-4 text-amber-600"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-display font-bold text-forest-900">Ubah Password</h3>
                                            <p class="text-xs text-forest-500">Kosongkan jika tidak ingin mengubah password</p>
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="space-y-4">
                                        <div>
                                            <label for="profile_password" class="block text-sm font-bold text-forest-800 mb-1.5">Password Baru</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                    <i data-lucide="key-round" class="h-4 w-4 text-forest-400"></i>
                                                </div>
                                                <input id="profile_password" name="password" type="password"
                                                    class="w-full pl-11 pr-4 py-3 bg-forest-50/50 border border-forest-200 rounded-xl text-forest-900 text-sm focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-forest-500 transition-all"
                                                    placeholder="Minimal 6 karakter">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="profile_password_confirmation" class="block text-sm font-bold text-forest-800 mb-1.5">Konfirmasi Password Baru</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                    <i data-lucide="key-round" class="h-4 w-4 text-forest-400"></i>
                                                </div>
                                                <input id="profile_password_confirmation" name="password_confirmation" type="password"
                                                    class="w-full pl-11 pr-4 py-3 bg-forest-50/50 border border-forest-200 rounded-xl text-forest-900 text-sm focus:outline-none focus:ring-2 focus:ring-forest-500 focus:border-forest-500 transition-all"
                                                    placeholder="Ulangi password baru">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <button type="submit"
                                    class="w-full flex items-center justify-center py-3.5 bg-forest-700 hover:bg-forest-800 font-bold text-white rounded-xl transition-all shadow-lg shadow-forest-700/20 text-sm mt-2">
                                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan Perubahan
                                </button>
                            </form>

                            <!-- Account Info -->
                            <div class="mt-6 pt-5 border-t border-forest-100">
                                <div class="flex items-center text-xs text-forest-400 space-x-4">
                                    <span class="flex items-center"><i data-lucide="calendar" class="w-3.5 h-3.5 mr-1.5"></i> Bergabung: {{ auth()->user()->created_at->translatedFormat('d F Y') }}</span>
                                    <span class="flex items-center"><i data-lucide="shield" class="w-3.5 h-3.5 mr-1.5"></i> Role: {{ ucfirst(auth()->user()->role) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- ============================================== -->
    <!-- DETAIL MODAL                                   -->
    <!-- ============================================== -->
    <div x-show="detailModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-md" style="display: none;" x-transition.opacity @keydown.escape.window="detailModal = false">
        <div @click.away="detailModal = false" class="bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl w-full max-w-lg max-h-[92vh] sm:max-h-[85vh] overflow-y-auto relative" x-transition.scale.duration.200ms>

            <!-- Loading State -->
            <div x-show="detailLoading" class="flex items-center justify-center p-16">
                <div class="w-10 h-10 border-4 border-forest-200 border-t-forest-600 rounded-full animate-spin"></div>
            </div>

            <!-- Detail Content -->
            <template x-if="detailData && !detailLoading">
                <div>
                    <!-- Hero Image Carousel -->
                    <div x-data="{ 
                        activeSlide: 0, 
                        get slides() { 
                            let all = [];
                            if (detailData.image) all.push(detailData.image);
                            if (detailData.gallery && Array.isArray(detailData.gallery)) all = all.concat(detailData.gallery);
                            return [...new Set(all)];
                        },
                        next() { this.activeSlide = (this.activeSlide + 1) % this.slides.length; },
                        prev() { this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length; }
                    }" class="relative w-full overflow-hidden rounded-t-3xl group" style="height: 220px;">
                        
                        <!-- Images -->
                        <template x-for="(slide, index) in slides" :key="index">
                            <img x-show="activeSlide === index" 
                                 x-transition.opacity.duration.400ms
                                 :src="getImageUrl(slide)" 
                                 :alt="detailData.name" 
                                 class="absolute inset-0 w-full h-full object-cover cursor-pointer"
                                 @click="openLightbox(getImageUrl(slide))">
                        </template>

                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent pointer-events-none"></div>

                        <!-- Prev/Next Controls -->
                        <template x-if="slides.length > 1">
                            <div>
                                <button @click.prevent.stop="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white rounded-full p-2 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none z-20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                </button>
                                <button @click.prevent.stop="next()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white rounded-full p-2 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none z-20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </button>
                                
                                <!-- Indicators -->
                                <div class="absolute bottom-[65px] left-0 right-0 flex justify-center gap-1.5 z-20 pointer-events-none">
                                    <template x-for="(_, i) in slides" :key="'ind-'+i">
                                        <div class="h-1.5 rounded-full transition-all" :class="activeSlide === i ? 'w-4 bg-white' : 'w-1.5 bg-white/50'"></div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Close Button (glass) -->
                        <button @click="detailModal = false" class="absolute top-4 right-4 bg-white/20 backdrop-blur-md text-white hover:bg-white/40 p-2 rounded-full transition-all hover:scale-110 border border-white/20 z-30">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>

                        <!-- Category Pill -->
                        <span x-show="detailData.category" x-text="detailData.category" class="absolute top-4 left-4 px-3 py-1 bg-white/20 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-widest rounded-full border border-white/30 z-20 pointer-events-none"></span>

                        <!-- Title on Image -->
                        <div class="absolute bottom-0 left-0 right-0 p-5 z-20 pointer-events-none">
                            <h2 class="text-xl font-display font-bold text-white leading-snug drop-shadow-lg" x-text="detailData.name"></h2>
                        </div>
                    </div>

                    <!-- Floating Price Pills -->
                    <div class="px-5 -mt-5 relative z-20 flex flex-wrap gap-2">
                        <div class="inline-flex items-center bg-white px-4 py-2 rounded-2xl shadow-lg border border-forest-100">
                            <i data-lucide="tag" class="w-4 h-4 text-forest-500 mr-2"></i>
                            <div>
                                <p class="text-[9px] uppercase tracking-wider text-forest-400 font-semibold">WNI</p>
                                <p class="text-sm font-bold text-forest-800">Rp <span x-text="formatPrice(detailData.price_wni)"></span></p>
                            </div>
                        </div>
                        <div x-show="detailData.price_wna" class="inline-flex items-center bg-white px-4 py-2 rounded-2xl shadow-lg border border-earth-100">
                            <i data-lucide="globe" class="w-4 h-4 text-earth-500 mr-2"></i>
                            <div>
                                <p class="text-[9px] uppercase tracking-wider text-earth-400 font-semibold">WNA</p>
                                <p class="text-sm font-bold text-earth-800">Rp <span x-text="formatPrice(detailData.price_wna)"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="px-5 pt-4 pb-6 space-y-5">

                        <!-- Rating Card -->
                        <div class="bg-gradient-to-br from-forest-50/80 to-earth-50/40 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center">
                                    <span class="text-xl font-display font-bold text-forest-800" x-text="detailData.ratings_avg_rating ? Number(detailData.ratings_avg_rating).toFixed(1) : '0.0'"></span>
                                </div>
                                <div>
                                    <div class="flex space-x-0.5 mb-0.5">
                                        <template x-for="s in 5" :key="'modal-star-' + s">
                                            <svg :class="s <= Math.round(detailData.ratings_avg_rating || 0) ? 'fill-amber-400 text-amber-400' : 'text-gray-200'" class="w-3.5 h-3.5" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                        </template>
                                    </div>
                                    <p class="text-[11px] text-forest-500"><span x-text="detailData.ratings_count || 0"></span> ulasan</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <p class="text-[10px] text-forest-500 mb-1 font-semibold uppercase tracking-wider">Beri Rating</p>
                                <div class="flex space-x-0.5 items-center" @mouseleave="hoverRating = 0">
                                    <template x-for="i in 5" :key="'rate-' + i">
                                        <button type="button" @mouseover="hoverRating = i" @click="submitRating(detailData.id, i)" class="focus:outline-none transition-transform hover:scale-125">
                                            <svg :class="(hoverRating >= i || userRating >= i) ? 'fill-amber-400 text-amber-400' : 'text-forest-200'" class="w-6 h-6 transition-colors" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                        </button>
                                    </template>
                                </div>
                                <span x-show="ratingMessage" x-transition class="text-[11px] text-earth-600 font-bold mt-1 bg-earth-100 px-2 py-0.5 rounded-full" x-text="ratingMessage"></span>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <h3 class="text-[11px] font-bold text-forest-400 mb-2 uppercase tracking-wider flex items-center">
                                <i data-lucide="info" class="w-3.5 h-3.5 mr-1.5"></i> Deskripsi
                            </h3>
                            <p class="text-forest-700 leading-relaxed text-sm" x-text="detailData.description || 'Belum ada deskripsi.'"></p>
                        </div>

                        <!-- Facilities as Chips -->
                        <div x-show="detailData.facilities_list">
                            <h3 class="text-[11px] font-bold text-forest-400 mb-2 uppercase tracking-wider flex items-center">
                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 mr-1.5"></i> Fasilitas
                            </h3>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="(fac, fi) in (detailData.facilities_list || '').split(',')" :key="'fac-' + fi">
                                    <span x-show="fac.trim()" x-text="fac.trim()" class="inline-flex items-center px-3 py-1.5 bg-forest-50 text-forest-700 text-xs font-medium rounded-full border border-forest-100"></span>
                                </template>
                            </div>
                        </div>

                        <!-- Gallery -->
                        <div x-show="detailData.gallery && detailData.gallery.length > 0">
                            <h3 class="text-[11px] font-bold text-forest-400 mb-2 uppercase tracking-wider flex items-center">
                                <i data-lucide="image" class="w-3.5 h-3.5 mr-1.5"></i> Galeri
                            </h3>
                            <div class="grid grid-cols-3 gap-2">
                                <template x-for="(img, idx) in detailData.gallery" :key="'gal-' + idx">
                                    <img :src="getImageUrl(img)" @click="openLightbox(getImageUrl(img))" class="w-full h-20 object-cover rounded-xl shadow-sm hover:shadow-md transition-shadow cursor-pointer hover:opacity-90" alt="Galeri">
                                </template>
                            </div>
                        </div>

                        <!-- Map Link -->
                        <div x-show="detailData.map_url">
                            <h3 class="text-[11px] font-bold text-forest-400 mb-2 uppercase tracking-wider flex items-center">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1.5"></i> Lokasi
                            </h3>
                            <a :href="'https://www.google.com/maps?q=' + detailData.map_url" target="_blank" class="inline-flex items-center bg-gradient-to-r from-blue-50 to-blue-100/50 text-blue-700 px-4 py-2 rounded-xl hover:from-blue-100 hover:to-blue-200/50 transition-all font-medium text-xs border border-blue-100 shadow-sm">
                                <i data-lucide="external-link" class="w-3.5 h-3.5 mr-2"></i>
                                Buka di Google Maps
                            </a>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Lightbox Modal -->
    <div x-show="lightboxModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div x-show="lightboxModal" x-transition.opacity class="absolute inset-0 bg-black/90 backdrop-blur-sm" @click="lightboxModal = false"></div>
        <div x-show="lightboxModal" x-transition.scale.95 class="relative z-10 max-w-5xl w-full flex justify-center">
            <button @click="lightboxModal = false" class="absolute -top-12 right-0 md:-top-4 md:-right-12 bg-white/10 hover:bg-white/20 text-white rounded-full p-2 transition-colors focus:outline-none">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
            <img :src="lightboxImage" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl" alt="Preview Galeri">
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-forest-900 text-forest-100 py-12 border-t border-forest-800">
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
                <p>&copy; <script>document.write(new Date().getFullYear())</script> Wisata Kawasan Ranca Upas. All rights reserved.</p>
                <p>Designed with <i data-lucide="heart" class="w-3 h-3 inline-block text-red-500 mx-0.5 fill-red-500"></i> by Abelia Sapitri</p>
            </div>
        </div>
    </footer>

    <script>
        function visitorDashboard() {
            return {
                activeTab: '{{ request('tab') === 'profil' ? 'profil' : ($isCalculated ? 'rekomendasi' : 'katalog') }}',
                sortBy: 'rating',
                searchQuery: '',
                detailModal: false,
                detailData: null,
                detailLoading: false,
                userRating: 0,
                hoverRating: 0,
                ratingMessage: '',
                lightboxModal: false,
                lightboxImage: '',
                allTourisms: window._tourismData || [],

                get sortedTourisms() {
                    let items = [...this.allTourisms];
                    if (this.searchQuery.trim()) {
                        const q = this.searchQuery.toLowerCase();
                        items = items.filter(t => t.name.toLowerCase().includes(q) || (t.category && t.category.toLowerCase().includes(q)));
                    }
                    if (this.sortBy === 'rating') {
                        items.sort((a, b) => (b.ratings_avg_rating || 0) - (a.ratings_avg_rating || 0));
                    } else if (this.sortBy === 'popular') {
                        items.sort((a, b) => (b.ratings_count || 0) - (a.ratings_count || 0));
                    } else if (this.sortBy === 'price_low') {
                        items.sort((a, b) => (a.price_wni || 0) - (b.price_wni || 0));
                    } else if (this.sortBy === 'price_high') {
                        items.sort((a, b) => (b.price_wni || 0) - (a.price_wni || 0));
                    } else if (this.sortBy === 'name') {
                        items.sort((a, b) => a.name.localeCompare(b.name));
                    }
                    return items;
                },

                openLightbox(url) {
                    this.lightboxImage = url;
                    this.lightboxModal = true;
                },

                async openDetail(tourismId) {
                    this.detailLoading = true;
                    this.detailModal = true;
                    this.userRating = 0;
                    this.hoverRating = 0;
                    this.ratingMessage = '';
                    try {
                        const res = await fetch(window._basePath + '/visitor/detail/' + tourismId);
                        const data = await res.json();
                        this.detailData = data.tourism;
                        this.userRating = data.user_rating || 0;
                    } catch(e) {
                        console.error(e);
                    }
                    this.detailLoading = false;
                    this.$nextTick(() => lucide.createIcons());
                },

                async submitRating(tourismId, rating) {
                    this.userRating = rating;
                    try {
                        const res = await fetch(window._basePath + '/visitor/rate', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ tourism_id: tourismId, rating: rating }),
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.ratingMessage = 'Rating berhasil disimpan!';
                            setTimeout(() => { this.ratingMessage = ''; }, 3000);
                        }
                    } catch(e) {
                        console.error(e);
                    }
                },

                formatPrice(val) {
                    if (!val) return '0';
                    return new Intl.NumberFormat('id-ID').format(val);
                },

                getImageUrl(image) {
                    if (!image) return 'https://via.placeholder.com/400x200?text=No+Image';
                    if (image.startsWith('http')) return image;
                    // Remove leading slash to avoid double-slash in URL
                    const cleanPath = image.startsWith('/') ? image.substring(1) : image;
                    return window._basePath + '/' + cleanPath;
                },
            };
        }

        lucide.createIcons();
        document.addEventListener('alpine:initialized', () => {
            setTimeout(() => lucide.createIcons(), 100);
        });
    </script>
</body>
</html>
