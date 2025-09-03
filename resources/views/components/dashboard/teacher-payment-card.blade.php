@props([
    'totalPaid' => 0,
    'pendingPayments' => 0,
    'paymentRate' => 0,
])

@php
    $totalPayments = $totalPaid + $pendingPayments;
    $paidPercentage = $totalPayments > 0 ? round(($totalPaid / $totalPayments) * 100) : 0;
@endphp

<x-dashboard.stats-card 
    title="Paiements professeurs"
    :value="number_format($totalPaid, 0, ',', ' ') . ' DH'"
    icon="fas fa-money-bill-wave"
    color="blue"
    :trend="$paymentRate >= 0 ? 'up' : 'down'"
    :trendValue="abs($paymentRate) . '%'"
    :trendLabel="$paymentRate >= 0 ? 'vs mois dernier' : 'vs mois dernier'"
>
    <x-slot name="footer">
        <div class="mt-4 pt-3 border-t border-gray-700">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-gray-400">En attente</span>
                <span class="font-medium text-white">{{ number_format($pendingPayments, 0, ',', ' ') }} DH</span>
            </div>
            <div class="w-full bg-gray-700 rounded-full h-2">
                <div 
                    class="bg-blue-500 h-2 rounded-full" 
                    style="width: {{ $paidPercentage }}%"
                ></div>
            </div>
            <div class="flex justify-between text-xs text-gray-400 mt-1">
                <span>{{ $paidPercentage }}% payé</span>
                <span>100%</span>
            </div>
        </div>
    </x-slot>
</x-dashboard.stats-card>
