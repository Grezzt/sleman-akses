@extends('layouts.public')

@section('title', 'Sleman Akses - Peta Eksplorasi')
@section('hide_footer', true)

@push('styles')
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            overflow: hidden;
            /* Prevent scrolling on map page */
        }

        /* Map Container */
        #map {
            height: 100vh;
            /* Full height, UI will float above it */
            width: 100vw;
            z-index: 10;
            background: var(--color-background);
        }

        /* Leaflet Overrides */
        .leaflet-control-zoom {
            border: none !important;
            box-shadow: var(--shadow-solid) !important;
            border-radius: var(--radius-input) !important;
            margin-right: 20px !important;
            margin-bottom: 20px !important;
        }

        .leaflet-control-zoom a {
            color: var(--color-primary) !important;
            background: var(--color-surface) !important;
            border: 2px solid var(--color-border-solid) !important;
        }

        .leaflet-control-zoom a:hover {
            background: var(--color-background) !important;
        }

        .leaflet-control-zoom a:first-child {
            border-bottom: none !important;
            border-top-left-radius: var(--radius-input) !important;
            border-top-right-radius: var(--radius-input) !important;
        }

        .leaflet-control-zoom a:last-child {
            border-bottom-left-radius: var(--radius-input) !important;
            border-bottom-right-radius: var(--radius-input) !important;
        }

        .leaflet-bottom.leaflet-right {
            bottom: 120px;
            /* Make room for FABs */
        }

        /* Floating UI Containers */
        .floating-ui {
            position: absolute;
            z-index: 1000;
            pointer-events: none;
            /* Let clicks pass through to map */
        }

        .floating-ui>* {
            pointer-events: auto;
            /* Re-enable clicks for children */
        }

        /* Position below the floating navbar (navbar is top-6 + height ~ 100px) */
        .top-left-ui {
            top: 120px;
            left: 20px;
            max-width: 450px;
            width: calc(100% - 40px);
        }

        .bottom-right-ui {
            bottom: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: flex-end;
        }

        /* Side Panel */
        .side-panel-ui {
            top: 120px;
            right: 20px;
            bottom: 30px;
            width: 380px;
            max-width: calc(100vw - 40px);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.3s ease;
            transform: translateX(120%);
            opacity: 0;
        }

        .side-panel-ui.active {
            transform: translateX(0);
            opacity: 1;
            pointer-events: auto;
            /* Active panel should catch all clicks */
        }

        /* Custom DivIcon Marker */
        .sleman-marker {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            width: 48px;
            height: 60px;
            position: relative;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .sleman-marker:hover,
        .sleman-marker.active {
            transform: scale(1.15) translateY(-5px);
            z-index: 1000 !important;
        }

        .sleman-marker .pin {
            color: var(--color-primary);
            font-size: 48px;
            filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));
        }

        .sleman-marker .icon-circle {
            position: absolute;
            top: 7px;
            width: 24px;
            height: 24px;
            background: var(--color-surface);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0px 1px 3px rgba(0, 0, 0, 0.1);
        }

        .sleman-marker .icon-circle i {
            color: var(--color-primary);
            font-size: 12px;
        }

        /* Search Bar */
        .search-bar-container {
            display: flex;
            align-items: center;
            background: var(--color-surface);
            border: 2px solid var(--color-border-solid);
            border-radius: 28px;
            box-shadow: 0px 6px 16px rgba(0, 0, 0, 0.12);
            padding: 4px 16px;
            margin-bottom: 12px;
            transition: border-color 0.2s;
        }

        .search-bar-container:focus-within {
            border-color: var(--color-primary);
        }

        .search-bar-container input {
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
            padding: 10px 0;
            font-weight: 500;
            color: var(--color-text-onsurface);
        }

        /* Chips */
        .chips-wrapper {
            display: flex;
            flex-wrap: wrap;
            /* Tampilkan semuanya tanpa terpotong (wrap) */
            gap: 8px;
            padding-bottom: 8px;
            /* For shadow space */
        }

        .category-chip {
            white-space: nowrap;
            padding: 8px 16px;
            border-radius: 24px;
            background: var(--color-surface);
            border: 2px solid transparent;
            color: var(--color-text-onsurface);
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .category-chip.active {
            background: var(--color-primary);
            color: var(--color-surface);
            box-shadow: 4px 4px 0px 0px var(--color-secondary);
            transform: translate(-2px, -2px);
        }

        /* Facility Chip in Panel */
        .facility-chip {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            background: rgba(2, 92, 48, 0.1);
            /* Primary with 10% opacity */
            border-radius: 8px;
            color: var(--color-primary);
            font-size: 12px;
            font-weight: 700;
        }

        /* Pulse animation for User Location Halo */
        @keyframes user-pulse {
            0% {
                transform: scale(0.7);
                opacity: 0.9;
            }

            70% {
                transform: scale(2.2);
                opacity: 0;
            }

            100% {
                transform: scale(2.4);
                opacity: 0;
            }
        }

        .user-halo {
            animation: user-pulse 2s cubic-bezier(0.2, 0.8, 0.4, 1) infinite;
        }
    </style>
@endpush

@section('content')
    <div class="relative w-full h-screen">
        @if (empty($maptilerKey))
            <div class="absolute top-0 left-0 w-full z-[2000] bg-warning text-text-onsurface p-2 text-center font-bold">
                MAPTILER_KEY belum diatur. Silakan atur di .env
            </div>
        @endif

        <!-- Leaflet Map Container -->
        <div id="map"></div>

        <!-- Top Left UI: Search & Chips (Disembunyikan ketika panel terbuka agar tidak numpuk) -->
        <div class="floating-ui top-left-ui" id="topLeftPanel">
            <div class="search-bar-container">
                <i class="fas fa-search text-text-muted mr-3"></i>
                <input type="text" id="searchInput" placeholder="Cari fasilitas di Sleman...">
            </div>

            <div class="chips-wrapper" id="categoryChips">
                <div class="category-chip active" data-category="Semua">Semua</div>
                @foreach ($categories as $category)
                    <div class="category-chip" data-category="{{ $category['name'] }}">
                        {{ $category['name'] }}
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Bottom Right UI: FABs -->
        <div class="floating-ui bottom-right-ui" style="z-index: 999;">
            <!-- My Location -->
            <button id="btnMyLocation"
                class="w-12 h-12 bg-surface border-2 border-border-solid rounded-[16px] flex items-center justify-center text-primary shadow-[2px_2px_0px_0px_#D4D4D4] hover:-translate-y-1 hover:shadow-[4px_4px_0px_0px_#D4D4D4] transition-all"
                title="Lokasi Saya">
                <i class="fas fa-location-crosshairs text-xl"></i>
            </button>
        </div>

        <!-- Side Panel (Detail View) -->
        <div class="floating-ui side-panel-ui flex flex-col" id="detailPanel">
            <div
                class="w-full bg-surface border-2 border-border-solid rounded-[24px] shadow-solid flex flex-col overflow-hidden relative max-h-full">
                <button id="btnClosePanel"
                    class="absolute top-3 right-3 w-8 h-8 bg-surface/90 backdrop-blur border-2 border-border-solid rounded-full flex items-center justify-center text-text-onsurface z-[100] hover:bg-background transition-colors shadow-sm">
                    <i class="fas fa-times"></i>
                </button>

                <!-- Image Cover -->
                <div class="w-full h-56 bg-background relative shrink-0 group">
                    <img id="panelImage" src="" alt="Fasilitas"
                        class="w-full h-full object-cover hidden transition-opacity duration-300">
                    <div id="panelImagePlaceholder"
                        class="w-full h-full flex flex-col items-center justify-center text-text-muted bg-gray-200">
                        <i class="fas fa-image text-4xl mb-2"></i>
                        <span class="text-xs font-bold uppercase tracking-wider">Tanpa Foto</span>
                    </div>

                    <!-- Navigation Arrows -->
                    <button id="btnPrevPhoto"
                        class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black/80 transition-all opacity-0 group-hover:opacity-100 hidden z-10 shadow-sm cursor-pointer border border-white/20">
                        <i class="fas fa-chevron-left text-sm"></i>
                    </button>
                    <button id="btnNextPhoto"
                        class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black/80 transition-all opacity-0 group-hover:opacity-100 hidden z-10 shadow-sm cursor-pointer border border-white/20">
                        <i class="fas fa-chevron-right text-sm"></i>
                    </button>

                    <!-- Top Left Pill (Category) -->
                    <div id="panelCategoryPill"
                        class="absolute top-4 left-4 bg-secondary border-2 border-primary text-primary font-black text-xs px-3 py-1 rounded-full shadow-[2px_2px_0px_0px_#025C30] uppercase z-10">
                        KATEGORI
                    </div>

                    <!-- Top Right Pill (Status) -->
                    <div id="panelStatusPill"
                        class="absolute top-4 right-14 bg-primary text-surface font-bold text-xs px-3 py-1 rounded-full shadow-md uppercase z-10">
                        AKTIF
                    </div>

                    <!-- Bottom Center Pill (Photos indicator) -->
                    <div id="panelPhotosIndicator"
                        class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-text-primary/70 text-surface text-xs font-bold px-4 py-1.5 rounded-full flex items-center gap-2 backdrop-blur-sm hidden z-10 pointer-events-none">
                        <i class="fas fa-images"></i> <span id="panelPhotosText">1 / 1</span>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-5 overflow-y-auto flex-1 flex flex-col">
                    <!-- Title -->
                    <h2 id="panelTitle" class="text-[22px] font-black text-primary leading-tight mb-2">Nama Fasilitas</h2>

                    <!-- Address -->
                    <div class="flex items-start text-text-primary text-sm mb-1">
                        <i class="fas fa-location-dot mt-1 mr-2 text-text-primary"></i>
                        <span id="panelAddress" class="leading-relaxed font-medium">Alamat lengkap fasilitas.</span>
                    </div>

                    <!-- Distance Badge (Calculated from User GPS via Haversine) -->
                    <div id="panelDistanceBadge"
                        class="hidden items-center gap-1.5 text-xs font-bold text-primary bg-secondary/60 border border-primary/40 px-2.5 py-1 rounded-lg w-fit mt-1.5 mb-2 shadow-[1px_1px_0px_0px_#025C30]">
                        <i class="fas fa-route"></i> <span id="panelDistanceText">0 km dari lokasi Anda</span>
                    </div>

                    <!-- Reporter -->
                    <div class="text-xs text-text-muted mb-4">
                        Dilaporkan oleh <span id="panelReporter" class="font-medium">Anonim</span>
                    </div>

                    <!-- Facilities -->
                    <h3 class="font-bold text-primary mb-3 text-[15px]">Fitur Aksesibilitas</h3>
                    <div id="panelFacilities" class="grid grid-cols-2 gap-2 mb-6">
                        <!-- Facility chips injected here -->
                    </div>

                    <!-- Action Buttons (Neobrutalism Style) -->
                    <div class="mt-auto pt-2 flex flex-col gap-2.5">
                        <!-- Tombol Rute Navigasi Utama -->
                        <button id="btnRuteNavigasi"
                            class="w-full bg-secondary border-2 border-primary text-primary font-black py-3 rounded-xl flex justify-center items-center gap-2 shadow-[2px_2px_0px_0px_#025C30] hover:shadow-[4px_4px_0px_0px_#025C30] hover:-translate-y-0.5 transition-all text-sm cursor-pointer">
                            <i class="fas fa-location-arrow" id="btnRuteNavIcon"></i> <span id="btnRuteNavText">Rute
                                Navigasi</span>
                        </button>

                        <!-- Row: Opsi Navigasi Eksternal & Salin Tautan -->
                        <div class="grid grid-cols-2 gap-2">
                            <button id="btnOpenNavModal"
                                class="w-full bg-surface border-2 border-border-solid text-text-onsurface font-bold py-2.5 rounded-xl flex justify-center items-center gap-2 shadow-[2px_2px_0px_0px_#D4D4D4] hover:shadow-[3px_3px_0px_0px_#0F0F0F] hover:-translate-y-0.5 transition-all text-xs cursor-pointer">
                                <i class="fas fa-compass text-primary"></i> Opsi Navigasi
                            </button>
                            <button id="btnShareLocation"
                                class="w-full bg-surface border-2 border-border-solid text-text-onsurface font-bold py-2.5 rounded-xl flex justify-center items-center gap-2 shadow-[2px_2px_0px_0px_#D4D4D4] hover:shadow-[3px_3px_0px_0px_#0F0F0F] hover:-translate-y-0.5 transition-all text-xs cursor-pointer"
                                title="Salin Tautan Fasilitas Ini">
                                <i class="fas fa-share-nodes text-primary"></i> Bagikan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Route Summary Card (Neobrutalism) -->
        <div id="routeSummaryCard" class="hidden floating-ui"
            style="bottom: 24px; left: 50%; transform: translateX(-50%); z-index: 1002; width: calc(100% - 40px); max-width: 520px;">
            <div
                class="bg-surface border-2 border-border-solid rounded-[20px] shadow-[4px_4px_0px_0px_#0F0F0F] p-4 flex flex-col gap-3">
                <div class="flex items-center justify-between border-b border-border-solid/20 pb-2">
                    <div class="flex items-center gap-2">
                        <span
                            class="w-7 h-7 rounded-full bg-primary text-secondary flex items-center justify-center text-xs font-black shadow-[1px_1px_0px_0px_#025C30]">
                            <i class="fas fa-route"></i>
                        </span>
                        <div>
                            <h4 class="text-[11px] font-bold text-text-muted uppercase tracking-wider">Rute Menuju</h4>
                            <div id="routeSummaryDestName"
                                class="text-sm font-black text-text-onsurface truncate max-w-[220px] sm:max-w-[340px]">Nama
                                Fasilitas</div>
                        </div>
                    </div>
                    <button id="btnCloseRoute"
                        class="w-8 h-8 rounded-full border-2 border-border-solid bg-surface text-text-muted hover:text-text-onsurface hover:bg-background flex items-center justify-center transition-all shadow-[1px_1px_0px_0px_#D4D4D4] text-xs cursor-pointer"
                        title="Hapus Rute">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Route Metrics -->
                <div
                    class="flex items-center justify-around bg-background/60 border border-border-solid/30 rounded-xl py-2 px-3">
                    <div class="text-center">
                        <div class="text-[10px] font-bold text-text-muted">JARAK Tujuan</div>
                        <div id="routeDistanceText" class="text-base font-black text-primary">0 km</div>
                    </div>
                    <div class="h-8 w-px bg-border-solid/30"></div>
                    <div class="text-center">
                        <div class="text-[10px] font-bold text-text-muted">ESTIMASI WAKTU</div>
                        <div id="routeDurationText" class="text-base font-black text-text-onsurface">0 menit</div>
                    </div>
                    <div class="h-8 w-px bg-border-solid/30"></div>
                    <div class="text-center">
                        <div class="text-[10px] font-bold text-text-muted">MODE</div>
                        <div class="text-xs font-black text-primary flex items-center justify-center gap-1">
                            <i class="fas fa-car-side"></i> <span>Kendaraan</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons: Mulai Navigasi Langsung di Peta Web -->
                <div class="grid grid-cols-2 gap-2">
                    <button id="btnStartInAppNav"
                        class="w-full bg-secondary border-2 border-primary text-primary font-black py-2.5 rounded-xl flex justify-center items-center gap-2 shadow-[2px_2px_0px_0px_#025C30] hover:shadow-[3px_3px_0px_0px_#025C30] hover:-translate-y-0.5 transition-all text-xs text-center cursor-pointer">
                        <i class="fas fa-play"></i> Mulai Navigasi
                    </button>
                    <button id="btnOpenModalFromSummary"
                        class="w-full bg-surface border-2 border-border-solid text-text-onsurface font-bold py-2.5 rounded-xl flex justify-center items-center gap-2 shadow-[2px_2px_0px_0px_#D4D4D4] hover:shadow-[3px_3px_0px_0px_#0F0F0F] hover:-translate-y-0.5 transition-all text-xs cursor-pointer">
                        <i class="fas fa-ellipsis"></i> Opsi Lain
                    </button>
                </div>
            </div>
        </div>

        <!-- In-App Navigation HUD: Top Banner (Neobrutalism) -->
        <div id="navTopBanner"
            class="fixed top-[108px] md:top-[112px] left-1/2 -translate-x-1/2 z-[1100] hidden w-[calc(100%-32px)] max-w-lg transition-all duration-300">
            <div
                class="bg-primary text-surface border-2 border-border-solid rounded-[20px] shadow-[4px_4px_0px_0px_#0F0F0F] p-3.5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div
                        class="w-12 h-12 rounded-xl bg-secondary border-2 border-surface text-primary flex items-center justify-center shrink-0 shadow-[1px_1px_0px_0px_#025C30]">
                        <i id="navManeuverIcon" class="fa-solid fa-arrow-up text-primary text-2xl"></i>
                    </div>
                    <div class="truncate">
                        <div id="navManeuverDist" class="text-xs font-black text-secondary uppercase tracking-wider">LURUS
                            TERUS</div>
                        <div id="navManeuverRoad" class="text-sm font-black text-surface truncate">Mempersiapkan rute
                            navigasi...</div>
                    </div>
                </div>

                <!-- Voice Audio Toggle Button -->
                <button id="btnToggleVoice"
                    class="w-9 h-9 rounded-xl border-2 border-surface bg-surface text-primary hover:bg-secondary flex items-center justify-center transition-all shrink-0 shadow-[1px_1px_0px_0px_#025C30] text-xs cursor-pointer"
                    title="Suara Panduan Navigasi">
                    <i class="fas fa-volume-high" id="iconVoice"></i>
                </button>
            </div>
        </div>

        <!-- In-App Navigation HUD: Bottom Status Bar (Neobrutalism) -->
        <div id="navBottomBar"
            class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[1500] hidden w-[calc(100%-32px)] max-w-lg transition-all duration-300">
            <div
                class="bg-surface border-2 border-border-solid rounded-[20px] shadow-[4px_4px_0px_0px_#0F0F0F] p-3.5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-4">
                    <div>
                        <div class="text-[10px] font-bold text-text-muted uppercase">SISA JARAK</div>
                        <div id="navLiveDistance" class="text-lg font-black text-primary leading-tight">0 km</div>
                    </div>
                    <div class="h-8 w-px bg-border-solid/20"></div>
                    <div>
                        <div class="text-[10px] font-bold text-text-muted uppercase">ESTIMASI</div>
                        <div id="navLiveDuration" class="text-lg font-black text-text-onsurface leading-tight">0 mnt</div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Recenter Map Button -->
                    <button id="btnRecenterNav"
                        class="w-10 h-10 rounded-xl border-2 border-border-solid bg-surface text-text-onsurface hover:bg-background flex items-center justify-center shadow-[1px_1px_0px_0px_#D4D4D4] text-sm cursor-pointer"
                        title="Pusatkan Kamera ke Posisi Saya">
                        <i class="fas fa-crosshairs text-primary"></i>
                    </button>

                    <!-- Stop / End Navigation Button -->
                    <button id="btnStopNav"
                        class="bg-red-500 hover:bg-red-600 border-2 border-border-solid text-white font-black px-4 py-2 rounded-xl flex items-center gap-1.5 shadow-[2px_2px_0px_0px_#0F0F0F] text-xs cursor-pointer transition-all">
                        <i class="fas fa-stop"></i> Selesai
                    </button>
                </div>
            </div>
        </div>

        <!-- Neobrutalism Navigation Choice Modal -->
        <div id="navChoiceModal"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[2500] hidden flex items-center justify-center p-4">
            <div
                class="bg-surface border-2 border-border-solid rounded-[24px] shadow-[6px_6px_0px_0px_#0F0F0F] max-w-md w-full p-6 relative animate-in fade-in zoom-in-95 duration-200">
                <!-- Close Button -->
                <button id="btnCloseNavModal"
                    class="absolute top-4 right-4 w-8 h-8 rounded-full border-2 border-border-solid bg-surface text-text-onsurface hover:bg-background flex items-center justify-center shadow-[1px_1px_0px_0px_#D4D4D4] cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>

                <!-- Header -->
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-secondary border-2 border-primary text-primary flex items-center justify-center text-xl shadow-[2px_2px_0px_0px_#025C30]">
                        <i class="fas fa-diamond-turn-right"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-lg text-text-onsurface">Pilih Mode Navigasi</h3>
                        <p class="text-xs text-text-muted font-medium truncate max-w-[260px]" id="modalDestSubtext">Menuju
                            Fasilitas Publik</p>
                    </div>
                </div>

                <!-- List Options -->
                <div class="flex flex-col gap-2.5 mb-5">
                    <!-- Mulai Navigasi Langsung di Peta Web -->
                    <button id="modalOptInAppNav"
                        class="flex items-center justify-between p-3.5 rounded-xl border-2 border-primary bg-secondary/30 hover:bg-secondary/60 transition-all group shadow-[2px_2px_0px_0px_#025C30] hover:shadow-[3px_3px_0px_0px_#025C30] hover:-translate-y-0.5 text-left cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-lg bg-secondary border-2 border-primary flex items-center justify-center text-primary text-base font-black shadow-[1px_1px_0px_0px_#025C30]">
                                <i class="fas fa-location-arrow"></i>
                            </div>
                            <div>
                                <div class="font-black text-sm text-primary">Mulai Navigasi Langsung</div>
                                <div class="text-[11px] text-text-muted">Navigasi langsung di peta web Sleman Akses tanpa
                                    keluar aplikasi</div>
                            </div>
                        </div>
                        <i class="fas fa-play text-xs text-primary"></i>
                    </button>

                    <!-- Google Maps (Eksternal) -->
                    <a id="modalOptGmaps" href="#" target="_blank"
                        class="flex items-center justify-between p-3.5 rounded-xl border-2 border-border-solid bg-background hover:bg-surface hover:border-primary transition-all group shadow-[2px_2px_0px_0px_#D4D4D4] hover:shadow-[3px_3px_0px_0px_#025C30] hover:-translate-y-0.5">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-lg bg-surface border border-border-solid flex items-center justify-center text-[#4285F4] text-lg font-black">
                                <i class="fab fa-google"></i>
                            </div>
                            <div>
                                <div
                                    class="font-black text-sm text-text-onsurface group-hover:text-primary transition-colors">
                                    Buka Google Maps</div>
                                <div class="text-[11px] text-text-muted">Buka aplikasi eksternal Google Maps di tab baru
                                </div>
                            </div>
                        </div>
                        <i class="fas fa-arrow-up-right-from-square text-xs text-text-muted group-hover:text-primary"></i>
                    </a>

                    <!-- Copy GPS Coordinates & Address -->
                    <button id="modalBtnCopyCoord"
                        class="flex items-center justify-between p-3.5 rounded-xl border-2 border-border-solid bg-background hover:bg-surface hover:border-primary transition-all group shadow-[2px_2px_0px_0px_#D4D4D4] hover:shadow-[3px_3px_0px_0px_#025C30] hover:-translate-y-0.5 text-left cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-lg bg-surface border border-border-solid flex items-center justify-center text-primary text-lg font-black">
                                <i class="fas fa-copy"></i>
                            </div>
                            <div>
                                <div
                                    class="font-black text-sm text-text-onsurface group-hover:text-primary transition-colors">
                                    Salin Koordinat GPS & Alamat</div>
                                <div class="text-[11px] text-text-muted" id="modalCoordText">-7.xxx, 110.xxx</div>
                            </div>
                        </div>
                        <i class="fas fa-clipboard text-xs text-text-muted group-hover:text-primary"></i>
                    </button>
                </div>

                <!-- Footer info -->
                <div class="text-[11px] text-text-muted text-center flex items-center justify-center gap-1.5 font-medium">
                    <i class="fas fa-shield-halved text-primary"></i> Navigasi didukung oleh OpenStreetMap & OSRM Engine
                </div>
            </div>
        </div>

        <!-- Neobrutalism System Response Modal: GPS Access Required -->
        <div id="gpsPromptModal"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[2600] hidden flex items-center justify-center p-4">
            <div
                class="bg-surface border-2 border-border-solid rounded-[24px] shadow-[6px_6px_0px_0px_#0F0F0F] max-w-sm w-full p-6 relative animate-in fade-in zoom-in-95 duration-200 text-center">
                <!-- Close Button -->
                <button id="btnCloseGpsModal"
                    class="absolute top-4 right-4 w-8 h-8 rounded-full border-2 border-border-solid bg-surface text-text-onsurface hover:bg-background flex items-center justify-center shadow-[1px_1px_0px_0px_#D4D4D4] cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>

                <!-- Mascot Asset -->
                <div class="w-24 h-24 mx-auto mb-3 flex items-center justify-center">
                    <img src="{{ asset('maskot-side-eye.svg') }}" alt="Maskot Sleman Akses"
                        class="w-full h-full object-contain">
                </div>

                <!-- Title & Description -->
                <h3 class="font-black text-xl text-text-onsurface mb-2">Lokasi GPS Belum Aktif</h3>
                <p id="gpsModalDesc" class="text-xs text-text-muted leading-relaxed mb-4">
                    Sleman Akses membutuhkan akses lokasi GPS untuk memandu navigasi langsung dari posisi Anda. Silakan
                    aktifkan GPS atau izinkan peramban mengakses lokasi.
                </p>

                <!-- Guide Box -->
                <div
                    class="bg-background border-2 border-border-solid rounded-xl p-3 text-left mb-5 text-[11px] text-text-onsurface shadow-[2px_2px_0px_0px_#D4D4D4]">
                    <div class="font-bold flex items-center gap-1.5 text-primary mb-1">
                        <i class="fas fa-lightbulb"></i> Tips & Cara Mengizinkan:
                    </div>
                    <div class="leading-normal text-text-muted">
                        1. Klik ikon setelan / gembok di sebelah kiri bilah URL browser.<br>
                        2. Pastikan <b>Lokasi (Location)</b> diubah ke <b>Izinkan (Allow)</b>.<br>
                        3. Klik tombol <b>Coba Deteksi Lokasi Lagi</b> di bawah.
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-2">
                    <button id="btnRetryGps"
                        class="w-full bg-secondary border-2 border-primary text-primary font-black py-3 rounded-xl flex justify-center items-center gap-2 shadow-[2px_2px_0px_0px_#025C30] hover:shadow-[3px_3px_0px_0px_#025C30] hover:-translate-y-0.5 transition-all text-sm cursor-pointer">
                        <i class="fas fa-location-crosshairs"></i> Coba Deteksi Lokasi Lagi
                    </button>
                    <a id="btnGpsFallbackGmaps" href="#" target="_blank"
                        class="w-full bg-surface border-2 border-border-solid text-text-onsurface font-bold py-2.5 rounded-xl flex justify-center items-center gap-2 shadow-[2px_2px_0px_0px_#D4D4D4] hover:shadow-[2px_2px_0px_0px_#0F0F0F] transition-all text-xs cursor-pointer">
                        <i class="fab fa-google text-[#4285F4]"></i> Buka Langsung di Google Maps
                    </a>
                </div>
            </div>
        </div>

        <!-- Neobrutalism System Response Modal: Arrival Success -->
        <div id="arrivalSuccessModal"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[2700] hidden flex items-center justify-center p-4">
            <div
                class="bg-surface border-2 border-primary rounded-[24px] shadow-[6px_6px_0px_0px_#025C30] max-w-sm w-full p-6 relative animate-in fade-in zoom-in-95 duration-200 text-center">
                <!-- Mascot Asset -->
                <div class="w-24 h-24 mx-auto mb-3 flex items-center justify-center">
                    <img src="{{ asset('laporan sukses.svg') }}" alt="Sukses" class="w-full h-full object-contain">
                </div>

                <!-- Title & Description -->
                <h3 class="font-black text-xl text-primary mb-1">Anda Telah Sampai!</h3>
                <p class="text-xs text-text-muted leading-relaxed mb-4">
                    Selamat! Anda telah tiba di fasilitas inklusif <br><b id="arrivalDestName"
                        class="text-text-onsurface text-sm font-black">Nama Fasilitas</b>.
                </p>

                <button id="btnFinishNav"
                    class="w-full bg-secondary border-2 border-primary text-primary font-black py-3 rounded-xl flex justify-center items-center gap-2 shadow-[2px_2px_0px_0px_#025C30] hover:shadow-[3px_3px_0px_0px_#025C30] hover:-translate-y-0.5 transition-all text-sm cursor-pointer">
                    <i class="fas fa-check-circle"></i> Selesai Navigasi
                </button>
            </div>
        </div>

        <!-- Toast Notification (Neobrutalism) -->
        <div id="mapToast"
            class="fixed top-24 left-1/2 -translate-x-1/2 z-[3000] hidden items-center gap-2.5 bg-surface border-2 border-border-solid py-2.5 px-4 rounded-xl shadow-[4px_4px_0px_0px_#0F0F0F] text-xs font-bold text-text-onsurface transition-all">
            <i class="fas fa-circle-check text-primary text-sm" id="toastIcon"></i>
            <span id="toastMsg">Pesan notifikasi</span>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            const markersData = @json($markers);
            const maptilerKey = @json($maptilerKey);
            const categoriesData = @json($categories);

            // --- MAP INITIALIZATION ---
            const map = L.map('map', {
                zoomControl: false,
                attributionControl: false
            }).setView([-7.716, 110.355], 13);

            L.control.zoom({
                position: 'bottomright'
            }).addTo(map);

            const tileUrl = maptilerKey ?
                `https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=${maptilerKey}` :
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

            L.tileLayer(tileUrl, {
                maxZoom: 20,
            }).addTo(map);

            const cluster = L.markerClusterGroup({
                showCoverageOnHover: false,
                maxClusterRadius: 40,
                iconCreateFunction: function(c) {
                    return L.divIcon({
                        html: `<div style="background:var(--color-primary);color:white;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:bold;border:3px solid white;box-shadow:0 2px 5px rgba(0,0,0,0.3)">${c.getChildCount()}</div>`,
                        className: 'custom-cluster',
                        iconSize: L.point(40, 40)
                    });
                }
            });

            // --- STATE ---
            let activeCategory = 'Semua';
            let searchQuery = '';
            let activeMarkerId = null;
            let activeMarkerData = null; // Menyimpan data fasilitas yang sedang aktif dibuka
            let mapMarkers = [];
            let currentPhotoUrls = [];
            let currentPhotoIndex = 0;

            // Navigasi & Lokasi State
            let userLocation = null; // { lat, lng }
            let userMarker = null;
            let routeOuterLayer = null;
            let routeInnerLayer = null;

            // Pulihkan lokasi dari sessionStorage agar pengguna tidak ditanya berulang kali
            try {
                const cachedLat = sessionStorage.getItem('sleman_user_lat');
                const cachedLng = sessionStorage.getItem('sleman_user_lng');
                if (cachedLat && cachedLng) {
                    userLocation = {
                        lat: parseFloat(cachedLat),
                        lng: parseFloat(cachedLng)
                    };
                }
            } catch (e) {}

            // --- HAVERSINE DISTANCE CALCULATION (TA-2 FOUNDATION) ---
            const calculateHaversineDistance = (lat1, lon1, lat2, lon2) => {
                const R = 6371; // Radius bumi dalam km
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a =
                    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLon / 2) * Math.sin(dLon / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c; // Hasil dalam kilometer
            };

            // --- TOAST FEEDBACK ---
            let toastTimeout = null;
            const showToast = (message, iconClass = 'fas fa-circle-check text-primary') => {
                const toast = document.getElementById('mapToast');
                const msg = document.getElementById('toastMsg');
                const icon = document.getElementById('toastIcon');
                if (!toast || !msg) return;
                msg.textContent = message;
                if (icon) icon.className = iconClass + ' text-sm';
                toast.classList.remove('hidden');
                toast.classList.add('flex');
                clearTimeout(toastTimeout);
                toastTimeout = setTimeout(() => {
                    toast.classList.add('hidden');
                    toast.classList.remove('flex');
                }, 3000);
            };

            // --- CUSTOM ICONS ---
            const createCustomIcon = (iconUrl) => {
                const html = `
                <div class="sleman-marker">
                    <i class="fas fa-map-marker pin"></i>
                    <div class="icon-circle">
                        <img src="${iconUrl || '/parkir-difabel.svg'}" alt="icon" style="width: 14px; height: 14px; object-fit: contain;">
                    </div>
                </div>
            `;
                return L.divIcon({
                    html: html,
                    className: '',
                    iconSize: [48, 60],
                    iconAnchor: [24, 60],
                });
            };

            const createUserIcon = () => {
                const html = `
                <div style="position:relative; width: 28px; height: 28px; display:flex; align-items:center; justify-content:center;">
                    <div class="user-halo" style="position:absolute; width: 28px; height: 28px; border-radius: 50%; background: rgba(3, 121, 64, 0.4);"></div>
                    <div style="width: 16px; height: 16px; border-radius: 50%; background: #4285F4; border: 3px solid #FFFFFF; box-shadow: 0 3px 6px rgba(0,0,0,0.35); z-index: 2;"></div>
                </div>
            `;
                return L.divIcon({
                    html: html,
                    className: '',
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });
            };

            // Jika lokasi sudah ada dari sessionStorage, pasang user marker di peta
            if (userLocation && userLocation.lat && userLocation.lng) {
                userMarker = L.marker([userLocation.lat, userLocation.lng], {
                    icon: createUserIcon(),
                    zIndexOffset: 1000
                }).addTo(map);
                userMarker.bindTooltip("<b>Lokasi Anda</b>", {
                    permanent: false,
                    direction: 'top'
                });
            }

            // --- DETAIL PANEL LOGIC ---
            const panel = document.getElementById('detailPanel');
            const btnClosePanel = document.getElementById('btnClosePanel');

            // Photo Slider Event Listeners
            document.getElementById('btnPrevPhoto').addEventListener('click', () => {
                if (currentPhotoUrls.length > 1) {
                    currentPhotoIndex = (currentPhotoIndex - 1 + currentPhotoUrls.length) % currentPhotoUrls
                        .length;
                    document.getElementById('panelImage').src = currentPhotoUrls[currentPhotoIndex];
                    document.getElementById('panelPhotosText').textContent =
                        `${currentPhotoIndex + 1} / ${currentPhotoUrls.length}`;
                }
            });

            document.getElementById('btnNextPhoto').addEventListener('click', () => {
                if (currentPhotoUrls.length > 1) {
                    currentPhotoIndex = (currentPhotoIndex + 1) % currentPhotoUrls.length;
                    document.getElementById('panelImage').src = currentPhotoUrls[currentPhotoIndex];
                    document.getElementById('panelPhotosText').textContent =
                        `${currentPhotoIndex + 1} / ${currentPhotoUrls.length}`;
                }
            });

            // Update Distance Badge Helper
            const updateDistanceBadge = (destLat, destLng) => {
                const badge = document.getElementById('panelDistanceBadge');
                const text = document.getElementById('panelDistanceText');
                if (!badge || !text) return;

                if (userLocation && userLocation.lat && userLocation.lng) {
                    const distKm = calculateHaversineDistance(userLocation.lat, userLocation.lng, destLat,
                        destLng);
                    const formatted = distKm < 1 ? `${Math.round(distKm * 1000)} m` : `${distKm.toFixed(1)} km`;
                    text.textContent = `${formatted} dari lokasi Anda`;
                    badge.classList.remove('hidden');
                    badge.classList.add('inline-flex');
                } else {
                    badge.classList.add('hidden');
                    badge.classList.remove('inline-flex');
                }
            };

            const showDetailPanel = (markerData) => {
                activeMarkerId = markerData.id;
                activeMarkerData = markerData;

                // Populate Basic Data
                document.getElementById('panelTitle').textContent = markerData.place_name;
                document.getElementById('panelAddress').textContent = markerData.address;
                document.getElementById('panelReporter').textContent = markerData.reporter_name || 'Anonim';
                document.getElementById('panelCategoryPill').textContent = markerData.primary_category ||
                    'LOKASI';
                document.getElementById('panelStatusPill').textContent = markerData.status || 'AKTIF';

                // Update Distance Badge jika GPS pengguna aktif
                updateDistanceBadge(markerData.lat, markerData.lng);

                // Photo Handling
                const imgEl = document.getElementById('panelImage');
                const placeholderEl = document.getElementById('panelImagePlaceholder');
                const photosIndEl = document.getElementById('panelPhotosIndicator');
                const photosTextEl = document.getElementById('panelPhotosText');
                const btnPrev = document.getElementById('btnPrevPhoto');
                const btnNext = document.getElementById('btnNextPhoto');

                if (markerData.photo_url) {
                    currentPhotoUrls = markerData.photo_url.split(',').map(u => u.trim());
                    currentPhotoIndex = 0;

                    imgEl.src = currentPhotoUrls[0];
                    imgEl.classList.remove('hidden');
                    placeholderEl.classList.add('hidden');

                    if (currentPhotoUrls.length > 1) {
                        photosIndEl.classList.remove('hidden');
                        btnPrev.classList.remove('hidden');
                        btnNext.classList.remove('hidden');
                        photosTextEl.textContent = `1 / ${currentPhotoUrls.length}`;
                    } else {
                        photosIndEl.classList.add('hidden');
                        btnPrev.classList.add('hidden');
                        btnNext.classList.add('hidden');
                    }
                } else {
                    currentPhotoUrls = [];
                    imgEl.src = '';
                    imgEl.classList.add('hidden');
                    placeholderEl.classList.remove('hidden');
                    photosIndEl.classList.add('hidden');
                    btnPrev.classList.add('hidden');
                    btnNext.classList.add('hidden');
                }

                // Facilities Grid
                const facContainer = document.getElementById('panelFacilities');
                facContainer.innerHTML = '';

                const facilities = markerData.facilities || [];
                if (facilities.length > 0) {
                    facilities.forEach(fac => {
                        const iconUrl = fac.icon_url;
                        if (fac.available) {
                            facContainer.innerHTML += `
                            <div class="bg-secondary border-2 border-primary text-primary font-bold text-sm rounded-xl px-3 py-2.5 flex items-center gap-2 shadow-[2px_2px_0px_0px_#025C30]">
                                <img src="${iconUrl}" class="w-5 h-5 object-contain opacity-80" alt="icon">
                                <span class="truncate">${fac.name}</span>
                            </div>
                        `;
                        } else {
                            facContainer.innerHTML += `
                            <div class="bg-surface border-2 border-border-solid text-text-muted font-bold text-sm rounded-xl px-3 py-2.5 flex items-center gap-2 shadow-[2px_2px_0px_0px_#D4D4D4]">
                                <img src="${iconUrl}" class="w-5 h-5 object-contain" style="filter: grayscale(100%) opacity(40%);" alt="icon">
                                <span class="truncate">${fac.name}</span>
                            </div>
                        `;
                        }
                    });
                } else {
                    facContainer.innerHTML =
                        `<span class="text-sm text-text-muted italic col-span-2">Belum ada data fasilitas.</span>`;
                }

                panel.classList.add('active');

                // Fly to marker offset to left (to fit right panel)
                const mapCenterOffset = map.project([markerData.lat, markerData.lng], 16).subtract([180, 0]);
                const targetLatLng = map.unproject(mapCenterOffset, 16);
                map.flyTo(targetLatLng, 16, {
                    duration: 0.8
                });
            };

            btnClosePanel.addEventListener('click', () => {
                panel.classList.remove('active');
                activeMarkerId = null;
                activeMarkerData = null;
            });

            // Navigasi Langsung (Live In-App Navigation) State
            let isLiveNavigating = false;
            let watchNavId = null;
            let followCamera = true;
            let voiceEnabled = true;
            let navSteps = [];
            let currentStepIndex = 0;
            let lastSpokenInstruction = '';

            // --- SPEECH SYNTHESIS HELPER (WEB VOICE GUIDANCE) ---
            const speakInstruction = (text) => {
                if (!voiceEnabled || !('speechSynthesis' in window)) return;
                try {
                    window.speechSynthesis.cancel();
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'id-ID';
                    utterance.rate = 1.0;
                    window.speechSynthesis.speak(utterance);
                } catch (err) {
                    console.warn('Speech synthesis error:', err);
                }
            };

            // --- MANEUVER PARSER (OSRM STEPS TO INDONESIAN & DIRECTIONAL ARROWS) ---
            const getManeuverDetails = (step) => {
                if (!step || !step.maneuver) {
                    return {
                        icon: 'fa-solid fa-arrow-up',
                        instruction: 'Lanjut ikuti jalan raya',
                        road: 'Jalan Raya'
                    };
                }
                const m = step.maneuver;
                const mod = (m.modifier || '').toLowerCase();
                const type = (m.type || '').toLowerCase();
                const road = step.name ? step.name : 'jalan berikutnya';

                let icon = 'fa-solid fa-arrow-up';
                let action = 'Lurus terus ke';

                if (type === 'turn') {
                    if (mod.includes('right')) {
                        icon = 'fa-solid fa-arrow-right';
                        action = mod.includes('sharp') ? 'Belok tajam ke kanan menuju' : (mod.includes(
                            'slight') ? 'Sedikit ke kanan ke' : 'Belok kanan ke');
                    } else if (mod.includes('left')) {
                        icon = 'fa-solid fa-arrow-left';
                        action = mod.includes('sharp') ? 'Belok tajam ke kiri menuju' : (mod.includes(
                            'slight') ? 'Sedikit ke kiri ke' : 'Belok kiri ke');
                    } else if (mod.includes('uturn')) {
                        icon = 'fa-solid fa-rotate-left';
                        action = 'Putar balik di';
                    } else if (mod.includes('straight')) {
                        icon = 'fa-solid fa-arrow-up';
                        action = 'Lurus terus ke';
                    }
                } else if (type === 'new name' || type === 'continue') {
                    if (mod.includes('right')) {
                        icon = 'fa-solid fa-arrow-right';
                        action = 'Ambil kanan ke';
                    } else if (mod.includes('left')) {
                        icon = 'fa-solid fa-arrow-left';
                        action = 'Ambil kiri ke';
                    } else {
                        icon = 'fa-solid fa-arrow-up';
                        action = 'Lanjut lurus ke';
                    }
                } else if (type === 'depart') {
                    icon = 'fa-solid fa-arrow-up';
                    action = 'Mulai melaju ke arah';
                } else if (type === 'arrive') {
                    icon = 'fa-solid fa-location-dot';
                    action = 'Tujuan ada di';
                } else if (type === 'roundabout' || type === 'rotary') {
                    icon = 'fa-solid fa-arrows-spin';
                    action = 'Masuk bundaran ke';
                } else if (type === 'fork') {
                    if (mod.includes('right')) {
                        icon = 'fa-solid fa-arrow-right';
                        action = 'Ambil percabangan kanan ke';
                    } else if (mod.includes('left')) {
                        icon = 'fa-solid fa-arrow-left';
                        action = 'Ambil percabangan kiri ke';
                    } else {
                        icon = 'fa-solid fa-arrow-up';
                        action = 'Ambil jalur tengah ke';
                    }
                } else if (type === 'end of road') {
                    if (mod.includes('right')) {
                        icon = 'fa-solid fa-arrow-right';
                        action = 'Ujung jalan, belok kanan ke';
                    } else if (mod.includes('left')) {
                        icon = 'fa-solid fa-arrow-left';
                        action = 'Ujung jalan, belok kiri ke';
                    } else {
                        icon = 'fa-solid fa-arrow-up';
                        action = 'Ujung jalan ke';
                    }
                } else if (type.includes('ramp')) {
                    if (mod.includes('right')) {
                        icon = 'fa-solid fa-arrow-right';
                        action = 'Masuk lajur kanan ke';
                    } else if (mod.includes('left')) {
                        icon = 'fa-solid fa-arrow-left';
                        action = 'Masuk lajur kiri ke';
                    } else {
                        icon = 'fa-solid fa-arrow-up';
                        action = 'Masuk lajur ke';
                    }
                } else {
                    // Fallback berdasarkan modifier
                    if (mod.includes('right')) {
                        icon = 'fa-solid fa-arrow-right';
                        action = 'Ke arah kanan menuju';
                    } else if (mod.includes('left')) {
                        icon = 'fa-solid fa-arrow-left';
                        action = 'Ke arah kiri menuju';
                    } else {
                        icon = 'fa-solid fa-arrow-up';
                        action = 'Lurus terus ke';
                    }
                }

                return {
                    icon: icon,
                    instruction: `${action} ${road}`,
                    road: road
                };
            };

            // --- ROUTING ENGINE (OSRM IN-MAP PREVIEW) ---
            const clearRoute = (showToastNotice = false) => {
                if (routeOuterLayer) {
                    map.removeLayer(routeOuterLayer);
                    routeOuterLayer = null;
                }
                if (routeInnerLayer) {
                    map.removeLayer(routeInnerLayer);
                    routeInnerLayer = null;
                }
                const summaryCard = document.getElementById('routeSummaryCard');
                if (summaryCard) summaryCard.classList.add('hidden');

                navSteps = [];
                currentStepIndex = 0;

                // Buka kembali panel detail jika ada fasilitas yang aktif dan tidak sedang live navigation
                if (activeMarkerData && !isLiveNavigating) {
                    panel.classList.add('active');
                }

                if (showToastNotice) {
                    showToast('Rute di peta berhasil dibersihkan', 'fas fa-info-circle text-primary');
                }
            };

            document.getElementById('btnCloseRoute').addEventListener('click', () => {
                clearRoute(true);
            });

            const fetchAndDrawRoute = async (origin, destination, placeName) => {
                clearRoute();
                const btn = document.getElementById('btnRuteNavigasi');
                const icon = document.getElementById('btnRuteNavIcon');
                const text = document.getElementById('btnRuteNavText');

                if (icon) icon.className = 'fas fa-spinner fa-spin';
                if (text) text.textContent = 'Mencari Rute...';
                if (btn) btn.disabled = true;

                const url =
                    `https://router.project-osrm.org/route/v1/driving/${origin.lng},${origin.lat};${destination.lng},${destination.lat}?overview=full&geometries=geojson&steps=true`;

                try {
                    const resp = await fetch(url);
                    if (!resp.ok) throw new Error('Network error saat request OSRM');
                    const data = await resp.json();

                    if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                        const route = data.routes[0];
                        const coords = route.geometry.coordinates.map(c => [c[1], c[
                        0]]); // [lng, lat] -> [lat, lng]

                        // Simpan steps untuk live navigation
                        navSteps = (route.legs && route.legs[0] && route.legs[0].steps) ? route.legs[0]
                            .steps : [];
                        currentStepIndex = 0;

                        // Outer stroke solid hitam (Neobrutalism border)
                        routeOuterLayer = L.polyline(coords, {
                            color: '#0F0F0F',
                            weight: 8,
                            opacity: 0.95,
                            lineCap: 'round',
                            lineJoin: 'round'
                        }).addTo(map);

                        // Inner line hijau primary Sleman Akses
                        routeInnerLayer = L.polyline(coords, {
                            color: '#037940',
                            weight: 5,
                            opacity: 1,
                            lineCap: 'round',
                            lineJoin: 'round'
                        }).addTo(map);

                        // Auto fit bounds agar user dan rute terlihat menyeluruh
                        map.fitBounds(routeOuterLayer.getBounds(), {
                            padding: [60, 60]
                        });

                        // Metrik rute
                        const distKm = (route.distance / 1000).toFixed(1);
                        const durMin = Math.max(1, Math.round(route.duration / 60));

                        // Sembunyikan panel detail fasilitas agar peta lapang dan tidak overlap
                        panel.classList.remove('active');

                        // Tampilkan di Summary Card
                        document.getElementById('routeSummaryDestName').textContent = placeName;
                        document.getElementById('routeDistanceText').textContent = `${distKm} km`;
                        document.getElementById('routeDurationText').textContent = `${durMin} mnt`;

                        const summaryCard = document.getElementById('routeSummaryCard');
                        if (summaryCard) summaryCard.classList.remove('hidden');

                        showToast(`Rute ditemukan: ${distKm} km (~${durMin} menit)`);
                    } else {
                        throw new Error('OSRM tidak menemukan rute berkendara');
                    }
                } catch (err) {
                    console.warn('Gagal memuat rute OSRM, mengalihkan ke modal navigasi:', err);
                    showToast('Rute lokal tidak tersedia, membuka opsi eksternal...',
                        'fas fa-info-circle text-blue-500');
                    openNavigationModal(destination, placeName);
                } finally {
                    if (icon) icon.className = 'fas fa-location-arrow';
                    if (text) text.textContent = 'Rute Navigasi';
                    if (btn) btn.disabled = false;
                }
            };

            // --- ROBUST GEOLOCATION HANDLER (ANTI-RACE CONDITION & DUAL-TIER FALLBACK) ---
            let activeLocationPromise = null;

            const getUserLocation = (forceRefresh = false) => {
                // Jika sudah ada userLocation yang valid, LANGSUNG SELESAI (jangan invalidate / minta ulang)
                if (!forceRefresh && userLocation && userLocation.lat && userLocation.lng) {
                    return Promise.resolve(userLocation);
                }

                // Cegah race conditions jika request sedang berlangsung
                if (activeLocationPromise) {
                    return activeLocationPromise;
                }

                activeLocationPromise = new Promise((resolve, reject) => {
                    if (!("geolocation" in navigator)) {
                        activeLocationPromise = null;
                        const err = new Error("Browser Anda tidak mendukung Geolocation API");
                        err.code = 0;
                        return reject(err);
                    }

                    const applyPosition = (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        userLocation = {
                            lat,
                            lng
                        };

                        // Simpan ke sessionStorage agar sesi pengguna tetap mengingat lokasi
                        try {
                            sessionStorage.setItem('sleman_user_lat', String(lat));
                            sessionStorage.setItem('sleman_user_lng', String(lng));
                        } catch (e) {}

                        if (userMarker) {
                            userMarker.setLatLng([lat, lng]);
                        } else {
                            userMarker = L.marker([lat, lng], {
                                icon: createUserIcon(),
                                zIndexOffset: 1000
                            }).addTo(map);
                            userMarker.bindTooltip("<b>Lokasi Anda</b>", {
                                permanent: false,
                                direction: 'top'
                            });
                        }

                        if (activeMarkerData) {
                            updateDistanceBadge(activeMarkerData.lat, activeMarkerData.lng);
                        }

                        activeLocationPromise = null;
                        resolve(userLocation);
                    };

                    // Percobaan 1: enableHighAccuracy: false terlebih dahulu (responsif < 1 detik, stabil di Wi-Fi / PC / Laptop)
                    navigator.geolocation.getCurrentPosition(
                        applyPosition,
                        (err1) => {
                            console.warn('Deteksi cepat lokasi gagal, mencoba opsi sekunder:',
                            err1);
                            if (err1.code === 1) {
                                activeLocationPromise = null;
                                return reject(err1); // Ditolak peramban
                            }
                            // Percobaan 2: enableHighAccuracy: true jika hardware mendukung
                            navigator.geolocation.getCurrentPosition(
                                applyPosition,
                                (err2) => {
                                    activeLocationPromise = null;
                                    reject(err2);
                                }, {
                                    enableHighAccuracy: true,
                                    timeout: 8000,
                                    maximumAge: 60000
                                }
                            );
                        }, {
                            enableHighAccuracy: false,
                            timeout: 7000,
                            maximumAge: 300000
                        }
                    );
                });

                return activeLocationPromise;
            };

            // --- GPS PROMPT MODAL (NEOBRUTALISM SYSTEM RESPONSE) ---
            const gpsModal = document.getElementById('gpsPromptModal');
            const btnCloseGpsModal = document.getElementById('btnCloseGpsModal');

            const openGpsModal = (destCoords, placeName, specificErrorMsg = null) => {
                if (!gpsModal) return;
                const fallbackGmaps = document.getElementById('btnGpsFallbackGmaps');
                if (fallbackGmaps && destCoords) {
                    fallbackGmaps.href =
                        `https://www.google.com/maps/dir/?api=1&destination=${destCoords.lat},${destCoords.lng}`;
                }
                const descEl = document.getElementById('gpsModalDesc');
                if (descEl) {
                    if (specificErrorMsg) {
                        descEl.innerHTML =
                            `<span class="text-red-600 font-bold">${specificErrorMsg}.</span> Silakan pastikan izin lokasi peramban telah diizinkan dan klik coba deteksi lagi.`;
                    } else {
                        descEl.textContent =
                            'Sleman Akses membutuhkan akses lokasi GPS untuk memandu navigasi langsung dari posisi Anda. Silakan aktifkan GPS atau izinkan peramban mengakses lokasi.';
                    }
                }
                gpsModal.classList.remove('hidden');
            };

            const closeGpsModal = () => {
                if (gpsModal) gpsModal.classList.add('hidden');
            };

            btnCloseGpsModal.addEventListener('click', closeGpsModal);
            gpsModal.addEventListener('click', (e) => {
                if (e.target === gpsModal) closeGpsModal();
            });

            document.getElementById('btnRetryGps').addEventListener('click', async () => {
                const retryBtn = document.getElementById('btnRetryGps');
                const originalHtml = retryBtn.innerHTML;
                retryBtn.disabled = true;
                retryBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mendeteksi Sinyal...';
                showToast('Mendeteksi sinyal lokasi...', 'fas fa-spinner fa-spin text-primary');

                try {
                    const loc = await getUserLocation(true);
                    closeGpsModal();
                    showToast('Lokasi berhasil dideteksi!');
                    if (activeMarkerData) {
                        fetchAndDrawRoute(loc, {
                            lat: activeMarkerData.lat,
                            lng: activeMarkerData.lng
                        }, activeMarkerData.place_name);
                    }
                } catch (err) {
                    console.warn('Retry GPS gagal:', err);
                    let errorMsg = 'Lokasi belum dapat dideteksi';
                    if (err.code === 1) {
                        errorMsg = 'Izin lokasi belum diizinkan atau diblokir oleh peramban';
                    } else if (err.code === 2) {
                        errorMsg = 'Sensor GPS fisik tidak ditemukan pada perangkat Anda (PC/Laptop)';
                    } else if (err.code === 3) {
                        errorMsg = 'Pencarian sinyal lokasi GPS melebihi batas waktu (timeout)';
                    }
                    showToast(errorMsg, 'fas fa-triangle-exclamation text-warning');
                    const descEl = document.getElementById('gpsModalDesc');
                    if (descEl) {
                        descEl.innerHTML =
                            `<span class="text-red-600 font-bold">${errorMsg}.</span> Pastikan izin lokasi di peramban telah diizinkan dan klik coba lagi.`;
                    }
                } finally {
                    retryBtn.disabled = false;
                    retryBtn.innerHTML = originalHtml;
                }
            });

            // --- BUTTON RUTE NAVIGASI CLICK ---
            document.getElementById('btnRuteNavigasi').addEventListener('click', async () => {
                if (!activeMarkerData) return;
                const destCoords = {
                    lat: activeMarkerData.lat,
                    lng: activeMarkerData.lng
                };
                const placeName = activeMarkerData.place_name || 'Fasilitas Publik';

                // 1. JIKA LOKASI SUDAH ADA (DARI SESI ATAU DETEKSI BACKGROUND): LANGSUNG BUAT RUTE!
                if (userLocation && userLocation.lat && userLocation.lng) {
                    fetchAndDrawRoute(userLocation, destCoords, placeName);
                    return;
                }

                // 2. JIKA LOKASI BELUM ADA: AMBIL LOKASI SECARA HALUS
                const icon = document.getElementById('btnRuteNavIcon');
                const text = document.getElementById('btnRuteNavText');
                if (icon) icon.className = 'fas fa-spinner fa-spin';
                if (text) text.textContent = 'Mendeteksi Lokasi...';

                try {
                    const loc = await getUserLocation();
                    // 3. LOKASI BERHASIL DIDAPAT: LANGSUNG ARAHKAN KE RUTE TANPA MEMBUKA MODAL!
                    fetchAndDrawRoute(loc, destCoords, placeName);
                } catch (e) {
                    console.warn('Geolocation ditolak atau tidak aktif:', e);
                    let errStr = 'Lokasi GPS belum aktif';
                    if (e.code === 1) errStr = 'Izin lokasi peramban belum aktif';
                    if (e.code === 2) errStr = 'Sensor GPS fisik tidak tersedia pada perangkat';
                    if (e.code === 3) errStr = 'Pencarian sinyal GPS timeout';
                    // HANYA jika benar-benar gagal mendapatkan lokasi, buka modal
                    openGpsModal(destCoords, placeName, errStr);
                } finally {
                    if (icon) icon.className = 'fas fa-location-arrow';
                    if (text) text.textContent = 'Rute Navigasi';
                }
            });

            // --- LIVE IN-APP NAVIGATION IMPLEMENTATION ---
            const topBanner = document.getElementById('navTopBanner');
            const bottomBar = document.getElementById('navBottomBar');
            const arrivalModal = document.getElementById('arrivalSuccessModal');

            const startInAppNavigation = async () => {
                if (!activeMarkerData) return;
                const destCoords = {
                    lat: activeMarkerData.lat,
                    lng: activeMarkerData.lng
                };
                const placeName = activeMarkerData.place_name || 'Fasilitas Publik';

                // Verifikasi lokasi pengguna
                if (!userLocation) {
                    try {
                        await getUserLocation();
                    } catch (err) {
                        let errStr = 'Lokasi GPS belum aktif';
                        if (err.code === 1) errStr = 'Izin lokasi peramban belum aktif';
                        if (err.code === 2) errStr = 'Sensor GPS fisik tidak tersedia pada perangkat';
                        if (err.code === 3) errStr = 'Pencarian sinyal GPS timeout';
                        openGpsModal(destCoords, placeName, errStr);
                        return;
                    }
                }

                // Jika rute belum diambil, ambil terlebih dahulu
                if (navSteps.length === 0) {
                    await fetchAndDrawRoute(userLocation, destCoords, placeName);
                }

                // Aktifkan mode navigasi
                isLiveNavigating = true;
                followCamera = true;
                currentStepIndex = 0;
                lastSpokenInstruction = '';

                // Sembunyikan elemen UI peta normal
                panel.classList.remove('active');
                document.getElementById('routeSummaryCard').classList.add('hidden');
                document.getElementById('topLeftPanel').classList.add('hidden');
                const myLocationFab = document.getElementById('btnMyLocation');
                if (myLocationFab && myLocationFab.parentElement) {
                    myLocationFab.parentElement.classList.add('hidden');
                }
                closeNavigationModal();

                // Tampilkan HUD navigasi
                topBanner.classList.remove('hidden');
                bottomBar.classList.remove('hidden');

                // Zoom in dan pusatkan kamera ke user
                map.setView([userLocation.lat, userLocation.lng], 17, {
                    animate: true
                });

                // Pengumuman awal suara
                speakInstruction(
                `Navigasi menuju ${placeName} dimulai. Ikuti rute yang tertera pada peta.`);

                // Update status HUD awal
                updateNavigationProgress(userLocation.lat, userLocation.lng);

                // Pasang watchPosition untuk live tracking GPS
                if ("geolocation" in navigator) {
                    watchNavId = navigator.geolocation.watchPosition(
                        position => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            userLocation = {
                                lat,
                                lng
                            };

                            if (userMarker) {
                                userMarker.setLatLng([lat, lng]);
                            }

                            if (followCamera && isLiveNavigating) {
                                map.setView([lat, lng], 17, {
                                    animate: true
                                });
                            }

                            updateNavigationProgress(lat, lng);
                        },
                        err => {
                            console.warn('Watch navigation error:', err);
                        }, {
                            enableHighAccuracy: true,
                            maximumAge: 2000,
                            timeout: 5000
                        }
                    );
                }

                showToast('Navigasi langsung aktif di dalam aplikasi', 'fas fa-play text-primary');
            };

            const updateNavigationProgress = (userLat, userLng) => {
                if (!activeMarkerData || !isLiveNavigating) return;

                // Hitung sisa jarak ke tujuan akhir
                const distToDestKm = calculateHaversineDistance(userLat, userLng, activeMarkerData.lat,
                    activeMarkerData.lng);
                const distToDestMeters = distToDestKm * 1000;

                // Cek apakah sudah sampai (radius < 35 meter)
                if (distToDestMeters <= 35) {
                    if (watchNavId) {
                        navigator.geolocation.clearWatch(watchNavId);
                        watchNavId = null;
                    }
                    document.getElementById('arrivalDestName').textContent = activeMarkerData.place_name;
                    arrivalModal.classList.remove('hidden');
                    speakInstruction(`Selamat, Anda telah tiba di tujuan: ${activeMarkerData.place_name}`);
                    return;
                }

                // Kemajuan langkah demi langkah (Step-by-step advance)
                if (navSteps.length > 0 && currentStepIndex < navSteps.length) {
                    const step = navSteps[currentStepIndex];
                    if (step.maneuver && step.maneuver.location) {
                        const stepLng = step.maneuver.location[0];
                        const stepLat = step.maneuver.location[1];
                        const distToStepMeters = calculateHaversineDistance(userLat, userLng, stepLat,
                            stepLng) * 1000;

                        // Jika berada dalam radius 25 meter dari titik belok dan bukan langkah terakhir
                        if (distToStepMeters < 25 && currentStepIndex < navSteps.length - 1) {
                            currentStepIndex++;
                        }
                    }

                    // Render instruksi belokan di Top Banner
                    const activeStep = navSteps[currentStepIndex];
                    const maneuverInfo = getManeuverDetails(activeStep);

                    let distToManeuverMeters = 50;
                    if (activeStep.maneuver && activeStep.maneuver.location) {
                        distToManeuverMeters = calculateHaversineDistance(userLat, userLng, activeStep.maneuver
                            .location[1], activeStep.maneuver.location[0]) * 1000;
                    }
                    const formattedManeuverDist = distToManeuverMeters < 1000 ?
                        `${Math.round(distToManeuverMeters)} M` :
                        `${(distToManeuverMeters / 1000).toFixed(1)} KM`;

                    document.getElementById('navManeuverIcon').className =
                        `${maneuverInfo.icon} text-primary text-2xl`;
                    document.getElementById('navManeuverDist').textContent = `DALAM ${formattedManeuverDist}`;
                    document.getElementById('navManeuverRoad').textContent = maneuverInfo.instruction;

                    // Suarakan instruksi jika baru
                    const voiceSpeechText =
                        `Dalam ${Math.round(distToManeuverMeters)} meter, ${maneuverInfo.instruction}`;
                    if (voiceSpeechText !== lastSpokenInstruction && distToManeuverMeters > 30) {
                        lastSpokenInstruction = voiceSpeechText;
                        speakInstruction(voiceSpeechText);
                    }
                }

                // Update Bottom Status Bar
                const formattedTotalDist = distToDestKm < 1 ?
                    `${Math.round(distToDestMeters)} m` :
                    `${distToDestKm.toFixed(1)} km`;
                const estimatedMin = Math.max(1, Math.round(distToDestKm / 30 *
                60)); // Asumsi rerata laju perkotaan 30 km/jam

                document.getElementById('navLiveDistance').textContent = formattedTotalDist;
                document.getElementById('navLiveDuration').textContent = `${estimatedMin} mnt`;
            };

            const stopInAppNavigation = () => {
                if (watchNavId) {
                    navigator.geolocation.clearWatch(watchNavId);
                    watchNavId = null;
                }
                isLiveNavigating = false;
                followCamera = true;
                window.speechSynthesis?.cancel();

                // Sembunyikan HUD navigasi & modal kedatangan
                topBanner.classList.add('hidden');
                bottomBar.classList.add('hidden');
                arrivalModal.classList.add('hidden');

                // Kembalikan UI normal
                document.getElementById('topLeftPanel').classList.remove('hidden');
                const myLocationFab = document.getElementById('btnMyLocation');
                if (myLocationFab && myLocationFab.parentElement) {
                    myLocationFab.parentElement.classList.remove('hidden');
                }
                if (activeMarkerData) {
                    document.getElementById('routeSummaryCard').classList.remove('hidden');
                    if (routeOuterLayer) {
                        map.fitBounds(routeOuterLayer.getBounds(), {
                            padding: [60, 60]
                        });
                    }
                }

                showToast('Navigasi selesai diakhiri', 'fas fa-flag-checkered text-primary');
            };

            // Event listener stop navigasi
            document.getElementById('btnStopNav').addEventListener('click', stopInAppNavigation);
            document.getElementById('btnFinishNav').addEventListener('click', stopInAppNavigation);

            // Event listener pusatkan kamera saat navigasi
            document.getElementById('btnRecenterNav').addEventListener('click', () => {
                followCamera = true;
                if (userLocation) {
                    map.setView([userLocation.lat, userLocation.lng], 17, {
                        animate: true
                    });
                    showToast('Kamera kembali mengikuti pergerakan Anda');
                }
            });

            // Matikan camera following jika user sengaja menggeser peta
            map.on('dragstart', () => {
                if (isLiveNavigating) {
                    followCamera = false;
                }
            });

            // Toggle suara panduan navigasi
            document.getElementById('btnToggleVoice').addEventListener('click', () => {
                voiceEnabled = !voiceEnabled;
                const icon = document.getElementById('iconVoice');
                if (voiceEnabled) {
                    icon.className = 'fas fa-volume-high';
                    showToast('Suara panduan navigasi aktif');
                    speakInstruction('Suara panduan aktif');
                } else {
                    icon.className = 'fas fa-volume-xmark';
                    window.speechSynthesis?.cancel();
                    showToast('Suara panduan navigasi dimatikan');
                }
            });

            // Trigger Mulai Navigasi Langsung dari Route Summary Card & Modal
            document.getElementById('btnStartInAppNav').addEventListener('click', startInAppNavigation);
            document.getElementById('modalOptInAppNav').addEventListener('click', startInAppNavigation);

            // --- NAVIGATION CHOICE MODAL LOGIC ---
            const navModal = document.getElementById('navChoiceModal');
            const btnCloseNavModal = document.getElementById('btnCloseNavModal');

            const openNavigationModal = (coords, placeName) => {
                if (!navModal) return;

                document.getElementById('modalDestSubtext').textContent = placeName;
                document.getElementById('modalCoordText').textContent =
                    `${coords.lat.toFixed(6)}, ${coords.lng.toFixed(6)}`;

                // URL Google Maps (hanya sebagai opsi cadangan eksternal)
                let gmapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${coords.lat},${coords.lng}`;
                if (userLocation) {
                    gmapsUrl += `&origin=${userLocation.lat},${userLocation.lng}`;
                }
                document.getElementById('modalOptGmaps').href = gmapsUrl;

                navModal.classList.remove('hidden');
            };

            const closeNavigationModal = () => {
                if (navModal) navModal.classList.add('hidden');
            };

            btnCloseNavModal.addEventListener('click', closeNavigationModal);
            navModal.addEventListener('click', (e) => {
                if (e.target === navModal) closeNavigationModal();
            });

            document.getElementById('btnOpenNavModal').addEventListener('click', () => {
                if (!activeMarkerData) return;
                openNavigationModal({
                    lat: activeMarkerData.lat,
                    lng: activeMarkerData.lng
                }, activeMarkerData.place_name);
            });

            document.getElementById('btnOpenModalFromSummary').addEventListener('click', () => {
                if (!activeMarkerData) return;
                openNavigationModal({
                    lat: activeMarkerData.lat,
                    lng: activeMarkerData.lng
                }, activeMarkerData.place_name);
            });

            // Salin Koordinat & Alamat di Modal
            document.getElementById('modalBtnCopyCoord').addEventListener('click', () => {
                if (!activeMarkerData) return;
                const textToCopy =
                    `${activeMarkerData.place_name}\nAlamat: ${activeMarkerData.address}\nKoordinat: ${activeMarkerData.lat}, ${activeMarkerData.lng}\nGoogle Maps: https://www.google.com/maps/search/?api=1&query=${activeMarkerData.lat},${activeMarkerData.lng}`;
                navigator.clipboard.writeText(textToCopy).then(() => {
                    showToast('Koordinat & alamat disalin!');
                    closeNavigationModal();
                }).catch(() => {
                    showToast('Gagal menyalin ke clipboard', 'fas fa-times text-danger');
                });
            });

            // Salin Tautan Fasilitas (Bagikan)
            document.getElementById('btnShareLocation').addEventListener('click', () => {
                if (!activeMarkerData) return;
                const shareUrl = `${window.location.origin}/map?id=${activeMarkerData.id}`;
                navigator.clipboard.writeText(shareUrl).then(() => {
                    showToast('Tautan fasilitas disalin ke clipboard!');
                }).catch(() => {
                    showToast('Gagal menyalin tautan fasilitas', 'fas fa-times text-danger');
                });
            });

            // --- FILTERING & RENDERING ---
            const renderMarkers = () => {
                cluster.clearLayers();
                mapMarkers = [];

                const filteredData = markersData.filter(marker => {
                    const cats = Array.isArray(marker.categories) ? marker.categories : [];
                    const matchCategory = activeCategory === 'Semua' || cats.includes(activeCategory);

                    const query = searchQuery.toLowerCase();
                    const matchSearch = query === '' ||
                        (marker.place_name && marker.place_name.toLowerCase().includes(query)) ||
                        (marker.address && marker.address.toLowerCase().includes(query));

                    return matchCategory && matchSearch;
                });

                filteredData.forEach(marker => {
                    let displayIconUrl = marker.primary_icon_url;

                    if (activeCategory !== 'Semua') {
                        const matchedFac = marker.facilities.find(f => f.name === activeCategory && f
                            .available);
                        if (matchedFac && matchedFac.icon_url) {
                            displayIconUrl = matchedFac.icon_url;
                        }
                    }

                    const icon = createCustomIcon(displayIconUrl);
                    const leafletMarker = L.marker([marker.lat, marker.lng], {
                        icon: icon
                    });

                    leafletMarker.on('click', () => showDetailPanel(marker));

                    cluster.addLayer(leafletMarker);
                    mapMarkers.push({
                        data: marker,
                        layer: leafletMarker
                    });
                });

                map.addLayer(cluster);
            };

            // --- SEARCH BAR ---
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchQuery = e.target.value;
                    renderMarkers();
                    if (searchQuery !== '') {
                        if (mapMarkers.length > 0) {
                            const bounds = L.latLngBounds(mapMarkers.map(m => [m.data.lat, m.data
                                .lng
                            ]));
                            map.fitBounds(bounds, {
                                padding: [50, 50],
                                maxZoom: 15
                            });
                        }
                    }
                }, 500);
            });

            // --- CHIPS ---
            const chips = document.querySelectorAll('.category-chip');
            chips.forEach(chip => {
                chip.addEventListener('click', (e) => {
                    chips.forEach(c => c.classList.remove('active'));
                    e.target.classList.add('active');

                    activeCategory = e.target.getAttribute('data-category');
                    renderMarkers();
                });
            });

            // --- MY LOCATION ---
            document.getElementById('btnMyLocation').addEventListener('click', async () => {
                const btn = document.getElementById('btnMyLocation');
                const icon = btn.querySelector('i');
                icon.className = 'fas fa-spinner fa-spin text-xl';

                try {
                    const loc = await getUserLocation(true);
                    map.flyTo([loc.lat, loc.lng], 15, {
                        duration: 1
                    });
                    showToast('Lokasi Anda berhasil dideteksi');
                } catch (err) {
                    console.warn('Gagal mendeteksi lokasi dari btnMyLocation:', err);
                    let errStr = 'Gagal mendeteksi lokasi';
                    if (err.code === 1) errStr = 'Izin lokasi belum diizinkan pada peramban';
                    if (err.code === 2) errStr = 'Perangkat PC/Laptop tidak memiliki sensor GPS fisik';
                    if (err.code === 3) errStr = 'Pencarian sinyal GPS timeout';
                    showToast(errStr, 'fas fa-triangle-exclamation text-warning');
                    openGpsModal(activeMarkerData ? {
                        lat: activeMarkerData.lat,
                        lng: activeMarkerData.lng
                    } : null, activeMarkerData ? activeMarkerData.place_name : '', errStr);
                } finally {
                    icon.className = 'fas fa-location-crosshairs text-xl';
                }
            });

            // --- AUTO DETECT LOCATION IN BACKGROUND (JIKA IZIN PERAMBAN SUDAH AKTIF) ---
            if ("geolocation" in navigator) {
                if (navigator.permissions && navigator.permissions.query) {
                    navigator.permissions.query({
                        name: 'geolocation'
                    }).then(permissionStatus => {
                        if (permissionStatus.state === 'granted') {
                            getUserLocation().catch(() => {});
                        }
                        permissionStatus.onchange = () => {
                            if (permissionStatus.state === 'granted') {
                                getUserLocation(true).catch(() => {});
                            }
                        };
                    }).catch(() => {
                        if (!userLocation) getUserLocation().catch(() => {});
                    });
                } else if (!userLocation) {
                    getUserLocation().catch(() => {});
                }
            }

            // Initialize markers
            renderMarkers();

            // --- DEEP LINK / AUTO SELECT MARKER VIA QUERY PARAM (?id=...) ---
            const urlParams = new URLSearchParams(window.location.search);
            const targetId = urlParams.get('id');
            if (targetId) {
                const foundMarker = markersData.find(m => String(m.id) === String(targetId));
                if (foundMarker) {
                    setTimeout(() => {
                        showDetailPanel(foundMarker);
                    }, 400);
                }
            }
        });
    </script>
@endpush
