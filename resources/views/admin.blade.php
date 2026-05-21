<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SPK Ranca Upas</title>
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
    <!-- Leaflet JS & CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .menu-active {
            background-color: var(--color-forest-600);
            border: 2px solid var(--color-forest-600);
            color: white;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(24, 86, 74, 0.3);
            transform: scale(1.05);
            transition: all 0.3s;
            cursor: default;
        }
        .menu-inactive {
            color: var(--color-forest-700);
            border: 2px solid transparent;
            border-radius: 0.75rem;
            transition: all 0.3s;
        }
        .menu-inactive:hover {
            color: var(--color-earth-600);
            background-color: rgba(240, 251, 252, 0.5);
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden" x-data="{ 
          activeMenu: '{{ session('activeTab', 'dashboard') }}', 
          sidebarOpen: false,
          modal: false, 
          modalType: '', 
          form: {},
          mapInstance: null,
          markerInstance: null,
          
          calculateDistance(lat2, lon2) {
              const lat1 = -7.138319075663751;
              const lon1 = 107.39174097766993;
              const R = 6371; // Radius of the earth in km
              const dLat = (lat2 - lat1) * Math.PI / 180;
              const dLon = (lon2 - lon1) * Math.PI / 180;
              const a = 
                  Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                  Math.sin(dLon/2) * Math.sin(dLon/2);
              const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
              const d = R * c;
              return parseFloat(d.toFixed(2));
          },
          
          initMap(lat, lng) {
              const defaultLat = lat || -7.142013;
              const defaultLng = lng || 107.391694;
              
              if(this.mapInstance === null) {
                  this.mapInstance = L.map('map-picker').setView([defaultLat, defaultLng], 15);
                  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                      attribution: '&copy; OpenStreetMap contributors'
                  }).addTo(this.mapInstance);
                  
                  this.markerInstance = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(this.mapInstance);
                  
                  this.markerInstance.on('dragend', (event) => {
                      const position = this.markerInstance.getLatLng();
                      this.form.map_url = position.lat.toFixed(6) + ',' + position.lng.toFixed(6);
                      this.form.distance_km = this.calculateDistance(position.lat, position.lng);
                  });

                  this.mapInstance.on('click', (event) => {
                      this.markerInstance.setLatLng(event.latlng);
                      this.form.map_url = event.latlng.lat.toFixed(6) + ',' + event.latlng.lng.toFixed(6);
                      this.form.distance_km = this.calculateDistance(event.latlng.lat, event.latlng.lng);
                  });
              } else {
                  this.mapInstance.setView([defaultLat, defaultLng], 15);
                  this.markerInstance.setLatLng([defaultLat, defaultLng]);
              }
              
              setTimeout(() => {
                  this.mapInstance.invalidateSize();
              }, 300);
          },

          openAddModal() {
              this.modalType = 'add';
              this.form = { id: null, name: '', category: '', map_url: '-7.142013,107.391694', description: '', price_wni: '', price_wna: '', distance_km: '', facilities_list: '', image: '', status: 'aktif' };
              this.modal = true;
              setTimeout(() => this.initMap(), 100);
          },
          openEditModal(data) {
              this.modalType = 'edit';
              this.form = {...data};
              if (typeof this.form.gallery === 'string') {
                  try {
                      this.form.gallery = JSON.parse(this.form.gallery);
                  } catch(e) {
                      this.form.gallery = [];
                  }
              }
              this.modal = true;
              
              let lat = -7.142013, lng = 107.391694;
              if (this.form.map_url && this.form.map_url.match(/^-?\d+\.\d+,-?\d+\.\d+/)) {
                  let parts = this.form.map_url.split(',');
                  lat = parseFloat(parts[0]);
                  lng = parseFloat(parts[1]);
              } else if (!this.form.map_url) {
                  this.form.map_url = lat + ',' + lng;
              }
              setTimeout(() => this.initMap(lat, lng), 100);
          },
          openDeleteModal(data) {
              this.modalType = 'delete';
              this.form = {...data};
              this.modal = true;
          },
          settingModalType: '',
          settingForm: {},
          openSettingModal(group, data = null) {
              this.modalType = 'setting';
              this.settingModalType = data ? 'edit' : 'add';
              this.settingForm = data ? {...data} : { id: null, group: group, label: '', value: '', icon: '', image: '', sort_order: '', is_active: true };
              this.modal = true;
          },
          openDeleteSettingModal(data) {
              this.modalType = 'delete_setting';
              this.settingForm = {...data};
              this.modal = true;
          },
          criteriaForm: {},
          openCriteriaModal(type, data = null) {
              this.modalType = 'criteria_' + type;
              this.criteriaForm = data ? {...data} : { id: null, code: '', name: '', type: 'benefit', weight: 0.1 };
              this.modal = true;
          }
      }">

    <div class="flex h-screen w-full relative">
        <!-- Sidebar Mobile Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-gray-900/50 z-40 md:hidden"
            x-transition.opacity style="display: none;"></div>

        <!-- Sidebar -->
        <aside
            class="w-64 bg-white border-r border-gray-200 flex flex-col shadow-sm z-50 shrink-0 absolute inset-y-0 left-0 transform transition-transform duration-300 md:relative md:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <!-- Logo -->
            <div class="h-20 flex items-center px-6 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Ranca Upas" class="w-10 h-10 object-contain">
                    <span class="font-display font-bold text-xl text-forest-900 tracking-tight">Ranca Upas</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-3">
                <button @click="activeMenu = 'dashboard'; sidebarOpen = false"
                    :class="activeMenu === 'dashboard' ? 'menu-active' : 'menu-inactive'"
                    class="w-full flex items-center px-4 py-3 transition-colors font-medium">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i>
                    Dashboard
                </button>
                <button @click="activeMenu = 'wisata'; sidebarOpen = false"
                    :class="activeMenu === 'wisata' ? 'menu-active' : 'menu-inactive'"
                    class="w-full flex items-center px-4 py-3 transition-colors font-medium">
                    <i data-lucide="map-pin" class="w-5 h-5 mr-3"></i>
                    Data Wisata
                </button>
                <button @click="activeMenu = 'kriteria'; sidebarOpen = false"
                    :class="activeMenu === 'kriteria' ? 'menu-active' : 'menu-inactive'"
                    class="w-full flex items-center px-4 py-3 transition-colors font-medium">
                    <i data-lucide="file-sliders" class="w-5 h-5 mr-3"></i>
                    Kriteria & Bobot
                </button>
                <button @click="activeMenu = 'saw'; sidebarOpen = false"
                    :class="activeMenu === 'saw' ? 'menu-active' : 'menu-inactive'"
                    class="w-full flex items-center px-4 py-3 transition-colors font-medium">
                    <i data-lucide="calculator" class="w-5 h-5 mr-3"></i>
                    Perhitungan SAW
                </button>
                <button @click="activeMenu = 'rekomendasi'; sidebarOpen = false"
                    :class="activeMenu === 'rekomendasi' ? 'menu-active' : 'menu-inactive'"
                    class="w-full flex items-center px-4 py-3 transition-colors font-medium">
                    <i data-lucide="award" class="w-5 h-5 mr-3"></i>
                    Hasil Rekomendasi
                </button>
                <button @click="activeMenu = 'user'; sidebarOpen = false"
                    :class="activeMenu === 'user' ? 'menu-active' : 'menu-inactive'"
                    class="w-full flex items-center px-4 py-3 transition-colors font-medium">
                    <i data-lucide="users" class="w-5 h-5 mr-3"></i>
                    Manajemen User
                </button>
                <button @click="activeMenu = 'laporan'; sidebarOpen = false"
                    :class="activeMenu === 'laporan' ? 'menu-active' : 'menu-inactive'"
                    class="w-full flex items-center px-4 py-3 transition-colors font-medium">
                    <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
                    Laporan
                </button>
                <button @click="activeMenu = 'pengaturan'; sidebarOpen = false"
                    :class="activeMenu === 'pengaturan' ? 'menu-active' : 'menu-inactive'"
                    class="w-full flex items-center px-4 py-3 transition-colors font-medium">
                    <i data-lucide="settings" class="w-5 h-5 mr-3"></i>
                    Pengaturan
                </button>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden relative bg-forest-50/30">
            <!-- Topbar -->
            <header
                class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-200 flex items-center justify-between px-4 md:px-8 shrink-0 z-10 sticky top-0">
                <div class="flex items-center text-gray-500">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="md:hidden p-2 -ml-2 mr-2 text-gray-500 hover:text-forest-600 hover:bg-forest-50 rounded-lg transition-colors focus:outline-none">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <span class="font-display font-medium text-lg text-forest-900 md:hidden">Ranca Upas</span>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-3 border-r border-gray-200 pr-6">
                        <div
                            class="w-8 h-8 rounded-full bg-forest-100 text-forest-700 flex items-center justify-center font-bold text-sm">
                            A
                        </div>
                        <span class="font-medium text-forest-900 hidden md:block">Administrator</span>
                    </div>

                    <form method="POST" action="/logout" class="m-0 p-0">
                        @csrf
                        <button type="submit"
                            class="flex items-center space-x-2 text-gray-500 hover:text-red-500 px-3 py-1.5 rounded-full hover:bg-red-50 transition-all duration-300 group">
                            <i data-lucide="log-out"
                                class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
                            <span class="text-sm font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- 1. Dashboard Content -->
            <div class="flex-1 overflow-y-auto p-8" x-show="activeMenu === 'dashboard'" x-transition.opacity>
                <!-- Existing Dashboard HTML -->
                <h1 class="text-3xl font-display font-medium text-forest-900 mb-8">Dashboard Admin</h1>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div
                        class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-forest-100 p-6 transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                        <h2 class="text-lg font-semibold text-forest-800 mb-6">Pengunjung Bulan Ini</h2>
                        <div class="relative h-64 w-full"><canvas id="barChart"></canvas></div>
                    </div>
                    <div
                        class="bg-white rounded-3xl shadow-sm border border-forest-100 p-6 flex flex-col transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                        <h2 class="text-lg font-semibold text-forest-800 mb-4">Asal Pengunjung</h2>
                        <div
                            class="relative w-full aspect-square md:aspect-auto flex-grow flex items-center justify-center">
                            <canvas id="pieChart" class="max-h-52"></canvas>
                        </div>
                        <div class="mt-4 flex flex-col space-y-2 text-sm text-forest-700">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-forest-400 rounded-full mr-2 shadow-sm"></div>
                                <span>Domestik</span> <span
                                    class="ml-auto font-semibold text-forest-900">{{ $originStats['domestik'] }}%</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-earth-500 rounded-full mr-2 shadow-sm"></div>
                                <span>Mancanegara</span> <span
                                    class="ml-auto font-semibold text-forest-900">{{ $originStats['mancanegara'] }}%</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-forest-100 flex flex-col overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                        <div class="p-6 border-b border-forest-100 bg-forest-50/50">
                            <h2 class="text-lg font-semibold text-forest-800">Aktivitas Pengguna</h2>
                        </div>
                        <div class="p-0 overflow-x-auto">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead>
                                    <tr
                                        class="bg-white text-forest-600 border-b border-forest-100 uppercase text-xs tracking-wider">
                                        <th class="py-3 px-6 font-semibold w-16 text-center">No</th>
                                        <th class="py-3 px-6 font-semibold">User</th>
                                        <th class="py-3 px-6 font-semibold">Aktivitas</th>
                                        <th class="py-3 px-6 font-semibold text-center">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="text-forest-800 divide-y divide-forest-50">
                                    @forelse($activity_logs as $index => $log)
                                        <tr class="hover:bg-forest-50 cursor-pointer transition-colors">
                                            <td class="py-3 px-6 text-center">{{ $index + 1 }}</td>
                                            <td class="py-3 px-6 font-medium">
                                                {{ $log->user ? $log->user->name : 'Sistem/Guest' }}
                                            </td>
                                            <td class="py-3 px-6 text-forest-500">{{ $log->activity }}</td>
                                            <td class="py-3 px-6 text-center text-forest-500">
                                                {{ str_replace([' seconds', ' minutes', ' hours', ' days', ' weeks', ' months', ' years', ' ago'], [' detik', ' menit', ' jam', ' hari', ' minggu', ' bulan', ' tahun', 'yg lalu'], $log->created_at->diffForHumans(null, true)) }}
                                                yg lalu
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-3 px-6 text-center text-forest-500">Belum ada data
                                                aktivitas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-forest-100 text-right mt-auto bg-white hover:bg-forest-50 transition-colors cursor-pointer"
                            onclick="alert('Lihat semua ditekan')">
                            <button
                                class="text-forest-600 text-sm font-semibold flex items-center justify-end w-full group">
                                Lihat Semua <i data-lucide="chevron-right"
                                    class="w-4 h-4 inline ml-1 group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Wisata Terpopuler Widget (Modernized Nature Theme) -->
                    <div class="flex flex-col w-full h-full transform transition-all duration-300 hover:-translate-y-1">
                        <div class="flex items-center justify-between mb-4 px-1">
                            <h2 class="text-xl font-bold text-forest-900 flex items-center">
                                <i data-lucide="trending-up" class="w-5 h-5 mr-2 text-earth-500"></i>
                                Wisata Terpopuler
                            </h2>
                        </div>

                        <!-- List Container -->
                        <div
                            class="flex-1 bg-white border border-forest-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-shadow duration-300 flex flex-col">

                            @foreach($tourisms->sortByDesc('ratings_avg_rating')->take(5) as $popular)
                                <div
                                    class="flex items-center p-4 {{ !$loop->last ? 'border-b border-forest-50' : '' }} hover:bg-forest-50/80 transition-all cursor-pointer group">
                                    <div
                                        class="relative w-16 h-16 bg-forest-100 rounded-2xl mr-4 shrink-0 overflow-hidden shadow-sm group-hover:scale-105 group-hover:shadow-md transition-all duration-300 border border-forest-200">
                                        <img src="{{ $popular->image ? (Str::startsWith($popular->image, 'http') ? $popular->image : asset($popular->image)) : 'https://via.placeholder.com/150' }}"
                                            alt="{{ $popular->name }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-gradient-to-t from-forest-900/80 to-transparent">
                                        </div>
                                        <span
                                            class="absolute bottom-1 right-2 z-10 text-xl font-extrabold font-display text-white drop-shadow-md">#{{ $loop->iteration }}</span>
                                    </div>
                                    <div class="flex flex-col justify-center flex-1">
                                        <h3
                                            class="font-bold text-forest-900 text-sm mb-1 group-hover:text-forest-700 transition-colors">
                                            {{ $popular->name }}
                                        </h3>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-0.5">
                                                @php $starRating = round($popular->ratings_avg_rating ?? 0); @endphp
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i data-lucide="star"
                                                        class="w-3.5 h-3.5 {{ $i <= $starRating ? 'fill-earth-500 text-earth-500' : 'text-gray-300' }}"></i>
                                                @endfor
                                                <span
                                                    class="text-xs font-semibold ml-1.5 text-forest-600">{{ number_format($popular->ratings_avg_rating ?? 0, 1) }}</span>
                                            </div>
                                            @if($popular->category)
                                                <span
                                                    class="text-[10px] font-bold uppercase tracking-wider bg-forest-100 text-forest-600 px-2 py-0.5 rounded-full">{{ $popular->category }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Data Wisata (CRUD Table) -->
            <div class="flex-1 overflow-y-auto p-8" x-show="activeMenu === 'wisata'" style="display: none;">
                @if(session('success'))
                    <div
                        class="mb-6 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 text-sm flex items-start shadow-sm animate-pulse">
                        <i data-lucide="check-circle-2" class="w-5 h-5 mr-3 shrink-0"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div>
                        <h1 class="text-3xl font-display font-bold text-forest-900">Data Wisata</h1>
                        <p class="text-sm text-forest-600 mt-1">Kelola dan perbarui informasi objek wisata kawasan Ranca
                            Upas</p>
                    </div>
                    <button @click="openAddModal()"
                        class="bg-forest-600 hover:bg-forest-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-all shadow-md flex items-center shrink-0">
                        <i data-lucide="plus" class="w-5 h-5 mr-2"></i> Tambah Wisata
                    </button>
                </div>

                <!-- 4 Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Total Wisata -->
                    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm flex items-center">
                        <div
                            class="w-12 h-12 bg-green-50 text-green-500 rounded-xl flex items-center justify-center mr-4 shrink-0">
                            <i data-lucide="map-pin" class="w-6 h-6 outline-none"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-500 font-bold mb-0.5 uppercase tracking-wide">Total Wisata
                            </p>
                            <p class="text-2xl font-bold text-gray-900 leading-none">{{ count($tourisms) }} <span
                                    class="text-[10px] font-normal text-gray-400 ml-1 uppercase">Objek</span></p>
                        </div>
                    </div>
                    @php
                        $colors = [
                            ['bg' => 'bg-blue-50', 'text' => 'text-blue-500'],
                            ['bg' => 'bg-amber-50', 'text' => 'text-amber-500'],
                            ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-500'],
                            ['bg' => 'bg-rose-50', 'text' => 'text-rose-500'],
                            ['bg' => 'bg-teal-50', 'text' => 'text-teal-500']
                        ];
                        $jwActive = $jenisWisata->where('is_active', true)->values();
                    @endphp
                    @foreach($jwActive as $idx => $jw)
                        @php $c = $colors[$idx % count($colors)]; @endphp
                        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm flex items-center">
                            <div class="w-12 h-12 {{ $c['bg'] }} {{ $c['text'] }} rounded-xl flex items-center justify-center mr-4 shrink-0">
                                <i data-lucide="{{ $jw->icon ?: 'map' }}" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-500 font-bold mb-0.5 uppercase tracking-wide">{{ $jw->label }}</p>
                                <p class="text-2xl font-bold text-gray-900 leading-none">
                                    {{ $tourisms->where('category', $jw->label)->count() }}
                                    <span class="text-[10px] font-normal text-gray-400 ml-1 uppercase">Objek</span>
                                </p>
                            </div>
                        </div>
                    @endforeach
                    <!-- Total Foto -->
                    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm flex items-center">
                        <div
                            class="w-12 h-12 bg-purple-50 text-purple-500 rounded-xl flex items-center justify-center mr-4 shrink-0">
                            <i data-lucide="image" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-500 font-bold mb-0.5 uppercase tracking-wide">Total Foto</p>
                            <p class="text-2xl font-bold text-gray-900 leading-none">{{ count($tourisms) }} <span
                                    class="text-[10px] font-normal text-gray-400 ml-1 uppercase">Foto</span></p>
                        </div>
                    </div>
                </div>

                <!-- Filters Layout -->
                <div class="flex flex-col md:flex-row gap-4 mb-6 relative z-10 w-full"
                    x-data="{ searchQuery: '', filterJenis: '', filterStatus: '' }" x-ref="wisataFilters"
                    @input.debounce.200ms="
                        $nextTick(() => {
                            const rows = document.querySelectorAll('.wisata-row');
                            let visibleIdx = 0;
                            rows.forEach(row => {
                                const name = row.dataset.name;
                                const category = row.dataset.category;
                                const status = row.dataset.status;
                                
                                let show = true;
                                if (searchQuery && !name.includes(searchQuery.toLowerCase())) show = false;
                                if (filterJenis && category !== filterJenis) show = false;
                                if (filterStatus && status !== filterStatus) show = false;
                                
                                row.style.display = show ? '' : 'none';
                                if (show) {
                                    visibleIdx++;
                                    row.querySelector('.row-num').textContent = visibleIdx;
                                }
                            });
                            document.getElementById('wisata-empty').style.display = visibleIdx === 0 ? '' : 'none';
                        })
                     ">
                    <div class="relative flex-1">
                        <i data-lucide="search"
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="text" x-model="searchQuery" placeholder="Cari nama wisata..."
                            class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-forest-500 transition-all font-medium text-gray-700">
                    </div>
                    <select x-model="filterJenis"
                        class="bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-forest-500 w-full md:w-48 font-medium text-gray-600 appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%236B7280%22%20stroke-width%3D%222%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[position:right_1rem_center]">
                        <option value="">Semua Jenis Wisata</option>
                        @foreach($jenisWisata->where('is_active', true) as $jw)
                            <option value="{{ strtolower($jw->label) }}">{{ $jw->label }}</option>
                        @endforeach
                    </select>
                    <select x-model="filterStatus"
                        class="bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-forest-500 w-full md:w-40 font-medium text-gray-600 appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%236B7280%22%20stroke-width%3D%222%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[position:right_1rem_center]">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                    <button @click="searchQuery = ''; filterJenis = ''; filterStatus = ''; $dispatch('input')"
                        class="bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-5 py-3 rounded-xl text-sm font-semibold transition-colors flex items-center justify-center w-full md:w-auto">
                        <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2"></i> Reset Filter
                    </button>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm min-w-max">
                            <thead>
                                <tr
                                    class="bg-gray-50 text-gray-600 border-b border-gray-100 font-bold text-[11px] uppercase tracking-wider">
                                    <th class="py-4 px-4 text-center w-12">No</th>
                                    <th class="py-4 px-4 w-28">Foto</th>
                                    <th class="py-4 px-4 min-w-[200px]">Nama Wisata</th>
                                    <th class="py-4 px-4 text-center">Jenis Wisata</th>
                                    <th class="py-4 px-4 text-center">Harga (Rp)</th>
                                    <th class="py-4 px-4 text-center">Jarak (km)</th>
                                    <th class="py-4 px-4 text-center">Fasilitas Utama</th>
                                    <th class="py-4 px-4 text-center">Rating Rata-rata</th>
                                    <th class="py-4 px-4 text-center">Status</th>
                                    <th class="py-4 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-800 divide-y divide-gray-100">
                                @foreach($tourisms as $idx => $t)
                                    <tr class="hover:bg-forest-50/40 transition-colors group wisata-row"
                                        data-name="{{ strtolower($t->name) }}"
                                        data-category="{{ strtolower($t->category ?? '') }}"
                                        data-status="{{ $t->status ?? 'aktif' }}">
                                        <td class="py-4 px-4 text-center font-bold text-gray-400 row-num">{{ $idx + 1 }}
                                        </td>
                                        <td class="py-4 px-4">
                                            <img src="{{ $t->image ? (Str::startsWith($t->image, 'http') ? $t->image : asset($t->image)) : 'https://via.placeholder.com/200' }}"
                                                alt="img"
                                                class="w-16 h-12 object-cover rounded shadow-sm border border-gray-200"
                                                title="{{ $t->name }}">
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-gray-900 text-[13px] mb-0.5">{{ $t->name }}</div>
                                            <div class="text-[11px] text-gray-500 line-clamp-1 max-w-[200px]">
                                                {{ $t->description }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if(stripos($t->category, 'Camp') !== false)
                                                <span
                                                    class="inline-flex text-[10px] font-bold px-2 py-0.5 bg-green-50 text-green-600 rounded border border-green-100 uppercase">Camping</span>
                                            @else
                                                <span
                                                    class="inline-flex text-[10px] font-bold px-2 py-0.5 bg-orange-50 text-orange-600 rounded border border-orange-100 uppercase">{{ $t->category ?: 'Kunjungan' }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center font-bold text-gray-700">
                                            {{ number_format($t->price_wni, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-4 text-center font-medium text-gray-600">
                                            {{ number_format($t->distance_km, 1) }}
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex flex-wrap items-center justify-center gap-1.5">
                                                @php
                                                    $facs = array_values(array_filter(array_map('trim', explode(',', $t->facilities_list))));
                                                    $displayFacs = array_slice($facs, 0, 4);
                                                    $remaining = count($facs) - 4;
                                                @endphp
                                                @foreach($displayFacs as $fac)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200" title="{{ $fac }}">
                                                        {{ $fac }}
                                                    </span>
                                                @endforeach
                                                @if($remaining > 0)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-forest-50 text-forest-700 border border-forest-200" title="{{ implode(', ', array_slice($facs, 4)) }}">
                                                        +{{ $remaining }}
                                                    </span>
                                                @endif
                                                @if(empty($facs))
                                                    <span class="text-xs text-gray-400 italic">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="flex items-center font-bold text-gray-900 text-[13px]">
                                                    {{ number_format($t->ratings_avg_rating ?? 0, 1) }}
                                                    <i data-lucide="star"
                                                        class="w-3.5 h-3.5 fill-amber-400 text-amber-400 ml-1 mt-[-1px]"></i>
                                                </div>
                                                <div class="text-[10px] text-gray-400 font-medium mt-0.5">
                                                    ({{ $t->ratings_count ?? 0 }})</div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($t->status === 'aktif')
                                                <span
                                                    class="inline-block text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded border border-emerald-100 uppercase tracking-widest">Aktif</span>
                                            @else
                                                <span
                                                    class="inline-block text-[10px] font-bold px-2 py-0.5 bg-red-50 text-red-500 rounded border border-red-100 uppercase tracking-widest">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center justify-center space-x-1">
                                                <button @click="openEditModal({{ json_encode($t) }})"
                                                    class="p-1.5 text-gray-400 bg-white border border-gray-200 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300 rounded transition-all shadow-sm"
                                                    title="Edit">
                                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                                </button>
                                                <button @click="openDeleteModal({{ json_encode($t) }})"
                                                    class="p-1.5 text-red-400 bg-white border border-gray-200 hover:text-red-600 hover:bg-red-50 hover:border-red-200 rounded transition-all shadow-sm"
                                                    title="Hapus">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                <!-- Empty State -->
                                <tr id="wisata-empty" style="display: none;">
                                    <td colspan="10" class="py-12 text-center">
                                        <div class="flex flex-col items-center text-gray-400">
                                            <i data-lucide="search-x" class="w-10 h-10 mb-3 text-gray-300"></i>
                                            <p class="text-sm font-medium">Tidak ada data yang cocok dengan filter</p>
                                            <p class="text-xs mt-1">Coba ubah kata kunci atau filter yang dipilih</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. Kriteria SAW -->
            <div class="flex-1 overflow-y-auto p-8" x-show="activeMenu === 'kriteria'" style="display: none;">

                <div
                    class="bg-green-50/50 border border-green-100 text-gray-600 px-4 py-3 rounded-lg flex items-center shadow-sm w-full mb-6 relative overflow-hidden">
                    <i data-lucide="info" class="w-5 h-5 mr-3 shrink-0 text-green-600"></i>
                    <div class="text-sm font-medium text-gray-600">
                        Kriteria digunakan untuk menilai wisata. Pastikan bobot total = <strong
                            class="text-green-700">1.00</strong>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                    <div class="p-5 border-b border-gray-100 bg-white">
                        <h2 class="text-lg font-bold text-gray-800">Daftar Kriteria</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-white text-gray-800 border-b border-gray-100 font-bold">
                                    <th class="py-4 px-6 w-16 text-center">No</th>
                                    <th class="py-4 px-6">Nama Kriteria</th>
                                    <th class="py-4 px-6 w-32">Bobot</th>
                                    <th class="py-4 px-6 w-32">Tipe</th>
                                    <th class="py-4 px-6">Keterangan</th>
                                    <th class="py-4 px-6 w-32 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @php
                                    $keteranganMap = [
                                        'C1' => ['icon' => 'banknote', 'desc' => 'Semakin kecil biaya, semakin baik.'],
                                        'C2' => ['icon' => 'building-2', 'desc' => 'Semakin lengkap fasilitas, semakin baik.'],
                                        'C3' => ['icon' => 'map-pin', 'desc' => 'Semakin dekat jarak, semakin baik.'],
                                        'C4' => ['icon' => 'star', 'desc' => 'Semakin tinggi rating, semakin baik.'],
                                    ];
                                @endphp
                                @foreach($criteria as $idx => $c)
                                    @php
                                        $info = $keteranganMap[$c->code] ?? ['icon' => 'file-text', 'desc' => '-'];
                                        $cleanName = explode(' (', $c->name)[0];
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 transition-colors bg-white">
                                        <td class="py-4 px-6 font-bold text-gray-800 text-center">{{ $idx + 1 }}</td>
                                        <td class="py-4 px-6">
                                            <div class="flex items-center space-x-4">
                                                <div
                                                    class="w-10 h-10 rounded-xl bg-forest-50 flex items-center justify-center text-forest-600 shrink-0">
                                                    <i data-lucide="{{ $info['icon'] }}" class="w-5 h-5"></i>
                                                </div>
                                                <span class="font-bold text-gray-800">{{ $cleanName }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-gray-700 font-medium">{{ number_format($c->weight, 2) }}
                                        </td>
                                        <td class="py-4 px-6">
                                            @if(strtolower($c->type) === 'cost')
                                                <span
                                                    class="inline-flex text-[11px] font-bold px-2.5 py-1 bg-red-50 text-red-500 rounded border border-red-100">Cost</span>
                                            @else
                                                <span
                                                    class="inline-flex text-[11px] font-bold px-2.5 py-1 bg-green-50 text-green-600 rounded border border-green-100">Benefit</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-gray-600 font-medium">{{ $info['desc'] }}</td>
                                        <td class="py-4 px-6">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button @click="openCriteriaModal('edit', {{ Js::from($c) }})"
                                                    class="p-1.5 text-forest-600 bg-white border border-forest-200 hover:bg-forest-50 rounded transition-all shadow-sm"
                                                    title="Edit">
                                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                                </button>
                                                <button @click="openCriteriaModal('delete', {{ Js::from($c) }})"
                                                    class="p-1.5 text-red-400 bg-white border border-red-200 hover:text-red-500 hover:bg-red-50 rounded transition-all shadow-sm"
                                                    title="Hapus">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t border-gray-100 bg-white">
                                <tr>
                                    <td class="py-5 px-6"></td>
                                    <td class="py-5 px-6 font-bold text-gray-800">Total Bobot</td>
                                    <td class="py-5 px-6 font-bold text-forest-600">
                                        {{ number_format($criteria->sum('weight'), 2) }}
                                    </td>
                                    <td class="py-5 px-6"></td>
                                    <td class="py-5 px-6"></td>
                                    <td class="py-5 px-6"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div
                    class="bg-blue-50 border border-blue-50 w-full px-4 py-4 rounded-lg flex items-start shadow-sm mt-8">
                    <i data-lucide="info" class="w-5 h-5 mr-3 shrink-0 fill-blue-500 text-white"></i>
                    <div class="text-sm font-medium text-blue-500">
                        Total bobot harus sama dengan 1.00 agar perhitungan SAW berjalan dengan benar.
                    </div>
                </div>

            </div>

            <!-- 4. Manajemen User -->
            <div class="flex-1 overflow-y-auto p-8" x-show="activeMenu === 'user'" style="display: none;">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-display font-medium text-gray-800">Manajemen User (Administrator)</h1>
                    <button
                        class="bg-forest-600 hover:bg-forest-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors border border-forest-700 shadow-sm flex items-center">
                        <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i> Tambah Admin
                    </button>
                </div>

                <div class="bg-white rounded shadow-sm border border-gray-200">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 border-b border-gray-200 uppercase text-xs">
                                <th class="py-3 px-6 font-medium">Username / Name</th>
                                <th class="py-3 px-6 font-medium border-l border-gray-200">Email Address</th>
                                <th class="py-3 px-6 font-medium border-l border-gray-200 text-center">Role</th>
                                <th class="py-3 px-6 font-medium border-l border-gray-200 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800">
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-4 px-6 font-medium flex items-center">
                                    <div
                                        class="w-8 h-8 rounded-full bg-forest-100 text-forest-700 flex items-center justify-center mr-3 font-bold">
                                        A</div>
                                    Admin Ranca Upas
                                </td>
                                <td class="py-4 px-6 border-l border-gray-200 text-gray-600">admin@rancaupas.com</td>
                                <td class="py-4 px-6 border-l border-gray-200 text-center">
                                    <span class="bg-earth-100 text-earth-800 px-2 py-1 rounded text-xs font-bold">Super
                                        Admin</span>
                                </td>
                                <td class="py-4 px-6 border-l border-gray-200 text-center">
                                    <button class="text-gray-400 hover:text-gray-600" disabled><i data-lucide="lock"
                                            class="w-4 h-4 inline"></i> Default</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 5. Laporan -->
            <div class="flex-1 overflow-y-auto p-8" x-show="activeMenu === 'laporan'" style="display: none;">
                <h1 class="text-3xl font-display font-medium text-gray-800 mb-6">Laporan & Laporan SPK</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded shadow-sm border border-gray-200 flex flex-col justify-between">
                        <form action="{{ route('admin.export.saw') }}" method="GET" class="h-full flex flex-col justify-between">
                            <div>
                                <div
                                    class="w-12 h-12 bg-forest-100 text-forest-600 rounded flex items-center justify-center mb-4 border border-forest-200">
                                    <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
                                </div>
                                <h3 class="text-xl font-medium text-gray-800 mb-2">Laporan Destinasi Optimal</h3>
                                <p class="text-gray-500 text-sm mb-4">Unduh hasil kalkulasi SAW historis dan laporan
                                    rekomendasi objek wisata terbaik periode ini.</p>
                                
                                <div class="mb-6">
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Laporan</label>
                                    <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-forest-500 focus:ring-2 focus:ring-forest-100 text-sm transition-all bg-gray-50 hover:bg-white focus:bg-white text-gray-700 font-medium">
                                </div>
                            </div>
                            <button type="submit"
                                class="w-full bg-white border border-gray-300 hover:bg-forest-50 hover:border-forest-300 text-gray-700 hover:text-forest-700 py-2.5 rounded-lg font-bold flex items-center justify-center transition-all shadow-sm">
                                <i data-lucide="download" class="w-4 h-4 mr-2"></i> Unduh Laporan PDF
                            </button>
                        </form>
                    </div>

                    <div class="bg-white p-6 rounded shadow-sm border border-gray-200 flex flex-col justify-between">
                        <div>
                            <div
                                class="w-12 h-12 bg-earth-100 text-earth-600 rounded flex items-center justify-center mb-4 border border-earth-200">
                                <i data-lucide="pie-chart" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-xl font-medium text-gray-800 mb-2">Statistik Pengunjung TAM</h3>
                            <p class="text-gray-500 text-sm mb-6">Unduh data persepsi pengunjung (TAM Model) PDF report
                                untuk keperluan evaluasi manajemen.</p>
                        </div>
                        <button
                            class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 py-2 rounded font-medium flex items-center justify-center transition-colors">
                            <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Cetak Laporan PDF
                        </button>
                    </div>
                </div>
            </div>

            <!-- 6. Perhitungan SAW -->
            <div class="flex-1 overflow-y-auto p-8" x-show="activeMenu === 'saw'" style="display: none;"
                x-data="sawCalculator()"
                x-init="$watch('activeMenu', v => { if(v === 'saw') $nextTick(() => lucide.createIcons()) })">

                <div class="mb-6">
                    <h1 class="text-3xl font-display font-medium text-gray-800">Perhitungan SAW</h1>
                    <p class="text-sm text-gray-500 mt-1">Proses perhitungan menggunakan metode Simple Additive
                        Weighting</p>
                </div>

                <!-- Filter & Action -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[180px]">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Pilih Jenis Wisata</label>
                            <select x-model="selectedCategory"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-forest-500 focus:ring-2 focus:ring-forest-100 bg-white text-sm">
                                <option value="">Semua Jenis</option>
                                @foreach($jenisWisata->where('is_active', true) as $jw)
                                    <option value="{{ $jw->label }}">{{ $jw->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-[180px]">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Perhitungan</label>
                            <input type="date" x-model="calcDate"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-forest-500 focus:ring-2 focus:ring-forest-100 text-sm">
                        </div>
                        <div>
                            <button @click="hitungSAW()" :disabled="isLoading"
                                class="bg-forest-600 hover:bg-forest-700 text-white px-6 py-2.5 rounded-lg font-bold text-sm flex items-center shadow-md hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-wait">
                                <template x-if="!isLoading">
                                    <span class="flex items-center"><i data-lucide="calculator"
                                            class="w-4 h-4 mr-2"></i> Hitung SAW</span>
                                </template>
                                <template x-if="isLoading">
                                    <span class="flex items-center">
                                        <svg class="animate-spin w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Menghitung...
                                    </span>
                                </template>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State (before calculation) -->
                <div x-show="!hasComputed && !isLoading"
                    class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
                    <div class="w-20 h-20 bg-forest-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="calculator" class="w-10 h-10 text-forest-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-600 mb-2">Belum Ada Perhitungan</h3>
                    <p class="text-sm text-gray-400 max-w-md mx-auto">Pilih jenis wisata (opsional), lalu klik tombol
                        <strong>"Hitung SAW"</strong> untuk memulai proses perhitungan dan melihat hasil perangkingan.
                    </p>
                </div>

                <!-- Results Section -->
                <div x-show="hasComputed" x-cloak>

                    <!-- Info Box Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                        <div
                            class="lg:col-span-2 bg-gradient-to-br from-forest-50 to-forest-100 rounded-2xl border border-forest-200 p-6">
                            <h3 class="text-sm font-bold text-forest-800 mb-4 flex items-center">
                                <i data-lucide="info" class="w-4 h-4 mr-2"></i> Informasi Perhitungan
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-2 text-sm">
                                <div class="flex"><span class="text-forest-600 w-40 shrink-0">Metode</span><span
                                        class="font-bold text-forest-900">: SAW</span></div>
                                <div class="flex"><span class="text-forest-600 w-40 shrink-0">Tanggal</span><span
                                        class="font-bold text-forest-900" x-text="': ' + calcDate"></span></div>
                                <div class="flex"><span class="text-forest-600 w-40 shrink-0">Jumlah
                                        Alternatif</span><span class="font-bold text-forest-900"
                                        x-text="': ' + sawFiltered.length"></span></div>
                                <div class="flex"><span class="text-forest-600 w-40 shrink-0">Jumlah
                                        Kriteria</span><span class="font-bold text-forest-900"
                                        x-text="': ' + sawCriteria.length"></span></div>
                                <div class="flex"><span class="text-forest-600 w-40 shrink-0">Filter
                                        Kategori</span><span class="font-bold text-forest-900"
                                        x-text="': ' + (selectedCategory || 'Semua Jenis')"></span></div>
                                <div class="flex"><span class="text-forest-600 w-40 shrink-0">Total Bobot</span><span
                                        class="font-bold text-forest-900"
                                        x-text="': ' + sawCriteria.reduce((s,c) => s + c.weight, 0).toFixed(2)"></span>
                                </div>
                                <div class="flex"><span class="text-forest-600 w-40 shrink-0">Kriteria
                                        Benefit</span><span class="font-bold text-forest-900"
                                        x-text="': ' + sawCriteria.filter(c => c.type==='benefit').map(c => c.name).join(', ')"></span>
                                </div>
                                <div class="flex"><span class="text-forest-600 w-40 shrink-0">Kriteria Cost</span><span
                                        class="font-bold text-forest-900"
                                        x-text="': ' + sawCriteria.filter(c => c.type==='cost').map(c => c.name).join(', ')"></span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <i data-lucide="sliders-horizontal" class="w-4 h-4 mr-2 text-forest-600"></i> Kriteria &
                                Bobot
                            </h3>
                            <div class="space-y-2.5">
                                <template x-for="c in sawCriteria" :key="c.code">
                                    <div
                                        class="flex items-center justify-between text-sm py-1 border-b border-gray-50 last:border-0">
                                        <div class="flex items-center">
                                            <span x-text="c.name" class="font-medium text-gray-700"></span>
                                            <span x-show="c.type === 'cost'"
                                                class="ml-2 text-[10px] font-bold px-1.5 py-0.5 rounded bg-red-50 text-red-500 border border-red-100">Cost</span>
                                            <span x-show="c.type === 'benefit'"
                                                class="ml-2 text-[10px] font-bold px-1.5 py-0.5 rounded bg-green-50 text-green-500 border border-green-100">Benefit</span>
                                        </div>
                                        <span class="font-bold text-gray-800" x-text="c.weight.toFixed(2)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- No data warning -->
                    <div x-show="sawFiltered.length === 0"
                        class="bg-amber-50 border border-amber-200 rounded-xl p-6 text-center mb-8">
                        <i data-lucide="alert-triangle" class="w-8 h-8 text-amber-400 mx-auto mb-3"></i>
                        <p class="text-sm font-bold text-amber-700">Tidak ada data wisata untuk kategori yang dipilih.
                        </p>
                        <p class="text-xs text-amber-500 mt-1">Silakan pilih kategori lain atau pilih "Semua Jenis".</p>
                    </div>

                    <!-- Proses Perhitungan SAW Table -->
                    <div x-show="sawFiltered.length > 0"
                        class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
                        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <i data-lucide="table" class="w-5 h-5 mr-2 text-forest-600"></i> Proses Perhitungan SAW
                        </h2>
                        <div class="overflow-x-auto" id="saw-table-container"></div>
                    </div>

                    <!-- Hasil Perangkingan -->
                    <div x-show="sawRanking.length > 0"
                        class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
                        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <i data-lucide="trophy" class="w-5 h-5 mr-2 text-earth-500"></i> Hasil Perangkingan
                        </h2>
                        <div class="flex flex-wrap gap-4 justify-center" id="saw-ranking-container"></div>
                    </div>

                    <!-- Keterangan -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start space-x-3">
                        <i data-lucide="info" class="w-5 h-5 text-blue-500 mt-0.5 shrink-0"></i>
                        <p class="text-sm text-blue-700">
                            <strong>Keterangan:</strong> Nilai (Yi) yang lebih tinggi menunjukkan alternatif Wisata yang
                            lebih direkomendasikan sesuai preferensi.
                        </p>
                    </div>
                </div>
            </div>

            <!-- 7. Hasil Rekomendasi -->
            <div class="flex-1 overflow-y-auto p-8" x-show="activeMenu === 'rekomendasi'" style="display: none;">
                <div class="mb-6">
                    <h1 class="text-3xl font-display font-medium text-gray-800">Hasil Rekomendasi</h1>
                    <p class="text-sm text-gray-500 mt-1">Menampilkan hasil rekomendasi wisata berdasarkan perhitungan
                        SAW</p>
                </div>

                <!-- Daftar Hasil Rekomendasi -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-bold text-gray-800 flex items-center">
                            <i data-lucide="list-ordered" class="w-5 h-5 mr-2 text-forest-600"></i> Daftar Hasil
                            Rekomendasi
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr
                                    class="bg-gray-50 text-gray-500 text-[11px] uppercase tracking-wider border-b border-gray-100">
                                    <th class="py-3.5 px-4 text-center w-16">Ranking</th>
                                    <th class="py-3.5 px-4 min-w-[250px]">Wisata</th>
                                    <th class="py-3.5 px-4 text-center">Jenis</th>
                                    <th class="py-3.5 px-4 text-center min-w-[120px]">Nilai Akhir (Vi)</th>
                                    @foreach($criteria as $c)
                                        <th class="py-3.5 px-4 text-center">
                                            <div class="font-bold">{{ explode(' (', $c->name)[0] }}</div>
                                            <div class="text-[9px] opacity-60 font-medium">
                                                ({{ number_format($c->weight, 2) }})</div>
                                        </th>
                                    @endforeach
                                    <th class="py-3.5 px-4 text-center w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($sawData['ranking'] as $r)
                                    <tr class="hover:bg-forest-50/30 transition-colors">
                                        {{-- Ranking --}}
                                        <td class="py-4 px-4 text-center">
                                            @if($r['rank'] === 1)
                                                <div
                                                    class="w-9 h-9 mx-auto rounded-full bg-gradient-to-br from-forest-500 to-forest-700 text-white flex items-center justify-center font-bold text-sm shadow-lg shadow-forest-500/30">
                                                    {{ $r['rank'] }}
                                                </div>
                                            @elseif($r['rank'] === 2)
                                                <div
                                                    class="w-9 h-9 mx-auto rounded-full bg-gradient-to-br from-earth-500 to-earth-600 text-white flex items-center justify-center font-bold text-sm shadow-md">
                                                    {{ $r['rank'] }}
                                                </div>
                                            @elseif($r['rank'] === 3)
                                                <div
                                                    class="w-9 h-9 mx-auto rounded-full bg-gradient-to-br from-amber-400 to-amber-600 text-white flex items-center justify-center font-bold text-sm shadow-md">
                                                    {{ $r['rank'] }}
                                                </div>
                                            @else
                                                <div
                                                    class="w-9 h-9 mx-auto rounded-full bg-gray-100 text-gray-500 flex items-center justify-center font-bold text-sm">
                                                    {{ $r['rank'] }}
                                                </div>
                                            @endif
                                        </td>
                                        {{-- Wisata Info --}}
                                        <td class="py-4 px-4">
                                            <div class="flex items-center">
                                                <img src="{{ $r['tourism']->image ? (Str::startsWith($r['tourism']->image, 'http') ? $r['tourism']->image : asset($r['tourism']->image)) : 'https://via.placeholder.com/80' }}"
                                                    alt="{{ $r['tourism']->name }}"
                                                    class="w-14 h-10 object-cover rounded-lg shadow-sm border border-gray-200 mr-3 shrink-0">
                                                <div>
                                                    <div class="font-bold text-gray-900 text-[13px]">
                                                        {{ $r['tourism']->name }}
                                                    </div>
                                                    <div class="text-[11px] text-gray-400 line-clamp-1 max-w-[200px]">
                                                        {{ $r['tourism']->description }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        {{-- Jenis --}}
                                        <td class="py-4 px-4 text-center">
                                            @if(stripos($r['tourism']->category, 'Camp') !== false)
                                                <span
                                                    class="inline-flex text-[10px] font-bold px-2.5 py-1 bg-green-50 text-green-600 rounded-full border border-green-100 uppercase">Camping</span>
                                            @else
                                                <span
                                                    class="inline-flex text-[10px] font-bold px-2.5 py-1 bg-orange-50 text-orange-600 rounded-full border border-orange-100 uppercase">{{ $r['tourism']->category ?: 'Kunjungan' }}</span>
                                            @endif
                                        </td>
                                        {{-- Nilai Akhir --}}
                                        <td class="py-4 px-4 text-center">
                                            <div
                                                class="text-xl font-extrabold {{ $r['rank'] <= 3 ? 'text-forest-700' : 'text-gray-700' }}">
                                                {{ number_format($r['score'], 2) }}
                                            </div>
                                            <div
                                                class="text-[10px] font-medium mt-0.5
                                                        {{ $r['score'] >= 0.85 ? 'text-forest-500' : ($r['score'] >= 0.70 ? 'text-blue-500' : ($r['score'] >= 0.55 ? 'text-amber-500' : 'text-gray-400')) }}">
                                                {{ $r['score'] >= 0.85 ? 'Sangat Baik' : ($r['score'] >= 0.70 ? 'Baik' : ($r['score'] >= 0.55 ? 'Cukup Baik' : 'Kurang')) }}
                                            </div>
                                        </td>
                                        {{-- Per-Criteria Weighted Values --}}
                                        @foreach($criteria as $c)
                                            <td class="py-4 px-4 text-center font-mono text-sm text-gray-600">
                                                {{ number_format($sawData['weightedMatrix'][$r['tourism']->id][$c->code] ?? 0, 2) }}
                                            </td>
                                        @endforeach
                                        {{-- Aksi --}}
                                        <td class="py-4 px-4 text-center">
                                            <button
                                                onclick="alert('Detail {{ $r['tourism']->name }}\nNilai Akhir: {{ number_format($r['score'], 4) }}\nRanking: #{{ $r['rank'] }}')"
                                                class="inline-flex items-center text-xs font-semibold text-forest-600 bg-forest-50 hover:bg-forest-100 border border-forest-200 px-3 py-1.5 rounded-lg transition-colors">
                                                <i data-lucide="eye" class="w-3.5 h-3.5 mr-1"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Footer Info --}}
                    <div class="px-6 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <div class="text-xs text-gray-500">Menampilkan {{ count($sawData['ranking']) }} data</div>
                        <div class="text-xs text-gray-400">Perhitungan berdasarkan metode SAW • {{ $criteria->count() }}
                            kriteria</div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="bg-forest-50 border border-forest-200 rounded-xl p-4 flex items-start space-x-3">
                    <i data-lucide="info" class="w-5 h-5 text-forest-500 mt-0.5 shrink-0"></i>
                    <div class="text-sm text-forest-700">
                        <strong>Keterangan:</strong> Nilai akhir (Vi) diperoleh dari proses perhitungan SAW berdasarkan
                        bobot dan nilai setiap kriteria.
                        Semakin tinggi nilai Vi, semakin direkomendasikan wisata tersebut.
                    </div>
                </div>
            </div>

            <!-- 8. Pengaturan -->
            <div class="flex-1 overflow-y-auto p-8" x-show="activeMenu === 'pengaturan'" style="display: none;">
                <h1 class="text-3xl font-display font-medium text-gray-800 mb-6">Pengaturan Website</h1>

                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-6 rounded shadow-sm border border-gray-200">
                    <div>
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h2 class="text-lg font-bold text-gray-800">Kategori Wisata</h2>
                            <button @click="openSettingModal('jenis_wisata')"
                                class="bg-forest-600 hover:bg-forest-700 text-white p-1.5 rounded"
                                title="Tambah Kategori"><i data-lucide="plus" class="w-4 h-4"></i></button>
                        </div>
                        <ul class="space-y-2">
                            @foreach($jenisWisata as $item)
                                <li class="flex justify-between items-center bg-gray-50 p-3 rounded border border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-white p-2 rounded shadow-sm border border-gray-200 text-forest-600">
                                            <i data-lucide="{{ $item->icon ?: 'circle' }}" class="w-4 h-4"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-800">{{ $item->label }}</div>
                                            <div class="text-xs text-gray-500">{{ $item->value }}</div>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <span
                                            class="px-2 py-0.5 rounded text-xs {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                        <button @click="openSettingModal('jenis_wisata', {{ Js::from($item) }})"
                                            class="text-blue-500 hover:text-blue-700"><i data-lucide="edit"
                                                class="w-4 h-4"></i></button>
                                        <button @click="openDeleteSettingModal({{ Js::from($item) }})"
                                            class="text-red-500 hover:text-red-700"><i data-lucide="trash-2"
                                                class="w-4 h-4"></i></button>
                                    </div>
                                </li>
                            @endforeach
                            @if($jenisWisata->isEmpty())
                            <li class="text-sm text-gray-400">Tidak ada data.</li> @endif
                        </ul>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h2 class="text-lg font-bold text-gray-800">Gambar Homepage</h2>
                            <button @click="openSettingModal('homepage_images')"
                                class="bg-forest-600 hover:bg-forest-700 text-white p-1.5 rounded"
                                title="Tambah Gambar"><i data-lucide="plus" class="w-4 h-4"></i></button>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($homepageImages as $item)
                                <div class="relative group rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                    <img src="{{ $item->image }}" alt="{{ $item->label }}" class="w-full h-32 object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-2">
                                        <div class="flex justify-end space-x-1">
                                            <button @click="openSettingModal('homepage_images', {{ Js::from($item) }})"
                                                class="bg-white text-blue-600 p-1 rounded-sm"><i data-lucide="edit"
                                                    class="w-3.5 h-3.5"></i></button>
                                            <button @click="openDeleteSettingModal({{ Js::from($item) }})"
                                                class="bg-white text-red-600 p-1 rounded-sm"><i data-lucide="trash-2"
                                                    class="w-3.5 h-3.5"></i></button>
                                        </div>
                                        <div
                                            class="text-white text-xs font-semibold bg-black/40 px-2 py-1 rounded truncate">
                                            {{ $item->label }}
                                        </div>
                                    </div>
                                    <div
                                        class="absolute top-2 left-2 bg-white px-1.5 rounded text-[10px] font-bold text-gray-700 shadow">
                                        {{ $item->sort_order }}
                                    </div>
                                    @if(!$item->is_active)
                                        <div
                                            class="absolute top-2 left-8 bg-red-500 text-white px-1.5 rounded text-[10px] font-bold shadow">
                                            Nonaktif</div>
                                    @endif
                                </div>
                            @endforeach
                            @if($homepageImages->isEmpty())
                            <div class="col-span-2 text-sm text-gray-400">Tidak ada data gambar.</div> @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modals Overlay -->
            <div x-show="modal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                style="display: none;" x-transition.opacity>

                <!-- Add / Edit Modal Box -->
                <div x-show="modalType === 'add' || modalType === 'edit'" @click.away="modal = false"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
                    x-transition.scale.duration.200ms>
                    <div
                        class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 sticky top-0 z-10">
                        <h2 class="text-xl font-display font-semibold text-gray-800"
                            x-text="modalType === 'add' ? 'Tambah Destinasi Wisata' : 'Edit Destinasi Wisata'"></h2>
                        <button @click="modal = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x"
                                class="w-6 h-6"></i></button>
                    </div>

                    <div class="p-6">
                        <form enctype="multipart/form-data"
                            :action="modalType === 'add' ? '{{ route('tourism.store') }}' : '/admin/tourism/' + form.id"
                            method="POST" class="space-y-5">
                            @csrf
                            <!-- If Editing, add Method PUT -->
                            <template x-if="modalType === 'edit'">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <!-- Nama Wisata -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Objek Wisata <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="name" x-model="form.name" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-forest-500 focus:ring-2 focus:ring-forest-100 transition-all">
                            </div>

                            <!-- Jenis Wisata (Kategori) -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Jenis Wisata <span
                                        class="text-red-500">*</span></label>
                                <select name="category" x-model="form.category"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-forest-500 focus:ring-2 focus:ring-forest-100 bg-white transition-all">
                                    <option value="">Pilih Jenis...</option>
                                    @foreach($jenisWisata->where('is_active', true) as $jw)
                                        <option value="{{ $jw->label }}">{{ $jw->label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi</label>
                                <textarea name="description" x-model="form.description" rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-forest-500 focus:ring-2 focus:ring-forest-100 transition-all"
                                    placeholder="Deskripsi singkat mengenai objek wisata..."></textarea>
                            </div>

                            <!-- Titik Lokasi Peta -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Titik Lokasi Peta</label>
                                <p class="text-xs text-gray-500 mb-2">Geser penanda peta ke lokasi destinasi untuk
                                    mendapatkan koordinat otomatis.</p>

                                <div id="map-picker"
                                    class="w-full h-64 border border-gray-300 rounded-lg overflow-hidden z-10 shadow-sm relative"
                                    style="z-index: 10;"></div>

                                <div class="mt-2 flex items-center justify-between text-xs text-forest-700 bg-forest-50 border border-forest-100 p-2.5 rounded-lg shadow-sm">
                                    <div class="flex items-center font-medium flex-1">
                                        <i data-lucide="map-pin" class="w-4 h-4 mr-2 shrink-0"></i> Koodinat / Lokasi:
                                        <input type="text" name="map_url" x-model="form.map_url"
                                            class="font-mono ml-2 w-full max-w-[280px] text-gray-800 bg-white px-2 py-1.5 rounded border border-gray-300 shadow-sm focus:outline-none focus:border-forest-500 focus:ring-1 focus:ring-forest-500"
                                            placeholder="-7.140709, 107.392044 atau Nama Lokasi">
                                    </div>
                                    <button type="button" @click="
                                        if (form.map_url) {
                                            let parts = form.map_url.split(',');
                                            if (parts.length >= 2) {
                                                let lat = parseFloat(parts[0]);
                                                let lng = parseFloat(parts[1]);
                                                if (!isNaN(lat) && !isNaN(lng)) {
                                                    mapInstance.setView([lat, lng], 15);
                                                    markerInstance.setLatLng([lat, lng]);
                                                    form.distance_km = calculateDistance(lat, lng);
                                                }
                                            }
                                        }
                                        setTimeout(() => mapInstance.invalidateSize(), 50);
                                    "
                                        class="text-forest-600 hover:text-earth-600 underline ml-3 shrink-0">Refresh Peta</button>
                                </div>
                            </div>

                            <!-- Harga & Jarak -->
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Harga WNI (Rp)</label>
                                    <input type="number" name="price_wni" x-model="form.price_wni"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-forest-500 focus:ring-2 focus:ring-forest-100 transition-all"
                                        placeholder="50000">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Harga WNA (Rp)</label>
                                    <input type="number" name="price_wna" x-model="form.price_wna"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-forest-500 focus:ring-2 focus:ring-forest-100 transition-all"
                                        placeholder="100000">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Jarak (km) <span class="text-xs font-normal text-gray-400">(Otomatis)</span></label>
                                    <input type="number" step="any" name="distance_km" x-model="form.distance_km"
                                        class="w-full border border-gray-200 bg-gray-100 text-gray-600 rounded-lg px-4 py-2.5 outline-none cursor-not-allowed"
                                        placeholder="Pilih lokasi peta..." readonly>
                                </div>
                            </div>

                            <!-- Fasilitas -->
                            <div x-data="{
                                newFacility: '',
                                selected: []
                            }" x-effect="
                                let _ = modal + modalType + form.id;
                                selected = (form.facilities_list || '').split(',').map(s => s.trim()).filter(Boolean);
                            ">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Fasilitas Utama</label>
                                
                                <div class="flex items-center space-x-2 mb-3">
                                    <input type="text" x-model="newFacility" @keydown.enter.prevent="if(newFacility.trim() && !selected.includes(newFacility.trim())) { selected.push(newFacility.trim()); form.facilities_list = selected.join(', '); newFacility = ''; }" placeholder="Ketik nama fasilitas..." class="flex-1 border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-forest-500 focus:ring-2 focus:ring-forest-100 transition-all text-sm">
                                    <button type="button" @click="if(newFacility.trim() && !selected.includes(newFacility.trim())) { selected.push(newFacility.trim()); form.facilities_list = selected.join(', '); newFacility = ''; setTimeout(() => typeof lucide !== 'undefined' && lucide.createIcons(), 50); }" class="bg-forest-600 hover:bg-forest-700 text-white px-4 py-2.5 rounded-lg text-sm font-bold transition-all flex items-center justify-center shrink-0">
                                        <i data-lucide="plus" class="w-4 h-4 mr-2 block"></i> Tambah
                                    </button>
                                </div>

                                <div class="flex flex-wrap gap-2 text-sm">
                                    <template x-for="fac in selected" :key="fac">
                                        <div class="inline-flex items-center px-3 py-1.5 rounded-full bg-forest-50 border border-forest-200 text-forest-700 font-medium shadow-sm">
                                            <span x-text="fac" class="mr-2"></span>
                                            <button type="button" @click="selected = selected.filter(s => s !== fac); form.facilities_list = selected.join(', ');" class="text-forest-400 hover:text-red-500 transition-colors focus:outline-none flex items-center justify-center rounded-full hover:bg-red-50 p-0.5">
                                                <svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><line x1='18' y1='6' x2='6' y2='18'></line><line x1='6' y1='6' x2='18' y2='18'></line></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <input type="hidden" name="facilities_list" x-model="form.facilities_list">
                            </div>

                            <!-- Upload Gambar (Multi) -->
                            <div x-data="{
                                filesArray: [],
                                previews: [],
                                handleFiles(event) {
                                    const files = event.target.files;
                                    for (let i = 0; i < files.length; i++) {
                                        this.filesArray.push(files[i]);
                                        const reader = new FileReader();
                                        reader.onload = (e) => this.previews.push({ url: e.target.result, name: files[i].name });
                                        reader.readAsDataURL(files[i]);
                                    }
                                    this.syncInput();
                                },
                                removeFile(index) {
                                    this.filesArray.splice(index, 1);
                                    this.previews.splice(index, 1);
                                    this.syncInput();
                                },
                                syncInput() {
                                    const dt = new DataTransfer();
                                    this.filesArray.forEach(f => dt.items.add(f));
                                    if (this.$refs && this.$refs.fileInput) {
                                        this.$refs.fileInput.files = dt.files;
                                    }
                                },
                                resetUpload() {
                                    this.filesArray = [];
                                    this.previews = [];
                                    if (this.$refs && this.$refs.fileInput) this.$refs.fileInput.value = '';
                                }
                            }" x-effect="if (!modal) resetUpload()">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Upload Foto
                                    <span class="text-xs text-gray-400 font-normal ml-1">(Bisa pilih lebih dari 1)</span>
                                    <span class="text-xs text-gray-400 font-normal" x-show="modalType === 'edit'"> — Kosongkan jika tidak ingin mengganti</span>
                                </label>

                                <!-- Existing Gallery Preview (Edit mode) -->
                                <template x-if="modalType === 'edit' && Array.isArray(form.gallery) && form.gallery.length > 0">
                                    <div class="mb-3">
                                        <p class="text-xs text-gray-500 mb-1.5 font-medium">Foto saat ini:</p>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="(img, gi) in form.gallery" :key="'eg-' + gi">
                                                <div class="relative group">
                                                    <img :src="img.startsWith('http') || img.startsWith('data:') ? img : '{{ rtrim(asset(''), '/') }}/' + img.replace(/^\/+/, '')"
                                                        class="h-16 w-16 rounded-lg shadow-sm border border-gray-200 object-cover" alt="Gallery">
                                                    <button type="button" @click="form.gallery.splice(gi, 1)" class="absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full p-0.5 shadow-md opacity-0 group-hover:opacity-100 transition-opacity transform hover:scale-110 focus:outline-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                <input type="hidden" name="existing_gallery" :value="JSON.stringify(form.gallery || [])">
                                
                                <!-- Fallback: show main image if no gallery -->
                                <template x-if="modalType === 'edit' && form.image && (!Array.isArray(form.gallery) || form.gallery.length === 0)">
                                    <div class="mb-3">
                                        <p class="text-xs text-gray-500 mb-1.5 font-medium">Foto saat ini:</p>
                                        <div class="relative group inline-block">
                                            <img :src="form.image.startsWith('http') || form.image.startsWith('data:') ? form.image : '{{ rtrim(asset(''), '/') }}/' + form.image.replace(/^\/+/, '')"
                                                class="h-16 rounded-lg shadow-sm border border-gray-200 object-cover" alt="Current Image">
                                            <button type="button" @click="form.image = null; form.gallery = []" class="absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full p-0.5 shadow-md opacity-0 group-hover:opacity-100 transition-opacity transform hover:scale-110 focus:outline-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- Upload Area -->
                                <label class="block w-full border-2 border-dashed border-gray-300 rounded-xl p-5 text-center cursor-pointer hover:border-forest-400 hover:bg-forest-50/30 transition-all group relative overflow-hidden" :class="{'border-red-400 bg-red-50': '{{ $errors->has('images.*') ? 'true' : 'false' }}' === 'true'}">
                                    <i data-lucide="image-plus" class="w-8 h-8 text-gray-300 mx-auto mb-2 group-hover:text-forest-400 transition-colors"></i>
                                    <p class="text-sm text-gray-500 group-hover:text-forest-600 font-medium">Klik untuk memilih foto (Bisa berkali-kali)</p>
                                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — Maks 2MB per file</p>
                                    <input type="file" name="images[]" multiple accept="image/*" class="hidden" x-ref="fileInput" @change="handleFiles($event)">
                                </label>
                                @error('images.*')
                                    <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                                @if($errors->any())
                                    <div class="mt-2 text-xs text-red-500">
                                        @foreach($errors->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- New Files Preview -->
                                <div x-show="previews.length > 0" class="mt-3">
                                    <p class="text-xs text-forest-600 mb-1.5 font-semibold"><span x-text="previews.length"></span> foto dipilih untuk diupload:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="(p, pi) in previews" :key="'np-' + pi">
                                            <div class="relative group">
                                                <img :src="p.url" class="h-16 w-16 rounded-lg shadow-sm border border-forest-200 object-cover">
                                                <button type="button" @click="removeFile(pi)" class="absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full p-0.5 shadow-md opacity-0 group-hover:opacity-100 transition-opacity transform hover:scale-110 focus:outline-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
                                <select name="status" x-model="form.status"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-forest-500 focus:ring-2 focus:ring-forest-100 bg-white transition-all">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>



                            <div class="pt-4 flex justify-end space-x-3 border-t border-gray-100 mt-4">
                                <button type="button" @click="modal = false"
                                    class="px-5 py-2.5 border border-gray-300 text-gray-700 bg-white rounded-lg font-medium hover:bg-gray-50 transition-colors">Batal</button>
                                <button type="submit"
                                    class="px-5 py-2.5 bg-forest-600 text-white rounded-lg font-bold hover:bg-forest-700 shadow-md flex items-center transition-colors">
                                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Confirmation Modal Box -->
                <div x-show="modalType === 'delete'" @click.away="modal = false"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden"
                    x-transition.scale.duration.200ms>
                    <div class="p-6 text-center">
                        <div
                            class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Hapus Data?</h2>
                        <p class="text-gray-500 text-sm mb-6">Anda yakin ingin menghapus <strong><span
                                    x-text="form.name"></span></strong>? Tindakan ini tidak bisa dibatalkan.</p>

                        <form :action="'/admin/tourism/' + form.id" method="POST" class="flex justify-center space-x-3">
                            @csrf
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="button" @click="modal = false"
                                class="px-4 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg font-medium hover:bg-gray-50">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 flex items-center shadow-lg shadow-red-500/30">
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Add / Edit Setting Modal Box -->
                <div x-show="modalType === 'setting'" @click.away="modal = false"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-auto max-h-[90vh]"
                    x-transition.scale.duration.200ms>
                    <div
                        class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 sticky top-0 z-10">
                        <h2 class="text-xl font-display font-semibold text-gray-800"
                            x-text="settingModalType === 'add' ? 'Tambah Pengaturan' : 'Edit Pengaturan'"></h2>
                        <button @click="modal = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x"
                                class="w-6 h-6"></i></button>
                    </div>
                    <div class="p-6">
                        <form enctype="multipart/form-data"
                            :action="settingModalType === 'add' ? '{{ route('settings.store') }}' : '/admin/settings/' + settingForm.id"
                            method="POST" class="space-y-4">
                            @csrf
                            <template x-if="settingModalType === 'edit'">
                                <input type="hidden" name="_method" value="PUT">
                            </template>
                            <input type="hidden" name="group" x-model="settingForm.group">

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Label</label>
                                <input type="text" name="label" x-model="settingForm.label" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 outline-none focus:border-forest-500 focus:ring-2 focus:ring-forest-100">
                            </div>

                            <div x-show="settingForm.group === 'jenis_wisata' || settingForm.group === 'fasilitas'">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Value / Kode</label>
                                <input type="text" name="value" x-model="settingForm.value"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 outline-none focus:border-forest-500 focus:ring-2 focus:ring-forest-100 text-sm"
                                    placeholder="Contoh: wifi_gratis">
                            </div>

                            <div x-show="settingForm.group === 'jenis_wisata' || settingForm.group === 'fasilitas'">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Ikon (Lucide)</label>
                                <input type="text" name="icon" x-model="settingForm.icon"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 outline-none focus:border-forest-500 focus:ring-2 focus:ring-forest-100 text-sm"
                                    placeholder="Contoh: check">
                                <p class="text-xs text-gray-500 mt-1">Gunakan nama ikon dari <a
                                        href="https://lucide.dev/icons" target="_blank"
                                        class="text-blue-500 hover:underline">lucide.dev</a></p>
                            </div>

                            <div x-show="settingForm.group === 'homepage_images'">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Upload Gambar</label>
                                <input type="file" name="image" accept="image/*"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-1.5 outline-none focus:border-forest-500 text-sm bg-white shadow-sm file:bg-forest-50 file:text-forest-700 file:border-0 file:rounded-full file:px-4 file:py-1 file:mr-4 file:font-semibold">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Urutan (Sort)</label>
                                    <input type="number" name="sort_order" x-model="settingForm.sort_order"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 outline-none focus:border-forest-500 focus:ring-2 focus:ring-forest-100">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                                    <label class="relative inline-flex items-center cursor-pointer mt-1">
                                        <input type="checkbox" name="is_active" value="1"
                                            x-model="settingForm.is_active" class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-forest-600">
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-700"
                                            x-text="settingForm.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="pt-4 flex justify-end space-x-3 border-t border-gray-100 mt-4">
                                <button type="button" @click="modal = false"
                                    class="px-5 py-2.5 border border-gray-300 text-gray-700 bg-white rounded-lg font-medium hover:bg-gray-50 transition-colors">Batal</button>
                                <button type="submit"
                                    class="px-5 py-2.5 bg-forest-600 text-white rounded-lg font-bold hover:bg-forest-700 shadow-md flex items-center transition-colors">
                                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Setting Modal Box -->
                <div x-show="modalType === 'delete_setting'" @click.away="modal = false"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden"
                    x-transition.scale.duration.200ms>
                    <div class="p-6 text-center">
                        <div
                            class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Hapus Pengaturan?</h2>
                        <p class="text-gray-500 text-sm mb-6">Anda yakin ingin menghapus <strong><span
                                    x-text="settingForm.label"></span></strong>? Tindakan ini tidak bisa dibatalkan.</p>

                        <form :action="'/admin/settings/' + settingForm.id" method="POST"
                            class="flex justify-center space-x-3">
                            @csrf
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="button" @click="modal = false"
                                class="px-4 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg font-medium hover:bg-gray-50">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 shadow-lg shadow-red-500/30">Ya,
                                Hapus</button>
                        </form>
                    </div>
                </div>

                <!-- Add / Edit Criteria Modal Box -->
                <div x-show="modalType === 'criteria_add' || modalType === 'criteria_edit'" @click.away="modal = false"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-auto"
                    x-transition.scale.duration.200ms style="display: none;">
                    <div
                        class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 sticky top-0 z-10">
                        <h2 class="text-xl font-display font-semibold text-gray-800"
                            x-text="modalType === 'criteria_add' ? 'Tambah Kriteria' : 'Edit Kriteria'"></h2>
                        <button @click="modal = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x"
                                class="w-6 h-6"></i></button>
                    </div>
                    <div class="p-6">
                        <form
                            :action="modalType === 'criteria_add' ? '{{ route('criteria.store') }}' : '/admin/criteria/' + criteriaForm.id"
                            method="POST" class="space-y-4">
                            @csrf
                            <template x-if="modalType === 'criteria_edit'">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Kode Kriteria</label>
                                    <input type="text" name="code" x-model="criteriaForm.code" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 outline-none focus:border-forest-500 text-sm"
                                        placeholder="Contoh: C5">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Bobot (0.00 -
                                        1.00)</label>
                                    <input type="number" step="0.01" min="0" max="1" name="weight"
                                        x-model="criteriaForm.weight" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 outline-none focus:border-forest-500 text-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Kriteria</label>
                                <input type="text" name="name" x-model="criteriaForm.name" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 outline-none focus:border-forest-500 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tipe Atribut</label>
                                <select name="type" x-model="criteriaForm.type" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 outline-none focus:border-forest-500 text-sm bg-white">
                                    <option value="benefit">Benefit (Semakin besar semakin baik)</option>
                                    <option value="cost">Cost (Semakin kecil semakin baik)</option>
                                </select>
                            </div>

                            <div class="pt-4 flex justify-end space-x-3 border-t border-gray-100 mt-4">
                                <button type="button" @click="modal = false"
                                    class="px-5 py-2.5 border border-gray-300 text-gray-700 bg-white rounded-lg font-medium hover:bg-gray-50 transition-colors text-sm">Batal</button>
                                <button type="submit"
                                    class="px-5 py-2.5 bg-forest-600 text-white rounded-lg font-bold hover:bg-forest-700 shadow-md flex items-center transition-colors text-sm">
                                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Criteria Modal Box -->
                <div x-show="modalType === 'criteria_delete'" @click.away="modal = false"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden"
                    x-transition.scale.duration.200ms style="display: none;">
                    <div class="p-6 text-center">
                        <div
                            class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Hapus Kriteria?</h2>
                        <p class="text-gray-500 text-sm mb-6">Yakin ingin menghapus kriteria <strong
                                x-text="criteriaForm.code + ' - '"></strong> <strong
                                x-text="criteriaForm.name"></strong>? Tindakan ini tidak bisa dibatalkan.</p>

                        <form :action="'/admin/criteria/' + criteriaForm.id" method="POST"
                            class="flex justify-center space-x-3">
                            @csrf
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="button" @click="modal = false"
                                class="px-4 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg font-medium hover:bg-gray-50">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 shadow-lg shadow-red-500/30">Ya,
                                Hapus</button>
                        </form>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <!-- SAW Calculator Script -->
    <script>
        function sawCalculator() {
            return {
                selectedCategory: '',
                calcDate: '{{ date("Y-m-d") }}',
                hasComputed: false,
                isLoading: false,

                allTourisms: {!! Js::from($tourisms->map(fn($t) => [
    'id' => $t->id,
    'name' => $t->name,
    'category' => $t->category,
    'price' => (float) ($t->price_wni ?? 0),
    'facilities_count' => count(array_filter(array_map('trim', explode(',', $t->facilities_list ?? '')))),
    'distance' => (float) ($t->distance_km ?? 0),
    'rating' => round((float) ($t->ratings_avg_rating ?? 0), 2),
])) !!},

                sawCriteria: {!! Js::from($criteria->map(fn($c) => [
    'code' => $c->code,
    'name' => explode(' (', $c->name)[0],
    'type' => $c->type,
    'weight' => $c->weight,
])) !!},

                sawFiltered: [],
                sawDecision: {},
                sawNormalized: {},
                sawWeighted: {},
                sawScores: {},
                sawRanking: [],

                scoreFieldMap: {
                    C1: 'price',
                    C2: 'facilities_count',
                    C3: 'distance',
                    C4: 'rating'
                },

                fmt(n) { return parseFloat(n).toFixed(2); },

                hitungSAW() {
                    this.isLoading = true;
                    this.hasComputed = false;

                    setTimeout(() => {
                        // 1. Filter
                        if (this.selectedCategory) {
                            this.sawFiltered = this.allTourisms.filter(t =>
                                (t.category || '').toLowerCase() === this.selectedCategory.toLowerCase()
                            );
                        } else {
                            this.sawFiltered = [...this.allTourisms];
                        }

                        if (this.sawFiltered.length === 0) {
                            this.sawRanking = [];
                            this.isLoading = false;
                            this.hasComputed = true;
                            this.$nextTick(() => lucide.createIcons());
                            return;
                        }

                        // 2. Decision Matrix
                        let dm = {};
                        this.sawFiltered.forEach(t => {
                            let row = {};
                            this.sawCriteria.forEach(c => {
                                row[c.code] = parseFloat(t[this.scoreFieldMap[c.code]] || 0);
                            });
                            dm[t.id] = row;
                        });
                        this.sawDecision = dm;

                        // 3. Min/Max
                        let mm = {};
                        this.sawCriteria.forEach(c => {
                            let vals = Object.values(dm).map(r => r[c.code]).filter(v => v > 0);
                            mm[c.code] = {
                                min: vals.length ? Math.min(...vals) : 0,
                                max: vals.length ? Math.max(...vals) : 1
                            };
                        });

                        // 4. Normalized Matrix
                        let nm = {};
                        Object.keys(dm).forEach(tid => {
                            let nRow = {};
                            this.sawCriteria.forEach(c => {
                                let v = dm[tid][c.code];
                                if (c.type === 'benefit') {
                                    if (v === 0) nRow[c.code] = 0;
                                    else nRow[c.code] = parseFloat((v / mm[c.code].max).toFixed(4));
                                } else {
                                    if (v === 0) nRow[c.code] = 1;
                                    else nRow[c.code] = parseFloat((mm[c.code].min / v).toFixed(4));
                                }
                            });
                            nm[tid] = nRow;
                        });
                        this.sawNormalized = nm;

                        // 5. Weighted Matrix
                        let wm = {};
                        Object.keys(nm).forEach(tid => {
                            let wRow = {};
                            this.sawCriteria.forEach(c => {
                                wRow[c.code] = parseFloat((nm[tid][c.code] * c.weight).toFixed(4));
                            });
                            wm[tid] = wRow;
                        });
                        this.sawWeighted = wm;

                        // 6. Final Scores
                        let fs = {};
                        Object.keys(wm).forEach(tid => {
                            fs[tid] = parseFloat(Object.values(wm[tid]).reduce((a, b) => a + b, 0).toFixed(4));
                        });
                        this.sawScores = fs;

                        // 7. Ranking
                        let sorted = Object.entries(fs).sort((a, b) => b[1] - a[1]);
                        this.sawRanking = sorted.map((entry, idx) => ({
                            rank: idx + 1,
                            tourismId: parseInt(entry[0]),
                            tourism: this.sawFiltered.find(t => t.id === parseInt(entry[0])),
                            score: entry[1]
                        }));

                        this.isLoading = false;
                        this.hasComputed = true;

                        // Render table & ranking
                        this.$nextTick(() => {
                            this.renderTable();
                            this.renderRanking();
                            lucide.createIcons();
                        });
                    }, 400);
                },

                renderTable() {
                    const crt = this.sawCriteria;
                    const filtered = this.sawFiltered;
                    const dm = this.sawDecision;
                    const nm = this.sawNormalized;
                    const wm = this.sawWeighted;
                    const fs = this.sawScores;
                    const fmt = this.fmt;

                    let html = '<table class="w-full text-xs border-collapse">';
                    // Header row 1
                    html += '<thead><tr>';
                    html += '<th rowspan="2" class="bg-forest-600 text-white py-2 px-3 border border-forest-700 text-center w-8 rounded-tl-lg">No</th>';
                    html += '<th rowspan="2" class="bg-forest-600 text-white py-2 px-3 border border-forest-700 min-w-[140px]">Alternatif Wisata</th>';
                    html += '<th colspan="' + crt.length + '" class="bg-forest-600 text-white py-2 px-3 border border-forest-700 text-center">Matriks Keputusan (X)</th>';
                    html += '<th colspan="' + crt.length + '" class="bg-blue-600 text-white py-2 px-3 border border-blue-700 text-center">Matriks Normalisasi (R)</th>';
                    html += '<th colspan="' + crt.length + '" class="bg-earth-600 text-white py-2 px-3 border border-earth-700 text-center">Matriks Terbobot (V)</th>';
                    html += '<th rowspan="2" class="bg-gray-700 text-white py-2 px-3 border border-gray-800 text-center min-w-[70px] rounded-tr-lg">Yi</th>';
                    html += '</tr><tr>';

                    crt.forEach(c => {
                        html += '<th class="bg-forest-500 text-white py-2 px-2 border border-forest-600 text-center text-[10px] whitespace-nowrap">' + c.name + '<br><span class="opacity-75">(' + c.type.charAt(0).toUpperCase() + c.type.slice(1) + ')</span></th>';
                    });
                    crt.forEach(c => {
                        html += '<th class="bg-blue-500 text-white py-2 px-2 border border-blue-600 text-center text-[10px] whitespace-nowrap">' + c.name + '<br><span class="opacity-75">(' + c.type.charAt(0).toUpperCase() + c.type.slice(1) + ')</span></th>';
                    });
                    crt.forEach(c => {
                        html += '<th class="bg-earth-500 text-white py-2 px-2 border border-earth-600 text-center text-[10px] whitespace-nowrap">' + c.name + '<br><span class="opacity-75">(' + fmt(c.weight) + ')</span></th>';
                    });
                    html += '</tr></thead><tbody class="divide-y divide-gray-100">';

                    filtered.forEach((t, idx) => {
                        html += '<tr class="hover:bg-yellow-50/50 transition-colors">';
                        html += '<td class="py-2.5 px-3 text-center font-bold text-gray-500 border border-gray-100">' + (idx + 1) + '</td>';
                        html += '<td class="py-2.5 px-3 font-medium text-gray-800 border border-gray-100 whitespace-nowrap"><div class="max-w-[160px] truncate" title="' + t.name + '">' + t.name + '</div></td>';

                        crt.forEach(c => {
                            html += '<td class="py-2.5 px-2 text-center font-mono text-gray-700 border border-gray-100 bg-forest-50/30">' + (dm[t.id] ? dm[t.id][c.code] : 0) + '</td>';
                        });
                        crt.forEach(c => {
                            html += '<td class="py-2.5 px-2 text-center font-mono text-blue-700 border border-gray-100 bg-blue-50/30">' + (nm[t.id] ? fmt(nm[t.id][c.code]) : '0.00') + '</td>';
                        });
                        crt.forEach(c => {
                            html += '<td class="py-2.5 px-2 text-center font-mono text-earth-700 border border-gray-100 bg-earth-50/30">' + (wm[t.id] ? fmt(wm[t.id][c.code]) : '0.00') + '</td>';
                        });
                        html += '<td class="py-2.5 px-2 text-center font-mono font-bold text-gray-900 border border-gray-100 bg-gray-50">' + (fs[t.id] ? fmt(fs[t.id]) : '0.00') + '</td>';
                        html += '</tr>';
                    });

                    html += '</tbody></table>';
                    const container = document.getElementById('saw-table-container');
                    if (container) container.innerHTML = html;
                },

                renderRanking() {
                    const ranking = this.sawRanking;
                    const fmt = this.fmt;
                    let html = '';

                    ranking.slice(0, 5).forEach(r => {
                        let badgeCls = r.rank === 1 ? 'bg-forest-600 text-white shadow-lg shadow-forest-600/30'
                            : r.rank === 2 ? 'bg-earth-500 text-white'
                                : r.rank === 3 ? 'bg-amber-500 text-white'
                                    : 'bg-gray-300 text-gray-700';
                        let cardCls = r.rank === 1
                            ? 'bg-gradient-to-br from-forest-50 to-forest-100 border-forest-300 ring-2 ring-forest-200'
                            : 'bg-white border-gray-200';
                        let scoreCls = r.rank === 1 ? 'text-forest-700' : 'text-gray-700';

                        html += '<div class="flex-shrink-0 w-48 border rounded-2xl p-5 text-center shadow-sm hover:shadow-lg transition-all hover:-translate-y-1 ' + cardCls + '">';
                        html += '<div class="w-10 h-10 mx-auto mb-3 rounded-full flex items-center justify-center font-bold text-lg ' + badgeCls + '">' + r.rank + '</div>';
                        html += '<div class="font-bold text-gray-800 text-sm mb-1 line-clamp-2 min-h-[40px]">' + r.tourism.name + '</div>';
                        html += '<div class="text-3xl font-extrabold mt-2 ' + scoreCls + '">' + fmt(r.score) + '</div>';
                        html += '</div>';
                    });

                    if (ranking.length > 5) {
                        html += '<div class="flex-shrink-0 w-48 bg-gray-50 border border-dashed border-gray-300 rounded-2xl p-5 text-center flex items-center justify-center">';
                        html += '<div class="text-sm text-gray-500"><i data-lucide="list" class="w-6 h-6 mx-auto mb-2 text-gray-400"></i>+ ' + (ranking.length - 5) + ' lainnya</div>';
                        html += '</div>';
                    }

                    const container = document.getElementById('saw-ranking-container');
                    if (container) container.innerHTML = html;
                }
            };
        }
    </script>

    <!-- Scripts -->
    <script>
        lucide.createIcons();
        document.addEventListener('DOMContentLoaded', function () {
            // Check if chart elements exist before trying to mount (in case they differ by tab context)
            const bcel = document.getElementById('barChart');
            if (bcel) {
                new Chart(bcel.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                        datasets: [{ data: {!! json_encode($dailyVisitors) !!}, backgroundColor: '#9bc8af', hoverBackgroundColor: '#468b68', barPercentage: 0.5, borderRadius: 4 }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 } } }
                    }
                });
                new Chart(document.getElementById('pieChart').getContext('2d'), {
                    type: 'pie', data: { labels: ['Domestik', 'Mancanegara'], datasets: [{ data: [{{ $originStats['domestik'] }}, {{ $originStats['mancanegara'] }}], backgroundColor: ['#468b68', '#c67c4f'], hoverOffset: 4 }] },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
                });
            }
        });
    </script>
</body>

</html>