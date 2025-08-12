<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FiliereController extends Controller
{
    /**
     * Afficher la liste des filières
     */
    public function index()
    {
        $this->authorize('viewAny', Filiere::class);
        
        $filieres = Filiere::with('niveau')
            ->latest()
            ->get()
            ->map(function ($filiere) {
                return [
                    'id' => $filiere->id,
                    'intitule' => $filiere->Intitule,
                    'niveau' => $filiere->niveau ? $filiere->niveau->Nom : 'Non défini',
                    'niveau_id' => $filiere->IdNiv,
                    'created_at' => $filiere->created_at ? $filiere->created_at->format('d/m/Y H:i') : null,
                    'updated_at' => $filiere->updated_at ? $filiere->updated_at->format('d/m/Y H:i') : null,
                ];
            });

        return Inertia::render('Filieres/Index', [
            'filieres' => $filieres,
            'niveaux' => Niveau::all(['id', 'Nom as nom']),
            'can' => [
                'create' => Auth::user()->can('create', Filiere::class),
                'edit' => Auth::user()->can('update', Filiere::class),
                'delete' => Auth::user()->can('delete', Filiere::class),
            ]
        ]);
    }

    /**
     * Afficher le formulaire de création d'une filière
     */
    public function create()
    {
        $this->authorize('create', Filiere::class);
        
        return Inertia::render('Filieres/Create', [
            'niveaux' => Niveau::all(['id', 'Nom as nom']),
        ]);
    }

    /**
     * Enregistrer une nouvelle filière
     */
    public function store(Request $request)
    {
        $this->authorize('create', Filiere::class);
        
        $validated = $request->validate([
            'intitule' => 'required|string|max:255',
            'niveau_id' => 'required|exists:Niveau,id',
        ]);

        Filiere::create([
            'Intitule' => $validated['intitule'],
            'IdNiv' => $validated['niveau_id'],
        ]);

        return redirect()->route('filieres.index')
            ->with('success', 'Filière créée avec succès.');
    }

    /**
     * Afficher le formulaire d'édition d'une filière
     */
    public function edit(Filiere $filiere)
    {
        $this->authorize('update', $filiere);
        
        return Inertia::render('Filieres/Edit', [
            'filiere' => [
                'id' => $filiere->id,
                'intitule' => $filiere->Intitule,
                'niveau_id' => $filiere->IdNiv,
                'created_at' => $filiere->created_at ? $filiere->created_at->format('d/m/Y H:i') : null,
                'updated_at' => $filiere->updated_at ? $filiere->updated_at->format('d/m/Y H:i') : null,
            ],
            'niveaux' => Niveau::all(['id', 'Nom as nom']),
        ]);
    }

    /**
     * Mettre à jour une filière existante
     */
    public function update(Request $request, Filiere $filiere)
    {
        $this->authorize('update', $filiere);
        
        $validated = $request->validate([
            'intitule' => 'required|string|max:255',
            'niveau_id' => 'required|exists:Niveau,id',
        ]);

        $filiere->update([
            'Intitule' => $validated['intitule'],
            'IdNiv' => $validated['niveau_id'],
        ]);

        return redirect()->route('filieres.index')
            ->with('success', 'Filière mise à jour avec succès.');
    }

    /**
     * Supprimer une filière
     */
    public function destroy(Filiere $filiere)
    {
        $this->authorize('delete', $filiere);
        
        // Vérifier s'il y a des étudiants dans cette filière
        if ($filiere->etudiants()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer cette filière car des étudiants y sont inscrits.');
        }

        $filiere->delete();

        return redirect()->route('filieres.index')
            ->with('success', 'Filière supprimée avec succès.');
    }

    /**
     * Récupérer les filières par niveau
     */
    public function byNiveau(Niveau $niveau)
    {
        return response()->json(
            $niveau->filieres()->select('id', 'Intitule as nom')->get()
        );
    }
}
