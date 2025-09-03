@props([
    'totalStudents' => 0,
    'newStudentsThisMonth' => 0,
    'attendanceRate' => 0,
])

<x-dashboard.stats-card 
    title="Total des élèves"
    :value="number_format($totalStudents, 0, ',', ' ')"
    icon="fas fa-user-graduate"
    color="primary"
    :trend="$newStudentsThisMonth > 0 ? 'up' : 'down'"
    :trendValue="number_format(abs($newStudentsThisMonth), 0, ',', ' ')"
    trendLabel="nouveaux ce mois"
>
    <x-slot name="footer">
        <div class="mt-4 pt-3 border-t border-gray-700">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-400">Taux de présence</span>
                <span class="font-medium text-white">{{ number_format($attendanceRate, 1) }}%</span>
            </div>
            <div class="mt-2 w-full bg-gray-700 rounded-full h-2">
                <div 
                    class="bg-green-500 h-2 rounded-full" 
                    style="width: {{ $attendanceRate }}%"
                ></div>
            </div>
        </div>
    </x-slot>
</x-dashboard.stats-card>
