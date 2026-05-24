@props(['name', 'label' => 'Unggah Foto', 'accept' => 'image/*', 'error' => null])

<div class="mb-4">
    @if ($label)
        <label class="block mb-2 font-bold text-sa-text">{{ $label }}</label>
    @endif
    
    <div class="relative flex items-center justify-center w-full">
        <label for="{{ $name }}" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-xl cursor-pointer bg-gray-50 border-gray-300 hover:bg-gray-100 transition {{ $error ? 'border-sa-error bg-red-50' : '' }}">
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <i class="fas fa-cloud-upload-alt text-2xl text-sa-muted mb-2"></i>
                <p class="mb-1 text-sm text-sa-muted"><span class="font-bold">Klik untuk mengunggah</span> atau seret file ke sini</p>
                <p class="text-xs text-gray-500">SVG, PNG, JPG atau GIF (MAX. 5MB)</p>
            </div>
            <input id="{{ $name }}" name="{{ $name }}" type="file" accept="{{ $accept }}" class="hidden" onchange="previewFile(this, '{{ $name }}-preview')" />
        </label>
    </div>
    
    <div id="{{ $name }}-preview-container" class="hidden mt-4">
        <p class="mb-2 text-sm font-bold">Preview:</p>
        <img id="{{ $name }}-preview" src="#" alt="Preview" class="h-32 rounded-lg border-2 border-sa-border object-cover" />
    </div>

    @if ($error)
        <span class="block mt-1 text-sm text-sa-error">{{ $error }}</span>
    @endif
</div>

<script>
    function previewFile(input, previewId) {
        const file = input.files[0];
        const preview = document.getElementById(previewId);
        const container = document.getElementById(previewId + '-container');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '#';
            container.classList.add('hidden');
        }
    }
</script>
