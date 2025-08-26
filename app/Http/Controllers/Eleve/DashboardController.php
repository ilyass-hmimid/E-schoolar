<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Enseignement;
use App\Models\Note;
use App\Models\Paiement;
use App\Enums\RoleType;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'élève
     */
    public function index(){
        $eleve = auth()->user();
        
        // Récupérer les enseignements de l'élève
        $enseignements = Enseignement::whereHas('eleves', function($query) use ($eleve) {
                $query->where('user_id', $eleve->id);
            })
            ->with(['matiere', 'filiere', 'niveau', 'professeur'])
            ->where('date_fin', '>=', now())
            ->orderBy('date_debut')
            ->get();
            
        // Prochaines séances
        $prochainesSeances = $enseignements
            ->filter(function($enseignement) {
                return $enseignement->date_debut->isAfter(now());
            })
            ->take(5);
            
        // Dernières notes
        $dernieresNotes = Note::where('eleve_id', $eleve->id)
            ->with(['enseignement.matiere', 'enseignement.professeur'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Dernières absences
        $dernieresAbsences = Absence::where('eleve_id', $eleve->id)
            ->with(['enseignement.matiere', 'enseignement.professeur'])
            ->orderBy('date_absence', 'desc')
            ->take(5)
            ->get();
            
        // Derniers paiements
        $derniersPaiements = Paiement::where('eleve_id', $eleve->id)
            ->orderBy('date_paiement', 'desc')
            ->take(5)
            ->get();
            
        return Inertia::render('Dashboard/Eleve/Index', [
            'enseignements' => $enseignements,
            'prochainesSeances' => $prochainesSeances,
            'dernieresNotes' => $dernieresNotes,
            'dernieresAbsences' => $dernieresAbsences,
            'derniersPaiements' => $derniersPaiements,
        ]);
    }
}
