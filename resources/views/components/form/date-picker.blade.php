@props([
    'name',
    'label' => null,
    'value' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'placeholder' => 'Sélectionner une date',
    'help' => null,
    'class' => '',
    'inputClass' => '',
    'labelClass' => '',
    'errorKey' => null,
    'minDate' => null,
    'maxDate' => null,
    'format' => 'Y-m-d',
])

@php
    $errorKey = $errorKey ?? $name;
    $hasError = $errors->has($errorKey);
    $inputId = $attributes->get('id', $name . '_datepicker');
    
    // Handle old input
    $oldValue = old($name);
    $dateValue = $oldValue ?? $value;
    
    // Format the date value for display
    $displayValue = $dateValue ? \Carbon\Carbon::parse($dateValue)->format('Y-m-d') : '';
    
    // Base classes
    $inputClasses = [
        'block w-full rounded-md border bg-dark-800 text-white focus:ring-2 focus:ring-offset-2 focus:ring-offset-dark-800',
        'border-gray-600 focus:border-primary-500 focus:ring-primary-500',
        'disabled:opacity-50 disabled:cursor-not-allowed',
        'pl-10 py-2',
        $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-600',
        $inputClass
    ];
    
    // Required attribute
    $requiredAttr = $required ? 'required' : '';
    
    // Disabled/Readonly attributes
    $disabledAttr = $disabled ? 'disabled' : '';
    $readonlyAttr = $readonly ? 'readonly' : '';
    
    // Min/Max date attributes
    $minAttr = $minDate ? "data-min-date=\"{{ $minDate }}?format('Y-m-d'):'' }}\"" : '';
    $maxAttr = $maxDate ? "data-max-date=\"{{ $maxDate }}?format('Y-m-d'):'' }}\"" : '';
    
    // Data attributes for Flatpickr
    $dataAttributes = [];
    if ($minDate) $dataAttributes[] = 'data-min-date=' . $minDate->format('Y-m-d');
    if ($maxDate) $dataAttributes[] = 'data-max-date=' . $maxDate->format('Y-m-d');
    $dataAttributes = implode(' ', $dataAttributes);
@endphp

<div class="space-y-1 {{ $class }}" x-data="{ date: '{{ $dateValue }}' }">
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-300 {{ $labelClass }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="mt-1 relative">
        <div class="relative">
            <input 
                type="text"
                id="{{ $inputId }}"
                name="{{ $name }}"
                value="{{ $displayValue }}"
                {{ $requiredAttr }}
                {{ $disabledAttr }}
                {{ $readonlyAttr }}
                placeholder="{{ $placeholder }}"
                class="{{ implode(' ', $inputClasses) }}"
                x-ref="datepicker"
                {{ $attributes->except(['id', 'class', 'required', 'disabled', 'readonly', 'placeholder']) }}
            >
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="far fa-calendar text-gray-400"></i>
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
    document.addEventListener('DOMContentLoaded', function() {
        const datepicker = document.getElementById('{{ $inputId }}');
        if (datepicker) {
            flatpickr(datepicker, {
                dateFormat: 'Y-m-d',
                allowInput: true,
                disableMobile: true,
                minDate: '{{ $minDate ? $minDate->format('Y-m-d') : '' }}',
                maxDate: '{{ $maxDate ? $maxDate->format('Y-m-d') : '' }}',
                locale: {
                    firstDayOfWeek: 1,
                    weekdays: {
                        shorthand: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
                        longhand: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
                    },
                    months: {
                        shorthand: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                        longhand: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
                    },
                },
                onChange: function(selectedDates, dateStr, instance) {
                    // Update the hidden input with the formatted date
                    const hiddenInput = document.getElementById('{{ $name }}_hidden');
                    if (hiddenInput) {
                        hiddenInput.value = dateStr;
                    }
                },
            });
        }
    });
</script>
@endpush
