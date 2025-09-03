<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Devoir;
use App\Models\Enseignement;
use App\Models\Note;
use App\Models\Paiement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'élève
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        $aujourdhui = Carbon::now()->format('d/m/Y');
        
        // Récupérer les enseignements de l'élève
        $enseignements = Enseignement::whereHas('eleves', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['matiere', 'filiere', 'niveau', 'professeur'])
            ->where('date_fin', '>=', now())
            ->orderBy('date_debut')
            ->get();
            
        // Calculer la moyenne générale
        $moyenne = Note::where('etudiant_id', $user->id)
            ->select(DB::raw('AVG(note) as moyenne'))
            ->first()
            ->moyenne;
            
        $moyenne = $moyenne ? number_format($moyenne, 2, ',', ' ') : 'N/A';
            
        // Prochains devoirs
        $prochainsDevoirs = Devoir::whereHas('classe.eleves', function($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->where('date_limite', '>=', now())
            ->orderBy('date_limite')
            ->take(5)
            ->get();
            
        // Prochaines séances
        $prochainesSeances = $enseignements
            ->filter(function($enseignement) {
                return $enseignement->date_debut->isAfter(now());
            })
            ->sortBy('date_debut')
            ->take(5);
            
        // Dernières notes
        $dernieresNotes = Note::where('etudiant_id', $user->id)
            ->with(['enseignement.matiere', 'enseignement.professeur'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Dernières absences
        $dernieresAbsences = Absence::where('etudiant_id', $user->id)
            ->with(['enseignement.matiere', 'enseignement.professeur'])
            ->orderBy('date_absence', 'desc')
            ->take(5)
            ->get();
            
        // Absences non justifiées
        $absencesNonJustifiees = Absence::where('etudiant_id', $user->id)
            ->where('est_justifiee', false)
            ->with(['enseignement.matiere'])
            ->orderBy('date_absence', 'desc')
            ->get();
            
        // Derniers paiements
        $derniersPaiements = Paiement::where('etudiant_id', $user->id)
            ->orderBy('date_paiement', 'desc')
            ->take(5)
            ->get();
            
        return view('eleve.dashboard', [
            'moyenne' => $moyenne,
            'aujourdhui' => $aujourdhui,
            'prochains_devoirs' => $prochainsDevoirs,
            'prochaines_seances' => $prochainesSeances,
            'dernieres_notes' => $dernieresNotes,
            'dernieres_absences' => $dernieresAbsences,
            'absences_non_justifiees' => $absencesNonJustifiees,
            'derniers_paiements' => $derniersPaiements,
            'enseignements' => $enseignements,
        ]);
    }
}
