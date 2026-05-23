@extends('layouts.app')

@section('title', 'Detail Laporan')
@section('page_title', 'Detail Laporan')

@push('styles')
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
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
                        @php
                            $photos = explode(',', $report->photo_url);
                        @endphp
                        <div class="row g-2">
                            @foreach ($photos as $photo)
                                <div class="col-6 col-md-4">
                                    <img src="{{ trim($photo) }}" alt="Foto laporan" class="img-fluid rounded border" style="object-fit: cover; aspect-ratio: 1/1; width: 100%;">
                                </div>
                            @endforeach
                        </div>
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
                    @php
                        $mapLocation = $report->validation?->mapLocation;
                    @endphp
                    <form id="validationForm" action="{{ route('admin.reports.validate', $report->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="approval_status" class="form-select" required>
                                <option value="approved" @selected(old('approval_status', 'approved') === 'approved')>Disetujui</option>
                                <option value="rejected" @selected(old('approval_status') === 'rejected')>Ditolak</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Tempat</label>
                            <input type="text" name="place_name" class="form-control"
                                value="{{ old('place_name', $mapLocation?->place_name) }}" maxlength="150" data-map-field />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" rows="2" class="form-control" maxlength="255" data-map-field>{{ old('address', $mapLocation?->address) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status Lokasi</label>
                            <select name="status" class="form-select" data-map-field>
                                <option value="active" @selected(old('status', $mapLocation?->status ?? 'active') === 'active')>Aktif</option>
                                <option value="inactive" @selected(old('status', $mapLocation?->status) === 'inactive')>Nonaktif</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori Badge</label>
                            <input type="text" name="facility_type" class="form-control"
                                value="{{ old('facility_type', $mapLocation?->facility_type) }}" maxlength="100"
                                data-map-field />
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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const map = L.map('report-map').setView([{{ $report->latitude }}, {{ $report->longitude }}], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap',
        }).addTo(map);

        L.marker([{{ $report->latitude }}, {{ $report->longitude }}]).addTo(map);

        const approvalSelect = document.querySelector('[name="approval_status"]');
        const mapFields = document.querySelectorAll('[data-map-field]');

        const toggleMapFields = () => {
            const isApproved = approvalSelect.value === 'approved';

            mapFields.forEach((field) => {
                field.disabled = !isApproved;
                field.required = isApproved;
            });
        };

        toggleMapFields();
        approvalSelect.addEventListener('change', toggleMapFields);

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
