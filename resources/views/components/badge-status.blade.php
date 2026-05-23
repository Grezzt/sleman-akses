@props(['status'])

@php
    $map = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
@endphp

<span class="badge bg-{{ $map[$status] ?? 'secondary' }}">{{ ucfirst($status) }}</span>
