@extends('layouts.app')

@section('title', 'Detail Laporan')
@section('page_title', 'Detail Laporan')

@push('styles')
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet"
        integrity="sha512-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMYX+IGu9eJwzS1hOFyZ0QvBv8FzD8B4h3ceE0gD/2ezQ=="
        crossorigin="">
@endpush

@section('content')
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Info Pengirim</h5>
                    <p class="mb-1"><strong>Nama:</strong> {{ $report->user?->name ?? '-' }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $report->user?->email ?? '-' }}</p>
                    <p class="mb-1"><strong>Waktu:</strong> {{ $report->timestamp?->format('d M Y H:i') }}</p>
                    <p class="mb-1"><strong>Koordinat:</strong> {{ $report->latitude }}, {{ $report->longitude }}</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Lokasi Laporan</h5>
                    <div id="report-map" style="height: 260px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Foto Laporan</h5>
                    @if ($report->photo_url)
                        <img src="{{ $report->photo_url }}" alt="Foto laporan" class="img-fluid rounded">
                    @else
                        <p class="text-muted">Tidak ada foto.</p>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Detail Fasilitas</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fasilitas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($report->details as $detail)
                                    <tr>
                                        <td>{{ $detail->category?->facility_name ?? '-' }}</td>
                                        <td>
                                            @if ($detail->availability_status)
                                                <span class="badge bg-success">Tersedia</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak tersedia</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-muted">Belum ada detail fasilitas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <h5 class="mb-3">Validasi Laporan</h5>
                    <form id="validationForm" action="{{ route('admin.reports.validate', $report->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="approval_status" class="form-select" required>
                                <option value="approved">Disetujui</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan Evaluasi</label>
                            <textarea name="evaluation_note" rows="3" class="form-control" maxlength="1000"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Validasi</button>
                    </form>
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
        const map = L.map('report-map').setView([{{ $report->latitude }}, {{ $report->longitude }}], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap',
        }).addTo(map);

        L.marker([{{ $report->latitude }}, {{ $report->longitude }}]).addTo(map);

        document.getElementById('validationForm').addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Yakin validasi laporan?',
                text: 'Status laporan akan diperbarui.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, validasi',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
        });
    </script>
@endpush
