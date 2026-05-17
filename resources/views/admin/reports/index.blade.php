@extends('layouts.app')

@section('title', 'Manajemen Laporan')
@section('page_title', 'Manajemen Laporan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Daftar Laporan</h4>
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button class="btn btn-primary" type="submit">Filter</button>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle" id="reportsTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pengirim</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $index => $report)
                            @php
                                $statusValue = $report->validation?->approval_status ?? 'pending';
                                $badgeMap = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                ];
                                $badge = $badgeMap[$statusValue] ?? 'secondary';
                                $categoryName = $report->details->first()?->category?->facility_name ?? '-';
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $report->user?->name ?? '-' }}</td>
                                <td>{{ $report->timestamp?->format('d M Y H:i') }}</td>
                                <td>{{ $categoryName }}</td>
                                <td><span class="badge bg-{{ $badge }}">{{ ucfirst($statusValue) }}</span></td>
                                <td>
                                    <a href="{{ route('admin.reports.show', $report->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $reports->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(function() {
            $('#reportsTable').DataTable({
                paging: false,
                info: false,
                order: [],
            });
        });
    </script>
@endpush
