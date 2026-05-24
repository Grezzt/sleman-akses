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
<body class="bg-background text-text-onsurface font-sans min-h-screen overflow-x-hidden">
    <div class="flex min-h-screen relative w-full">
        <!-- Sidebar Overlay (Mobile) -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed lg:sticky top-0 left-0 h-[100dvh] w-[260px] bg-primary text-white p-6 flex flex-col border-r-2 border-border-solid flex-shrink-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out overflow-y-auto" style="background-image: url('{{ asset('landing-hijau.svg') }}'); background-size: cover; background-position: center;">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <div class="text-xl font-black text-white tracking-wide">Sleman Akses</div>
                    <div class="text-xs font-bold text-secondary tracking-widest uppercase mt-1">Admin Panel</div>
                </div>
                <button id="close-sidebar" class="lg:hidden text-white/80 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
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

            <div class="mt-8 pt-6 border-t-2 border-white/20">
                <div class="flex items-center gap-3 px-4 py-2 mb-2">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center font-bold text-white flex-shrink-0">
                        {{ substr(auth()->user()?->full_name ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex flex-col overflow-hidden">
                        <span class="font-bold text-sm truncate">{{ auth()->user()?->full_name ?? 'Admin' }}</span>
                        <span class="text-xs text-white/70 truncate">{{ auth()->user()?->email ?? '-' }}</span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold transition-all text-white/80 hover:bg-white/10 hover:text-white text-left">
                        <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="bg-surface border-b-2 border-border-solid shadow-[0_4px_0_0_var(--color-border-solid)] px-6 py-4 flex items-center gap-4 sticky top-0 z-10">
                <button id="open-sidebar" class="lg:hidden text-text-primary hover:text-primary transition-colors flex items-center justify-center">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <h2 class="text-xl font-black text-text-primary m-0">@yield('page_title', 'Dashboard')</h2>
            </header>

            <!-- Page Content -->
            <section class="p-6 lg:p-8 flex-1 w-full overflow-x-hidden">
                @yield('content')
            </section>
        </main>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const openBtn = document.getElementById('open-sidebar');
        const closeBtn = document.getElementById('close-sidebar');

        const toggleSidebar = () => {
            const isOpen = sidebar.classList.contains('translate-x-0');
            if (isOpen) {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            } else {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.remove('hidden');
            }
        };

        openBtn.addEventListener('click', toggleSidebar);
        closeBtn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);
    </script>

    @stack('scripts')
</body>
</html>
