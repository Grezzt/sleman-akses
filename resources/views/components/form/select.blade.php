@props(['name', 'label', 'error' => null])

<div class="mb-4">
    @if ($label)
        <label for="{{ $name }}" class="block mb-2 font-bold text-sa-text">{{ $label }}</label>
    @endif
    <select 
        name="{{ $name }}" 
        id="{{ $name }}" 
        {{ $attributes->merge(['class' => 'input-solid ' . ($error ? 'error' : '')]) }}
    >
        {{ $slot }}
    </select>
    @if ($error)
        <span class="block mt-1 text-sm text-sa-error">{{ $error }}</span>
    @endif
</div>
