@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')

@push('styles')
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet" crossorigin="">
    <style>
        .table-admin th {
            background: rgba(0,0,0,0.02);
            font-weight: 700;
            color: var(--color-text-primary);
        }
        .table-admin td {
            border-bottom: 1px solid var(--color-border-solid);
        }
    </style>
@endpush

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <x-stat-card title="Total Laporan" :value="$totalReports" icon="fa-solid fa-file-lines" color="primary" />
        <x-stat-card title="Pending" :value="$pendingCount" icon="fa-solid fa-clock" color="warning" />
        <x-stat-card title="Disetujui" :value="$approvedCount" icon="fa-solid fa-check-circle" color="success" />
        <x-stat-card title="Ditolak" :value="$rejectedCount" icon="fa-solid fa-circle-xmark" color="danger" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2">
            <div class="admin-card h-full m-0 flex flex-col">
                <h5 class="mb-4 font-bold text-lg text-text-primary">Laporan Masuk 12 Bulan Terakhir</h5>
                <div class="flex-grow relative w-full min-h-[260px]">
                    <canvas id="reportChart"></canvas>
                </div>
            </div>
        </div>
        <div class="lg:col-span-1">
            <div class="admin-card h-full m-0">
                <h5 class="mb-4 font-bold text-lg text-text-primary">Ringkasan Pengguna</h5>
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-[rgba(3,121,64,0.1)] p-4 rounded-xl text-primary">
                        <i class="fa-solid fa-users text-2xl"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-text-muted font-bold text-sm">Total Pengguna Aktif</p>
                        <h2 class="m-0 font-black text-2xl">{{ $totalUsers }}</h2>
                    </div>
                </div>

                <h5 class="mb-3 font-bold text-lg text-text-primary mt-6 pt-4 border-t-2 border-border-solid">Lokasi Terbaru</h5>
                <ul class="list-none p-0 m-0 space-y-3">
                    @forelse ($recentLocations as $location)
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-map-pin text-success mt-1"></i>
                            <div>
                                <strong class="block text-text-onsurface">{{ $location->validation?->report?->user?->name ?? 'Laporan' }}</strong>
                                <small class="text-text-muted font-bold">{{ $location->publish_date?->format('d M Y') ?? '' }}</small>
                            </div>
                        </li>
                    @empty
                        <li class="text-text-muted text-sm font-bold">Belum ada lokasi terpublikasi.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
        <div class="xl:col-span-3">
            <div class="admin-card h-full m-0">
                <div class="flex justify-between items-center mb-6">
                    <h5 class="font-bold text-lg text-text-primary m-0">Laporan Terbaru</h5>
                    <a href="{{ route('admin.reports.index') }}" class="px-4 py-1.5 border-2 border-border-solid rounded-full text-sm font-bold text-text-muted hover:bg-background transition-colors">Lihat Semua</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left table-admin">
                        <thead>
                            <tr>
                                <th class="rounded-l-lg py-3 px-4">Pengirim</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="rounded-r-lg py-3 px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentReports as $val)
                                @php
                                    $categoryName = $val->report?->details->first()?->category?->facility_name ?? '-';
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-text-onsurface">{{ $val->report?->user?->full_name ?? '-' }}</div>
                                        <div class="text-xs font-bold text-text-muted">{{ $val->report?->user?->email ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="bg-background text-text-onsurface border-2 border-border-solid px-3 py-1 rounded-full text-xs font-bold">{{ $categoryName }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-sm font-bold text-text-muted">{{ $val->created_at?->format('d M Y') }}</td>
                                    <td class="py-3 px-4">
                                        <x-badge-status :status="$val->approval_status" />
                                    </td>
                                    <td class="py-3 px-4">
                                        <a href="{{ route('admin.reports.show', $val->report_id ?? 0) }}" class="inline-block px-4 py-1.5 border-2 border-primary text-primary rounded-full text-xs font-bold hover:bg-primary hover:text-white transition-colors">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-text-muted font-bold">Belum ada laporan masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="xl:col-span-2">
            <div class="admin-card h-full m-0">
                <h5 class="mb-4 font-bold text-lg text-text-primary">Peta Persebaran</h5>
                <div id="mini-map" class="h-[320px] rounded-xl border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] z-0"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        const chartLabels = @json($chartData['labels']);
        const chartValues = @json($chartData['values']);

        new Chart(document.getElementById('reportChart'), {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: chartValues,
                    backgroundColor: 'rgba(2, 92, 48, 0.8)', /* primary color */
                    borderColor: 'rgba(2, 92, 48, 1)',
                    borderWidth: 2,
                    borderRadius: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
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
