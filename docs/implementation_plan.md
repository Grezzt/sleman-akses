# Sleman Akses — Web Page Plan

Rencana pengembangan halaman-halaman website Sleman Akses berdasarkan design system (design.md) dan dokumen rancangan sistem.

---

## Ringkasan Singkat

Website Sleman Akses terdiri dari **dua zona utama**:

1. **Zona Publik** — Dapat diakses siapa saja tanpa login. Berfokus pada peta interaktif sebagai fitur utama, landing page informatif, dan halaman tentang proyek.
2. **Zona Admin** — Diakses administrator yang terautentikasi. Berisi dashboard, manajemen laporan, peta kelola, kategori, dan pengguna.

---

## Zona 1: Halaman Publik

### P-01 · Landing Page (`/`)

**Tujuan:** Halaman pertama yang dikunjungi pengguna umum. Harus memberikan *first impression* yang kuat sekaligus langsung mengarahkan ke peta.

**Konten:**
- **Hero Section** — Headline besar, tagline, tombol CTA "Jelajahi Peta" dan "Download Aplikasi"
- **Statistik Cepat** — Angka dinamis: total fasilitas terpetakan, jumlah kategori, jumlah pelapor
- **Cara Kerja** — 3 langkah singkat: Unduh App → Laporkan Fasilitas → Admin Verifikasi → Tampil di Peta
- **Kategori Fasilitas** — Grid ikon kategori (Ramp, Toilet Difabel, Parkir Difabel, Lift, Guiding Block) dengan penjelasan singkat
- **Preview Peta** — Iframe/embed peta mini (teaser) dengan tombol "Buka Peta Penuh"
- **CTA Download App** — Bagian dengan mockup HP + link Google Play Store / App Store
- **Footer** — Logo, navigasi cepat, informasi hak cipta, link media sosial

**Design Notes:**
- Navbar sticky dengan solid shadow
- Hero menggunakan warna Primary (`#037940`) sebagai background dengan ilustrasi/maskot
- Statistik menggunakan card dengan secondary shadow lime (`#C7DE64`)
- Font KitRounded Bold untuk semua heading

---

### P-02 · Peta Interaktif Publik (`/map`)

**Tujuan:** Fitur utama berbasis fullscreen map yang secara visual dan UX diadaptasi dari mobile app (`home_screen.dart`), namun dioptimalkan secara proporsional untuk layar lebar (desktop/Windows).

**Layout Desktop (Web):**
- **Peta Leaflet.js Fullscreen** — Mengambil seluruh viewport (100vh, 100vw) tanpa navbar statis. Menggunakan basemap MapTiler Streets V2 (atau padanannya).
- **Floating UI Kiri Atas (Search & Filter):**
  - **Search Bar**: Input pencarian membulat (pill-shape) mengambang, menyatu dengan logo Sleman Akses di sebelah kiri. Menggunakan solid shadow yang identik dengan mobile app.
  - **Category Chips**: Di bawah search bar, terdapat barisan tombol filter kategori bergaya "Chip" (stadium border). Saat aktif, chip memiliki background Primary dan shadow Secondary.
- **Floating UI Kanan Atas (User Actions):**
  - Tombol aksi berbentuk lingkaran (circular buttons) untuk Notifikasi dan "Masuk Admin" (sebagai pengganti profil user).
- **Floating UI Kanan Bawah (FABs):**
  - **FAB Lokasi Saya**: Tombol kecil berwarna putih untuk mendapatkan GPS pengguna (`Icons.my_location`).
  - **FAB Tambah Laporan**: Tombol utama yang besar, warna Primary dengan solid shadow Secondary (`offset: 4,4`).
- **Desain Marker Kustom (Leaflet DivIcon):**
  - Mereplikasi bentuk marker Flutter: Ikon Map Pin besar berwarna Primary (`#025C30`) dengan elemen lingkaran putih (`#FFFFFF`) di bagian atas-tengah yang memuat mini ikon kategori.
  - Ditambahkan efek interaktif hover (skala membesar) saat diletakkan kursor.
- **Detail Panel (Pengganti Popup Biasa):**
  - Saat marker diklik, munculkan **Floating Side Panel** (Card Panel) di sisi kiri layar (melayang di atas peta), yang meniru layout `_buildFacilityCard` dari mobile app:
    - Foto sampul dengan rasio 16:9 (via Cloudinary/CachedImage).
    - Judul fasilitas, badge status, dan alamat lengkap.
    - Grid/Wrap *chip* fasilitas pendukung (kategori spesifik) dengan *background opacity* Primary.

**Design Notes:**
- Menghindari penggunaan kontrol default Leaflet (Zoom, Attribution) di posisi standar jika menabrak UI custom. Pindahkan zoom control ke kanan bawah (di atas FAB).
- Seluruh elemen *floating* wajib menggunakan utilitas CSS *Neobrutalism* (`box-shadow: var(--shadow-solid)` dan radius tumpul) agar serasi dengan *design system* utama.
- Interaksi mulus (smooth flyTo) saat menekan chip pencarian atau marker.

---

### P-03 · Tentang Proyek (`/about`)

**Tujuan:** Menjelaskan latar belakang, tujuan, dan tim di balik Sleman Akses.

**Konten:**
- **Hero Kecil** — Judul halaman + breadcrumb
- **Latar Belakang** — Narasi tentang pentingnya aksesibilitas untuk penyandang disabilitas di Sleman
- **Tujuan Sistem** — Poin-poin tujuan (Pemetaan Digital, Partisipasi Publik, Validasi Data, Aksesibilitas Informasi)
- **Timeline Pengembangan** — Kapan sistem mulai dikembangkan (opsional)
- **CTA** — Ajakan untuk mengunduh aplikasi atau melaporkan fasilitas

---

## Zona 2: Halaman Admin (Autentikasi)

### A-00 · Login Admin (`/login`)

**Tujuan:** Pintu masuk ke zona admin. Hanya untuk administrator.

**Konten:**
- Layout dua kolom:
  - **Kiri** — Background bergambar peta Sleman dengan overlay warna Primary (blur), quote atau tagline sistem
  - **Kanan** — Card login: Logo + Nama sistem, input Email, input Password (toggle visibility), tombol "Masuk", pesan error jika gagal
- Tidak ada link "Daftar" (admin hanya dibuat via seeder)

**Design Notes:**
- Card login menggunakan `.card-solid` dengan border + solid shadow Secondary
- Tombol "Masuk" menggunakan `.btn-primary-solid` (pill shape, warna Primary)

---

### A-01 · Dashboard Admin (`/admin/dashboard`)

**Tujuan:** Halaman ringkasan utama bagi admin setelah login.

**Konten:**
- **4 Stat Card** — Total Laporan, Laporan Pending, Laporan Disetujui, Laporan Ditolak (masing-masing dengan ikon dan angka dinamis dari DB)
- **Bar Chart (Chart.js)** — Grafik laporan masuk per bulan (6 atau 12 bulan terakhir)
- **Tabel Laporan Terbaru** — 5-10 laporan terbaru dengan kolom: Pengirim, Tanggal, Status badge, tombol Aksi
- **Mini Peta** — Leaflet.js kecil yang menampilkan titik-titik fasilitas yang sudah approved

**Design Notes:**
- Stat card menggunakan `.admin-card` dengan solid shadow
- Badge status: Pending (kuning), Approved (hijau Primary), Rejected (merah Error)

---

### A-02 · Manajemen Laporan (`/admin/reports`)

**Tujuan:** Pusat manajemen semua laporan yang masuk dari mobile app.

**Konten:**
- **Header** — Judul, filter tab (Semua / Pending / Disetujui / Ditolak) bergaya pill/tab
- **Tabel Laporan (DataTables)** — Kolom: No, Nama Pelapor, Tanggal Laporan, Kategori Fasilitas, Status (badge berwarna), Aksi (tombol Lihat Detail)
- **Pagination** — Jika data banyak

**Design Notes:**
- Tab filter menggunakan style pill dengan outline dan warna Primary saat aktif
- Tombol aksi menggunakan outline button kecil

---

### A-03 · Detail & Validasi Laporan (`/admin/reports/{id}`)

**Tujuan:** Halaman terpenting admin — meninjau laporan dan mengambil keputusan approve/reject.

**Konten:**
- **Kolom Kiri — Info Laporan:**
  - Data pengirim (nama, email, waktu)
  - Koordinat GPS
  - Mini peta Leaflet dengan marker lokasi laporan
- **Kolom Kanan — Bukti Foto:**
  - Gallery foto laporan (bisa multiple foto dari Cloudinary)
  - Klik foto → zoom/lightbox
- **Bawah — Detail Fasilitas:**
  - Tabel: Nama Kategori | Ikon | Status Ketersediaan (ada/tidak ada)
- **Form Validasi:**
  - Dropdown/Select status (Setujui / Tolak)
  - Textarea catatan evaluasi (wajib jika Tolak)
  - Tombol Submit dengan konfirmasi SweetAlert2
- **Riwayat Validasi** — Jika laporan sudah pernah diproses, tampilkan riwayatnya

---

### A-04 · Peta Kelola Fasilitas (`/admin/map`)

**Tujuan:** Admin dapat memantau dan mengelola semua lokasi yang sudah terpublikasi di peta.

**Konten:**
- **Leaflet.js Fullscreen** — Semua lokasi approved ditampilkan
- **Sidebar Kiri** — Daftar semua lokasi published (nama kota/kecamatan, tanggal, tombol Hapus)
- **Interaksi** — Klik baris di sidebar → kamera peta bergerak ke marker tersebut
- **Tombol Hapus Lokasi** — Dengan konfirmasi SweetAlert2, hapus data `map_locations` tanpa hapus laporan aslinya

---

### A-05 · Manajemen Kategori (`/admin/categories`)

**Tujuan:** CRUD kategori fasilitas umum.

**Konten:**
- **Tabel Kategori** — Kolom: No, Nama Fasilitas, Preview Ikon Marker, Aksi (Edit, Hapus)
- **Tombol Tambah** → Modal form: input nama fasilitas, upload ikon marker
- **Edit** → Modal form (data terisi)
- **Hapus** → Konfirmasi SweetAlert2

---

### A-06 · Manajemen Pengguna (`/admin/users`)

**Tujuan:** Melihat daftar pengguna terdaftar dari mobile app.

**Konten:**
- **Tabel Pengguna** — Kolom: No, Nama Lengkap, Email, Tanggal Daftar, Total Laporan, Aksi
- **Filter/Search** — Cari berdasarkan nama atau email
- (Opsional) Tombol nonaktifkan akun pengguna

---

## Ringkasan Semua Halaman

| Kode | URL | Zona | Prioritas |
|------|-----|------|-----------|
| P-01 | `/` | Publik | 🔴 Tinggi |
| P-02 | `/map` | Publik | 🔴 Tinggi |
| P-03 | `/about` | Publik | 🟡 Sedang |
| A-00 | `/login` | Admin | 🔴 Tinggi |
| A-01 | `/admin/dashboard` | Admin | 🔴 Tinggi |
| A-02 | `/admin/reports` | Admin | 🔴 Tinggi |
| A-03 | `/admin/reports/{id}` | Admin | 🔴 Tinggi |
| A-04 | `/admin/map` | Admin | 🟡 Sedang |
| A-05 | `/admin/categories` | Admin | 🟡 Sedang |
| A-06 | `/admin/users` | Admin | 🟢 Rendah |

---

## Frontend Architecture — Modular Design

Seluruh frontend dibangun di atas **Blade Views** dengan prinsip modularitas. Setiap elemen UI yang digunakan lebih dari satu kali dibuat sebagai **Blade Component** reusable. JavaScript diorganisir sebagai **ES Modules** terpisah per fitur dan di-bundle via Vite.

---

### Layer 1 — Layouts (Kerangka Halaman)

```
resources/views/layouts/
├── public.blade.php    # Zona publik: navbar publik + footer
└── app.blade.php       # Zona admin: sidebar + topbar
```

| Layout | Digunakan oleh |
|---|---|
| `public.blade.php` | P-01, P-02, P-03 |
| `app.blade.php` | A-01 s/d A-06 |

---

### Layer 2 — Blade Components

Semua komponen di `resources/views/components/`, dipanggil via `<x-nama-komponen>`.

#### Komponen Publik (`components/public/`)

| Komponen | Fungsi |
|---|---|
| `navbar.blade.php` | Navbar sticky publik (logo + tombol CTA) |
| `footer.blade.php` | Footer: navigasi + copyright |
| `stat-counter.blade.php` | Card statistik angka (reusable di landing & about) |
| `map/filter-panel.blade.php` | Panel filter kategori (floating card solid shadow) |
| `map/popup-card.blade.php` | Konten popup marker peta (foto, fasilitas, tanggal) |

*(Catatan: UI yang hanya digunakan sekali seperti Hero Section, How It Works, Category Grid, Map Preview, dan Download CTA tidak dibuat sebagai komponen, melainkan ditulis langsung di dalam `landing.blade.php`)*

#### Komponen Admin (`components/admin/`)

| Komponen | Fungsi | Status |
|---|---|---|
| `sidebar.blade.php` | Sidebar navigasi (logo + nav links) | 🆕 Baru |
| `topbar.blade.php` | Topbar (nama admin + logout) | 🆕 Baru |
| `stat-card.blade.php` | Card statistik dashboard | ✅ Ada |
| `badge-status.blade.php` | Badge status laporan (pending/approved/rejected) | ✅ Ada |
| `alert.blade.php` | Alert flash message | ✅ Ada |
| `page-header.blade.php` | Header halaman (judul + breadcrumb) | 🆕 Baru |
| `data-table.blade.php` | Wrapper tabel DataTables + toolbar filter | 🆕 Baru |
| `modal.blade.php` | Wrapper Bootstrap Modal (slot header/body/footer) | 🆕 Baru |
| `confirm-dialog.blade.php` | Trigger SweetAlert2 konfirmasi | 🆕 Baru |
| `facility-detail-row.blade.php` | Baris tabel detail fasilitas (ikon + status) | 🆕 Baru |
| `map/mini-map.blade.php` | Peta kecil readonly (detail laporan) | 🆕 Baru |
| `map/admin-map.blade.php` | Peta full admin kelola lokasi | 🆕 Baru |

#### Komponen Shared (`components/form/` & `components/ui/`)

| Komponen | Fungsi |
|---|---|
| `form/input.blade.php` | Input teks: label + error state |
| `form/textarea.blade.php` | Textarea: label + error state |
| `form/select.blade.php` | Select dropdown: label + error state |
| `form/file-upload.blade.php` | Input file dengan preview |
| `ui/btn-primary.blade.php` | Tombol primary solid (`.btn-primary-solid`) |
| `ui/btn-outline.blade.php` | Tombol outline (aksi sekunder) |
| `ui/card.blade.php` | Card solid shadow reusable (`.card-solid`) |
| `ui/empty-state.blade.php` | Tampilan kosong: ikon + pesan |
| `ui/photo-gallery.blade.php` | Gallery foto dengan lightbox |

---

### Layer 3 — Pages

Halaman merangkai komponen reusable dan berisi HTML spesifik yang tidak digunakan berulang.

```
resources/views/
├── public/
│   ├── landing.blade.php       # P-01 (Berisi Hero, Cara Kerja, Grid Kategori, Download CTA)
│   ├── map.blade.php           # P-02 ✅ Ada
│   └── about.blade.php         # P-03
├── auth/
│   └── login.blade.php         # A-00
└── admin/
    ├── dashboard/index.blade.php   # A-01
    ├── reports/index.blade.php     # A-02
    ├── reports/show.blade.php      # A-03
    ├── map/index.blade.php         # A-04
    ├── categories/index.blade.php  # A-05
    └── users/index.blade.php       # A-06
```

---

### Layer 4 — JavaScript Modules

```
resources/js/
├── app.js                       # Entry point Vite
├── bootstrap.js                 # Setup axios ✅ Ada
└── modules/
    ├── map/
    │   ├── public-map.js        # Leaflet peta publik, cluster, popup
    │   ├── admin-map.js         # Peta admin + sidebar sync
    │   ├── mini-map.js          # Peta kecil readonly
    │   └── map-filter.js        # Logic filter kategori
    ├── ui/
    │   ├── confirm.js           # Wrapper SweetAlert2
    │   ├── gallery.js           # Lightbox foto
    │   └── counter.js           # Animasi angka statistik
    └── admin/
        ├── datatables.js        # Konfigurasi DataTables (bahasa ID)
        ├── chart.js             # Bar chart Chart.js dashboard
        └── validation-form.js  # Conditional fields form validasi
```

---

### Konvensi Penggunaan CSS & Komponen

> [!IMPORTANT]
> **Strict CSS Rule**: Setiap komponen dan halaman WAJIB memprioritaskan utility class yang sudah ada di `resources/css/app.css` dan `resources/css/admin.css` (seperti `.card-solid`, `.btn-primary-solid`, `.input-solid`, warna `--sa-*`) daripada menulis ulang dengan raw Tailwind classes.

```blade
{{-- PENGGUNAAN CSS SYSTEM --}}
<div class="card-solid p-6">
    <button class="btn-primary-solid">Simpan</button>
</div>

{{-- PENGGUNAAN KOMPONEN REUSABLE --}}
<x-admin.stat-card label="Total Laporan" :value="$total" icon="📋" />

<x-form.input name="email" label="Email" type="email" />
```

---

## Open Questions

> [!IMPORTANT]
> **Apakah halaman Landing Page (P-01) dan Peta Publik (P-02) dijadikan satu halaman atau terpisah?**
> Peta bisa menjadi bagian dari hero landing page (fullscreen map di-embed) **atau** berdiri sendiri di route `/map` dengan navbar minimal. Versi terpisah lebih bersih untuk pengalaman menjelajah peta.

> [!IMPORTANT]
> **Apakah ada halaman "Detail Fasilitas Publik" yang terpisah?**
> Saat ini detail fasilitas hanya muncul di popup marker peta. Apakah perlu halaman tersendiri di `/facilities/{id}` yang bisa di-share via URL? Ini berguna untuk SEO dan link-sharing.

> [!NOTE]
> **Untuk halaman About (P-03)**, apakah ada konten khusus yang ingin ditonjolkan? Misalnya profil institusi, nama anggota tim, atau kontak.

> [!NOTE]
> **Download link aplikasi mobile** — Apakah aplikasi sudah/akan di-publish ke Google Play Store? Jika belum, tombol CTA download bisa diganti dengan QR code atau link APK langsung.
