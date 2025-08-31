<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Enseignement;
use App\Models\Note;
use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\Classe;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord du professeur
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $professeur = Auth::user();
        $maintenant = now();
        $moisDernier = now()->subMonth();
        
        // Récupérer les enseignements actuels
        $enseignements = Enseignement::where('professeur_id', $professeur->id)
            ->where('date_fin', '>=', $maintenant)
            ->with(['classe', 'matiere'])
            ->get();
            
        // Récupérer les enseignements du mois dernier pour comparaison
        $enseignementsMoisDernier = Enseignement::where('professeur_id', $professeur->id)
            ->whereBetween('date_debut', [$moisDernier, $maintenant])
            ->get();
        
        // Récupérer les élèves actuels
        $eleves = Eleve::whereIn('classe_id', $enseignements->pluck('classe_id'))->get();
        
        // Récupérer les élèves du mois dernier
        $elevesMoisDernier = Eleve::whereIn('classe_id', $enseignementsMoisDernier->pluck('classe_id'))->get();
        
        // Récupérer la moyenne générale actuelle
        $moyenneGenerale = Note::whereHas('enseignement', function($query) use ($professeur) {
            $query->where('professeur_id', $professeur->id);
        })->avg('valeur');
        
        // Récupérer les prochains cours (7 prochains jours)
        $prochainsCours = $professeur->cours()
            ->whereBetween('date_debut', [$maintenant, $maintenant->copy()->addWeek()])
            ->with(['classe', 'matiere'])
            ->orderBy('date_debut')
            ->get();
            
        // Récupérer les derniers devoirs créés
        $derniersDevoirs = $professeur->devoirs()
            ->with(['classe', 'matiere'])
            ->latest()
            ->take(5)
            ->get();
            
        // Récupérer les dernières notes attribuées
        $dernieresNotes = Note::whereHas('cours', function($query) use ($professeur) {
                $query->where('professeur_id', $professeur->id);
            })
            ->with(['eleve', 'matiere'])
            ->latest()
            ->take(5)
            ->get();
            
        // Récupérer les absences non justifiées des élèves
        $absencesNonJustifiees = Absence::whereHas('cours', function($query) use ($professeur) {
                $query->where('professeur_id', $professeur->id);
            })
            ->where('est_justifiee', false)
            ->with(['eleve', 'matiere'])
            ->latest('date_absence')
            ->take(5)
            ->get();
            
        // Statistiques
        $stats = [
            'total_classes' => $enseignements->count(),
            'total_eleves' => $eleves->count(),
            'cours_ce_mois' => $professeur->cours()
                ->whereMonth('date_debut', $maintenant->month)
                ->count(),
            'devoirs_en_attente' => $professeur->devoirs()
                ->where('date_limite', '>=', $maintenant)
                ->count()
        ];
        
        // Calculer les évolutions
        $nbClasses = $enseignements->groupBy('classe_id')->count();
        $nbClassesMoisDernier = $enseignementsMoisDernier->groupBy('classe_id')->count();
        $evolutionClasses = $nbClassesMoisDernier > 0 
            ? round((($nbClasses - $nbClassesMoisDernier) / $nbClassesMoisDernier) * 100, 1)
            : 0;
            
        $evolutionEleves = $elevesMoisDernier->count() > 0 
            ? round((($eleves->count() - $elevesMoisDernier->count()) / $elevesMoisDernier->count()) * 100, 1)
            : 0;
            
        $moyenneMoisDernier = Note::whereHas('enseignement', function($query) use ($professeur, $moisDernier, $maintenant) {
            $query->where('professeur_id', $professeur->id)
                  ->whereBetween('created_at', [$moisDernier, $maintenant]);
        })->avg('valeur');
        
        $evolutionMoyenne = $moyenneMoisDernier > 0 
            ? round((($moyenneGenerale - $moyenneMoisDernier) / $moyenneMoisDernier) * 100, 1)
            : 0;

        return view('professeur.dashboard', [
            'classes' => $enseignements->pluck('classe')->unique(),
            'prochains_cours' => $prochainsCours,
            'derniers_devoirs' => $derniersDevoirs,
            'dernieres_notes' => $dernieresNotes,
            'absences_non_justifiees' => $absencesNonJustifiees,
            'stats' => $stats,
            'aujourdhui' => $maintenant->format('d/m/Y'),
            'moyenne_generale' => $moyenneGenerale,
            'enseignements' => $enseignements,
            'evolution_classes' => $evolutionClasses,
            'evolution_eleves' => $evolutionEleves,
            'evolution_moyenne' => $evolutionMoyenne
        ]);
    }
    
    /**
     * Récupère la répartition des notes par matière
     */
    protected function getNotesParMatiere($professeurId)
    {
        return DB::table('notes')
            ->join('enseignements', 'notes.enseignement_id', '=', 'enseignements.id')
            ->join('matieres', 'enseignements.matiere_id', '=', 'matieres.id')
            ->select(
                'matieres.nom as matiere',
                DB::raw('AVG(notes.valeur) as moyenne'),
                DB::raw('COUNT(notes.id) as nombre_notes')
            )
            ->where('enseignements.professeur_id', $professeurId)
            ->groupBy('matieres.id', 'matieres.nom')
            ->orderBy('moyenne', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'matiere' => $item->matiere,
                    'moyenne' => round($item->moyenne, 2),
                    'nombre_notes' => $item->nombre_notes
                ];
            });
    }
    
    /**
     * Récupère la fréquence des absences par jour
     */
    protected function getFrequenceAbsences($professeurId)
    {
        $debut = now()->subDays(30);
        $fin = now();
        
        $absences = DB::table('absences')
            ->join('enseignements', 'absences.enseignement_id', '=', 'enseignements.id')
            ->select(
                DB::raw('DATE(absences.date_absence) as date'),
                DB::raw('COUNT(absences.id) as nombre_absences')
            )
            ->where('enseignements.professeur_id', $professeurId)
            ->whereBetween('absences.date_absence', [$debut, $fin])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Créer un tableau pour chaque jour de la période
        $jours = collect();
        $current = $debut->copy();
        
        while ($current <= $fin) {
            $date = $current->format('Y-m-d');
            $jour = $current->isoFormat('dddd D MMM');
            
            $absence = $absences->first(function($item) use ($date) {
                return $item->date === $date;
            });
            
            $jours->push([
                'date' => $date,
                'jour' => $jour,
                'absences' => $absence ? $absence->nombre_absences : 0
            ]);
            
            $current->addDay();
        }
        
        return $jours;
    }
}
