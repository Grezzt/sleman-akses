@extends('layouts.app')

@section('title', 'Analitik Spasial DBSCAN & Blank Spot')
@section('page_title', 'Analitik Spasial & Deteksi Blank Spot')

@push('styles')
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet" crossorigin="" />
    <style>
        #spatialMap {
            height: 600px !important;
            min-height: 600px !important;
            width: 100% !important;
            display: block !important;
            border-radius: var(--radius-card);
        }

        .leaflet-container {
            font-family: var(--font-sans);
        }

        .custom-popup .leaflet-popup-content-wrapper {
            background: var(--color-surface);
            border: 2px solid var(--color-border-solid);
            border-radius: var(--radius-card);
            box-shadow: 4px 4px 0px 0px var(--color-border-solid);
            padding: 8px;
        }

        .custom-popup .leaflet-popup-tip {
            background: var(--color-surface);
            border: 2px solid var(--color-border-solid);
        }

        .stat-card-neo {
            background: var(--color-surface);
            border: 2px solid var(--color-border-solid);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-solid);
            padding: 22px;
            transition: all 0.2s ease;
        }

        .stat-card-neo:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0px 0px var(--color-border-solid);
        }

        /* Floating Map Panel Neobrutalism */
        .map-floating-panel {
            position: absolute;
            top: 14px;
            right: 14px;
            z-index: 1000;
            max-width: 440px;
            width: calc(100% - 28px);
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            border: 2px solid var(--color-border-solid);
            border-radius: var(--radius-card);
            box-shadow: 4px 4px 0px 0px var(--color-border-solid);
            transition: all 0.25s ease;
        }
    </style>
@endpush

@section('content')
    <div class="flex flex-col gap-6 pb-12">

        <!-- 1. Header Judul Fleksibel & Terpisah (Tanpa Card Berat) -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 pt-1">
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[rgba(3,121,64,0.1)] text-primary border border-primary/20">
                        <i class="fa-solid fa-satellite-dish"></i> Decision Support System
                    </span>
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-text-muted border border-border-solid">
                        <i class="fa-solid fa-map-location-dot"></i> 17 Kapanewon Sleman
                    </span>
                </div>
                <h1 class="text-2xl lg:text-3xl font-black text-text-primary tracking-tight m-0">
                    Analisis Spasial DBSCAN & Deteksi Blank Spot Disabilitas
                </h1>
                <p class="text-sm text-text-muted mt-1 max-w-4xl leading-relaxed m-0">
                    Pemetaan densitas fasilitas ramah disabilitas fisik di 17 Kapanewon Kabupaten Sleman menggunakan
                    algoritma DBSCAN (Haversine Distance).
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 flex-shrink-0">
                <a href="{{ route('admin.map.index') }}"
                    class="px-5 py-2.5 rounded-full font-bold border-2 border-border-solid bg-surface text-text-primary hover:bg-slate-100 transition-all shadow-[2px_2px_0px_0px_var(--color-border-solid)] text-sm flex items-center gap-2">
                    <i class="fa-solid fa-map-pin text-primary"></i>
                    <span>Peta Manajemen</span>
                </a>
                <a href="{{ url('/map') }}" target="_blank"
                    class="px-5 py-2.5 rounded-full font-bold border-2 border-border-solid bg-primary text-white hover:bg-primary-hover transition-all shadow-[2px_2px_0px_0px_var(--color-secondary)] text-sm flex items-center gap-2">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>Buka Peta Publik</span>
                </a>
            </div>
        </div>

        <!-- 2. Kartu KPI Metrik Riset DBSCAN (Grid 4x1 Horizontal) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6">
            <!-- KPI 1: Total Fasilitas -->
            <div class="stat-card-neo flex flex-col justify-between p-4 lg:p-5"
                style="border-top: 4px solid var(--color-primary);">
                <div class="flex justify-between items-start mb-2.5">
                    <div class="min-w-0 pr-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-text-muted truncate block">Total
                            Fasilitas</span>
                        <h3 id="kpiTotalFacilities" class="m-0 font-black text-2xl lg:text-3xl text-text-onsurface mt-1">
                            {{ $analysis['summary']['total_facilities'] }}
                        </h3>
                    </div>
                    <div
                        class="p-2.5 lg:p-3 rounded-xl bg-[rgba(3,121,64,0.1)] text-primary flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-location-dot text-lg lg:text-xl"></i>
                    </div>
                </div>
                <div
                    class="pt-2.5 border-t border-border-solid flex items-center justify-between text-[11px] font-bold text-primary">

                </div>
            </div>

            <!-- KPI 2: Klaster Terbentuk -->
            <div class="stat-card-neo flex flex-col justify-between p-4 lg:p-5" style="border-top: 4px solid #2563EB;">
                <div class="flex justify-between items-start mb-2.5">
                    <div class="min-w-0 pr-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-text-muted truncate block">Klaster
                            Aglomerasi</span>
                        <h3 id="kpiTotalClusters" class="m-0 font-black text-2xl lg:text-3xl text-blue-700 mt-1">
                            {{ $analysis['summary']['total_clusters'] }}
                        </h3>
                    </div>
                    <div class="p-2.5 lg:p-3 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-nodes text-lg lg:text-xl"></i>
                    </div>
                </div>
                <div
                    class="pt-2.5 border-t border-border-solid flex items-center justify-between text-[11px] font-bold text-text-muted">
                    <span id="kpiCoreBorderPoints" class="text-blue-700 truncate mr-1">
                        {{ $analysis['summary']['total_core_points'] }} Core &middot;
                        {{ $analysis['summary']['total_border_points'] }} Border
                    </span>
                    <span class="shrink-0">Padat</span>
                </div>
            </div>

            <!-- KPI 3: Fasilitas Tersebar Mandiri -->
            <div class="stat-card-neo flex flex-col justify-between p-4 lg:p-5" style="border-top: 4px solid #D97706;">
                <div class="flex justify-between items-start mb-2.5">
                    <div class="min-w-0 pr-2">
                        <span
                            class="text-[11px] font-bold uppercase tracking-wider text-text-muted truncate block">Fasilitas
                            Tersebar Mandiri</span>
                        <h3 id="kpiNoisePoints" class="m-0 font-black text-2xl lg:text-3xl text-amber-700 mt-1">
                            {{ $analysis['summary']['total_noise_points'] }}
                        </h3>
                    </div>
                    <div
                        class="p-2.5 lg:p-3 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-location-crosshairs text-lg lg:text-xl"></i>
                    </div>
                </div>
                <div
                    class="pt-2.5 border-t border-border-solid flex items-center justify-between text-[11px] font-bold text-text-muted">
                    <span class="truncate mr-1">Radius &le; <span id="kpiNoiseEps">{{ $epsilon }}</span>m</span>
                    <span class="text-amber-700 shrink-0 font-bold">Tersebar</span>
                </div>
            </div>

            <!-- KPI 4: Kapanewon Belum Ada Fasilitas -->
            <div class="stat-card-neo flex flex-col justify-between p-4 lg:p-5" style="border-top: 4px solid #DC2626;">
                <div class="flex justify-between items-start mb-2.5">
                    <div class="min-w-0 pr-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-text-muted truncate block">Belum
                            Ada Fasilitas</span>
                        <h3 id="kpiBlankSpotCount" class="m-0 font-black text-2xl lg:text-3xl text-red-600 mt-1">
                            {{ $analysis['summary']['blank_spot_count'] }} <span
                                class="text-base font-bold text-text-muted">/ 17</span>
                        </h3>
                    </div>
                    <div class="p-2.5 lg:p-3 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-xmark text-lg lg:text-xl"></i>
                    </div>
                </div>
                <div
                    class="pt-2.5 border-t border-border-solid flex items-center justify-between text-[11px] font-bold text-red-600">
                    <span class="truncate mr-1"><span
                            id="kpiBlankSpotRatio">{{ $analysis['summary']['blank_spot_ratio'] }}%</span> Wilayah</span>
                    <span class="shrink-0">Butuh Fasilitas</span>
                </div>
            </div>
        </div>

        <!-- 3. Peta Spasial GIS Leaflet dengan KONTROL PARAMETER TERINTEGRASI LANGSUNG -->
        <div class="admin-card p-6 lg:p-8 m-0">
            <div
                class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4 pb-4 border-b-2 border-border-solid">
                <div>
                    <h3 class="text-lg lg:text-xl font-bold text-text-primary flex items-center gap-2.5 m-0">
                        <i class="fa-solid fa-map-location-dot text-primary"></i>
                        <span>Peta Choropleth 17 Kapanewon & Klaster Spasial</span>
                    </h3>
                    <p class="text-xs text-text-muted mt-1 m-0">
                        Visualisasi batas administrasi (Esri Canvas) dengan klaster aglomerasi dan kontrol simulasi
                        parameter DBSCAN langsung di peta.
                    </p>
                </div>

                <!-- Legenda Klasifikasi Peta -->
                <div class="flex flex-wrap items-center gap-2">
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-800 border border-green-200">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-600 inline-block"></span> Kepadatan Tinggi
                    </span>
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 inline-block"></span> Fasilitas Memadai
                    </span>
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span> Tersebar Mandiri
                    </span>
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-800 border border-red-200">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-600 inline-block"></span> Belum Ada Fasilitas (0)
                    </span>
                </div>
            </div>

            <!-- Wadah Peta & Kontrol Terapung (Floating Parameter Panel) -->
            <div class="relative w-full rounded-2xl overflow-hidden border-2 border-border-solid shadow-inner">
                <!-- Canvas Map -->
                <div id="spatialMap" class="w-full relative z-0"></div>

                <!-- FLOATING DBSCAN PARAMETER CONTROL PANEL (Menghemat Ruang) -->
                <div class="map-floating-panel p-4" id="floatingParamPanel">
                    <!-- Baris Judul & Tombol Toggle -->
                    <div class="flex items-center justify-between pb-2 mb-3 border-b border-border-solid">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-sliders text-primary"></i>
                            <span class="font-bold text-sm text-text-primary">Parameter DBSCAN</span>
                            <span id="floatingSummaryBadge"
                                class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-secondary text-primary border border-secondary/40">
                                {{ $epsilon }}m &bull; {{ $minPts }} pts
                            </span>
                        </div>
                        <button id="btnToggleParams" type="button" title="Tutup / Buka Panel Parameter"
                            class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 border border-border-solid flex items-center justify-center text-text-muted transition-all">
                            <i class="fa-solid fa-chevron-up text-xs" id="iconToggleParams"></i>
                        </button>
                    </div>

                    <!-- Konten Parameter (Bisa di-collapse untuk area pandang peta maksimal) -->
                    <div id="paramPanelBody" class="flex flex-col gap-3.5 transition-all duration-300">
                        <!-- Epsilon Slider -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label for="inputEps"
                                    class="font-bold text-xs text-text-primary flex items-center gap-1.5">
                                    <i class="fa-solid fa-ruler-combined text-primary text-[11px]"></i>
                                    <span>Radius Epsilon (&epsilon;)</span>
                                </label>
                                <span id="badgeEpsVal"
                                    class="px-2 py-0.5 rounded-full text-[11px] font-black bg-secondary text-primary">
                                    {{ $epsilon }} meter
                                </span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <input type="range" id="sliderEps" min="300" max="2000" step="50"
                                    value="{{ $epsilon }}"
                                    class="w-full accent-primary cursor-pointer h-2 bg-slate-200 rounded-lg" />
                                <input type="number" id="inputEps" min="300" max="2000" step="50"
                                    value="{{ $epsilon }}"
                                    class="w-20 px-2 py-1 border-2 border-border-solid rounded-lg font-bold text-xs text-center bg-white shadow-sm" />
                            </div>
                            <p class="text-[10px] text-text-muted mt-1 m-0">
                                Jarak maksimum antar fasilitas untuk dikategorikan bertetangga.
                            </p>
                        </div>

                        <!-- MinPts Slider -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label for="inputMinPts"
                                    class="font-bold text-xs text-text-primary flex items-center gap-1.5">
                                    <i class="fa-solid fa-users-rectangle text-primary text-[11px]"></i>
                                    <span>MinPts (Titik Minimum)</span>
                                </label>
                                <span id="badgeMinPtsVal"
                                    class="px-2 py-0.5 rounded-full text-[11px] font-black bg-secondary text-primary">
                                    {{ $minPts }} titik
                                </span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <input type="range" id="sliderMinPts" min="2" max="10" step="1"
                                    value="{{ $minPts }}"
                                    class="w-full accent-primary cursor-pointer h-2 bg-slate-200 rounded-lg" />
                                <input type="number" id="inputMinPts" min="2" max="10" step="1"
                                    value="{{ $minPts }}"
                                    class="w-20 px-2 py-1 border-2 border-border-solid rounded-lg font-bold text-xs text-center bg-white shadow-sm" />
                            </div>
                            <p class="text-[10px] text-text-muted mt-1 m-0">
                                Ambang batas minimum fasilitas untuk membentuk klaster aglomerasi padat.
                            </p>
                        </div>

                        <!-- Tombol Aksi Hitung Ulang & Reset -->
                        <div class="flex items-center gap-2 pt-2 border-t border-border-solid">
                            <button id="btnRecalculate" type="button"
                                class="flex-1 py-2 px-3 rounded-full font-bold text-xs border-2 border-border-solid bg-primary text-white hover:bg-primary-hover transition-all shadow-[2px_2px_0_0_var(--color-secondary)] flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-calculator" id="iconRecalc"></i>
                                <span>Hitung Ulang DBSCAN</span>
                            </button>
                            <button id="btnResetDefault" type="button" title="Kembalikan Default 800m, 3 titik"
                                class="py-2 px-3 rounded-full font-bold text-xs border-2 border-border-solid bg-white text-text-muted hover:bg-slate-100 hover:text-text-primary transition-all shadow-sm flex items-center justify-center gap-1">
                                <i class="fa-solid fa-rotate-left"></i>
                                <span>Reset</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panduan & Footer Peta -->
            <div
                class="mt-4 pt-4 border-t border-border-solid flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-xs text-text-muted">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-primary text-sm"></i>
                    <span><strong>Tips Interaktif:</strong> Klik pada poligon Kapanewon untuk zoom wilayah. Klik marker
                        lingkaran untuk rincian fasilitas (Ramp, Toilet, dsb).</span>
                </div>
                <div class="font-mono text-[11px] text-text-muted font-bold">
                    Basemap: Esri World Light Gray &bull; Proyeksi: EPSG:4326
                </div>
            </div>
        </div>

        <!-- 4. Rekapitulasi Klaster DBSCAN yang Terbentuk -->
        <div class="admin-card p-6 lg:p-8 m-0">
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-6 pb-4 border-b-2 border-border-solid">
                <div>
                    <h3 class="text-lg lg:text-xl font-bold text-text-primary flex items-center gap-2.5 m-0">
                        <i class="fa-solid fa-layer-group text-primary"></i>
                        <span>Rekapitulasi Klaster Aglomerasi Terbentuk (DBSCAN)</span>
                    </h3>
                    <p class="text-xs text-text-muted mt-1 m-0">
                        Rincian kelompok aglomerasi fasilitas, Kapanewon dominan, serta komposisi titik Core (inti) dan
                        Border (tepian).
                    </p>
                </div>
                <span
                    class="text-xs font-bold text-primary bg-[rgba(3,121,64,0.1)] px-3 py-1 rounded-full border border-primary/20">
                    Formula Jarak Haversine
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left table-admin" id="tableClusters">
                    <thead>
                        <tr>
                            <th class="rounded-l-xl py-3.5 px-4">ID Klaster</th>
                            <th class="py-3.5 px-4">Nama Klaster</th>
                            <th class="py-3.5 px-4">Kapanewon Dominan</th>
                            <th class="py-3.5 px-4 text-center">Total Fasilitas</th>
                            <th class="py-3.5 px-4 text-center">Titik Core</th>
                            <th class="py-3.5 px-4 text-center">Titik Border</th>
                            <th class="rounded-r-xl py-3.5 px-4">Koordinat Sentroid</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-solid" id="tbodyClusters">
                        @forelse ($analysis['clusters'] as $cluster)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3.5 px-4 font-black">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-black text-white shadow-sm inline-block"
                                        style="background-color: {{ $cluster['color'] }};">
                                        #{{ $cluster['cluster_id'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-text-primary">
                                    {{ $cluster['label'] }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-text-onsurface">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 border border-border-solid text-xs">
                                        {{ $cluster['dominant_kapanewon'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-black">
                                    <span
                                        class="px-3 py-1 rounded-full bg-slate-100 border border-slate-300 font-bold text-xs">
                                        {{ $cluster['points_count'] }} Fasilitas
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center text-green-700 font-bold">
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle text-[8px]"></i> {{ $cluster['core_count'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center text-blue-700 font-bold">
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fa-regular fa-circle text-[8px]"></i> {{ $cluster['border_count'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-xs text-text-muted">
                                    {{ number_format($cluster['center'][0], 5) }},
                                    {{ number_format($cluster['center'][1], 5) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-text-muted font-bold">
                                    Tidak ada klaster yang terbentuk pada kombinasi parameter saat ini. Seluruh titik
                                    merupakan fasilitas tersebar mandiri.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. Tabel Analisis Disparitas Aksesibilitas 17 Kapanewon -->
        <div class="admin-card p-6 lg:p-8 m-0">
            <div
                class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-4 border-b-2 border-border-solid">
                <div>
                    <h3 class="text-lg lg:text-xl font-bold text-text-primary flex items-center gap-2.5 m-0">
                        <i class="fa-solid fa-chart-column text-primary"></i>
                        <span>Tabel Disparitas & Pemetaan Blank Spot 17 Kapanewon Sleman</span>
                    </h3>
                    <p class="text-xs text-text-muted mt-1 m-0">
                        Data komparatif per Kapanewon: luas wilayah, kepadatan fasilitas, status wilayah, dan pemenuhan
                        sarana disabilitas fisik.
                    </p>
                </div>
                <span
                    class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-secondary text-primary border border-primary/20">
                </span>
            </div>

            <!-- Filter Chips Ramah (Quick Filter 17 Kapanewon) -->
            <div class="flex flex-wrap items-center gap-2 mb-6">
                <span class="text-xs font-bold text-text-muted mr-2">Filter Kategori:</span>
                <button type="button"
                    class="btn-filter-kap active px-4 py-1.5 rounded-full text-xs font-bold border-2 transition-all bg-primary text-white border-primary shadow-[2px_2px_0_0_var(--color-secondary)]"
                    data-filter="ALL">
                    Semua Kapanewon (17)
                </button>
                <button type="button"
                    class="btn-filter-kap px-4 py-1.5 rounded-full text-xs font-bold border-2 transition-all bg-surface text-text-muted border-border-solid hover:bg-slate-100 hover:text-text-primary"
                    data-filter="BLANK_SPOT">
                    <i class="fa-solid fa-circle-xmark text-red-500 mr-1"></i> Belum Ada Fasilitas (10)
                </button>
                <button type="button"
                    class="btn-filter-kap px-4 py-1.5 rounded-full text-xs font-bold border-2 transition-all bg-surface text-text-muted border-border-solid hover:bg-slate-100 hover:text-text-primary"
                    data-filter="HIGH_DENSITY">
                    <i class="fa-solid fa-star text-green-600 mr-1"></i> Kepadatan Tinggi (2)
                </button>
                <button type="button"
                    class="btn-filter-kap px-4 py-1.5 rounded-full text-xs font-bold border-2 transition-all bg-surface text-text-muted border-border-solid hover:bg-slate-100 hover:text-text-primary"
                    data-filter="MEDIUM_DENSITY">
                    <i class="fa-solid fa-circle-check text-blue-600 mr-1"></i> Fasilitas Memadai (1)
                </button>
                <button type="button"
                    class="btn-filter-kap px-4 py-1.5 rounded-full text-xs font-bold border-2 transition-all bg-surface text-text-muted border-border-solid hover:bg-slate-100 hover:text-text-primary"
                    data-filter="ISOLATED_FACILITIES">
                    <i class="fa-solid fa-location-crosshairs text-amber-500 mr-1"></i> Tersebar Mandiri (4)
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left table-admin" id="tableKapanewon">
                    <thead>
                        <tr>
                            <th class="rounded-l-xl py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4">Kapanewon</th>
                            <th class="py-3.5 px-4 text-right">Luas (km&sup2;)</th>
                            <th class="py-3.5 px-4 text-center">Total Fasilitas</th>
                            <th class="py-3.5 px-4">Kategori Wilayah</th>
                            <th class="py-3.5 px-4 text-center">Rasio Aksesibilitas</th>
                            <th class="rounded-r-xl py-3.5 px-4">Kelengkapan Fasilitas Dasar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-solid" id="tbodyKapanewon">
                        @foreach ($analysis['kapanewon'] as $idx => $kap)
                            <tr class="hover:bg-slate-50 transition-colors {{ $kap['facilities_count'] === 0 ? 'bg-red-50/40' : '' }}"
                                data-status="{{ $kap['status'] }}">
                                <td class="py-3.5 px-4 text-center font-bold text-text-muted">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-4 font-bold text-text-onsurface">
                                    {{ $kap['kapanewon'] }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-xs text-text-muted">
                                    {{ number_format($kap['luas_km2'], 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-black">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-black {{ $kap['facilities_count'] > 0 ? 'bg-primary text-white' : 'bg-red-100 text-red-800 border border-red-300' }}">
                                        {{ $kap['facilities_count'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($kap['status'] === 'BLANK_SPOT')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-300">
                                            <i class="fa-solid fa-xmark"></i> Belum Ada Fasilitas (Defisit)
                                        </span>
                                    @elseif ($kap['status'] === 'HIGH_DENSITY')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-900 border border-green-300">
                                            <i class="fa-solid fa-star"></i> Kawasan Kepadatan Tinggi
                                        </span>
                                    @elseif ($kap['status'] === 'MEDIUM_DENSITY')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-900 border border-blue-300">
                                            <i class="fa-solid fa-circle-check"></i> Kawasan Fasilitas Memadai
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                            <i class="fa-solid fa-location-crosshairs"></i> Fasilitas Tersebar Mandiri
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold">
                                    @if ($kap['facilities_count'] > 0)
                                        <div class="flex items-center justify-center gap-2.5">
                                            <div
                                                class="w-20 bg-slate-200 rounded-full h-2.5 overflow-hidden border border-slate-300">
                                                <div class="bg-primary h-2.5 rounded-full"
                                                    style="width: {{ $kap['accessibility_percentage'] }}%"></div>
                                            </div>
                                            <span
                                                class="text-xs font-mono font-bold">{{ $kap['accessibility_percentage'] }}%</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-text-muted font-mono font-bold">0.0%</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-xs font-bold text-text-muted">
                                    @if ($kap['facilities_count'] > 0)
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-border-solid"
                                                title="Ramp"><i class="fa-solid fa-wheelchair text-primary"></i> Ramp:
                                                {{ $kap['details']['ramp'] }}</span>
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-border-solid"
                                                title="Toilet Difabel"><i class="fa-solid fa-restroom text-primary"></i>
                                                Toilet: {{ $kap['details']['toilet_difabel'] }}</span>
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-border-solid"
                                                title="Parkir Difabel"><i
                                                    class="fa-solid fa-square-parking text-primary"></i> Parkir:
                                                {{ $kap['details']['parkir_difabel'] }}</span>
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-border-solid"
                                                title="Lift"><i class="fa-solid fa-elevator text-primary"></i> Lift:
                                                {{ $kap['details']['lift'] }}</span>
                                        </div>
                                    @else
                                        <span class="text-red-500 font-bold italic">Tidak ada infrastruktur terdata</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data Awal dari Blade Controller
            let currentData = @json($analysis);
            let geojsonLayer = null;
            let markersLayer = L.layerGroup();
            let geojsonData = null;
            let currentFilter = 'ALL';

            // Inisialisasi Peta Leaflet Sleman
            const map = L.map('spatialMap', {
                center: [-7.7150, 110.3800],
                zoom: 11,
                scrollWheelZoom: true,
            });

            // Tile Layer (Esri World Light Gray Canvas - Clean & Neutral)
            L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles &copy; Esri, DeLorme, NAVTEQ',
                    maxZoom: 16
                }).addTo(map);

            markersLayer.addTo(map);

            // Fetch Data Batas Wilayah GeoJSON
            fetch('{{ asset('data/sleman_kapanewon.geojson') }}')
                .then(res => res.json())
                .then(data => {
                    geojsonData = data;
                    renderMapLayers(currentData);
                })
                .catch(err => {
                    console.error('Gagal memuat batas GeoJSON:', err);
                });

            // Fungsi Render Layer Poligon Kapanewon & Marker Fasilitas
            function renderMapLayers(analysis) {
                // Bersihkan layer sebelumnya
                if (geojsonLayer) {
                    map.removeLayer(geojsonLayer);
                }
                markersLayer.clearLayers();

                // Buat lookup status Kapanewon
                const kapLookup = {};
                analysis.kapanewon.forEach(k => {
                    kapLookup[k.kapanewon] = k;
                });

                // Style Choropleth Poligon Kapanewon
                if (geojsonData) {
                    geojsonLayer = L.geoJSON(geojsonData, {
                        style: function(feature) {
                            const name = feature.properties.kapanewon;
                            const info = kapLookup[name];
                            const count = info ? info.facilities_count : 0;
                            const status = info ? info.status : 'BLANK_SPOT';

                            let fillColor = '#DC2626'; // Blank Spot default
                            let fillOpacity = 0.22;
                            let color = '#B91C1C';
                            let weight = 1.5;

                            if (status === 'HIGH_DENSITY') {
                                fillColor = '#037940';
                                fillOpacity = 0.35;
                                color = '#024C28';
                                weight = 2.5;
                            } else if (status === 'MEDIUM_DENSITY') {
                                fillColor = '#2563EB';
                                fillOpacity = 0.28;
                                color = '#1D4ED8';
                                weight = 2;
                            } else if (status === 'ISOLATED_FACILITIES') {
                                fillColor = '#D97706';
                                fillOpacity = 0.25;
                                color = '#B45309';
                                weight = 1.8;
                            }

                            return {
                                color: color,
                                weight: weight,
                                opacity: 0.9,
                                fillColor: fillColor,
                                fillOpacity: fillOpacity,
                                dashArray: count === 0 ? '4, 4' : null,
                            };
                        },
                        onEachFeature: function(feature, layer) {
                            const name = feature.properties.kapanewon;
                            const info = kapLookup[name];
                            const count = info ? info.facilities_count : 0;
                            const statusLabel = info ? info.status_label : 'Belum Ada Fasilitas';

                            layer.bindTooltip(`
                                <div class="text-xs font-sans">
                                    <div class="font-black text-sm">${name}</div>
                                    <div class="font-bold text-slate-700">${statusLabel}</div>
                                    <div class="font-bold text-primary">${count} Fasilitas Terdata</div>
                                </div>
                            `, {
                                sticky: true
                            });

                            layer.on('mouseover', function() {
                                layer.setStyle({
                                    weight: 3.5,
                                    fillOpacity: 0.5
                                });
                            });
                            layer.on('mouseout', function() {
                                geojsonLayer.resetStyle(layer);
                            });
                            layer.on('click', function() {
                                map.fitBounds(layer.getBounds(), {
                                    padding: [20, 20]
                                });
                            });
                        }
                    }).addTo(map);
                }

                // Render Marker 43 Titik Fasilitas
                const clusterColors = {
                    1: '#037940',
                    2: '#2563EB',
                    3: '#D97706',
                    4: '#7C3AED',
                    5: '#DB2777',
                    6: '#0D9488',
                    7: '#4F46E5',
                };

                analysis.facilities.forEach(fac => {
                    const cId = fac.cluster_id;
                    const isNoise = cId === -1;
                    const color = isNoise ? '#64748B' : (clusterColors[cId] || '#0F0F0F');
                    const pointTypeLabel = fac.point_type === 'CORE' ? 'Titik Inti (Pusat)' : (fac
                        .point_type ===
                        'BORDER' ? 'Titik Penghubung (Tepian)' : 'Fasilitas Mandiri (Tersebar)');

                    // Marker Lingkaran Neobrutalism
                    const marker = L.circleMarker([fac.latitude, fac.longitude], {
                        radius: isNoise ? 6 : (fac.point_type === 'CORE' ? 8 : 7),
                        fillColor: color,
                        color: '#FFFFFF',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.9,
                    });

                    // Popup Detail Interaktif
                    const popupHtml = `
                        <div class="p-1 min-w-[240px]">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase text-white" style="background-color: ${color}">
                                    ${isNoise ? 'MANDIRI (TERSEBAR)' : 'KELOMPOK ' + cId} &middot; ${pointTypeLabel}
                                </span>
                                <span class="text-[10px] font-bold text-slate-500">${fac.kapanewon}</span>
                            </div>
                            <h4 class="font-black text-sm text-slate-900 leading-snug m-0 mb-1">${fac.place_name}</h4>
                            <p class="text-[11px] text-slate-600 mb-2 leading-relaxed m-0">${fac.address}</p>
                            <div class="border-t border-slate-200 pt-1.5 grid grid-cols-2 gap-1 text-[11px] font-bold">
                                <div><i class="fa-solid fa-wheelchair ${fac.accessibility.ramp ? 'text-green-600' : 'text-slate-300'} mr-1"></i> Ramp</div>
                                <div><i class="fa-solid fa-restroom ${fac.accessibility.toilet_difabel ? 'text-green-600' : 'text-slate-300'} mr-1"></i> Toilet</div>
                                <div><i class="fa-solid fa-square-parking ${fac.accessibility.parkir_difabel ? 'text-green-600' : 'text-slate-300'} mr-1"></i> Parkir</div>
                                <div><i class="fa-solid fa-elevator ${fac.accessibility.lift ? 'text-green-600' : 'text-slate-300'} mr-1"></i> Lift</div>
                            </div>
                        </div>
                    `;

                    marker.bindPopup(popupHtml, {
                        className: 'custom-popup'
                    });
                    markersLayer.addLayer(marker);
                });
            }

            // Sync Slider & Input
            const sliderEps = document.getElementById('sliderEps');
            const inputEps = document.getElementById('inputEps');
            const badgeEpsVal = document.getElementById('badgeEpsVal');

            const sliderMinPts = document.getElementById('sliderMinPts');
            const inputMinPts = document.getElementById('inputMinPts');
            const badgeMinPtsVal = document.getElementById('badgeMinPtsVal');
            const floatingSummaryBadge = document.getElementById('floatingSummaryBadge');

            function updateSummaryBadge() {
                if (floatingSummaryBadge) {
                    floatingSummaryBadge.textContent = `${inputEps.value}m • ${inputMinPts.value} pts`;
                }
            }

            sliderEps.addEventListener('input', function() {
                inputEps.value = this.value;
                badgeEpsVal.textContent = this.value + ' meter';
                updateSummaryBadge();
            });
            inputEps.addEventListener('input', function() {
                sliderEps.value = this.value;
                badgeEpsVal.textContent = this.value + ' meter';
                updateSummaryBadge();
            });

            sliderMinPts.addEventListener('input', function() {
                inputMinPts.value = this.value;
                badgeMinPtsVal.textContent = this.value + ' titik';
                updateSummaryBadge();
            });
            inputMinPts.addEventListener('input', function() {
                sliderMinPts.value = this.value;
                badgeMinPtsVal.textContent = this.value + ' titik';
                updateSummaryBadge();
            });

            // Toggle Minimize / Expand Floating Parameter Panel
            const btnToggleParams = document.getElementById('btnToggleParams');
            const paramPanelBody = document.getElementById('paramPanelBody');
            const iconToggleParams = document.getElementById('iconToggleParams');
            let isPanelOpen = true;

            btnToggleParams.addEventListener('click', function() {
                isPanelOpen = !isPanelOpen;
                if (isPanelOpen) {
                    paramPanelBody.classList.remove('hidden');
                    iconToggleParams.className = 'fa-solid fa-chevron-up text-xs';
                } else {
                    paramPanelBody.classList.add('hidden');
                    iconToggleParams.className = 'fa-solid fa-chevron-down text-xs';
                }
            });

            // Eksekusi AJAX Recalculation
            const btnRecalculate = document.getElementById('btnRecalculate');
            const iconRecalc = document.getElementById('iconRecalc');

            btnRecalculate.addEventListener('click', function() {
                const eps = parseFloat(inputEps.value) || 800;
                const minPts = parseInt(inputMinPts.value) || 3;

                iconRecalc.className = 'fa-solid fa-spinner fa-spin';
                btnRecalculate.disabled = true;

                fetch(`{{ route('admin.spatial.data') }}?eps=${eps}&min_pts=${minPts}`)
                    .then(res => res.json())
                    .then(res => {
                        if (res.success && res.data) {
                            currentData = res.data;
                            updateKpiCards(currentData);
                            renderMapLayers(currentData);
                            updateTables(currentData);
                            updateSummaryBadge();
                        }
                    })
                    .catch(err => {
                        alert('Gagal menghitung ulang DBSCAN: ' + err.message);
                    })
                    .finally(() => {
                        iconRecalc.className = 'fa-solid fa-calculator';
                        btnRecalculate.disabled = false;
                    });
            });

            // Reset Default
            document.getElementById('btnResetDefault').addEventListener('click', function() {
                sliderEps.value = 800;
                inputEps.value = 800;
                badgeEpsVal.textContent = '800 meter';

                sliderMinPts.value = 3;
                inputMinPts.value = 3;
                badgeMinPtsVal.textContent = '3 titik';
                updateSummaryBadge();

                btnRecalculate.click();
            });

            // Filter Chips Event Listener
            document.querySelectorAll('.btn-filter-kap').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.btn-filter-kap').forEach(b => {
                        b.classList.remove('active', 'bg-primary', 'text-white',
                            'border-primary',
                            'shadow-[2px_2px_0_0_var(--color-secondary)]');
                        b.classList.add('bg-surface', 'text-text-muted',
                            'border-border-solid');
                    });
                    this.classList.add('active', 'bg-primary', 'text-white', 'border-primary',
                        'shadow-[2px_2px_0_0_var(--color-secondary)]');
                    this.classList.remove('bg-surface', 'text-text-muted', 'border-border-solid');

                    currentFilter = this.getAttribute('data-filter');
                    filterKapanewonRows(currentFilter);
                });
            });

            function filterKapanewonRows(filter) {
                const rows = document.querySelectorAll('#tbodyKapanewon tr[data-status]');
                rows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    if (filter === 'ALL' || rowStatus === filter) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Update KPI Cards UI
            function updateKpiCards(data) {
                document.getElementById('kpiTotalFacilities').textContent = data.summary.total_facilities;
                document.getElementById('kpiTotalClusters').textContent = data.summary.total_clusters;
                document.getElementById('kpiCoreBorderPoints').textContent =
                    `${data.summary.total_core_points} Core &middot; ${data.summary.total_border_points} Border`;
                document.getElementById('kpiNoisePoints').textContent = data.summary.total_noise_points;
                document.getElementById('kpiNoiseEps').textContent = data.parameters.epsilon;
                document.getElementById('kpiBlankSpotCount').innerHTML =
                    `${data.summary.blank_spot_count} <span class="text-lg font-bold text-text-muted">/ 17</span>`;
                document.getElementById('kpiBlankSpotRatio').textContent = data.summary.blank_spot_ratio + '%';
            }

            // Update Tables UI
            function updateTables(data) {
                // Update Klaster Table
                const tbodyClusters = document.getElementById('tbodyClusters');
                if (data.clusters.length === 0) {
                    tbodyClusters.innerHTML = `
                        <tr>
                            <td colspan="7" class="py-8 text-center text-text-muted font-bold">
                                Tidak ada klaster yang terbentuk pada kombinasi parameter saat ini. Seluruh titik merupakan fasilitas tersebar mandiri.
                            </td>
                        </tr>
                    `;
                } else {
                    let html = '';
                    data.clusters.forEach(c => {
                        html += `
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3.5 px-4 font-black">
                                    <span class="px-3 py-1 rounded-full text-xs font-black text-white shadow-sm inline-block" style="background-color: ${c.color};">
                                        #${c.cluster_id}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-text-primary">${c.label}</td>
                                <td class="py-3.5 px-4 font-bold text-text-onsurface">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 border border-border-solid text-xs">
                                        ${c.dominant_kapanewon}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-black">
                                    <span class="px-3 py-1 rounded-full bg-slate-100 border border-slate-300 font-bold text-xs">${c.points_count} Fasilitas</span>
                                </td>
                                <td class="py-3.5 px-4 text-center text-green-700 font-bold">
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle text-[8px]"></i> ${c.core_count}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center text-blue-700 font-bold">
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fa-regular fa-circle text-[8px]"></i> ${c.border_count}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-xs text-text-muted">
                                    ${Number(c.center[0]).toFixed(5)}, ${Number(c.center[1]).toFixed(5)}
                                </td>
                            </tr>
                        `;
                    });
                    tbodyClusters.innerHTML = html;
                }

                // Update Kapanewon Table
                const tbodyKap = document.getElementById('tbodyKapanewon');
                let kapHtml = '';
                data.kapanewon.forEach((kap, idx) => {
                    let statusBadge = '';
                    if (kap.status === 'BLANK_SPOT') {
                        statusBadge =
                            `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-300"><i class="fa-solid fa-xmark"></i> Belum Ada Fasilitas (Defisit)</span>`;
                    } else if (kap.status === 'HIGH_DENSITY') {
                        statusBadge =
                            `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-900 border border-green-300"><i class="fa-solid fa-star"></i> Kawasan Kepadatan Tinggi</span>`;
                    } else if (kap.status === 'MEDIUM_DENSITY') {
                        statusBadge =
                            `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-900 border border-blue-300"><i class="fa-solid fa-circle-check"></i> Kawasan Fasilitas Memadai</span>`;
                    } else {
                        statusBadge =
                            `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300"><i class="fa-solid fa-location-crosshairs"></i> Fasilitas Tersebar Mandiri</span>`;
                    }

                    let detailsHtml = '';
                    if (kap.facilities_count > 0) {
                        detailsHtml = `
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-border-solid" title="Ramp"><i class="fa-solid fa-wheelchair text-primary"></i> Ramp: ${kap.details.ramp}</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-border-solid" title="Toilet Difabel"><i class="fa-solid fa-restroom text-primary"></i> Toilet: ${kap.details.toilet_difabel}</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-border-solid" title="Parkir Difabel"><i class="fa-solid fa-square-parking text-primary"></i> Parkir: ${kap.details.parkir_difabel}</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-border-solid" title="Lift"><i class="fa-solid fa-elevator text-primary"></i> Lift: ${kap.details.lift}</span>
                            </div>
                        `;
                    } else {
                        detailsHtml =
                            `<span class="text-red-500 font-bold italic">Tidak ada infrastruktur terdata</span>`;
                    }

                    kapHtml += `
                        <tr class="hover:bg-slate-50 transition-colors ${kap.facilities_count === 0 ? 'bg-red-50/40' : ''}" data-status="${kap.status}">
                            <td class="py-3.5 px-4 text-center font-bold text-text-muted">${idx + 1}</td>
                            <td class="py-3.5 px-4 font-bold text-text-onsurface">${kap.kapanewon}</td>
                            <td class="py-3.5 px-4 text-right font-mono text-xs text-text-muted">${Number(kap.luas_km2).toFixed(2)}</td>
                            <td class="py-3.5 px-4 text-center font-black">
                                <span class="px-3 py-1 rounded-full text-xs font-black ${kap.facilities_count > 0 ? 'bg-primary text-white' : 'bg-red-100 text-red-800 border border-red-300'}">
                                    ${kap.facilities_count}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">${statusBadge}</td>
                            <td class="py-3.5 px-4 text-center font-bold">
                                ${kap.facilities_count > 0 ? `
                                                    <div class="flex items-center justify-center gap-2.5">
                                                        <div class="w-20 bg-slate-200 rounded-full h-2.5 overflow-hidden border border-slate-300">
                                                            <div class="bg-primary h-2.5 rounded-full" style="width: ${kap.accessibility_percentage}%"></div>
                                                        </div>
                                                        <span class="text-xs font-mono font-bold">${kap.accessibility_percentage}%</span>
                                                    </div>
                                                ` : '<span class="text-xs text-text-muted font-mono font-bold">0.0%</span>'}
                            </td>
                            <td class="py-3.5 px-4 text-xs font-bold text-text-muted">${detailsHtml}</td>
                        </tr>
                    `;
                });
                tbodyKap.innerHTML = kapHtml;
                filterKapanewonRows(currentFilter);
            }
        });
    </script>
@endpush
