@extends('layouts.app')

@section('title', 'Pratinjau Artikel: ' . $article->title)
@section('page_title', 'Pratinjau Berita & Edukasi')

@push('styles')
<style>
    .article-body figure {
        margin: 1.5rem 0;
        padding: 0.5rem;
        background: #ffffff;
        border: 2px solid var(--color-border-solid);
        border-radius: 16px;
        box-shadow: 3px 3px 0px 0px var(--color-border-solid);
        display: inline-block;
        max-width: 100%;
    }
    .article-body figure img {
        border-radius: 12px;
        width: 100%;
        border: 1px solid var(--color-border-solid);
        object-fit: cover;
    }
    .article-body figcaption {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--color-text-muted);
        text-align: center;
        margin-top: 0.5rem;
    }
    .article-body p {
        margin-bottom: 1.25rem;
        line-height: 1.8;
    }
</style>
@endpush

@section('content')

    {{-- Top Bar: Back Button & Quick Actions --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.articles.index') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-text-primary font-black border-2 border-border-solid rounded-full shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all text-sm">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Berita</span>
        </a>

        {{-- Curation & Management Action Buttons --}}
        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Status Badge --}}
            <x-badge-status :status="$article->status" />

            {{-- Tahap Pemilahan: ACC / Tolak jika status Pending --}}
            @if($article->isPending())
                <form action="{{ route('admin.articles.approve', $article->id) }}" method="POST">
                    @csrf
                    <button type="submit" 
                        class="px-4 py-2 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center gap-1.5"
                        title="Setujui dan terbitkan berita ini ke publik & mobile">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>Setujui (ACC)</span>
                    </button>
                </form>

                <form action="{{ route('admin.articles.reject', $article->id) }}" method="POST"
                      data-confirm="Tolak artikel berita ini agar tidak dipublikasikan ke publik & mobile?"
                      data-confirm-title="Tolak Artikel Berita"
                      data-confirm-btn="Tolak Berita"
                      data-confirm-color="danger">
                    @csrf
                    <button type="submit" 
                        class="px-4 py-2 rounded-full bg-rose-600 hover:bg-rose-700 text-white font-black text-xs border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center gap-1.5"
                        title="Tolak berita ini">
                        <i class="fa-solid fa-xmark text-xs"></i>
                        <span>Tolak</span>
                    </button>
                </form>
            @elseif($article->isRejected())
                <form action="{{ route('admin.articles.approve', $article->id) }}" method="POST">
                    @csrf
                    <button type="submit" 
                        class="px-4 py-2 rounded-full bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-black text-xs border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center gap-1.5"
                        title="Pulihkan dan terbitkan artikel ini">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span>Pulihkan & ACC</span>
                    </button>
                </form>
            @endif

            {{-- Edit Button --}}
            <a href="{{ route('admin.articles.edit', $article->id) }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-300 hover:bg-amber-400 text-amber-950 font-black text-xs border-2 border-border-solid rounded-full shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
               title="Edit Konten Berita & Foto">
                <i class="fa-solid fa-pen-to-square text-xs"></i>
                <span>Edit</span>
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
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-rose-600 hover:text-white text-slate-700 font-black text-xs border-2 border-border-solid rounded-full shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                    title="Hapus Permanen">
                    <i class="fa-solid fa-trash text-xs"></i>
                    <span>Hapus</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Main Preview Grid Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {{-- Left Column: Full Article Document Preview (8 Cols) --}}
        <div class="lg:col-span-8 flex flex-col gap-6">
            <div class="admin-card p-6 md:p-8 m-0 relative overflow-hidden bg-white">
                {{-- Badges Bar: Category & Engine --}}
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="inline-block px-3.5 py-1 text-xs font-black rounded-full border-2 border-border-solid shadow-[1px_1px_0px_0px_var(--color-border-solid)]
                        @if($article->category === 'Aksesibilitas') bg-emerald-100 text-emerald-900
                        @elseif($article->category === 'Regulasi & Kebijakan') bg-purple-100 text-purple-900
                        @elseif($article->category === 'Fasilitas Publik') bg-amber-100 text-amber-900
                        @else bg-blue-100 text-blue-900 @endif">
                        {{ $article->category }}
                    </span>

                    @if($article->ai_processed)
                        <span class="inline-flex items-center gap-1.5 text-xs font-black bg-purple-100 text-purple-900 px-3 py-1 rounded-full border-2 border-border-solid shadow-[1px_1px_0px_0px_var(--color-border-solid)]" title="Diproses dan disempurnakan secara semantik oleh Google Gemini AI">
                            <i class="fa-solid fa-wand-magic-sparkles text-[10px] text-purple-700"></i>
                            <span>Gemini AI Engine</span>
                        </span>
                    @elseif($article->is_scraped)
                        <span class="inline-flex items-center gap-1.5 text-xs font-black bg-cyan-100 text-cyan-900 px-3 py-1 rounded-full border-2 border-border-solid shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                            <i class="fa-solid fa-robot text-[10px] text-cyan-700"></i>
                            <span>Bot RSS Ekstraksi</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-xs font-black bg-emerald-100 text-emerald-900 px-3 py-1 rounded-full border-2 border-border-solid shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                            <i class="fa-solid fa-pen-nib text-[10px] text-emerald-700"></i>
                            <span>Manual Entry</span>
                        </span>
                    @endif

                    @if($article->isPublished())
                        <span class="inline-flex items-center gap-1 text-xs font-black bg-emerald-500 text-white px-3 py-1 rounded-full border-2 border-border-solid shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                            <i class="fa-solid fa-satellite-dish text-[10px]"></i>
                            <span>Live Publik & Mobile</span>
                        </span>
                    @endif
                </div>

                {{-- Article Title (H1) --}}
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-text-primary leading-tight mt-2 mb-4">
                    {{ $article->title }}
                </h1>

                {{-- Metadata Bar: Author, Time, Reading Time --}}
                <div class="flex flex-wrap items-center gap-y-2 gap-x-4 text-xs font-bold text-text-muted pb-5 mb-6 border-b-2 border-border-solid">
                    <div class="flex items-center gap-1.5 text-text-onsurface">
                        <i class="fa-solid fa-user-pen text-primary"></i>
                        <span>{{ $article->author_name ?: $article->source_name ?: 'Admin Sleman Akses' }}</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <i class="fa-regular fa-calendar-check text-text-muted"></i>
                        <span>{{ $article->published_at ? $article->published_at->format('d M Y, H:i') : $article->created_at->format('d M Y, H:i') }} WIB</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-text-muted"></i>
                        <span>{{ $article->reading_time_minutes }} menit baca</span>
                    </div>

                    @if($article->source_url)
                        <a href="{{ $article->source_url }}" target="_blank" rel="noopener noreferrer" 
                           class="inline-flex items-center gap-1 px-3 py-1 bg-slate-100 hover:bg-slate-200 text-primary border-2 border-border-solid rounded-full text-xs font-black shadow-[1px_1px_0px_0px_var(--color-border-solid)] transition-all ml-auto">
                            <span>Sumber Asli</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    @endif
                </div>

                {{-- Cover Photo --}}
                @if($article->image_url)
                    <div class="my-6 rounded-2xl overflow-hidden border-2 border-border-solid shadow-[4px_4px_0px_0px_var(--color-border-solid)] bg-slate-50">
                        <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full max-h-[460px] object-cover">
                        <div class="px-4 py-2 bg-slate-50 border-t-2 border-border-solid text-xs text-text-muted font-bold flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-camera text-primary"></i>
                                <span>Foto Cover: Dokumentasi Asli ({{ $article->source_name ?: 'Sleman Akses' }})</span>
                            </span>
                            <a href="{{ $article->image_url }}" target="_blank" class="text-primary hover:underline font-black text-[11px]">Buka Gambar Utuh &rarr;</a>
                        </div>
                    </div>
                @else
                    <div class="my-6 p-6 rounded-2xl border-2 border-dashed border-border-solid bg-slate-50 flex items-center justify-center gap-3 text-text-muted">
                        <i class="fa-regular fa-image text-2xl"></i>
                        <span class="font-bold text-xs">Artikel ini tidak menyertakan foto cover utama</span>
                    </div>
                @endif

                {{-- Summary / Lead Box --}}
                @if($article->summary)
                    <div class="p-4 sm:p-5 mb-6 bg-emerald-50/70 border-l-4 border-primary border-y-2 border-r-2 border-border-solid rounded-2xl shadow-[2px_2px_0px_0px_var(--color-border-solid)]">
                        <div class="text-[11px] font-black uppercase text-primary mb-1 tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-quote-left"></i>
                            <span>Ringkasan / Intisari Edukasi</span>
                        </div>
                        <p class="font-bold text-sm sm:text-base text-text-onsurface leading-relaxed m-0">
                            {{ $article->summary }}
                        </p>
                    </div>
                @endif

                {{-- Full Body Content --}}
                <div class="article-body text-text-onsurface text-sm sm:text-base font-normal leading-relaxed pt-2">
                    {!! nl2br($article->content) !!}
                </div>

                {{-- Footer Signature --}}
                <div class="mt-8 pt-6 border-t-2 border-border-solid flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-xs font-bold text-text-muted">
                    <div>
                        Dipublikasikan dalam sistem <strong>Sleman Akses</strong> sebagai materi edukasi & peningkatan kesadaran hak disabilitas fisik.
                    </div>
                    <img src="{{ asset('maskot-genit.svg') }}" alt="Maskot Sleman Akses" class="w-10 h-10 drop-shadow-sm flex-shrink-0">
                </div>
            </div>
        </div>

        {{-- Right Column: Inspector & Curation Sidebar (4 Cols) --}}
        <div class="lg:col-span-4 flex flex-col gap-6">
            {{-- Card 1: Status Kurasi & Keputusan Admin --}}
            <div class="admin-card p-5 m-0 bg-white">
                <div class="flex items-center gap-2 mb-3 pb-3 border-b-2 border-border-solid">
                    <div class="w-7 h-7 rounded-lg bg-secondary text-primary border-2 border-border-solid flex items-center justify-center text-xs font-black shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h4 class="font-black text-base text-text-primary m-0">Status Kurasi Admin</h4>
                </div>

                <div class="mb-4">
                    <div class="text-xs font-bold text-text-muted uppercase mb-1.5">Status Saat Ini:</div>
                    <div>
                        <x-badge-status :status="$article->status" />
                    </div>
                </div>

                @if($article->isPending())
                    <div class="p-3.5 bg-amber-50 border-2 border-amber-300 rounded-xl shadow-[2px_2px_0px_0px_var(--color-border-solid)] mb-4">
                        <div class="flex items-start gap-2 text-xs text-amber-950 font-bold leading-snug">
                            <i class="fa-solid fa-circle-exclamation text-amber-600 mt-0.5 flex-shrink-0"></i>
                            <span>Menunggu Keputusan: Berita ini belum tayang ke masyarakat. Harap teliti keakuratan informasi sebelum menyetujui.</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <form action="{{ route('admin.articles.approve', $article->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                class="w-full px-3 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-check"></i>
                                <span>Setujui (ACC)</span>
                            </button>
                        </form>

                        <form action="{{ route('admin.articles.reject', $article->id) }}" method="POST"
                              data-confirm="Tolak artikel berita ini agar tidak dipublikasikan ke publik?"
                              data-confirm-title="Tolak Artikel Berita"
                              data-confirm-btn="Tolak Berita"
                              data-confirm-color="danger">
                            @csrf
                            <button type="submit" 
                                class="w-full px-3 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-xmark"></i>
                                <span>Tolak Berita</span>
                            </button>
                        </form>
                    </div>
                @elseif($article->isPublished())
                    <div class="p-3.5 bg-emerald-50 border-2 border-emerald-300 rounded-xl shadow-[2px_2px_0px_0px_var(--color-border-solid)]">
                        <div class="flex items-start gap-2 text-xs text-emerald-950 font-bold leading-snug">
                            <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 flex-shrink-0"></i>
                            <span>Artikel telah Terbit Live. Konten dapat dibaca oleh seluruh pengguna aplikasi mobile Flutter dan portal web.</span>
                        </div>
                    </div>
                @else
                    <div class="p-3.5 bg-rose-50 border-2 border-rose-300 rounded-xl shadow-[2px_2px_0px_0px_var(--color-border-solid)] mb-3">
                        <div class="flex items-start gap-2 text-xs text-rose-950 font-bold leading-snug">
                            <i class="fa-solid fa-circle-xmark text-rose-600 mt-0.5 flex-shrink-0"></i>
                            <div>
                                <span>Artikel Ditolak.</span>
                                @if($article->rejection_reason)
                                    <div class="text-[11px] text-rose-800 mt-1 font-normal">Alasan: {{ $article->rejection_reason }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.articles.approve', $article->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                            class="w-full px-3 py-2.5 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-black text-xs border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>Pulihkan & Setujui Terbit</span>
                        </button>
                    </form>
                @endif
            </div>

            {{-- Card 2: Deteksi Fasilitas Difabel Inklusif (AI / Semantic Analysis) --}}
            <div class="admin-card p-5 m-0 bg-white">
                <div class="flex items-center gap-2 mb-3 pb-3 border-b-2 border-border-solid">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 border-2 border-border-solid flex items-center justify-center text-xs font-black shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                        <i class="fa-solid fa-wheelchair"></i>
                    </div>
                    <h4 class="font-black text-base text-text-primary m-0">Fasilitas Difabel Terdeteksi</h4>
                </div>

                @if(!empty($article->detected_facilities))
                    <div class="flex flex-wrap gap-2">
                        @foreach($article->detected_facilities as $facility)
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 text-text-primary font-black text-xs rounded-xl border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)]">
                                @if(str_contains(strtolower($facility), 'ramp'))
                                    <span class="text-sm">♿</span>
                                @elseif(str_contains(strtolower($facility), 'toilet'))
                                    <span class="text-sm">🚻</span>
                                @elseif(str_contains(strtolower($facility), 'parkir'))
                                    <span class="text-sm">🅿️</span>
                                @elseif(str_contains(strtolower($facility), 'lift'))
                                    <span class="text-sm">🛗</span>
                                @elseif(str_contains(strtolower($facility), 'guiding'))
                                    <span class="text-sm">🦯</span>
                                @else
                                    <span class="text-sm">🏷️</span>
                                @endif
                                <span>{{ $facility }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-3 bg-slate-50 border-2 border-border-solid rounded-xl text-xs font-bold text-text-muted">
                        Tidak ada tag fasilitas spesifik yang terdeteksi dalam artikel ini.
                    </div>
                @endif
            </div>

            {{-- Card 3: Informasi Sumber & Teknis --}}
            <div class="admin-card p-5 m-0 bg-white">
                <div class="flex items-center gap-2 mb-3 pb-3 border-b-2 border-border-solid">
                    <div class="w-7 h-7 rounded-lg bg-sky-100 text-sky-800 border-2 border-border-solid flex items-center justify-center text-xs font-black shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                        <i class="fa-solid fa-server"></i>
                    </div>
                    <h4 class="font-black text-base text-text-primary m-0">Rincian Teknis & Sumber</h4>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="block font-bold text-text-muted uppercase text-[10px]">Media / Publisher:</span>
                        <span class="font-black text-text-primary text-sm">{{ $article->source_name ?: 'Media Pemkab Sleman / Internal' }}</span>
                    </div>

                    @if($article->source_url)
                        <div>
                            <span class="block font-bold text-text-muted uppercase text-[10px]">Tautan Sumber Asli:</span>
                            <a href="{{ $article->source_url }}" target="_blank" rel="noopener noreferrer" 
                               class="text-primary hover:underline font-bold break-all flex items-center gap-1 mt-0.5">
                                <span class="truncate max-w-[240px]">{{ $article->source_url }}</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[9px] flex-shrink-0"></i>
                            </a>
                        </div>
                    @endif

                    <div>
                        <span class="block font-bold text-text-muted uppercase text-[10px]">Slug URL:</span>
                        <div class="font-mono text-[11px] bg-slate-100 p-2 rounded-lg border border-border-solid break-all mt-0.5 text-text-onsurface">
                            {{ $article->slug }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-border-solid">
                        <div>
                            <span class="block font-bold text-text-muted uppercase text-[10px]">ID Artikel:</span>
                            <span class="font-black text-text-primary text-sm">#{{ $article->id }}</span>
                        </div>
                        <div>
                            <span class="block font-bold text-text-muted uppercase text-[10px]">Metode:</span>
                            <span class="font-bold text-text-onsurface">
                                @if($article->ai_processed)
                                    Gemini AI
                                @elseif($article->is_scraped)
                                    Bot Scraper
                                @else
                                    Manual Input
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
