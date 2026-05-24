@extends('layouts.app')

@section('title', 'Manajemen Kategori')
@section('page_title', 'Manajemen Kategori')

@section('content')
    <div class="flex flex-wrap justify-between items-center mb-6">
        <h4 class="m-0 font-black text-2xl text-text-primary">Kategori Fasilitas</h4>
        <button class="btn-primary-solid js-open-modal" data-target="createCategoryModal">Tambah Kategori</button>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-2 border-green-500 text-green-700 p-4 rounded-xl mb-6 font-bold shadow-[2px_2px_0_0_#22c55e]">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-card m-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left table-admin">
                <thead>
                    <tr>
                        <th class="rounded-l-lg py-3 px-4">No</th>
                        <th class="py-3 px-4">Nama Fasilitas</th>
                        <th class="py-3 px-4">Icon Marker</th>
                        <th class="rounded-r-lg py-3 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $index => $category)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 font-bold text-text-onsurface">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-text-onsurface">{{ $category->facility_name }}</td>
                            <td class="py-3 px-4">
                                @if ($category->icon_marker)
                                    <img src="{{ $category->icon_marker }}" alt="Icon" class="w-8 h-8 object-contain">
                                @else
                                    <span class="text-text-muted font-bold text-sm">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <button class="inline-block px-4 py-1.5 border-2 border-primary text-primary rounded-full text-xs font-bold hover:bg-primary hover:text-white transition-colors js-edit" 
                                    data-id="{{ $category->id }}"
                                    data-name="{{ $category->facility_name }}" 
                                    data-icon="{{ $category->icon_marker }}">
                                    Edit
                                </button>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline-block js-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-block px-4 py-1.5 border-2 border-error text-error rounded-full text-xs font-bold hover:bg-error hover:text-white transition-colors">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-text-muted font-bold">Belum ada kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Create -->
    <div id="createCategoryModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-surface rounded-2xl border-2 border-border-solid shadow-[4px_4px_0_0_var(--color-border-solid)] w-full max-w-md overflow-hidden flex flex-col">
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="p-6 border-b-2 border-border-solid flex justify-between items-center bg-slate-50">
                    <h5 class="text-lg font-black m-0 text-text-primary">Tambah Kategori</h5>
                    <button type="button" class="text-text-muted hover:text-error transition-colors js-close-modal" data-target="createCategoryModal">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <div class="p-6 flex flex-col gap-4">
                    <div>
                        <label class="block font-bold text-text-muted mb-2">Nama Fasilitas</label>
                        <input type="text" name="facility_name" class="input-solid w-full" required>
                    </div>
                    <div>
                        <label class="block font-bold text-text-muted mb-2">Icon Marker (URL)</label>
                        <input type="text" name="icon_marker" class="input-solid w-full">
                    </div>
                </div>
                <div class="p-6 border-t-2 border-border-solid flex justify-end gap-3 bg-slate-50">
                    <button type="button" class="px-4 py-2 border-2 border-border-solid rounded-full font-bold text-text-muted hover:bg-slate-200 transition-colors js-close-modal" data-target="createCategoryModal">Batal</button>
                    <button type="submit" class="btn-primary-solid">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editCategoryModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-surface rounded-2xl border-2 border-border-solid shadow-[4px_4px_0_0_var(--color-border-solid)] w-full max-w-md overflow-hidden flex flex-col">
            <form method="POST" id="editCategoryForm">
                @csrf
                @method('PUT')
                <div class="p-6 border-b-2 border-border-solid flex justify-between items-center bg-slate-50">
                    <h5 class="text-lg font-black m-0 text-text-primary">Edit Kategori</h5>
                    <button type="button" class="text-text-muted hover:text-error transition-colors js-close-modal" data-target="editCategoryModal">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <div class="p-6 flex flex-col gap-4">
                    <div>
                        <label class="block font-bold text-text-muted mb-2">Nama Fasilitas</label>
                        <input type="text" name="facility_name" class="input-solid w-full" id="editFacilityName" required>
                    </div>
                    <div>
                        <label class="block font-bold text-text-muted mb-2">Icon Marker (URL)</label>
                        <input type="text" name="icon_marker" class="input-solid w-full" id="editIconMarker">
                    </div>
                </div>
                <div class="p-6 border-t-2 border-border-solid flex justify-end gap-3 bg-slate-50">
                    <button type="button" class="px-4 py-2 border-2 border-border-solid rounded-full font-bold text-text-muted hover:bg-slate-200 transition-colors js-close-modal" data-target="editCategoryModal">Batal</button>
                    <button type="submit" class="btn-primary-solid">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Custom Modal Logic
        const openModal = (id) => {
            document.getElementById(id).classList.remove('hidden');
        };
        const closeModal = (id) => {
            document.getElementById(id).classList.add('hidden');
        };

        document.querySelectorAll('.js-open-modal').forEach(btn => {
            btn.addEventListener('click', () => openModal(btn.dataset.target));
        });
        document.querySelectorAll('.js-close-modal').forEach(btn => {
            btn.addEventListener('click', () => closeModal(btn.dataset.target));
        });

        const editForm = document.getElementById('editCategoryForm');

        document.querySelectorAll('.js-edit').forEach((button) => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;
                const name = button.dataset.name;
                const icon = button.dataset.icon;

                editForm.action = `{{ url('admin/categories') }}/${id}`;
                document.getElementById('editFacilityName').value = name || '';
                document.getElementById('editIconMarker').value = icon || '';

                openModal('editCategoryModal');
            });
        });

        document.querySelectorAll('.js-delete').forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                Swal.fire({
                    title: 'Hapus kategori ini?',
                    text: 'Data kategori akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn-primary-solid mr-2',
                        cancelButton: 'px-4 py-2 border-2 border-border-solid rounded-full font-bold text-text-muted hover:bg-slate-200 transition-colors'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
