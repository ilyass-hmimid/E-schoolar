@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'required' => false,
    'disabled' => false,
    'placeholder' => 'Rechercher...',
    'noResultsText' => 'Aucun résultat trouvé',
    'help' => null,
    'class' => '',
    'selectClass' => '',
    'labelClass' => '',
    'errorKey' => null,
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'searchable' => true,
    'clearable' => true,
])

@php
    $errorKey = $errorKey ?? $name;
    $hasError = $errors->has($errorKey);
    $selectId = $attributes->get('id', $name) . '_' . uniqid();
    
    // Handle old input
    $oldValue = old($name);
    $selectedValue = $oldValue ?? $selected;
    
    // Required attribute
    $requiredAttr = $required ? 'required' : '';
    
    // Disabled attribute
    $disabledAttr = $disabled ? 'disabled' : '';
    
    // Check if options are associative or sequential
    $isAssoc = count(array_filter(array_keys($options), 'is_string')) > 0;
    
    // Find the selected option
    $selectedOption = null;
    $optionsArray = [];
    
    if ($isAssoc) {
        foreach ($options as $value => $label) {
            $option = [
                'value' => $value,
                'label' => $label,
                'selected' => (string)$selectedValue === (string)$value,
            ];
            $optionsArray[] = $option;
            
            if ((string)$selectedValue === (string)$value) {
                $selectedOption = $option;
            }
        }
    } else {
        foreach ($options as $option) {
            $value = is_object($option) ? $option->{$optionValue} : $option[$optionValue] ?? $option;
            $label = is_object($option) ? $option->{$optionLabel} : $option[$optionLabel] ?? $option;
            $isSelected = (string)$selectedValue === (string)$value;
            
            $optionData = [
                'value' => $value,
                'label' => $label,
                'selected' => $isSelected,
            ];
            
            // Add additional data attributes if they exist
            if (is_object($option) && isset($option->attributes) && is_array($option->attributes)) {
                foreach ($option->attributes as $key => $val) {
                    $optionData["data-{$key}"] = $val;
                }
            } elseif (is_array($option) && isset($option['attributes']) && is_array($option['attributes'])) {
                foreach ($option['attributes'] as $key => $val) {
                    $optionData["data-{$key}"] = $val;
                }
            }
            
            $optionsArray[] = $optionData;
            
            if ($isSelected) {
                $selectedOption = $optionData;
            }
        }
    }
    
    // Convert options to JSON for JavaScript
    $optionsJson = json_encode($optionsArray);
    
    // Generate a unique ID for the select
    $selectContainerId = 'select_' . uniqid();
@endphp

<div class="space-y-1 {{ $class }}" x-data="selectSearch('{{ $selectId }}', {{ $optionsJson }}, {{ $searchable ? 'true' : 'false' }}, {{ $clearable ? 'true' : 'false' }})">
    @if($label)
        <label for="{{ $selectId }}" class="block text-sm font-medium text-gray-300 {{ $labelClass }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="mt-1 relative" id="{{ $selectContainerId }}">
        <!-- Hidden select for form submission -->
        <select 
            name="{{ $name }}"
            id="{{ $selectId }}"
            class="hidden"
            {{ $requiredAttr }}
            {{ $disabledAttr }}
            {{ $attributes->except(['id', 'class', 'required', 'disabled', 'placeholder']) }}
            x-model="selectedValue"
        >
            @if(!$required)
                <option value=""></option>
            @endif
            
            @foreach($optionsArray as $option)
                <option 
                    value="{{ $option['value'] }}" 
                    {{ $option['selected'] ? 'selected' : '' }}
                    @foreach($option as $key => $value)
                        @if(str_starts_with($key, 'data-'))
                            {{ $key }}="{{ $value }}"
                        @endif
                    @endforeach
                >
                    {{ $option['label'] }}
                </option>
            @endforeach
        </select>
        
        <!-- Custom select UI -->
        <div class="relative">
            <!-- Selected value display -->
            <button 
                type="button"
                class="relative w-full bg-dark-800 border rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm {{ $hasError ? 'border-red-500' : 'border-gray-600' }} {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}"
                @click="open = !open"
                @keydown.arrow-down.prevent="if (!open) { open = true; highlightNext() }"
                @keydown.arrow-up.prevent="if (!open) { open = true; highlightPrevious() }"
                @keydown.enter.prevent="if (highlightedIndex !== null) { selectOption(filteredOptions[highlightedIndex]) }"
                @keydown.space.prevent="if (!open) { open = true }"
                @keydown.escape="open = false"
                :aria-expanded="open"
                :aria-haspopup="listbox"
                :aria-labelledby="$id('listbox-label')"
                :aria-activedescendant="highlightedIndex !== null ? $id('listbox-option-' + highlightedIndex) : null"
                {{ $disabledAttr }}
            >
                <span class="block truncate" x-text="selectedOption ? selectedOption.label : '{{ $placeholder }}'" :class="{ 'text-gray-400': !selectedOption }"></span>
                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </span>
            </button>
            
            <!-- Dropdown panel -->
            <div 
                x-show="open" 
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute z-10 mt-1 w-full bg-dark-700 shadow-lg rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm max-h-60"
                style="display: none;"
                @click.away="open = false"
                x-ref="listbox"
            >
                <!-- Search input -->
                @if($searchable)
                    <div class="px-3 py-2 border-b border-gray-600">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                class="block w-full bg-dark-800 border-0 pl-10 pr-3 py-2 text-white placeholder-gray-400 focus:ring-0 sm:text-sm" 
                                placeholder="{{ $placeholder }}" 
                                x-model="searchQuery"
                                @input="filterOptions()"
                                @keydown.arrow-down.prevent="highlightNext()"
                                @keydown.arrow-up.prevent="highlightPrevious()"
                                @keydown.enter.prevent="if (highlightedIndex !== null) { selectOption(filteredOptions[highlightedIndex]) }"
                                @keydown.escape="open = false"
                            >
                        </div>
                    </div>
                @endif
                
                <!-- Options -->
                <div class="py-1">
                    <template x-if="filteredOptions.length === 0">
                        <div class="px-3 py-2 text-sm text-gray-400">{{ $noResultsText }}</div>
                    </template>
                    
                    <template x-for="(option, index) in filteredOptions" :key="index">
                        <div 
                            :id="$id('listbox-option-' + index)"
                            class="cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-dark-600"
                            :class="{ 'bg-primary-600 text-white': highlightedIndex === index, 'text-gray-300': highlightedIndex !== index }"
                            @click="selectOption(option)"
                            @mouseenter="highlightedIndex = index"
                            @mouseleave="highlightedIndex = null"
                            role="option"
                        >
                            <span class="block truncate" :class="{ 'font-semibold': isSelected(option), 'font-normal': !isSelected(option) }" x-text="option.label"></span>
                            
                            <span 
                                x-show="isSelected(option)" 
                                class="absolute inset-y-0 right-0 flex items-center pr-4"
                                :class="{ 'text-white': highlightedIndex === index, 'text-primary-500': highlightedIndex !== index }"
                            >
                                <i class="fas fa-check"></i>
                            </span>
                        </div>
                    </template>
                </div>
                
                <!-- Clear selection button -->
                @if($clearable)
                    <div class="border-t border-gray-600 px-3 py-2">
                        <button 
                            type="button"
                            class="w-full text-left text-sm text-gray-300 hover:text-white"
                            @click="clearSelection()"
                            x-show="selectedOption"
                        >
                            <i class="fas fa-times mr-1"></i> Effacer la sélection
                        </button>
                    </div>
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
    function selectSearch(selectId, options, searchable = true, clearable = true) {
        return {
            open: false,
            searchQuery: '',
            selectedValue: null,
            selectedOption: null,
            options: options,
            filteredOptions: [],
            highlightedIndex: null,
            
            init() {
                // Set initial selected value
                const select = document.getElementById(selectId);
                if (select) {
                    this.selectedValue = select.value;
                    this.selectedOption = this.options.find(opt => String(opt.value) === String(this.selectedValue)) || null;
                }
                
                // Initialize filtered options
                this.filterOptions();
                
                // Close dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    if (!this.$el.contains(e.target)) {
                        this.open = false;
                    }
                });
            },
            
            isSelected(option) {
                return option && this.selectedValue !== null && String(option.value) === String(this.selectedValue);
            },
            
            selectOption(option) {
                if (!option) return;
                
                this.selectedValue = option.value;
                this.selectedOption = option;
                this.open = false;
                this.searchQuery = '';
                this.filterOptions();
                
                // Update the hidden select
                const select = document.getElementById(selectId);
                if (select) {
                    select.value = option.value;
                    // Trigger change event
                    const event = new Event('change');
                    select.dispatchEvent(event);
                }
            },
            
            clearSelection() {
                this.selectedValue = null;
                this.selectedOption = null;
                this.open = false;
                this.searchQuery = '';
                this.filterOptions();
                
                // Update the hidden select
                const select = document.getElementById(selectId);
                if (select) {
                    select.value = '';
                    // Trigger change event
                    const event = new Event('change');
                    select.dispatchEvent(event);
                }
            },
            
            filterOptions() {
                if (!this.searchQuery) {
                    this.filteredOptions = [...this.options];
                } else {
                    const query = this.searchQuery.toLowerCase();
                    this.filteredOptions = this.options.filter(option => 
                        option.label.toLowerCase().includes(query)
                    );
                }
                
                // Reset highlighted index
                this.highlightedIndex = this.filteredOptions.length > 0 ? 0 : null;
            },
            
            highlightNext() {
                if (this.highlightedIndex === null || this.highlightedIndex >= this.filteredOptions.length - 1) {
                    this.highlightedIndex = 0;
                } else {
                    this.highlightedIndex++;
                }
                this.scrollToHighlighted();
            },
            
            highlightPrevious() {
                if (this.highlightedIndex === null || this.highlightedIndex <= 0) {
                    this.highlightedIndex = this.filteredOptions.length - 1;
                } else {
                    this.highlightedIndex--;
                }
                this.scrollToHighlighted();
            },
            
            scrollToHighlighted() {
                if (this.highlightedIndex !== null) {
                    const listbox = this.$refs.listbox;
                    const option = listbox.querySelector(`[id$="listbox-option-${this.highlightedIndex}"]`);
                    
                    if (option) {
                        option.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest',
                            inline: 'nearest'
                        });
                    }
                }
            }
        };
    }
</script>
@endpush
