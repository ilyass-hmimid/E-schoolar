@props([
    'name',
    'label' => null,
    'value' => null,
    'required' => false,
    'disabled' => false,
    'accept' => 'image/*',
    'multiple' => false,
    'help' => 'Formats acceptés: JPG, PNG, GIF (max 2MB)',
    'class' => '',
    'previewClass' => '',
    'labelClass' => '',
    'errorKey' => null,
    'preview' => true,
    'previewSize' => 'h-16 w-16',
    'showFileName' => true,
])

@php
    $errorKey = $errorKey ?? $name;
    $hasError = $errors->has($errorKey);
    $inputId = $attributes->get('id', $name);
    
    // Handle old input for file fields (not applicable, but we can show the existing file)
    $oldValue = old($name);
    $fileValue = $value ?? $oldValue;
    
    // Required attribute
    $requiredAttr = $required ? 'required' : '';
    
    // Disabled attribute
    $disabledAttr = $disabled ? 'disabled' : '';
    
    // Multiple attribute
    $multipleAttr = $multiple ? 'multiple' : '';
    
    // Preview classes
    $previewContainerClasses = [
        'flex items-center',
        $previewClass,
    ];
    
    // Input classes
    $inputClasses = [
        'sr-only',
        $hasError ? 'border-red-500' : '',
    ];
    
    // Button classes
    $buttonClasses = [
        'px-3 py-1.5 border rounded-md text-sm font-medium',
        'border-gray-600 text-gray-300 bg-dark-700 hover:bg-dark-600',
        'focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500',
        $disabled ? 'opacity-50 cursor-not-allowed' : '',
    ];
    
    // File name classes
    $fileNameClasses = [
        'ml-3 text-sm text-gray-400',
        $disabled ? 'opacity-50' : '',
    ];
    
    // Generate a unique ID for the file input
    $fileInputId = 'file_' . uniqid();
@endphp

<div class="space-y-1 {{ $class }}">
    @if($label)
        <label class="block text-sm font-medium text-gray-300 {{ $labelClass }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="mt-1">
        <div class="{{ implode(' ', $previewContainerClasses) }}">
            @if($preview)
                @if($fileValue && !$oldValue) {{-- Existing file --}}
                    @if(str_starts_with($fileValue, 'http') || str_starts_with($fileValue, '/'))
                        <img id="{{ $fileInputId }}_preview" src="{{ $fileValue }}" alt="Preview" class="{{ $previewSize }} rounded-full object-cover mr-4">
                    @else
                        <img id="{{ $fileInputId }}_preview" src="{{ Storage::url($fileValue) }}" alt="Preview" class="{{ $previewSize }} rounded-full object-cover mr-4">
                    @endif
                @elseif($oldValue) {{-- Newly uploaded file --}}
                    <div id="{{ $fileInputId }}_preview" class="{{ $previewSize }} rounded-full bg-cover bg-center mr-4" style="background-image: url('{{ $oldValue->temporaryUrl() }}')">
                    </div>
                @else {{-- No file --}}
                    <div id="{{ $fileInputId }}_preview" class="{{ $previewSize }} rounded-full bg-dark-600 flex items-center justify-center text-2xl text-gray-400 mr-4">
                        <i class="fas fa-user"></i>
                    </div>
                @endif
            @endif
            
            <div>
                <input 
                    type="file"
                    id="{{ $fileInputId }}"
                    name="{{ $name }}"
                    {{ $multipleAttr }}
                    {{ $requiredAttr }}
                    {{ $disabledAttr }}
                    accept="{{ $accept }}"
                    class="{{ implode(' ', $inputClasses) }}"
                    onchange="previewFile(this, '{{ $fileInputId }}')"
                    {{ $attributes->except(['id', 'class', 'accept', 'multiple', 'required', 'disabled']) }}
                >
                
                <label for="{{ $fileInputId }}" class="{{ implode(' ', $buttonClasses) }} {{ $disabled ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                    <i class="fas fa-upload mr-1"></i> Choisir un fichier
                </label>
                
                @if($showFileName)
                    <span id="{{ $fileInputId }}_name" class="{{ implode(' ', $fileNameClasses) }}">
                        {{ $fileValue ? basename($fileValue) : 'Aucun fichier sélectionné' }}
                    </span>
                @endif
            </div>
        </div>
        
        @error($errorKey)
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
        
        @if($help && !$hasError)
            <p class="mt-1 text-xs text-gray-400">{{ $help }}</p>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function previewFile(input, fileInputId) {
        const file = input.files[0];
        const preview = document.getElementById(`${fileInputId}_preview`);
        const fileName = document.getElementById(`${fileInputId}_name`);
        
        if (file) {
            // Update file name
            if (fileName) {
                fileName.textContent = file.name;
            }
            
            // Show preview if it's an image
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (preview.tagName === 'IMG') {
                        preview.src = e.target.result;
                    } else {
                        preview.style.backgroundImage = `url(${e.target.result})`;
                        preview.classList.remove('bg-dark-600');
                        preview.classList.add('bg-cover', 'bg-center');
                        
                        // Hide the icon if it exists
                        const icon = preview.querySelector('i');
                        if (icon) {
                            icon.style.display = 'none';
                        }
                    }
                };
                reader.readAsDataURL(file);
            } else {
                // For non-image files, show a file icon
                if (preview.tagName === 'IMG') {
                    preview.src = '#'; // Or a file icon
                } else {
                    preview.style.backgroundImage = 'none';
                    preview.classList.add('bg-dark-600');
                    preview.classList.remove('bg-cover', 'bg-center');
                    
                    // Show a file icon
                    const icon = preview.querySelector('i');
                    if (icon) {
                        icon.className = 'fas fa-file';
                        icon.style.display = 'block';
                    }
                }
            }
        }
    }
</script>
@endpush
