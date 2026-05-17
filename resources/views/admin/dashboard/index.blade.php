@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')

@push('styles')
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet"
        integrity="sha512-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMYX+IGu9eJwzS1hOFyZ0QvBv8FzD8B4h3ceE0gD/2ezQ=="
        crossorigin="">
@endpush

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Total Laporan</div>
                    <h3 class="mb-0">{{ $totalReports }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <div class="text-muted">Pending</div>
                    <h3 class="mb-0">{{ $pendingCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <div class="text-muted">Disetujui</div>
                    <h3 class="mb-0">{{ $approvedCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-danger">
                <div class="card-body">
                    <div class="text-muted">Ditolak</div>
                    <h3 class="mb-0">{{ $rejectedCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Laporan Masuk 12 Bulan Terakhir</h5>
                    <canvas id="reportChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Ringkasan Pengguna</h5>
                    <p class="mb-1 text-muted">Total pengguna</p>
                    <h3 class="mb-0">{{ $totalUsers }}</h3>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Lokasi Terbaru</h5>
                    <ul class="list-unstyled mb-0">
                        @forelse ($recentLocations as $location)
                            <li class="mb-2">
                                <strong>{{ $location->validation?->report?->user?->name ?? 'Laporan' }}</strong>
                                <div class="text-muted small">{{ $location->publish_date?->format('d M Y') }}</div>
                            </li>
                        @empty
                            <li class="text-muted">Belum ada lokasi terpublikasi.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h5 class="mb-3">Mini Map Lokasi Terpublikasi</h5>
            <div id="mini-map" style="height: 280px;"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha512-KjS/2t3C6+P5THK6T/ymM3Gf3XzQFh9c2vQYl8uVLA2GgK2GkC3qTxdx3EJfYp8tC9f9a6BqM8XrU9FQ5x3n1g=="
        crossorigin=""></script>
    <script>
        const chartLabels = @json($chartData['labels']);
        const chartValues = @json($chartData['values']);

        new Chart(document.getElementById('reportChart'), {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Jumlah laporan',
                    data: chartValues,
                    backgroundColor: 'rgba(15, 118, 110, 0.6)',
                    borderColor: 'rgba(15, 118, 110, 1)',
                    borderWidth: 1,
                }],
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                }
            }
        });

        const mapPoints = @json($mapPoints);
        const miniMap = L.map('mini-map').setView([-7.716, 110.355], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap',
        }).addTo(miniMap);

        mapPoints.forEach((point) => {
            L.marker([point.lat, point.lng])
                .addTo(miniMap)
                .bindPopup(point.label || 'Lokasi');
        });
    </script>
@endpush
