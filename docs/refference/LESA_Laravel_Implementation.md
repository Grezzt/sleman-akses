# 🚀 LESA App — Implementasi dengan Laravel

> Panduan lengkap membangun ulang website LESA menggunakan Laravel framework.

---

## 📋 Daftar Isi
1. [Tech Stack](#tech-stack)
2. [Struktur Proyek](#struktur-proyek)
3. [Setup Awal](#setup-awal)
4. [Database & Migration](#database--migration)
5. [Routes](#routes)
6. [Controllers](#controllers)
7. [Blade Templates](#blade-templates)
8. [Asset & CSS](#asset--css)
9. [Fitur Utama](#fitur-utama)

---

## 1. Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | Laravel 11.x (PHP 8.2+) |
| **Frontend** | Blade Templates + Vanilla CSS |
| **Database** | MySQL / PostgreSQL |
| **Asset Build** | Vite (bawaan Laravel 11) |
| **Email** | Laravel Mail + Mailtrap (dev) |
| **Queue** | Laravel Queue (untuk kirim email waitlist) |
| **Deployment** | Apache/Nginx + Shared Hosting / VPS |

---

## 2. Struktur Proyek

```
lesa-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php
│   │   │   └── WaitlistController.php
│   │   └── Requests/
│   │       └── WaitlistRequest.php
│   └── Models/
│       └── Waitlist.php
├── database/
│   └── migrations/
│       └── create_waitlists_table.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php
│   │   ├── partials/
│   │   │   ├── announcement-bar.blade.php
│   │   │   ├── sidebar.blade.php
│   │   │   └── fab.blade.php
│   │   └── home.blade.php
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
├── routes/
│   └── web.php
└── public/
    └── images/
        └── characters/
```

---

## 3. Setup Awal

```bash
# 1. Buat project Laravel baru
composer create-project laravel/laravel lesa-app
cd lesa-app

# 2. Konfigurasi .env
cp .env.example .env
php artisan key:generate

# 3. Setting database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lesa_db
DB_USERNAME=root
DB_PASSWORD=

# 4. Install dependencies frontend
npm install

# 5. Jalankan dev server
php artisan serve
npm run dev
```

---

## 4. Database & Migration

### Migration: Tabel Waitlist

```php
// database/migrations/xxxx_create_waitlists_table.php

public function up(): void
{
    Schema::create('waitlists', function (Blueprint $table) {
        $table->id();
        $table->string('email')->unique();
        $table->string('locale')->default('en'); // 'en' atau 'is' (Icelandic)
        $table->ipAddress('ip_address')->nullable();
        $table->timestamps();
    });
}
```

### Model: Waitlist

```php
// app/Models/Waitlist.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    protected $fillable = ['email', 'locale', 'ip_address'];
}
```

### Jalankan Migration

```bash
php artisan migrate
```

---

## 5. Routes

```php
// routes/web.php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\WaitlistController;

// Halaman utama
Route::get('/', [HomeController::class, 'index'])->name('home');

// Waitlist
Route::post('/waitlist', [WaitlistController::class, 'store'])->name('waitlist.store');

// Ganti bahasa
Route::get('/locale/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'is'])) {
        session(['locale' => $lang]);
    }
    return back();
})->name('locale.switch');
```

---

## 6. Controllers

### HomeController

```php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use App\Models\Waitlist;

class HomeController extends Controller
{
    public function index()
    {
        $waitlistCount = Waitlist::count();

        return view('home', compact('waitlistCount'));
    }
}
```

### WaitlistController

```php
// app/Http/Controllers/WaitlistController.php

namespace App\Http\Controllers;

use App\Models\Waitlist;
use App\Http\Requests\WaitlistRequest;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    public function store(WaitlistRequest $request)
    {
        // Cek apakah email sudah terdaftar
        $exists = Waitlist::where('email', $request->email)->exists();

        if ($exists) {
            return back()->with('info', 'Email already registered!');
        }

        Waitlist::create([
            'email'      => $request->email,
            'locale'     => session('locale', 'en'),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'You are on the waitlist! 🎉');
    }
}
```

### WaitlistRequest (Form Validation)

```php
// app/Http/Requests/WaitlistRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WaitlistRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Please enter your email address.',
            'email.email'    => 'Please enter a valid email address.',
        ];
    }
}
```

---

## 7. Blade Templates

### Layout Utama

```html
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ session('locale', 'en') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="LESA - The most fun reading game for children grades 1-4">
    <title>LESA – Reading Adventure for Kids</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    {{-- Announcement Bar --}}
    @include('partials.announcement-bar')

    <div class="page-wrapper">
        {{-- Sidebar Kiri --}}
        @include('partials.sidebar')

        {{-- Konten Utama --}}
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    {{-- FAB Button --}}
    @include('partials.fab')

</body>
</html>
```

### Announcement Bar Partial

```html
{{-- resources/views/partials/announcement-bar.blade.php --}}
<div class="announcement-bar">
    <div class="ticker-wrap">
        <div class="ticker">
            <span>LESA will be released in the fall of 2026. Get ready to dive into something exciting!</span>
            <span>Sign up for the waiting list, and let's make learning fun and exciting!</span>
            <span>LESA will be released in the fall of 2026. Get ready to dive into something exciting!</span>
        </div>
    </div>
</div>
```

### Sidebar Partial

```html
{{-- resources/views/partials/sidebar.blade.php --}}
<aside class="sidebar">
    <button class="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>

    <div class="lang-switcher">
        <a href="{{ route('locale.switch', 'en') }}"
           class="{{ session('locale','en') === 'en' ? 'active' : '' }}">🇺🇸</a>
        <a href="{{ route('locale.switch', 'is') }}"
           class="{{ session('locale','en') === 'is' ? 'active' : '' }}">🇮🇸</a>
    </div>

    <div class="brand-vertical">LESA</div>
</aside>
```

### Home Page

```html
{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('content')

{{-- HERO SECTION --}}
<section class="section-hero">
    <div class="hero-logo">LESA</div>
    <div class="hero-characters">
        <img src="{{ asset('images/characters/mascot-group.svg') }}" alt="LESA Characters">
    </div>
    <h1 class="hero-headline">
        We're about to create the most fun reading game in the world!
    </h1>
    <div class="hero-actions">
        <a href="#contact" class="btn btn-dark">Get in touch</a>
        <a href="#waitlist" class="btn btn-outline">Join the waitlist</a>
    </div>
    <div class="hero-progress">
        <div class="progress-bar active"></div>
        <div class="progress-bar"></div>
        <div class="progress-bar"></div>
    </div>
</section>

{{-- GOAL SECTION --}}
<section class="section-goal">
    <div class="container">
        <h2>
            The goal of LESA is to boost the joy of reading and enhance
            reading skills for children in grades 1-4, while also gaining
            an overview of reading across the nation. Let's make reading a fun adventure!
        </h2>
        <div class="characters-row">
            <img src="{{ asset('images/characters/reindeer.svg') }}" alt="Reindeer character">
            <img src="{{ asset('images/characters/sheep.svg') }}" alt="Sheep character">
            <img src="{{ asset('images/characters/seal.svg') }}" alt="Seal character">
        </div>
    </div>
</section>

{{-- WHAT IS LESA --}}
<section class="section-what">
    <div class="container feature-layout">
        <div class="feature-image">
            <img src="{{ asset('images/app-preview.png') }}" alt="LESA App Preview">
        </div>
        <div class="feature-text">
            <h2>What is the <span class="text-purple">LESA</span> reading game?</h2>
            <p>
                LESA is an app that helps young school-age children
                develop their reading skills in a fun and engaging way.
                This reading app supports the reading and preservation
                of the Icelandic language.
            </p>
        </div>
    </div>
</section>

{{-- GAMIFICATION --}}
<section class="section-features">
    <div class="container feature-layout reverse">
        <div class="feature-image">
            <div class="image-box image-box--yellow">
                <img src="{{ asset('images/characters/kids-playing.svg') }}" alt="Kids playing">
            </div>
        </div>
        <div class="feature-text">
            <h2><span class="text-amber">A Fun</span> and Gamified World</h2>
            <p>LESA uses gamification to spark children's interest and create a positive connection with reading.</p>
        </div>
    </div>

    <div class="container feature-layout">
        <div class="feature-image">
            <div class="image-box image-box--pink">
                <img src="{{ asset('images/characters/kids-stats.svg') }}" alt="Statistics">
            </div>
        </div>
        <div class="feature-text">
            <h2>Get detailed <span class="text-pink">statistics</span> on your child's progress with a smile!</h2>
        </div>
    </div>
</section>

{{-- WAITLIST COUNTER --}}
<section class="section-counter">
    <div class="container text-center">
        <div class="counter-number">{{ number_format($waitlistCount) }}+ and counting</div>
        <p class="counter-sub">Be among the first to experience reading — the LESA way.</p>
        <a href="#waitlist" class="btn btn-dark">Join the waitlist</a>
    </div>
    <div class="playground-scene">
        <img src="{{ asset('images/playground-scene.svg') }}" alt="Playground">
    </div>
</section>

{{-- WAITLIST FORM --}}
<section class="section-waitlist" id="waitlist">
    <div class="container text-center">
        <h2>The game is set to arrive in the fall of 2026. Sign up for the waitlist!</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <form action="{{ route('waitlist.store') }}" method="POST" class="waitlist-form">
            @csrf
            <input
                type="email"
                name="email"
                placeholder="Your email address....."
                class="input-email @error('email') is-invalid @enderror"
                required
            >
            @error('email')
                <span class="error-msg">{{ $message }}</span>
            @enderror
            <button type="submit" class="btn btn-green">Join the waitlist</button>
        </form>

        <div class="footer-contact">
            <span>📞 +3545478001</span>
            <a href="#" class="social-icon">Facebook</a>
        </div>
        <div class="footer-links">
            <a href="#">Cookie settings</a>
            <a href="#">Cookies</a>
            <a href="#">Privacy Policy</a>
        </div>
    </div>

    <div class="footer-brand">LESA</div>
    <div class="footer-bottom">
        <span>Funded by Technology Development Fund</span>
        <span>© 2026 LESA. All rights reserved</span>
    </div>
</section>

@endsection
```

---

## 8. Asset & CSS

### CSS Utama (Design Tokens)

```css
/* resources/css/app.css */

/* === GOOGLE FONTS === */
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap');

/* === DESIGN TOKENS === */
:root {
  /* Colors */
  --color-yellow:     #FFD600;
  --color-purple:     #5252D4;
  --color-purple-dark:#4B4BC8;
  --color-pink:       #E91E8C;
  --color-amber:      #F59E0B;
  --color-green:      #3D8B6E;
  --color-dark:       #1A1A2E;
  --color-off-white:  #F5F0EB;
  --color-white:      #FFFFFF;

  /* Typography */
  --font-main: 'Nunito', sans-serif;

  /* Spacing */
  --section-py: 80px;
  --container-max: 1200px;

  /* Border Radius */
  --radius-pill: 50px;
  --radius-card: 20px;
  --radius-sm:   12px;
}

/* === RESET === */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: var(--font-main);
  background: var(--color-white);
  color: var(--color-dark);
  overflow-x: hidden;
}

/* === ANNOUNCEMENT BAR === */
.announcement-bar {
  background: var(--color-yellow);
  color: var(--color-dark);
  padding: 10px 0;
  font-weight: 600;
  font-size: 14px;
  overflow: hidden;
  white-space: nowrap;
  position: sticky;
  top: 0;
  z-index: 1000;
}

.ticker { display: inline-block; animation: marquee 25s linear infinite; }
.ticker span { margin-right: 60px; }

@keyframes marquee {
  0%   { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

/* === PAGE WRAPPER === */
.page-wrapper { display: flex; }

/* === SIDEBAR === */
.sidebar {
  position: fixed;
  left: 0; top: 40px; bottom: 0;
  width: 56px;
  background: var(--color-purple);
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 16px 0;
  gap: 16px;
  z-index: 900;
  border-radius: 0 12px 12px 0;
}

.hamburger { background: none; border: none; cursor: pointer; }
.hamburger span {
  display: block; width: 22px; height: 2px;
  background: white; margin: 4px 0;
}

.lang-switcher { display: flex; flex-direction: column; gap: 8px; }
.lang-switcher a { font-size: 20px; text-decoration: none; }

.brand-vertical {
  position: absolute; bottom: 80px;
  writing-mode: vertical-rl;
  transform: rotate(180deg);
  font-size: 22px; font-weight: 900;
  color: white; letter-spacing: 6px;
}

/* === MAIN CONTENT === */
.main-content { margin-left: 56px; width: 100%; }

/* === CONTAINER === */
.container { max-width: var(--container-max); margin: 0 auto; padding: 0 24px; }

/* === SECTIONS === */
.section-hero {
  background: var(--color-yellow);
  min-height: 100vh;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  text-align: center;
  padding: 40px 24px;
  position: relative;
}

.hero-logo {
  font-size: 120px; font-weight: 900;
  color: white; opacity: 0.3;
  letter-spacing: 20px;
  position: absolute; top: 10%;
}

.hero-headline {
  font-size: 40px; font-weight: 800;
  color: white;
  max-width: 700px;
  margin-bottom: 32px;
  line-height: 1.3;
}

.hero-actions { display: flex; gap: 16px; flex-wrap: wrap; justify-content: center; }

.section-goal {
  background: var(--color-purple);
  padding: var(--section-py) 0;
  color: white;
  text-align: center;
}

.section-goal h2 {
  font-size: 36px; font-weight: 800;
  max-width: 900px; margin: 0 auto 48px;
  line-height: 1.4;
}

.section-what, .section-features {
  background: var(--color-off-white);
  padding: var(--section-py) 0;
}

.feature-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 48px;
  align-items: center;
}

.feature-layout.reverse { direction: rtl; }
.feature-layout.reverse > * { direction: ltr; }

.image-box {
  border-radius: var(--radius-card);
  overflow: hidden;
  padding: 24px;
}

.image-box--yellow { background: var(--color-yellow); }
.image-box--pink   { background: #FF69B4; }

.section-counter {
  background: var(--color-purple);
  padding: var(--section-py) 0 0;
  color: white;
  text-align: center;
  position: relative;
}

.counter-number {
  font-size: 56px; font-weight: 900;
  margin-bottom: 16px;
}

.counter-sub { font-size: 20px; opacity: 0.8; margin-bottom: 32px; }

.section-waitlist {
  background: var(--color-purple-dark);
  padding: var(--section-py) 0;
  color: white;
  text-align: center;
  position: relative;
}

.waitlist-form {
  display: flex; gap: 12px;
  justify-content: center;
  flex-wrap: wrap;
  margin: 32px 0;
}

/* === BUTTONS === */
.btn {
  padding: 14px 32px;
  border-radius: var(--radius-pill);
  font-weight: 700; font-size: 16px;
  border: none; cursor: pointer;
  text-decoration: none;
  display: inline-block;
  transition: transform 0.2s, box-shadow 0.2s;
}

.btn:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.25); }

.btn-dark  { background: var(--color-dark);  color: white; }
.btn-green { background: var(--color-green); color: white; }
.btn-outline {
  background: transparent;
  color: var(--color-dark);
  border: 2px solid var(--color-dark);
}

/* === INPUT === */
.input-email {
  padding: 14px 24px;
  border-radius: var(--radius-pill);
  border: 2px solid rgba(255,255,255,0.4);
  background: rgba(255,255,255,0.15);
  color: white; font-size: 16px;
  min-width: 280px; font-family: var(--font-main);
}

.input-email::placeholder { color: rgba(255,255,255,0.6); }

/* === ALERTS === */
.alert {
  padding: 12px 24px;
  border-radius: var(--radius-sm);
  margin: 16px auto;
  max-width: 500px;
  font-weight: 600;
}

.alert-success { background: #d1fae5; color: #065f46; }
.alert-info    { background: #dbeafe; color: #1e40af; }
.error-msg     { color: #fca5a5; font-size: 14px; display: block; margin-top: 4px; }

/* === FAB === */
.fab-chat {
  position: fixed;
  bottom: 24px; right: 24px;
  width: 60px; height: 60px;
  border-radius: 50%;
  background: #FF69B4;
  border: 3px solid white;
  box-shadow: 0 4px 16px rgba(0,0,0,0.3);
  cursor: pointer;
  overflow: hidden;
  z-index: 800;
}

/* === TEXT HIGHLIGHTS === */
.text-purple { color: var(--color-purple); }
.text-pink   { color: var(--color-pink);   }
.text-amber  { color: var(--color-amber);  }

/* === UTILITY === */
.text-center { text-align: center; }

/* === ANIMATIONS === */
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50%       { transform: translateY(-15px); }
}

.character-float { animation: float 3s ease-in-out infinite; }

/* === RESPONSIVE === */
@media (max-width: 768px) {
  .feature-layout { grid-template-columns: 1fr; }
  .feature-layout.reverse { direction: ltr; }
  .hero-headline { font-size: 28px; }
  .counter-number { font-size: 36px; }
  .sidebar { width: 48px; }
  .main-content { margin-left: 48px; }
}
```

### JavaScript (Scroll Reveal + Counter)

```js
// resources/js/app.js

// === SCROLL REVEAL ===
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// === COUNT-UP ANIMATION ===
function countUp(el, target, duration = 2000) {
  let start = 0;
  const step = target / (duration / 16);
  const timer = setInterval(() => {
    start += step;
    if (start >= target) { start = target; clearInterval(timer); }
    el.textContent = Math.floor(start).toLocaleString() + '+ and counting';
  }, 16);
}

const counterEl = document.querySelector('.counter-number');
if (counterEl) {
  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const target = parseInt(counterEl.dataset.count || '1228');
        countUp(counterEl, target);
        counterObserver.unobserve(entry.target);
      }
    });
  });
  counterObserver.observe(counterEl);
}
```

---

## 9. Fitur Utama

### 9.1 Ringkasan Fitur yang Diimplementasi

| Fitur | Implementasi |
|-------|-------------|
| Waitlist Form | `WaitlistController@store` + `Waitlist` model |
| Counter Realtime | `HomeController` → kirim `$waitlistCount` ke view |
| Multi-Language | Session-based locale switching |
| Email Validation | `WaitlistRequest` (Form Request) |
| Marquee Bar | Pure CSS `@keyframes marquee` |
| Scroll Reveal | Vanilla JS `IntersectionObserver` |
| Count-up Number | Vanilla JS animation |
| Responsive | CSS Grid + Media Queries |

### 9.2 Perintah Artisan yang Berguna

```bash
# Buat controller
php artisan make:controller WaitlistController

# Buat model + migration sekaligus
php artisan make:model Waitlist -m

# Buat form request
php artisan make:request WaitlistRequest

# Jalankan migration
php artisan migrate

# Lihat semua route
php artisan route:list

# Build asset production
npm run build

# Clear cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### 9.3 Tambahan: Admin Dashboard (Opsional)

Untuk melihat daftar waitlist dari admin panel:

```php
// routes/web.php (tambahkan)
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/waitlist', function () {
        $waitlists = \App\Models\Waitlist::latest()->paginate(20);
        return view('admin.waitlist', compact('waitlists'));
    })->name('admin.waitlist');
});
```

---

*Dokumentasi implementasi Laravel untuk referensi pengembangan LESA App — Mata Kuliah Pemrograman UI Semester 6.*
