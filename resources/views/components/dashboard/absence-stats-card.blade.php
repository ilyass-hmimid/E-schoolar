@props([
    'totalAbsences' => 0,
    'monthlyAbsences' => 0,
    'absenceRate' => 0,
])

<x-dashboard.stats-card 
    title="Absences du mois"
    :value="$monthlyAbsences"
    icon="fas fa-calendar-times"
    color="orange"
    :trend="$absenceRate >= 0 ? 'down' : 'up'"
    :trendValue="abs($absenceRate) . '%'"
    :trendLabel="$absenceRate >= 0 ? 'plus que le mois dernier' : 'moins que le mois dernier'"
>
    <x-slot name="footer">
        <div class="mt-4 pt-3 border-t border-gray-700">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-400">Total cette année</span>
                <span class="font-medium text-white">{{ $totalAbsences }}</span>
            </div>
            <div class="mt-2 w-full bg-gray-700 rounded-full h-2">
                <div 
                    class="bg-orange-500 h-2 rounded-full" 
                    style="width: {{ min($absenceRate, 100) }}%"
                ></div>
            </div>
        </div>
    </x-slot>
</x-dashboard.stats-card>
