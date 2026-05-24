@extends('layouts.app')

@section('title', 'Peta Lokasi')
@section('page_title', 'Peta Lokasi Terpublikasi')

@push('styles')
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet" crossorigin="">
@endpush

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-4">
            <div class="admin-card m-0 h-full flex flex-col">
                <h5 class="mb-4 font-black text-xl text-text-primary">Daftar Lokasi</h5>
                <div class="flex-grow flex flex-col gap-3" id="location-list">
                    @forelse ($locations as $location)
                        <div class="flex justify-between items-start p-4 border-2 border-border-solid rounded-xl hover:bg-slate-50 transition-colors">
                            <button type="button" class="text-left js-focus flex-grow"
                                data-lat="{{ $location->latitude }}" data-lng="{{ $location->longitude }}"
                                data-id="{{ $location->id }}">
                                <div class="font-black text-text-onsurface">{{ $location->validation?->report?->user?->full_name ?? 'Lokasi' }}
                                </div>
                                <small class="text-text-muted font-bold block mt-1">{{ $location->publish_date?->format('d M Y') }}</small>
                            </button>
                            <form action="{{ route('admin.map.destroy', $location->id) }}" method="POST"
                                class="ml-3 js-delete flex-shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 border-2 border-error text-error rounded-full text-xs font-bold hover:bg-error hover:text-white transition-colors">Hapus</button>
                            </form>
                        </div>
                    @empty
                        <div class="text-text-muted font-bold text-center py-6 border-2 border-dashed border-border-solid rounded-xl">Belum ada lokasi terpublikasi.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-8">
            <div class="admin-card m-0 h-full">
                @if (empty($maptilerKey))
                    <div class="bg-yellow-100 border-2 border-yellow-500 text-yellow-800 p-4 rounded-xl mb-4 font-bold shadow-[2px_2px_0_0_#eab308]">
                        MAPTILER_KEY belum diatur.
                    </div>
                @endif
                <div id="admin-map" class="w-full h-[520px] rounded-xl border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] z-0"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
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
            });
        });
    </script>
@endpush
