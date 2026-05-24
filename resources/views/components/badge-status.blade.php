@props(['status'])

@php
    $bg = 'bg-slate-100 text-slate-700 border-slate-300';
    if ($status === 'pending') $bg = 'bg-warning text-white border-yellow-600';
    if ($status === 'approved') $bg = 'bg-success text-white border-green-700';
    if ($status === 'rejected') $bg = 'bg-error text-white border-red-700';
@endphp

<span class="inline-block px-3 py-1 rounded-full text-xs font-bold border-2 {{ $bg }} shadow-sm">
    {{ ucfirst($status) }}
</span>
