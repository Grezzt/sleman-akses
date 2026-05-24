@extends('layouts.app')

@section('title', 'Peta Lokasi')
@section('page_title', 'Peta Lokasi Terpublikasi')

@push('styles')
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet" crossorigin="">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <style>
        /* Leaflet Overrides */
        .leaflet-control-zoom {
            border: none !important;
            box-shadow: var(--shadow-solid) !important;
            border-radius: var(--radius-input) !important;
            margin-right: 10px !important;
            margin-bottom: 10px !important;
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

        /* Floating Panel UI */
        .floating-ui {
            position: absolute;
            z-index: 1000;
            pointer-events: none;
        }
        .floating-ui > * {
            pointer-events: auto;
        }

        .side-panel-ui { 
            top: 20px; 
            right: 20px; 
            bottom: 20px; 
            width: 380px; 
            max-width: calc(100% - 40px);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.3s ease; 
            transform: translateX(120%); 
            opacity: 0;
        }
        .side-panel-ui.active { 
            transform: translateX(0); 
            opacity: 1;
            pointer-events: auto;
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
        .sleman-marker.inactive .pin {
            color: #94a3b8; /* slate-400 */
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
    </style>
@endpush

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[calc(100vh-140px)] min-h-[600px]">
        <div class="lg:col-span-4 h-full flex flex-col">
            <div class="admin-card m-0 h-full flex flex-col overflow-hidden">
                <h5 class="mb-4 font-black text-xl text-text-primary">Daftar Lokasi</h5>
                <div class="flex-grow flex flex-col gap-3 overflow-y-auto pr-2 pb-4" id="location-list">
                    @forelse ($locations as $location)
                        <div class="flex flex-col p-4 border-2 border-border-solid rounded-xl hover:bg-slate-50 transition-colors {{ $location->status === 'inactive' ? 'opacity-60' : '' }}">
                            <button type="button" class="text-left js-focus w-full"
                                data-lat="{{ $location->latitude }}" data-lng="{{ $location->longitude }}"
                                data-id="{{ $location->id }}">
                                <div class="font-black text-text-onsurface">{{ $location->place_name }}</div>
                                <div class="text-sm font-bold text-text-muted truncate">{{ $location->address }}</div>
                                <div class="flex justify-between items-center mt-2">
                                    <small class="text-text-muted font-bold">{{ $location->publish_date?->format('d M Y') }}</small>
                                    @if($location->status === 'active')
                                        <span class="px-2 py-0.5 bg-success text-white text-[10px] rounded-full font-bold">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-slate-400 text-white text-[10px] rounded-full font-bold">Nonaktif</span>
                                    @endif
                                </div>
                            </button>
                        </div>
                    @empty
                        <div class="text-text-muted font-bold text-center py-6 border-2 border-dashed border-border-solid rounded-xl">Belum ada lokasi terpublikasi.</div>
                    @endforelse
                </div>
                @if($locations->hasPages())
                    <div class="mt-4 pt-4 border-t-2 border-border-solid">
                        {{ $locations->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-8 h-full">
            <div class="admin-card m-0 h-full p-0 relative overflow-hidden">
                @if (empty($maptilerKey))
                    <div class="absolute top-4 left-1/2 -translate-x-1/2 bg-yellow-100 border-2 border-yellow-500 text-yellow-800 px-4 py-2 rounded-xl font-bold shadow-[2px_2px_0_0_#eab308] z-[1000]">
                        MAPTILER_KEY belum diatur.
                    </div>
                @endif
                <div id="admin-map" class="w-full h-full z-0"></div>

                <!-- Floating Side Panel -->
                <div class="floating-ui side-panel-ui flex flex-col" id="detailPanel">
                    <div class="w-full bg-surface border-2 border-border-solid rounded-[24px] shadow-solid flex flex-col overflow-hidden relative max-h-full">
                        <button id="btnClosePanel" class="absolute top-3 right-3 w-8 h-8 bg-surface/90 backdrop-blur border-2 border-border-solid rounded-full flex items-center justify-center text-text-onsurface z-[100] hover:bg-background transition-colors shadow-sm">
                            <i class="fas fa-times"></i>
                        </button>

                        <!-- Image Cover -->
                        <div class="w-full h-48 bg-background relative shrink-0 group">
                            <img id="panelImage" src="" alt="Fasilitas" class="w-full h-full object-cover hidden transition-opacity duration-300">
                            <div id="panelImagePlaceholder" class="w-full h-full flex flex-col items-center justify-center bg-gray-200">
                                <img src="{{ asset('Tambah foto fasilitas umum.svg') }}" alt="No image" class="w-24 h-24 object-contain opacity-50">
                                <span class="text-xs font-bold uppercase tracking-wider text-text-muted mt-2">Tanpa Foto</span>
                            </div>
                            
                            <button id="btnPrevPhoto" class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black/80 transition-all opacity-0 group-hover:opacity-100 hidden z-10 shadow-sm cursor-pointer border border-white/20">
                                <i class="fas fa-chevron-left text-sm"></i>
                            </button>
                            <button id="btnNextPhoto" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black/80 transition-all opacity-0 group-hover:opacity-100 hidden z-10 shadow-sm cursor-pointer border border-white/20">
                                <i class="fas fa-chevron-right text-sm"></i>
                            </button>
                            
                            <div id="panelCategoryPill" class="absolute top-4 left-4 bg-secondary border-2 border-primary text-primary font-black text-xs px-3 py-1 rounded-full shadow-[2px_2px_0px_0px_#025C30] uppercase z-10">KATEGORI</div>
                            <div id="panelStatusPill" class="absolute top-4 right-14 bg-primary text-surface font-bold text-xs px-3 py-1 rounded-full shadow-md uppercase z-10">AKTIF</div>
                            
                            <div id="panelPhotosIndicator" class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-text-primary/70 text-surface text-xs font-bold px-4 py-1.5 rounded-full flex items-center gap-2 backdrop-blur-sm hidden z-10 pointer-events-none">
                                <i class="fas fa-images"></i> <span id="panelPhotosText">1 / 1</span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5 overflow-y-auto flex-1 flex flex-col">
                            <h2 id="panelTitle" class="text-xl font-black text-primary leading-tight mb-2">Nama Fasilitas</h2>
                            <div class="flex items-start text-text-primary text-sm mb-1">
                                <i class="fas fa-location-dot mt-1 mr-2 text-text-primary"></i>
                                <span id="panelAddress" class="leading-relaxed font-medium">Alamat lengkap fasilitas.</span>
                            </div>
                            <div class="text-xs text-text-muted mb-4">
                                Dilaporkan oleh <span id="panelReporter" class="font-medium">Anonim</span>
                            </div>

                            <h3 class="font-bold text-primary mb-3 text-sm">Fitur Aksesibilitas</h3>
                            <div id="panelFacilities" class="grid grid-cols-2 gap-2 mb-6">
                                <!-- Chips injected here -->
                            </div>
                            
                            <!-- Admin Actions -->
                            <div class="mt-auto pt-4 border-t-2 border-border-solid flex flex-col gap-3">
                                <div class="flex items-center justify-between p-3 bg-slate-50 border-2 border-border-solid rounded-xl">
                                    <div class="font-bold text-sm text-text-onsurface">Status Publikasi</div>
                                    <form id="formToggleStatus" method="POST" action="">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" id="btnToggleStatus" class="px-4 py-1.5 rounded-full font-bold text-xs border-2 transition-all">
                                            Toggle
                                        </button>
                                    </form>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <a id="btnEditReport" href="#" class="w-full bg-slate-100 border-2 border-border-solid text-text-onsurface hover:bg-slate-200 font-black py-2.5 rounded-xl flex justify-center items-center gap-2 transition-colors text-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form id="formDeleteMap" method="POST" action="" class="w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this.form)" class="w-full bg-white border-2 border-error text-error hover:bg-error hover:text-white font-black py-2.5 rounded-xl flex justify-center items-center gap-2 transition-colors text-sm">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script>
        const maptilerKey = @json($maptilerKey);
        const markers = @json($markers);

        const map = L.map('admin-map', {
            zoomControl: false,
            attributionControl: false
        }).setView([-7.716, 110.355], 11);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        const tileUrl = maptilerKey ?
            `https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=${maptilerKey}` :
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

        L.tileLayer(tileUrl, { maxZoom: 20 }).addTo(map);

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

        const markerMap = new Map();
        let currentPhotoUrls = [];
        let currentPhotoIndex = 0;
        let activeMarkerId = null;

        const createCustomIcon = (iconUrl, status) => {
            const isInactive = status === 'inactive';
            const html = `
                <div class="sleman-marker ${isInactive ? 'inactive' : ''}">
                    <i class="fas fa-map-marker pin"></i>
                    <div class="icon-circle">
                        <img src="${iconUrl}" alt="icon" style="width: 14px; height: 14px; object-fit: contain; ${isInactive ? 'filter: grayscale(100%);' : ''}">
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

        const panel = document.getElementById('detailPanel');
        
        // Photo Slider Logic
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
            
            document.getElementById('panelTitle').textContent = markerData.place_name;
            document.getElementById('panelAddress').textContent = markerData.address;
            document.getElementById('panelReporter').textContent = markerData.reporter_name;
            document.getElementById('panelCategoryPill').textContent = markerData.primary_category;
            
            const statusPill = document.getElementById('panelStatusPill');
            const toggleBtn = document.getElementById('btnToggleStatus');
            
            if(markerData.raw_status === 'active') {
                statusPill.textContent = 'AKTIF';
                statusPill.className = 'absolute top-4 right-14 bg-success text-surface font-bold text-xs px-3 py-1 rounded-full shadow-md uppercase z-10';
                toggleBtn.textContent = 'Nonaktifkan';
                toggleBtn.className = 'px-4 py-1.5 rounded-full font-bold text-xs border-2 border-border-solid bg-white hover:bg-slate-200 text-text-onsurface transition-all';
            } else {
                statusPill.textContent = 'NONAKTIF';
                statusPill.className = 'absolute top-4 right-14 bg-slate-500 text-surface font-bold text-xs px-3 py-1 rounded-full shadow-md uppercase z-10';
                toggleBtn.textContent = 'Aktifkan';
                toggleBtn.className = 'px-4 py-1.5 rounded-full font-bold text-xs border-2 border-primary bg-primary hover:bg-primary-hover text-white transition-all shadow-[2px_2px_0px_0px_var(--color-secondary)]';
            }
            
            // Photo Handling
            const imgEl = document.getElementById('panelImage');
            const placeholderEl = document.getElementById('panelImagePlaceholder');
            const photosIndEl = document.getElementById('panelPhotosIndicator');
            
            if (markerData.photo_url) {
                currentPhotoUrls = markerData.photo_url.split(',').map(u => u.trim());
                currentPhotoIndex = 0;
                imgEl.src = currentPhotoUrls[0];
                imgEl.classList.remove('hidden');
                placeholderEl.classList.add('hidden');
                
                if(currentPhotoUrls.length > 1) {
                    photosIndEl.classList.remove('hidden');
                    document.getElementById('btnPrevPhoto').classList.remove('hidden');
                    document.getElementById('btnNextPhoto').classList.remove('hidden');
                    document.getElementById('panelPhotosText').textContent = `1 / ${currentPhotoUrls.length}`;
                } else {
                    photosIndEl.classList.add('hidden');
                    document.getElementById('btnPrevPhoto').classList.add('hidden');
                    document.getElementById('btnNextPhoto').classList.add('hidden');
                }
            } else {
                imgEl.classList.add('hidden');
                placeholderEl.classList.remove('hidden');
                photosIndEl.classList.add('hidden');
                document.getElementById('btnPrevPhoto').classList.add('hidden');
                document.getElementById('btnNextPhoto').classList.add('hidden');
            }

            // Facilities Grid
            const facContainer = document.getElementById('panelFacilities');
            facContainer.innerHTML = '';
            
            const facilities = markerData.facilities || [];
            if (facilities.length > 0) {
                facilities.forEach(fac => {
                    if (fac.available) {
                        facContainer.innerHTML += `
                            <div class="bg-secondary border-2 border-primary text-primary font-bold text-xs rounded-xl px-2 py-2 flex items-center gap-2 shadow-[2px_2px_0px_0px_#025C30]">
                                <img src="${fac.icon_url}" class="w-4 h-4 object-contain opacity-80" alt="icon">
                                <span class="truncate">${fac.name}</span>
                            </div>
                        `;
                    } else {
                        facContainer.innerHTML += `
                            <div class="bg-surface border-2 border-border-solid text-text-muted font-bold text-xs rounded-xl px-2 py-2 flex items-center gap-2 shadow-[2px_2px_0px_0px_#D4D4D4]">
                                <img src="${fac.icon_url}" class="w-4 h-4 object-contain grayscale opacity-40" alt="icon">
                                <span class="truncate">${fac.name}</span>
                            </div>
                        `;
                    }
                });
            } else {
                facContainer.innerHTML = `<span class="text-xs text-text-muted italic col-span-2">Belum ada data fasilitas.</span>`;
            }

            // Update Forms & Links
            document.getElementById('formToggleStatus').action = `/admin/map/${markerData.id}/toggle-status`;
            document.getElementById('formDeleteMap').action = `/admin/map/${markerData.id}`;
            
            const editBtn = document.getElementById('btnEditReport');
            if (markerData.report_id) {
                editBtn.href = `/admin/reports/${markerData.report_id}`;
                editBtn.style.display = 'flex';
            } else {
                editBtn.style.display = 'none';
            }

            panel.classList.add('active');
            
            // Fly to marker offset to left (to fit right panel in its own container)
            const mapCenterOffset = map.project([markerData.lat, markerData.lng], 16).subtract([100, 0]);
            const targetLatLng = map.unproject(mapCenterOffset, 16);
            map.flyTo(targetLatLng, 16, { duration: 0.8 });
        };

        document.getElementById('btnClosePanel').addEventListener('click', () => {
            panel.classList.remove('active');
            activeMarkerId = null;
        });

        // Initialize Map
        if(markers.length > 0) {
            markers.forEach((marker) => {
                const icon = createCustomIcon(marker.icon_url, marker.raw_status);
                const leafletMarker = L.marker([marker.lat, marker.lng], { icon: icon });
                
                leafletMarker.on('click', () => showDetailPanel(marker));
                
                cluster.addLayer(leafletMarker);
                markerMap.set(marker.id, leafletMarker);
            });
            map.addLayer(cluster);
            
            const bounds = L.latLngBounds(markers.map(m => [m.lat, m.lng]));
            map.fitBounds(bounds, { padding: [20, 20], maxZoom: 15 });
        }

        // Sidebar List Click
        document.querySelectorAll('.js-focus').forEach((button) => {
            button.addEventListener('click', () => {
                const id = Number(button.dataset.id);
                const markerData = markers.find(m => m.id === id);
                
                if (markerData) {
                    showDetailPanel(markerData);
                }
            });
        });

        // AJAX Toggle Status
        document.getElementById('formToggleStatus').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('btnToggleStatus');
            const originalText = btn.textContent;
            
            try {
                btn.textContent = 'Memproses...';
                btn.disabled = true;
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams(new FormData(form))
                });
                
                if (response.ok) {
                    const data = await response.json();
                    
                    const markerData = markers.find(m => m.id === activeMarkerId);
                    if (markerData) {
                        markerData.raw_status = markerData.raw_status === 'active' ? 'inactive' : 'active';
                        markerData.status = markerData.raw_status.toUpperCase();
                        
                        const leafletMarker = markerMap.get(markerData.id);
                        if (leafletMarker) {
                            leafletMarker.setIcon(createCustomIcon(markerData.icon_url, markerData.raw_status));
                        }
                        
                        showDetailPanel(markerData);
                        
                        const listButton = document.querySelector(`.js-focus[data-id="${markerData.id}"]`);
                        if (listButton) {
                            const container = listButton.closest('.p-4');
                            if (markerData.raw_status === 'inactive') {
                                container.classList.add('opacity-60');
                                listButton.innerHTML = listButton.innerHTML.replace('bg-success text-white', 'bg-slate-400 text-white').replace('Aktif', 'Nonaktif');
                            } else {
                                container.classList.remove('opacity-60');
                                listButton.innerHTML = listButton.innerHTML.replace('bg-slate-400 text-white', 'bg-success text-white').replace('Nonaktif', 'Aktif');
                            }
                        }
                        
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Status berhasil diubah',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                } else {
                    throw new Error('Gagal merespons');
                }
            } catch (error) {
                Swal.fire('Error', 'Gagal mengubah status lokasi', 'error');
            } finally {
                btn.textContent = originalText;
                btn.disabled = false;
            }
        });

        // SweetAlert Delete
        window.confirmDelete = function(form) {
            Swal.fire({
                title: 'Hapus lokasi ini?',
                text: 'Lokasi akan dihapus dari peta publik.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn-primary-solid mr-2',
                    cancelButton: 'px-4 py-2 border-2 border-border-solid rounded-full font-bold text-text-muted hover:bg-slate-200 transition-colors'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
@endpush
