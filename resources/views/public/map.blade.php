@extends('layouts.public')

@section('title', 'Sleman Akses - Peta Eksplorasi')
@section('hide_footer', true)

@push('styles')
<style>
    body, html {
        margin: 0;
        padding: 0;
        overflow: hidden; /* Prevent scrolling on map page */
    }

    /* Map Container */
    #map {
        height: 100vh; /* Full height, UI will float above it */
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
        bottom: 120px; /* Make room for FABs */
    }

    /* Floating UI Containers */
    .floating-ui {
        position: absolute;
        z-index: 1000;
        pointer-events: none; /* Let clicks pass through to map */
    }
    .floating-ui > * {
        pointer-events: auto; /* Re-enable clicks for children */
    }

    /* Position below the floating navbar (navbar is top-6 + height ~ 100px) */
    .top-left-ui { top: 120px; left: 20px; max-width: 450px; width: calc(100% - 40px); }
    .bottom-right-ui { bottom: 20px; right: 20px; display: flex; flex-direction: column; gap: 12px; align-items: flex-end; }
    
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
        pointer-events: auto; /* Active panel should catch all clicks */
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
    .sleman-marker:hover, .sleman-marker.active {
        transform: scale(1.15) translateY(-5px);
        z-index: 1000 !important;
    }
    .sleman-marker .pin {
        color: var(--color-primary);
        font-size: 48px;
        filter: drop-shadow(0px 4px 4px rgba(0,0,0,0.25));
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
        box-shadow: inset 0px 1px 3px rgba(0,0,0,0.1);
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
        box-shadow: 0px 6px 16px rgba(0,0,0,0.12);
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
        flex-wrap: wrap; /* Tampilkan semuanya tanpa terpotong (wrap) */
        gap: 8px;
        padding-bottom: 8px; /* For shadow space */
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
        background: rgba(2, 92, 48, 0.1); /* Primary with 10% opacity */
        border-radius: 8px;
        color: var(--color-primary);
        font-size: 12px;
        font-weight: 700;
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
        <button id="btnMyLocation" class="w-12 h-12 bg-surface border-2 border-border-solid rounded-[16px] flex items-center justify-center text-primary shadow-[2px_2px_0px_0px_#D4D4D4] hover:-translate-y-1 hover:shadow-[4px_4px_0px_0px_#D4D4D4] transition-all" title="Lokasi Saya">
            <i class="fas fa-location-crosshairs text-xl"></i>
        </button>
    </div>

    <!-- Side Panel (Detail View) -->
    <div class="floating-ui side-panel-ui flex flex-col" id="detailPanel">
        <div class="w-full bg-surface border-2 border-border-solid rounded-[24px] shadow-solid flex flex-col overflow-hidden relative max-h-full">
            <button id="btnClosePanel" class="absolute top-3 right-3 w-8 h-8 bg-surface/90 backdrop-blur border-2 border-border-solid rounded-full flex items-center justify-center text-text-onsurface z-[100] hover:bg-background transition-colors shadow-sm">
                <i class="fas fa-times"></i>
            </button>

            <!-- Image Cover -->
            <div class="w-full h-56 bg-background relative shrink-0 group">
                <img id="panelImage" src="" alt="Fasilitas" class="w-full h-full object-cover hidden transition-opacity duration-300">
                <div id="panelImagePlaceholder" class="w-full h-full flex flex-col items-center justify-center text-text-muted bg-gray-200">
                    <i class="fas fa-image text-4xl mb-2"></i>
                    <span class="text-xs font-bold uppercase tracking-wider">Tanpa Foto</span>
                </div>
                
                <!-- Navigation Arrows -->
                <button id="btnPrevPhoto" class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black/80 transition-all opacity-0 group-hover:opacity-100 hidden z-10 shadow-sm cursor-pointer border border-white/20">
                    <i class="fas fa-chevron-left text-sm"></i>
                </button>
                <button id="btnNextPhoto" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black/80 transition-all opacity-0 group-hover:opacity-100 hidden z-10 shadow-sm cursor-pointer border border-white/20">
                    <i class="fas fa-chevron-right text-sm"></i>
                </button>
                
                <!-- Top Left Pill (Category) -->
                <div id="panelCategoryPill" class="absolute top-4 left-4 bg-secondary border-2 border-primary text-primary font-black text-xs px-3 py-1 rounded-full shadow-[2px_2px_0px_0px_#025C30] uppercase z-10">
                    KATEGORI
                </div>
                
                <!-- Top Right Pill (Status) -->
                <div id="panelStatusPill" class="absolute top-4 right-14 bg-primary text-surface font-bold text-xs px-3 py-1 rounded-full shadow-md uppercase z-10">
                    AKTIF
                </div>

                <!-- Bottom Center Pill (Photos indicator) -->
                <div id="panelPhotosIndicator" class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-text-primary/70 text-surface text-xs font-bold px-4 py-1.5 rounded-full flex items-center gap-2 backdrop-blur-sm hidden z-10 pointer-events-none">
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
                
                <!-- Reporter -->
                <div class="text-xs text-text-muted mb-4">
                    Dilaporkan oleh <span id="panelReporter" class="font-medium">Anonim</span>
                </div>

                <!-- Facilities -->
                <h3 class="font-bold text-primary mb-3 text-[15px]">Fitur Aksesibilitas</h3>
                <div id="panelFacilities" class="grid grid-cols-2 gap-2 mb-6">
                    <!-- Facility chips injected here -->
                </div>
                
                <!-- Spacer to push buttons to bottom -->
                <div class="mt-auto pt-2 flex flex-col gap-4">
                    <button class="w-full bg-secondary border-2 border-primary text-primary font-black py-3 rounded-xl flex justify-center items-center gap-2 shadow-[2px_2px_0px_0px_#025C30] hover:shadow-[4px_4px_0px_0px_#025C30] hover:-translate-y-0.5 transition-all text-sm cursor-pointer">
                        <i class="fas fa-location-arrow"></i> Rute Navigasi
                    </button>
                </div>
            </div>
        </div>
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
        // Menghilangkan zoom control default dan attribution watermark
        const map = L.map('map', { 
            zoomControl: false,
            attributionControl: false 
        }).setView([-7.716, 110.355], 13);
        
        L.control.zoom({ position: 'bottomright' }).addTo(map);

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
        let mapMarkers = []; // Simpan referensi layer marker
        let currentPhotoUrls = [];
        let currentPhotoIndex = 0;

        // --- CUSTOM DIV ICON ---
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

        // --- DETAIL PANEL LOGIC ---
        const panel = document.getElementById('detailPanel');
        const btnClosePanel = document.getElementById('btnClosePanel');
        
        // Photo Slider Event Listeners
        document.getElementById('btnPrevPhoto').addEventListener('click', () => {
            if (currentPhotoUrls.length > 1) {
                currentPhotoIndex = (currentPhotoIndex - 1 + currentPhotoUrls.length) % currentPhotoUrls.length;
                document.getElementById('panelImage').src = currentPhotoUrls[currentPhotoIndex];
                document.getElementById('panelPhotosText').textContent = `${currentPhotoIndex + 1} / ${currentPhotoUrls.length}`;
            }
        });
        
        document.getElementById('btnNextPhoto').addEventListener('click', () => {
            if (currentPhotoUrls.length > 1) {
                currentPhotoIndex = (currentPhotoIndex + 1) % currentPhotoUrls.length;
                document.getElementById('panelImage').src = currentPhotoUrls[currentPhotoIndex];
                document.getElementById('panelPhotosText').textContent = `${currentPhotoIndex + 1} / ${currentPhotoUrls.length}`;
            }
        });

        const showDetailPanel = (markerData) => {
            activeMarkerId = markerData.id;
            
            // Populate Basic Data
            document.getElementById('panelTitle').textContent = markerData.place_name;
            document.getElementById('panelAddress').textContent = markerData.address;
            document.getElementById('panelReporter').textContent = markerData.reporter_name || 'Anonim';
            document.getElementById('panelCategoryPill').textContent = markerData.primary_category || 'LOKASI';
            document.getElementById('panelStatusPill').textContent = markerData.status || 'AKTIF';
            
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
                
                if(currentPhotoUrls.length > 1) {
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
                facContainer.innerHTML = `<span class="text-sm text-text-muted italic col-span-2">Belum ada data fasilitas.</span>`;
            }

            // Update Link Details
            const linkDetail = document.getElementById('panelDetailLink');
            if (linkDetail) {
                linkDetail.href = `/reports/${markerData.id}`; // Assuming detail route
            }

            panel.classList.add('active');
            
            // Fly to marker offset to left (to fit right panel)
            const mapCenterOffset = map.project([markerData.lat, markerData.lng], 16).subtract([180, 0]);
            const targetLatLng = map.unproject(mapCenterOffset, 16);
            map.flyTo(targetLatLng, 16, { duration: 0.8 });
        };

        btnClosePanel.addEventListener('click', () => {
            panel.classList.remove('active');
            activeMarkerId = null;
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
                // Determine icon based on active filter
                let displayIconUrl = marker.primary_icon_url;
                
                if (activeCategory !== 'Semua') {
                    const matchedFac = marker.facilities.find(f => f.name === activeCategory && f.available);
                    if (matchedFac && matchedFac.icon_url) {
                        displayIconUrl = matchedFac.icon_url;
                    }
                }

                const icon = createCustomIcon(displayIconUrl);
                const leafletMarker = L.marker([marker.lat, marker.lng], { icon: icon });
                
                leafletMarker.on('click', () => showDetailPanel(marker));
                
                cluster.addLayer(leafletMarker);
                mapMarkers.push({ data: marker, layer: leafletMarker });
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
                if(searchQuery !== '') {
                    // Fit bounds to filtered markers if any
                    if(mapMarkers.length > 0) {
                        const bounds = L.latLngBounds(mapMarkers.map(m => [m.data.lat, m.data.lng]));
                        map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
                    }
                }
            }, 500);
        });

        // --- CHIPS ---
        const chips = document.querySelectorAll('.category-chip');
        chips.forEach(chip => {
            chip.addEventListener('click', (e) => {
                // Update active class
                chips.forEach(c => c.classList.remove('active'));
                e.target.classList.add('active');
                
                activeCategory = e.target.getAttribute('data-category');
                renderMarkers();
            });
        });

        // --- MY LOCATION ---
        document.getElementById('btnMyLocation').addEventListener('click', () => {
            if ("geolocation" in navigator) {
                const btn = document.getElementById('btnMyLocation');
                const icon = btn.querySelector('i');
                icon.className = 'fas fa-spinner fa-spin text-xl'; // loading state
                
                navigator.geolocation.getCurrentPosition(
                    position => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        map.flyTo([lat, lng], 15, { duration: 1 });
                        
                        // Add marker for user
                        L.circleMarker([lat, lng], {
                            radius: 8,
                            fillColor: "#4285F4",
                            color: "#FFFFFF",
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 1
                        }).addTo(map);
                        
                        icon.className = 'fas fa-location-crosshairs text-xl';
                    },
                    error => {
                        console.error(error);
                        alert("Gagal mendapatkan lokasi Anda.");
                        icon.className = 'fas fa-location-crosshairs text-xl';
                    }
                );
            } else {
                alert("Geolocation tidak didukung di browser ini.");
            }
        });

        // Initialize
        renderMarkers();
    });
</script>
@endpush
