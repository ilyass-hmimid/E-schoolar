<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Absence;
use Inertia\Inertia;
use Carbon\Carbon;

class ClasseController extends Controller
{
    /**
     * Affiche la liste des classes avec des statistiques
     */
    public function index(Request $request)
    {
        $query = Classe::query();
        
        // Filtres
        if ($request->has('niveau') && $request->niveau) {
            $query->where('niveau', $request->niveau);
        }
        
        $classes = $query->withCount('eleves')
            ->with(['professeurPrincipal'])
            ->orderBy('niveau')
            ->orderBy('nom')
            ->paginate(15);
            
        // Statistiques globales
        $stats = [
            'total_classes' => Classe::count(),
            'total_eleves' => Eleve::count(),
            'moyenne_eleves_par_classe' => Classe::count() > 0 
                ? round(Eleve::count() / Classe::count(), 1) 
                : 0,
            'absences_ce_mois' => Absence::whereMonth('date', Carbon::now()->month)->count()
        ];
        
        return Inertia::render('Assistant/Classes/Index', [
            'classes' => $classes,
            'stats' => $stats,
            'filters' => $request->only(['niveau'])
        ]);
    }
    
    /**
     * Affiche les détails d'une classe
     */
    public function show($id)
    {
        $classe = Classe::with([
            'eleves' => function($query) {
                $query->orderBy('nom')->orderBy('prenom');
            },
            'professeurPrincipal',
            'matieres.professeur'
        ])->findOrFail($id);
        
        // Statistiques de la classe
        $stats = [
            'total_eleves' => $classe->eleves->count(),
            'garcons' => $classe->eleves->where('sexe', 'M')->count(),
            'filles' => $classe->eleves->where('sexe', 'F')->count(),
            'moyenne_age' => $classe->eleves->avg(function($eleve) {
                return Carbon::parse($eleve->date_naissance)->age;
            }),
            'absences_ce_mois' => Absence::where('classe_id', $id)
                ->whereMonth('date', Carbon::now()->month)
                ->count(),
            'taux_absenteisme' => $classe->eleves->count() > 0
                ? round(Absence::where('classe_id', $id)
                    ->whereMonth('date', Carbon::now()->month)
                    ->count() / ($classe->eleves->count() * 20) * 100, 2) // 20 jours d'école par mois
                : 0
        ];
        
        // Dernières absences de la classe
        $dernieres_absences = Absence::with('eleve')
            ->where('classe_id', $id)
            ->latest('date')
            ->take(10)
            ->get();
        
        return Inertia::render('Assistant/Classes/Show', [
            'classe' => $classe,
            'stats' => $stats,
            'dernieres_absences' => $dernieres_absences
        ]);
    }
}
