@extends('layouts.public')

@section('title', 'Berita & Edukasi Aksesibilitas - Sleman Akses')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-background pt-32 lg:pt-36 pb-12 border-b-2 border-border-solid">
    <div class="relative z-10 mx-auto px-5 lg:px-14 flex flex-col items-center w-full max-w-6xl text-center">
        <!-- Top Pill Badge -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-black text-primary bg-white border-2 border-border-solid mb-6 shadow-[2px_2px_0px_0px_var(--color-border-solid)]">
            <i class="fa-solid fa-newspaper text-primary"></i>
            <span>Wawasan &amp; Edukasi Inklusif</span>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-center gap-6 mb-6">
            <h1 class="font-black text-primary leading-tight max-w-3xl" style="font-size: clamp(32px, 4.5vw, 56px); letter-spacing: -0.02em;">
                Kabar &amp; Edukasi Aksesibilitas <br>
                <span class="text-text-onsurface">Kabupaten Sleman</span>
            </h1>
            <img src="{{ asset('maskot-genit.svg') }}" alt="Maskot Sleman Akses" class="w-16 h-16 sm:w-20 sm:h-20 object-contain drop-shadow-sm hidden sm:block flex-shrink-0">
        </div>

        <p class="text-text-muted text-base sm:text-lg max-w-2xl leading-relaxed mb-8">
            Informasi terkini mengenai ketersediaan fasilitas ramah disabilitas, kebijakan inklusi sosial, serta panduan hak aksesibilitas publik di Kabupaten Sleman.
        </p>

        <!-- Search Bar -->
        <div class="w-full max-w-2xl">
            <form action="{{ route('public.articles.index') }}" method="GET" class="relative flex items-center">
                @if(request('category') && request('category') !== 'Semua')
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-text-muted text-base"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari topik edukasi, trotoar, ramp, atau nama tempat..." 
                           class="w-full pl-12 pr-28 py-3.5 bg-white border-2 border-border-solid rounded-full text-sm font-bold text-text-onsurface shadow-[3px_3px_0px_0px_var(--color-border-solid)] focus:outline-none focus:border-primary transition-all">
                    
                    @if($search)
                        <a href="{{ route('public.articles.index', ['category' => request('category')]) }}" 
                           class="absolute right-24 top-1/2 -translate-y-1/2 text-text-muted hover:text-rose-600 text-xs font-black pr-2">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" 
                        class="absolute right-1.5 top-1/2 -translate-y-1/2 px-5 py-2.5 bg-primary text-white rounded-full font-black text-xs border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:bg-primary-hover hover:translate-x-0.5 hover:translate-y-0.5 transition-all">
                    Cari
                </button>
            </form>
        </div>

        <!-- Category Filters -->
        <div class="flex flex-wrap items-center justify-center gap-2 mt-8">
            @foreach($categories as $cat)
                <a href="{{ route('public.articles.index', array_merge(request()->except('page'), ['category' => $cat])) }}"
                   class="px-4 py-2 rounded-full text-xs font-black border-2 border-border-solid transition-all
                   {{ $activeCategory === $cat 
                      ? 'bg-primary text-white shadow-[2px_2px_0px_0px_var(--color-border-solid)] translate-x-0.5 translate-y-0.5' 
                      : 'bg-white text-text-primary hover:bg-slate-100 shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:-translate-y-0.5' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Content Catalog Section -->
<section class="py-14 bg-surface min-h-[500px]">
    <div class="container px-4 mx-auto max-w-6xl">

        {{-- Featured Article (Only on page 1 with all categories & no search) --}}
        @if($featuredArticle)
            <div class="mb-14">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-3 h-3 rounded-full bg-secondary border border-border-solid inline-block"></span>
                    <span class="text-xs font-black uppercase tracking-wider text-text-muted">Sorotan Edukasi Pilihan</span>
                </div>

                <div class="card-solid p-6 md:p-8 bg-white grid grid-cols-1 lg:grid-cols-12 gap-8 items-center hover:shadow-[6px_6px_0px_0px_var(--color-border-solid)] transition-all">
                    <div class="lg:col-span-6 overflow-hidden rounded-2xl border-2 border-border-solid shadow-[3px_3px_0px_0px_var(--color-border-solid)] bg-slate-100 aspect-video lg:aspect-4/3 relative">
                        <img src="{{ $featuredArticle->image_url }}" 
                             alt="{{ $featuredArticle->title }}" 
                             class="w-full h-full object-cover">
                        <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm px-3 py-1 rounded-full border-2 border-border-solid text-[10px] font-black text-primary shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                            {{ $featuredArticle->category }}
                        </div>
                    </div>

                    <div class="lg:col-span-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3 text-xs font-bold text-text-muted mb-3 flex-wrap">
                                <span><i class="fa-regular fa-calendar-check mr-1.5 text-primary"></i> {{ $featuredArticle->published_at ? $featuredArticle->published_at->format('d M Y') : $featuredArticle->created_at->format('d M Y') }}</span>
                                <span>•</span>
                                <span><i class="fa-regular fa-clock mr-1 text-primary"></i> {{ $featuredArticle->reading_time_minutes }} menit baca</span>
                                <span>•</span>
                                <span><i class="fa-regular fa-eye mr-1 text-primary"></i> {{ number_format($featuredArticle->views_count, 0, ',', '.') }} dibaca</span>
                            </div>

                            <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-text-primary leading-tight mb-3">
                                <a href="{{ route('public.articles.show', $featuredArticle->slug) }}" class="hover:text-primary transition-colors">
                                    {{ $featuredArticle->title }}
                                </a>
                            </h2>

                            <p class="text-text-muted text-sm font-medium leading-relaxed line-clamp-3 mb-4">
                                {{ $featuredArticle->summary }}
                            </p>

                            {{-- Detected Facilities Badges --}}
                            @if(!empty($featuredArticle->detected_facilities))
                                <div class="flex flex-wrap gap-1.5 mb-6">
                                    @foreach($featuredArticle->detected_facilities as $fac)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-black px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-900 border border-emerald-300">
                                            @if($fac === 'Ramp') ♿ Ramp
                                            @elseif($fac === 'Toilet Disabilitas') 🚻 Toilet
                                            @elseif($fac === 'Parkir Khusus') 🅿️ Parkir
                                            @elseif($fac === 'Lift') 🛗 Lift
                                            @elseif($fac === 'Guiding Block') 🦯 Guiding Block
                                            @else 🏷️ {{ $fac }}
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div>
                            <a href="{{ route('public.articles.show', $featuredArticle->slug) }}" 
                               class="btn-primary-solid inline-flex items-center gap-2 text-sm">
                                <span>Baca Selengkapnya</span>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Active Filter / Search Indicator --}}
        @if($search || ($activeCategory && $activeCategory !== 'Semua'))
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3 pb-3 border-b-2 border-border-solid">
                <div class="text-sm font-black text-text-primary flex items-center gap-2">
                    <i class="fa-solid fa-filter text-primary"></i>
                    <span>Menampilkan hasil untuk:</span>
                    @if($activeCategory && $activeCategory !== 'Semua')
                        <span class="px-2.5 py-0.5 bg-primary/10 text-primary border border-primary rounded-full text-xs font-black">{{ $activeCategory }}</span>
                    @endif
                    @if($search)
                        <span class="px-2.5 py-0.5 bg-amber-100 text-amber-900 border border-amber-300 rounded-full text-xs font-black">"{{ $search }}"</span>
                    @endif
                </div>

                <a href="{{ route('public.articles.index') }}" class="text-xs font-black text-text-muted hover:text-rose-600 transition-colors flex items-center gap-1">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset Semua Filter</span>
                </a>
            </div>
        @endif

        {{-- Articles Grid --}}
        @if($articles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($articles as $article)
                    <article class="bg-white border-2 border-border-solid rounded-2xl shadow-[4px_4px_0px_0px_var(--color-border-solid)] hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_var(--color-border-solid)] transition-all flex flex-col justify-between overflow-hidden">
                        <div>
                            {{-- Cover Thumbnail --}}
                            <a href="{{ route('public.articles.show', $article->slug) }}" class="block relative aspect-video overflow-hidden bg-slate-100 border-b-2 border-border-solid group">
                                <img src="{{ $article->image_url }}" 
                                     alt="{{ $article->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                
                                <span class="absolute top-3 left-3 bg-white/95 px-2.5 py-1 rounded-full border border-border-solid text-[10px] font-black text-primary shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                                    {{ $article->category }}
                                </span>
                            </a>

                            {{-- Card Body --}}
                            <div class="p-5">
                                <div class="flex items-center gap-2 text-[11px] font-bold text-text-muted mb-2 flex-wrap">
                                    <span>{{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}</span>
                                    <span>•</span>
                                    <span>{{ $article->reading_time_minutes }} mnt baca</span>
                                    <span>•</span>
                                    <span><i class="fa-regular fa-eye mr-0.5 text-primary"></i> {{ number_format($article->views_count, 0, ',', '.') }}</span>
                                </div>

                                <h3 class="text-base font-black text-text-primary leading-snug line-clamp-2 mb-2">
                                    <a href="{{ route('public.articles.show', $article->slug) }}" class="hover:text-primary transition-colors">
                                        {{ $article->title }}
                                    </a>
                                </h3>

                                <p class="text-xs text-text-muted font-medium line-clamp-3 leading-relaxed mb-3">
                                    {{ $article->summary }}
                                </p>

                                {{-- Detected Facilities Badges --}}
                                @if(!empty($article->detected_facilities))
                                    <div class="flex flex-wrap gap-1 mb-2">
                                        @foreach(array_slice($article->detected_facilities, 0, 3) as $fac)
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-300">
                                                @if($fac === 'Ramp') ♿ Ramp
                                                @elseif($fac === 'Toilet Disabilitas') 🚻 Toilet
                                                @elseif($fac === 'Parkir Khusus') 🅿️ Parkir
                                                @elseif($fac === 'Lift') 🛗 Lift
                                                @elseif($fac === 'Guiding Block') 🦯 Guiding Block
                                                @else {{ $fac }}
                                                @endif
                                            </span>
                                        @endforeach
                                        @if(count($article->detected_facilities) > 3)
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 text-text-muted">+{{ count($article->detected_facilities) - 3 }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Card Footer --}}
                        <div class="px-5 pb-5 pt-3 border-t-2 border-slate-100 flex items-center justify-between text-xs">
                            <span class="font-bold text-text-muted truncate max-w-[150px]">
                                <i class="fa-solid fa-user-pen mr-1 text-primary"></i>
                                {{ $article->author_name ?: $article->source_name ?: 'Sleman Akses' }}
                            </span>

                            <a href="{{ route('public.articles.show', $article->slug) }}" 
                               class="font-black text-primary hover:text-primary-hover flex items-center gap-1 group">
                                <span>Baca</span>
                                <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination Links --}}
            <div class="mt-12 flex justify-center">
                {{ $articles->appends(request()->query())->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="p-12 bg-white border-2 border-border-solid rounded-3xl shadow-[4px_4px_0px_0px_var(--color-border-solid)] text-center max-w-xl mx-auto my-8">
                <div class="w-20 h-20 mx-auto mb-4 p-2 rounded-2xl bg-amber-50 border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] flex items-center justify-center">
                    <img src="{{ asset('maskot-side-eye.svg') }}" alt="Pencarian Kosong" class="w-14 h-14 object-contain">
                </div>
                <h3 class="text-xl font-black text-text-primary mb-2">Belum Ada Berita yang Cocok</h3>
                <p class="text-xs text-text-muted font-medium leading-relaxed mb-6">
                    Tidak ditemukan berita atau artikel edukasi dengan kata kunci atau filter yang Anda pilih. Silakan gunakan kata kunci lain atau reset filter.
                </p>
                <a href="{{ route('public.articles.index') }}" class="btn-primary-solid inline-flex items-center gap-2 text-xs">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Tampilkan Semua Berita</span>
                </a>
            </div>
        @endif

    </div>
</section>
@endsection
