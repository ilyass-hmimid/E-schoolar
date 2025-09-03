@props([
    'title' => 'Taux de présence',
    'presentData' => [],
    'absentData' => [],
    'labels' => [],
])

@php
    $totalPresent = array_sum($presentData);
    $totalAbsent = array_sum($absentData);
    $attendanceRate = $totalPresent + $totalAbsent > 0 
        ? round(($totalPresent / ($totalPresent + $totalAbsent)) * 100, 1) 
        : 0;
@endphp

<x-dashboard.chart 
    :title="$title"
    type="line"
    :chartData="[
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Présences',
                'data' => $presentData,
                'borderColor' => 'rgba(16, 185, 129, 1)',
                'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                'tension' => 0.3,
                'fill' => true,
                'pointBackgroundColor' => 'rgba(16, 185, 129, 1)',
                'pointBorderColor' => '#fff',
                'pointHoverRadius' => 5,
                'pointHoverBackgroundColor' => 'rgba(16, 185, 129, 1)',
                'pointHoverBorderColor' => '#fff',
                'pointHitRadius' => 10,
                'pointBorderWidth' => 2,
            ],
            [
                'label' => 'Absences',
                'data' => $absentData,
                'borderColor' => 'rgba(239, 68, 68, 1)',
                'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                'tension' => 0.3,
                'fill' => true,
                'pointBackgroundColor' => 'rgba(239, 68, 68, 1)',
                'pointBorderColor' => '#fff',
                'pointHoverRadius' => 5,
                'pointHoverBackgroundColor' => 'rgba(239, 68, 68, 1)',
                'pointHoverBorderColor' => '#fff',
                'pointHitRadius' => 10,
                'pointBorderWidth' => 2,
            ]
        ]
    ]"
    :options="[
        'responsive' => true,
        'maintainAspectRatio' => false,
        'interaction' => [
            'mode' => 'index',
            'intersect' => false,
        ],
        'scales' => [
            'x' => [
                'grid' => [
                    'display' => false
                ]
            ],
            'y' => [
                'beginAtZero' => true,
                'ticks' => [
                    'stepSize' => 1
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
                'mode' => 'index',
                'intersect' => false,
            ],
            'annotation' => [
                'annotations' => [
                    'line1' => [
                        'type' => 'line',
                        'yMin' => 0,
                        'yMax' => 0,
                        'borderColor' => 'rgba(255, 255, 255, 0.5)',
                        'borderWidth' => 1,
                        'borderDash' => [6, 6],
                        'borderDashOffset' => 0,
                        'label' => [
                            'display' => true,
                            'content' => 'Moyenne: ' . $attendanceRate . '%',
                            'position' => 'right',
                            'backgroundColor' => 'rgba(0, 0, 0, 0.7)',
                            'color' => '#fff',
                            'font' => [
                                'size' => 10
                            ],
                            'padding' => [
                                'top' => 4,
                                'bottom' => 4,
                                'left' => 8,
                                'right' => 8
                            ],
                            'borderRadius' => 4
                        ]
                    ]
                ]
            ]
        ]
    ]"
    class="h-80"
>
    <x-slot name="footer">
        <div class="flex items-center justify-between text-sm mt-4 pt-3 border-t border-gray-700">
            <div class="flex items-center">
                <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                <span class="text-gray-400">Taux de présence:</span>
                <span class="ml-1 font-medium text-white">{{ $attendanceRate }}%</span>
            </div>
            <div class="text-gray-400">
                {{ $totalPresent }} présences / {{ $totalPresent + $totalAbsent }} séances
            </div>
        </div>
    </x-slot>
</x-dashboard.chart>
