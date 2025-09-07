@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'placeholder' => '',
    'help' => null,
    'addon' => null,
    'icon' => null,
    'size' => 'md',
    'class' => '',
    'inputClass' => '',
    'labelClass' => '',
    'errorKey' => null,
    'autocomplete' => null,
    'pattern' => null,
    'min' => null,
    'max' => null,
    'step' => null,
    'maxlength' => null,
    'data' => [],
])

@php
    $errorKey = $errorKey ?? $name;
    $hasError = $errors->has($errorKey);
    $inputId = $attributes->get('id', $name);
    
    // Base classes
    $inputClasses = [
        'block w-full rounded-md border bg-dark-800 text-white focus:ring-2 focus:ring-offset-2 focus:ring-offset-dark-800',
        'border-gray-600 focus:border-primary-500 focus:ring-primary-500',
        'disabled:opacity-50 disabled:cursor-not-allowed',
        $size === 'sm' ? 'px-3 py-2 text-sm' : 'px-4 py-2',
        $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-600',
        $inputClass
    ];
    
    if ($icon) {
        $inputClasses[] = 'pl-10';
    }
    
    // Add data attributes
    $dataAttributes = '';
    foreach ($data as $key => $value) {
        $dataAttributes .= ' data-' . $key . '="' . e($value) . '"';
    }
    
    // Input group classes
    $inputGroupClass = $addon ? 'flex rounded-md shadow-sm' : '';
    $addonClass = $addon ? 'inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-600 bg-dark-700 text-gray-300 sm:text-sm' : '';
    
    // Get title attribute for pattern validation
    $titleAttr = ($pattern && isset($data['pattern-message'])) ? e($data['pattern-message']) : '';
@endphp

<div class="space-y-1 {{ $class }}">
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-300 {{ $labelClass }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="mt-1 relative rounded-md shadow-sm">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-{{ $icon }} text-gray-400"></i>
            </div>
        @endif
        
        <div class="{{ $inputGroupClass }}">
            <input 
                type="{{ $type }}"
                name="{{ $name }}"
                id="{{ $inputId }}"
                value="{{ old($name, $value) }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
                @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
                @if($pattern) pattern="{{ $pattern }}" @endif
                @if($titleAttr) {!! $titleAttr !!} @endif
                @if($min) min="{{ $min }}" @endif
                @if($max) max="{{ $max }}" @endif
                @if($step) step="{{ $step }}" @endif
                @if($maxlength) maxlength="{{ $maxlength }}" @endif
                {!! $dataAttributes !!}
                class="{{ implode(' ', $inputClasses) }}"
                placeholder="{{ $placeholder }}"
                {{ $attributes->except(['id', 'class', 'type', 'value', 'required', 'disabled', 'readonly', 'placeholder', 'autocomplete', 'pattern', 'min', 'max', 'step', 'maxlength']) }}
            >
            
            @if($addon)
                <span class="{{ $addonClass }}">
                    {{ $addon }}
                </span>
            @endif
        </div>
        
        @error($errorKey)
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
        
        @if($help && !$hasError)
            <p class="mt-1 text-xs text-gray-400">{{ $help }}</p>
        @endif
    </div>
</div>
