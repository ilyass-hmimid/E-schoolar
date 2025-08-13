@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-4 py-2 rounded-lg bg-primary-50 text-primary-700 font-medium text-sm leading-5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium text-sm leading-5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
