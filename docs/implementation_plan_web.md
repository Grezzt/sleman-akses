# Sleman Akses — Web Implementation Plan

> **Platform:** Laravel 12 · Blade · Bootstrap 5 · Leaflet.js  
> **Scope:** Halaman Publik (peta interaktif) + Admin Dashboard  
> **Prerequisite:** Backend fase B1–B4 harus selesai terlebih dahulu

---

## Ringkasan Fase

| #   | Fase                             | Output Utama                     | Status |
| --- | -------------------------------- | -------------------------------- | ------ |
| W1  | Middleware & Routes Web          | CheckRole, web.php               | ⬜     |
| W2  | Layout & Aset Frontend           | 2 layout Blade + admin.css       | ⬜     |
| W3  | Auth Web Admin                   | LoginController + login view     | ⬜     |
| W4  | Halaman Publik — Peta Interaktif | PublicMapController + view       | ⬜     |
| W5  | Admin Dashboard                  | DashboardController + view       | ⬜     |
| W6  | Admin — Manajemen Laporan        | ReportController + 2 view        | ⬜     |
| W7  | Admin — Validasi Laporan         | ValidationController             | ⬜     |
| W8  | Admin — Peta & Kategori & User   | 3 Controller + 3 view            | ⬜     |
| W9  | Blade Components & Finalisasi    | Components, polish, QA           | ⬜     |

---

## FASE W1 — Middleware & Routes Web

### Task: Middleware CheckRole

> File ini dibuat di backend, tapi didaftarkan di sini untuk Web.

- [ ] Pastikan `app/Http/Middleware/CheckRole.php` sudah ada (dari backend B7)
- [ ] Daftarkan alias `role` di `bootstrap/app.php`:
  ```php
  ->withMiddleware(function (Middleware $middleware) {
      $middleware->alias(['role' => CheckRole::class]);
  })
  ```

### Task: Update `routes/web.php`

```php
// Public
Route::get('/', [PublicMapController::class, 'index'])->name('public.map');

// Auth
Route::get('/login',  [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

// Admin — protected
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('dashboard',                [DashboardController::class, 'index'])->name('dashboard');

    Route::get('reports',                  [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{id}',             [AdminReportController::class, 'show'])->name('reports.show');
    Route::post('reports/{id}/validate',   [ValidationController::class, 'store'])->name('reports.validate');

    Route::get('map',                      [AdminMapController::class, 'index'])->name('map.index');
    Route::delete('map/{id}',              [AdminMapController::class, 'destroy'])->name('map.destroy');

    Route::get('categories',               [CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories',              [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{id}',          [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{id}',       [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('users',                    [AdminUserController::class, 'index'])->name('users.index');
});
```

---

## FASE W2 — Layout & Aset Frontend

> Libraries via CDN: Bootstrap 5.3 · Font Awesome 6 · Leaflet.js 1.9 · MarkerCluster · DataTables · SweetAlert2 · Chart.js

### Task: Layout Blade — `resources/views/layouts/`

- [ ] **[NEW]** `layouts/app.blade.php` — Layout Admin
  ```
  <html>
    <head> Bootstrap CSS, Font Awesome, @stack('styles') </head>
    <body>
      <aside> Sidebar: logo, menu nav (Dashboard, Laporan, Peta, Kategori, Pengguna) </aside>
      <main>
        <header> Topbar: nama admin, tombol logout </header>
        <div class="content"> @yield('content') </div>
      </main>
      <script> Bootstrap JS, @stack('scripts') </script>
    </body>
  </html>
  ```

- [ ] **[NEW]** `layouts/public.blade.php` — Layout Publik
  ```
  <html>
    <head> Bootstrap CSS, Leaflet CSS, @stack('styles') </head>
    <body>
      <nav> Navbar: logo "Sleman Akses", tombol "Masuk Admin" </nav>
      @yield('content')
      <script> Leaflet JS, MarkerCluster JS, @stack('scripts') </script>
    </body>
  </html>
  ```

### Task: CSS Admin — `resources/css/admin.css`

- [ ] **[NEW]** `resources/css/admin.css`
  ```css
  /* Warna utama sidebar: #1e293b */
  /* Sidebar width: 260px */
  /* Active menu: background highlight */
  /* Card shadow dan border-radius */
  /* Badge status: pending=warning, approved=success, rejected=danger */
  ```

---

## FASE W3 — Auth Web Admin

### Task: LoginController

- [ ] **[NEW]** `app/Http/Controllers/Auth/LoginController.php`
  ```php
  public function showForm(): View          // return view('auth.login')
  public function login(Request $request): RedirectResponse
  {
      // Validasi email + password
      // Auth::attempt(['email', 'password', 'role' => 'admin'])
      // Jika berhasil: redirect('/admin/dashboard')
      // Jika gagal: back()->withErrors(['email' => 'Kredensial tidak valid.'])
  }
  public function logout(Request $request): RedirectResponse
  {
      // Auth::logout(), session invalidate, redirect '/login'
  }
  ```

### Task: View Login

- [ ] **[NEW]** `resources/views/auth/login.blade.php`
  - Layout dua kolom: kiri = background peta blur (foto/gradient), kanan = card login
  - Logo sistem + judul "Sleman Akses Admin"
  - Form: input email, input password, tombol "Masuk"
  - Flash error message
  - Tidak ada link registrasi

---

## FASE W4 — Halaman Publik: Peta Interaktif

### Task: Controller

- [ ] **[NEW]** `app/Http/Controllers/PublicMapController.php`
  ```php
  // Inject: MapLocationService
  public function index(): View
  {
      $locations = $this->mapService->getPublished();
      $categories = $this->categoryService->getAll();
      return view('public.map', compact('locations', 'categories'));
  }
  ```

### Task: View Peta Publik

- [ ] **[NEW]** `resources/views/public/map.blade.php`
  ```
  @extends('layouts.public')
  @section('content')
    <div id="map" style="height: calc(100vh - 56px)"></div>
    <!-- Filter kategori floating panel kanan atas -->
    <!-- Script Leaflet init -->
    <!-- Script MarkerCluster -->
    <!-- Popup: foto, nama, daftar fasilitas + status, tanggal publish -->
    <!-- Marker per kategori berbeda warna/icon -->
  @endsection
  ```
  
  Data markers di-pass sebagai JSON dari controller:
  ```php
  // Di controller:
  $markersJson = $locations->map(fn($loc) => [
      'lat'       => $loc->latitude,
      'lng'       => $loc->longitude,
      'photo_url' => $loc->validation->report->photo_url,
      'category'  => $loc->validation->report->reportDetails->first()->category->facility_name,
      'facilities'=> $loc->validation->report->reportDetails->map(...),
      'published' => $loc->publish_date,
  ])->toJson();
  ```

---

## FASE W5 — Admin Dashboard

### Task: Controller

- [ ] **[NEW]** `app/Http/Controllers/Admin/DashboardController.php`
  ```php
  // Inject: ValidationRepository, UserRepository, MapLocationService
  public function index(): View
  {
      return view('admin.dashboard.index', [
          'totalReports'    => Validation::count(),
          'pendingCount'    => Validation::where('approval_status', 'pending')->count(),
          'approvedCount'   => Validation::where('approval_status', 'approved')->count(),
          'rejectedCount'   => Validation::where('approval_status', 'rejected')->count(),
          'totalUsers'      => User::where('role', 'user')->count(),
          'chartData'       => $this->getMonthlyReportData(),   // 12 bulan terakhir
          'recentLocations' => $this->mapService->getPublished()->take(5),
      ]);
  }
  ```

### Task: View Dashboard

- [ ] **[NEW]** `resources/views/admin/dashboard/index.blade.php`
  ```
  @extends('layouts.app')
  @section('content')
    <!-- 4 Stat Card: Total Laporan, Pending, Disetujui, Ditolak -->
    <!-- Grafik Chart.js: laporan masuk per bulan (bar chart) -->
    <!-- Mini peta Leaflet: lokasi yang sudah approved -->
  @endsection
  ```

---

## FASE W6 — Admin: Manajemen Laporan

### Task: Controller

- [ ] **[NEW]** `app/Http/Controllers/Admin/ReportController.php`
  ```php
  // Inject: ReportRepository
  public function index(Request $request): View
  {
      // Filter by status: request('status') → 'pending'|'approved'|'rejected'|null
      $reports = $this->reportRepo->getAllPaginated(
          ['status' => $request->get('status')],
          perPage: 15
      );
      return view('admin.reports.index', compact('reports'));
  }

  public function show(int $id): View
  {
      $report = $this->reportRepo->findWithRelations($id);
      abort_if(!$report, 404);
      return view('admin.reports.show', compact('report'));
  }
  ```

### Task: Views Laporan

- [ ] **[NEW]** `resources/views/admin/reports/index.blade.php`
  ```
  - DataTables: kolom No, Pengirim, Tanggal, Kategori, Status (badge), Aksi
  - Dropdown filter status (All / Pending / Approved / Rejected)
  - Tombol "Lihat Detail" → /admin/reports/{id}
  ```

- [ ] **[NEW]** `resources/views/admin/reports/show.blade.php`
  ```
  Layout 2 kolom:
  Kiri:
    - Info pengirim (nama, email)
    - Timestamp laporan
    - Koordinat GPS (lat, lng)
    - Mini peta Leaflet dengan marker lokasi

  Kanan:
    - Foto laporan (img fullsize, klik untuk lightbox/zoom)

  Bawah:
    - Tabel detail fasilitas per kategori (ada ✓ / tidak ✗)
    - Form validasi: select status + textarea catatan evaluasi
    - Tombol "Submit" dengan konfirmasi SweetAlert2
  ```

---

## FASE W7 — Admin: Validasi Laporan

### Task: Controller

- [ ] **[NEW]** `app/Http/Controllers/Admin/ValidationController.php`
  ```php
  // Inject: ValidationService, ReportRepository
  public function store(Request $request, int $reportId): RedirectResponse
  {
      $request->validate([
          'approval_status' => 'required|in:approved,rejected',
          'evaluation_note' => 'nullable|string|max:1000',
      ]);

      $report     = $this->reportRepo->findWithRelations($reportId);
      $validation = $report->validation;

      $this->validationService->validate($validation, $request->only([
          'approval_status', 'evaluation_note'
      ]), auth()->user());

      return redirect()->route('admin.reports.index')
          ->with('success', 'Laporan berhasil divalidasi.');
  }
  ```

---

## FASE W8 — Admin: Peta, Kategori & Pengguna

### Task: Admin Map Controller

- [ ] **[NEW]** `app/Http/Controllers/Admin/MapController.php`
  ```php
  // Inject: MapLocationService
  public function index(): View
  {
      $locations = $this->mapService->getPublished();
      return view('admin.map.index', compact('locations'));
  }
  public function destroy(int $id): RedirectResponse
  {
      $this->mapService->delete($id);
      return back()->with('success', 'Lokasi berhasil dihapus.');
  }
  ```

- [ ] **[NEW]** `resources/views/admin/map/index.blade.php`
  ```
  - Leaflet fullscreen di sisi kanan
  - Sidebar kiri: daftar lokasi published (nama, tanggal)
  - Klik item sidebar → peta pindah ke marker
  - Tombol hapus lokasi + konfirmasi SweetAlert2
  ```

### Task: Category Controller & View

- [ ] **[NEW]** `app/Http/Controllers/Admin/CategoryController.php`
  ```php
  // Inject: CategoryService
  index()   → view('admin.categories.index', ['categories' => $this->categoryService->getAll()])
  store()   → validate + create + redirect back with success
  update()  → validate + update + redirect back with success
  destroy() → delete + redirect back with success
  ```

- [ ] **[NEW]** `resources/views/admin/categories/index.blade.php`
  ```
  - Tabel: No, Nama Fasilitas, Preview Icon, Aksi (Edit/Hapus)
  - Tombol "Tambah Kategori" → Bootstrap Modal form
  - Modal Edit dengan prefill data
  - Konfirmasi hapus SweetAlert2
  ```

### Task: Admin User Controller & View

- [ ] **[NEW]** `app/Http/Controllers/Admin/UserController.php`
  ```php
  // Inject: UserRepository
  public function index(): View
  {
      $users = User::where('role', 'user')->withCount('reports')->paginate(20);
      return view('admin.users.index', compact('users'));
  }
  ```

- [ ] **[NEW]** `resources/views/admin/users/index.blade.php`
  ```
  - Tabel: No, Nama, Email, Tanggal Daftar, Jumlah Laporan
  - Pagination Laravel default
  ```

---

## FASE W9 — Blade Components & Finalisasi

### Task: Blade Components — `resources/views/components/`

- [ ] **[NEW]** `stat-card.blade.php`
  ```blade
  @props(['title', 'value', 'icon', 'color' => 'primary'])
  <div class="card border-{{ $color }}">
    <div class="card-body">
      <div class="d-flex justify-content-between">
        <div>
          <p class="text-muted">{{ $title }}</p>
          <h3>{{ $value }}</h3>
        </div>
        <i class="fa {{ $icon }} fa-2x text-{{ $color }}"></i>
      </div>
    </div>
  </div>
  ```

- [ ] **[NEW]** `badge-status.blade.php`
  ```blade
  @props(['status'])
  @php
    $map = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
  @endphp
  <span class="badge bg-{{ $map[$status] ?? 'secondary' }}">{{ ucfirst($status) }}</span>
  ```

- [ ] **[NEW]** `alert.blade.php`
  ```blade
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  ```

### Task: QA & Finalisasi

- [ ] Test login admin → redirect ke dashboard
- [ ] Test akses tanpa login → redirect ke `/login`
- [ ] Test akses role bukan admin → 403
- [ ] Test peta publik tampil marker dengan popup
- [ ] Test filter laporan by status
- [ ] Test validasi laporan (approve + reject)
- [ ] Test tambah/edit/hapus kategori
- [ ] Test hapus lokasi dari peta admin
- [ ] Validasi CSRF pada semua form POST
- [ ] Test responsif mobile browser (Bootstrap grid)

---

## Struktur Direktori Web

```
app/Http/Controllers/
├── Auth/
│   └── LoginController.php
├── Admin/
│   ├── DashboardController.php
│   ├── ReportController.php
│   ├── ValidationController.php
│   ├── MapController.php
│   ├── CategoryController.php
│   └── UserController.php
└── PublicMapController.php

resources/
├── css/
│   └── admin.css
└── views/
    ├── layouts/
    │   ├── app.blade.php       ← Admin layout
    │   └── public.blade.php    ← Public layout
    ├── auth/
    │   └── login.blade.php
    ├── public/
    │   └── map.blade.php
    ├── admin/
    │   ├── dashboard/index.blade.php
    │   ├── reports/
    │   │   ├── index.blade.php
    │   │   └── show.blade.php
    │   ├── map/index.blade.php
    │   ├── categories/index.blade.php
    │   └── users/index.blade.php
    └── components/
        ├── stat-card.blade.php
        ├── badge-status.blade.php
        └── alert.blade.php

routes/
└── web.php    ← [MODIFY]
```

---

*Tandai setiap item dengan `[x]` saat selesai dikerjakan.*
