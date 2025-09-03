@props([
    'totalTeachers' => 0,
    'availableTeachers' => 0,
    'activeClasses' => 0,
])

<x-dashboard.stats-card 
    title="Total des professeurs"
    :value="number_format($totalTeachers, 0, ',', ' ')"
    icon="fas fa-chalkboard-teacher"
    color="purple"
    :trend="$availableTeachers > 0 ? 'up' : 'down'"
    :trendValue="number_format($availableTeachers, 0, ',', ' ')"
    trendLabel="disponibles"
>
    <x-slot name="footer">
        <div class="mt-4 pt-3 border-t border-gray-700">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-400">Cours en cours</span>
                <span class="font-medium text-white">{{ $activeClasses }}</span>
            </div>
        </div>
    </x-slot>
</x-dashboard.stats-card>
