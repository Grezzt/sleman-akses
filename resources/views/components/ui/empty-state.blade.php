@props(['icon' => 'folder-open', 'title' => 'Tidak Ada Data', 'message' => ''])

<div class="flex flex-col items-center justify-center p-8 text-center bg-gray-50 border-2 border-dashed border-gray-300 rounded-2xl">
    <div class="w-16 h-16 mb-4 text-sa-muted opacity-50 flex items-center justify-center">
        <i class="fas fa-{{ $icon }} fa-3x"></i>
    </div>
    <h3 class="text-lg font-bold text-sa-text mb-2">{{ $title }}</h3>
    @if($message)
        <p class="text-sm text-sa-muted max-w-sm">{{ $message }}</p>
    @endif
    <div class="mt-4">
        {{ $slot }}
    </div>
</div>
