@props(['title', 'value', 'icon', 'color' => 'primary'])

@php
    $bgClass = '';
    $iconColor = 'var(--color-primary)';
    
    if($color == 'warning') {
        $iconColor = 'var(--color-warning)';
    } elseif($color == 'success') {
        $iconColor = 'var(--color-success)';
    } elseif($color == 'danger') {
        $iconColor = 'var(--color-error)';
    }
@endphp

<div class="admin-card flex justify-between items-center m-0" style="border-top: 4px solid {{ $iconColor }};">
    <div>
        <p class="text-text-muted font-bold mb-1 text-sm uppercase tracking-wider">{{ $title }}</p>
        <h3 class="m-0 font-black text-3xl text-text-onsurface">{{ $value }}</h3>
    </div>
    <div style="background: rgba(0,0,0,0.03); color: {{ $iconColor }}" class="p-4 rounded-xl flex items-center justify-center">
        <i class="{{ $icon }} text-3xl"></i>
    </div>
</div>
