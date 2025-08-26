<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Enseignement;
use App\Models\Note;
use App\Enums\RoleType;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord du professeur
     */
    public function index()
    {
        $professeur = auth()->user();
        
        // Récupérer les enseignements du professeur
        $enseignements = Enseignement::where('professeur_id', $professeur->id)
            ->with(['matiere', 'filiere', 'niveau'])
            ->where('date_fin', '>=', now())
            ->orderBy('date_debut')
            ->get();
            
        // Récupérer les prochaines séances
        $prochainesSeances = $enseignements
            ->filter(function($enseignement) {
                return $enseignement->date_debut->isAfter(now());
            })
            ->take(5);
            
        // Récupérer les dernières notes
        $dernieresNotes = Note::whereHas('enseignement', function($query) use ($professeur) {
                $query->where('professeur_id', $professeur->id);
            })
            ->with(['eleve', 'enseignement.matiere'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Récupérer les dernières absences
        $dernieresAbsences = Absence::whereHas('enseignement', function($query) use ($professeur) {
                $query->where('professeur_id', $professeur->id);
            })
            ->with(['eleve', 'enseignement.matiere'])
            ->orderBy('date_absence', 'desc')
            ->take(5)
            ->get();
            
        return Inertia::render('Dashboard/Professeur/Index', [
            'enseignements' => $enseignements,
            'prochainesSeances' => $prochainesSeances,
            'dernieresNotes' => $dernieresNotes,
            'dernieresAbsences' => $dernieresAbsences,
        ]);
    }
}
