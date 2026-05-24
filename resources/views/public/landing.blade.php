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
    <section id="cara-kerja" class="py-24 bg-background border-b-2 border-border-solid">
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-0 border-y-2 border-l-2 border-border-solid bg-surface shadow-solid">
                <!-- Step 1 -->
                <div class="p-8 border-r-2 border-border-solid relative group hover:bg-secondary/10 transition-colors">
                    <div class="mb-6 text-primary">
                        <i class="fas fa-search-location text-4xl"></i>
                    </div>
                    <h2 class="font-bold text-text-onsurface text-2xl mb-2">1. Temukan</h2>
                    <p class="text-text-muted text-sm leading-relaxed mb-6">
                        Temukan fasilitas publik yang belum terpetakan aksesibilitasnya atau periksa lokasi yang sudah ada di sekitar Anda.
                    </p>
                </div>
                <!-- Step 2 -->
                <div class="p-8 border-r-2 border-border-solid relative group hover:bg-secondary/10 transition-colors">
                    <div class="mb-6 text-primary">
                        <i class="fas fa-camera text-4xl"></i>
                    </div>
                    <h2 class="font-bold text-text-onsurface text-2xl mb-2">2. Lapor</h2>
                    <p class="text-text-muted text-sm leading-relaxed mb-6">
                        Buka aplikasi, ambil foto fasilitas pendukung (seperti ramp atau toilet difabel), dan kirimkan laporan beserta detailnya.
                    </p>
                </div>
                <!-- Step 3 -->
                <div class="p-8 border-r-2 border-border-solid relative group hover:bg-secondary/10 transition-colors">
                    <div class="mb-6 text-primary">
                        <i class="fas fa-check-double text-4xl"></i>
                    </div>
                    <h2 class="font-bold text-text-onsurface text-2xl mb-2">3. Publikasi</h2>
                    <p class="text-text-muted text-sm leading-relaxed mb-6">
                        Admin akan memvalidasi laporan Anda. Jika disetujui, lokasi akan langsung muncul di Peta Publik untuk membantu orang lain.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. CATEGORY GRID -->
    <section class="py-24 bg-surface border-b-2 border-border-solid">
        <div class="container px-4 mx-auto max-w-7xl">
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
                    <div class="card-solid p-6 flex flex-col justify-center items-center hover:-translate-y-1 transition duration-300 group">
                        <div class="w-16 h-16 bg-background rounded-xl border-2 border-border-solid flex items-center justify-center mb-4 group-hover:bg-secondary group-hover:shadow-[2px_2px_0px_0px_#0F0F0F] transition-all">
                            <i class="{{ $category->icon_marker ?? 'fas fa-map-marker-alt' }} text-2xl text-text-onsurface"></i>
                        </div>
                        <h3 class="font-bold text-text-onsurface text-center text-sm md:text-base">{{ $category->facility_name }}</h3>
                    </div>
                @empty
                    <div class="card-solid p-6 text-center"><h3 class="font-bold">Belum ada kategori</h3></div>
                @endforelse
            </div>

            <div class="mt-12">
                <a href="{{ url('/map') }}" class="font-bold text-primary border-b-2 border-primary pb-1 hover:text-primary-hover transition-colors">
                    Lihat Semua Fasilitas di Peta →
                </a>
            </div>
        </div>
    </section>

    <!-- 4. DOWNLOAD CTA (Preserved Section) -->
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
    </section>
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
