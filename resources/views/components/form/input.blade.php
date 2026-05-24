@props(['name', 'label', 'type' => 'text', 'error' => null])

<div class="mb-4">
    @if ($label)
        <label for="{{ $name }}" class="block mb-2 font-bold text-sa-text">{{ $label }}</label>
    @endif
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $name }}" 
        {{ $attributes->merge(['class' => 'input-solid ' . ($error ? 'error' : '')]) }}
    >
    @if ($error)
        <span class="block mt-1 text-sm text-sa-error">{{ $error }}</span>
    @endif
</div>
