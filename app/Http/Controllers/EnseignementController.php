<?php

namespace App\Http\Controllers;

use App\Models\Enseignement;
use App\Models\Matiere;
use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EnseignementController extends Controller
{
    public function index()
    {
        $enseignements = Enseignement::with(['matiere', 'professeur', 'etudiant', 'niveau'])
            ->orderBy('date_debut', 'desc')
            ->get();

        $matieres = Matiere::where('is_active', true)->orderBy('nom')->get();
        $professeurs = User::where('role', RoleType::PROFESSEUR)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $etudiants = User::where('role', RoleType::ELEVE)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return Inertia::render('Enseignements/Index', [
            'enseignements' => $enseignements,
            'matieres' => $matieres,
            'professeurs' => $professeurs,
            'etudiants' => $etudiants,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'matiere_id' => 'required|exists:matieres,id',
            'professeur_id' => 'required|exists:users,id',
            'etudiant_id' => 'required|exists:users,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'prix' => 'required|numeric|min:0',
            'statut' => 'required|in:actif,termine,suspendu',
        ]);

        Enseignement::create($request->all());

        return redirect()->back()->with('success', 'Enseignement créé avec succès.');
    }

    public function update(Request $request, Enseignement $enseignement)
    {
        $request->validate([
            'matiere_id' => 'required|exists:matieres,id',
            'professeur_id' => 'required|exists:users,id',
            'etudiant_id' => 'required|exists:users,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'prix' => 'required|numeric|min:0',
            'statut' => 'required|in:actif,termine,suspendu',
        ]);

        $enseignement->update($request->all());

        return redirect()->back()->with('success', 'Enseignement mis à jour avec succès.');
    }

    public function destroy(Enseignement $enseignement)
    {
        $enseignement->delete();

        return redirect()->back()->with('success', 'Enseignement supprimé avec succès.');
    }

    public function show(Enseignement $enseignement)
    {
        $enseignement->load(['matiere', 'professeur', 'etudiant', 'niveau']);

        return Inertia::render('Enseignements/Show', [
            'enseignement' => $enseignement,
        ]);
    }

    public function stats()
    {
        $stats = [
            'total' => Enseignement::count(),
            'actifs' => Enseignement::where('statut', 'actif')->count(),
            'termines' => Enseignement::where('statut', 'termine')->count(),
            'suspendus' => Enseignement::where('statut', 'suspendu')->count(),
            'revenus_totaux' => Enseignement::sum('prix'),
            'revenus_mois' => Enseignement::whereMonth('date_debut', now()->month)
                ->whereYear('date_debut', now()->year)
                ->sum('prix'),
        ];

        return response()->json($stats);
    }
}
