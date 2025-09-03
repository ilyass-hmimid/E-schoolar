@props([
    'headers' => [],
    'rows' => [],
    'emptyMessage' => 'Aucune donnée disponible',
    'emptyIcon' => 'fas fa-inbox',
    'hover' => true,
    'striped' => true,
    'compact' => false,
    'responsive' => true,
    'actions' => null,
    'actionColumn' => true,
    'rowUrl' => null,
    'id' => null,
    'class' => '',
])

@php
    $tableClasses = [
        'min-w-full divide-y divide-gray-700',
        $class,
    ];
    
    $theadClasses = [
        'bg-dark-700',
    ];
    
    $thClasses = [
        'px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider',
        'whitespace-nowrap',
    ];
    
    $tbodyClasses = [
        'bg-dark-800 divide-y divide-gray-700',
        $striped ? 'divide-y divide-gray-700' : '',
    ];
    
    $trClasses = [
        $hover ? 'hover:bg-dark-700 transition-colors' : '',
    ];
    
    $tdClasses = [
        'px-6 py-4 whitespace-nowrap',
        $compact ? 'py-2 text-sm' : 'py-4',
    ];
    
    $emptyTdClasses = [
        'px-6 py-12 text-center text-gray-400',
    ];
    
    // Handle row click
    $rowClick = $rowUrl ? "onclick="window.location.href='{$rowUrl}'.replace('__id__', '{{ '" + row.id + "' }}')"" : '';
@endif

<div class="overflow-x-auto rounded-lg border border-gray-700 shadow">
    <div class="inline-block min-w-full align-middle">
        <div class="overflow-hidden">
            <table class="{{ implode(' ', $tableClasses) }}" {{ $id ? 'id='.$id : '' }}>
                @if(!empty($headers))
                    <thead class="{{ implode(' ', $theadClasses) }}">
                        <tr>
                            @foreach($headers as $header)
                                <th scope="col" class="{{ implode(' ', $thClasses) }}">
                                    {{ $header }}
                                </th>
                            @endforeach
                            @if($actionColumn && $actions)
                                <th scope="col" class="{{ implode(' ', $thClasses) }} text-right">
                                    Actions
                                </th>
                            @endif
                        </tr>
                    </thead>
                @endif
                
                <tbody class="{{ implode(' ', $tbodyClasses) }}">
                    @if(count($rows) > 0)
                        @foreach($rows as $row)
                            <tr class="{{ implode(' ', $trClasses) }}" {!! $rowClick ? str_replace('row.id', $row['id'] ?? $loop->index, $rowClick) : '' !!}>
                                @foreach($row as $key => $cell)
                                    @if($key !== 'id' && $key !== 'actions')
                                        <td class="{{ implode(' ', $tdClasses) }}">
                                            {!! $cell !!}
                                        </td>
                                    @endif
                                @endforeach
                                
                                @if($actionColumn && $actions)
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            {!! is_callable($actions) ? $actions($row) : $actions !!}
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ count($headers) + ($actionColumn ? 1 : 0) }}" class="{{ implode(' ', $emptyTdClasses) }}">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="{{ $emptyIcon }} text-4xl text-gray-600"></i>
                                    <p class="text-gray-400">{{ $emptyMessage }}</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($responsive)
    <style>
        @media (max-width: 768px) {
            .responsive-table {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
@endif
