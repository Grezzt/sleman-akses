<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sleman Akses - Admin')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    @vite(['resources/css/admin.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="admin-body">
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <div class="brand-title">Sleman Akses</div>
                <div class="brand-subtitle">Admin Panel</div>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-simple"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.reports.index') }}"
                    class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <span>Laporan</span>
                </a>
                <a href="{{ route('admin.map.index') }}"
                    class="nav-link {{ request()->routeIs('admin.map.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <span>Peta</span>
                </a>
                <a href="{{ route('admin.categories.index') }}"
                    class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i>
                    <span>Kategori</span>
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Pengguna</span>
                </a>
            </nav>
        </aside>

        <main class="admin-main">
            <header class="admin-topbar">
                <div class="topbar-left">
                    <span class="topbar-title">@yield('page_title', 'Dashboard')</span>
                </div>
                <div class="topbar-right">
                    <span class="admin-name">{{ auth()->user()?->name ?? 'Admin' }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light">Keluar</button>
                    </form>
                </div>
            </header>

            <section class="admin-content">
                @yield('content')
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    @stack('scripts')
</body>

</html>
