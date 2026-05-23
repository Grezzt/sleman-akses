@props(['title', 'value', 'icon', 'color' => 'primary'])

<div class="card border-{{ $color }} shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <p class="text-muted mb-1">{{ $title }}</p>
                <h3 class="mb-0">{{ $value }}</h3>
            </div>
            <i class="fa {{ $icon }} fa-2x text-{{ $color }}"></i>
        </div>
    </div>
</div>
