@extends('layouts.app')

@section('title', 'Manajemen Berita & Kurasi Edukasi')
@section('page_title', 'Manajemen Berita & Kurasi Edukasi')

@section('content')

    {{-- Header Section & Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        {{-- Total Berita --}}
        <div class="admin-card p-4 m-0 flex flex-col justify-between" style="border-top: 4px solid var(--color-primary);">
            <div class="text-xs font-black text-text-muted uppercase tracking-wider">Total Berita</div>
            <div class="flex items-end justify-between mt-2">
                <span class="text-2xl sm:text-3xl font-black text-primary leading-none">{{ $stats['total'] }}</span>
                <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm">
                    <i class="fa-solid fa-newspaper"></i>
                </div>
            </div>
        </div>

        {{-- Perlu Kurasi (ACC) --}}
        <div class="admin-card p-4 m-0 flex flex-col justify-between bg-amber-50/40 relative overflow-hidden" style="border-top: 4px solid var(--color-warning);">
            <div class="text-xs font-black text-amber-900 uppercase tracking-wider flex items-center justify-between">
                <span>Perlu Kurasi (ACC)</span>
                @if($stats['pending'] > 0)
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                @endif
            </div>
            <div class="flex items-end justify-between mt-2">
                <span class="text-2xl sm:text-3xl font-black text-amber-600 leading-none">{{ $stats['pending'] }}</span>
                <span class="text-xs font-black text-amber-800 bg-amber-200 border-2 border-amber-300 px-2 py-0.5 rounded-full">
                    Antrean
                </span>
            </div>
        </div>

        {{-- Disetujui (Live) --}}
        <div class="admin-card p-4 m-0 flex flex-col justify-between bg-emerald-50/30" style="border-top: 4px solid var(--color-success);">
            <div class="text-xs font-black text-emerald-900 uppercase tracking-wider">Disetujui (Live)</div>
            <div class="flex items-end justify-between mt-2">
                <span class="text-2xl sm:text-3xl font-black text-emerald-600 leading-none">{{ $stats['published'] }}</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>

        {{-- Ditolak --}}
        <div class="admin-card p-4 m-0 flex flex-col justify-between bg-rose-50/30" style="border-top: 4px solid var(--color-error);">
            <div class="text-xs font-black text-rose-900 uppercase tracking-wider">Ditolak</div>
            <div class="flex items-end justify-between mt-2">
                <span class="text-2xl sm:text-3xl font-black text-rose-600 leading-none">{{ $stats['rejected'] }}</span>
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
            </div>
        </div>

        {{-- Olahan Gemini AI --}}
        <div class="admin-card p-4 m-0 flex flex-col justify-between bg-purple-50/30 col-span-2 sm:col-span-1" style="border-top: 4px solid #9333ea;">
            <div class="text-xs font-black text-purple-900 uppercase tracking-wider">Olahan Gemini AI</div>
            <div class="flex items-end justify-between mt-2">
                <span class="text-2xl sm:text-3xl font-black text-purple-600 leading-none">{{ $stats['ai'] }}</span>
                <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
            </div>
        </div>

        {{-- Total Pembaca --}}
        <div class="admin-card p-4 m-0 flex flex-col justify-between bg-blue-50/30 col-span-2 sm:col-span-1" style="border-top: 4px solid #2563eb;">
            <div class="text-xs font-black text-blue-900 uppercase tracking-wider">Total Pembaca</div>
            <div class="flex items-end justify-between mt-2">
                <span class="text-2xl sm:text-3xl font-black text-blue-600 leading-none">{{ number_format($stats['total_views'] ?? 0, 0, ',', '.') }}</span>
                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-eye"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Tabs: Navigasi Tahap Pemilahan Berita (Pill Shape Neobrutalism) --}}
    <div class="flex items-center gap-3 pb-3 mb-6 overflow-x-auto">
        <a href="{{ route('admin.articles.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}"
           class="px-5 py-2 rounded-full font-black text-sm border-2 transition-all whitespace-nowrap flex items-center gap-2 {{ ($activeStatus === 'all' || empty($activeStatus)) ? 'bg-primary text-white border-primary shadow-[2px_2px_0px_0px_var(--color-secondary)] -translate-y-0.5 -translate-x-0.5' : 'bg-surface text-text-muted border-border-solid hover:bg-slate-100 hover:text-text-primary' }}">
            <span>Semua</span>
            <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ ($activeStatus === 'all' || empty($activeStatus)) ? 'bg-white/25 text-white' : 'bg-slate-200 text-text-muted' }}">{{ $stats['total'] }}</span>
        </a>

        <a href="{{ route('admin.articles.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}"
           class="px-5 py-2 rounded-full font-black text-sm border-2 transition-all whitespace-nowrap flex items-center gap-2 {{ $activeStatus === 'pending' ? 'bg-primary text-white border-primary shadow-[2px_2px_0px_0px_var(--color-secondary)] -translate-y-0.5 -translate-x-0.5' : 'bg-surface text-text-muted border-border-solid hover:bg-slate-100 hover:text-text-primary' }}">
            <i class="fa-solid fa-inbox text-xs"></i>
            <span>Antrean Kurasi</span>
            @if($stats['pending'] > 0)
                <span class="px-2 py-0.5 text-xs bg-amber-400 text-amber-950 border border-amber-500 rounded-full font-black animate-pulse">{{ $stats['pending'] }}</span>
            @else
                <span class="px-2 py-0.5 text-xs bg-slate-200 text-text-muted rounded-full font-bold">0</span>
            @endif
        </a>

        <a href="{{ route('admin.articles.index', array_merge(request()->except('status', 'page'), ['status' => 'published'])) }}"
           class="px-5 py-2 rounded-full font-black text-sm border-2 transition-all whitespace-nowrap flex items-center gap-2 {{ $activeStatus === 'published' ? 'bg-primary text-white border-primary shadow-[2px_2px_0px_0px_var(--color-secondary)] -translate-y-0.5 -translate-x-0.5' : 'bg-surface text-text-muted border-border-solid hover:bg-slate-100 hover:text-text-primary' }}">
            <i class="fa-solid fa-circle-check text-xs"></i>
            <span>Disetujui / Terbit</span>
            <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $activeStatus === 'published' ? 'bg-white/25 text-white' : 'bg-slate-200 text-text-muted' }}">{{ $stats['published'] }}</span>
        </a>

        <a href="{{ route('admin.articles.index', array_merge(request()->except('status', 'page'), ['status' => 'rejected'])) }}"
           class="px-5 py-2 rounded-full font-black text-sm border-2 transition-all whitespace-nowrap flex items-center gap-2 {{ $activeStatus === 'rejected' ? 'bg-primary text-white border-primary shadow-[2px_2px_0px_0px_var(--color-secondary)] -translate-y-0.5 -translate-x-0.5' : 'bg-surface text-text-muted border-border-solid hover:bg-slate-100 hover:text-text-primary' }}">
            <i class="fa-solid fa-circle-xmark text-xs"></i>
            <span>Ditolak</span>
            <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $activeStatus === 'rejected' ? 'bg-white/25 text-white' : 'bg-slate-200 text-text-muted' }}">{{ $stats['rejected'] }}</span>
        </a>
    </div>

    {{-- Action & Filter Bar --}}
    <div class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center mb-6 gap-4">
        {{-- Search & Filter Form --}}
        <form action="{{ route('admin.articles.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <input type="hidden" name="status" value="{{ $activeStatus }}">

            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, sumber..." 
                    class="w-full px-4 py-2.5 bg-white border-2 border-border-solid rounded-full font-bold focus:outline-none focus:border-primary shadow-[2px_2px_0px_0px_var(--color-border-solid)] pr-10 text-sm">
                <button type="submit" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-text-muted hover:text-primary transition-colors">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <select name="category" onchange="this.form.submit()" 
                class="px-4 py-2.5 bg-white border-2 border-border-solid rounded-full font-bold text-sm shadow-[2px_2px_0px_0px_var(--color-border-solid)] focus:outline-none focus:border-primary">
                <option value="Semua">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>

            @if(request()->anyFilled(['search', 'category']))
                <a href="{{ route('admin.articles.index', ['status' => $activeStatus]) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 border-2 border-border-solid rounded-full font-bold text-sm transition-all shadow-[2px_2px_0px_0px_var(--color-border-solid)] text-text-onsurface">
                    <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                </a>
            @endif
        </form>

        {{-- Action Buttons: Tombol Tarik Berita & Tulis Manual --}}
        <div class="flex flex-wrap items-center gap-3 flex-shrink-0">
            <button type="button" onclick="openScrapeModal()" 
                class="flex items-center gap-2 px-5 py-2.5 bg-secondary text-primary font-black border-2 border-border-solid rounded-full shadow-[3px_3px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all text-xs sm:text-sm">
                <i class="fa-solid fa-cloud-arrow-down"></i>
                <span>Tarik Berita Eksternal</span>
            </button>

            <a href="{{ route('admin.articles.create') }}" 
                class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white font-black border-2 border-border-solid rounded-full shadow-[3px_3px_0px_0px_var(--color-secondary)] hover:shadow-[1px_1px_0px_0px_var(--color-secondary)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all text-xs sm:text-sm">
                <i class="fa-solid fa-plus"></i>
                <span>Tulis Manual</span>
            </a>
        </div>
    </div>

    {{-- Articles Table Card --}}
    <div class="admin-card p-0 overflow-hidden m-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left table-admin text-sm">
                <thead>
                    <tr>
                        <th class="py-3.5 px-3 w-10 text-center rounded-l-lg">
                            <input type="checkbox" id="selectAllArticles" class="w-4 h-4 rounded border-2 border-border-solid text-primary focus:ring-primary cursor-pointer" title="Pilih Semua di Halaman Ini">
                        </th>
                        <th class="py-3.5 px-3 w-12 text-center">No</th>
                        <th class="py-3.5 px-4 min-w-[260px]">Artikel & Edukasi</th>
                        <th class="py-3.5 px-4 min-w-[140px]">Kategori</th>
                        <th class="py-3.5 px-4 min-w-[140px]">Sumber & Engine</th>
                        <th class="py-3.5 px-4 min-w-[110px]">Tanggal</th>
                        <th class="py-3.5 px-3 min-w-[90px] text-center">Pembaca</th>
                        <th class="py-3.5 px-4 min-w-[120px] text-center">Status Kurasi</th>
                        <th class="py-3.5 px-4 min-w-[170px] text-center rounded-r-lg">Aksi / Keputusan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($articles as $index => $article)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $article->isPending() ? 'bg-amber-50/30' : ($article->isRejected() ? 'bg-rose-50/20' : '') }}" data-article-id="{{ $article->id }}">
                            <td class="py-3.5 px-3 text-center">
                                <input type="checkbox" name="selected_articles[]" value="{{ $article->id }}" class="article-checkbox w-4 h-4 rounded border-2 border-border-solid text-primary focus:ring-primary cursor-pointer">
                            </td>
                            <td class="py-3.5 px-3 text-center font-bold text-text-muted">
                                {{ $articles->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-start gap-3">
                                    @if($article->image_url)
                                        <a href="{{ route('admin.articles.show', $article->id) }}" class="flex-shrink-0 group" title="Pratinjau Berita">
                                            <img src="{{ $article->image_url }}" 
                                                 alt="{{ $article->title }}" 
                                                 class="w-16 h-14 rounded-xl object-cover border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] group-hover:scale-105 transition-transform">
                                        </a>
                                    @else
                                        <a href="{{ route('admin.articles.show', $article->id) }}" class="flex-shrink-0" title="Pratinjau Berita">
                                            <div class="w-16 h-14 rounded-xl bg-slate-100 border-2 border-border-solid flex items-center justify-center text-slate-400 shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:bg-slate-200 transition-colors">
                                                 <i class="fa-solid fa-newspaper text-lg"></i>
                                            </div>
                                        </a>
                                    @endif
                                    <div class="flex flex-col min-w-0">
                                        <a href="{{ route('admin.articles.show', $article->id) }}" 
                                           class="font-black text-text-primary leading-snug line-clamp-2 hover:text-primary transition-colors cursor-pointer"
                                           title="Klik untuk melihat pratinjau berita lengkap">
                                            {{ $article->title }}
                                        </a>
                                        <div class="text-xs text-text-muted mt-1 line-clamp-1 font-medium">
                                            {{ $article->summary }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-block px-3 py-1 text-xs font-black rounded-full border-2 border-border-solid shadow-[1px_1px_0px_0px_var(--color-border-solid)]
                                    @if($article->category === 'Aksesibilitas') bg-emerald-100 text-emerald-900
                                    @elseif($article->category === 'Regulasi & Kebijakan') bg-purple-100 text-purple-900
                                    @elseif($article->category === 'Fasilitas Publik') bg-amber-100 text-amber-900
                                    @else bg-blue-100 text-blue-900 @endif">
                                    {{ $article->category }}
                                </span>
                                @if(!empty($article->detected_facilities))
                                    <div class="flex flex-wrap gap-1 mt-1.5">
                                        @foreach($article->detected_facilities as $facility)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-white text-text-primary px-2 py-0.5 rounded-md border-2 border-border-solid shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                                                @if(str_contains(strtolower($facility), 'ramp')) ♿
                                                @elseif(str_contains(strtolower($facility), 'toilet')) 🚻
                                                @elseif(str_contains(strtolower($facility), 'parkir')) 🅿️
                                                @elseif(str_contains(strtolower($facility), 'lift')) 🛗
                                                @elseif(str_contains(strtolower($facility), 'guiding')) 🦯
                                                @else 🏷️ @endif
                                                {{ $facility }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-xs text-text-primary truncate max-w-[140px]" title="{{ $article->author_name ?: $article->source_name }}">
                                    {{ $article->author_name ?: $article->source_name ?: 'Admin' }}
                                </div>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                    @if($article->ai_processed)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-black bg-purple-100 text-purple-900 px-2.5 py-0.5 rounded-full border-2 border-border-solid shadow-[1px_1px_0px_0px_var(--color-border-solid)]" title="Diproses dan disempurnakan oleh Google Gemini AI">
                                            <i class="fa-solid fa-wand-magic-sparkles text-[9px] text-purple-700"></i> Gemini AI
                                        </span>
                                    @elseif($article->is_scraped)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-black bg-cyan-100 text-cyan-900 px-2.5 py-0.5 rounded-full border-2 border-border-solid shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                                            <i class="fa-solid fa-robot text-[9px] text-cyan-700"></i> Bot RSS
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-black bg-emerald-100 text-emerald-900 px-2.5 py-0.5 rounded-full border-2 border-border-solid shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                                            <i class="fa-solid fa-pen-nib text-[9px] text-emerald-700"></i> Manual
                                        </span>
                                    @endif

                                    @if($article->source_url)
                                        <a href="{{ $article->source_url }}" target="_blank" rel="noopener noreferrer" 
                                           class="text-[10px] text-primary hover:underline font-bold inline-flex items-center gap-0.5 ml-1" title="Buka tautan berita resmi asli">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[8px]"></i> Rujukan
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-bold text-text-muted">
                                <div>{{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}</div>
                                <div class="text-[11px] text-text-muted/80 mt-0.5"><i class="fa-regular fa-clock mr-0.5"></i> {{ $article->reading_time_minutes }} menit</div>
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 border border-slate-300 text-slate-700 text-xs font-bold shadow-sm">
                                    <i class="fa-solid fa-eye text-[10px] text-text-muted"></i>
                                    {{ number_format($article->views_count, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <x-badge-status :status="$article->status" />
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Tahap Pemilahan: Tombol ACC / Tolak jika status Pending --}}
                                    @if($article->isPending())
                                        <form action="{{ route('admin.articles.approve', $article->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                class="px-3.5 py-1.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center gap-1"
                                                title="Setujui (ACC) dan terbitkan berita ini ke publik & mobile">
                                                <i class="fa-solid fa-check text-xs"></i>
                                                <span>ACC</span>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.articles.reject', $article->id) }}" method="POST"
                                              data-confirm="Tolak artikel berita ini agar tidak dipublikasikan ke publik dan mobile?"
                                              data-confirm-title="Tolak Artikel Berita"
                                              data-confirm-btn="Tolak Berita"
                                              data-confirm-color="danger">
                                            @csrf
                                            <button type="submit" 
                                                class="px-3.5 py-1.5 rounded-full bg-rose-600 hover:bg-rose-700 text-white font-black text-xs border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center gap-1"
                                                title="Tolak berita ini">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                                <span>Tolak</span>
                                            </button>
                                        </form>
                                    @elseif($article->isRejected())
                                        {{-- Tombol Re-approve jika sebelumnya ditolak --}}
                                        <form action="{{ route('admin.articles.approve', $article->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                class="px-3.5 py-1.5 rounded-full bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-black text-xs border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center gap-1"
                                                title="Pulihkan dan terbitkan artikel ini">
                                                <i class="fa-solid fa-rotate-left text-xs"></i>
                                                <span>ACC</span>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Preview Button --}}
                                    <a href="{{ route('admin.articles.show', $article->id) }}" 
                                       class="w-8 h-8 flex items-center justify-center rounded-full bg-sky-100 hover:bg-sky-200 text-sky-900 font-black border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                                       title="Pratinjau Berita Lengkap">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>

                                    {{-- Edit Button --}}
                                    <a href="{{ route('admin.articles.edit', $article->id) }}" 
                                       class="w-8 h-8 flex items-center justify-center rounded-full bg-amber-300 hover:bg-amber-400 text-amber-950 font-black border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                                       title="Edit Konten Berita & Sisipkan Foto">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>

                                    {{-- Delete Button --}}
                                    <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" 
                                          data-confirm="Apakah Anda yakin ingin menghapus artikel berita ini secara permanen?"
                                          data-confirm-title="Hapus Artikel Permanen"
                                          data-confirm-btn="Hapus Permanen"
                                          data-confirm-color="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 hover:bg-rose-600 hover:text-white text-slate-700 font-black border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                                            title="Hapus Permanen">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-text-muted">
                                <div class="flex flex-col items-center justify-center max-w-md mx-auto p-4">
                                    <img src="{{ asset('maskot-side-eye.svg') }}" alt="Maskot Sleman Akses" class="w-24 h-24 mb-3 drop-shadow-sm">
                                    <div class="font-black text-lg text-text-primary">Tidak Ada Artikel Berita</div>
                                    <p class="text-xs text-text-muted mt-1 leading-relaxed">
                                        Saat ini belum ada data berita pada status ini. Anda dapat menarik berita edukasi secara otomatis atau menulis rujukan secara manual.
                                    </p>
                                    <div class="mt-4 flex items-center gap-3">
                                        <button type="button" onclick="openScrapeModal()" 
                                            class="px-5 py-2 bg-secondary text-primary font-black border-2 border-border-solid rounded-full shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all text-xs">
                                            <i class="fa-solid fa-cloud-arrow-down mr-1"></i> Tarik Berita Eksternal
                                        </button>
                                        <a href="{{ route('admin.articles.create') }}" 
                                            class="px-5 py-2 bg-primary text-white font-black border-2 border-border-solid rounded-full shadow-[2px_2px_0px_0px_var(--color-secondary)] hover:shadow-[1px_1px_0px_0px_var(--color-secondary)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all text-xs">
                                            <i class="fa-solid fa-plus mr-1"></i> Tulis Manual
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($articles->hasPages())
            <div class="p-4 border-t-2 border-border-solid bg-slate-50/50">
                {{ $articles->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Konfigurasi Penarikan Berita (Rentang Waktu & Pilihan Engine) --}}
    <div id="scrapeModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white border-2 border-border-solid rounded-2xl shadow-[6px_6px_0px_0px_var(--color-border-solid)] max-w-lg w-full p-6 relative">
            {{-- Close Button --}}
            <button type="button" onclick="closeScrapeModal()" class="w-8 h-8 rounded-full border-2 border-border-solid bg-white hover:bg-rose-500 hover:text-white flex items-center justify-center transition-all shadow-[2px_2px_0px_0px_var(--color-border-solid)] absolute top-4 right-4 text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>

            {{-- Modal Header --}}
            <div class="flex items-center gap-3 mb-5 pr-8">
                <div class="w-11 h-11 rounded-2xl bg-secondary text-primary border-2 border-border-solid flex items-center justify-center shadow-[2px_2px_0px_0px_var(--color-border-solid)] flex-shrink-0">
                    <i class="fa-solid fa-cloud-arrow-down text-lg"></i>
                </div>
                <div>
                    <h3 class="font-black text-lg text-text-primary leading-tight">Tarik Berita Eksternal</h3>
                    <p class="text-xs text-text-muted font-medium mt-0.5">Atur rentang waktu publikasi dan pilih mesin penarik</p>
                </div>
            </div>

            <form id="formScrapeGeneral" method="POST">
                @csrf
                {{-- Bagian 1: Rentang Waktu (Quick Options) --}}
                <div class="mb-5">
                    <label class="block font-black text-xs uppercase text-text-primary tracking-wider mb-2.5">
                        1. Pilih Rentang Waktu Terbit
                    </label>
                    <div class="grid grid-cols-2 gap-2.5">
                        {{-- Hari Ini --}}
                        <label class="relative flex flex-col p-3 border-2 border-border-solid rounded-xl cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:bg-emerald-50 has-[:checked]:border-primary has-[:checked]:shadow-[3px_3px_0px_0px_var(--color-primary)]">
                            <input type="radio" name="time_range" value="today" onchange="toggleCustomDates(this.value)" class="sr-only">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-black text-xs text-text-primary flex items-center gap-1.5">
                                    <i class="fa-solid fa-calendar-day text-emerald-600"></i>
                                    <span>Hari Ini</span>
                                </span>
                                <span class="text-[10px] font-black px-1.5 py-0.5 bg-emerald-100 text-emerald-800 rounded border border-emerald-300">24 Jam</span>
                            </div>
                            <span class="text-[11px] text-text-muted font-medium">Berita 24 jam terakhir</span>
                        </label>

                        {{-- Minggu Ini --}}
                        <label class="relative flex flex-col p-3 border-2 border-border-solid rounded-xl cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:bg-sky-50 has-[:checked]:border-sky-600 has-[:checked]:shadow-[3px_3px_0px_0px_#0284c7]">
                            <input type="radio" name="time_range" value="week" onchange="toggleCustomDates(this.value)" class="sr-only">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-black text-xs text-text-primary flex items-center gap-1.5">
                                    <i class="fa-solid fa-calendar-week text-sky-600"></i>
                                    <span>Minggu Ini</span>
                                </span>
                                <span class="text-[10px] font-black px-1.5 py-0.5 bg-sky-100 text-sky-800 rounded border border-sky-300">7 Hari</span>
                            </div>
                            <span class="text-[11px] text-text-muted font-medium">Berita 7 hari terakhir</span>
                        </label>

                        {{-- Bulan Ini (Default) --}}
                        <label class="relative flex flex-col p-3 border-2 border-border-solid rounded-xl cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:bg-purple-50 has-[:checked]:border-purple-600 has-[:checked]:shadow-[3px_3px_0px_0px_#7c3aed]">
                            <input type="radio" name="time_range" value="month" checked onchange="toggleCustomDates(this.value)" class="sr-only">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-black text-xs text-text-primary flex items-center gap-1.5">
                                    <i class="fa-solid fa-calendar text-purple-600"></i>
                                    <span>Bulan Ini</span>
                                </span>
                                <span class="text-[10px] font-black px-1.5 py-0.5 bg-purple-100 text-purple-800 rounded border border-purple-300">30 Hari</span>
                            </div>
                            <span class="text-[11px] text-text-muted font-medium">Berita 1 bulan terakhir</span>
                        </label>

                        {{-- Rentang Kustom --}}
                        <label class="relative flex flex-col p-3 border-2 border-border-solid rounded-xl cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:bg-amber-50 has-[:checked]:border-amber-600 has-[:checked]:shadow-[3px_3px_0px_0px_#d97706]">
                            <input type="radio" name="time_range" value="custom" onchange="toggleCustomDates(this.value)" class="sr-only">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-black text-xs text-text-primary flex items-center gap-1.5">
                                    <i class="fa-solid fa-sliders text-amber-600"></i>
                                    <span>Rentang Kustom</span>
                                </span>
                                <span class="text-[10px] font-black px-1.5 py-0.5 bg-amber-100 text-amber-800 rounded border border-amber-300">Bebas</span>
                            </div>
                            <span class="text-[11px] text-text-muted font-medium">Tentukan tanggal spesifik</span>
                        </label>
                    </div>

                    {{-- Form Input Tanggal Kustom --}}
                    <div id="customDateContainer" class="hidden mt-3 p-3.5 bg-slate-50 border-2 border-border-solid rounded-xl grid grid-cols-2 gap-3 shadow-[2px_2px_0px_0px_var(--color-border-solid)]">
                        <div>
                            <label class="block text-[11px] font-black uppercase text-text-muted mb-1">Tanggal Mulai:</label>
                            <input type="date" name="start_date" id="inputStartDate" 
                                class="w-full px-3 py-2 bg-white border-2 border-border-solid rounded-lg text-xs font-bold focus:outline-none focus:border-primary shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black uppercase text-text-muted mb-1">Tanggal Selesai:</label>
                            <input type="date" name="end_date" id="inputEndDate" 
                                class="w-full px-3 py-2 bg-white border-2 border-border-solid rounded-lg text-xs font-bold focus:outline-none focus:border-primary shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Pilihan Mesin Penarik (Bot vs AI) --}}
                <div class="mb-5">
                    <label class="block font-black text-xs uppercase text-text-primary tracking-wider mb-2.5">
                        2. Pilih Mesin Penarik (Engine)
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Opsi 1: Bot RSS --}}
                        <button type="button" onclick="submitScrape('bot')" id="btnModalScrapeBot"
                            class="p-3.5 bg-cyan-50 hover:bg-cyan-100 border-2 border-border-solid rounded-xl shadow-[3px_3px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all text-left flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <div class="w-7 h-7 rounded-lg bg-cyan-200 border-2 border-border-solid flex items-center justify-center text-cyan-900 shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                                        <i class="fa-solid fa-robot text-xs" id="iconModalBot"></i>
                                    </div>
                                    <span class="font-black text-xs text-cyan-950">Tarik via Bot</span>
                                </div>
                                <p class="text-[11px] text-cyan-800 leading-snug font-medium">Ekstraksi RSS standar tanpa kuota token, cepat & deterministik.</p>
                            </div>
                            <div class="mt-3 text-[10px] font-black uppercase text-cyan-800 flex items-center gap-1">
                                <span id="textModalBot">Mulai Tarik Bot &rarr;</span>
                            </div>
                        </button>

                        {{-- Opsi 2: Gemini AI --}}
                        <button type="button" onclick="submitScrape('ai')" id="btnModalScrapeAi"
                            class="p-3.5 bg-purple-50 hover:bg-purple-100 border-2 border-border-solid rounded-xl shadow-[3px_3px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all text-left flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <div class="w-7 h-7 rounded-lg bg-purple-200 border-2 border-border-solid flex items-center justify-center text-purple-900 shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                                        <i class="fa-solid fa-wand-magic-sparkles text-xs" id="iconModalAi"></i>
                                    </div>
                                    <span class="font-black text-xs text-purple-950">Tarik via AI</span>
                                </div>
                                <p class="text-[11px] text-purple-800 leading-snug font-medium">Analisis semantik, rewrite edukasi 2 paragraf, tag fasilitas & anti duplikat.</p>
                            </div>
                            <div class="mt-3 text-[10px] font-black uppercase text-purple-800 flex items-center gap-1">
                                <span id="textModalAi">Mulai Tarik AI &rarr;</span>
                            </div>
                        </button>
                    </div>
                </div>

                {{-- Catatan Kurasi --}}
                <div class="p-3.5 bg-amber-50 border-2 border-amber-300 rounded-xl text-[11px] text-amber-950 flex items-start gap-2.5 shadow-[2px_2px_0px_0px_var(--color-border-solid)] font-medium">
                    <i class="fa-solid fa-shield-halved text-amber-600 mt-0.5 flex-shrink-0 text-sm"></i>
                    <span>Semua hasil penarikan akan tersimpan pada status <strong>Pending (Antrean Kurasi)</strong> agar Anda dapat meninjau dan meng-ACC sebelum terbit live ke publik & mobile.</span>
                </div>
            </form>
        </div>
    </div>

    {{-- Floating Bulk Action Bar (Neobrutalism) --}}
    <div id="bulkActionBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 transition-all duration-300 translate-y-32 pointer-events-none opacity-0">
        <div class="bg-surface border-4 border-border-solid rounded-2xl p-4 shadow-[6px_6px_0px_0px_var(--color-border-solid)] flex flex-wrap items-center gap-4 pointer-events-auto max-w-2xl">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-primary text-white flex items-center justify-center font-black text-sm border-2 border-border-solid shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                    <span id="selectedCountBadge">0</span>
                </span>
                <span class="font-black text-sm text-text-primary whitespace-nowrap">Artikel Terpilih</span>
            </div>

            <div class="h-6 w-0.5 bg-border-solid hidden sm:block"></div>

            <div class="flex items-center gap-2 flex-wrap">
                {{-- Bulk ACC Button --}}
                <button type="button" onclick="submitBulkAction('approve')" 
                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center gap-1.5"
                        title="Setujui dan terbitkan seluruh artikel terpilih">
                    <i class="fa-solid fa-check"></i>
                    <span>ACC Terpilih</span>
                </button>

                {{-- Bulk Reject Button --}}
                <button type="button" onclick="openBulkRejectModal()" 
                        class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center gap-1.5"
                        title="Tolak seluruh artikel terpilih">
                    <i class="fa-solid fa-xmark"></i>
                    <span>Tolak Terpilih</span>
                </button>

                {{-- Bulk Delete Button --}}
                <button type="button" onclick="submitBulkAction('delete')" 
                        class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-black text-xs border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center gap-1.5"
                        title="Hapus permanen artikel terpilih">
                    <i class="fa-solid fa-trash-can"></i>
                    <span class="hidden sm:inline">Hapus</span>
                </button>

                {{-- Deselect All Button --}}
                <button type="button" onclick="clearSelectedArticles()" 
                        class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-text-muted font-bold text-xs border-2 border-border-solid transition-all">
                    Batal
                </button>
            </div>
        </div>
    </div>

    {{-- Hidden Form for Bulk Action Submission --}}
    <form id="formBulkAction" action="{{ route('admin.articles.bulk_action') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="action" id="bulkActionInput" value="">
        <input type="hidden" name="rejection_reason" id="bulkRejectionReasonInput" value="">
        <div id="bulkArticleIdsContainer"></div>
    </form>

    {{-- Modal Bulk Reject with Mascot --}}
    <div id="bulkRejectModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-surface border-4 border-border-solid rounded-3xl p-6 max-w-md w-full shadow-[8px_8px_0px_0px_var(--color-border-solid)] relative animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('maskot-nangis.svg') }}" alt="Maskot Sleman Akses" class="w-12 h-12 object-contain">
                    <div>
                        <h3 class="font-black text-lg text-text-primary leading-tight">Tolak Artikel Massal</h3>
                        <p class="text-xs text-text-muted font-bold"><span id="rejectCountText">0</span> artikel terpilih akan ditolak</p>
                    </div>
                </div>
                <button type="button" onclick="closeBulkRejectModal()" class="w-8 h-8 rounded-full border-2 border-border-solid bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-xs font-black">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="mb-4">
                <label for="inputBulkRejectReason" class="block text-xs font-black text-text-primary mb-1.5">Alasan Penolakan Kurasi:</label>
                <textarea id="inputBulkRejectReason" rows="3" 
                          placeholder="Contoh: Konten berita tidak relevan dengan isu aksesibilitas disabilitas di Sleman." 
                          class="w-full px-3.5 py-2.5 bg-background border-2 border-border-solid rounded-xl text-xs font-bold focus:outline-none focus:border-rose-600 transition-all"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeBulkRejectModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 font-black text-xs border-2 border-border-solid transition-all">
                    Batal
                </button>
                <button type="button" onclick="confirmBulkReject()" 
                        class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-ban"></i>
                    <span>Tolak Sekarang</span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Checkbox & Bulk Actions Logic
        const selectAllCheckbox = document.getElementById('selectAllArticles');
        const articleCheckboxes = document.querySelectorAll('.article-checkbox');
        const bulkActionBar = document.getElementById('bulkActionBar');
        const selectedCountBadge = document.getElementById('selectedCountBadge');

        function getSelectedArticleIds() {
            const checkedBoxes = document.querySelectorAll('.article-checkbox:checked');
            return Array.from(checkedBoxes).map(cb => cb.value);
        }

        function updateBulkBar() {
            const selectedIds = getSelectedArticleIds();
            const count = selectedIds.length;

            if (count > 0) {
                selectedCountBadge.innerText = count;
                bulkActionBar.classList.remove('translate-y-32', 'pointer-events-none', 'opacity-0');
                bulkActionBar.classList.add('translate-y-0', 'opacity-100');
            } else {
                bulkActionBar.classList.add('translate-y-32', 'pointer-events-none', 'opacity-0');
                bulkActionBar.classList.remove('translate-y-0', 'opacity-100');
            }

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = (count > 0 && count === articleCheckboxes.length);
                selectAllCheckbox.indeterminate = (count > 0 && count < articleCheckboxes.length);
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                articleCheckboxes.forEach(cb => {
                    cb.checked = selectAllCheckbox.checked;
                });
                updateBulkBar();
            });
        }

        articleCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkBar);
        });

        function clearSelectedArticles() {
            articleCheckboxes.forEach(cb => cb.checked = false);
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            updateBulkBar();
        }

        function submitBulkAction(action) {
            const selectedIds = getSelectedArticleIds();
            if (selectedIds.length === 0) {
                window.showAlertDialog({
                    title: 'Pilih Artikel Terlebih Dahulu',
                    text: 'Silakan centang minimal satu artikel untuk melakukan aksi massal.',
                    icon: 'warning',
                    confirmText: 'Mengerti'
                });
                return;
            }

            let confirmTitle = 'Konfirmasi Aksi Massal';
            let confirmText = `Apakah Anda yakin ingin memproses ${selectedIds.length} artikel terpilih?`;
            let confirmBtn = 'Ya, Lanjutkan';
            let confirmColor = 'primary';

            if (action === 'approve') {
                confirmTitle = 'ACC Artikel Massal';
                confirmText = `Apakah Anda yakin ingin menyetujui (ACC) dan menerbitkan ${selectedIds.length} artikel terpilih ke publik?`;
                confirmBtn = 'ACC Semua Terpilih';
                confirmColor = 'success';
            } else if (action === 'delete') {
                confirmTitle = 'Hapus Artikel Massal';
                confirmText = `PERINGATAN: ${selectedIds.length} artikel terpilih akan dihapus permanen. Tindakan ini tidak dapat dibatalkan!`;
                confirmBtn = 'Hapus Permanen';
                confirmColor = 'danger';
            }

            window.showConfirmDialog({
                title: confirmTitle,
                text: confirmText,
                icon: action === 'delete' ? 'danger' : 'info',
                confirmText: confirmBtn,
                confirmColor: confirmColor,
                onConfirm: function() {
                    const form = document.getElementById('formBulkAction');
                    document.getElementById('bulkActionInput').value = action;
                    const container = document.getElementById('bulkArticleIdsContainer');
                    container.innerHTML = '';

                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'article_ids[]';
                        input.value = id;
                        container.appendChild(input);
                    });

                    form.submit();
                }
            });
        }

        function openBulkRejectModal() {
            const selectedIds = getSelectedArticleIds();
            if (selectedIds.length === 0) {
                window.showAlertDialog({
                    title: 'Pilih Artikel Terlebih Dahulu',
                    text: 'Silakan centang minimal satu artikel untuk ditolak.',
                    icon: 'warning',
                    confirmText: 'Mengerti'
                });
                return;
            }

            document.getElementById('rejectCountText').innerText = selectedIds.length;
            document.getElementById('inputBulkRejectReason').value = '';
            document.getElementById('bulkRejectModal').classList.remove('hidden');
        }

        function closeBulkRejectModal() {
            document.getElementById('bulkRejectModal').classList.add('hidden');
        }

        function confirmBulkReject() {
            const selectedIds = getSelectedArticleIds();
            const reason = document.getElementById('inputBulkRejectReason').value.trim();

            const form = document.getElementById('formBulkAction');
            document.getElementById('bulkActionInput').value = 'reject';
            document.getElementById('bulkRejectionReasonInput').value = reason || 'Tidak memenuhi kriteria kurasi';

            const container = document.getElementById('bulkArticleIdsContainer');
            container.innerHTML = '';
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'article_ids[]';
                input.value = id;
                container.appendChild(input);
            });

            closeBulkRejectModal();
            form.submit();
        }

        // Scrape modal handlers
        function openScrapeModal() {
            document.getElementById('scrapeModal').classList.remove('hidden');
        }

        function closeScrapeModal() {
            document.getElementById('scrapeModal').classList.add('hidden');
        }

        function toggleCustomDates(value) {
            const container = document.getElementById('customDateContainer');
            if (value === 'custom') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        function submitScrape(type) {
            const form = document.getElementById('formScrapeGeneral');
            const timeRangeInput = form.querySelector('input[name="time_range"]:checked');
            const timeRange = timeRangeInput ? timeRangeInput.value : 'month';

            if (timeRange === 'custom') {
                const start = document.getElementById('inputStartDate').value;
                if (!start) {
                    window.showAlertDialog({
                        title: 'Rentang Waktu Belum Lengkap',
                        text: 'Silakan tentukan minimal Tanggal Mulai untuk rentang kustom.',
                        icon: 'warning',
                        confirmText: 'Mengerti'
                    });
                    return;
                }
            }

            if (type === 'ai') {
                form.action = '{{ route('admin.articles.scrape_ai') }}';
                const btn = document.getElementById('btnModalScrapeAi');
                const icon = document.getElementById('iconModalAi');
                const text = document.getElementById('textModalAi');
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                icon.className = 'fa-solid fa-spinner fa-spin text-xs';
                text.innerText = 'Memproses Gemini AI...';
            } else {
                form.action = '{{ route('admin.articles.scrape_bot') }}';
                const btn = document.getElementById('btnModalScrapeBot');
                const icon = document.getElementById('iconModalBot');
                const text = document.getElementById('textModalBot');
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                icon.className = 'fa-solid fa-spinner fa-spin text-xs';
                text.innerText = 'Menarik via Bot...';
            }

            form.submit();
        }

        // Close modals when clicking outside box
        window.addEventListener('click', function(e) {
            const scrapeModal = document.getElementById('scrapeModal');
            if (e.target === scrapeModal) {
                closeScrapeModal();
            }
            const bulkRejectModal = document.getElementById('bulkRejectModal');
            if (e.target === bulkRejectModal) {
                closeBulkRejectModal();
            }
        });
    </script>
    @endpush
@endsection
