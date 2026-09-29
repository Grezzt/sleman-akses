@props(['status'])

@php
    $bg = 'bg-slate-100 text-slate-800 border-border-solid';
    $icon = 'fa-solid fa-circle-question';
    
    if ($status === 'pending') {
        $bg = 'bg-amber-100 text-amber-900 border-amber-400';
        $icon = 'fa-solid fa-clock text-amber-600';
    } elseif ($status === 'approved' || $status === 'published') {
        $bg = 'bg-emerald-100 text-emerald-900 border-emerald-400';
        $icon = 'fa-solid fa-circle-check text-emerald-600';
    } elseif ($status === 'rejected') {
        $bg = 'bg-rose-100 text-rose-900 border-rose-400';
        $icon = 'fa-solid fa-circle-xmark text-rose-600';
    }
    
    $label = match($status) {
        'pending' => 'Pending',
        'approved', 'published' => 'Disetujui',
        'rejected' => 'Ditolak',
        default => ucfirst($status),
    };
@endphp

<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black border-2 {{ $bg }} shadow-[1px_1px_0px_0px_var(--color-border-solid)]">
    <i class="{{ $icon }} text-[11px]"></i>
    <span>{{ $label }}</span>
</span>
