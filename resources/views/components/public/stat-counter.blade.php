@props(['label', 'value', 'icon', 'color' => 'primary'])

@php
    $bgClass = $color === 'primary' ? 'bg-sa-primary' : 'bg-sa-secondary';
    $textColor = $color === 'primary' ? 'text-white' : 'text-sa-text';
    $shadowClass = $color === 'primary' ? 'shadow-solid-secondary' : 'shadow-solid';
@endphp

<div class="card-solid p-6 flex items-center gap-6 group hover:-translate-y-1 hover:shadow-solid-secondary transition-all duration-300">
    <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl {{ $bgClass }} {{ $textColor }} {{ $shadowClass }} group-hover:scale-110 transition-transform">
        <i class="{{ $icon }}"></i>
    </div>
    <div>
        <h3 class="text-4xl font-black text-sa-text mb-1"><span class="stat-counter-value" data-target="{{ $value }}">0</span>+</h3>
        <p class="font-bold text-sa-muted">{{ $label }}</p>
    </div>
</div>
