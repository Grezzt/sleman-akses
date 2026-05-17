<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sleman Akses')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet"
        integrity="sha512-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMYX+IGu9eJwzS1hOFyZ0QvBv8FzD8B4h3ceE0gD/2ezQ=="
        crossorigin="">
    <link href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="public-body">
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('public.map') }}">Sleman Akses</a>
            <div class="d-flex">
                <a href="{{ route('login') }}" class="btn btn-outline-primary">Masuk Admin</a>
            </div>
        </div>
    </nav>

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha512-KjS/2t3C6+P5THK6T/ymM3Gf3XzQFh9c2vQYl8uVLA2GgK2GkC3qTxdx3EJfYp8tC9f9a6BqM8XrU9FQ5x3n1g=="
        crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    @stack('scripts')
</body>

</html>
