# 📚 LESA App — Dokumentasi Desain UI/UX Lengkap

> **Sumber Referensi:** [https://www.lesa.app](https://www.lesa.app)  
> **Tanggal Analisis:** 17 Mei 2026  
> **Konteks:** LESA adalah aplikasi game membaca interaktif untuk anak-anak usia sekolah dasar kelas 1–4.

---

## 📋 Daftar Isi

1. [Gambaran Umum](#gambaran-umum)
2. [Sistem Warna](#sistem-warna)
3. [Tipografi & Font](#tipografi--font)
4. [Bentuk & Komponen UI](#bentuk--komponen-ui)
5. [Layout & Struktur Halaman](#layout--struktur-halaman)
6. [Animasi & Interaksi](#animasi--interaksi)
7. [Ilustrasi & Aset Visual](#ilustrasi--aset-visual)
8. [Komponen Navigasi](#komponen-navigasi)
9. [Prinsip Desain Utama](#prinsip-desain-utama)

---

## 1. Gambaran Umum

LESA (_Leseapp_ / Reading App) adalah platform edukasi berbasis game untuk anak-anak. Desainnya dirancang dengan filosofi **"Playful & Joyful"** — setiap elemen visual menyampaikan kesenangan, energi, dan semangat belajar.

### Karakteristik Desain
| Aspek | Deskripsi |
|-------|-----------|
| **Gaya** | Playful, Cartoonish, Child-Friendly |
| **Mood** | Ceria, Bersemangat, Inklusif |
| **Target Pengguna** | Anak usia 6–10 tahun + orang tua/guru |
| **Platform Utama** | Mobile App (iOS/Android) + Landing Page Web |
| **Bahasa Visual** | Ilustrasi flat vector, karakter hewan animasi |

---

## 2. Sistem Warna

### 2.1 Palet Warna Utama

| Peran | Nama Warna | Hex | Penggunaan |
|-------|-----------|-----|------------|
| **Primary** | Vivid Yellow | `#F5C800` / `#FFD600` | Hero section background, aksen energi |
| **Secondary** | Royal Purple/Blue | `#4B4BC8` / `#5252D4` | Section background, sidebar, CTA area |
| **Accent 1** | Hot Pink | `#E91E8C` / `#FF1493` | Highlight teks, ikon aktif |
| **Accent 2** | Lime Green | `#4CAF50` / `#00C853` | Elemen alam, tombol sukses |
| **Accent 3** | Sky Blue | `#29B6F6` | Detail ilustrasi, badge |
| **Neutral Light** | Off White | `#F5F0EB` / `#FAF7F2` | Section background netral |
| **Neutral Dark** | Near Black | `#1A1A2E` / `#0D0D1A` | Teks body, tombol dark |
| **White** | Pure White | `#FFFFFF` | Teks pada background gelap, kartu |

### 2.2 Penggunaan Warna per Section

```
Hero Section       → Background: #FFD600 (Kuning Vivid)
Goal Section       → Background: #5252D4 (Ungu/Biru Royal)
What is LESA       → Background: #F5F0EB (Off White)
Feature Sections   → Background: #F5F0EB (alternating dengan ilustrasi berwarna)
Waitlist Counter   → Background: #5252D4 (Ungu)
Footer/CTA         → Background: #4B4BC8 (Ungu Tua)
Announcement Bar   → Background: #FFD600 (Kuning)
```

### 2.3 Kombinasi Warna yang Digunakan

- **Kuning + Hitam** → Announcement bar (high contrast, mudah dibaca)
- **Ungu + Putih** → Section teks utama (kontras tinggi)
- **Off-White + Hitam** → Section konten (clean, readable)
- **Pink + Hitam** → Keyword highlight ("statistics", "Fun")
- **Amber/Orange + Hitam** → Keyword highlight ("Fun")

---

## 3. Tipografi & Font

### 3.1 Font yang Digunakan

LESA menggunakan pendekatan tipografi **Bold & Rounded** yang memancarkan energi dan kegembiraan.

#### Font Utama: **Nunito** atau serupa (Rounded Sans-Serif)
```css
font-family: 'Nunito', 'Poppins', sans-serif;
```

| Elemen | Weight | Size (approx) | Style |
|--------|--------|---------------|-------|
| Brand Logo "LESA" | ExtraBold 900 | 72–96px | Vertikal, rounded |
| H1 Heading (Hero) | Bold 700 | 48–56px | Center, White |
| H2 Section Title | Bold 700–800 | 36–48px | Mixed color highlight |
| Body Text | Regular 400 | 16–20px | Dark on light BG |
| Subtitle/Subtext | Regular 400 | 14–18px | Gray/muted |
| Button Text | Bold 700 | 16–18px | Uppercase/Title Case |
| Announcement Bar | SemiBold 600 | 14px | Marquee scrolling |

### 3.2 Karakteristik Tipografi

- **Letter-spacing:** Normal hingga sedikit lebar pada heading
- **Line-height:** Lebar (~1.4–1.6) untuk keterbacaan anak
- **Text alignment:** Center pada hero dan CTA; left pada content section
- **Highlight Kata:** Warna berbeda pada kata kunci (mis: "LESA" dalam judul berwarna ungu/biru, "statistics" berwarna pink, "Fun" berwarna amber)

### 3.3 Contoh Hierarchy Teks

```
H1 → "We're about to create the most fun reading game in the world!"
     → Bold, White, 48px, Center

H2 → "What is the LESA reading game?"
     → Bold, Dark, "LESA" dalam warna biru/ungu

H3 → "A Fun and Gamified World"
     → Bold, "Fun" dalam warna amber/orange

Body → Regular, Dark Gray, 16-18px

Counter → "1228+ and counting"
        → Bold, White, 56px, Center
```

---

## 4. Bentuk & Komponen UI

### 4.1 Tombol (Buttons)

#### Tombol Primary (Dark/Black)
```css
.btn-primary {
  background-color: #1A1A2E;  /* Near Black */
  color: #FFFFFF;
  border-radius: 50px;         /* Pill shape — sangat rounded */
  padding: 14px 32px;
  font-weight: 700;
  font-size: 16px;
  border: none;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}
```
**Contoh:** "Get in touch", "Join the waitlist"

#### Tombol Secondary (Green/Teal)
```css
.btn-secondary {
  background-color: #3D8B6E;  /* Teal Green */
  color: #FFFFFF;
  border-radius: 50px;
  padding: 14px 28px;
  font-weight: 700;
}
```
**Contoh:** "Join the waitlist" (dalam form footer)

#### Input Field (Email)
```css
.input-email {
  background-color: rgba(255,255,255,0.15);
  border: 2px solid rgba(255,255,255,0.4);
  border-radius: 50px;
  padding: 14px 24px;
  color: #FFFFFF;
  font-size: 16px;
  min-width: 280px;
}
```

### 4.2 Kartu (Cards)

#### App Preview Card
```css
.app-card {
  border-radius: 20px;         /* Rounded corners */
  overflow: hidden;
  background: #4B4BC8;         /* Purple */
  padding: 0;
  max-width: 460px;
  box-shadow: 0 12px 40px rgba(0,0,0,0.2);
}
```

#### Feature Image Box
```css
.feature-image-box {
  border-radius: 16px;
  overflow: hidden;
  /* Background warna cerah sesuai konten */
  /* Yellow: #FFD600, Pink: #E91E8C, dll */
}
```

### 4.3 Sidebar Navigasi Vertikal

```css
.sidebar-nav {
  position: fixed;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 56px;
  background: #5252D4;
  border-radius: 0 12px 12px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 12px 0;
  gap: 8px;
  z-index: 100;
}
```

### 4.4 Logo Brand Vertikal

```css
.brand-vertical {
  position: fixed;
  left: 0;
  bottom: 80px;
  writing-mode: vertical-rl;
  text-orientation: mixed;
  transform: rotate(180deg);
  font-size: 28px;
  font-weight: 900;
  color: #FFFFFF;
  background: #5252D4;
  padding: 12px 8px;
  border-radius: 0 8px 8px 0;
  letter-spacing: 4px;
}
```

### 4.5 Announcement Bar / Ticker

```css
.announcement-bar {
  background: #FFD600;
  color: #1A1A2E;
  padding: 10px 0;
  font-weight: 600;
  font-size: 14px;
  overflow: hidden;
  white-space: nowrap;
}

.ticker-content {
  display: inline-block;
  animation: marquee 20s linear infinite;
}

@keyframes marquee {
  0%   { transform: translateX(100%); }
  100% { transform: translateX(-100%); }
}
```

### 4.6 Floating Action Button (Chat/Character)

```css
.fab-chat {
  position: fixed;
  bottom: 24px;
  right: 24px;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #FF69B4;  /* Pink */
  border: 3px solid #FFFFFF;
  box-shadow: 0 4px 16px rgba(0,0,0,0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  overflow: hidden;
}
```
> Menampilkan karakter maskot (panda/karakter LESA) sebagai avatar chat.

### 4.7 Badge / Stat Counter

```css
.stat-counter {
  font-size: 56px;
  font-weight: 900;
  color: #FFFFFF;
  text-align: center;
}

.stat-label {
  font-size: 20px;
  font-weight: 400;
  color: rgba(255,255,255,0.8);
  text-align: center;
}
```

---

## 5. Layout & Struktur Halaman

### 5.1 Struktur Umum Halaman

```
┌─────────────────────────────────────┐
│  ANNOUNCEMENT BAR (marquee ticker)  │  ← Fixed/sticky top
├──┬──────────────────────────────────┤
│  │                                  │
│S │         MAIN CONTENT             │
│I │                                  │
│D │  [Section 1: Hero - Yellow BG]   │
│E │  [Section 2: Goal - Purple BG]   │
│B │  [Section 3: What is LESA]       │
│A │  [Section 4: Gamification]       │
│R │  [Section 5: Statistics]         │
│  │  [Section 6: Waitlist Counter]   │
│  │  [Section 7: Footer/CTA]        │
└──┴──────────────────────────────────┘
│  BRAND VERTICAL (fixed left)        │
│  FAB BUTTON (fixed right bottom)    │
```

### 5.2 Grid & Spacing

```css
:root {
  --container-max: 1200px;
  --section-padding-y: 80px 60px;
  --content-gap: 48px;
  --card-gap: 32px;
}

.container {
  max-width: var(--container-max);
  margin: 0 auto;
  padding: 0 24px;
}

/* Layout 2 kolom (feature sections) */
.feature-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--content-gap);
  align-items: center;
}
```

### 5.3 Responsive Design

- **Desktop (>1024px):** 2-column layout untuk feature sections
- **Tablet (768–1024px):** Single column, ilustrasi lebih kecil
- **Mobile (<768px):** Full stacked layout, sidebar tersembunyi atau berubah menjadi hamburger menu

---

## 6. Animasi & Interaksi

### 6.1 Animasi yang Terdeteksi

| Elemen | Jenis Animasi | Deskripsi |
|--------|--------------|-----------|
| Announcement Bar | Marquee/Scroll | Teks berjalan dari kanan ke kiri tanpa henti |
| Karakter Maskot | Float/Bounce | Karakter bergerak naik-turun perlahan |
| Ilustrasi Karakter | Entrance Animation | Fade-in + slide saat scroll ke section |
| Tombol | Hover Transform | Sedikit naik (translateY) + shadow saat hover |
| Counter Number | Count-up | Angka bertambah saat section masuk viewport |
| Karakter FAB | Idle Animation | Berputar/berkedip sedikit |

### 6.2 CSS Animasi Dasar

```css
/* Float animation untuk karakter */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50%       { transform: translateY(-15px); }
}

.character-float {
  animation: float 3s ease-in-out infinite;
}

/* Bounce ringan */
@keyframes bounce-light {
  0%, 100% { transform: translateY(0); }
  50%       { transform: translateY(-8px); }
}

/* Fade in dari bawah (scroll reveal) */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(40px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.reveal {
  animation: fadeInUp 0.6s ease forwards;
}

/* Spin dekorasi */
@keyframes spin-slow {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}

.deco-star {
  animation: spin-slow 8s linear infinite;
}
```

### 6.3 Transisi Hover

```css
/* Semua interactive element */
* {
  transition: transform 0.2s ease, 
              box-shadow 0.2s ease, 
              opacity 0.2s ease;
}

.btn:hover      { transform: translateY(-3px); }
.card:hover     { transform: translateY(-5px); box-shadow: 0 16px 40px rgba(0,0,0,0.15); }
.character:hover { transform: scale(1.05); }
```

---

## 7. Ilustrasi & Aset Visual

### 7.1 Gaya Ilustrasi

- **Gaya:** Flat vector illustration, cartoonish
- **Tools perkiraan:** Adobe Illustrator / Figma / Lottie animations
- **Karakteristik:**
  - Outline tebal dan rounded
  - Warna solid tanpa gradasi kompleks
  - Ekspresi wajah besar dan ekspresif
  - Proporsi chibi/cute (kepala besar, badan kecil)

### 7.2 Karakter Maskot

| Karakter | Deskripsi | Warna Dominan |
|----------|-----------|---------------|
| Maskot Utama | Panda abu-abu membaca buku hijau | Abu-abu, Hijau |
| Mouse Pink | Tikus kecil pink dengan bola lampu | Pink, Kuning |
| Rusa | Rusa coklat dengan syal merah | Coklat, Merah |
| Domba | Domba putih dengan kacamata pink | Putih, Pink |
| Anjing Laut | Anjing laut abu-abu lucu | Abu-abu |
| Rubah | Rubah orange dengan loupe | Orange |
| Kuda | Kuda coklat muda | Coklat Muda |
| Beruang | Karakter beruang kulit gelap | Coklat Gelap |
| Kelinci/Kucing | Karakter ungu | Ungu/Lavender |

### 7.3 Elemen Dekoratif

```
★ Bintang kecil (diamond/rhombus shape) — warna-warni
★ Confetti serpihan warna-warni
★ Bunga/flower kecil
★ Tanda musik (♪ ♫) 
★ Bola cahaya/sparkle (✦)
★ Pita/ribbon dekoratif
★ Tanda tanya (?) pada kartu terkunci
★ Bendera (flag) penanda posisi
```

### 7.4 Ilustrasi Gameplay (App Preview)

Menampilkan screenshot aplikasi dengan elemen:
- **Calendar/Progress Map:** Peta level berbentuk jalur (kartu berlapis)
- **Avatar Badge:** Circular avatar karakter
- **Level Cards:** Kartu kuning dan ungu berlapis
- **Flag "Today":** Penanda hari ini dalam peta level
- **Star Icons:** Bintang sebagai reward

---

## 8. Komponen Navigasi

### 8.1 Sidebar Kiri (Unique Navigation Pattern)

LESA menggunakan navigasi sidebar vertikal di sisi kiri — pola yang tidak umum dan sangat khas:

```
├── [☰] Hamburger menu (top)
├── [🇺🇸] Flag — English
├── [🇮🇸] Flag — Icelandic
│
│   (space kosong — konten)
│
└── [LESA] Brand text vertikal (bottom)
```

### 8.2 Fixed Elements

| Elemen | Posisi | Z-Index |
|--------|--------|---------|
| Announcement Bar | `top: 0` | `z: 1000` |
| Sidebar Navigasi | `left: 0` | `z: 900` |
| Brand Vertikal | `left: 0, bottom: 80px` | `z: 900` |
| FAB Chat Button | `right: 24px, bottom: 24px` | `z: 800` |
| "Honors" Label | `right: 0` | `z: 800` |

---

## 9. Prinsip Desain Utama

### 9.1 Design Principles LESA

1. **Joy First** → Warna terang dan karakter lucu menciptakan kegembiraan instan
2. **Accessibility** → Kontras tinggi (kuning+hitam, putih+ungu) memastikan keterbacaan
3. **Child-Centric** → Ukuran font besar, elemen rounded, tidak ada sudut tajam
4. **Storytelling Visual** → Karakter dengan kepribadian unik membangun narasi
5. **Gamification Cue** → Elemen game (star, flag, level card) mengkomunikasikan mekanisme permainan
6. **Inclusive Design** → Karakter dari berbagai latar (skin tone beragam)

### 9.2 Dos & Don'ts

| ✅ DO | ❌ DON'T |
|------|---------|
| Gunakan warna bold & cerah | Warna pastel pucat atau abu-abu saja |
| Rounded corners (≥12px) | Sudut tajam (0px radius) |
| Font bold dan besar | Font tipis/light weight |
| Banyak ilustrasi karakter | Hanya teks dan ikon |
| Animasi ceria dan springy | Animasi kaku atau terlalu cepat |
| Background berwarna solid | Background putih polos saja |
| Mixed color text highlights | Teks satu warna monoton |

---

*Dokumentasi ini merupakan hasil analisis visual dari website LESA (lesa.app) untuk keperluan referensi desain UI/UX mata kuliah Pemrograman UI.*
