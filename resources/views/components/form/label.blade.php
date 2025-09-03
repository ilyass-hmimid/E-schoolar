@props([
    'for',
    'required' => false,
    'class' => '',
    'value' => null,
])

<label for="{{ $for }}" {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-300 ' . $class]) }}>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-red-500">*</span>
    @endif
</label>
