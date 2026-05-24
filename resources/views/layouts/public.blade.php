<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Sleman Akses')</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Design System CSS -->
    @vite(['resources/css/app.css'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body class="public-body">
    <x-public.navbar />

    <main class="min-h-screen">
        @yield('content')
    </main>

    @unless(View::hasSection('hide_footer'))
        <x-public.footer />
    @endunless

    @stack('scripts')
</body>
</html>
