<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Classe;
use App\Models\Devoir;
use App\Models\Note;
use App\Models\Absence;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $semaineProchaine = $aujourdhui->copy()->addWeek();
        
        // Récupérer les classes du professeur
        $classes = $user->classes()->withCount('eleves')->get();
        
        // Récupérer les prochains cours (7 prochains jours)
        $prochainsCours = $user->cours()
            ->whereBetween('date_debut', [$aujourdhui, $semaineProchaine])
            ->with(['classe', 'matiere'])
            ->orderBy('date_debut')
            ->get();
            
        // Récupérer les derniers devoirs créés
        $derniersDevoirs = Devoir::where('professeur_id', $user->id)
            ->with(['classe', 'matiere'])
            ->latest()
            ->take(5)
            ->get();
            
        // Récupérer les dernières notes attribuées
        $dernieresNotes = Note::whereHas('cours', function($query) use ($user) {
                $query->where('professeur_id', $user->id);
            })
            ->with(['eleve', 'matiere'])
            ->latest()
            ->take(5)
            ->get();
            
        // Récupérer les absences non justifiées des élèves
        $absencesNonJustifiees = Absence::whereHas('cours', function($query) use ($user) {
                $query->where('professeur_id', $user->id);
            })
            ->where('est_justifiee', false)
            ->with(['eleve', 'matiere'])
            ->latest('date_absence')
            ->take(5)
            ->get();
            
        // Statistiques
        $stats = [
            'total_classes' => $classes->count(),
            'total_eleves' => $classes->sum('eleves_count'),
            'cours_ce_mois' => $user->cours()
                ->whereMonth('date_debut', $aujourdhui->month)
                ->count(),
            'devoirs_en_attente' => $user->devoirs()
                ->where('date_limite', '>=', $aujourdhui)
                ->count()
        ];
        
        return view('professeur.dashboard', [
            'classes' => $classes,
            'prochains_cours' => $prochainsCours,
            'derniers_devoirs' => $derniersDevoirs,
            'dernieres_notes' => $dernieresNotes,
            'absences_non_justifiees' => $absencesNonJustifiees,
            'stats' => $stats,
            'aujourdhui' => $aujourdhui->format('d/m/Y')
        ]);
    }
}
