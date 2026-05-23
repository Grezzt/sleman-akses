# 🎨 LESA — Design System Specification

> **Referensi:** [lesa.app](https://www.lesa.app) | **Analisis:** 17 Mei 2026

---

## 🎯 Design Philosophy

> **"Playful & Joyful"** — Setiap piksel menyampaikan kesenangan dan semangat belajar.

| Prinsip       | Penjelasan                                                       |
| ------------- | ---------------------------------------------------------------- |
| Joy First     | Warna cerah & karakter lucu menciptakan kegembiraan instan       |
| Child-Centric | Font besar, sudut rounded, tanpa elemen tajam                    |
| Gamification  | Elemen game (bintang, bendera, peta level) sebagai bahasa visual |
| Inclusive     | Karakter beragam latar belakang & warna kulit                    |
| High Contrast | Kombinasi warna memastikan keterbacaan optimal                   |

---

## 🎨 Color Palette

### Primary Colors

| Swatch | Nama             | Hex       | Digunakan Pada                    |
| ------ | ---------------- | --------- | --------------------------------- |
| 🟨     | **Vivid Yellow** | `#FFD600` | Hero background, announcement bar |
| 🟦     | **Royal Purple** | `#5252D4` | Section goal, sidebar, counter    |
| 🟪     | **Deep Purple**  | `#4B4BC8` | Footer, CTA section               |

### Accent Colors

| Swatch | Nama           | Hex       | Digunakan Pada                    |
| ------ | -------------- | --------- | --------------------------------- |
| 🩷     | **Hot Pink**   | `#E91E8C` | Highlight kata kunci "statistics" |
| 🟧     | **Warm Amber** | `#F59E0B` | Highlight kata kunci "Fun"        |
| 🟩     | **Teal Green** | `#3D8B6E` | Tombol waitlist form              |
| 🩵     | **Sky Blue**   | `#29B6F6` | Detail ilustrasi, badge           |

### Neutral Colors

| Swatch | Nama           | Hex                  | Digunakan Pada            |
| ------ | -------------- | -------------------- | ------------------------- |
| 🤍     | **Off White**  | `#F5F0EB` , '#FDFBFD | Section background netral |
| ⬛     | **Near Black** | `#1A1A2E`            | Tombol dark, body text    |
| ⬜     | **Pure White** | `#FFFFFF`            | Teks pada bg gelap, kartu |

### Kombinasi Warna per Section

```
Announcement Bar  →  BG: #FFD600   Text: #1A1A2E  (Kuning + Hitam)
Hero Section      →  BG: #FFD600   Text: #FFFFFF  (Kuning + Putih)
Goal Section      →  BG: #5252D4   Text: #FFFFFF  (Ungu + Putih)
Content Section   →  BG: #F5F0EB   Text: #1A1A2E  (Off-White + Hitam)
Counter Section   →  BG: #5252D4   Text: #FFFFFF  (Ungu + Putih)
Footer/CTA        →  BG: #4B4BC8   Text: #FFFFFF  (Deep Purple + Putih)
```

---

## ✍️ Typography

### Font Family

```
Primary Font: Nunito (Google Fonts), kitrounded
Fallback    : Poppins, Rounded Mplus 1c, sans-serif

Import:
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap');
```

### Type Scale

| Elemen                 | Size     | Weight        | Color                   | Alignment         |
| ---------------------- | -------- | ------------- | ----------------------- | ----------------- |
| **Brand Logo "LESA"**  | 72–120px | 900 ExtraBold | White                   | Center / Vertical |
| **H1 Hero Headline**   | 40–56px  | 800 ExtraBold | White                   | Center            |
| **H2 Section Title**   | 32–48px  | 700–800 Bold  | Dark / Mixed            | Center / Left     |
| **H3 Sub-heading**     | 24–32px  | 700 Bold      | Dark / Accent           | Left              |
| **Body Text**          | 16–20px  | 400 Regular   | `#1A1A2E`               | Left              |
| **Subtitle / Subtext** | 14–18px  | 400 Regular   | `rgba(255,255,255,0.8)` | Center            |
| **Button Label**       | 16–18px  | 700 Bold      | White / Dark            | Center            |
| **Announcement Bar**   | 13–14px  | 600 SemiBold  | `#1A1A2E`               | Marquee           |
| **Stat Counter**       | 48–56px  | 900 ExtraBold | White                   | Center            |

### Highlight Kata Kunci (Inline Color)

Teknik unik LESA: kata tertentu dalam heading diberi warna berbeda.

```
"What is the [LESA] reading game?"       → "LESA"       berwarna #5252D4 (Ungu)
"A [Fun] and Gamified World"             → "Fun"        berwarna #F59E0B (Amber)
"Get detailed [statistics] on..."        → "statistics" berwarna #E91E8C (Pink)
```

---

## 📐 Shapes & Border Radius

LESA **tidak menggunakan sudut tajam**. Semua elemen menggunakan border-radius besar.

| Komponen          | Border Radius   | Keterangan                   |
| ----------------- | --------------- | ---------------------------- |
| **Tombol (pill)** | `50px`          | Fully rounded, capsule shape |
| **Input field**   | `50px`          | Sama seperti tombol          |
| **Kartu / Card**  | `20px`          | Rounded corners              |
| **Image box**     | `16px`          | Kotak ilustrasi feature      |
| **Sidebar**       | `0 12px 12px 0` | Rounded kanan saja           |
| **FAB Button**    | `50%`           | Lingkaran penuh              |
| **Alert / Badge** | `12px`          | Sedikit rounded              |

---

## 🧩 Components

### 1. Buttons

#### Button Dark (Primary CTA)

```css
.btn-dark {
    background: #1a1a2e;
    color: #ffffff;
    padding: 14px 32px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 16px;
    border: none;
    transition:
        transform 0.2s,
        box-shadow 0.2s;
}
.btn-dark:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
}
```

📍 _Contoh:_ "Get in touch", "Join the waitlist"

#### Button Green (Form Submit)

```css
.btn-green {
    background: #3d8b6e;
    color: #ffffff;
    padding: 14px 28px;
    border-radius: 50px;
    font-weight: 700;
}
```

📍 _Contoh:_ "Join the waitlist" (dalam form email)

#### Button Outline

```css
.btn-outline {
    background: transparent;
    color: #1a1a2e;
    border: 2px solid #1a1a2e;
    padding: 14px 32px;
    border-radius: 50px;
    font-weight: 700;
}
```

---

### 2. Input Field (Email)

```css
.input-email {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 50px;
    padding: 14px 24px;
    color: #ffffff;
    font-size: 16px;
    min-width: 280px;
}
.input-email::placeholder {
    color: rgba(255, 255, 255, 0.6);
}
```

---

### 3. Announcement Bar / Ticker

```css
.announcement-bar {
    background: #ffd600;
    color: #1a1a2e;
    padding: 10px 0;
    font-weight: 600;
    font-size: 14px;
    overflow: hidden;
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 1000;
}
.ticker {
    display: inline-block;
    animation: marquee 25s linear infinite;
}
@keyframes marquee {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}
```

---

### 4. Sidebar Navigasi Vertikal

```css
.sidebar {
    position: fixed;
    left: 0;
    top: 40px;
    bottom: 0;
    width: 56px;
    background: #5252d4;
    border-radius: 0 12px 12px 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px 0;
    gap: 16px;
    z-index: 900;
}
```

**Isi sidebar (dari atas ke bawah):**

- `☰` Hamburger menu
- `🇺🇸` Language toggle EN
- `🇮🇸` Language toggle IS
- `LESA` Brand text vertikal (bottom)

---

### 5. App Preview Card

```css
.app-card {
    border-radius: 20px;
    overflow: hidden;
    background: #5252d4;
    max-width: 460px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
}
```

---

### 6. Feature Image Box

```css
.image-box {
    border-radius: 16px;
    overflow: hidden;
    padding: 24px;
}
.image-box--yellow {
    background: #ffd600;
}
.image-box--pink {
    background: #ff69b4;
}
```

---

### 7. Stat Counter

```css
.counter-number {
    font-size: 56px;
    font-weight: 900;
    color: #ffffff;
    text-align: center;
}
.counter-sub {
    font-size: 20px;
    font-weight: 400;
    color: rgba(255, 255, 255, 0.8);
}
```

---

### 8. FAB (Floating Action Button)

```css
.fab-chat {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #ff69b4;
    border: 3px solid #ffffff;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
    z-index: 800;
    overflow: hidden;
    cursor: pointer;
}
```

> Menampilkan karakter maskot LESA sebagai avatar interaktif.

---

## 📐 Layout & Grid

### Struktur Halaman

```
┌─────────────────────────────────────────────┐
│   ANNOUNCEMENT BAR  (sticky, z:1000)        │
├──┬──────────────────────────────────────────┤
│  │                                          │
│S │   SECTION 1 — Hero        (BG: Yellow)  │
│I │   SECTION 2 — Goal        (BG: Purple)  │
│D │   SECTION 3 — What LESA   (BG: Offwhite)│
│E │   SECTION 4 — Gamification(BG: Offwhite)│
│B │   SECTION 5 — Statistics  (BG: Offwhite)│
│A │   SECTION 6 — Counter     (BG: Purple)  │
│R │   SECTION 7 — Waitlist    (BG: D.Purple)│
│  │                                          │
└──┴──────────────────────────────────────────┘
   FAB Button  (fixed, bottom-right, z:800)
```

### Grid System

```css
:root {
    --container-max: 1200px;
    --section-py: 80px;
    --gap-lg: 48px;
    --gap-md: 32px;
    --gap-sm: 16px;
}

.container {
    max-width: var(--container-max);
    margin: 0 auto;
    padding: 0 24px;
}

/* 2-Column Feature Layout */
.feature-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--gap-lg);
    align-items: center;
}
```

### Responsive Breakpoints

| Breakpoint | Width        | Perubahan                                 |
| ---------- | ------------ | ----------------------------------------- |
| Desktop    | `> 1024px`   | 2-column layout, sidebar penuh            |
| Tablet     | `768–1024px` | 1-column, ilustrasi ukuran lebih kecil    |
| Mobile     | `< 768px`    | Full stack, sidebar collapse ke hamburger |

---

## ✨ Animasi & Interaksi

### Animasi Utama

| Elemen            | Animasi          | Duration | Easing        |
| ----------------- | ---------------- | -------- | ------------- |
| Announcement text | `marquee` scroll | 25s      | `linear`      |
| Karakter maskot   | `float` bounce   | 3s       | `ease-in-out` |
| Section reveal    | `fadeInUp`       | 0.6s     | `ease`        |
| Counter number    | `count-up` JS    | 2s       | `linear`      |
| Dekorasi bintang  | `spin-slow`      | 8s       | `linear`      |

### CSS Keyframes

```css
/* Float — karakter bergerak naik-turun */
@keyframes float {
    0%,
    100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-15px);
    }
}

/* Fade In dari bawah — scroll reveal */
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

/* Rotate pelan — elemen dekoratif */
@keyframes spin-slow {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
```

### Hover Effects

```css
/* Tombol */
.btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
}

/* Kartu */
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.15);
}

/* Karakter ilustrasi */
.character:hover {
    transform: scale(1.05);
}

/* Transisi global */
* {
    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}
```

---

## 🦊 Ilustrasi & Karakter

### Gaya Ilustrasi

| Aspek        | Detail                                 |
| ------------ | -------------------------------------- |
| **Style**    | Flat vector, cartoonish                |
| **Outline**  | Tebal dan rounded                      |
| **Warna**    | Solid, tidak ada gradasi kompleks      |
| **Proporsi** | Chibi/cute — kepala besar, badan kecil |
| **Ekspresi** | Besar, ekspresif, penuh emosi          |
| **Format**   | SVG / PNG dengan transparansi          |

### Daftar Karakter Maskot

| Karakter                | Warna Dominan   | Peran                           |
| ----------------------- | --------------- | ------------------------------- |
| 🐼 Panda (Maskot Utama) | Abu-abu + Hijau | Membaca buku — karakter sentral |
| 🐭 Mouse Pink           | Pink + Kuning   | Ide & kreativitas (bola lampu)  |
| 🦌 Reindeer             | Coklat + Merah  | Petualangan                     |
| 🐑 Sheep                | Putih + Pink    | Musik (memegang marakas)        |
| 🦭 Seal                 | Abu-abu         | Gembira & bermain               |
| 🦊 Fox                  | Orange          | Detektif (memegang loupe)       |
| 🐴 Horse                | Coklat Muda     | Energik                         |
| 🐻 Bear                 | Coklat Gelap    | Kuat                            |
| 🐰 Rabbit/Cat           | Lavender        | Misterius                       |

### Elemen Dekoratif

```
◆  Diamond / Rhombus kecil  → Accent warna-warni
★  Bintang berputar          → Momen reward
✦  Sparkle / Glitter         → Magic effect
🎵  Not musik                 → Kesenangan belajar
🎊  Confetti                  → Perayaan
❓  Tanda tanya               → Kartu level terkunci
🚩  Bendera merah              → Penanda "Today" di peta
```

---

## 🔑 Design Tokens (CSS Variables)

```css
:root {
    /* ── Colors ── */
    --clr-yellow: #ffd600;
    --clr-purple: #5252d4;
    --clr-purple-dark: #4b4bc8;
    --clr-pink: #e91e8c;
    --clr-amber: #f59e0b;
    --clr-green: #3d8b6e;
    --clr-sky: #29b6f6;
    --clr-off-white: #f5f0eb;
    --clr-dark: #1a1a2e;
    --clr-white: #ffffff;

    /* ── Typography ── */
    --font-main: "Nunito", sans-serif;
    --fw-regular: 400;
    --fw-semibold: 600;
    --fw-bold: 700;
    --fw-extrabold: 800;
    --fw-black: 900;

    /* ── Font Sizes ── */
    --fs-xs: 13px;
    --fs-sm: 14px;
    --fs-base: 16px;
    --fs-md: 18px;
    --fs-lg: 24px;
    --fs-xl: 32px;
    --fs-2xl: 40px;
    --fs-3xl: 48px;
    --fs-4xl: 56px;
    --fs-hero: 80px;

    /* ── Border Radius ── */
    --radius-pill: 50px;
    --radius-card: 20px;
    --radius-box: 16px;
    --radius-sm: 12px;
    --radius-full: 9999px;

    /* ── Spacing ── */
    --space-xs: 8px;
    --space-sm: 16px;
    --space-md: 24px;
    --space-lg: 32px;
    --space-xl: 48px;
    --space-2xl: 64px;
    --space-3xl: 80px;

    /* ── Shadows ── */
    --shadow-sm: 0 4px 16px rgba(0, 0, 0, 0.15);
    --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.2);
    --shadow-lg: 0 12px 40px rgba(0, 0, 0, 0.25);

    /* ── Z-index ── */
    --z-bar: 1000;
    --z-sidebar: 900;
    --z-fab: 800;

    /* ── Transitions ── */
    --transition-fast: 0.2s ease;
    --transition-med: 0.4s ease;
    --transition-slow: 0.6s ease;

    /* ── Layout ── */
    --container-max: 1200px;
    --sidebar-width: 56px;
}
```

---

## ✅ Do's & Don'ts

| ✅ Lakukan                                  | ❌ Hindari                                   |
| ------------------------------------------- | -------------------------------------------- |
| Warna bold dan cerah                        | Warna pastel pucat / abu-abu monoton         |
| Border-radius besar (≥ 12px)                | Sudut tajam (0px radius)                     |
| Font Nunito Bold/ExtraBold                  | Font tipis / Light weight                    |
| Banyak karakter ilustrasi                   | Layout hanya teks dan ikon saja              |
| Highlight kata kunci warna-warni            | Teks heading satu warna polos                |
| Animasi float / bounce yang ceria           | Animasi kaku, terlalu cepat, atau berlebihan |
| Background section berwarna solid           | Background putih polos di seluruh halaman    |
| Kontras tinggi kuning+hitam atau putih+ungu | Kontras rendah yang sulit dibaca             |
| Tombol berbentuk pill (capsule)             | Tombol persegi tanpa border-radius           |

---

_Design specification ini dibuat berdasarkan analisis visual website LESA (lesa.app) untuk referensi mata kuliah Pemrograman UI — Semester 6._
