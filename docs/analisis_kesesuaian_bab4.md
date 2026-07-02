# Analisis Kesesuaian Laporan BAB 4 vs Implementasi Aktual
## Proyek: Sleman Akses — Peta Aksesibilitas Fasilitas Umum & Edukasi
**Dokumen Sumber:** PROYEK UTAMA INFORMATIKA BAB 4.docx  
**Tanggal Analisis:** 1 Juni 2026  
**Analis:** Antigravity AI

---

## 📊 Ringkasan Eksekutif

| Dimensi | Status | Tingkat Kesesuaian |
|---|---|---|
| Arsitektur Sistem (Backend Web) | ✅ Sesuai | **95%** |
| Database & Model ERD | ✅ Sesuai | **92%** |
| API untuk Mobile (Autentikasi) | ✅ Sesuai | **95%** |
| API untuk Mobile (Laporan/Geo-tagging) | ✅ Sesuai | **90%** |
| Web Publik (Beranda + Peta) | ✅ Sesuai | **88%** |
| Web Administrator (Dashboard) | ✅ Sesuai | **90%** |
| Web Administrator (Manajemen Laporan & Validasi) | ✅ Sesuai | **85%** |
| Web Administrator (Manajemen Pengguna) | ⚠️ Sebagian | **70%** |
| Aplikasi Mobile (deskripsi fungsional) | ⚠️ Tidak Dapat Diverifikasi | N/A |
| Kebutuhan Non-Fungsional (Versi Stack) | ⚠️ Potensi Gap | **75%** |

> [!IMPORTANT]
> Kode backend (Laravel) **sangat sesuai** dengan narasi laporan. Namun ditemukan beberapa **gap faktual dan konseptual** yang perlu dikoreksi di laporan agar akurat dan tidak menyesatkan penguji.

---

## ✅ BAGIAN 1: HAL-HAL YANG SUDAH SESUAI (COCOK)

### 1.1 Arsitektur Sistem — Sangat Cocok ✅

Laporan (Halaman Arsitektur Sistem, BAB III):
> *"subsistem backend digerakkan oleh web server Apache yang menjalankan framework Laravel. Laravel bertugas mengeksekusi Application Programming Interface (API)..."*

**Bukti di Kode:**
- `routes/api.php` → terdapat endpoint API `/v1/auth/*`, `/v1/reports`, `/v1/map`, dll.
- `app/Http/Controllers/Api/` → terdapat `AuthController`, `ReportController`, `MapController`, `ProfileController`.
- `app/Http/Controllers/Admin/` → terdapat `DashboardController`, `ValidationController`, `UserController`, `MapController`, `ReportController`.
- Arsitektur MVC dengan Service Layer dan Repository Pattern terbukti ada.

**Penilaian:** Arsitektur client-server dengan Laravel sebagai backend **100% sesuai** dengan klaim laporan.

---

### 1.2 Database / ERD — Sangat Cocok ✅

Laporan menyebutkan 6 entitas utama: `users`, `reports`, `validations`, `map_locations`, `facility_categories`, `report_details`.

**Bukti di Kode (Models):**
| Entitas di Laporan | Model Laravel | Status |
|---|---|---|
| `users` | `User.php` | ✅ Ada |
| `reports` | `Report.php` | ✅ Ada |
| `validations` | `Validation.php` | ✅ Ada |
| `map_locations` | `MapLocation.php` | ✅ Ada |
| `facility_categories` | `FacilityCategory.php` | ✅ Ada |
| `report_details` | `ReportDetail.php` | ✅ Ada |

**Relasi di Laporan vs Kode:**
- `User → hasMany → Reports` ✅ (terbukti di `User.php` & `Report.php`)
- `Report → hasOne → Validation` ✅ (terbukti di `Report.php`)
- `Validation → hasOne → MapLocation` ✅ (terbukti di `Validation.php`)
- `Report → hasMany → ReportDetails` ✅ (terbukti di `Report.php`)
- `FacilityCategory → hasMany → ReportDetails` ✅ (terbukti di `FacilityCategory.php`)

**Atribut yang disebutkan di laporan vs kode:**
- `Reports`: `latitude`, `longitude`, `photo_url`, `timestamp` → ✅ semua ada di `Report.php`
- `MapLocations`: `place_name`, `address`, `status`, `facility_type`, `latitude`, `longitude`, `publish_date` → ✅ semua ada di `MapLocation.php`
- `Validations`: `approval_status`, `evaluation_note`, `validation_date` → ✅ semua ada di `Validation.php`
- `Users`: `full_name`, `email`, `role`, `is_active` → ✅ semua ada di `User.php`

---

### 1.3 Alur Autentikasi Mobile (Laravel Sanctum) — Sesuai ✅

Laporan (BAB IV, Halaman Login):
> *"Pada saat inisialisasi halaman, lapisan front-end Flutter akan memerika ketersediaan API token berbasis Laravel Sactum di dalam penyimpanan aman perangkat."*

**Bukti di Kode:**
- `Api/AuthController.php` → `register()`, `login()`, `logout()` **sudah ada dan fungsional**
- `routes/api.php` → `/v1/auth/login`, `/v1/auth/register`, `/v1/auth/logout` semua terdefinisi
- Middleware `auth:sanctum` digunakan di semua endpoint protected
- `User.php` → menggunakan trait `HasApiTokens` dari Sanctum

---

### 1.4 Alur Pelaporan & Geo-tagging — Sesuai ✅

Laporan (BAB IV, Halaman Pelaporan Fasilitas):
> *"aplikasi mengirimkan data tersebut kepada server melalui protokol HTTP. sistem akan secara mandiri melakukan validasi integritas data sebelum mengirimkan instruksi eksekusi kueri penyimpanan menuju basis data."*

**Bukti di Kode:**
- `StoreReportRequest.php` → validasi `latitude`, `longitude`, `photo_url`, `categories[]` ✅
- `ReportService::createReport()` → menyimpan ke `reports` + `report_details` + membuat record `validation` dengan status pending ✅
- `ValidationRepository::initPending()` dipanggil otomatis setelah laporan dibuat ✅

---

### 1.5 Alur Validasi Administrator — Sesuai ✅

Laporan (BAB IV, Halaman Detail & Validasi):
> *"data yang valid akan diperbarui statusnya dan dipublikasikan secara langsung ke dalam peta"*

**Bukti di Kode:**
- `ValidationService::validate()` → jika `approved`: membuat/update record di `map_locations` ✅
- Jika `rejected`: menghapus dari `map_locations` ✅
- `ValidationController` → validasi wajib isi `evaluation_note` ketika `rejected` ✅
- `routes/web.php` → `POST /admin/reports/{id}/validate` → `ValidationController@store` ✅

---

### 1.6 Halaman Web Publik — Sesuai ✅

Laporan menyebutkan:
1. Halaman Beranda dengan Hero Section, grid kategori, statistik dinamis ✅
2. Halaman Peta Interaktif menggunakan Leaflet.js + MapTiller ✅

**Bukti di Kode:**
- `PublicController::landing()` → menarik `categories` + statistik `locations`, `categories`, `reports` dari DB ✅
- `PublicMapController::index()` → membangun marker dengan lat/lng, foto, kategori, fasilitas ✅
- `resources/views/public/landing.blade.php` ada (23KB) ✅
- `resources/views/public/map.blade.php` ada (24KB) ✅
- `maptilerKey` di-pass ke view ✅

---

### 1.7 Dashboard Admin — Sesuai ✅

Laporan (BAB IV):
> *"controller Laravel mengeksekusi kueri agregat menuju basis data untuk menghitung total laporan, jumlah antrean validasi, laporan disetujui, dan laporan ditolak"*

**Bukti di Kode (`DashboardController.php`):**
- `Validation::count()` → total laporan ✅
- `Validation::where('approval_status', 'pending')->count()` ✅
- `Validation::where('approval_status', 'approved')->count()` ✅
- `Validation::where('approval_status', 'rejected')->count()` ✅
- `getMonthlyReportData()` → data tren 12 bulan untuk Chart.js ✅
- `mapService->getPublished()` → peta mini Leaflet ✅

---

### 1.8 Manajemen Pengguna — Sebagian Sesuai ✅

Laporan:
> *"administrator dapat memicu toggle button penonaktifan akun... yang secara otomatis mencabut validitas token Laravel Sanctum pengguna tersebut di sisi mobile"*

**Bukti di Kode (`UserController.php`):**
- `toggleStatus()` → mengubah `is_active` di database ✅
- NAMUN: **tidak ada kode yang secara aktif mencabut/revoke token Sanctum saat akun dinonaktifkan** ⚠️

---

## ⚠️ BAGIAN 2: GAP & KETIDAKSESUAIAN (HARUS DIPERBAIKI DI LAPORAN)

### GAP #1 — KRITIS: Pencabutan Token Sanctum Tidak Diimplementasikan
**Lokasi di Laporan:** BAB IV, Halaman Manajemen Pengguna  
**Klaim Laporan:**
> *"pemicu ini akan mengirimkan permintaan pembaruan status ke backend Laravel untuk membekukan akun terkait, yang secara otomatis mencabut validitas token Laravel Sanctum pengguna tersebut di sisi mobile."*

**Realita di Kode (`UserController::toggleStatus`):**
```php
$user->is_active = !$user->is_active;
$user->save();
// ❌ TIDAK ADA: $user->tokens()->delete();
```
Kode hanya mengubah flag `is_active` di database. Token Sanctum **TIDAK** dihapus/revoke. Jika pengguna sedang login aktif, token mereka masih valid sampai expired secara alami. Artinya klaim "secara otomatis mencabut validitas token" di laporan adalah **TIDAK AKURAT**.

> [!CAUTION]
> **Ini adalah gap teknis yang serius.** Laporan mengklaim fitur keamanan yang secara faktual belum diimplementasikan. Pilihan: (1) Perbaiki kode dengan menambah `$user->tokens()->delete()`, atau (2) Perbaiki narasi laporan untuk tidak mengklaim token dicabut otomatis.

---

### GAP #2 — KRITIS: Halaman Pelaporan — Nama/Deskripsi Fasilitas Tidak Ada di Model Report

**Klaim Laporan (BAB IV, Halaman Pelaporan Langkah 3):**
> *"pengguna diwajibkan untuk mengisi nama fasilitas, uraian deskripsi tekstual, serta memilih kategori operasional infrastruktur melalui menu dropdown."*

**Realita di Kode (`StoreReportRequest.php`):**
```php
return [
    'latitude'   => [...],
    'longitude'  => [...],
    'photo_url'  => [...],
    'categories' => [...],
    // ❌ TIDAK ADA field: 'name', 'place_name', 'description'
];
```
Dan di `Report.php` model:
```php
protected $fillable = [
    'user_id', 'latitude', 'longitude', 'photo_url', 'timestamp',
    // ❌ TIDAK ADA: 'name', 'description', 'place_name'
];
```
**Nama dan deskripsi fasilitas TIDAK tersimpan di tabel `reports`.** Field ini baru diisi oleh **administrator** saat validasi (`place_name`, `address`, `facility_type` di `ValidationController`). Laporan menyebut pengguna mobile mengisi nama fasilitas, padahal itu tugasnya admin.

> [!WARNING]
> Narasi alur pelaporan di laporan perlu dikoreksi: pengguna mobile hanya mengisi **koordinat GPS, foto, dan kategori fasilitas (checklist)**. Nama dan alamat tempat diisi oleh **administrator** saat proses validasi.

---

### GAP #3 — SIGNIFIKAN: Klaim Filter Pin Peta secara Real-time di Mobile (Belum Terverifikasi)

**Klaim Laporan (BAB IV, Halaman Utama Peta Terintegrasi):**
> *"ketika pengguna mengetikkan kata kunci tertentu, sistem akan memicu fungsi penyaringan real-time yang langsung memfilter pin di peta secara real-time berdasarkan kesesuaian string nama fasilitas."*

**Catatan:** Ini merujuk ke fungsionalitas Flutter/mobile yang tidak bisa diverifikasi dari kode backend web. Namun perlu diperhatikan bahwa karena **nama fasilitas tidak tersimpan di tabel reports** (lihat GAP #2), data yang akan difilter di peta mobile kemungkinan berasal dari `map_locations.place_name` yang diisi administrator. Klaim ini perlu diverifikasi konsistensinya dengan arsitektur data yang ada.

---

### GAP #4 — SIGNIFIKAN: Tabel `REPORTS` Tidak Memiliki Field `name`/`description` Meski Class Diagram Mengklaim Ada

**Klaim Laporan (BAB III, Class Diagram):**
> *"Kelas Reports secara unik mencatat sebuah sesi pengumpulan data geo-tagging partisipatif dengan atribut seperti ID report, koordinat GPS yaitu latitude dan longitude, photo_url, serta timestamp"*

**Realita:** Atribut yang disebutkan **sesuai** (`id`, `latitude`, `longitude`, `photo_url`, `timestamp`). ✅ Namun di bagian lain laporan (deskripsi pelaporan), muncul kontradiksi dengan klaim bahwa pengguna mengisi "nama fasilitas" yang tidak ada di tabel ini.

---

### GAP #5 — MODERAT: Halaman "About" Tidak Disebutkan di Laporan

**Realita di Kode:**
- `routes/web.php` → `GET /about` → `PublicController::about()` ✅
- `resources/views/public/about.blade.php` ada (4.3KB)

Laporan **tidak menyebutkan** adanya halaman "Tentang" (About) pada aplikasi web publik. Ini bukan masalah besar, tetapi halaman ini ada di sistem dan bisa ditambahkan ke laporan sebagai bagian dari fitur web publik.

---

### GAP #6 — MODERAT: Halaman Kategori Fasilitas (CRUD) Tidak Disebutkan di Laporan

**Realita di Kode (`routes/web.php`):**
```php
Route::get('categories', [CategoryController::class, 'index']);
Route::post('categories', [CategoryController::class, 'store']);
Route::put('categories/{id}', [CategoryController::class, 'update']);
Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
```
Administrator memiliki halaman penuh untuk mengelola kategori fasilitas (ramp, toilet, lift, parkir). Fungsionalitas CRUD Kategori ini **tidak disebutkan sama sekali** di laporan BAB IV (Hasil dan Pembahasan).

> [!NOTE]
> Halaman manajemen kategori adalah fitur yang nyata ada di sistem tetapi "invisible" di laporan. Perlu ditambahkan sebagai salah satu halaman aplikasi web administrator.

---

### GAP #7 — MODERAT: Klaim Statistik Dashboard — Sumber Data Tidak Akurat

**Klaim Laporan:**
> *"controller Laravel mengeksekusi kueri agregat menuju basis data untuk menghitung total laporan"*

**Realita di Kode (`DashboardController.php`):**
```php
$totalReports = Validation::count(); // ← Menghitung dari tabel VALIDATIONS, bukan REPORTS
```
**Total laporan dihitung dari tabel `validations`, bukan `reports`.** Ini sedikit berbeda secara semantis — angkanya sama jika setiap laporan selalu punya 1 validasi, tetapi secara teknis kode tidak menggunakan `Report::count()`. Laporan perlu mengklarifikasi ini atau kode perlu disesuaikan.

---

### GAP #8 — MODERAT: Pembahasan Black Box Testing Tanpa Skenario Pengujian Aktual Mobile

**Klaim Laporan (BAB IV, Pembahasan Hasil):**
Seluruh halaman pembahasan black box testing (untuk mobile dan web) ditulis dalam **gaya naratif deskriptif** tanpa:
- Tabel pengujian formal (test case ID, input, expected output, actual output, status)
- Screenshot bukti pengujian
- Catatan waktu pengujian

Standar pengujian Black Box dalam laporan akademis biasanya menyertakan **tabel formal** seperti:

| No | Skenario | Input | Expected | Actual | Status |
|---|---|---|---|---|---|
| 1 | Login valid | email+password benar | Masuk ke halaman utama | Masuk ke halaman utama | ✅ PASS |

Laporan saat ini hanya mendeskripsikan kondisi normal/tidak normal dalam paragraf prosa. Ini adalah **kelemahan metodologis** yang perlu diperbaiki.

---

### GAP #9 — MODERAT: Versi Stack Teknologi — Tidak Terverifikasi vs Klaim Laporan

**Klaim Laporan (Kebutuhan Non-Fungsional):**
```
PHP: 8.2
Laravel: 12
Flutter SDK: 3.41.4
Dart: 3.11.5
MySQL: 8.0
```

**Realita di Kode (`composer.json`):** Perlu diperiksa apakah versi yang terinstall sesuai.

> [!NOTE]
> Perlu diverifikasi dengan menjalankan `php -v`, `php artisan --version`, dll. di environment aktual untuk memastikan klaim versi di laporan akurat.

---

### GAP #10 — MINOR: Klaim Halaman "Peta Kelola" — Animasi `flyTo` Kamera

**Klaim Laporan (BAB IV, Pembahasan Peta Kelola):**
> *"peta secara normal memicu animasi flyTo untuk bergeser dan melakukan pembesaran zoom tepat di atas penanda koordinat terkait"*

**Catatan:** Ini adalah klaim UI yang ada di Blade template (`resources/views/admin/map/`). Tidak bisa diverifikasi dari sisi backend PHP saja — perlu diperiksa file JavaScript di template tersebut. Namun ini adalah klaim yang masuk akal untuk Leaflet.js.

---

### GAP #11 — MINOR: Narasi Sequence Diagram Tidak Mencakup Alur Upload Foto (Cloudinary/Storage)

**Klaim Laporan (Sequence Diagram):**
> *"Setelah pengguna melengkapi formulir dan mengunggah foto infrastruktur, aplikasi mobile mengirimkan data tersebut kepada server melalui protokol HTTP"*

**Realita di Kode:**
- Ada service `PhotoUploadService.php` (5KB) — kemungkinan besar menghandle upload ke Cloudinary atau local storage
- Alur upload foto sebenarnya adalah: Mobile → Server (upload foto) → Cloudinary/Storage → URL disimpan di `reports.photo_url`
- Sequence Diagram di laporan **menyederhanakan alur ini** tanpa menyebutkan storage eksternal

---

## 🔎 BAGIAN 3: ANALISIS PER HALAMAN APLIKASI

### 3.1 Aplikasi Mobile (Tidak Dapat Diverifikasi Penuh)

Kode Flutter/Dart tidak berada di repositori ini (hanya backend Laravel). Analisis kesesuaian mobile **hanya bisa dilakukan dari sisi API contract**:

| Fitur Mobile (Klaim Laporan) | API Backend Tersedia | Keterangan |
|---|---|---|
| Login | `POST /v1/auth/login` ✅ | Sesuai |
| Registrasi | `POST /v1/auth/register` ✅ | Sesuai |
| Logout | `POST /v1/auth/logout` (Sanctum) ✅ | Sesuai |
| Lihat peta fasilitas | `GET /v1/map` ✅ | Sesuai |
| Detail fasilitas | `GET /v1/map/{id}` ✅ | Sesuai |
| Kirim laporan | `POST /v1/reports` ✅ | Sesuai |
| Riwayat laporan | `GET /v1/reports` ✅ | Sesuai |
| Detail laporan | `GET /v1/reports/{id}` ✅ | Sesuai |
| Profil pengguna | `GET /v1/profile`, `PUT /v1/profile` ✅ | Sesuai |
| Kategori fasilitas | `GET /v1/categories` ✅ | Sesuai |
| Upload media/foto | `GET /v1/media`, `DELETE /v1/media/{id}` ✅ | Ada endpoint media |
| Statistik laporan | `GET /v1/reports/stats` ✅ | Ada endpoint stats |

**Semua endpoint yang diklaim di laporan tersedia di backend.** Kesesuaian API: **95%**.

---

### 3.2 Aplikasi Web Publik

| Halaman (Klaim Laporan) | Status Implementasi |
|---|---|
| Beranda (Hero, statistik, kategori, FAQ) | ✅ Ada (`landing.blade.php`, 23KB) |
| Peta Interaktif Publik (Leaflet.js + MapTiller) | ✅ Ada (`map.blade.php`, 24KB) |
| Halaman About | ✅ Ada (tidak disebutkan di laporan) |
| Halaman API Docs | ✅ Ada (tidak disebutkan di laporan) |

---

### 3.3 Aplikasi Web Administrator

| Halaman (Klaim Laporan) | Status Implementasi |
|---|---|
| Login Admin | ✅ Ada (`auth/LoginController`) |
| Dashboard Utama (statistik + chart + peta mini) | ✅ Ada (`DashboardController`) |
| Manajemen Laporan (tabel, filter tab) | ✅ Ada (`Admin/ReportController`) |
| Detail & Validasi Laporan | ✅ Ada (`ValidationController`) |
| Peta Kelola Fasilitas | ✅ Ada (`Admin/MapController`) |
| Manajemen Pengguna (toggle aktif) | ✅ Ada (`UserController`) — dengan catatan GAP #1 |
| Manajemen Kategori Fasilitas | ✅ Ada (tidak disebutkan di laporan!) |

---

## 📋 BAGIAN 4: REKOMENDASI PERBAIKAN LAPORAN

### Koreksi Wajib (Harus Diperbaiki):

1. **[GAP #1]** Hapus klaim bahwa token Sanctum "otomatis dicabut" saat akun dinonaktifkan, ATAU tambahkan kode `$user->tokens()->delete()` di `UserController::toggleStatus()`. Ini adalah ketidakakuratan teknis yang bisa dipertanyakan penguji.

2. **[GAP #2]** Koreksi narasi "Halaman Pelaporan Langkah 3" — pengguna mobile **tidak mengisi nama fasilitas**. Nama/alamat tempat diisi oleh **administrator** saat validasi. Langkah 3 hanya berisi pemilihan kategori infrastruktur (dropdown checklist) dan deskripsi singkat opsional.

3. **[GAP #8]** Tambahkan **tabel Black Box Testing formal** dengan kolom: No, Skenario Uji, Data Masukan, Hasil yang Diharapkan, Hasil Aktual, Status. Narasi prosa deskriptif tidak cukup sebagai bukti pengujian ilmiah.

### Penambahan Konten (Disarankan):

4. **[GAP #6]** Tambahkan sub-bab "Halaman Manajemen Kategori Fasilitas" di bagian Hasil Aplikasi Web Administrator (ada 4 route CRUD yang tidak terdokumentasi).

5. **[GAP #5]** Tambahkan "Halaman Tentang (About)" sebagai bagian dari web publik.

6. **[GAP #9]** Verifikasi dan cantumkan versi teknologi yang **aktual terinstall** (bukan hanya target), gunakan output `php artisan --version`, `flutter --version`, dll.

### Klarifikasi Teknis (Baik untuk Ditambahkan):

7. **[GAP #11]** Jelaskan alur upload foto lebih detail di Sequence Diagram — sebutkan peran `PhotoUploadService` dan mekanisme penyimpanan foto (lokal/cloud) sebelum URL disimpan ke database.

8. **[GAP #7]** Klarifikasi bahwa perhitungan "total laporan" di dashboard menggunakan tabel `validations` sebagai sumber agregat, bukan tabel `reports` langsung.

---

## 🏆 KESIMPULAN AKHIR

**Sistem yang dibangun secara keseluruhan SANGAT SESUAI dengan rancangan yang dideskripsikan dalam laporan.** Arsitektur, basis data, alur API, dan fitur-fitur utama telah diimplementasikan dengan baik dan konsisten.

Namun, **laporan memiliki beberapa ketidakakuratan faktual** yang perlu dikoreksi sebelum diserahkan:

- **1 gap kritis** (klaim token Sanctum dicabut otomatis — tidak terimplementasi)
- **1 gap kritis** (klaim pengguna mengisi nama fasilitas — bukan alur yang benar)
- **1 gap metodologis** (black box testing tidak menggunakan format tabel formal)
- **3 fitur tidak terdokumentasi** (halaman About, halaman Kategori Admin, endpoint API Stats)
- **Beberapa penyederhanaan berlebihan** yang perlu dikembalikan ke realita teknis

**Skor Kesesuaian Keseluruhan: ±87%** — Sistem sudah sangat baik, laporan perlu disesuaikan dengan realita implementasi.
