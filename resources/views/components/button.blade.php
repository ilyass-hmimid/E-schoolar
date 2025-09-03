@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'fullWidth' => false,
    'as' => 'button',
    'href' => '#',
    'disabled' => false,
    'loading' => false,
    'loadingText' => 'Chargement...',
    'class' => '',
])

@php
    // Base classes
    $baseClasses = [
        'inline-flex items-center justify-center',
        'font-medium rounded-md',
        'focus:outline-none focus:ring-2 focus:ring-offset-2',
        'transition-colors duration-200',
        'disabled:opacity-50 disabled:cursor-not-allowed',
        $fullWidth ? 'w-full' : '',
    ];
    
    // Variants
    $variants = [
        'primary' => [
            'bg-primary-600 hover:bg-primary-700',
            'text-white',
            'border border-transparent',
            'focus:ring-primary-500',
        ],
        'secondary' => [
            'bg-dark-700 hover:bg-dark-600',
            'text-white',
            'border border-gray-600',
            'focus:ring-dark-500',
        ],
        'success' => [
            'bg-green-600 hover:bg-green-700',
            'text-white',
            'border border-transparent',
            'focus:ring-green-500',
        ],
        'danger' => [
            'bg-red-600 hover:bg-red-700',
            'text-white',
            'border border-transparent',
            'focus:ring-red-500',
        ],
        'warning' => [
            'bg-yellow-600 hover:bg-yellow-700',
            'text-white',
            'border border-transparent',
            'focus:ring-yellow-500',
        ],
        'outline' => [
            'bg-transparent hover:bg-dark-700',
            'text-gray-300',
            'border border-gray-600',
            'hover:border-gray-500',
            'focus:ring-dark-500',
        ],
        'link' => [
            'bg-transparent',
            'text-primary-500 hover:text-primary-400',
            'underline',
            'focus:ring-primary-500',
        ],
    ];
    
    // Sizes
    $sizes = [
        'xs' => ['text-xs', 'px-2.5 py-1.5', 'gap-1'],
        'sm' => ['text-sm', 'px-3 py-2', 'gap-1.5'],
        'md' => ['text-sm', 'px-4 py-2', 'gap-2'],
        'lg' => ['text-base', 'px-6 py-3', 'gap-2'],
        'xl' => ['text-lg', 'px-8 py-4', 'gap-3'],
    ];
    
    // Combine all classes
    $buttonClasses = array_merge(
        $baseClasses,
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
        [$class]
    );
    
    // Remove empty values and join
    $buttonClass = implode(' ', array_filter($buttonClasses));
    
    // Icon classes
    $iconSize = [
        'xs' => 'h-3 w-3',
        'sm' => 'h-3.5 w-3.5',
        'md' => 'h-4 w-4',
        'lg' => 'h-5 w-5',
        'xl' => 'h-6 w-6',
    ][$size] ?? 'h-4 w-4';
    
    // Button content
    $buttonContent = '';
    
    if ($loading) {
        $buttonContent = "
            <svg class='animate-spin -ml-1 mr-2 {$iconSize}' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'>
                <circle class='opacity-25' cx='12' cy='12' r='10' stroke='currentColor' stroke-width='4'></circle>
                <path class='opacity-75' fill='currentColor' d='M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z'></path>
            </svg>
            <span>{$loadingText}</span>
        ";
    } else {
        $iconHtml = $icon ? "<i class='{$icon} {$iconSize}'></i>" : '';
        $slotContent = trim($slot);
        
        if ($icon && $slotContent) {
            if ($iconPosition === 'left') {
                $buttonContent = "{$iconHtml}<span>{$slotContent}</span>";
            } else {
                $buttonContent = "<span>{$slotContent}</span>{$iconHtml}";
            }
        } elseif ($icon) {
            $buttonContent = $iconHtml;
        } else {
            $buttonContent = $slotContent;
        }
    }
@endphp

@if($as === 'a')
    <a 
        {{ $attributes->merge([
            'class' => $buttonClass,
            'href' => $href,
        ]) }}
        @if($disabled) disabled @endif
    >
        {!! $buttonContent !!}
    </a>
@else
    <button 
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $buttonClass]) }}
        @if($disabled) disabled @endif
    >
        {!! $buttonContent !!}
    </button>
@endif
