@extends('layouts.app')

@section('title', 'Detail Laporan')
@section('page_title', 'Detail Laporan')

@push('styles')
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet" crossorigin="">
@endpush

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-5 flex flex-col gap-6">
            <div class="admin-card m-0">
                <h5 class="mb-4 font-black text-xl text-text-primary">Info Pengirim</h5>
                <p class="mb-2 text-text-onsurface"><strong class="font-bold text-text-muted">Nama:</strong> {{ $report->user?->full_name ?? '-' }}</p>
                <p class="mb-2 text-text-onsurface"><strong class="font-bold text-text-muted">Email:</strong> {{ $report->user?->email ?? '-' }}</p>
                <p class="mb-2 text-text-onsurface"><strong class="font-bold text-text-muted">Waktu:</strong> {{ $report->timestamp?->format('d M Y H:i') }}</p>
                <p class="m-0 text-text-onsurface"><strong class="font-bold text-text-muted">Koordinat:</strong> {{ $report->latitude }}, {{ $report->longitude }}</p>
            </div>

            <div class="admin-card m-0">
                <h5 class="mb-4 font-black text-xl text-text-primary">Lokasi Laporan</h5>
                <div id="report-map" class="h-[260px] rounded-xl border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] z-0"></div>
            </div>
        </div>

        <div class="lg:col-span-7 flex flex-col gap-6">
            <div class="admin-card m-0">
                <h5 class="mb-4 font-black text-xl text-text-primary">Foto Laporan</h5>
                @if ($report->photo_url)
                    @php
                        $photos = explode(',', $report->photo_url);
                    @endphp
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach ($photos as $photo)
                            <div>
                                <img src="{{ trim($photo) }}" alt="Foto laporan" class="w-full aspect-square object-cover rounded-xl border-2 border-border-solid shadow-sm">
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-text-muted font-bold text-sm">Tidak ada foto.</p>
                @endif
            </div>

            <div class="admin-card m-0">
                <h5 class="mb-4 font-black text-xl text-text-primary">Detail Fasilitas</h5>
                <div class="overflow-x-auto">
                    <table class="w-full text-left table-admin">
                        <thead>
                            <tr>
                                <th class="rounded-l-lg py-3 px-4">Fasilitas</th>
                                <th class="rounded-r-lg py-3 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($report->details as $detail)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4 font-bold text-text-onsurface">{{ $detail->category?->facility_name ?? '-' }}</td>
                                    <td class="py-3 px-4">
                                        @if ($detail->availability_status)
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border-2 bg-success text-white border-green-700 shadow-sm">Tersedia</span>
                                        @else
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border-2 bg-slate-100 text-slate-700 border-slate-300 shadow-sm">Tidak tersedia</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-4 text-text-muted font-bold">Belum ada detail fasilitas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <hr class="my-6 border-border-solid border-t-2">

                <h5 class="mb-4 font-black text-xl text-text-primary">Validasi Laporan</h5>
                @php
                    $mapLocation = $report->validation?->mapLocation;
                @endphp
                <form id="validationForm" action="{{ route('admin.reports.validate', $report->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block font-bold text-text-muted mb-2">Status</label>
                        <select name="approval_status" class="input-solid w-full" required>
                            <option value="approved" @selected(old('approval_status', 'approved') === 'approved')>Disetujui</option>
                            <option value="rejected" @selected(old('approval_status') === 'rejected')>Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block font-bold text-text-muted mb-2">Nama Tempat</label>
                        <input type="text" name="place_name" class="input-solid w-full"
                            value="{{ old('place_name', $mapLocation?->place_name) }}" maxlength="150" data-map-field />
                    </div>
                    <div class="mb-4">
                        <label class="block font-bold text-text-muted mb-2">Alamat</label>
                        <textarea name="address" rows="2" class="input-solid w-full" maxlength="255" data-map-field>{{ old('address', $mapLocation?->address) }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block font-bold text-text-muted mb-2">Status Lokasi</label>
                        <select name="status" class="input-solid w-full" data-map-field>
                            <option value="active" @selected(old('status', $mapLocation?->status ?? 'active') === 'active')>Aktif</option>
                            <option value="inactive" @selected(old('status', $mapLocation?->status) === 'inactive')>Nonaktif</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block font-bold text-text-muted mb-2">Kategori Badge</label>
                        <input type="text" name="facility_type" class="input-solid w-full"
                            value="{{ old('facility_type', $mapLocation?->facility_type) }}" maxlength="100"
                            data-map-field />
                    </div>
                    <div class="mb-6">
                        <label class="block font-bold text-text-muted mb-2">Catatan Evaluasi</label>
                        <textarea name="evaluation_note" rows="3" class="input-solid w-full" maxlength="1000"></textarea>
                    </div>
                    <button type="submit" class="btn-primary-solid w-full sm:w-auto">Submit Validasi</button>
                </form>
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
