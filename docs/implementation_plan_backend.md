# Sleman Akses — Backend Implementation Plan

> **Platform:** Laravel 12 (PHP 8.2) + MySQL
> **Scope:** Foundation bersama (Database, Models, Repository, Service) + REST API untuk Mobile
> **Lokasi:** `c:\laragon\www\sleman-akses`

---

## Ringkasan Fase

| #   | Fase                            | Output Utama                 | Status |
| --- | ------------------------------- | ---------------------------- | ------ |
| B1  | Fondasi: Database & Migration   | Migration + Seeder           | X      |
| B2  | Models & Relasi Eloquent        | 6 Model                      | x      |
| B3  | Repository Layer                | 5 Interface + 5 Implementasi | x      |
| B4  | Service Layer                   | 6 Service                    | x      |
| B5  | REST API — Auth & Profil        | 2 Controller + Form Request  | x      |
| B6  | REST API — Laporan & Peta       | 3 Controller + Resources     | x      |
| B7  | Middleware, Config & Finalisasi | Sanctum, Rate Limit, CORS    | x      |

---

## FASE B1 — Fondasi: Database & Migration

> Semua struktur tabel dibuat dari migration Laravel. Jalankan `migrate:fresh` hanya di environment development.

### Task: Lengkapi Kolom Migration

- [ ] **`create_users_table.php`** — Pastikan kolom: `full_name`, `email` (UNIQUE), `password`, `role` ENUM('user','admin') DEFAULT 'user', `remember_token`
- [ ] **`create_facility_categories_table.php`** — Kolom: `facility_name`, `icon_marker`
- [ ] **`create_reports_table.php`** — Kolom: `user_id` FK, `latitude` DECIMAL(10,7), `longitude` DECIMAL(10,7), `photo_url`, `timestamp` DEFAULT CURRENT_TIMESTAMP
- [ ] **`create_report_details_table.php`** — Kolom: `report_id` FK, `category_id` FK, `availability_status` BOOLEAN
- [ ] **`create_validations_table.php`** — Kolom: `user_id` FK, `report_id` FK, `approval_status` ENUM('pending','approved','rejected') DEFAULT 'pending', `evaluation_note` TEXT NULLABLE, `validation_date` TIMESTAMP NULLABLE
- [ ] **`create_map_locations_table.php`** — Kolom: `validation_id` FK, `latitude` DECIMAL(10,7), `longitude` DECIMAL(10,7), `publish_date` TIMESTAMP NULLABLE
- [ ] Publish Sanctum migration:
    ```bash
    php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
    ```
- [ ] Jalankan migrasi:
    ```bash
    php artisan migrate:fresh
    ```

### Task: Buat Seeders

- [ ] **[NEW]** `database/seeders/FacilityCategorySeeder.php`

    ```php
    // 5 kategori default:
    // 1. Ramp / Jalur Khusus     → ramp.png
    // 2. Toilet Difabel           → toilet.png
    // 3. Parkir Difabel           → parking.png
    // 4. Lift Aksesibel           → elevator.png
    ```

- [ ] **[NEW]** `database/seeders/UserSeeder.php`

    ```php
    // Admin default:
    // email    : admin@sleman-akses.id
    // password : password (bcrypt)
    // role     : admin
    ```

- [ ] **[MODIFY]** `database/seeders/DatabaseSeeder.php` — Panggil `FacilityCategorySeeder` dan `UserSeeder`

- [ ] Jalankan:
    ```bash
    php artisan migrate:fresh --seed
    ```

---

## FASE B2 — Models & Relasi Eloquent

> Model hanya berisi definisi kolom, fillable, casts, dan relasi Eloquent. Tidak ada business logic.

### Task: Lengkapi Semua Model — `app/Models/`

- [ ] **`User.php`**
    - Trait: `HasApiTokens`, `HasFactory`, `Notifiable`
    - `$fillable`: `full_name`, `email`, `password`, `role`
    - `$hidden`: `password`, `remember_token`
    - `$casts`: `password → hashed`
    - Relasi: `hasMany(Report::class)`, `hasMany(Validation::class)`

- [ ] **`FacilityCategory.php`**
    - `$fillable`: `facility_name`, `icon_marker`
    - Relasi: `hasMany(ReportDetail::class)`

- [ ] **`Report.php`**
    - `$fillable`: `user_id`, `latitude`, `longitude`, `photo_url`, `timestamp`
    - `$casts`: `latitude → float`, `longitude → float`, `timestamp → datetime`
    - Relasi: `belongsTo(User::class)`, `hasMany(ReportDetail::class)`, `hasOne(Validation::class)`

- [ ] **`ReportDetail.php`**
    - `$fillable`: `report_id`, `category_id`, `availability_status`
    - `$casts`: `availability_status → boolean`
    - Relasi: `belongsTo(Report::class)`, `belongsTo(FacilityCategory::class, 'category_id')`

- [ ] **`Validation.php`**
    - `$fillable`: `user_id`, `report_id`, `approval_status`, `evaluation_note`, `validation_date`
    - `$casts`: `validation_date → datetime`
    - Relasi: `belongsTo(User::class)`, `belongsTo(Report::class)`, `hasOne(MapLocation::class)`

- [ ] **`MapLocation.php`**
    - `$fillable`: `validation_id`, `latitude`, `longitude`, `publish_date`
    - `$casts`: `latitude → float`, `longitude → float`, `publish_date → datetime`
    - Relasi: `belongsTo(Validation::class)`

---

## FASE B3 — Repository Layer

> Satu-satunya layer yang berinteraksi dengan Eloquent. Service **tidak boleh** query database langsung.

### Task: Buat Interface — `app/Repositories/Contracts/`

- [ ] **[NEW]** `UserRepositoryInterface.php`

    ```php
    public function findByEmail(string $email): ?User;
    public function findById(int $id): ?User;
    public function create(array $data): User;
    public function update(User $user, array $data): User;
    ```

- [ ] **[NEW]** `ReportRepositoryInterface.php`

    ```php
    public function create(array $data): Report;
    public function addDetail(Report $report, int $categoryId, bool $status): ReportDetail;
    public function findWithRelations(int $id): ?Report;
    public function getByUserPaginated(int $userId, int $perPage = 10): LengthAwarePaginator;
    public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    ```

- [ ] **[NEW]** `ValidationRepositoryInterface.php`

    ```php
    public function initPending(Report $report): Validation;
    public function update(Validation $validation, array $data): Validation;
    public function findByReport(int $reportId): ?Validation;
    public function getCountByStatus(): array; // ['pending' => n, 'approved' => n, 'rejected' => n]
    ```

- [ ] **[NEW]** `MapLocationRepositoryInterface.php`

    ```php
    public function createFromReport(Validation $validation, Report $report): MapLocation;
    public function getAllPublished(): Collection;
    public function findWithDetails(int $id): ?MapLocation;
    public function delete(int $id): bool;
    ```

- [ ] **[NEW]** `CategoryRepositoryInterface.php`
    ```php
    public function all(): Collection;
    public function find(int $id): ?FacilityCategory;
    public function create(array $data): FacilityCategory;
    public function update(FacilityCategory $category, array $data): FacilityCategory;
    public function delete(int $id): bool;
    ```

### Task: Buat Implementasi — `app/Repositories/`

- [ ] **[NEW]** `UserRepository.php` — Implementasi `UserRepositoryInterface`
- [ ] **[NEW]** `ReportRepository.php` — Implementasi `ReportRepositoryInterface`
    - `getAllPaginated()` harus support filter `status` (join validations)
- [ ] **[NEW]** `ValidationRepository.php` — Implementasi `ValidationRepositoryInterface`
- [ ] **[NEW]** `MapLocationRepository.php` — Implementasi `MapLocationRepositoryInterface`
    - `getAllPublished()` eager load: `validation.report.reportDetails.category`
- [ ] **[NEW]** `CategoryRepository.php` — Implementasi `CategoryRepositoryInterface`

### Task: Bind Interface ke Implementasi

- [ ] **[MODIFY]** `app/Providers/AppServiceProvider.php`

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

## FASE B4 — Service Layer

> Business logic utama. Controller API dan Web hanya memanggil Service — tidak ada logika di controller.

### Task: Buat Services — `app/Services/`

- [ ] **[NEW]** `AuthService.php`

    ```
    register(array $data): array
    ├── Hash password
    ├── UserRepository::create($data)
    └── Buat Sanctum token → return ['token' => ..., 'user' => ...]

    login(array $credentials): array
    ├── Auth::attempt($credentials)
    ├── Cek user exists & role valid
    ├── Buat Sanctum token
    └── return ['token' => ..., 'user' => ...]

    logout(User $user): void
    └── $user->currentAccessToken()->delete()
    ```

- [ ] **[NEW]** `ReportService.php`

    ```
    createReport(array $data, int $userId, UploadedFile $photo): Report
    ├── PhotoUploadService::upload($photo, $userId) → $photoUrl
    ├── ReportRepository::create([..., 'photo_url' => $photoUrl])
    ├── foreach $data['categories'] as $item:
    │     ReportRepository::addDetail($report, $item['id'], $item['available'])
    └── ValidationRepository::initPending($report)

    getUserReports(int $userId, int $perPage): LengthAwarePaginator
    └── ReportRepository::getByUserPaginated($userId, $perPage)

    getReportDetail(int $reportId, int $userId): Report
    └── ReportRepository::findWithRelations($reportId) + policy check
    ```

- [ ] **[NEW]** `ValidationService.php`

    ```
    validate(Validation $validation, array $data, User $admin): Validation
    ├── ValidationRepository::update($validation, [
    │     'approval_status' => $data['status'],
    │     'evaluation_note' => $data['note'],
    │     'user_id'         => $admin->id,
    │     'validation_date' => now()
    │   ])
    └── if $data['status'] === 'approved':
          MapLocationRepository::createFromReport($validation, $validation->report)
    ```

- [ ] **[NEW]** `MapLocationService.php`

    ```
    getPublished(): Collection
    └── MapLocationRepository::getAllPublished()

    getDetail(int $id): MapLocation
    └── MapLocationRepository::findWithDetails($id)

    delete(int $locationId): void
    └── MapLocationRepository::delete($locationId)
    ```

- [ ] **[NEW]** `CategoryService.php`

    ```
    getAll(): Collection   → CategoryRepository::all()
    create(array $data)    → CategoryRepository::create($data)
    update(int $id, array) → CategoryRepository::find($id) → update()
    delete(int $id)        → CategoryRepository::delete($id)
    ```

- [ ] **[NEW]** `PhotoUploadService.php`

    ```
    upload(UploadedFile $file, int $userId): string
    ├── Resize ke max 1200px lebar via Intervention Image
    ├── Simpan ke storage/public/reports/{userId}/{uuid}.jpg
    └── return Storage::url($path)

    delete(string $path): void
    └── Storage::delete($path)
    ```

---

## FASE B5 — REST API: Auth & Profil

> Auth menggunakan **Laravel Sanctum** (Bearer Token).
> Prefix route: `/api/v1/`

### Task: Install Dependencies

- [ ] `composer require laravel/sanctum`
- [ ] `composer require intervention/image-laravel`
- [ ] Publish konfigurasi Sanctum & tambahkan `HasApiTokens` ke `User`

### Task: Setup `routes/api.php`

```php
// Public
Route::prefix('v1')->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login',    [AuthController::class, 'login']);
    Route::get('categories',     [CategoryController::class, 'index']);
    Route::get('map',            [MapController::class, 'index']);
    Route::get('map/{id}',       [MapController::class, 'show']);
});

// Protected
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('profile',      [ProfileController::class, 'show']);
    Route::put('profile',      [ProfileController::class, 'update']);
    Route::get('reports',      [ReportController::class, 'index']);
    Route::post('reports',     [ReportController::class, 'store']);
    Route::get('reports/{id}', [ReportController::class, 'show']);
});
```

- [ ] Daftarkan `routes/api.php` di `bootstrap/app.php`

### Task: Form Requests — `app/Http/Requests/Api/`

- [ ] **[NEW]** `RegisterRequest.php`

    ```
    full_name  : required|string|max:255
    email      : required|email|unique:users,email
    password   : required|string|min:8|confirmed
    ```

- [ ] **[NEW]** `LoginRequest.php`

    ```
    email    : required|email
    password : required|string
    ```

- [ ] **[NEW]** `UpdateProfileRequest.php`
    ```
    full_name : sometimes|string|max:255
    email     : sometimes|email|unique:users,email,{userId}
    password  : sometimes|string|min:8|confirmed
    ```

### Task: Controllers — `app/Http/Controllers/Api/`

- [ ] **[NEW]** `AuthController.php`

    ```php
    // Inject: AuthService
    public function register(RegisterRequest $request): JsonResponse
    public function login(LoginRequest $request): JsonResponse
    public function logout(Request $request): JsonResponse
    ```

- [ ] **[NEW]** `ProfileController.php`
    ```php
    // Inject: UserRepository
    public function show(Request $request): JsonResponse
    public function update(UpdateProfileRequest $request): JsonResponse
    ```

### Task: Format Response Auth

```json
// POST /api/v1/auth/register — 201 Created
{
  "success": true,
  "message": "Registrasi berhasil.",
  "data": {
    "token": "1|abc123...",
    "user": { "user_id": 1, "full_name": "Budi", "email": "...", "role": "user" }
  }
}

// POST /api/v1/auth/login — 200 OK
{
  "success": true,
  "message": "Login berhasil.",
  "data": { "token": "...", "user": { ... } }
}

// POST /api/v1/auth/login — 401 Unauthorized
{
  "success": false,
  "message": "Email atau password salah.",
  "errors": null
}
```

---

## FASE B6 — REST API: Laporan & Peta

### Task: Form Requests — `app/Http/Requests/Api/`

- [ ] **[NEW]** `StoreReportRequest.php`
    ```
    latitude     : required|numeric|between:-90,90
    longitude    : required|numeric|between:-180,180
    photo        : required|file|mimes:jpg,jpeg,png|max:5120
    categories   : required|array|min:1
    categories.*.id        : required|integer|exists:facility_categories,category_id
    categories.*.available : required|boolean
    ```

### Task: API Resources — `app/Http/Resources/`

- [ ] **[NEW]** `UserResource.php`

    ```json
    { "user_id", "full_name", "email", "role", "created_at" }
    ```

- [ ] **[NEW]** `ReportResource.php`

    ```json
    {
      "report_id", "latitude", "longitude", "photo_url", "timestamp",
      "validation_status": "pending|approved|rejected",
      "details": [{ "category", "icon_marker", "availability_status" }]
    }
    ```

- [ ] **[NEW]** `MapLocationResource.php`

    ```json
    {
      "location_id", "latitude", "longitude", "publish_date",
      "photo_url", "reported_by",
      "facilities": [{ "category", "icon_marker", "available" }]
    }
    ```

- [ ] **[NEW]** `CategoryResource.php`
    ```json
    { "category_id", "facility_name", "icon_marker" }
    ```

### Task: Controllers — `app/Http/Controllers/Api/`

- [ ] **[NEW]** `ReportController.php`

    ```php
    // Inject: ReportService
    public function index(Request $request): JsonResponse     // laporan milik user login
    public function store(StoreReportRequest $request): JsonResponse
    public function show(int $id, Request $request): JsonResponse
    ```

- [ ] **[NEW]** `CategoryController.php`

    ```php
    // Inject: CategoryService
    public function index(): JsonResponse  // semua kategori (public)
    ```

- [ ] **[NEW]** `MapController.php`
    ```php
    // Inject: MapLocationService
    public function index(): JsonResponse   // semua lokasi approved (public)
    public function show(int $id): JsonResponse
    ```

### Task: Format Response Laporan

```json
// GET /api/v1/reports — 200 OK (dengan pagination)
{
  "success": true,
  "message": "Berhasil mengambil daftar laporan.",
  "data": {
    "reports": [ { ...ReportResource } ],
    "pagination": { "current_page": 1, "last_page": 3, "per_page": 10, "total": 25 }
  }
}

// POST /api/v1/reports — 201 Created
{
  "success": true,
  "message": "Laporan berhasil dikirim.",
  "data": { ...ReportResource }
}
```

---

## FASE B7 — Middleware, Config & Finalisasi

### Task: Middleware & Config

- [ ] Buat `app/Http/Middleware/CheckRole.php`
    ```php
    // Digunakan untuk route web admin
    if (!auth()->check() || auth()->user()->role !== $role) {
        abort(403, 'Akses ditolak.');
    }
    ```
- [ ] Daftarkan alias `role` di `bootstrap/app.php`

- [ ] Konfigurasi CORS di `config/cors.php`:

    ```
    allowed_origins: ['*']   // sesuaikan saat production
    allowed_methods: ['*']
    allowed_headers: ['*']
    ```

- [ ] Konfigurasi Rate Limiting di `bootstrap/app.php`:

    ```php
    RateLimiter::for('api', fn($req) => Limit::perMinute(60)->by($req->user()?->id ?: $req->ip()));
    ```

- [ ] Update `.env`:
    ```env
    APP_NAME="Sleman Akses"
    APP_TIMEZONE=Asia/Jakarta
    FILESYSTEM_DISK=public
    SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
    ```

### Task: Error Handling Global

- [ ] Update `bootstrap/app.php` — tambahkan exception handler untuk `JsonResponse` saat request `Accept: application/json`:
    ```php
    // 404: ModelNotFoundException → { success: false, message: "Data tidak ditemukan." }
    // 422: ValidationException   → { success: false, message: "...", errors: {...} }
    // 401: Unauthenticated       → { success: false, message: "Autentikasi diperlukan." }
    // 403: AuthorizationException → { success: false, message: "Akses ditolak." }
    ```

### Task: Finalisasi & Testing

- [ ] `php artisan storage:link`
- [ ] `php artisan migrate:fresh --seed`
- [ ] Test semua endpoint API menggunakan Postman:
    - [ ] Auth: register, login, logout
    - [ ] Profile: show, update
    - [ ] Reports: index, store (dengan foto), show
    - [ ] Map: index, show
    - [ ] Categories: index
- [ ] Validasi response format konsisten di semua endpoint
- [ ] Test error case: unauthorized, not found, validation error

---

## Struktur Direktori Backend

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── ProfileController.php
│   │       ├── ReportController.php
│   │       ├── CategoryController.php
│   │       └── MapController.php
│   ├── Middleware/
│   │   └── CheckRole.php
│   ├── Requests/
│   │   └── Api/
│   │       ├── RegisterRequest.php
│   │       ├── LoginRequest.php
│   │       ├── StoreReportRequest.php
│   │       └── UpdateProfileRequest.php
│   └── Resources/
│       ├── UserResource.php
│       ├── ReportResource.php
│       ├── MapLocationResource.php
│       └── CategoryResource.php
├── Models/
│   ├── User.php
│   ├── FacilityCategory.php
│   ├── Report.php
│   ├── ReportDetail.php
│   ├── Validation.php
│   └── MapLocation.php
├── Repositories/
│   ├── Contracts/
│   │   ├── UserRepositoryInterface.php
│   │   ├── ReportRepositoryInterface.php
│   │   ├── ValidationRepositoryInterface.php
│   │   ├── MapLocationRepositoryInterface.php
│   │   └── CategoryRepositoryInterface.php
│   ├── UserRepository.php
│   ├── ReportRepository.php
│   ├── ValidationRepository.php
│   ├── MapLocationRepository.php
│   └── CategoryRepository.php
├── Services/
│   ├── AuthService.php
│   ├── ReportService.php
│   ├── ValidationService.php
│   ├── MapLocationService.php
│   ├── CategoryService.php
│   └── PhotoUploadService.php
└── Providers/
    └── AppServiceProvider.php

routes/
├── api.php    ← [NEW]
└── web.php    ← [MODIFY — untuk web client]

database/
├── migrations/  ← semua migration
└── seeders/
    ├── DatabaseSeeder.php
    ├── FacilityCategorySeeder.php  ← [NEW]
    └── UserSeeder.php              ← [NEW]
```

---

## Alur Data Backend (Ringkasan)

```
HTTP Request (Mobile / Postman)
    │
    ▼
routes/api.php
    │
    ▼
Controller (Api/)       ← Validasi FormRequest, delegasi ke Service
    │
    ▼
Service (Services/)     ← Business logic, orkestrasi Repository
    │
    ▼
Repository (Repositories/)  ← Query Eloquent, satu-satunya akses DB
    │
    ▼
Model (Models/)         ← Mapping tabel, relasi
    │
    ▼
MySQL Database
```

_Tandai setiap item dengan `[x]` saat selesai dikerjakan._
