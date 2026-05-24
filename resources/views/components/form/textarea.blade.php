@props(['name', 'label', 'rows' => 4, 'error' => null])

<div class="mb-4">
    @if ($label)
        <label for="{{ $name }}" class="block mb-2 font-bold text-sa-text">{{ $label }}</label>
    @endif
    <textarea 
        name="{{ $name }}" 
        id="{{ $name }}" 
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => 'input-solid ' . ($error ? 'error' : '')]) }}
    >{{ $slot }}</textarea>
    @if ($error)
        <span class="block mt-1 text-sm text-sa-error">{{ $error }}</span>
    @endif
</div>
