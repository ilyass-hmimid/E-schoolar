<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Absence;
use Inertia\Inertia;

class EleveController extends Controller
{
    /**
     * Affiche la liste des élèves avec des filtres
     */
    public function index(Request $request)
    {
        $query = Eleve::query();
        
        // Filtres
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%$search%")
                  ->orWhere('prenom', 'like', "%$search%")
                  ->orWhere('matricule', 'like', "%$search%");
            });
        }
        
        if ($request->has('classe_id') && $request->classe_id) {
            $query->where('classe_id', $request->classe_id);
        }
        
        $eleves = $query->with('classe')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->paginate(20);
            
        $classes = Classe::orderBy('nom')->get();
        
        return Inertia::render('Assistant/Eleves/Index', [
            'eleves' => $eleves,
            'classes' => $classes,
            'filters' => $request->only(['search', 'classe_id'])
        ]);
    }
    
    /**
     * Affiche les détails d'un élève
     */
    public function show($id)
    {
        $eleve = Eleve::with(['classe', 'absences' => function($query) {
            $query->latest('date')->take(10);
        }])->findOrFail($id);
        
        // Statistiques d'absences
        $stats = [
            'total_absences' => $eleve->absences()->count(),
            'absences_justifiees' => $eleve->absences()->where('justifiee', true)->count(),
            'absences_non_justifiees' => $eleve->absences()->where('justifiee', false)->count(),
            'taux_absenteisme' => $eleve->absences()->count() > 0 
                ? round(($eleve->absences()->count() / 180) * 100, 2) 
                : 0 // Sur une base de 180 jours d'école
        ];
        
        return Inertia::render('Assistant/Eleves/Show', [
            'eleve' => $eleve,
            'stats' => $stats
        ]);
    }
}
