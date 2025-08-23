<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NiveauController extends Controller
{
    public function index()
    {
        $niveaux = Niveau::withCount(['eleves', 'filieres'])
            ->orderBy('nom')
            ->get();

        return Inertia::render('Niveaux/Index', [
            'niveaux' => $niveaux,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:niveaux,nom',
            'code' => 'nullable|string|max:50|unique:niveaux,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Niveau::create($request->all());

        return redirect()->back()->with('success', 'Niveau créé avec succès.');
    }

    public function update(Request $request, Niveau $niveau)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:niveaux,nom,' . $niveau->id,
            'code' => 'nullable|string|max:50|unique:niveaux,code,' . $niveau->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $niveau->update($request->all());

        return redirect()->back()->with('success', 'Niveau mis à jour avec succès.');
    }

    public function destroy(Niveau $niveau)
    {
        // Vérifier si le niveau est utilisé par des élèves
        if ($niveau->eleves()->count() > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer ce niveau car des élèves y sont inscrits.');
        }

        // Vérifier si le niveau est utilisé par des filières
        if ($niveau->filieres()->count() > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer ce niveau car des filières y sont associées.');
        }

        $niveau->delete();

        return redirect()->back()->with('success', 'Niveau supprimé avec succès.');
    }

    public function show(Niveau $niveau)
    {
        $niveau->load(['eleves', 'filieres']);

        return Inertia::render('Niveaux/Show', [
            'niveau' => $niveau,
        ]);
    }

    public function stats()
    {
        $stats = [
            'total' => Niveau::count(),
            'actifs' => Niveau::where('is_active', true)->count(),
            'avec_eleves' => Niveau::has('eleves')->count(),
            'avec_filieres' => Niveau::has('filieres')->count(),
        ];

        return response()->json($stats);
    }
}
