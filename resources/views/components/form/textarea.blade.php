@props([
    'name',
    'label' => null,
    'value' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'placeholder' => '',
    'help' => null,
    'rows' => 3,
    'class' => '',
    'inputClass' => '',
    'labelClass' => '',
    'errorKey' => null,
    'maxlength' => null,
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
        'px-4 py-2',
        $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-600',
        $inputClass
    ];
    
    // Handle old input
    $oldValue = old($name);
    $value = $oldValue ?? $value;
    
    // Required attribute
    $requiredAttr = $required ? 'required' : '';
    
    // Disabled/Readonly attributes
    $disabledAttr = $disabled ? 'disabled' : '';
    $readonlyAttr = $readonly ? 'readonly' : '';
    
    // Maxlength attribute
    $maxlengthAttr = $maxlength ? "maxlength=\"{{ $maxlength }}\"" : '';
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
    
    <div class="mt-1">
        <textarea 
            name="{{ $name }}"
            id="{{ $inputId }}"
            rows="{{ $rows }}"
            {{ $requiredAttr }}
            {{ $disabledAttr }}
            {{ $readonlyAttr }}
            {{ $maxlengthAttr }}
            class="{{ implode(' ', $inputClasses) }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->except(['id', 'class', 'rows', 'required', 'disabled', 'readonly', 'placeholder', 'maxlength']) }}
        >{{ $value }}</textarea>
        
        @error($errorKey)
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
        @include('partials.errors')
        
        @if($help && !$hasError)
            <p class="mt-1 text-xs text-gray-400">{{ $help }}</p>
        @endif
    </div>
</div>
