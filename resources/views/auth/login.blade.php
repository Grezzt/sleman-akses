<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sleman Akses Admin - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-body min-h-screen flex flex-col md:flex-row">
    <!-- Kolom Kiri - Background Hijau -->
    <div class="hidden md:flex md:w-1/2 relative overflow-hidden items-center justify-center bg-[var(--color-primary)]"
         style="background-image: url('/landing-hijau.svg'); background-size: cover; background-position: center;">

        <!-- Overlay untuk mempertegas tulisan jika diperlukan -->
        <div class="absolute inset-0 bg-[var(--color-primary)] opacity-40"></div>

        <div class="relative z-10 p-12 text-white max-w-lg">
            <h1 class="text-5xl font-bold mb-4 font-sans leading-tight "style="color: var(--color-text-onprimary);">Sleman Akses</h1>
            <p class="text-xl opacity-90 mb-6 font-sans">Dashboard pengelolaan laporan aksesibilitas fasilitas umum di Kabupaten Sleman.</p>
            <span class="bg-white text-[var(--color-primary)] px-4 py-1.5 rounded-full text-sm font-bold shadow-sm font-sans border-2 border-[var(--color-border-solid)]" style="box-shadow: 2px 2px 0px 0px var(--color-border-solid);">Admin Only</span>
        </div>
    </div>

    <!-- Kolom Kanan - Form Login dengan Background Putih -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-6 relative bg-[var(--color-background)]"
         style="background-image: url('/landing-putih.svg'); background-size: cover; background-position: center;">

        <!-- Login Card -->
        <div class="card-solid p-8 md:p-10 w-full max-w-md relative z-10">
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold text-primary-heading mb-2 font-sans">Login Admin</h2>
                <p class="text-[var(--color-text-muted)] font-sans">Masuk untuk mengelola laporan dan fasilitas.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-6 font-sans text-sm shadow-[2px_2px_0px_0px_rgba(248,113,113,1)]" role="alert">
                    <span class="block sm:inline">{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5 font-sans">
                @csrf
                <div>
                    <label class="block text-sm font-bold mb-2 text-[var(--color-text-primary)]" for="email">Email</label>
                    <input type="email" name="email" id="email" class="input-solid" value="{{ old('email') }}" required autofocus placeholder="admin@slemanakses.id">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2 text-[var(--color-text-primary)]" for="password">Password</label>
                    <input type="password" name="password" id="password" class="input-solid" required placeholder="••••••••">
                </div>
                <div class="pt-4">
                    <button type="submit" class="btn-primary-solid w-full text-center">Masuk</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
