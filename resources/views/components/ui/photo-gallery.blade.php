@props(['photos' => []])

@if(count($photos) > 0)
<div class="grid grid-cols-2 gap-4 md:grid-cols-3">
    @foreach($photos as $photo)
    <div class="relative overflow-hidden rounded-xl aspect-square card-solid cursor-pointer hover:opacity-90 transition" onclick="openLightbox('{{ $photo }}')">
        <img src="{{ $photo }}" alt="Foto Laporan" class="object-cover w-full h-full">
    </div>
    @endforeach
</div>

{{-- Lightbox Modal --}}
<div id="gallery-lightbox" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-80 backdrop-blur-sm" onclick="closeLightbox()">
    <div class="relative max-w-4xl max-h-[90vh] p-4 mx-auto">
        <button class="absolute top-0 right-0 p-2 text-white bg-sa-error rounded-full -mt-4 -mr-4 border-2 border-white" onclick="closeLightbox()">
            <i class="fas fa-times"></i>
        </button>
        <img id="lightbox-img" src="" class="max-w-full max-h-[85vh] rounded-lg border-4 border-white shadow-2xl">
    </div>
</div>

<script>
    function openLightbox(src) {
        document.getElementById('lightbox-img').src = src;
        document.getElementById('gallery-lightbox').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeLightbox() {
        document.getElementById('gallery-lightbox').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLightbox();
    });
</script>
@else
    <x-ui.empty-state icon="image" title="Tidak ada foto" message="Pelapor tidak menyertakan foto tambahan." />
@endif
