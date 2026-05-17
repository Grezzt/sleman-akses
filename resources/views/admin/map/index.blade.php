@extends('layouts.app')

@section('title', 'Peta Lokasi')
@section('page_title', 'Peta Lokasi Terpublikasi')

@push('styles')
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet"
        integrity="sha512-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMYX+IGu9eJwzS1hOFyZ0QvBv8FzD8B4h3ceE0gD/2ezQ=="
        crossorigin="">
@endpush

@section('content')
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Daftar Lokasi</h5>
                    <div class="list-group" id="location-list">
                        @forelse ($locations as $location)
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <button type="button" class="btn btn-link text-decoration-none p-0 text-start js-focus"
                                    data-lat="{{ $location->latitude }}" data-lng="{{ $location->longitude }}"
                                    data-id="{{ $location->id }}">
                                    <div class="fw-semibold">{{ $location->validation?->report?->user?->name ?? 'Lokasi' }}
                                    </div>
                                    <small class="text-muted">{{ $location->publish_date?->format('d M Y') }}</small>
                                </button>
                                <form action="{{ route('admin.map.destroy', $location->id) }}" method="POST"
                                    class="ms-2 js-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        @empty
                            <div class="text-muted">Belum ada lokasi terpublikasi.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    @if (empty($maptilerKey))
                        <div class="alert alert-warning">MAPTILER_KEY belum diatur.</div>
                    @endif
                    <div id="admin-map" style="height: 520px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha512-KjS/2t3C6+P5THK6T/ymM3Gf3XzQFh9c2vQYl8uVLA2GgK2GkC3qTxdx3EJfYp8tC9f9a6BqM8XrU9FQ5x3n1g=="
        crossorigin=""></script>
    <script>
        const maptilerKey = @json($maptilerKey);
        const markers = @json($markers);

        const map = L.map('admin-map').setView([-7.716, 110.355], 11);

        const tileUrl = maptilerKey ?
            `https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=${maptilerKey}` :
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

        const attribution = maptilerKey ?
            '&copy; <a href="https://www.maptiler.com/copyright/">MapTiler</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>' :
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';

        L.tileLayer(tileUrl, {
            maxZoom: 19,
            attribution: attribution,
        }).addTo(map);

        const markerMap = new Map();

        markers.forEach((marker) => {
            const point = L.marker([marker.lat, marker.lng]).addTo(map)
                .bindPopup(marker.label || 'Lokasi');
            markerMap.set(marker.id, point);
        });

        document.querySelectorAll('.js-focus').forEach((button) => {
            button.addEventListener('click', () => {
                const lat = parseFloat(button.dataset.lat);
                const lng = parseFloat(button.dataset.lng);
                const id = button.dataset.id;

                map.setView([lat, lng], 15);
                const marker = markerMap.get(Number(id));
                if (marker) {
                    marker.openPopup();
                }
            });
        });

        document.querySelectorAll('.js-delete').forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                Swal.fire({
                    title: 'Hapus lokasi ini?',
                    text: 'Lokasi akan dihapus dari peta publik.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
