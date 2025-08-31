<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Note;
use App\Models\Devoir;
use App\Models\Absence;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EleveDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'élève
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $aujourdhui = Carbon::today();
        $semaineProchaine = $aujourdhui->copy()->addWeek();
        
        // Récupérer les prochaines présences (7 prochains jours)
        $prochainesPresences = $user->presences()
            ->whereBetween('date_seance', [$aujourdhui, $semaineProchaine])
            ->with(['matiere', 'classe', 'professeur'])
            ->orderBy('date_seance')
            ->orderBy('heure_debut')
            ->take(5)
            ->get();
            
        // Récupérer les dernières notes
        $dernieresNotes = $user->notes()
            ->with('matiere')
            ->orderByDesc('date_evaluation')
            ->take(5)
            ->get();
            
        // Récupérer les absences non justifiées
        $absencesNonJustifiees = $user->absences()
            ->where('est_justifiee', false)
            ->with('matiere')
            ->orderByDesc('date_absence')
            ->take(5)
            ->get();
            
        // Récupérer les prochains devoirs
        $prochainsDevoirs = Devoir::where('classe_id', $user->classe_id)
            ->where('date_limite', '>=', $aujourdhui)
            ->with('matiere')
            ->orderBy('date_limite')
            ->take(5)
            ->get();
        
        // Calculer la moyenne générale
        $moyenne = $user->notes()->avg('valeur');
        
        return view('eleve.dashboard', [
            'prochains_cours' => $prochainesPresences,
            'dernieres_notes' => $dernieresNotes,
            'absences_non_justifiees' => $absencesNonJustifiees,
            'prochains_devoirs' => $prochainsDevoirs,
            'moyenne' => round($moyenne, 2),
            'aujourdhui' => $aujourdhui->format('d/m/Y')
        ]);
    }
}
