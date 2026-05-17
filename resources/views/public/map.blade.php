@extends('layouts.public')

@section('title', 'Sleman Akses - Peta Publik')

@section('content')
    <div class="position-relative">
        @if (empty($maptilerKey))
            <div class="alert alert-warning m-3">MAPTILER_KEY belum diatur.</div>
        @endif

        <div id="map" class="public-map"></div>

        <div class="map-filter">
            <h6>Filter Kategori</h6>
            <div class="map-filter-list">
                @forelse ($categories as $category)
                    <div class="form-check">
                        <input class="form-check-input js-category-filter" type="checkbox" value="{{ $category['name'] }}"
                            id="category-{{ $category['id'] }}">
                        <label class="form-check-label" for="category-{{ $category['id'] }}">
                            {{ $category['name'] }}
                        </label>
                    </div>
                @empty
                    <p class="text-muted mb-0">Kategori belum tersedia.</p>
                @endforelse
            </div>

            <div class="map-legend mt-3">
                <span><span class="dot"></span> Lokasi terpublikasi</span>
                <span class="text-muted">Centang kategori untuk filter marker.</span>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const markersData = @json($markers);
        const categories = @json($categories);
        const maptilerKey = @json($maptilerKey);

        const mapCenter = [-7.716, 110.355];
        const map = L.map('map').setView(mapCenter, 11);

        const tileUrl = maptilerKey ?
            `https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=${maptilerKey}` :
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

        const attribution = maptilerKey ?
            '&copy; <a href="https://www.maptiler.com/copyright/">MapTiler</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>' :
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';

        L.tileLayer(tileUrl, {
            maxZoom: 20,
            attribution: attribution,
        }).addTo(map);

        const cluster = L.markerClusterGroup();
        const iconCache = new Map();

        const escapeHtml = (value) => {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        };

        const getIcon = (url) => {
            if (!url) {
                return undefined;
            }
            if (iconCache.has(url)) {
                return iconCache.get(url);
            }

            const icon = L.icon({
                iconUrl: url,
                iconSize: [32, 40],
                iconAnchor: [16, 40],
                popupAnchor: [0, -32],
            });

            iconCache.set(url, icon);
            return icon;
        };

        const buildPopup = (marker) => {
            const title = marker.primary_category || 'Lokasi Publik';
            const facilities = Array.isArray(marker.facilities) ? marker.facilities : [];
            const facilityList = facilities.length ?
                facilities
                .map((item) => `<li>${escapeHtml(item.name)}: ${item.available ? 'Tersedia' : 'Tidak tersedia'}</li>`)
                .join('') :
                '<li>Data fasilitas belum tersedia.</li>';

            const photoHtml = marker.photo_url ?
                `<img src="${escapeHtml(marker.photo_url)}" alt="Foto lokasi" style="width:100%; border-radius:8px; margin-bottom:8px;">` :
                '';

            return `
            <div style="min-width:220px;">
                ${photoHtml}
                <strong>${escapeHtml(title)}</strong>
                <div style="font-size:12px; color:#64748b; margin-bottom:6px;">${escapeHtml(marker.published || '')}</div>
                <ul style="padding-left:16px; margin:0;">${facilityList}</ul>
            </div>
        `;
        };

        const activeCategories = new Set();

        const renderMarkers = () => {
            cluster.clearLayers();

            markersData.forEach((marker) => {
                const markerCategories = Array.isArray(marker.categories) ? marker.categories : [];
                const isVisible = activeCategories.size === 0 ||
                    markerCategories.some((name) => activeCategories.has(name));

                if (!isVisible) {
                    return;
                }

                const icon = getIcon(marker.primary_icon);
                const leafletMarker = L.marker([marker.lat, marker.lng], icon ? {
                        icon
                    } : undefined)
                    .bindPopup(buildPopup(marker));

                cluster.addLayer(leafletMarker);
            });

            map.addLayer(cluster);
        };

        document.querySelectorAll('.js-category-filter').forEach((checkbox) => {
            checkbox.addEventListener('change', (event) => {
                if (event.target.checked) {
                    activeCategories.add(event.target.value);
                } else {
                    activeCategories.delete(event.target.value);
                }
                renderMarkers();
            });
        });

        renderMarkers();
    </script>
@endpush
