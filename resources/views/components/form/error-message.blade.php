@props([
    'name',
    'class' => '',
    'icon' => 'exclamation-circle',
])

@error($name)
    <p {{ $attributes->merge(['class' => 'mt-1 text-sm text-red-500 flex items-start ' . $class]) }}>
        @if($icon)
            <i class="fas fa-{{ $icon }} mr-1.5 mt-0.5 flex-shrink-0"></i>
        @endif
        <span>{{ $message }}</span>
    </p>
@enderror
