@props([
    'name',
    'label' => null,
    'value' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'placeholder' => 'JJ/MM/YYYY',
    'help' => null,
    'min' => null,
    'max' => null,
    'class' => '',
    'inputClass' => '',
    'labelClass' => '',
    'errorKey' => null,
    'format' => 'Y-m-d',
])

@php
    $errorKey = $errorKey ?? $name;
    $hasError = $errors->has($errorKey);
    $inputId = $attributes->get('id', $name);
    
    // Format the date value for display
    $formattedValue = '';
    if (!empty($value)) {
        try {
            if ($value instanceof \Illuminate\Support\Carbon) {
                $formattedValue = $value->format('Y-m-d');
            } elseif (is_string($value)) {
                // Try to parse the date string
                $date = \Carbon\Carbon::createFromFormat('Y-m-d', $value);
                $formattedValue = $date->format('Y-m-d');
            }
        } catch (Exception $e) {
            // If parsing fails, use the original value
            $formattedValue = $value;
        }
    }
    
    // Handle old input
    $oldValue = old($name);
    if ($oldValue) {
        $formattedValue = $oldValue;
    }
    
    // Format min and max dates
    $minDate = '';
    $maxDate = '';
    
    if ($min) {
        try {
            if ($min === 'today') {
                $minDate = now()->format('Y-m-d');
            } elseif ($min === 'tomorrow') {
                $minDate = now()->addDay()->format('Y-m-d');
            } elseif ($min === 'yesterday') {
                $minDate = now()->subDay()->format('Y-m-d');
            } elseif ($min instanceof \Illuminate\Support\Carbon) {
                $minDate = $min->format('Y-m-d');
            } else {
                $minDate = $min;
            }
        } catch (Exception $e) {
            $minDate = '';
        }
    }
    
    if ($max) {
        try {
            if ($max === 'today') {
                $maxDate = now()->format('Y-m-d');
            } elseif ($max === 'tomorrow') {
                $maxDate = now()->addDay()->format('Y-m-d');
            } elseif ($max === 'yesterday') {
                $maxDate = now()->subDay()->format('Y-m-d');
            } elseif ($max instanceof \Illuminate\Support\Carbon) {
                $maxDate = $max->format('Y-m-d');
            } else {
                $maxDate = $max;
            }
        } catch (Exception $e) {
            $maxDate = '';
        }
    }
    
    // Base classes
    $inputClasses = [
        'block w-full rounded-md border bg-dark-800 text-white focus:ring-2 focus:ring-offset-2 focus:ring-offset-dark-800',
        'border-gray-600 focus:border-primary-500 focus:ring-primary-500',
        'disabled:opacity-50 disabled:cursor-not-allowed',
        'px-4 py-2',
        $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-600',
        $inputClass
    ];
    
    // Required attribute
    $requiredAttr = $required ? 'required' : '';
    
    // Disabled/Readonly attributes
    $disabledAttr = $disabled ? 'disabled' : '';
    $readonlyAttr = $readonly ? 'readonly' : '';
    
    // Min/Max attributes
    $minAttr = $minDate ? "min=\"{{ $minDate }}\"" : '';
    $maxAttr = $maxDate ? "max=\"{{ $maxDate }}\"" : '';
    
    // Add data attributes
    $dataAttributes = '';
    foreach ($attributes->getAttributes() as $key => $value) {
        if (str_starts_with($key, 'data-')) {
            $dataAttributes .= " {$key}=\"{{ $value }}\"";
        }
    }
    
    // Generate a unique ID for the date picker
    $datePickerId = 'datepicker_' . uniqid();
@endphp

<div class="space-y-1 {{ $class }}" x-data="{ showPicker: false }" x-on:click.away="showPicker = false">
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-300 {{ $labelClass }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="mt-1 relative rounded-md shadow-sm">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="far fa-calendar text-gray-400"></i>
        </div>
        
        <input 
            type="date"
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ $formattedValue }}"
            {{ $requiredAttr }}
            {{ $disabledAttr }}
            {{ $readonlyAttr }}
            {{ $minAttr }}
            {{ $maxAttr }}
            placeholder="{{ $placeholder }}"
            class="{{ implode(' ', $inputClasses) }} pl-10"
            {!! $dataAttributes !!}
            {{ $attributes->except(['id', 'class', 'value', 'required', 'disabled', 'readonly', 'placeholder', 'min', 'max']) }}
        >
        
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
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize date picker with flatpickr if available
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#{{ $inputId }}", {
                dateFormat: "Y-m-d",
                allowInput: true,
                locale: "fr",
                @if($minDate)
                    minDate: "{{ $minDate }}",
                @endif
                @if($maxDate)
                    maxDate: "{{ $maxDate }}",
                @endif
                disableMobile: true, // Better UX on mobile
            });
        }
        
        // Format date on blur (for better UX)
        const dateInput = document.getElementById('{{ $inputId }}');
        if (dateInput) {
            dateInput.addEventListener('blur', function(e) {
                try {
                    const date = new Date(e.target.value);
                    if (!isNaN(date.getTime())) {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        e.target.value = `${year}-${month}-${day}`;
                    }
                } catch (error) {
                    console.error('Error formatting date:', error);
                }
            });
        }
    });
</script>
@endpush
