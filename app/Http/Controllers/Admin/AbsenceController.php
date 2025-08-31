<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Cours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsenceController extends Controller
{
    /**
     * Affiche la liste des absences
     */
    public function index()
    {
        $absences = Absence::with(['eleve', 'cours.matiere', 'cours.professeur'])
            ->orderBy('date_absence', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.absences.index', compact('absences'));
    }

    /**
     * Affiche le formulaire de création d'une absence
     */
    public function create()
    {
        $eleves = Eleve::with('classe')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
            
        $cours = Cours::with(['matiere', 'professeur'])
            ->orderBy('date_cours', 'desc')
            ->get();
            
        return view('admin.absences.create', compact('eleves', 'cours'));
    }

    /**
     * Enregistre une nouvelle absence
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'cours_id' => 'required|exists:cours,id',
            'date_absence' => 'required|date',
            'justificatif' => 'nullable|string',
            'justifiee' => 'boolean'
        ]);

        try {
            DB::beginTransaction();
            
            // Vérifier si une absence similaire existe déjà
            $existingAbsence = Absence::where('eleve_id', $validated['eleve_id'])
                ->where('cours_id', $validated['cours_id'])
                ->whereDate('date_absence', $validated['date_absence'])
                ->first();
                
            if ($existingAbsence) {
                return back()
                    ->withInput()
                    ->with('error', 'Une absence pour cet élève à ce cours à cette date existe déjà.');
            }
            
            // Créer l'absence
            $absence = Absence::create($validated);
            
            DB::commit();
            
            return redirect()->route('admin.absences.index')
                ->with('success', 'Absence enregistrée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement de l\'absence: ' . $e->getMessage());
        }
    }

    /**
     * Affiche le formulaire d'édition d'une absence
     */
    public function edit(Absence $absence)
    {
        $absence->load(['eleve.classe', 'cours.matiere', 'cours.professeur']);
        return view('admin.absences.edit', compact('absence'));
    }

    /**
     * Met à jour une absence
     */
    public function update(Request $request, Absence $absence)
    {
        $validated = $request->validate([
            'justificatif' => 'nullable|string',
            'justifiee' => 'boolean'
        ]);

        try {
            $absence->update($validated);
            
            return redirect()->route('admin.absences.index')
                ->with('success', 'Absence mise à jour avec succès.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'absence: ' . $e->getMessage());
        }
    }

    /**
     * Supprime une absence
     */
    public function destroy(Absence $absence)
    {
        try {
            $absence->delete();
            
            return redirect()->route('admin.absences.index')
                ->with('success', 'Absence supprimée avec succès.');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'absence: ' . $e->getMessage());
        }
    }
}
