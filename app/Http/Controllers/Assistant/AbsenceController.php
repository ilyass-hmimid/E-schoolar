<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsencesExport;
use App\Imports\AbsencesImport;

class AbsenceController extends Controller
{
    /**
     * Affiche la liste des absences avec filtres
     */
    public function index(Request $request)
    {
        $query = Absence::with(['eleve', 'classe']);
        
        // Filtres
        if ($request->has('eleve_id') && $request->eleve_id) {
            $query->where('eleve_id', $request->eleve_id);
        }
        
        if ($request->has('date_debut') && $request->date_debut) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        
        if ($request->has('date_fin') && $request->date_fin) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $absences = $query->latest('date')->paginate(20);
        $eleves = Eleve::orderBy('nom')->orderBy('prenom')->get();
        
        return Inertia::render('Assistant/Absences', [
            'absences' => $absences,
            'eleves' => $eleves,
            'filters' => $request->all(['eleve_id', 'date_debut', 'date_fin'])
        ]);
    }
    
    /**
     * Affiche le formulaire de création d'une absence
     */
    public function create()
    {
        $eleves = Eleve::orderBy('nom')->orderBy('prenom')->get();
        $classes = Classe::orderBy('nom')->get();
        
        return Inertia::render('Assistant/Absences/Create', [
            'eleves' => $eleves,
            'classes' => $classes
        ]);
    }
    
    /**
     * Enregistre une nouvelle absence
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'justifiee' => 'boolean',
            'motif' => 'nullable|string|max:500'
        ]);
        
        Absence::create($validated);
        
        return redirect()->route('assistant.absences.index')
            ->with('success', 'Absence enregistrée avec succès.');
    }
    
    /**
     * Affiche les détails d'une absence
     */
    public function show(string $id)
    {
        $absence = Absence::with(['eleve', 'classe'])->findOrFail($id);
        
        return Inertia::render('Assistant/Absences/Show', [
            'absence' => $absence
        ]);
    }
    
    /**
     * Affiche le formulaire de modification d'une absence
     */
    public function edit(string $id)
    {
        $absence = Absence::findOrFail($id);
        $eleves = Eleve::orderBy('nom')->orderBy('prenom')->get();
        $classes = Classe::orderBy('nom')->get();
        
        return Inertia::render('Assistant/Absences/Edit', [
            'absence' => $absence,
            'eleves' => $eleves,
            'classes' => $classes
        ]);
    }
    
    /**
     * Met à jour une absence existante
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'justifiee' => 'boolean',
            'motif' => 'nullable|string|max:500'
        ]);
        
        $absence = Absence::findOrFail($id);
        $absence->update($validated);
        
        return redirect()->route('assistant.absences.index')
            ->with('success', 'Absence mise à jour avec succès.');
    }
    
    /**
     * Supprime une absence
     */
    public function destroy(string $id)
    {
        $absence = Absence::findOrFail($id);
        $absence->delete();
        
        return redirect()->route('assistant.absences.index')
            ->with('success', 'Absence supprimée avec succès.');
    }
    
    /**
     * Affiche les absences d'un élève spécifique
     */
    public function byEleve($eleveId)
    {
        $eleve = Eleve::findOrFail($eleveId);
        $absences = Absence::with('classe')
            ->where('eleve_id', $eleveId)
            ->latest('date')
            ->paginate(15);
            
        return Inertia::render('Assistant/Absences/ByEleve', [
            'eleve' => $eleve,
            'absences' => $absences
        ]);
    }
    
    /**
     * Importe des absences depuis un fichier Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:xlsx,xls'
        ]);
        
        Excel::import(new AbsencesImport, $request->file('fichier'));
        
        return back()->with('success', 'Absences importées avec succès.');
    }
    
    /**
     * Exporte les absences au format Excel
     */
    public function export(Request $request)
    {
        return Excel::download(new AbsencesExport($request->all()), 
            'absences-' . now()->format('Y-m-d') . '.xlsx');
    }
}
