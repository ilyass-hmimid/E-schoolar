<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Absence;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProfesseurDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord du professeur
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $aujourdhui = Carbon::today();
        
        // Récupérer les classes du professeur
        $classes = $user->classes()->withCount('eleves')->get();
        
        // Récupérer les absences récentes
        $absencesRecentes = Absence::whereHas('matiere', function($query) use ($user) {
                $query->where('professeur_id', $user->id);
            })
            ->with(['eleve', 'matiere'])
            ->latest('date_absence')
            ->take(5)
            ->get();
            
        // Statistiques
        $stats = [
            'total_classes' => $classes->count(),
            'total_eleves' => $classes->sum('eleves_count'),
            'absences_ce_mois' => Absence::whereHas('matiere', function($query) use ($user, $aujourdhui) {
                    $query->where('professeur_id', $user->id);
                })
                ->whereMonth('date_absence', $aujourdhui->month)
                ->count()
        ];
        
        return view('professeur.dashboard', [
            'classes' => $classes,
            'absences_recentes' => $absencesRecentes,
            'stats' => $stats,
            'aujourdhui' => $aujourdhui->format('d/m/Y')
        ]);
    }
}
