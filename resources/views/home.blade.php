@extends('layouts.app')

@section('content')
    <div x-data="homeController()">
        <!-- Hero Section (beranda) -->
        <section id="beranda"
            class="relative pt-28 pb-32 bg-forest-900 min-h-[70vh] flex flex-col items-center justify-center overflow-hidden group"
            x-data="{
                images: {{ $homepageImages->pluck('image')->values()->toJson() }},
                currentIndex: 0,
                interval: null,
                init() {
                    if(this.images.length > 1) {
                        this.startInterval();
                    }
                },
                startInterval() {
                    this.interval = setInterval(() => {
                        this.next();
                    }, 5000);
                },
                stopInterval() {
                    clearInterval(this.interval);
                },
                next() {
                    this.currentIndex = (this.currentIndex + 1) % this.images.length;
                },
                prev() {
                    this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
                },
                goTo(index) {
                    this.currentIndex = index;
                }
            }"
            @mouseenter="stopInterval()"
            @mouseleave="if(images.length > 1) startInterval()">

            <!-- Dynamic Background Slider -->
            <template x-for="(img, index) in images" :key="index">
                <div class="absolute inset-0 transition-opacity duration-1000"
                    :class="currentIndex === index ? 'opacity-40 z-0' : 'opacity-0 -z-10'">
                    <img :src="img" class="object-cover w-full h-full" alt="Background">
                </div>
            </template>
            
            <!-- Slider Controls -->
            <template x-if="images.length > 1">
                <div class="absolute inset-0 z-10 pointer-events-none flex flex-col justify-center">
                    <!-- Arrows -->
                    <div class="max-w-7xl w-full mx-auto px-4 flex justify-between items-center pointer-events-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button @click="prev()" class="w-12 h-12 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/20 transition-all hover:scale-110 focus:outline-none hidden md:flex">
                            <i data-lucide="chevron-left" class="w-6 h-6"></i>
                        </button>
                        <button @click="next()" class="w-12 h-12 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/20 transition-all hover:scale-110 focus:outline-none hidden md:flex">
                            <i data-lucide="chevron-right" class="w-6 h-6"></i>
                        </button>
                    </div>
                    
                    <!-- Indicators -->
                    <div class="absolute bottom-8 left-0 right-0 flex justify-center gap-2 pointer-events-auto">
                        <template x-for="(_, i) in images" :key="'ind-'+i">
                            <button @click="goTo(i)" 
                                class="h-2 rounded-full transition-all duration-300 focus:outline-none" 
                                :class="currentIndex === i ? 'w-8 bg-white' : 'w-2 bg-white/50 hover:bg-white/70'">
                            </button>
                        </template>
                    </div>
                </div>
            </template>
            <!-- Overlay gradient for text readability -->
            <div class="absolute inset-0 bg-gradient-to-b from-forest-900/60 via-forest-900/40 to-forest-50/90 z-0"></div>

            <!-- Decorative background blobs -->
            <div
                class="absolute top-0 right-0 lg:-mr-20 lg:-mt-20 w-64 h-64 lg:w-96 lg:h-96 rounded-full bg-forest-200/20 mix-blend-multiply filter blur-3xl opacity-50 animate-blob">
            </div>
            <div
                class="absolute bottom-0 left-0 lg:-ml-20 lg:-mb-20 w-64 h-64 lg:w-96 lg:h-96 rounded-full bg-earth-200/20 mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000">
            </div>

            <div class="relative z-10 text-center px-4 md:px-8 max-w-5xl mx-auto py-12 md:py-20 w-full">
                <div
                    class="inline-block mb-6 px-4 py-1.5 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-white text-sm font-semibold tracking-wide uppercase shadow-sm">
                    <i data-lucide="leaf" class="w-4 h-4 inline mr-1 -mt-1 text-earth-300"></i> {{ __('messages.hero_badge') }}
                </div>
                <h1
                    class="text-5xl sm:text-6xl md:text-7xl font-display font-extrabold text-white mb-6 leading-tight tracking-tight drop-shadow-lg">
                    {{ __('messages.hero_welcome') }} <br class="hidden sm:block">
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-earth-400 to-green-300 inline-block hover:scale-105 hover:rotate-1 transition-all duration-300 drop-shadow-md">{{ __('messages.hero_in') }}</span>
                </h1>
                <p class="text-lg sm:text-xl text-white mb-10 max-w-3xl mx-auto font-medium leading-relaxed drop-shadow-md">
                    {{ __('messages.hero_desc') }}
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto">
                    <a href="#informasi-wisata"
                        class="w-full sm:w-auto group inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-forest-900 bg-white backdrop-blur-sm border-2 border-white rounded-full hover:bg-forest-50 hover:border-forest-50 transition-all duration-300 shadow-xl hover:shadow-2xl hover:-translate-y-1">
                        <i data-lucide="info" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform"></i> {{ __('messages.btn_view_info') }}
                    </a>
                    @auth
                        <a href="{{ route('visitor.dashboard') }}"
                            class="w-full sm:w-auto group inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-earth-500 to-earth-600 border-2 border-transparent rounded-full hover:from-earth-400 hover:to-earth-500 transition-all duration-300 shadow-xl hover:-translate-y-1">
                            <i data-lucide="sparkles"
                                class="w-5 h-5 mr-2 animate-pulse group-hover:scale-110 transition-transform"></i> {{ __('messages.btn_find_recs') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="w-full sm:w-auto group inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-earth-500 to-earth-600 border-2 border-transparent rounded-full hover:from-earth-400 hover:to-earth-500 transition-all duration-300 shadow-xl hover:-translate-y-1">
                            <i data-lucide="log-in"
                                class="w-5 h-5 mr-2 animate-pulse group-hover:scale-110 transition-transform"></i> {{ __('messages.btn_login_recs') }}
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="relative z-20 -mt-16 sm:-mt-24 mb-16 px-4">
            <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-8">

                <div
                    class="bg-white/90 backdrop-blur-lg p-6 rounded-3xl shadow-xl shadow-forest-900/5 border border-white flex flex-col sm:flex-row items-center text-center sm:text-left group hover:-translate-y-3 hover:shadow-2xl transition-all duration-500 cursor-pointer">
                    <div
                        class="w-16 h-16 sm:w-20 sm:h-20 bg-forest-50 rounded-full flex items-center justify-center mb-4 sm:mb-0 sm:mr-5 group-hover:bg-forest-600 transition-colors duration-500 shrink-0">
                        <i data-lucide="mountain"
                            class="w-8 h-8 sm:w-10 sm:h-10 text-forest-600 group-hover:text-white group-hover:scale-110 transition-all duration-500"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-forest-900 text-lg mb-1 group-hover:text-forest-700 transition-colors">
                            {{ __('messages.feat_1_title') }}</h4>
                        <p class="text-sm text-forest-500 leading-snug">{{ __('messages.feat_1_desc') }}</p>
                    </div>
                </div>

                <div
                    class="bg-white/90 backdrop-blur-lg p-6 rounded-3xl shadow-xl shadow-earth-900/5 border border-white flex flex-col sm:flex-row items-center text-center sm:text-left group hover:-translate-y-3 hover:shadow-2xl transition-all duration-500 cursor-pointer">
                    <div
                        class="w-16 h-16 sm:w-20 sm:h-20 bg-earth-50 rounded-full flex items-center justify-center mb-4 sm:mb-0 sm:mr-5 group-hover:bg-earth-500 transition-colors duration-500 shrink-0">
                        <i data-lucide="heart"
                            class="w-8 h-8 sm:w-10 sm:h-10 text-earth-600 group-hover:text-white group-hover:scale-110 transition-all duration-500"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-forest-900 text-lg mb-1 group-hover:text-earth-600 transition-colors">
                            {{ __('messages.feat_2_title') }}</h4>
                        <p class="text-sm text-forest-500 leading-snug">{{ __('messages.feat_2_desc') }}</p>
                    </div>
                </div>

                <div
                    class="bg-white/90 backdrop-blur-lg p-6 rounded-3xl shadow-xl shadow-forest-900/5 border border-white flex flex-col sm:flex-row items-center text-center sm:text-left group hover:-translate-y-3 hover:shadow-2xl transition-all duration-500 cursor-pointer">
                    <div
                        class="w-16 h-16 sm:w-20 sm:h-20 bg-forest-50 rounded-full flex items-center justify-center mb-4 sm:mb-0 sm:mr-5 group-hover:bg-forest-600 transition-colors duration-500 shrink-0">
                        <i data-lucide="map-pin"
                            class="w-8 h-8 sm:w-10 sm:h-10 text-forest-600 group-hover:text-white group-hover:scale-110 transition-all duration-500"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-forest-900 text-lg mb-1 group-hover:text-forest-700 transition-colors">
                            {{ __('messages.feat_3_title') }}</h4>
                        <p class="text-sm text-forest-500 leading-snug">{{ __('messages.feat_3_desc') }}</p>
                    </div>
                </div>

            </div>
        </section>

        <!-- Informasi Wisata Section -->
        <section id="informasi-wisata" class="py-24 bg-gradient-to-b from-white to-forest-50 relative">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div
                        class="inline-block mb-4 px-4 py-1.5 rounded-full bg-forest-100 border border-forest-200 text-forest-700 text-sm font-semibold tracking-wide uppercase">
                        {{ __('messages.info_badge') }}
                    </div>
                    <h2 class="text-4xl sm:text-5xl font-display font-extrabold text-forest-900 mb-6">{{ __('messages.info_title') }}</h2>
                    <p class="text-lg text-forest-600 max-w-2xl mx-auto">{{ __('messages.info_desc') }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    @foreach($tourisms->take(3) as $index => $wisataCard)
                        @if($index === 1)
                            {{-- Middle Card: Earth gradient highlighted --}}
                            <div
                                class="bg-gradient-to-br from-earth-600 to-earth-700 rounded-2xl shadow-lg hover:shadow-2xl hover:shadow-earth-900/20 p-0 flex flex-col transform hover:-translate-y-2 transition-all duration-500 group relative overflow-hidden text-white">
                                {{-- Image --}}
                                <div class="relative w-full shrink-0 overflow-hidden rounded-t-2xl" style="height: 200px;">
                                    <img src="{{ $wisataCard->image ? (Str::startsWith($wisataCard->image, 'http') ? $wisataCard->image : asset($wisataCard->image)) : 'https://via.placeholder.com/400x300' }}"
                                        alt="{{ $wisataCard->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-earth-700 via-earth-700/40 to-transparent">
                                    </div>
                                    @if($wisataCard->category)
                                        <span
                                            class="absolute top-3 left-3 bg-white/30 backdrop-blur text-white text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full border border-white/30">{{ $wisataCard->category }}</span>
                                    @endif
                                </div>
                                <div class="p-6 pt-4 flex flex-col flex-1">
                                    <div
                                        class="absolute -right-12 -bottom-12 w-36 h-36 bg-white/10 rounded-full group-hover:scale-150 transition-transform duration-700 ease-out z-0">
                                    </div>
                                    <div class="relative z-10 flex-1">
                                        <h3 class="text-lg font-display font-bold text-white mb-1.5 line-clamp-1">{{ $wisataCard->name }}</h3>
                                        <div class="flex items-center mb-2">
                                            <i data-lucide="star" class="w-3.5 h-3.5 text-gold-400 fill-current"></i>
                                            <span class="text-xs font-bold ml-1.5">{{ number_format($wisataCard->ratings_avg_rating ?? 0, 1) }}</span>
                                            <span class="text-[11px] text-white/70 ml-1">({{ $wisataCard->ratings_count }} {{ __('messages.reviews') }})</span>
                                        </div>
                                        <p class="text-earth-50 text-xs leading-relaxed mb-4 line-clamp-2">
                                            {{ $wisataCard->description }}</p>
                                        <div
                                            class="inline-flex items-center bg-white/20 border border-white/20 px-2.5 py-1 rounded-full text-xs font-bold mb-4">
                                            <i data-lucide="tag" class="w-3 h-3 mr-1"></i>
                                            Rp {{ number_format($wisataCard->price_wni, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div @click="openDetail({{ $wisataCard->id }})"
                                        class="relative z-10 mt-auto flex justify-between items-center bg-white/20 p-1.5 pl-5 rounded-full backdrop-blur-md border border-white/20 cursor-pointer hover:bg-white/30 transition-colors">
                                        <span class="font-bold tracking-wide text-xs">{{ __('messages.btn_view_detail') }}</span>
                                        <div
                                            class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-earth-600 group-hover:scale-110 transition-transform">
                                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- White Cards (index 0 and 2) --}}
                            <div
                                class="bg-white rounded-2xl border border-forest-100 shadow-md hover:shadow-2xl hover:shadow-forest-900/10 p-0 flex flex-col transform hover:-translate-y-2 transition-all duration-500 group relative overflow-hidden">
                                {{-- Image --}}
                                <div class="relative w-full shrink-0 overflow-hidden rounded-t-2xl" style="height: 200px;">
                                    <img src="{{ $wisataCard->image ? (Str::startsWith($wisataCard->image, 'http') ? $wisataCard->image : asset($wisataCard->image)) : 'https://via.placeholder.com/400x300' }}"
                                        alt="{{ $wisataCard->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-white via-white/20 to-transparent"></div>
                                    @if($wisataCard->category)
                                        <span
                                            class="absolute top-3 left-3 bg-forest-600/80 backdrop-blur text-white text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full">{{ $wisataCard->category }}</span>
                                    @endif
                                </div>
                                <div class="p-6 pt-4 flex flex-col flex-1">
                                    <div
                                        class="absolute -right-12 -bottom-12 w-36 h-36 bg-forest-50 rounded-full group-hover:scale-150 transition-transform duration-700 ease-out z-0">
                                    </div>
                                    <div class="relative z-10 flex-1">
                                        <h3
                                            class="text-lg font-display font-bold text-forest-900 mb-1.5 group-hover:text-forest-700 transition-colors line-clamp-1">
                                            {{ $wisataCard->name }}</h3>
                                        <div class="flex items-center mb-2">
                                            <i data-lucide="star" class="w-3.5 h-3.5 text-gold-400 fill-current"></i>
                                            <span class="text-xs font-bold text-forest-800 ml-1.5">{{ number_format($wisataCard->ratings_avg_rating ?? 0, 1) }}</span>
                                            <span class="text-[11px] text-forest-500 ml-1">({{ $wisataCard->ratings_count }} {{ __('messages.reviews') }})</span>
                                        </div>
                                        <p class="text-forest-600 text-xs leading-relaxed mb-4 line-clamp-2">
                                            {{ $wisataCard->description }}</p>
                                        <div
                                            class="inline-flex items-center bg-forest-50 border border-forest-200 px-2.5 py-1 rounded-full text-xs font-bold text-forest-800 mb-4">
                                            <i data-lucide="tag" class="w-3 h-3 mr-1 text-earth-500"></i>
                                            Rp {{ number_format($wisataCard->price_wni, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="relative z-10 mt-auto">
                                        <button @click="openDetail({{ $wisataCard->id }})"
                                            class="inline-flex items-center text-forest-700 font-bold hover:text-earth-600 transition-colors group/link cursor-pointer text-sm">
                                            {{ __('messages.btn_view_detail') }} <i data-lucide="arrow-right"
                                                class="w-4 h-4 ml-2 group-hover/link:translate-x-1 transition-transform"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                </div>
            </div>
        </section>

        <!-- ============================================== -->
        <!-- DETAIL MODAL                                   -->
        <!-- ============================================== -->
        <div x-show="detailModal"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            style="display: none;" x-transition.opacity @keydown.escape.window="detailModal = false">
            <div @click.away="detailModal = false"
                class="bg-white rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto relative"
                x-transition.scale.duration.200ms>

                <!-- Loading State -->
                <div x-show="detailLoading" class="flex items-center justify-center p-20">
                    <div class="w-12 h-12 border-4 border-forest-200 border-t-forest-600 rounded-full animate-spin"></div>
                </div>

                <!-- Detail Content -->
                <template x-if="detailData && !detailLoading">
                    <div>
                        <!-- Image Header Carousel -->
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
                        }" class="relative w-full h-72 overflow-hidden rounded-t-3xl group">
                            
                            <!-- Images -->
                            <template x-for="(slide, index) in slides" :key="index">
                                <img x-show="activeSlide === index" 
                                     x-transition.opacity.duration.400ms
                                     :src="getImageUrl(slide)" 
                                     :alt="detailData.name" 
                                     class="absolute inset-0 w-full h-full object-cover cursor-pointer"
                                     @click="openLightbox(getImageUrl(slide))">
                            </template>

                            <div class="absolute inset-0 bg-gradient-to-t from-forest-950/90 via-transparent to-transparent pointer-events-none"></div>

                            <!-- Prev/Next Controls -->
                            <template x-if="slides.length > 1">
                                <div>
                                    <button @click.prevent.stop="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white rounded-full p-2 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none z-20">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                    </button>
                                    <button @click.prevent.stop="next()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white rounded-full p-2 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none z-20">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                    </button>
                                    
                                    <!-- Indicators -->
                                    <div class="absolute bottom-[90px] left-0 right-0 flex justify-center gap-1.5 z-20 pointer-events-none">
                                        <template x-for="(_, i) in slides" :key="'ind-'+i">
                                            <div class="h-1.5 rounded-full transition-all" :class="activeSlide === i ? 'w-5 bg-white' : 'w-2 bg-white/50'"></div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Close Button -->
                            <button @click="detailModal = false"
                                class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-forest-900 hover:bg-white p-2.5 rounded-full shadow-lg transition-all hover:scale-110 z-30">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>

                            <!-- Title Overlay -->
                            <div class="absolute bottom-6 left-6 right-6 z-20 pointer-events-none">
                                <span x-show="detailData.category" x-text="detailData.category"
                                    class="inline-block mb-3 px-3 py-1 bg-white/30 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-widest rounded-full border border-white/40"></span>
                                <h2 class="text-3xl md:text-4xl font-display font-bold text-white leading-tight drop-shadow-lg"
                                    x-text="detailData.name"></h2>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="p-6 md:p-8 space-y-6">
                            <!-- Price Badges -->
                            <div class="flex flex-wrap gap-3">
                                <div
                                    class="inline-flex items-center bg-gradient-to-r from-forest-50 to-white border border-forest-200 px-5 py-2.5 rounded-2xl shadow-sm">
                                    <i data-lucide="tag" class="w-5 h-5 text-earth-600 mr-2"></i>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-wider text-forest-500 font-semibold">{{ __('messages.ticket_wni') }}</p>
                                        <p class="text-lg font-bold text-forest-900">Rp <span
                                                x-text="formatPrice(detailData.price_wni)"></span></p>
                                    </div>
                                </div>
                                <div x-show="detailData.price_wna"
                                    class="inline-flex items-center bg-gradient-to-r from-earth-50 to-white border border-earth-200 px-5 py-2.5 rounded-2xl shadow-sm">
                                    <i data-lucide="globe" class="w-5 h-5 text-earth-600 mr-2"></i>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-wider text-earth-500 font-semibold">{{ __('messages.ticket_wna') }}</p>
                                        <p class="text-lg font-bold text-earth-900">Rp <span
                                                x-text="formatPrice(detailData.price_wna)"></span></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Rating Summary -->
                            <div
                                class="bg-forest-50 border border-forest-200 rounded-2xl p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <div class="flex items-center">
                                    <div class="text-4xl font-display font-bold text-forest-900 mr-3"
                                        x-text="detailData.ratings_avg_rating ? Number(detailData.ratings_avg_rating).toFixed(1) : '0.0'">
                                    </div>
                                    <div>
                                        <div class="flex space-x-0.5 mb-1">
                                            <template x-for="s in 5" :key="'modal-star-' + s">
                                                <svg :class="s <= Math.round(detailData.ratings_avg_rating || 0) ? 'fill-amber-400 text-amber-400' : 'text-gray-300'"
                                                    class="w-5 h-5" viewBox="0 0 24 24">
                                                    <polygon
                                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                                    </polygon>
                                                </svg>
                                            </template>
                                        </div>
                                        <p class="text-xs text-forest-500"><span
                                                x-text="detailData.ratings_count || 0"></span> {{ __('messages.visitor_reviews') }}</p>
                                    </div>
                                </div>
                                <div class="sm:ml-auto">
                                    <a href="{{ route('login') }}"
                                        class="inline-flex items-center justify-center bg-forest-600 hover:bg-forest-700 text-white font-medium text-sm px-4 py-2 rounded-xl transition-colors">
                                        <i data-lucide="log-in" class="w-4 h-4 mr-2"></i> {{ __('messages.btn_login_review') }}
                                    </a>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <h3 class="text-lg font-bold text-forest-900 mb-2 flex items-center">
                                    <i data-lucide="info" class="w-5 h-5 mr-2 text-forest-500"></i> {{ __('messages.desc') }}
                                </h3>
                                <p class="text-forest-600 leading-relaxed"
                                    x-text="detailData.description || '{{ __('messages.no_desc') }}'"></p>
                            </div>

                            <!-- Facilities -->
                            <div x-show="detailData.facilities_list">
                                <h3 class="text-lg font-bold text-forest-900 mb-2 flex items-center">
                                    <i data-lucide="check-circle-2" class="w-5 h-5 mr-2 text-earth-500"></i> {{ __('messages.facilities') }}
                                </h3>
                                <p class="text-forest-600 leading-relaxed" x-text="detailData.facilities_list"></p>
                            </div>

                            <!-- Map Link -->
                            <div x-show="detailData.map_url">
                                <h3 class="text-lg font-bold text-forest-900 mb-2 flex items-center">
                                    <i data-lucide="map-pin" class="w-5 h-5 mr-2 text-red-500"></i> {{ __('messages.location') }}
                                </h3>
                                <a :href="'https://www.google.com/maps?q=' + detailData.map_url" target="_blank"
                                    class="inline-flex items-center bg-blue-50 border border-blue-200 text-blue-700 px-4 py-2 rounded-xl hover:bg-blue-100 transition-colors font-medium text-sm">
                                    <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                                    {{ __('messages.open_map') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        
        <!-- Lightbox Modal -->
        <div x-show="lightboxModal" style="display: none;" class="fixed inset-0 z-[110] flex items-center justify-center p-4">
            <div x-show="lightboxModal" x-transition.opacity class="absolute inset-0 bg-black/90 backdrop-blur-sm" @click="lightboxModal = false"></div>
            <div x-show="lightboxModal" x-transition.scale.95 class="relative z-10 max-w-5xl w-full flex justify-center">
                <button @click="lightboxModal = false" class="absolute -top-12 right-0 md:-top-4 md:-right-12 bg-white/10 hover:bg-white/20 text-white rounded-full p-2 transition-colors focus:outline-none">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
                <img :src="lightboxImage" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl" alt="Preview Galeri">
            </div>
        </div>

    </div> <!-- End x-data div -->

    <script>
        function homeController() {
            return {
                detailModal: false,
                detailData: null,
                detailLoading: false,
                lightboxModal: false,
                lightboxImage: '',

                openLightbox(url) {
                    this.lightboxImage = url;
                    this.lightboxModal = true;
                },

                async openDetail(tourismId) {
                    this.detailLoading = true;
                    this.detailModal = true;

                    try {
                        const res = await fetch('{{ url("/tourism/detail") }}/' + tourismId);
                        const data = await res.json();
                        this.detailData = data.tourism;
                    } catch (e) {
                        console.error(e);
                    }
                    this.detailLoading = false;
                    this.$nextTick(() => {
                        if (window.lucide) {
                            lucide.createIcons();
                        }
                    });
                },

                formatPrice(val) {
                    if (!val) return '0';
                    return new Intl.NumberFormat('id-ID').format(val);
                },

                getImageUrl(image) {
                    if (!image) return 'https://via.placeholder.com/800x400?text=No+Image';
                    if (image.startsWith('http')) return image;
                    // Hapus slash di awal jika ada, lalu gabungkan dengan base path
                    const cleanPath = image.startsWith('/') ? image.substring(1) : image;
                    return '{{ url('/') }}/' + cleanPath;
                }
            };
        }
    </script>

@endsection