@extends('layouts.app')

@section('title', 'Edit Berita & Kurasi Edukasi')
@section('page_title', 'Edit Berita & Kurasi Edukasi')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('admin.articles.index') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-text-primary font-black border-2 border-border-solid rounded-full shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all text-sm">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Daftar Berita</span>
            </a>

            <div class="flex items-center gap-2.5">
                {{-- Status Badge --}}
                <x-badge-status :status="$article->status" />

                @if($article->is_scraped && $article->source_url)
                    <a href="{{ $article->source_url }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-100 hover:bg-sky-200 text-sky-900 font-black border-2 border-border-solid rounded-full shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all text-xs">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>Tinjau Sumber Asli</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="admin-card p-6 md:p-8 m-0 relative overflow-hidden">
            {{-- Header with Brand Mascot --}}
            <div class="flex items-center justify-between pb-6 mb-6 border-b-2 border-border-solid">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-300 text-amber-950 border-2 border-border-solid flex items-center justify-center shadow-[2px_2px_0px_0px_var(--color-border-solid)] flex-shrink-0">
                        <i class="fa-solid fa-pen-to-square text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-xl text-text-primary leading-tight">Perbarui & Kurasi Berita Edukasi</h3>
                        <p class="text-xs text-text-muted font-medium mt-0.5">Edit teks hasil ekstraksi, perbaiki tata bahasa, dan sisipkan foto dokumentasi</p>
                    </div>
                </div>
                <img src="{{ asset('maskot-genit.svg') }}" alt="Maskot Sleman Akses" class="w-14 h-14 hidden sm:block drop-shadow-sm flex-shrink-0">
            </div>

            <form action="{{ route('admin.articles.update', $article->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-rose-50 border-2 border-error text-error rounded-2xl font-bold shadow-[3px_3px_0px_0px_var(--color-border-solid)]">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fa-solid fa-circle-exclamation text-lg"></i>
                            <span>Terdapat beberapa kesalahan input:</span>
                        </div>
                        <ul class="list-disc pl-6 text-sm font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Judul Berita --}}
                    <div class="md:col-span-2">
                        <label for="title" class="block font-black text-xs uppercase text-text-primary tracking-wider mb-2">
                            Judul Berita / Materi Edukasi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}" required 
                            class="w-full px-4 py-3 bg-white border-2 border-border-solid rounded-xl font-bold focus:outline-none focus:border-primary shadow-[2px_2px_0px_0px_var(--color-border-solid)] text-sm">
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label for="category" class="block font-black text-xs uppercase text-text-primary tracking-wider mb-2">
                            Kategori Edukasi <span class="text-rose-500">*</span>
                        </label>
                        <select name="category" id="category" required 
                            class="w-full px-4 py-3 bg-white border-2 border-border-solid rounded-xl font-bold focus:outline-none focus:border-primary shadow-[2px_2px_0px_0px_var(--color-border-solid)] text-sm">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ old('category', $article->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Penulis / Kontributor --}}
                    <div>
                        <label for="author_name" class="block font-black text-xs uppercase text-text-primary tracking-wider mb-2">
                            Nama Penulis / Instansi
                        </label>
                        <input type="text" name="author_name" id="author_name" value="{{ old('author_name', $article->author_name) }}" 
                            class="w-full px-4 py-3 bg-white border-2 border-border-solid rounded-xl font-bold focus:outline-none focus:border-primary shadow-[2px_2px_0px_0px_var(--color-border-solid)] text-sm">
                    </div>

                    {{-- Ringkasan Singkat (Summary) --}}
                    <div class="md:col-span-2">
                        <label for="summary" class="block font-black text-xs uppercase text-text-primary tracking-wider mb-2">
                            Ringkasan Singkat (Lead / Excerpt)
                            <span class="text-[11px] font-medium text-text-muted normal-case">(Ditampilkan di kartu preview mobile & web)</span>
                        </label>
                        <textarea name="summary" id="summary" rows="3" 
                            class="w-full px-4 py-3 bg-white border-2 border-border-solid rounded-xl font-medium focus:outline-none focus:border-primary shadow-[2px_2px_0px_0px_var(--color-border-solid)] text-sm leading-relaxed">{{ old('summary', $article->summary) }}</textarea>
                    </div>

                    {{-- Isi Berita / Konten Lengkap dengan Alat Sisipkan Foto --}}
                    <div class="md:col-span-2">
                        <label for="content" class="block font-black text-xs uppercase text-text-primary tracking-wider mb-2">
                            Isi Artikel Lengkap <span class="text-rose-500">*</span>
                        </label>

                        {{-- Widget Sisipkan Foto ke Body Artikel (Neobrutalism Card) --}}
                        <div class="mb-3 p-4 bg-slate-50 border-2 border-border-solid rounded-2xl shadow-[3px_3px_0px_0px_var(--color-border-solid)]">
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="font-black text-xs uppercase text-text-primary tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-camera text-primary"></i>
                                    <span>Sisipkan Foto ke Body Artikel (In-Body Media)</span>
                                </span>
                                <span class="text-[11px] text-text-muted font-medium hidden sm:inline">Foto akan disematkan ke posisi kursor pada naskah artikel</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                {{-- Upload File --}}
                                <div class="md:col-span-4">
                                    <label class="block text-[11px] font-black uppercase text-text-muted mb-1">Unggah Berkas Foto:</label>
                                    <input type="file" id="bodyImageFile" accept="image/*" 
                                        class="w-full text-xs px-2.5 py-2 bg-white border-2 border-border-solid rounded-xl font-medium file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-2 file:border-border-solid file:text-xs file:font-black file:bg-secondary file:text-primary file:cursor-pointer shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                                </div>

                                {{-- Atau Masukkan URL --}}
                                <div class="md:col-span-4">
                                    <label class="block text-[11px] font-black uppercase text-text-muted mb-1">Atau Tautan URL Gambar Asli:</label>
                                    <input type="url" id="bodyImageUrl" placeholder="https://example.com/foto.jpg" 
                                        class="w-full text-xs px-3 py-2.5 bg-white border-2 border-border-solid rounded-xl font-bold focus:outline-none focus:border-primary shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                                </div>

                                {{-- Caption / Keterangan Foto --}}
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-black uppercase text-text-muted mb-1">Keterangan / Caption:</label>
                                    <input type="text" id="bodyImageCaption" placeholder="Contoh: Ramp difabel" 
                                        class="w-full text-xs px-3 py-2.5 bg-white border-2 border-border-solid rounded-xl font-bold focus:outline-none focus:border-primary shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                                </div>

                                {{-- Tombol Sisipkan --}}
                                <div class="md:col-span-2">
                                    <button type="button" id="btnInsertBodyImage" onclick="insertBodyImage()" 
                                        class="w-full px-4 py-2.5 bg-secondary text-primary font-black text-xs border-2 border-border-solid rounded-xl shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                        <span id="btnInsertText">Sisipkan Foto</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <textarea name="content" id="content" rows="10" required
                            class="w-full px-4 py-3 bg-white border-2 border-border-solid rounded-xl font-medium focus:outline-none focus:border-primary shadow-[2px_2px_0px_0px_var(--color-border-solid)] text-sm leading-relaxed">{{ old('content', $article->content) }}</textarea>
                    </div>

                    {{-- Pratinjau Foto Cover Saat Ini --}}
                    @if($article->image_url)
                        <div class="md:col-span-2 p-4 bg-slate-50 border-2 border-border-solid rounded-2xl shadow-[2px_2px_0px_0px_var(--color-border-solid)] flex items-center gap-4">
                            <img src="{{ $article->image_url }}" alt="Cover" class="w-24 h-20 rounded-xl object-cover border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] flex-shrink-0">
                            <div class="min-w-0">
                                <div class="text-xs font-black text-text-primary uppercase tracking-wider mb-1">Foto Cover Saat Ini (Data Asli)</div>
                                <div class="text-xs text-text-muted truncate max-w-md font-mono bg-white px-2 py-1 rounded border border-border-solid inline-block">{{ $article->image_url }}</div>
                            </div>
                        </div>
                    @endif

                    {{-- Ganti Foto Cover (File) --}}
                    <div>
                        <label for="image_file" class="block font-black text-xs uppercase text-text-primary tracking-wider mb-2">
                            Ganti Foto Cover (Upload Baru)
                        </label>
                        <input type="file" name="image_file" id="image_file" accept="image/*"
                            class="w-full px-4 py-2.5 bg-white border-2 border-border-solid rounded-xl font-medium focus:outline-none focus:border-primary shadow-[2px_2px_0px_0px_var(--color-border-solid)] text-sm file:mr-3 file:py-1 file:px-3.5 file:rounded-full file:border-2 file:border-border-solid file:font-black file:bg-secondary file:text-primary file:cursor-pointer">
                    </div>

                    {{-- Atau Ganti Tautan URL --}}
                    <div>
                        <label for="image_url" class="block font-black text-xs uppercase text-text-primary tracking-wider mb-2">
                            Atau Ubah Tautan URL Gambar Cover
                        </label>
                        <input type="url" name="image_url" id="image_url" value="{{ old('image_url', $article->image_url) }}" 
                            class="w-full px-4 py-3 bg-white border-2 border-border-solid rounded-xl font-bold focus:outline-none focus:border-primary shadow-[2px_2px_0px_0px_var(--color-border-solid)] text-sm">
                    </div>

                    {{-- Sumber Rujukan --}}
                    <div>
                        <label for="source_name" class="block font-black text-xs uppercase text-text-primary tracking-wider mb-2">
                            Nama Sumber Rujukan
                        </label>
                        <input type="text" name="source_name" id="source_name" value="{{ old('source_name', $article->source_name) }}" 
                            class="w-full px-4 py-3 bg-white border-2 border-border-solid rounded-xl font-bold focus:outline-none focus:border-primary shadow-[2px_2px_0px_0px_var(--color-border-solid)] text-sm">
                    </div>

                    <div>
                        <label for="source_url" class="block font-black text-xs uppercase text-text-primary tracking-wider mb-2">
                            Tautan Sumber Asli
                        </label>
                        <input type="url" name="source_url" id="source_url" value="{{ old('source_url', $article->source_url) }}" 
                            class="w-full px-4 py-3 bg-white border-2 border-border-solid rounded-xl font-bold focus:outline-none focus:border-primary shadow-[2px_2px_0px_0px_var(--color-border-solid)] text-sm">
                    </div>

                    {{-- Status Kurasi & Submit --}}
                    <div class="md:col-span-2 pt-5 border-t-2 border-border-solid flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $article->is_published ? '1' : '0') === '1' ? 'checked' : '' }} 
                                class="w-5 h-5 accent-primary rounded cursor-pointer border-2 border-border-solid">
                            <span class="font-black text-sm text-text-primary">Status: Terbitkan Artikel (Published & Live)</span>
                        </label>

                        <button type="submit" 
                            class="px-8 py-3.5 bg-primary text-white font-black border-2 border-border-solid rounded-full shadow-[3px_3px_0px_0px_var(--color-secondary)] hover:shadow-[1px_1px_0px_0px_var(--color-secondary)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all text-sm flex items-center gap-2">
                            <i class="fa-solid fa-check"></i>
                            <span>Perbarui Artikel</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        async function insertBodyImage() {
            const fileInput = document.getElementById('bodyImageFile');
            const urlInput = document.getElementById('bodyImageUrl');
            const captionInput = document.getElementById('bodyImageCaption');
            const contentArea = document.getElementById('content');
            const btnText = document.getElementById('btnInsertText');

            let imageUrl = urlInput.value.trim();
            const caption = captionInput.value.trim() || 'Dokumentasi Fasilitas';

            if (fileInput.files.length > 0) {
                btnText.innerText = 'Mengunggah...';
                const formData = new FormData();
                formData.append('image', fileInput.files[0]);
                formData.append('_token', '{{ csrf_token() }}');

                try {
                    const res = await fetch('{{ route('admin.articles.upload_body_image') }}', {
                        method: 'POST',
                        body: formData,
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success && data.url) {
                        imageUrl = data.url;
                    } else {
                        window.showAlertDialog({
                            title: 'Gagal Mengunggah Foto',
                            text: data.message || 'Gagal mengunggah berkas foto.',
                            icon: 'error'
                        });
                        btnText.innerText = 'Sisipkan Foto';
                        return;
                    }
                } catch (e) {
                    window.showAlertDialog({
                        title: 'Kesalahan Jaringan',
                        text: 'Terjadi kesalahan jaringan saat mengunggah foto.',
                        icon: 'error'
                    });
                    btnText.innerText = 'Sisipkan Foto';
                    return;
                }
            }

            if (!imageUrl) {
                window.showAlertDialog({
                    title: 'Foto Belum Ditentukan',
                    text: 'Silakan pilih berkas foto untuk diunggah atau masukkan tautan URL foto.',
                    icon: 'warning'
                });
                btnText.innerText = 'Sisipkan Foto';
                return;
            }

            const figureHtml = `\n\n<figure class="my-5 p-2 bg-white border-2 border-border-solid rounded-2xl shadow-[3px_3px_0px_0px_var(--color-border-solid)] inline-block max-w-full">\n  <img src="${imageUrl}" alt="${caption}" class="w-full rounded-xl object-cover border border-border-solid">\n  <figcaption class="text-xs text-text-muted mt-2 text-center font-black">${caption}</figcaption>\n</figure>\n\n`;

            const startPos = contentArea.selectionStart || contentArea.value.length;
            const endPos = contentArea.selectionEnd || contentArea.value.length;
            contentArea.value = contentArea.value.substring(0, startPos) + figureHtml + contentArea.value.substring(endPos);
            contentArea.focus();
            contentArea.selectionStart = startPos + figureHtml.length;
            contentArea.selectionEnd = startPos + figureHtml.length;

            fileInput.value = '';
            urlInput.value = '';
            captionInput.value = '';
            btnText.innerText = 'Sisipkan Foto';
        }
    </script>
    @endpush
@endsection
