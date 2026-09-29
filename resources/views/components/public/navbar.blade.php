<nav class="fixed top-6 left-1/2 -translate-x-1/2 w-[92%] max-w-7xl z-50 bg-surface border-2 border-border-solid shadow-solid rounded-full px-6 py-3 flex items-center justify-between">
    <!-- Logo -->
    <a href="{{ url('/') }}" class="flex items-center gap-3">
        <div class="bg-primary rounded-2xl p-1.5 flex items-center justify-center">
            <img src="{{ asset('Logo Image_margin.svg') }}" alt="Sleman Akses Logo" class="h-8 w-8 object-contain">
        </div>
        <div class="hidden md:block">
            <h1 class="text-xl font-black text-primary leading-none">Sleman Akses</h1>
            <span class="text-xs font-bold text-text-muted">Kabupaten Sleman</span>
        </div>
    </a>

    <!-- Desktop Nav -->
    <div class="hidden md:flex items-center gap-8 font-bold">
        <a href="{{ url('/') }}" class="text-text-primary hover:text-primary transition {{ request()->is('/') ? 'text-primary border-b-2 border-primary' : '' }}">Beranda</a>
        <a href="{{ url('/map') }}" class="text-text-primary hover:text-primary transition {{ request()->is('map') ? 'text-primary border-b-2 border-primary' : '' }}">Peta Aksesibilitas</a>
        <a href="{{ route('public.articles.index') }}" class="text-text-primary hover:text-primary transition {{ request()->is('berita*') ? 'text-primary border-b-2 border-primary' : '' }}">Berita & Edukasi</a>
        <a href="{{ url('/about') }}" class="text-text-primary hover:text-primary transition {{ request()->is('about') ? 'text-primary border-b-2 border-primary' : '' }}">Tentang</a>
    </div>

    <!-- Desktop CTA -->
    <div class="hidden md:block">
        <a href="{{ url('/login') }}" class="btn-primary-solid text-sm">
            <i class="fas fa-sign-in-alt mr-2"></i> Masuk
        </a>
    </div>

    <!-- Mobile Menu Button -->
    <button class="md:hidden text-text-primary text-2xl focus:outline-none" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden absolute top-full left-0 mt-4 w-full bg-surface border-2 border-border-solid shadow-solid rounded-2xl flex flex-col font-bold overflow-hidden origin-top transition-all">
        <a href="{{ url('/') }}" class="block px-6 py-4 text-text-primary hover:bg-background border-b-2 border-border-solid">Beranda</a>
        <a href="{{ url('/map') }}" class="block px-6 py-4 text-text-primary hover:bg-background border-b-2 border-border-solid">Peta Aksesibilitas</a>
        <a href="{{ route('public.articles.index') }}" class="block px-6 py-4 text-text-primary hover:bg-background border-b-2 border-border-solid {{ request()->is('berita*') ? 'text-primary bg-background font-black' : '' }}">Berita & Edukasi</a>
        <a href="{{ url('/about') }}" class="block px-6 py-4 text-text-primary hover:bg-background border-b-2 border-border-solid">Tentang</a>
        <div class="p-6">
            <a href="{{ url('/login') }}" class="btn-primary-solid block text-center w-full">
                <i class="fas fa-sign-in-alt mr-2"></i> Masuk
            </a>
        </div>
    </div>
</nav>
