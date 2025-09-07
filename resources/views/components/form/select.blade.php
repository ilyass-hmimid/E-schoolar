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
    'multiple' => false,
    'searchable' => false,
])

@php
    // Initialisation des variables
    $errorKey = $errorKey ?? $name;
    $hasError = $errors->has($errorKey);
    $selectId = $attributes->get('id', $name);
    
    // Classes CSS de base
    $selectClasses = [
        'block w-full rounded-md border bg-dark-800 text-white focus:ring-2 focus:ring-offset-2 focus:ring-offset-dark-800',
        'border-gray-600 focus:border-primary-500 focus:ring-primary-500',
        'disabled:opacity-50 disabled:cursor-not-allowed',
        'py-2 pl-3 pr-10',
        $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-600',
        $selectClass,
        $multiple ? 'h-auto' : '',
        $searchable ? 'pr-10' : ''
    ];
    
    // Gestion des valeurs sélectionnées
    $selectedValue = old($name, $selected);
    if ($multiple) {
        $selectedValue = is_array($selectedValue) ? $selectedValue : (is_null($selectedValue) ? [] : [$selectedValue]);
    }
    
    // Attributs HTML
    $requiredAttr = $required ? 'required' : '';
    $disabledAttr = $disabled ? 'disabled' : '';
    $multipleAttr = $multiple ? 'multiple' : '';
    
    // Préparation des options
    $options = $options instanceof \Illuminate\Support\Collection ? $options->toArray() : (array)$options;
    $isAssoc = count(array_filter(array_keys($options), 'is_string')) > 0;
    $isGrouped = $optgroup && !empty($options) && is_array(reset($options));
    
    // Filtrage des attributs
    $htmlAttributes = [];
    $dataAttributes = [];
    
    foreach ($attributes->getIterator() as $key => $value) {
        if (str_starts_with($key, 'data-')) {
            $dataAttributes[] = $key . '="' . e($value) . '"';
        } elseif (!in_array($key, ['id', 'class', 'required', 'disabled', 'placeholder', 'multiple'])) {
            $htmlAttributes[] = $key . '="' . e($value) . '"';
        }
    }
    
    $htmlAttributes = implode(' ', $htmlAttributes);
    $dataAttributes = implode(' ', $dataAttributes);
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
        <div class="relative">
            @if($searchable)
                <input 
                    type="text" 
                    class="w-full px-3 py-2 bg-dark-800 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="Rechercher..."
                    x-data="{ search: '' }"
                    x-on:input="search = $event.target.value"
                >
            @endif
            
            <select 
                name="{{ $multiple ? $name.'[]' : $name }}"
                id="{{ $selectId }}"
                {{ $requiredAttr }}
                {{ $disabledAttr }}
                {{ $multipleAttr }}
                class="{{ implode(' ', $selectClasses) }}"
                {!! $htmlAttributes !!}
                {!! $dataAttributes ? ' ' . $dataAttributes : '' !!}
                @if($searchable) 
                    x-data="{ search: '' }"
                    x-on:change="$dispatch('input', $event.target.value)"
                    x-bind:class="{ 'opacity-0 absolute': search }"
                @endif
            >
                @if($placeholder && !$multiple)
                    <option value="" {{ empty($selectedValue) ? 'selected' : '' }} disabled>
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
                                    $isSelected = $multiple 
                                        ? in_array((string)$value, array_map('strval', (array)$selectedValue)) 
                                        : (string)$selectedValue === (string)$value;
                                    
                                    $dataAttributes = '';
                                    if (is_object($option) && method_exists($option, 'getAttributes')) {
                                        $dataAttributes = collect($option->getAttributes())
                                            ->except([$optionValue, $optionLabel, 'created_at', 'updated_at', 'deleted_at'])
                                            ->map(function($val, $key) {
                                                return 'data-' . $key . '="' . e($val) . '"';
                                            })->implode(' ');
                                    }
                                @endphp
                                <option value="{{ $value }}" {{ $isSelected ? 'selected' : '' }} {!! $dataAttributes !!}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                @else
                    @foreach($options as $key => $option)
                        @php
                            $value = is_object($option) ? $option->{$optionValue} : ($isAssoc ? $key : $option);
                            $label = is_object($option) ? $option->{$optionLabel} : $option;
                            $isSelected = $multiple 
                                ? in_array((string)$value, array_map('strval', (array)$selectedValue)) 
                                : (string)$selectedValue === (string)$value;
                            
                            $dataAttributes = '';
                            if (is_object($option) && method_exists($option, 'getAttributes')) {
                                $dataAttributes = collect($option->getAttributes())
                                    ->except([$optionValue, $optionLabel, 'created_at', 'updated_at', 'deleted_at'])
                                    ->map(function($val, $key) {
                                        return 'data-' . $key . '="' . e($val) . '"';
                                    })->implode(' ');
                            }
                        @endphp
                        <option value="{{ $value }}" {{ $isSelected ? 'selected' : '' }} {!! $dataAttributes !!}>
                            {{ $label }}
                        </option>
                    @endforeach
                @endif
            </select>
            
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                <i class="fas fa-chevron-down"></i>
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
