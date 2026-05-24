<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sleman Akses - Admin')</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-background text-text-onsurface font-sans min-h-screen">
    <div class="flex flex-col lg:flex-row min-h-screen">
        <!-- Sidebar -->
        <aside class="w-full lg:w-[260px] bg-primary text-white p-6 flex flex-col border-b-2 lg:border-b-0 lg:border-r-2 border-border-solid flex-shrink-0 z-20 sticky top-0 lg:h-screen" style="background-image: url('{{ asset('landing-hijau.svg') }}'); background-size: cover; background-position: center;">
            <div class="mb-8">
                <div class="text-xl font-black text-white tracking-wide">Sleman Akses</div>
                <div class="text-xs font-bold text-secondary tracking-widest uppercase mt-1">Admin Panel</div>
            </div>

            <nav class="flex flex-col gap-2 flex-grow">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-white text-primary border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-secondary)] -translate-y-0.5 -translate-x-0.5' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-chart-simple w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.reports.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-white text-primary border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-secondary)] -translate-y-0.5 -translate-x-0.5' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-clipboard-list w-5 text-center"></i>
                    <span>Laporan</span>
                </a>
                <a href="{{ route('admin.map.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold transition-all {{ request()->routeIs('admin.map.*') ? 'bg-white text-primary border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-secondary)] -translate-y-0.5 -translate-x-0.5' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-map-location-dot w-5 text-center"></i>
                    <span>Peta</span>
                </a>
                <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-white text-primary border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-secondary)] -translate-y-0.5 -translate-x-0.5' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-tags w-5 text-center"></i>
                    <span>Kategori</span>
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold transition-all {{ request()->routeIs('admin.users.*') ? 'bg-white text-primary border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-secondary)] -translate-y-0.5 -translate-x-0.5' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-users w-5 text-center"></i>
                    <span>Pengguna</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="bg-surface border-b-2 border-border-solid shadow-[0_4px_0_0_var(--color-border-solid)] px-6 py-4 flex items-center justify-between sticky top-0 z-10">
                <div>
                    <h2 class="text-xl font-black text-text-primary">@yield('page_title', 'Dashboard')</h2>
                </div>
                <div class="flex items-center gap-4">
                    <span class="font-bold text-sm hidden md:inline-block text-text-muted">{{ auth()->user()?->name ?? 'Admin' }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-white text-error border-2 border-error px-4 py-1.5 rounded-full text-sm font-bold shadow-[2px_2px_0_0_var(--color-error)] hover:-translate-y-0.5 hover:shadow-[3px_3px_0_0_var(--color-error)] transition-all">Keluar</button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <section class="p-6 lg:p-8 flex-1 w-full max-w-[100vw]">
                @yield('content')
            </section>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
