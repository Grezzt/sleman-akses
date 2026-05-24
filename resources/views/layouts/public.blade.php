<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Sleman Akses')</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Design System CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">

    @stack('styles')
</head>
<body class="public-body">
    @unless(View::hasSection('hide_navbar'))
        <x-public.navbar />
    @endunless

    <main class="min-h-screen">
        @yield('content')
    </main>

    @unless(View::hasSection('hide_footer'))
        <x-public.footer />
    @endunless

    @stack('scripts')
</body>
</html>
