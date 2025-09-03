@props([
    'totalPaid' => 0,
    'totalUnpaid' => 0,
    'paymentRate' => 0,
])

@php
    $totalAmount = $totalPaid + $totalUnpaid;
    $paidPercentage = $totalAmount > 0 ? round(($totalPaid / $totalAmount) * 100) : 0;
@endphp

<x-dashboard.stats-card 
    title="Paiements élèves"
    :value="number_format($totalPaid, 0, ',', ' ') . ' DH'"
    icon="fas fa-wallet"
    color="green"
    :trend="$paymentRate >= 0 ? 'up' : 'down'"
    :trendValue="abs($paymentRate) . '%'"
    :trendLabel="$paymentRate >= 0 ? 'vs mois dernier' : 'vs mois dernier'"
>
    <x-slot name="footer">
        <div class="mt-4 pt-3 border-t border-gray-700">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-gray-400">En attente</span>
                <span class="font-medium text-white">{{ number_format($totalUnpaid, 0, ',', ' ') }} DH</span>
            </div>
            <div class="w-full bg-gray-700 rounded-full h-2">
                <div 
                    class="bg-green-500 h-2 rounded-full" 
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
