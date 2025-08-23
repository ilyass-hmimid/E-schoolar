<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MatiereController extends Controller
{
    public function index()
    {
        $matieres = Matiere::with(['professeur'])
            ->withCount('enseignements')
            ->orderBy('nom')
            ->get();

        $professeurs = User::where('role', RoleType::PROFESSEUR)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return Inertia::render('Matieres/Index', [
            'matieres' => $matieres,
            'professeurs' => $professeurs,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:matieres,nom',
            'description' => 'nullable|string',
            'professeur_id' => 'nullable|exists:users,id',
            'prix_mensuel' => 'required|numeric|min:0',
            'commission_prof' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        Matiere::create($request->all());

        return redirect()->back()->with('success', 'Matière créée avec succès.');
    }

    public function update(Request $request, Matiere $matiere)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:matieres,nom,' . $matiere->id,
            'description' => 'nullable|string',
            'professeur_id' => 'nullable|exists:users,id',
            'prix_mensuel' => 'required|numeric|min:0',
            'commission_prof' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $matiere->update($request->all());

        return redirect()->back()->with('success', 'Matière mise à jour avec succès.');
    }

    public function destroy(Matiere $matiere)
    {
        // Vérifier si la matière est utilisée dans des enseignements
        if ($matiere->enseignements()->count() > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette matière car elle est utilisée dans des enseignements.');
        }

        $matiere->delete();

        return redirect()->back()->with('success', 'Matière supprimée avec succès.');
    }

    public function show(Matiere $matiere)
    {
        $matiere->load(['professeur', 'enseignements.etudiant', 'enseignements.niveau']);

        return Inertia::render('Matieres/Show', [
            'matiere' => $matiere,
        ]);
    }

    public function stats()
    {
        $stats = [
            'total' => Matiere::count(),
            'actives' => Matiere::where('is_active', true)->count(),
            'prix_moyen' => Matiere::avg('prix_mensuel'),
            'commission_moyenne' => Matiere::avg('commission_prof'),
            'avec_professeur' => Matiere::whereNotNull('professeur_id')->count(),
        ];

        return response()->json($stats);
    }
}
