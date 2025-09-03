@props([
    'title' => null,
    'type' => 'bar', // bar, line, pie, doughnut
    'chartId' => 'chart-' . uniqid(),
    'height' => '300px',
    'class' => '',
])

@php
    $colors = [
        'primary' => 'rgba(99, 102, 241, 0.8)',
        'success' => 'rgba(16, 185, 129, 0.8)',
        'danger' => 'rgba(239, 68, 68, 0.8)',
        'warning' => 'rgba(245, 158, 11, 0.8)',
        'info' => 'rgba(59, 130, 246, 0.8)',
    ];
    
    $borderColors = [
        'primary' => 'rgb(99, 102, 241)',
        'success' => 'rgb(16, 185, 129)',
        'danger' => 'rgb(239, 68, 68)',
        'warning' => 'rgb(245, 158, 11)',
        'info' => 'rgb(59, 130, 246)',
    ];
@endphp

<div class="bg-dark-800 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-200 {{ $class }}">
    @if($title)
        <h3 class="text-lg font-medium text-white mb-4">{{ $title }}</h3>
    @endif
    
    <div class="relative" style="height: {{ $height }};">
        <canvas id="{{ $chartId }}"></canvas>
    </div>
    
    @if(isset($footer))
        <div class="mt-4 pt-3 border-t border-gray-700">
            {{ $footer }}
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('{{ $chartId }}').getContext('2d');
    const chartData = @json($chartData ?? []);
    
    // Default chart options
    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    color: '#9CA3AF',
                    font: {
                        family: 'Inter, sans-serif',
                    },
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: 'rgba(17, 24, 39, 0.95)',
                titleFont: {
                    family: 'Inter, sans-serif',
                    size: 14
                },
                bodyFont: {
                    family: 'Inter, sans-serif',
                    size: 13,
                    weight: 'normal'
                },
                padding: 12,
                borderColor: 'rgba(255, 255, 255, 0.1)',
                borderWidth: 1,
                displayColors: true,
                usePointStyle: true,
                callbacks: {
                    labelColor: function(context) {
                        return {
                            borderColor: 'transparent',
                            backgroundColor: context.dataset.backgroundColor[context.dataIndex]
                        };
                    }
                }
            }
        },
        scales: {
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    color: '#9CA3AF',
                    font: {
                        family: 'Inter, sans-serif',
                    }
                }
            },
            y: {
                grid: {
                    color: 'rgba(255, 255, 255, 0.05)',
                    drawBorder: false
                },
                ticks: {
                    color: '#9CA3AF',
                    font: {
                        family: 'Inter, sans-serif',
                    },
                    callback: function(value) {
                        return value % 1 === 0 ? value : '';
                    }
                }
            }
        },
        elements: {
            bar: {
                borderRadius: 6,
                borderSkipped: false,
            }
        },
        interaction: {
            intersect: false,
            mode: 'index',
        }
    };
    
    // Merge default options with custom options
    const options = {
        ...defaultOptions,
        ...@json($options ?? [])
    };
    
    // Create the chart
    const chart = new Chart(ctx, {
        type: '{{ $type }}',
        data: chartData,
        options: options
    });
    
    // Make chart instance available globally for updates
    window.chartInstances = window.chartInstances || {};
    window.chartInstances['{{ $chartId }}'] = chart;
});
</script>
@endpush
