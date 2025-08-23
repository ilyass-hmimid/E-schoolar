<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FiliereController extends Controller
{
    public function index()
    {
        $filieres = Filiere::with(['niveau'])
            ->withCount(['eleves', 'matieres'])
            ->orderBy('nom')
            ->get();

        $niveaux = Niveau::orderBy('nom')->get();

        return Inertia::render('Filieres/Index', [
            'filieres' => $filieres,
            'niveaux' => $niveaux,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:filieres,nom',
            'description' => 'nullable|string',
            'niveau_id' => 'required|exists:niveaux,id',
            'is_active' => 'boolean',
        ]);

        Filiere::create($request->all());

        return redirect()->back()->with('success', 'Filière créée avec succès.');
    }

    public function update(Request $request, Filiere $filiere)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:filieres,nom,' . $filiere->id,
            'description' => 'nullable|string',
            'niveau_id' => 'required|exists:niveaux,id',
            'is_active' => 'boolean',
        ]);

        $filiere->update($request->all());

        return redirect()->back()->with('success', 'Filière mise à jour avec succès.');
    }

    public function destroy(Filiere $filiere)
    {
        // Vérifier si la filière est utilisée par des élèves
        if ($filiere->eleves()->count() > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette filière car des élèves y sont inscrits.');
        }

        $filiere->delete();

        return redirect()->back()->with('success', 'Filière supprimée avec succès.');
    }

    public function show(Filiere $filiere)
    {
        $filiere->load(['niveau', 'eleves', 'matieres']);

        return Inertia::render('Filieres/Show', [
            'filiere' => $filiere,
        ]);
    }

    public function stats()
    {
        $stats = [
            'total' => Filiere::count(),
            'actives' => Filiere::where('is_active', true)->count(),
            'avec_eleves' => Filiere::has('eleves')->count(),
            'avec_matieres' => Filiere::has('matieres')->count(),
        ];

        return response()->json($stats);
    }
}
