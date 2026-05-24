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
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-6">
        <div class="flex flex-col gap-4 w-full md:w-auto">
            <h4 class="m-0 font-black text-2xl text-text-primary">Daftar Laporan</h4>
            
            <!-- Filter Tabs -->
            <div class="flex gap-3 overflow-x-auto pb-2 w-full">
                <a href="{{ route('admin.reports.index') }}" 
                   class="px-5 py-2 rounded-full font-bold border-2 transition-all whitespace-nowrap {{ empty($status) ? 'bg-primary text-white border-primary shadow-[2px_2px_0_0_var(--color-secondary)]' : 'bg-surface text-text-muted border-border-solid hover:bg-slate-100 hover:text-text-primary' }}">
                   Semua
                </a>
                <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" 
                   class="px-5 py-2 rounded-full font-bold border-2 transition-all whitespace-nowrap {{ $status === 'pending' ? 'bg-primary text-white border-primary shadow-[2px_2px_0_0_var(--color-secondary)]' : 'bg-surface text-text-muted border-border-solid hover:bg-slate-100 hover:text-text-primary' }}">
                   Pending
                </a>
                <a href="{{ route('admin.reports.index', ['status' => 'approved']) }}" 
                   class="px-5 py-2 rounded-full font-bold border-2 transition-all whitespace-nowrap {{ $status === 'approved' ? 'bg-primary text-white border-primary shadow-[2px_2px_0_0_var(--color-secondary)]' : 'bg-surface text-text-muted border-border-solid hover:bg-slate-100 hover:text-text-primary' }}">
                   Disetujui
                </a>
                <a href="{{ route('admin.reports.index', ['status' => 'rejected']) }}" 
                   class="px-5 py-2 rounded-full font-bold border-2 transition-all whitespace-nowrap {{ $status === 'rejected' ? 'bg-primary text-white border-primary shadow-[2px_2px_0_0_var(--color-secondary)]' : 'bg-surface text-text-muted border-border-solid hover:bg-slate-100 hover:text-text-primary' }}">
                   Ditolak
                </a>
            </div>
        </div>

        <!-- Per Page Selector & Category Filter -->
        <form method="GET" class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto mt-2 md:mt-0">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <label class="font-bold text-text-muted whitespace-nowrap hidden sm:block">Kategori:</label>
                <select name="category_id" class="input-solid py-1.5 px-3 bg-white w-full sm:w-auto" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->facility_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <label class="font-bold text-text-muted whitespace-nowrap hidden sm:block">Per halaman:</label>
                <select name="per_page" class="input-solid py-1.5 px-3 bg-white w-full sm:w-auto" onchange="this.form.submit()">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
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
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 font-bold text-text-onsurface">{{ $reports->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-bold text-text-onsurface">{{ $report->user?->full_name ?? '-' }}</td>
                            <td class="py-3 px-4 text-text-muted font-bold text-sm">{{ $report->timestamp?->format('d M Y H:i') }}</td>
                            <td class="py-3 px-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($report->details->where('availability_status', true) as $detail)
                                        <span class="bg-background text-text-onsurface border-2 border-border-solid px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap">
                                            {{ $detail->category?->facility_name ?? '-' }}
                                        </span>
                                    @endforeach
                                    @if ($report->details->where('availability_status', true)->isEmpty())
                                        <span class="text-text-muted text-xs font-bold">-</span>
                                    @endif
                                </div>
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
