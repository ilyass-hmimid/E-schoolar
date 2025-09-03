@props([
    'title' => null,
    'header' => null,
    'footer' => null,
    'noPadding' => false,
    'variant' => 'default',
    'class' => '',
    'headerClass' => '',
    'bodyClass' => '',
    'footerClass' => '',
])

@php
    // Base classes
    $baseClasses = [
        'rounded-lg shadow',
        'border border-gray-700',
        'overflow-hidden',
        $class,
    ];
    
    // Variants
    $variants = [
        'default' => 'bg-dark-800',
        'primary' => 'bg-primary-900/20 border-primary-700/50',
        'success' => 'bg-green-900/20 border-green-700/50',
        'danger' => 'bg-red-900/20 border-red-700/50',
        'warning' => 'bg-yellow-900/20 border-yellow-700/50',
        'info' => 'bg-blue-900/20 border-blue-700/50',
    ];
    
    // Card classes
    $cardClasses = array_merge($baseClasses, [$variants[$variant] ?? $variants['default']]);
    $cardClass = implode(' ', array_filter($cardClasses));
    
    // Header classes
    $headerClasses = [
        'px-6 py-4',
        'border-b border-gray-700',
        'bg-dark-700/50',
        $headerClass,
    ];
    $headerClass = implode(' ', array_filter($headerClasses));
    
    // Body classes
    $bodyClasses = [
        $noPadding ? 'p-0' : 'p-6',
        $bodyClass,
    ];
    $bodyClass = implode(' ', array_filter($bodyClasses));
    
    // Footer classes
    $footerClasses = [
        'px-6 py-4',
        'border-t border-gray-700',
        'bg-dark-700/30',
        $footerClass,
    ];
    $footerClass = implode(' ', array_filter($footerClasses));
@endif

<div {{ $attributes->merge(['class' => $cardClass]) }}>
    @if($header || $title)
        <div class="{{ $headerClass }}">
            @if($title && !$header)
                <h3 class="text-lg font-medium text-white">{{ $title }}</h3>
            @else
                {{ $header }}
            @endif
        </div>
    @endif
    
    <div class="{{ $bodyClass }}">
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="{{ $footerClass }}">
            {{ $footer }}
        </div>
    @endif
</div>
