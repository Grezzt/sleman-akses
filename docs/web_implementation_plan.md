# Sleman Akses — Web Implementation Plan

> **Project:** Sistem Peta Aksesibilitas Fasilitas Umum Ramah Disabilitas  
> **Stack:** Laravel 12 · PHP 8.2 · MySQL · Bootstrap 5 · Leaflet.js  
> **Lokasi:** `c:\laragon\www\sleman-akses`

---

## Ringkasan Fase

| # | Fase | File Baru | Status |
|---|------|-----------|--------|
| 1 | Database & Migrations | 3 Seeder | ⬜ |
| 2 | Models & Relasi | — (modifikasi) | ⬜ |
| 3 | Repository Layer | 10 file | ⬜ |
| 4 | Service Layer | 6 file | ⬜ |
| 5 | Backend REST API — Controllers | 5 file | ⬜ |
| 6 | Web Admin — Controllers & Middleware | 8 file | ⬜ |
| 7 | Blade Views & Frontend | 14 file | ⬜ |
| 8 | Finalisasi & Konfigurasi | — | ⬜ |

---

## FASE 1 — Database & Migrations

### Task: Lengkapi Kolom Semua Migration

- [ ] `create_users_table.php` — Tambah `full_name`, `email`, `password`, `role`, `remember_token`
- [ ] `create_facility_categories_table.php` — Tambah `facility_name`, `icon_marker`
- [ ] `create_reports_table.php` — Tambah `user_id` (FK), `latitude`, `longitude`, `photo_url`, `timestamp`
- [ ] `create_report_details_table.php` — Tambah `report_id` (FK), `category_id` (FK), `availability_status`
- [ ] `create_validations_table.php` — Tambah `user_id` (FK), `report_id` (FK), `approval_status`, `evaluation_note`, `validation_date`
- [ ] `create_map_locations_table.php` — Tambah `validation_id` (FK), `latitude`, `longitude`, `publish_date`
- [ ] Publish Sanctum migration (`php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`)
- [ ] Jalankan `php artisan migrate:fresh`

### Task: Buat Seeders

- [ ] **[NEW]** `database/seeders/FacilityCategorySeeder.php` — 5 kategori: Ramp, Toilet Difabel, Parkir Difabel, Lift, Guiding Block
- [ ] **[NEW]** `database/seeders/UserSeeder.php` — Admin default: `admin@sleman-akses.id`
- [ ] **[MODIFY]** `database/seeders/DatabaseSeeder.php` — Panggil kedua seeder

---

## FASE 2 — Models & Relasi

### Task: Lengkapi Semua Model (modifikasi file yang sudah ada)

- [ ] **`User.php`** — Tambah `HasApiTokens`, fillable, hidden, relasi `hasMany(Report)` & `hasMany(Validation)`
- [ ] **`Report.php`** — Fillable, casts timestamp, relasi `belongsTo(User)`, `hasMany(ReportDetail)`, `hasOne(Validation)`
- [ ] **`ReportDetail.php`** — Fillable, cast boolean, relasi `belongsTo(Report)` & `belongsTo(FacilityCategory)`
- [ ] **`FacilityCategory.php`** — Fillable, relasi `hasMany(ReportDetail)`
- [ ] **`Validation.php`** — Fillable, casts, relasi `belongsTo(User)`, `belongsTo(Report)`, `hasOne(MapLocation)`
- [ ] **`MapLocation.php`** — Fillable, casts, relasi `belongsTo(Validation)`

---

## FASE 3 — Repository Layer

> Abstraksi semua query database. Service **tidak boleh** memanggil Eloquent secara langsung — semua melalui Repository.

### Task: Buat Interface — `app/Repositories/Contracts/`

- [ ] **[NEW]** `UserRepositoryInterface.php`
  - `findByEmail(string $email)`, `create(array $data)`, `update(User $user, array $data)`
- [ ] **[NEW]** `ReportRepositoryInterface.php`
  - `create(array $data)`, `addDetail(Report $report, int $categoryId, bool $status)`, `findWithRelations(int $id)`, `getAllPaginated(array $filters)`
- [ ] **[NEW]** `ValidationRepositoryInterface.php`
  - `initPending(Report $report)`, `update(Validation $validation, array $data)`, `findByReport(int $reportId)`
- [ ] **[NEW]** `MapLocationRepositoryInterface.php`
  - `createFromReport(Validation $validation, Report $report)`, `getAllPublished()`, `delete(int $id)`
- [ ] **[NEW]** `CategoryRepositoryInterface.php`
  - `all()`, `find(int $id)`, `create(array $data)`, `update(FacilityCategory $category, array $data)`, `delete(int $id)`

### Task: Buat Implementasi — `app/Repositories/`

- [ ] **[NEW]** `UserRepository.php` — Implementasi `UserRepositoryInterface`
- [ ] **[NEW]** `ReportRepository.php` — Implementasi `ReportRepositoryInterface`
- [ ] **[NEW]** `ValidationRepository.php` — Implementasi `ValidationRepositoryInterface`
- [ ] **[NEW]** `MapLocationRepository.php` — Implementasi `MapLocationRepositoryInterface`
- [ ] **[NEW]** `CategoryRepository.php` — Implementasi `CategoryRepositoryInterface`

### Task: Bind Interface ke Implementasi

- [ ] **[MODIFY]** `app/Providers/AppServiceProvider.php` — Daftarkan binding:

```php
public function register(): void
{
    $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
    $this->app->bind(ValidationRepositoryInterface::class, ValidationRepository::class);
    $this->app->bind(MapLocationRepositoryInterface::class, MapLocationRepository::class);
    $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
}
```

---

## FASE 4 — Service Layer

> Business logic utama sistem. Controller hanya memanggil Service, tidak mengandung logic sendiri.

### Task: Buat Services — `app/Services/`

- [ ] **[NEW]** `AuthService.php`
  - `register(array $data): array` — Buat user, return token
  - `login(array $credentials): array` — Auth attempt, buat Sanctum token, return token + user
  - `logout(User $user): void` — Revoke current token

- [ ] **[NEW]** `ReportService.php`
  - `createReport(array $data, int $userId, UploadedFile $photo): Report`
    → Panggil `PhotoUploadService`, `ReportRepository::create()`, `addDetail()`, `ValidationRepository::initPending()`
  - `getUserReports(int $userId): Collection`

- [ ] **[NEW]** `ValidationService.php`
  - `validate(Validation $validation, array $data, User $admin): Validation`
    → Update status, jika `approved` panggil `MapLocationRepository::createFromReport()`

- [ ] **[NEW]** `MapLocationService.php`
  - `getPublished(): Collection`
  - `getPublishedWithDetails(): Collection`
  - `delete(int $locationId): void`

- [ ] **[NEW]** `CategoryService.php`
  - `getAll(): Collection`, `create(array $data)`, `update(int $id, array $data)`, `delete(int $id)`

- [ ] **[NEW]** `PhotoUploadService.php`
  - `upload(UploadedFile $file, int $userId): string` — Resize 1200px via Intervention Image, simpan ke `storage/public/reports/{userId}/`, return URL
  - `delete(string $path): void`

---

## FASE 5 — Backend REST API — Controllers

> Auth: **Laravel Sanctum** | Prefix: `/api/v1/`  
> Controller **hanya memanggil Service** — tidak ada logika di dalam controller.

### Task: Setup

- [ ] `composer require laravel/sanctum`
- [ ] `composer require intervention/image-laravel`
- [ ] Buat dan daftarkan `routes/api.php` di `bootstrap/app.php`

### Task: Endpoint API

```
// Public (tanpa auth)
POST   /api/v1/auth/register         → Api\AuthController@register
POST   /api/v1/auth/login            → Api\AuthController@login
GET    /api/v1/categories            → Api\CategoryController@index
GET    /api/v1/map                   → Api\MapController@index
GET    /api/v1/map/{id}              → Api\MapController@show

// Protected (Bearer Token Sanctum)
POST   /api/v1/auth/logout           → Api\AuthController@logout
GET    /api/v1/profile               → Api\ProfileController@show
PUT    /api/v1/profile               → Api\ProfileController@update
GET    /api/v1/reports               → Api\ReportController@index
POST   /api/v1/reports               → Api\ReportController@store
GET    /api/v1/reports/{id}          → Api\ReportController@show
```

### Task: Buat Controllers API — `app/Http/Controllers/Api/`

> Setiap controller inject Service via constructor (Dependency Injection)

- [ ] **[NEW]** `AuthController.php` — Inject `AuthService` → `register`, `login`, `logout`
- [ ] **[NEW]** `ProfileController.php` — Inject `UserRepository` → `show`, `update`
- [ ] **[NEW]** `ReportController.php` — Inject `ReportService` → `index`, `store`, `show`
- [ ] **[NEW]** `CategoryController.php` — Inject `CategoryService` → `index`
- [ ] **[NEW]** `MapController.php` — Inject `MapLocationService` → `index`, `show`

### Task: Buat Form Requests — `app/Http/Requests/Api/`

- [ ] **[NEW]** `RegisterRequest.php` — `full_name|required`, `email|unique`, `password|min:8|confirmed`
- [ ] **[NEW]** `LoginRequest.php` — `email|required`, `password|required`
- [ ] **[NEW]** `StoreReportRequest.php` — `latitude|numeric`, `longitude|numeric`, `photo|file|max:5120|mimes:jpg,png`, `categories|array`
- [ ] **[NEW]** `UpdateProfileRequest.php` — semua optional/sometimes

### Task: Buat API Resources — `app/Http/Resources/`

- [ ] **[NEW]** `UserResource.php`
- [ ] **[NEW]** `ReportResource.php` — include details & validation status
- [ ] **[NEW]** `MapLocationResource.php` — include validation & categories
- [ ] **[NEW]** `CategoryResource.php`

---

## FASE 6 — Web Admin: Controllers & Middleware

### Task: Middleware

- [ ] **[NEW]** `app/Http/Middleware/CheckRole.php`
  ```php
  if (!auth()->check() || auth()->user()->role !== $role) {
      abort(403);
  }
  ```
- [ ] Daftarkan alias `role` di `bootstrap/app.php`

### Task: Update `routes/web.php`

```
GET         /                              PublicMapController@index
GET         /login                         Auth\LoginController@showForm
POST        /login                         Auth\LoginController@login
POST        /logout                        Auth\LoginController@logout

// Group: middleware ['auth', 'role:admin']
GET         /admin/dashboard               Admin\DashboardController@index
GET         /admin/reports                 Admin\ReportController@index
GET         /admin/reports/{id}            Admin\ReportController@show
POST        /admin/reports/{id}/validate   Admin\ValidationController@store
GET         /admin/map                     Admin\MapController@index
DELETE      /admin/map/{id}               Admin\MapController@destroy
GET         /admin/categories              Admin\CategoryController@index
POST        /admin/categories              Admin\CategoryController@store
PUT         /admin/categories/{id}        Admin\CategoryController@update
DELETE      /admin/categories/{id}        Admin\CategoryController@destroy
GET         /admin/users                   Admin\UserController@index
```

### Task: Buat Controllers Web

> Setiap controller inject Service via constructor (Dependency Injection)

- [ ] **[NEW]** `app/Http/Controllers/Auth/LoginController.php`
- [ ] **[NEW]** `app/Http/Controllers/PublicMapController.php` — Inject `MapLocationService`
- [ ] **[NEW]** `app/Http/Controllers/Admin/DashboardController.php` — Inject `ReportRepository`, `UserRepository`
- [ ] **[NEW]** `app/Http/Controllers/Admin/ReportController.php` — Inject `ReportRepository`
- [ ] **[NEW]** `app/Http/Controllers/Admin/ValidationController.php` — Inject `ValidationService`
- [ ] **[NEW]** `app/Http/Controllers/Admin/MapController.php` — Inject `MapLocationService`
- [ ] **[NEW]** `app/Http/Controllers/Admin/CategoryController.php` — Inject `CategoryService`
- [ ] **[NEW]** `app/Http/Controllers/Admin/UserController.php` — Inject `UserRepository`

---

## FASE 7 — Blade Views & Aset Frontend

> **Libraries (CDN):** Bootstrap 5 · Font Awesome 6 · Leaflet.js · Leaflet.MarkerCluster · DataTables · SweetAlert2 · Chart.js

### Task: Layout

- [ ] **[NEW]** `resources/views/layouts/app.blade.php` — Sidebar + topbar + `@yield('content')`
- [ ] **[NEW]** `resources/views/layouts/public.blade.php` — Navbar publik + fullscreen map
- [ ] **[NEW]** `resources/css/admin.css` — Warna sidebar, card, custom style

### Task: Views Auth

- [ ] **[NEW]** `resources/views/auth/login.blade.php` — Card login, error message

### Task: Views Publik

- [ ] **[NEW]** `resources/views/public/map.blade.php`
  - Leaflet.js fullscreen
  - MarkerCluster untuk titik-titik laporan
  - Popup: nama fasilitas, foto, status aksesibilitas
  - Filter kategori fasilitas

### Task: Views Admin

- [ ] **[NEW]** `resources/views/admin/dashboard/index.blade.php`
  - 4 stat card (total, pending, approved, rejected)
  - Grafik Chart.js laporan per bulan
  - Mini peta approved locations

- [ ] **[NEW]** `resources/views/admin/reports/index.blade.php`
  - DataTables dengan kolom: ID, Pengguna, Tanggal, Status (badge), Aksi
  - Filter dropdown by status

- [ ] **[NEW]** `resources/views/admin/reports/show.blade.php`
  - Info pengirim + timestamp + koordinat GPS
  - Foto laporan (gallery / lightbox)
  - Mini peta marker lokasi laporan
  - Tabel detail fasilitas per kategori
  - Form validasi: select approve/reject + textarea catatan
  - Konfirmasi SweetAlert2 sebelum submit

- [ ] **[NEW]** `resources/views/admin/map/index.blade.php`
  - Peta Leaflet fullscreen
  - Sidebar daftar lokasi
  - Tombol hapus + SweetAlert2 konfirmasi

- [ ] **[NEW]** `resources/views/admin/categories/index.blade.php`
  - Tabel + Bootstrap Modal form tambah/edit
  - Tombol hapus dengan konfirmasi

- [ ] **[NEW]** `resources/views/admin/users/index.blade.php`
  - Tabel: nama, email, tanggal daftar, jumlah laporan

### Task: Blade Components

- [ ] **[NEW]** `resources/views/components/stat-card.blade.php`
- [ ] **[NEW]** `resources/views/components/alert.blade.php`
- [ ] **[NEW]** `resources/views/components/badge-status.blade.php`

---

## FASE 8 — Finalisasi & Konfigurasi

### Task

- [ ] Jalankan `php artisan storage:link`
- [ ] Update `.env`: `APP_NAME="Sleman Akses"`, `FILESYSTEM_DISK=public`
- [ ] Jalankan `php artisan migrate:fresh --seed`
- [ ] Test endpoint API dengan Postman
- [ ] Test semua halaman web admin manual
- [ ] Review response error handling di semua controller

---

## Struktur Direktori Akhir

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── AuthController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── MapController.php
│   │   │   ├── ProfileController.php
│   │   │   └── ReportController.php
│   │   ├── Admin/
│   │   │   ├── CategoryController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── MapController.php
│   │   │   ├── ReportController.php
│   │   │   ├── UserController.php
│   │   │   └── ValidationController.php
│   │   ├── Auth/
│   │   │   └── LoginController.php
│   │   └── PublicMapController.php
│   ├── Middleware/
│   │   └── CheckRole.php
│   ├── Requests/
│   │   └── Api/
│   │       ├── LoginRequest.php
│   │       ├── RegisterRequest.php
│   │       ├── StoreReportRequest.php
│   │       └── UpdateProfileRequest.php
│   └── Resources/
│       ├── CategoryResource.php
│       ├── MapLocationResource.php
│       ├── ReportResource.php
│       └── UserResource.php
├── Models/
│   ├── FacilityCategory.php
│   ├── MapLocation.php
│   ├── Report.php
│   ├── ReportDetail.php
│   ├── User.php
│   └── Validation.php
├── Repositories/
│   ├── Contracts/                          ← Interface
│   │   ├── CategoryRepositoryInterface.php
│   │   ├── MapLocationRepositoryInterface.php
│   │   ├── ReportRepositoryInterface.php
│   │   ├── UserRepositoryInterface.php
│   │   └── ValidationRepositoryInterface.php
│   ├── CategoryRepository.php              ← Implementasi
│   ├── MapLocationRepository.php
│   ├── ReportRepository.php
│   ├── UserRepository.php
│   └── ValidationRepository.php
├── Services/
│   ├── AuthService.php
│   ├── CategoryService.php
│   ├── MapLocationService.php
│   ├── PhotoUploadService.php
│   ├── ReportService.php
│   └── ValidationService.php
└── Providers/
    └── AppServiceProvider.php              ← Binding interface → implementasi

resources/
├── css/
│   └── admin.css
└── views/
    ├── admin/
    │   ├── categories/index.blade.php
    │   ├── dashboard/index.blade.php
    │   ├── map/index.blade.php
    │   ├── reports/
    │   │   ├── index.blade.php
    │   │   └── show.blade.php
    │   └── users/index.blade.php
    ├── auth/
    │   └── login.blade.php
    ├── components/
    │   ├── alert.blade.php
    │   ├── badge-status.blade.php
    │   └── stat-card.blade.php
    ├── layouts/
    │   ├── app.blade.php
    │   └── public.blade.php
    └── public/
        └── map.blade.php

routes/
├── api.php          ← [NEW]
├── web.php          ← [MODIFY]
└── console.php

database/
└── seeders/
    ├── DatabaseSeeder.php         ← [MODIFY]
    ├── FacilityCategorySeeder.php ← [NEW]
    └── UserSeeder.php             ← [NEW]

docs/
├── web_implementation_plan.md    ← file ini
└── dokumen_rancangan_sistem.md
```

---

## Prinsip Alur Antar Layer

```
HTTP Request
    │
    ▼
Controller          ← Validasi input, delegasi ke Service, return response
    │
    ▼
Service             ← Business logic, orkestrasi, tidak tahu soal HTTP
    │
    ▼
Repository          ← Query database via Eloquent, implementasi interface
    │
    ▼
Model (Eloquent)    ← Mapping tabel, relasi
    │
    ▼
MySQL Database
```

*Tandai setiap item dengan `[x]` saat selesai dikerjakan.*
