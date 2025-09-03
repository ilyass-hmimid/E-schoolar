@props([
    'name',
    'label' => null,
    'value',
    'checked' => false,
    'required' => false,
    'disabled' => false,
    'help' => null,
    'class' => '',
    'labelClass' => '',
    'errorKey' => null,
    'id' => null,
])

@php
    $errorKey = $errorKey ?? $name;
    $hasError = $errors->has($errorKey);
    $inputId = $id ?? $name . '_' . uniqid();
    
    // Handle old input
    $oldValue = old($name);
    $isChecked = $checked;
    
    if ($oldValue !== null) {
        $isChecked = (string)$oldValue === (string)$value;
    }
    
    // Base classes
    $radioClasses = [
        'h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-600',
        $hasError ? 'border-red-500' : 'border-gray-600',
        $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
    ];
    
    // Label classes
    $labelClasses = [
        'ml-2 text-sm text-gray-300',
        $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
        $labelClass,
    ];
    
    // Required attribute
    $requiredAttr = $required ? 'required' : '';
    
    // Disabled attribute
    $disabledAttr = $disabled ? 'disabled' : '';
    
    // Checked attribute
    $checkedAttr = $isChecked ? 'checked' : '';
@endphp

<div class="flex items-start {{ $class }}">
    <div class="flex items-center h-5">
        <input 
            type="radio"
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="{{ $value }}"
            {{ $checkedAttr }}
            {{ $requiredAttr }}
            {{ $disabledAttr }}
            class="{{ implode(' ', $radioClasses) }}"
            {{ $attributes->except(['id', 'class', 'checked', 'required', 'disabled', 'value']) }}
        >
    </div>
    
    @if($label || $help)
        <div class="ml-3">
            @if($label)
                <label for="{{ $inputId }}" class="{{ implode(' ', $labelClasses) }}">
                    {{ $label }}
                    @if($required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
            @endif
            
            @if($help && !$hasError)
                <p class="text-xs text-gray-400 mt-0.5">{{ $help }}</p>
            @endif
        </div>
    @endif
    
    @if($loop->last && $hasError)
        <p class="mt-1 text-sm text-red-500">{{ $errors->first($errorKey) }}</p>
    @endif
</div>
