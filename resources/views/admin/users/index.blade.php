@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna')

@section('content')
    <div class="flex flex-wrap justify-between items-center mb-6">
        <h4 class="m-0 font-black text-2xl text-text-primary">Daftar Pengguna</h4>
    </div>

    <div class="admin-card m-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left table-admin">
                <thead>
                    <tr>
                        <th class="rounded-l-lg py-3 px-4">No</th>
                        <th class="py-3 px-4">Nama</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Tanggal Daftar</th>
                        <th class="rounded-r-lg py-3 px-4">Jumlah Laporan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $index => $user)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 font-bold text-text-onsurface">{{ $users->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-bold text-text-onsurface">{{ $user->name }}</td>
                            <td class="py-3 px-4 text-text-muted font-bold text-sm">{{ $user->email }}</td>
                            <td class="py-3 px-4 text-text-muted font-bold text-sm">{{ $user->created_at?->format('d M Y') }}</td>
                            <td class="py-3 px-4 text-primary font-black">{{ $user->reports_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-text-muted font-bold">Belum ada pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
@endsection
