{{-- Neobrutalism System Response Dialog & Alert Component with Mascot Assets --}}
@php
    $flashType = null;
    $flashMessage = null;
    $flashTitle = null;

    if (session('success')) {
        $flashType = 'success';
        $flashTitle = 'Berhasil!';
        $flashMessage = session('success');
    } elseif (session('error')) {
        $flashType = 'error';
        $flashTitle = 'Terjadi Kesalahan!';
        $flashMessage = session('error');
    } elseif (session('warning')) {
        $flashType = 'warning';
        $flashTitle = 'Peringatan!';
        $flashMessage = session('warning');
    } elseif (session('info')) {
        $flashType = 'info';
        $flashTitle = 'Informasi';
        $flashMessage = session('info');
    } elseif ($errors->any()) {
        $flashType = 'error';
        $flashTitle = 'Validasi Gagal';
        $flashMessage = $errors->first();
    }

    // Mapping maskot sesuai situasi dan kondisi
    $mascotAsset = match($flashType) {
        'success' => asset('laporan sukses.svg'),
        'error' => asset('maskot-nangis.svg'),
        'warning' => asset('maskot-side-eye.svg'),
        'info' => asset('maskot-genit.svg'),
        default => asset('maskot-genit.svg'),
    };
@endphp

@if ($flashType && $flashMessage)
    <div id="system-response-alert" 
         class="mb-6 p-4 rounded-2xl border-2 border-border-solid shadow-[4px_4px_0px_0px_var(--color-border-solid)] flex items-start justify-between gap-4 transition-all duration-300
         @if($flashType === 'success') bg-emerald-50 text-emerald-950
         @elseif($flashType === 'error') bg-rose-50 text-rose-950
         @elseif($flashType === 'warning') bg-amber-50 text-amber-950
         @else bg-sky-50 text-sky-950 @endif"
         role="alert">
        
        <div class="flex items-start gap-3.5 flex-1">
            {{-- Sleman Akses Mascot Avatar --}}
            <div class="w-13 h-13 rounded-2xl bg-white border-2 border-border-solid flex items-center justify-center p-1.5 shadow-[2px_2px_0px_0px_var(--color-border-solid)] flex-shrink-0 mt-0.5">
                <img src="{{ $mascotAsset }}" alt="Maskot Sleman Akses" class="w-10 h-10 object-contain drop-shadow-sm">
            </div>

            <div class="flex flex-col flex-1">
                <div class="flex items-center gap-2">
                    <span class="font-black text-sm leading-tight text-text-primary">
                        {{ $flashTitle }}
                    </span>
                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full border border-border-solid shadow-[1px_1px_0px_0px_var(--color-border-solid)]
                        @if($flashType === 'success') bg-emerald-200 text-emerald-900
                        @elseif($flashType === 'error') bg-rose-200 text-rose-900
                        @elseif($flashType === 'warning') bg-amber-200 text-amber-900
                        @else bg-sky-200 text-sky-900 @endif">
                        {{ $flashType }}
                    </span>
                </div>

                <span class="text-xs font-bold mt-1.5 leading-relaxed opacity-95">
                    {{ $flashMessage }}
                </span>

                @if($errors->count() > 1)
                    <ul class="mt-2 list-disc list-inside text-xs font-bold space-y-0.5 text-rose-900">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <button type="button" 
                onclick="document.getElementById('system-response-alert').remove()" 
                class="w-7 h-7 rounded-full border-2 border-border-solid bg-white hover:bg-slate-100 flex items-center justify-center text-text-muted hover:text-text-primary transition-transform active:translate-x-0.5 active:translate-y-0.5 flex-shrink-0"
                aria-label="Tutup Alert">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.showToast === 'function') {
                window.showToast({
                    type: @json($flashType),
                    title: @json($flashTitle),
                    message: @json($flashMessage),
                    mascot: @json($mascotAsset)
                });
            }
        });
    </script>
    @endpush
@endif
