@extends('layouts.app')

@section('title', 'Manajemen Laporan')
@section('page_title', 'Manajemen Laporan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--color-primary) !important;
            color: white !important;
            border: 2px solid var(--color-border-solid) !important;
            border-radius: 8px !important;
            box-shadow: 2px 2px 0 0 var(--color-secondary) !important;
        }
        .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input {
            border: 2px solid var(--color-border-solid);
            border-radius: 8px;
            padding: 4px 8px;
            background: var(--color-background);
        }
    </style>
@endpush

@section('content')
    <div class="flex flex-wrap justify-between items-center mb-6">
        <h4 class="m-0 font-black text-2xl text-text-primary">Daftar Laporan</h4>
        <form method="GET" class="flex gap-2">
            <select name="status" class="input-solid min-w-[150px] py-1.5 px-3">
                <option value="">Semua Status</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button class="btn-primary-solid text-sm px-4 py-1.5" type="submit">Filter</button>
        </form>
    </div>

    <div class="admin-card m-0">
        <div class="overflow-x-auto pb-4">
            <table class="w-full text-left table-admin" id="reportsTable">
                <thead>
                    <tr>
                        <th class="rounded-l-lg py-3 px-4">No</th>
                        <th class="py-3 px-4">Pengirim</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="rounded-r-lg py-3 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $index => $report)
                        @php
                            $statusValue = $report->validation?->approval_status ?? 'pending';
                            $categoryName = $report->details->first()?->category?->facility_name ?? '-';
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 font-bold text-text-onsurface">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-text-onsurface">{{ $report->user?->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-text-muted font-bold text-sm">{{ $report->timestamp?->format('d M Y H:i') }}</td>
                            <td class="py-3 px-4">
                                <span class="bg-background text-text-onsurface border-2 border-border-solid px-3 py-1 rounded-full text-xs font-bold">{{ $categoryName }}</span>
                            </td>
                            <td class="py-3 px-4"><x-badge-status :status="$statusValue" /></td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.reports.show', $report->id) }}"
                                    class="inline-block px-4 py-1.5 border-2 border-primary text-primary rounded-full text-xs font-bold hover:bg-primary hover:text-white transition-colors">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $reports->withQueryString()->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
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
