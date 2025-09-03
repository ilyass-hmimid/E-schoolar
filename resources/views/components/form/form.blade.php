@props([
    'method' => 'POST',
    'action' => '',
    'hasFiles' => false,
    'class' => 'space-y-6',
    'id' => null,
    'validate' => false,
])

@php
    $method = strtoupper($method);
    $spoofedMethods = ['PUT', 'PATCH', 'DELETE'];
    $isSpoofed = in_array($method, $spoofedMethods);
    $formMethod = $isSpoofed ? 'POST' : $method;
    
    // Add validation attribute if needed
    $formAttributes = $attributes->merge([
        'class' => $class,
        'id' => $id,
    ]);
    
    if ($validate) {
        $formAttributes = $formAttributes->merge(['data-validate' => 'true']);
    }
    
    // Add file enctype if needed
    if ($hasFiles) {
        $formAttributes = $formAttributes->merge(['enctype' => 'multipart/form-data']);
    }
@endphp

<form method="{{ $formMethod }}" action="{{ $action }}" {!! $formAttributes !!}>
    @csrf
    
    @if($isSpoofed)
        @method($method)
    @endif
    
    {{ $slot }}
    
    @if (isset($actions))
        <div class="flex items-center justify-end space-x-4 pt-6">
            {{ $actions }}
        </div>
    @endif
</form>
