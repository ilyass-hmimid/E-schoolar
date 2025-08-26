<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Enseignement;
use Inertia\Inertia;

class AbsenceController extends Controller
{
    /**
     * Affiche la liste des absences de l'élève
     */
    public function index()
    {
        $eleve = auth()->user();
        
        $absences = Absence::where('eleve_id', $eleve->id)
            ->with(['enseignement.matiere', 'enseignement.professeur'])
            ->orderBy('date_absence', 'desc')
            ->paginate(10);
            
        return Inertia::render('Eleve/Absences/Index', [
            'absences' => $absences,
        ]);
    }
    
    /**
     * Affiche les détails d'une absence
     */
    public function show(Absence $absence)
    {
        $this->authorize('view', $absence);
        
        $absence->load(['enseignement.matiere', 'enseignement.professeur']);
        
        return Inertia::render('Eleve/Absences/Show', [
            'absence' => $absence,
        ]);
    }
}
