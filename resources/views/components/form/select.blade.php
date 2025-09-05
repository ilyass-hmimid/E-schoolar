@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'required' => false,
    'disabled' => false,
    'placeholder' => 'Sélectionner une option',
    'help' => null,
    'class' => '',
    'selectClass' => '',
    'labelClass' => '',
    'errorKey' => null,
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'optgroup' => null,
])

@php
    $errorKey = $errorKey ?? $name;
    $hasError = $errors->has($errorKey);
    $selectId = $attributes->get('id', $name);
    
    // Base classes
    $selectClasses = [
        'block w-full rounded-md border bg-dark-800 text-white focus:ring-2 focus:ring-offset-2 focus:ring-offset-dark-800',
        'border-gray-600 focus:border-primary-500 focus:ring-primary-500',
        'disabled:opacity-50 disabled:cursor-not-allowed',
        'py-2 pl-3 pr-10',
        $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-600',
        $selectClass
    ];
    
    // Handle old input
    $oldValue = old($name);
    $selectedValue = $oldValue ?? $selected;
    
    // Required attribute
    $requiredAttr = $required ? 'required' : '';
    
    // Disabled attribute
    $disabledAttr = $disabled ? 'disabled' : '';
    
    // Check if options are associative or sequential
    $isAssoc = count(array_filter(array_keys($options), 'is_string')) > 0;
    
    // Check if options are grouped
    $isGrouped = $optgroup && $options && is_array($options) && is_array(reset($options));
    
    // Add data attributes
    $dataAttributes = '';
    foreach ($attributes->getAttributes() as $key => $value) {
        if (str_starts_with($key, 'data-')) {
            $dataAttributes .= " {$key}=\"{{ $value }}\"";
        }
    }
@endphp

<div class="space-y-1 {{ $class }}">
    @if($label)
        <label for="{{ $selectId }}" class="block text-sm font-medium text-gray-300 {{ $labelClass }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="mt-1 relative">
        <select 
            name="{{ $name }}"
            id="{{ $selectId }}"
            {{ $requiredAttr }}
            {{ $disabledAttr }}
            class="{{ implode(' ', $selectClasses) }}"
            {{ $attributes->except(['id', 'class', 'required', 'disabled', 'placeholder']) }}
            {!! $dataAttributes !!}
        >
            @if($placeholder)
                <option value="" {{ $selectedValue === null || $selectedValue === '' ? 'selected' : '' }} disabled>
                    {{ $placeholder }}
                </option>
            @endif
            
            @if($isGrouped)
                @foreach($options as $groupLabel => $groupOptions)
                    <optgroup label="{{ $groupLabel }}">
                        @foreach($groupOptions as $key => $option)
                            @php
                                $value = is_object($option) ? $option->{$optionValue} : $key;
                                $label = is_object($option) ? $option->{$optionLabel} : $option;
                                $isSelected = (string)$selectedValue === (string)$value;
                            @endphp
                            <option value="{{ $value }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            @elseif($isAssoc)
                @foreach($options as $value => $label)
                    <option value="{{ $value }}" {{ (string)$selectedValue === (string)$value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            @else
                @foreach($options as $option)
                    @php
                        $value = is_object($option) ? $option->{$optionValue} : $option;
                        $label = is_object($option) ? $option->{$optionLabel} : $option;
                        $isSelected = (string)$selectedValue === (string)$value;
                    @endphp
                    <option value="{{ $value }}" {{ $isSelected ? 'selected' : '' }} {{ is_object($option) ? collect($option->attributes ?? [])->map(fn($val, $key) => "data-{$key}=\"{$val}\"")->implode(' ') : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            @endif
        </select>
        
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
            <i class="fas fa-chevron-down"></i>
        </div>
        
        @error($errorKey)
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
        @include('partials.errors')
        
        @if($help && !$hasError)
            <p class="mt-1 text-xs text-gray-400">{{ $help }}</p>
        @endif
    </div>
</div>
