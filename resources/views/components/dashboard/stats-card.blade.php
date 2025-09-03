@props([
    'title',
    'value',
    'icon',
    'trend' => null, // 'up' or 'down'
    'trendValue' => null,
    'trendLabel' => null,
    'color' => 'primary',
    'class' => '',
])

@php
    // Define color variants
    $colors = [
        'primary' => [
            'bg' => 'bg-primary-500/10',
            'icon' => 'text-primary-500',
            'trendUp' => 'text-green-500',
            'trendDown' => 'text-red-500',
        ],
        'success' => [
            'bg' => 'bg-green-500/10',
            'icon' => 'text-green-500',
            'trendUp' => 'text-green-500',
            'trendDown' => 'text-red-500',
        ],
        'danger' => [
            'bg' => 'bg-red-500/10',
            'icon' => 'text-red-500',
            'trendUp' => 'text-green-500',
            'trendDown' => 'text-red-500',
        ],
        'warning' => [
            'bg' => 'bg-yellow-500/10',
            'icon' => 'text-yellow-500',
            'trendUp' => 'text-green-500',
            'trendDown' => 'text-red-500',
        ],
        'info' => [
            'bg' => 'bg-blue-500/10',
            'icon' => 'text-blue-500',
            'trendUp' => 'text-green-500',
            'trendDown' => 'text-red-500',
        ],
    ];
    
    $colorScheme = $colors[$color] ?? $colors['primary'];
    $trendColor = $trend === 'up' ? $colorScheme['trendUp'] : $colorScheme['trendDown'];
    $trendIcon = $trend === 'up' ? 'fas fa-arrow-up' : 'fas fa-arrow-down';
@endphp

<div class="bg-dark-800 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-200 {{ $class }}">
    <div class="flex items-center justify-between">
        <div class="flex-shrink-0">
            <div class="p-3 rounded-lg {{ $colorScheme['bg'] }} w-12 h-12 flex items-center justify-center">
                <i class="{{ $icon }} text-xl {{ $colorScheme['icon'] }}"></i>
            </div>
        </div>
        <div class="ml-4 flex-1">
            <div class="text-sm font-medium text-gray-400">{{ $title }}</div>
            <div class="mt-1 text-2xl font-semibold text-white">{{ $value }}</div>
            
            @if($trend && $trendValue)
                <div class="mt-1 flex items-center">
                    <span class="text-sm font-medium {{ $trendColor }}">
                        <i class="{{ $trendIcon }} mr-1"></i>
                        {{ $trendValue }}
                    </span>
                    @if($trendLabel)
                        <span class="ml-1 text-xs text-gray-500">{{ $trendLabel }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
    
    @if(isset($footer))
        <div class="mt-4 pt-3 border-t border-gray-700">
            {{ $footer }}
        </div>
    @endif
</div>
