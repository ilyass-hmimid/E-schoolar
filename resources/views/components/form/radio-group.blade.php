@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'required' => false,
    'disabled' => false,
    'help' => null,
    'class' => '',
    'labelClass' => '',
    'optionClass' => '',
    'inline' => true,
    'errorKey' => null,
])

@php
    $errorKey = $errorKey ?? $name;
    $hasError = $errors->has($errorKey);
    $groupClass = $inline ? 'flex flex-wrap gap-4' : 'space-y-2';
    
    // Handle old input
    $oldValue = old($name);
    $selectedValue = $oldValue ?? $selected;
    
    // Required attribute
    $requiredAttr = $required ? 'required' : '';
    
    // Disabled attribute
    $disabledAttr = $disabled ? 'disabled' : '';
    
    // Base classes for radio inputs
    $radioClasses = [
        'h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-600',
        $hasError ? 'border-red-500 focus:ring-red-500' : 'border-gray-600',
        $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
    ];
    
    // Label classes
    $labelClasses = [
        'ml-2 block text-sm text-gray-300',
        $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
    ];
@endphp

<div class="space-y-1 {{ $class }}">
    @if($label)
        <div class="block text-sm font-medium text-gray-300 {{ $labelClass }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </div>
    @endif
    
    <div class="mt-1">
        <div class="{{ $groupClass }} {{ $optionClass }}">
            @foreach($options as $value => $option)
                @php
                    $label = is_array($option) ? ($option['label'] ?? $value) : $option;
                    $description = is_array($option) ? ($option['description'] ?? null) : null;
                    $optionDisabled = is_array($option) ? ($option['disabled'] ?? false) : false;
                    $isChecked = (string)$selectedValue === (string)$value;
                    $optionId = "{$name}_{$value}";
                @endphp
                
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input 
                            type="radio"
                            id="{{ $optionId }}"
                            name="{{ $name }}"
                            value="{{ $value }}"
                            {{ $isChecked ? 'checked' : '' }}
                            {{ $requiredAttr }}
                            {{ $disabled || $optionDisabled ? 'disabled' : '' }}
                            class="{{ implode(' ', $radioClasses) }}"
                            {{ $attributes->except(['class', 'id', 'disabled']) }}
                        >
                    </div>
                    <div class="ml-2">
                        <label for="{{ $optionId }}" class="{{ implode(' ', $labelClasses) }}">
                            {{ $label }}
                        </label>
                        @if($description)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $description }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        @error($errorKey)
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
        
        @if($help && !$hasError)
            <p class="mt-1 text-xs text-gray-400">{{ $help }}</p>
        @endif
    </div>
</div>
