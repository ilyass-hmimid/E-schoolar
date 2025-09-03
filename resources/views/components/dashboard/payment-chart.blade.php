@props([
    'title' => 'Statistiques de paiement',
    'paidData' => [],
    'unpaidData' => [],
    'labels' => [],
])

<x-dashboard.chart 
    :title="$title"
    type="bar"
    :chartData="[
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Payé',
                'data' => $paidData,
                'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                'borderColor' => 'rgba(16, 185, 129, 1)',
                'borderWidth' => 1,
                'borderRadius' => 4,
                'barPercentage' => 0.7,
            ],
            [
                'label' => 'En attente',
                'data' => $unpaidData,
                'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                'borderColor' => 'rgba(239, 68, 68, 1)',
                'borderWidth' => 1,
                'borderRadius' => 4,
                'barPercentage' => 0.7,
            ]
        ]
    ]"
    :options="[
        'scales' => [
            'x' => [
                'stacked' => true,
                'grid' => [
                    'display' => false
                ]
            ],
            'y' => [
                'stacked' => true,
                'beginAtZero' => true,
                'ticks' => [
                    'callback' => 'function(value) { return value + " DH"; }'
                ]
            ]
        ],
        'plugins' => [
            'legend' => [
                'position' => 'top',
                'align' => 'end',
                'labels' => [
                    'usePointStyle' => true,
                    'pointStyle' => 'circle',
                    'padding' => 20
                ]
            ],
            'tooltip' => [
                'callbacks' => [
                    'label' => 'function(context) { 
                        return context.dataset.label + ": " + context.raw.toLocaleString() + " DH"; 
                    }'
                ]
            ]
        ]
    ]"
    class="h-80"
/>
