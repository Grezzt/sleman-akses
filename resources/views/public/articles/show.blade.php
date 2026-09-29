@extends('layouts.public')

@section('title', $article->title . ' - Sleman Akses')

@push('styles')
<style>
    .article-body {
        font-size: 1rem;
        line-height: 1.8;
        color: var(--color-text-onsurface, #0F0F0F);
    }
    .article-body p {
        margin-bottom: 1.25rem;
    }
    .article-body h2 {
        font-size: 1.5rem;
        font-weight: 900;
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: var(--color-text-primary, #037940);
    }
    .article-body h3 {
        font-size: 1.25rem;
        font-weight: 800;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        color: var(--color-text-primary, #037940);
    }
    .article-body blockquote {
        border-left: 4px solid var(--color-primary, #025C30);
        padding-left: 1rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: var(--color-text-muted, #4B5563);
        background: #f8fafc;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        border-radius: 0 12px 12px 0;
    }
    .article-body figure {
        margin: 2rem 0;
        padding: 0.75rem;
        background: #ffffff;
        border: 2px solid var(--color-border-solid, #D4D4D4);
        border-radius: 20px;
        box-shadow: 4px 4px 0px 0px var(--color-border-solid, #D4D4D4);
        display: block;
        max-width: 100%;
    }
    .article-body figure img {
        border-radius: 14px;
        width: 100%;
        max-height: 520px;
        object-fit: cover;
        border: 1px solid var(--color-border-solid, #D4D4D4);
    }
    .article-body figcaption {
        font-size: 0.8rem;
        font-weight: 800;
        color: var(--color-text-muted, #4B5563);
        text-align: center;
        margin-top: 0.6rem;
    }
    .article-body ul, .article-body ol {
        margin-bottom: 1.25rem;
        padding-left: 1.5rem;
    }
    .article-body ul {
        list-style-type: disc;
    }
    .article-body ol {
        list-style-type: decimal;
    }
    .article-body li {
        margin-bottom: 0.5rem;
    }
    .tts-active-highlight {
        background-color: #FEF08A !important;
        border-left: 4px solid var(--color-primary, #037940) !important;
        padding-left: 0.75rem !important;
        border-radius: 6px !important;
        transition: all 0.2s ease-in-out !important;
    }
</style>
@endpush

@section('content')
<!-- Breadcrumbs & Header Section -->
<section class="bg-background pt-32 lg:pt-36 pb-8 border-b-2 border-border-solid">
    <div class="container px-4 mx-auto max-w-6xl">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-bold text-text-muted mb-6 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:text-primary transition-colors">Beranda</a>
            <span>/</span>
            <a href="{{ route('public.articles.index') }}" class="hover:text-primary transition-colors">Berita & Edukasi</a>
            <span>/</span>
            <span class="text-primary font-black truncate max-w-xs sm:max-w-md">{{ $article->title }}</span>
        </nav>

        <!-- Category & Time Meta -->
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <span class="px-3.5 py-1 text-xs font-black rounded-full border-2 border-border-solid shadow-[1px_1px_0px_0px_var(--color-border-solid)]
                @if($article->category === 'Aksesibilitas') bg-emerald-100 text-emerald-900
                @elseif($article->category === 'Regulasi & Kebijakan') bg-purple-100 text-purple-900
                @elseif($article->category === 'Fasilitas Publik') bg-amber-100 text-amber-900
                @else bg-blue-100 text-blue-900 @endif">
                {{ $article->category }}
            </span>

            <span class="text-xs font-bold text-text-muted flex items-center gap-1.5">
                <i class="fa-regular fa-clock text-primary"></i>
                <span>{{ $article->reading_time_minutes }} menit baca</span>
            </span>

            <span class="text-xs font-bold text-text-muted flex items-center gap-1.5">
                <i class="fa-regular fa-calendar-check text-primary"></i>
                <span>{{ $article->published_at ? $article->published_at->format('d F Y, H:i') : $article->created_at->format('d F Y, H:i') }} WIB</span>
            </span>

            <span class="text-xs font-bold text-text-muted flex items-center gap-1.5">
                <i class="fa-regular fa-eye text-primary"></i>
                <span>{{ number_format($article->views_count, 0, ',', '.') }} kali dibaca</span>
            </span>
        </div>

        <!-- Main H1 Article Title -->
        <h1 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-black text-text-primary leading-tight max-w-4xl mb-6">
            {{ $article->title }}
        </h1>

        <!-- Author / Attribution Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4 py-4 border-t-2 border-border-solid">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-secondary text-primary border-2 border-border-solid flex items-center justify-center font-black text-sm shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div>
                    <div class="font-black text-sm text-text-onsurface leading-none">
                        {{ $article->author_name ?: $article->source_name ?: 'Tim Editorial Sleman Akses' }}
                    </div>
                    @if($article->source_name)
                        <div class="text-xs font-bold text-text-muted mt-1">
                            Sumber: <span class="text-primary">{{ $article->source_name }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Share Buttons Quick Links -->
            <div class="flex items-center gap-2">
                <span class="text-xs font-black text-text-muted mr-1 hidden sm:inline">Bagikan:</span>
                
                {{-- WhatsApp Share --}}
                <a href="https://api.whatsapp.com/send?text={{ urlencode($article->title . ' - ' . url()->current()) }}" 
                   target="_blank" rel="noopener noreferrer"
                   class="w-8 h-8 rounded-full bg-white hover:bg-emerald-500 hover:text-white text-emerald-600 border-2 border-border-solid flex items-center justify-center text-xs shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                   title="Bagikan ke WhatsApp">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>

                {{-- X / Twitter Share --}}
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(url()->current()) }}" 
                   target="_blank" rel="noopener noreferrer"
                   class="w-8 h-8 rounded-full bg-white hover:bg-slate-900 hover:text-white text-slate-900 border-2 border-border-solid flex items-center justify-center text-xs shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                   title="Bagikan ke X">
                    <i class="fa-brands fa-x-twitter"></i>
                </a>

                {{-- Copy URL Button --}}
                <button type="button" 
                        onclick="copyArticleLink()" 
                        class="w-8 h-8 rounded-full bg-white hover:bg-primary hover:text-white text-primary border-2 border-border-solid flex items-center justify-center text-xs shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                        title="Salin Tautan Artikel">
                    <i class="fa-solid fa-link"></i>
                </button>
            </div>
        </div>

        {{-- Accessibility Toolbar (Audio Reader TTS & Font Resizer) --}}
        <div id="accessibilityToolbar" class="mt-4 p-4 bg-white border-2 border-border-solid rounded-2xl shadow-[4px_4px_0px_0px_var(--color-border-solid)] flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            {{-- Audio Reader TTS Section --}}
            <div class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 border-2 border-border-solid flex items-center justify-center text-primary shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                        <i class="fa-solid fa-volume-high text-sm" id="ttsIcon"></i>
                    </div>
                    <div>
                        <div class="text-[11px] font-black uppercase text-primary tracking-wider">Audio Reader (TTS)</div>
                        <div class="text-xs font-bold text-text-muted" id="ttsStatus">Siap mendengarkan</div>
                    </div>
                </div>

                {{-- Player Controls --}}
                <div class="flex items-center gap-1.5 ml-0 sm:ml-2">
                    {{-- Play / Resume Button --}}
                    <button type="button" id="btnTtsPlay" onclick="togglePlayTts()" 
                            class="px-3.5 py-1.5 rounded-full bg-primary hover:bg-primary-hover text-white font-black text-xs border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-play text-xs" id="iconPlayPause"></i>
                        <span id="labelPlayPause">Dengarkan</span>
                    </button>

                    {{-- Stop Button --}}
                    <button type="button" id="btnTtsStop" onclick="stopTts()" 
                            class="px-3 py-1.5 rounded-full bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-700 font-bold text-xs border-2 border-border-solid transition-all flex items-center gap-1 hidden">
                        <i class="fa-solid fa-stop text-xs"></i>
                        <span>Stop</span>
                    </button>

                    {{-- Speed Dropdown / Buttons --}}
                    <div class="inline-flex items-center gap-1 bg-slate-100 border-2 border-border-solid rounded-full p-0.5 text-[11px] font-bold">
                        <button type="button" onclick="setTtsSpeed(0.8)" id="speedBtn08" class="px-2 py-0.5 rounded-full transition-all text-text-muted hover:text-text-primary">0.8x</button>
                        <button type="button" onclick="setTtsSpeed(1.0)" id="speedBtn10" class="px-2 py-0.5 rounded-full transition-all bg-primary text-white font-black shadow-sm">1.0x</button>
                        <button type="button" onclick="setTtsSpeed(1.2)" id="speedBtn12" class="px-2 py-0.5 rounded-full transition-all text-text-muted hover:text-text-primary">1.2x</button>
                    </div>
                </div>
            </div>

            {{-- Font Resizer Section --}}
            <div class="flex items-center justify-between md:justify-end gap-3 pt-3 md:pt-0 border-t-2 md:border-t-0 border-slate-100">
                <span class="text-xs font-black text-text-muted flex items-center gap-1">
                    <i class="fa-solid fa-text-height text-primary"></i>
                    <span>Ukuran Teks:</span>
                </span>
                <div class="inline-flex items-center gap-1 bg-slate-100 border-2 border-border-solid rounded-full p-0.5 text-xs font-black shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                    <button type="button" onclick="changeFontSize('small')" id="fontBtnSmall" class="px-2.5 py-1 rounded-full transition-all text-text-muted hover:text-text-primary font-bold text-xs" title="Ukuran Teks Kecil (15px)">A-</button>
                    <button type="button" onclick="changeFontSize('normal')" id="fontBtnNormal" class="px-2.5 py-1 rounded-full transition-all bg-primary text-white font-black shadow-sm text-xs" title="Ukuran Teks Normal (17px)">A</button>
                    <button type="button" onclick="changeFontSize('large')" id="fontBtnLarge" class="px-2.5 py-1 rounded-full transition-all text-text-muted hover:text-text-primary font-bold text-sm" title="Ukuran Teks Besar (20px)">A+</button>
                    <button type="button" onclick="changeFontSize('xlarge')" id="fontBtnXlarge" class="px-2.5 py-1 rounded-full transition-all text-text-muted hover:text-text-primary font-bold text-base" title="Ukuran Teks Sangat Besar (24px)">A++</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Article Main Content & Sidebar Section -->
<section class="py-12 bg-surface">
    <div class="container px-4 mx-auto max-w-6xl">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            
            <!-- Left: Article Content (8 Columns) -->
            <main class="lg:col-span-8 flex flex-col gap-8">
                
                {{-- Cover Photo --}}
                @if($article->image_url)
                    <div class="rounded-3xl overflow-hidden border-2 border-border-solid shadow-[4px_4px_0px_0px_var(--color-border-solid)] bg-slate-100">
                        <img src="{{ $article->image_url }}" 
                             alt="{{ $article->title }}" 
                             class="w-full max-h-[500px] object-cover">
                        <div class="p-3 bg-slate-50 border-t-2 border-border-solid text-xs text-text-muted font-bold flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-camera text-primary"></i>
                                <span>Dokumentasi Berita</span>
                            </span>
                            @if($article->source_name)
                                <span>Kredit: {{ $article->source_name }}</span>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Lead Summary Box --}}
                @if($article->summary)
                    <div class="p-6 rounded-2xl bg-emerald-50 border-2 border-primary/30 shadow-[3px_3px_0px_0px_var(--color-border-solid)] flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-full bg-primary text-white border-2 border-border-solid flex items-center justify-center flex-shrink-0 shadow-[1px_1px_0px_0px_var(--color-border-solid)] mt-0.5">
                            <i class="fa-solid fa-lightbulb text-sm text-secondary"></i>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-xs font-black uppercase tracking-wider text-primary mb-1">Ringkasan Wawasan</h2>
                            <p class="lead-summary-text text-sm font-bold text-emerald-950 leading-relaxed m-0">
                                {{ $article->summary }}
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Formatted Article Body --}}
                <div class="article-body" id="articleContentBody">
                    {!! nl2br($article->content) !!}
                </div>

                {{-- Detected Accessibility Facilities Box --}}
                @if(!empty($article->detected_facilities))
                    <div class="p-6 bg-slate-50 border-2 border-border-solid rounded-2xl shadow-[3px_3px_0px_0px_var(--color-border-solid)]">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-7 h-7 rounded-lg bg-primary text-white flex items-center justify-center text-xs">
                                <i class="fa-solid fa-wheelchair"></i>
                            </div>
                            <h2 class="text-sm font-black text-text-primary uppercase tracking-wide">Fasilitas Difabel Terkait</h2>
                        </div>
                        <p class="text-xs text-text-muted font-medium mb-3">
                            Artikel ini memuat pembahasan mengenai indikator sarana dan prasarana aksesibilitas berikut:
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($article->detected_facilities as $facility)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border-2 border-border-solid rounded-full font-black text-xs text-primary shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
                                    @if($facility === 'Ramp') ♿ Jalur Ramp Kursi Roda
                                    @elseif($facility === 'Toilet Disabilitas') 🚻 Toilet Aksesibel
                                    @elseif($facility === 'Parkir Khusus') 🅿️ Parkir Khusus Difabel
                                    @elseif($facility === 'Lift') 🛗 Lift Ramah Disabilitas
                                    @elseif($facility === 'Guiding Block') 🦯 Guiding Block Tunanetra
                                    @else 🏷️ {{ $facility }}
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Original Source Link --}}
                @if($article->source_url)
                    <div class="p-5 bg-white border-2 border-border-solid rounded-2xl shadow-[2px_2px_0px_0px_var(--color-border-solid)] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-100 border-2 border-border-solid flex items-center justify-center text-primary text-base flex-shrink-0">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </div>
                            <div>
                                <span class="text-xs font-black text-text-muted block">Rujukan Dokumen / Berita Asli</span>
                                <span class="text-sm font-bold text-text-primary">{{ $article->source_name ?: 'Penerbit Resmi Eksternal' }}</span>
                            </div>
                        </div>

                        <a href="{{ $article->source_url }}" target="_blank" rel="noopener noreferrer" 
                           class="btn-primary-solid inline-flex items-center justify-center gap-1.5 text-xs text-center">
                            <span>Kunjungi Sumber Asli</span>
                            <i class="fa-solid fa-external-link text-[10px]"></i>
                        </a>
                    </div>
                @endif

                {{-- Bottom Share & Back Bar --}}
                <div class="pt-6 border-t-2 border-border-solid flex flex-wrap items-center justify-between gap-4">
                    <a href="{{ route('public.articles.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-text-primary font-black border-2 border-border-solid rounded-full shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:shadow-[1px_1px_0px_0px_var(--color-border-solid)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all text-xs">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Kembali ke Semua Berita</span>
                    </a>

                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-text-muted">Bagikan Artikel:</span>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($article->title . ' - ' . url()->current()) }}" 
                           target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-full bg-emerald-600 text-white border-2 border-border-solid flex items-center justify-center text-sm shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:scale-105 transition-transform"
                           title="WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                        <button type="button" 
                                onclick="copyArticleLink()" 
                                class="w-9 h-9 rounded-full bg-white text-primary border-2 border-border-solid flex items-center justify-center text-sm shadow-[2px_2px_0px_0px_var(--color-border-solid)] hover:scale-105 transition-transform"
                                title="Salin Tautan">
                            <i class="fa-solid fa-link"></i>
                        </button>
                    </div>
                </div>
            </main>

            <!-- Right: Sidebar (4 Columns) -->
            <aside class="lg:col-span-4 flex flex-col gap-6 sticky top-28">
                
                {{-- Interactive Map CTA Card with Mascot --}}
                <div class="p-6 bg-emerald-50 border-2 border-border-solid rounded-2xl shadow-[4px_4px_0px_0px_var(--color-border-solid)] text-center relative overflow-hidden">
                    <div class="w-16 h-16 mx-auto mb-3 p-1.5 rounded-2xl bg-white border-2 border-border-solid shadow-[2px_2px_0px_0px_var(--color-border-solid)] flex items-center justify-center">
                        <img src="{{ asset('maskot-genit.svg') }}" alt="Maskot Sleman Akses" class="w-12 h-12 object-contain">
                    </div>
                    <h2 class="text-base font-black text-primary leading-snug mb-2">Cek Fasilitas Ramah Difabel di Sleman</h2>
                    <p class="text-xs text-text-muted font-medium leading-relaxed mb-4">
                        Lihat persebaran fasilitas umum yang memiliki jalur ramp, toilet khusus, dan guiding block melalui peta interaktif kami.
                    </p>
                    <a href="{{ url('/map') }}" class="btn-primary-solid inline-flex items-center justify-center gap-2 text-xs w-full">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Buka Peta Aksesibilitas</span>
                    </a>
                </div>

                {{-- Related Articles List --}}
                @if($relatedArticles->count() > 0)
                    <div class="card-solid p-6 bg-white">
                        <div class="flex items-center gap-2 pb-3 mb-4 border-b-2 border-border-solid">
                            <i class="fa-solid fa-newspaper text-primary"></i>
                            <h2 class="text-sm font-black text-text-primary uppercase tracking-wide">Wawasan Terkait</h2>
                        </div>

                        <div class="flex flex-col divide-y-2 divide-slate-100">
                            @foreach($relatedArticles as $related)
                                <a href="{{ route('public.articles.show', $related->slug) }}" 
                                   class="py-3.5 first:pt-0 last:pb-0 flex items-start gap-3 group">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden border border-border-solid flex-shrink-0 bg-slate-100">
                                        <img src="{{ $related->image_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-[10px] font-black text-primary uppercase block mb-0.5">
                                            {{ $related->category }}
                                        </span>
                                        <h3 class="text-xs font-black text-text-primary leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                                            {{ $related->title }}
                                        </h3>
                                        <span class="text-[10px] font-bold text-text-muted mt-1 block">
                                            {{ $related->published_at ? $related->published_at->format('d M Y') : $related->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Participation Callout --}}
                <div class="p-5 bg-white border-2 border-border-solid rounded-2xl shadow-[3px_3px_0px_0px_var(--color-border-solid)]">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-hand-holding-heart text-primary"></i>
                        <h2 class="text-xs font-black text-text-primary uppercase">Peduli Aksesibilitas</h2>
                    </div>
                    <p class="text-xs text-text-muted font-medium leading-relaxed">
                        Menemukan trotoar berlubang atau fasilitas publik yang belum ramah kursi roda di sekitar Anda? Laporkan melalui aplikasi mobile Sleman Akses untuk kami petakan.
                    </p>
                </div>
            </aside>

        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    function copyArticleLink() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            if (typeof window.showToast === 'function') {
                window.showToast({
                    type: 'success',
                    title: 'Tautan Tersalin',
                    message: 'Tautan artikel berhasil disalin ke papan klip!'
                });
            } else {
                alert('Tautan artikel berhasil disalin ke papan klip!');
            }
        }).catch(err => {
            console.error('Gagal menyalin tautan:', err);
        });
    }

    // ==========================================
    // Accessibility: Font Resizer Engine
    // ==========================================
    function changeFontSize(size) {
        const body = document.getElementById('articleContentBody');
        if (!body) return;

        const buttons = {
            'small': document.getElementById('fontBtnSmall'),
            'normal': document.getElementById('fontBtnNormal'),
            'large': document.getElementById('fontBtnLarge'),
            'xlarge': document.getElementById('fontBtnXlarge'),
        };

        const baseClasses = 'px-2.5 py-1 rounded-full transition-all text-text-muted hover:text-text-primary font-bold';
        const activeClasses = 'px-2.5 py-1 rounded-full transition-all bg-primary text-white font-black shadow-sm';

        Object.keys(buttons).forEach(key => {
            if (buttons[key]) {
                buttons[key].className = (key === size) ? activeClasses : baseClasses;
            }
        });

        if (size === 'small') {
            body.style.fontSize = '0.9375rem'; // 15px
            body.style.lineHeight = '1.7';
        } else if (size === 'large') {
            body.style.fontSize = '1.25rem'; // 20px
            body.style.lineHeight = '1.9';
        } else if (size === 'xlarge') {
            body.style.fontSize = '1.5rem'; // 24px
            body.style.lineHeight = '2.0';
        } else {
            body.style.fontSize = '1.0625rem'; // 17px
            body.style.lineHeight = '1.8';
        }

        try {
            localStorage.setItem('sleman_article_font_size', size);
        } catch (e) {}
    }

    // Restore saved font preference on load
    document.addEventListener('DOMContentLoaded', () => {
        try {
            const savedSize = localStorage.getItem('sleman_article_font_size');
            if (savedSize && ['small', 'normal', 'large', 'xlarge'].includes(savedSize)) {
                changeFontSize(savedSize);
            }
        } catch (e) {}
    });

    // ==========================================
    // Accessibility: Text-to-Speech (TTS) Engine
    // ==========================================
    let synth = ('speechSynthesis' in window) ? window.speechSynthesis : null;
    let textElements = [];
    let currentElementIndex = 0;
    let isTtsPlaying = false;
    let isTtsPaused = false;
    let ttsSpeed = 1.0;

    function initTextElements() {
        textElements = [];
        const summaryEl = document.querySelector('.lead-summary-text');
        if (summaryEl && summaryEl.innerText.trim()) {
            textElements.push(summaryEl);
        }
        const bodyEl = document.getElementById('articleContentBody');
        if (bodyEl) {
            const paragraphs = bodyEl.querySelectorAll('p, h2, h3, blockquote, li');
            paragraphs.forEach(p => {
                if (p.innerText.trim()) {
                    textElements.push(p);
                }
            });
        }
    }

    function clearTtsHighlight() {
        textElements.forEach(el => el.classList.remove('tts-active-highlight'));
    }

    function setTtsStatus(text, iconClass = 'fa-solid fa-volume-high') {
        const statusEl = document.getElementById('ttsStatus');
        const iconEl = document.getElementById('ttsIcon');
        if (statusEl) statusEl.innerText = text;
        if (iconEl) iconEl.className = `${iconClass} text-sm`;
    }

    function updatePlayButtonUI() {
        const btnPlay = document.getElementById('btnTtsPlay');
        const iconPlay = document.getElementById('iconPlayPause');
        const labelPlay = document.getElementById('labelPlayPause');
        const btnStop = document.getElementById('btnTtsStop');

        if (isTtsPlaying && !isTtsPaused) {
            iconPlay.className = 'fa-solid fa-pause text-xs';
            labelPlay.innerText = 'Jeda';
            btnPlay.classList.replace('bg-primary', 'bg-amber-600');
            btnPlay.classList.replace('hover:bg-primary-hover', 'hover:bg-amber-700');
            btnStop.classList.remove('hidden');
            setTtsStatus('Sedang membacakan...', 'fa-solid fa-volume-high fa-bounce');
        } else if (isTtsPaused) {
            iconPlay.className = 'fa-solid fa-play text-xs';
            labelPlay.innerText = 'Lanjut';
            btnPlay.classList.replace('bg-amber-600', 'bg-primary');
            btnPlay.classList.replace('hover:bg-amber-700', 'hover:bg-primary-hover');
            btnStop.classList.remove('hidden');
            setTtsStatus('Dijeda', 'fa-solid fa-pause');
        } else {
            iconPlay.className = 'fa-solid fa-play text-xs';
            labelPlay.innerText = 'Dengarkan';
            btnPlay.classList.replace('bg-amber-600', 'bg-primary');
            btnPlay.classList.replace('hover:bg-amber-700', 'hover:bg-primary-hover');
            btnStop.classList.add('hidden');
            setTtsStatus('Siap mendengarkan', 'fa-solid fa-volume-high');
            clearTtsHighlight();
        }
    }

    function playElementAt(index) {
        if (!synth) return;
        if (index >= textElements.length) {
            stopTts();
            setTtsStatus('Selesai dibacakan', 'fa-solid fa-circle-check');
            return;
        }

        currentElementIndex = index;
        clearTtsHighlight();

        const targetEl = textElements[index];
        targetEl.classList.add('tts-active-highlight');
        targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

        const textToRead = targetEl.innerText.trim();
        const utterance = new SpeechSynthesisUtterance(textToRead);
        utterance.lang = 'id-ID';
        utterance.rate = ttsSpeed;

        const voices = synth.getVoices();
        const indonesianVoice = voices.find(v => v.lang.includes('id') || v.lang.includes('ID'));
        if (indonesianVoice) {
            utterance.voice = indonesianVoice;
        }

        utterance.onend = function() {
            if (isTtsPlaying && !isTtsPaused) {
                playElementAt(index + 1);
            }
        };

        utterance.onerror = function(e) {
            if (isTtsPlaying && !isTtsPaused) {
                playElementAt(index + 1);
            }
        };

        synth.speak(utterance);
    }

    function togglePlayTts() {
        if (!synth) {
            alert('Peramban Anda belum mendukung fitur Web Speech API (Text-to-Speech).');
            return;
        }

        if (!isTtsPlaying) {
            initTextElements();
            if (textElements.length === 0) return;
            synth.cancel();
            isTtsPlaying = true;
            isTtsPaused = false;
            updatePlayButtonUI();
            playElementAt(0);
        } else if (isTtsPaused) {
            synth.resume();
            isTtsPaused = false;
            updatePlayButtonUI();
        } else {
            synth.pause();
            isTtsPaused = true;
            updatePlayButtonUI();
        }
    }

    function stopTts() {
        if (synth) {
            synth.cancel();
        }
        isTtsPlaying = false;
        isTtsPaused = false;
        currentElementIndex = 0;
        updatePlayButtonUI();
    }

    function setTtsSpeed(speed) {
        ttsSpeed = speed;
        const btns = {
            0.8: document.getElementById('speedBtn08'),
            1.0: document.getElementById('speedBtn10'),
            1.2: document.getElementById('speedBtn12'),
        };
        Object.keys(btns).forEach(s => {
            if (btns[s]) {
                if (parseFloat(s) === speed) {
                    btns[s].className = 'px-2 py-0.5 rounded-full transition-all bg-primary text-white font-black shadow-sm';
                } else {
                    btns[s].className = 'px-2 py-0.5 rounded-full transition-all text-text-muted hover:text-text-primary font-bold';
                }
            }
        });

        if (isTtsPlaying && !isTtsPaused) {
            synth.cancel();
            playElementAt(currentElementIndex);
        }
    }

    window.addEventListener('beforeunload', () => {
        if (synth) synth.cancel();
    });
</script>
@endpush
