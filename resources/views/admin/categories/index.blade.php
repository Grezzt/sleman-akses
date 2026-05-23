@extends('layouts.app')

@section('title', 'Manajemen Kategori')
@section('page_title', 'Manajemen Kategori')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Kategori Fasilitas</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">Tambah Kategori</button>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Fasilitas</th>
                            <th>Icon Marker</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $index => $category)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $category->facility_name }}</td>
                                <td>
                                    @if ($category->icon_marker)
                                        <img src="{{ $category->icon_marker }}" alt="Icon"
                                            style="width: 28px; height: 28px;">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary js-edit" data-id="{{ $category->id }}"
                                        data-name="{{ $category->facility_name }}" data-icon="{{ $category->icon_marker }}">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                        class="d-inline js-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-muted">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Fasilitas</label>
                        <input type="text" name="facility_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon Marker (URL)</label>
                        <input type="text" name="icon_marker" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" id="editCategoryForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Fasilitas</label>
                        <input type="text" name="facility_name" class="form-control" id="editFacilityName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon Marker (URL)</label>
                        <input type="text" name="icon_marker" class="form-control" id="editIconMarker">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
        const editForm = document.getElementById('editCategoryForm');

        document.querySelectorAll('.js-edit').forEach((button) => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;
                const name = button.dataset.name;
                const icon = button.dataset.icon;

                editForm.action = `{{ url('admin/categories') }}/${id}`;
                document.getElementById('editFacilityName').value = name || '';
                document.getElementById('editIconMarker').value = icon || '';

                editModal.show();
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
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
