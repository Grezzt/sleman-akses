@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna')

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
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h4 class="m-0 font-black text-2xl text-text-primary">Daftar Pengguna</h4>
        
        <form action="{{ route('admin.users.index') }}" method="GET" class="w-full md:w-auto relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari di server..." 
                class="w-full md:w-80 px-4 py-2.5 border-2 border-border-solid rounded-xl font-bold focus:outline-none focus:ring-0 focus:border-primary shadow-[2px_2px_0px_0px_var(--color-border-solid)] pr-10">
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-text-muted hover:text-primary transition-colors">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <div class="admin-card m-0">
        <div class="overflow-x-auto pb-4">
            <table class="w-full text-left table-admin" id="usersTable">
                <thead>
                    <tr>
                        <th class="rounded-l-lg py-3 px-4">No</th>
                        <th class="py-3 px-4">Nama Lengkap</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Tanggal Daftar</th>
                        <th class="py-3 px-4">Jumlah Laporan</th>
                        <th class="rounded-r-lg py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $index => $user)
                        <tr class="hover:bg-slate-50 transition-colors {{ !$user->is_active ? 'bg-slate-50/50 grayscale-[20%]' : '' }}">
                            <td class="py-3 px-4 font-bold text-text-onsurface">{{ $users->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-text-onsurface">{{ $user->full_name }}</div>
                                @if(!$user->is_active)
                                    <span class="text-[10px] bg-slate-300 text-slate-700 px-2 py-0.5 rounded-full font-bold mt-1 inline-block">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-text-muted font-bold text-sm">{{ $user->email }}</td>
                            <td class="py-3 px-4 text-text-muted font-bold text-sm">{{ $user->created_at?->format('d M Y') }}</td>
                            <td class="py-3 px-4 text-primary font-black">{{ $user->reports_count }}</td>
                            <td class="py-3 px-4 text-center">
                                <form action="{{ route('admin.users.toggle_status', $user->id) }}" method="POST" class="form-toggle-status inline-block" data-id="{{ $user->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-toggle px-4 py-1.5 rounded-full font-bold text-xs border-2 transition-all {{ $user->is_active ? 'border-error text-error hover:bg-error hover:text-white' : 'border-success text-success hover:bg-success hover:text-white' }}">
                                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-text-muted font-bold">Belum ada pengguna atau data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="mt-6">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() {
        $('#usersTable').DataTable({
            paging: false,
            info: false,
            order: [],
        });
    });

    document.querySelectorAll('.form-toggle-status').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = form.querySelector('.btn-toggle');
            const originalText = btn.textContent;
            
            try {
                btn.textContent = '...';
                btn.disabled = true;
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams(new FormData(form))
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    window.showToast({
                        type: 'success',
                        title: 'Status Pengguna Diperbarui',
                        message: data.message
                    });
                    
                    const tr = form.closest('tr');
                    const nameContainer = tr.querySelector('td:nth-child(2)');
                    
                    if (data.is_active) {
                        tr.classList.remove('bg-slate-50/50', 'grayscale-[20%]');
                        btn.className = 'btn-toggle px-4 py-1.5 rounded-full font-bold text-xs border-2 transition-all border-error text-error hover:bg-error hover:text-white';
                        btn.textContent = 'Nonaktifkan';
                        
                        const badge = nameContainer.querySelector('span');
                        if (badge) badge.remove();
                    } else {
                        tr.classList.add('bg-slate-50/50', 'grayscale-[20%]');
                        btn.className = 'btn-toggle px-4 py-1.5 rounded-full font-bold text-xs border-2 transition-all border-success text-success hover:bg-success hover:text-white';
                        btn.textContent = 'Aktifkan';
                        
                        if (!nameContainer.querySelector('span')) {
                            nameContainer.innerHTML += '<span class="text-[10px] bg-slate-300 text-slate-700 px-2 py-0.5 rounded-full font-bold mt-1 inline-block">Nonaktif</span>';
                        }
                    }
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                window.showAlertDialog({
                    title: 'Gagal Memperbarui Status',
                    text: error.message,
                    icon: 'error'
                });
                btn.textContent = originalText;
            } finally {
                btn.disabled = false;
            }
        });
    });
</script>
@endpush
