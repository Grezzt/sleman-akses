@extends('layouts.public')

@section('title', 'Sleman Akses - Sistem Pemetaan Aksesibilitas')

@section('content')
    <!-- 1. HERO SECTION -->
    <section class="relative overflow-hidden pt-32 lg:pt-40 min-h-[85vh] flex flex-col justify-between" style="padding-bottom: 3rem; background-image: url('{{ asset('landing-hijau.svg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <!-- Content -->
        <div class="relative z-10 mx-auto px-5 lg:px-14 flex flex-col lg:flex-row items-stretch w-full max-w-7xl flex-grow gap-0 pb-24 lg:pb-32">
            <!-- Left: Bordered text box -->
            <div class="flex-1 flex flex-col justify-between p-8 lg:p-12 border bg-surface shadow-solid">
                <div class="mb-8">
                    <div class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold text-primary bg-secondary mb-6 shadow-solid">
                        <i class="fas fa-wheelchair mr-2"></i> Sleman Inklusif
                    </div>
                    <h1 class="font-bold leading-tight text-surface" style="font-size: clamp(32px, 5vw, 64px); letter-spacing: -0.02em;">
                        Peta Aksesibilitas <br>
                        <span class="text-secondary">Fasilitas Publik</span>
                    </h1>
                </div>

                <div class="flex flex-col gap-8">
                    <p class="text-text-onsurface" style="font-size: clamp(16px, 1.6vw, 19px); line-height: 1.6; max-width: 500px;">
                        Temukan dan laporkan fasilitas publik yang ramah disabilitas fisik di Kabupaten Sleman. Bersama kita wujudkan lingkungan yang aksesibel bagi semua.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="{{ url('/map') }}" class="btn-primary-solid bg-secondary text-text-onsurface border-text-onsurface shadow-[4px_4px_0px_0px_#0F0F0F] hover:shadow-[2px_2px_0px_0px_#0F0F0F]">
                            Jelajahi Peta
                        </a>
                        <a href="#cara-kerja" class="btn-primary-solid bg-primary text-surface border-text-onsurface shadow-[4px_4px_0px_0px_#0F0F0F] hover:shadow-[2px_2px_0px_0px_#0F0F0F]">
                            Cara Kerja
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right: Hero visual -->
            <div class="flex-1 min-h-[400px] flex items-end justify-center relative overflow-hidden">
                <img src="{{ asset('onboarding slide 3.svg') }}" alt="Peta Interaktif" class="w-[100%] h-[100%] object-center object-top">
            </div>
        </div>

        <!-- STAT COUNTERS (Cards) -->
        <div class="relative z-20 mx-auto px-5 lg:px-14 -mt-20 lg:-mt-24 w-full max-w-7xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card-solid p-6 flex items-center gap-6 group hover:-translate-y-1 hover:shadow-solid-secondary transition-all duration-300">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl bg-primary text-surface shadow-solid-secondary group-hover:scale-110 transition-transform">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div>
                        <h3 class="text-4xl font-black text-text-onsurface mb-1"><span class="stat-counter-value" data-target="{{ $stats['locations'] ?? 0 }}">0</span>+</h3>
                        <p class="font-bold text-text-muted">Fasilitas Terpetakan</p>
                    </div>
                </div>

                <div class="card-solid p-6 flex items-center gap-6 group hover:-translate-y-1 hover:shadow-solid-secondary transition-all duration-300">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl bg-primary text-surface shadow-solid-secondary group-hover:scale-110 transition-transform border-2 border-primary">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div>
                        <h3 class="text-4xl font-black text-text-onsurface mb-1"><span class="stat-counter-value" data-target="{{ $stats['categories'] ?? 0 }}">0</span>+</h3>
                        <p class="font-bold text-text-muted">Kategori Tersedia</p>
                    </div>
                </div>

                <div class="card-solid p-6 flex items-center gap-6 group hover:-translate-y-1 hover:shadow-solid-secondary transition-all duration-300">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl bg-primary text-surface shadow-solid-secondary group-hover:scale-110 transition-transform">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h3 class="text-4xl font-black text-text-onsurface mb-1"><span class="stat-counter-value" data-target="{{ $stats['reports'] ?? 0 }}">0</span>+</h3>
                        <p class="font-bold text-text-muted">Partisipasi Laporan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- 2. CARA KERJA (Features Style) -->
    <section id="cara-kerja" class="py-24 bg-background border-b-2 border-border-solid" >
        <div class="container px-4 mx-auto max-w-7xl">
            <p class="font-bold text-primary mb-4 uppercase tracking-wider text-sm">Pemetaan Partisipatif</p>
            <div class="mb-14 max-w-3xl">
                <h2 class="font-black text-text-onsurface mb-6" style="font-size: clamp(28px, 4vw, 48px); line-height: 1.1; letter-spacing: -0.02em;">
                    Temukan. Lapor. Publikasi.
                </h2>
                <p class="text-text-muted text-lg leading-relaxed">
                    Sleman Akses mengandalkan partisipasi masyarakat. Sistem tiga langkah kami memastikan setiap fasilitas terdata dan tervalidasi dengan baik.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-6 lg:gap-10 pb-16 md:pb-24">
                <!-- Step 1 -->
                <div class="card-solid bg-surface p-6 md:p-8 relative group flex flex-col h-full">
                    <!-- Number Badge -->
                    <div class="absolute -top-5 -left-5 w-12 h-12 bg-primary text-surface rounded-full flex items-center justify-center font-black text-xl border-2 border-border-solid shadow-[4px_4px_0px_0px_#0F0F0F] z-10 group-hover:scale-110 group-hover:rotate-6 transition-all">
                        1
                    </div>
                    <!-- Image Box -->
                    <div class="mb-6 border-2 border-border-solid rounded-xl bg-surface overflow-hidden flex items-center justify-center p-4 h-48 md:h-56 group-hover:bg-secondary/30 transition-colors">
                        <img src="{{ asset('onboarding slide 2.svg') }}" alt="Temukan" class="w-full h-full object-contain">
                    </div>
                    <!-- Text Content -->
                    <div class="flex-grow">
                        <h3 class="font-black text-text-onsurface text-2xl mb-3">Temukan</h3>
                        <p class="text-text-muted text-base leading-relaxed">
                            Temukan fasilitas publik yang belum terpetakan aksesibilitasnya atau periksa lokasi yang sudah ada di sekitar Anda.
                        </p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="card-solid bg-surface p-6 md:p-8 relative group flex flex-col h-full">
                    <!-- Number Badge -->
                    <div class="absolute -top-5 -left-5 w-12 h-12 bg-secondary text-text-onsurface rounded-full flex items-center justify-center font-black text-xl border-2 border-border-solid shadow-[4px_4px_0px_0px_#0F0F0F] z-10 group-hover:scale-110 group-hover:-rotate-6 transition-all">
                        2
                    </div>
                    <!-- Image Box -->
                    <div class="mb-6 border-2 border-border-solid rounded-xl bg-surface overflow-hidden flex items-center justify-center p-4 h-48 md:h-56 group-hover:bg-primary/20 transition-colors">
                        <img src="{{ asset('konfirmasi-titik-lokasi-unactive.svg') }}" alt="Lapor" class="w-full h-full object-contain">
                    </div>
                    <!-- Text Content -->
                    <div class="flex-grow">
                        <h3 class="font-black text-text-onsurface text-2xl mb-3">Lapor</h3>
                        <p class="text-text-muted text-base leading-relaxed">
                            Buka aplikasi, ambil foto fasilitas pendukung (seperti ramp atau toilet difabel), dan kirimkan laporan beserta detailnya.
                        </p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="card-solid bg-surface p-6 md:p-8 relative group flex flex-col h-full">
                    <!-- Number Badge -->
                    <div class="absolute -top-5 -left-5 w-12 h-12 bg-primary text-surface rounded-full flex items-center justify-center font-black text-xl border-2 border-border-solid shadow-[4px_4px_0px_0px_#0F0F0F] z-10 group-hover:scale-110 group-hover:rotate-6 transition-all">
                        3
                    </div>
                    <!-- Image Box -->
                    <div class="mb-6 border-2 border-border-solid rounded-xl bg-surface overflow-hidden flex items-center justify-center p-4 h-48 md:h-56 group-hover:bg-secondary/30 transition-colors">
                        <img src="{{ asset('konfirmasi titik lokasi aktif.svg') }}" alt="Publikasi" class="w-full h-full object-contain">
                    </div>
                    <!-- Text Content -->
                    <div class="flex-grow">
                        <h3 class="font-black text-text-onsurface text-2xl mb-3">Publikasi</h3>
                        <p class="text-text-muted text-base leading-relaxed">
                            Admin akan memvalidasi laporan Anda. Jika disetujui, lokasi akan langsung muncul di Peta Publik untuk membantu orang lain.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>



     <!-- BANNER PENGHUBUNG -->
    <section class="relative bg-primary overflow-hidden py-12 lg:py-16 border-b-2 border-border-solid" style="background-image: url('{{ asset('landing-hijau.svg') }}'); background-size: cover; background-position: center;">
        <div class="container px-4 mx-auto max-w-7xl relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-surface max-w-xl">
                    <h2 class="text-3xl md:text-4xl font-black mb-3 " style="color: var(--color-secondary);">Aplikasi Sleman Akses</h2>
                    <p class="text-lg opacity-90 leading-relaxed">
                        Cara paling mudah untuk berkontribusi. Laporkan fasilitas publik yang belum terpetakan secara real-time langsung dari smartphone Anda.
                    </p>
                </div>
                <!-- Pop Up Asset (Replacing Phone/QR) -->
                {{-- <a href="#" class="card-solid bg-surface p-5 md:p-6 flex items-center gap-6 transform md:rotate-2 hover:rotate-0 transition-transform duration-300 group">
                    <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl flex items-center justify-center text-2xl md:text-3xl bg-secondary text-text-onsurface border-2 border-border-solid shadow-[4px_4px_0px_0px_#0F0F0F] group-hover:scale-110 transition-transform">
                        <i class="fab fa-google-play"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-text-onsurface text-lg md:text-xl mb-1">Unduh Sekarang</h3>
                        <p class="text-text-muted text-sm font-bold">Tersedia di Google Play</p>
                    </div>
                </a> --}}
            </div>
        </div>
    </section>

    <!-- 3. CATEGORY GRID -->
    <section class="py-24 bg-background border-b-2 border-border-solid" style="background-image: url('{{ asset('landing-putih.svg') }}'); background-size: cover; background-position: center;">
        <div class="container px-4 mx-auto max-w-7xl relative z-10">
            <div class="mb-14 max-w-3xl">
                <h2 class="font-black text-text-onsurface mb-6" style="font-size: clamp(28px, 4vw, 40px); line-height: 1.1; letter-spacing: -0.02em;">
                    Kategori Aksesibilitas
                </h2>
                <p class="text-text-muted text-lg">
                    Informasi ketersediaan berbagai fasilitas pendukung untuk disabilitas fisik yang kami petakan.
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($categories as $category)
                    @php
                        $iconPath = 'parkir-difabel.svg'; // fallback
                        $name = strtolower($category->facility_name ?? '');

                        if (Str::contains($name, 'parkir')) {
                            $iconPath = 'parkir-difabel.svg';
                        } elseif (Str::contains($name, 'toilet') || Str::contains($name, 'kamar mandi')) {
                            $iconPath = 'toilet-difabel.svg';
                        } elseif (Str::contains($name, 'ramp') || Str::contains($name, 'landai') || Str::contains($name, 'kursi roda')) {
                            $iconPath = 'ramp.svg';
                        } elseif (Str::contains($name, 'lift') || Str::contains($name, 'elevator')) {
                            $iconPath = 'lift.svg';
                        } elseif (!empty($category->icon_marker) && Str::endsWith($category->icon_marker, '.svg')) {
                            $iconPath = $category->icon_marker;
                        }
                    @endphp
                    <div class="card-solid bg-surface p-6 flex flex-col justify-center items-center hover:-translate-y-1 transition duration-300 group">
                        <div class="w-16 h-16 bg-background rounded-xl border-2 border-border-solid flex items-center justify-center mb-4 group-hover:bg-secondary group-hover:shadow-[2px_2px_0px_0px_#0F0F0F] transition-all overflow-hidden p-3 relative z-10">
                            <img src="{{ asset($iconPath) }}" alt="{{ $category->facility_name }}" class="w-full h-full object-contain">
                        </div>
                        <h3 class="font-bold text-text-onsurface text-center text-sm md:text-base">{{ $category->facility_name }}</h3>
                    </div>
                @empty
                    <div class="card-solid bg-surface p-6 text-center"><h3 class="font-bold">Belum ada kategori</h3></div>
                @endforelse
            </div>

            <div class="mt-12">
                <a href="{{ url('/map') }}" class="font-bold text-primary border-b-2 border-primary pb-1 hover:text-primary-hover transition-colors">
                    Lihat Semua Fasilitas di Peta →
                </a>
            </div>
        </div>
    </section>

    <!-- NEWS / EDUKASI SECTION -->
    @if(isset($latestArticles) && $latestArticles->count() > 0)
    <section class="py-24 bg-background border-b-2 border-border-solid">
        <div class="container px-4 mx-auto max-w-7xl">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                <div>
                    <div class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black text-primary bg-white mb-4 border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)]">
                        <i class="fa-solid fa-newspaper mr-2"></i> Wawasan & Edukasi
                    </div>
                    <h2 class="font-black text-text-onsurface leading-tight" style="font-size: clamp(28px, 4vw, 42px); letter-spacing: -0.02em;">
                        Kabar Aksesibilitas Terkini
                    </h2>
                    <p class="text-text-muted text-base mt-2 max-w-xl">
                        Informasi terverifikasi mengenai fasilitas difabel, kebijakan inklusi, dan pembaruan fasilitas publik di Kabupaten Sleman.
                    </p>
                </div>
                <div>
                    <a href="{{ route('public.articles.index') }}" class="btn-primary-solid inline-flex items-center gap-2 text-sm">
                        <span>Lihat Semua Berita</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($latestArticles as $article)
                    <article class="bg-white border-2 border-border-solid rounded-2xl shadow-[4px_4px_0px_0px_var(--color-border-solid)] hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_var(--color-border-solid)] transition-all flex flex-col justify-between overflow-hidden">
                        <div>
                            <a href="{{ route('public.articles.show', $article->slug) }}" class="block relative aspect-video overflow-hidden bg-slate-100 border-b-2 border-border-solid group">
                                <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <span class="absolute top-3 left-3 bg-white/95 px-2.5 py-1 rounded-full border border-border-solid text-[10px] font-black text-primary shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                                    {{ $article->category }}
                                </span>
                            </a>
                            <div class="p-5">
                                <div class="flex items-center gap-2 text-[11px] font-bold text-text-muted mb-2">
                                    <span>{{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}</span>
                                    <span>•</span>
                                    <span>{{ $article->reading_time_minutes }} mnt baca</span>
                                </div>
                                <h3 class="text-base font-black text-text-primary leading-snug line-clamp-2 mb-2">
                                    <a href="{{ route('public.articles.show', $article->slug) }}" class="hover:text-primary transition-colors">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-text-muted font-medium line-clamp-2 leading-relaxed">
                                    {{ $article->summary }}
                                </p>
                            </div>
                        </div>
                        <div class="px-5 pb-5 pt-2 flex items-center justify-between text-xs">
                            <span class="text-[11px] font-bold text-text-muted truncate max-w-[150px]">
                                {{ $article->author_name ?: $article->source_name ?: 'Sleman Akses' }}
                            </span>
                            <a href="{{ route('public.articles.show', $article->slug) }}" class="font-black text-primary hover:text-primary-hover flex items-center gap-1 group">
                                <span>Baca</span>
                                <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- FAQ SECTION -->
    <section class="py-24 bg-secondary border-b-2 border-border-solid">
        <div class="container px-4 mx-auto max-w-4xl">
            <div class="text-center mb-14">
                <div class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold text-secondary bg-primary mb-6 border-2 border-border-solid shadow-[4px_4px_0px_0px_#0F0F0F]">
                    <i class="fas fa-question-circle mr-2"></i> FAQ
                </div>
                <h2 class="font-black text-text-onsurface mb-4" style="font-size: clamp(28px, 4vw, 40px); line-height: 1.1; letter-spacing: -0.02em;">
                    Pertanyaan yang Sering Diajukan
                </h2>
                <p class="text-text-muted text-lg">Jawaban cepat untuk hal-hal yang mungkin ingin Anda ketahui tentang Sleman Akses.</p>
            </div>

            <div class="space-y-4">
                <!-- FAQ 1 -->
                <details class="group card-solid bg-background border-2 border-border-solid rounded-xl shadow-solid [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex justify-between items-center font-bold text-lg cursor-pointer p-6 list-none hover:bg-secondary/10 transition-colors">
                        <span class="text-text-onsurface">Apa itu Sleman Akses?</span>
                        <span class="transition group-open:rotate-180">
                            <i class="fas fa-chevron-down text-primary"></i>
                        </span>
                    </summary>
                    <div class="p-6 pt-0 text-text-muted text-base leading-relaxed">
                        Sleman Akses adalah platform pemetaan digital partisipatif yang memungkinkan masyarakat untuk menemukan dan melaporkan ketersediaan fasilitas ramah disabilitas di Kabupaten Sleman.
                    </div>
                </details>

                <!-- FAQ 2 -->
                <details class="group card-solid bg-background border-2 border-border-solid rounded-xl shadow-solid [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex justify-between items-center font-bold text-lg cursor-pointer p-6 list-none hover:bg-secondary/10 transition-colors">
                        <span class="text-text-onsurface">Siapa saja yang bisa memberikan laporan fasilitas?</span>
                        <span class="transition group-open:rotate-180">
                            <i class="fas fa-chevron-down text-primary"></i>
                        </span>
                    </summary>
                    <div class="p-6 pt-0 text-text-muted text-base leading-relaxed">
                        Semua warga masyarakat dapat berpartisipasi dengan mengunduh aplikasi Sleman Akses. Anda dapat memotret fasilitas pendukung, memberikan detail lokasi, dan mengirimkannya agar dapat divalidasi oleh admin.
                    </div>
                </details>

                <!-- FAQ 3 -->
                <details class="group card-solid bg-background border-2 border-border-solid rounded-xl shadow-solid [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex justify-between items-center font-bold text-lg cursor-pointer p-6 list-none hover:bg-secondary/10 transition-colors">
                        <span class="text-text-onsurface">Apakah laporan saya langsung muncul di peta?</span>
                        <span class="transition group-open:rotate-180">
                            <i class="fas fa-chevron-down text-primary"></i>
                        </span>
                    </summary>
                    <div class="p-6 pt-0 text-text-muted text-base leading-relaxed">
                        Tidak. Setiap laporan yang masuk akan melalui tahap verifikasi oleh tim admin untuk memastikan keakuratan informasi dan keaslian foto. Jika disetujui, laporan baru akan muncul di Peta Aksesibilitas publik.
                    </div>
                </details>

                <!-- FAQ 4 -->
                <details class="group card-solid bg-background border-2 border-border-solid rounded-xl shadow-solid [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex justify-between items-center font-bold text-lg cursor-pointer p-6 list-none hover:bg-secondary/10 transition-colors">
                        <span class="text-text-onsurface">Apakah aplikasi ini berbayar?</span>
                        <span class="transition group-open:rotate-180">
                            <i class="fas fa-chevron-down text-primary"></i>
                        </span>
                    </summary>
                    <div class="p-6 pt-0 text-text-muted text-base leading-relaxed">
                        Sleman Akses adalah layanan publik yang sepenuhnya gratis untuk digunakan, baik untuk keperluan mencari informasi fasilitas melalui website maupun untuk melaporkan fasilitas melalui aplikasi mobile.
                    </div>
                </details>
            </div>
        </div>
    </section>

    {{-- <!-- 4. DOWNLOAD CTA (Preserved Section) -->
    <section class="py-24 bg-secondary overflow-hidden">
        <div class="container px-4 mx-auto max-w-7xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
                <div class="order-2 md:order-1 relative flex justify-center">
                    <!-- Mobile Mockup -->
                    <div class="card-solid bg-surface p-4 max-w-xs w-full rotate-[-4deg] hover:rotate-0 transition-transform duration-500">
                        <div class="bg-gray-100 rounded-2xl overflow-hidden aspect-[9/19] relative border-4 border-text-onsurface">
                            <img src="{{ asset('images/mobile-mockup.jpg') }}" alt="Aplikasi Sleman Akses" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>

                <div class="order-1 md:order-2">
                    <p class="font-bold text-primary mb-4 uppercase tracking-wider text-sm">Aplikasi Mobile</p>
                    <h2 class="font-black text-primary mb-6 leading-tight" style="font-size: clamp(32px, 4vw, 56px); letter-spacing: -0.02em;">
                        Kontribusi Lebih Cepat & Mudah
                    </h2>
                    <p class="text-primary/80 text-lg mb-8 font-medium max-w-lg">
                        Unduh aplikasi Sleman Akses di smartphone Anda. Foto fasilitas, tentukan lokasi otomatis menggunakan GPS, dan kirim laporan dalam hitungan detik.
                    </p>

                    <a href="#" class="inline-flex items-center gap-4 bg-primary text-surface px-8 py-4 rounded-xl border-2 border-primary shadow-[4px_4px_0px_0px_#022c22] hover:translate-y-[2px] hover:translate-x-[2px] hover:shadow-[2px_2px_0px_0px_#022c22] transition-all">
                        <i class="fab fa-google-play text-3xl text-secondary"></i>
                        <div class="text-left">
                            <div class="text-xs font-bold uppercase tracking-wider text-surface/70">Get it on</div>
                            <div class="text-xl font-black leading-none">Google Play</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section> --}}
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const counters = document.querySelectorAll('.stat-counter-value');
        const speed = 200;

        const animateCounters = () => {
            counters.forEach(counter => {
                const updateCount = () => {
                    const target = +counter.getAttribute('data-target');
                    const count = +counter.innerText;
                    const inc = target / speed;

                    if (count < target) {
                        counter.innerText = Math.ceil(count + inc);
                        setTimeout(updateCount, 10);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
            });
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => observer.observe(counter));
    });
</script>
@endpush
