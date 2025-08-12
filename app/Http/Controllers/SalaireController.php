<?php

namespace App\Http\Controllers;

use App\Models\Professeur;
use App\Models\Salaires;
use App\Models\Valeurs_Salaires;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SalaireController extends Controller
{
    /**
     * Afficher la liste des salaires
     */
    public function index()
    {
        $this->authorize('viewAny', Salaires::class);
        
        $salaires = Salaires::with(['professeur' => function($query) {
                $query->select('id', 'Nom', 'Prenom');
            }])
            ->latest()
            ->get()
            ->map(function ($salaire) {
                return [
                    'id' => $salaire->id,
                    'professeur_id' => $salaire->IdProf,
                    'professeur_nom' => $salaire->professeur ? $salaire->professeur->Nom . ' ' . $salaire->professeur->Prenom : 'Inconnu',
                    'montant' => number_format($salaire->Montant, 2, ',', ' '),
                    'montant_actuel' => number_format($salaire->Montant_actuel, 2, ',', ' '),
                    'reste' => number_format($salaire->Reste, 2, ',', ' '),
                    'pourcentage' => $salaire->Pourcentage . '%',
                    'etat' => $salaire->Etat,
                    'date_salaire' => $salaire->Date_Salaire ? Carbon::parse($salaire->Date_Salaire)->format('d/m/Y') : null,
                    'created_at' => $salaire->created_at ? $salaire->created_at->format('d/m/Y H:i') : null,
                ];
            });

        return Inertia::render('Salaires/Index', [
            'salaires' => $salaires,
            'can' => [
                'create' => Auth::user()->can('create', Salaires::class),
                'edit' => Auth::user()->can('update', Salaires::class),
                'delete' => Auth::user()->can('delete', Salaires::class),
            ]
        ]);
    }

    /**
     * Afficher le formulaire de création d'un salaire
     */
    public function create()
    {
        $this->authorize('create', Salaires::class);
        
        return Inertia::render('Salaires/Create', [
            'professeurs' => Professeur::select('id', 'Nom', 'Prenom')
                ->orderBy('Nom')
                ->get()
                ->map(function($professeur) {
                    return [
                        'id' => $professeur->id,
                        'nom_complet' => $professeur->Nom . ' ' . $professeur->Prenom,
                    ];
                }),
            'valeurs_salaires' => Valeurs_Salaires::all()
                ->map(function($valeur) {
                    return [
                        'id' => $valeur->id,
                        'libelle' => $valeur->Libelle,
                        'pourcentage' => $valeur->Pourcentage,
                    ];
                }),
        ]);
    }

    /**
     * Enregistrer un nouveau salaire
     */
    public function store(Request $request)
    {
        $this->authorize('create', Salaires::class);
        
        $validated = $request->validate([
            'professeur_id' => 'required|exists:Professeur,id',
            'valeur_salaire_id' => 'required|exists:Valeurs_Salaires,id',
            'montant_total' => 'required|numeric|min:0',
            'montant_verse' => 'required|numeric|min:0|max:' . $request->input('montant_total'),
            'date_salaire' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $valeurSalaire = Valeurs_Salaires::findOrFail($validated['valeur_salaire_id']);
        $montantTotal = (float)$validated['montant_total'];
        $montantVerse = (float)$validated['montant_verse'];
        $reste = max(0, $montantTotal - $montantVerse);
        $etat = $reste <= 0 ? 'payé' : 'partiel';
        $pourcentage = $valeurSalaire->Pourcentage;

        $salaire = Salaires::create([
            'IdProf' => $validated['professeur_id'],
            'Montant' => $montantTotal,
            'Montant_actuel' => $montantVerse,
            'Reste' => $reste,
            'Pourcentage' => $pourcentage,
            'Etat' => $etat,
            'Date_Salaire' => $validated['date_salaire'],
            'Notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('salaires.index')
            ->with('success', 'Salaire enregistré avec succès.');
    }

    /**
     * Afficher les détails d'un salaire
     */
    public function show(Salaires $salaire)
    {
        $this->authorize('view', $salaire);
        
        $salaire->load(['professeur' => function($query) {
            $query->select('id', 'Nom', 'Prenom');
        }]);

        return Inertia::render('Salaires/Show', [
            'salaire' => [
                'id' => $salaire->id,
                'professeur_id' => $salaire->IdProf,
                'professeur_nom' => $salaire->professeur ? $salaire->professeur->Nom . ' ' . $salaire->professeur->Prenom : 'Inconnu',
                'montant' => number_format($salaire->Montant, 2, ',', ' '),
                'montant_actuel' => number_format($salaire->Montant_actuel, 2, ',', ' '),
                'reste' => number_format($salaire->Reste, 2, ',', ' '),
                'pourcentage' => $salaire->Pourcentage . '%',
                'etat' => $salaire->Etat,
                'date_salaire' => $salaire->Date_Salaire ? Carbon::parse($salaire->Date_Salaire)->format('d/m/Y') : null,
                'notes' => $salaire->Notes,
                'created_at' => $salaire->created_at ? $salaire->created_at->format('d/m/Y H:i') : null,
                'updated_at' => $salaire->updated_at ? $salaire->updated_at->format('d/m/Y H:i') : null,
            ]
        ]);
    }

    /**
     * Afficher le formulaire d'édition d'un salaire
     */
    public function edit(Salaires $salaire)
    {
        $this->authorize('update', $salaire);
        
        $salaire->load(['professeur' => function($query) {
            $query->select('id', 'Nom', 'Prenom');
        }]);

        return Inertia::render('Salaires/Edit', [
            'salaire' => [
                'id' => $salaire->id,
                'professeur_id' => $salaire->IdProf,
                'professeur_nom' => $salaire->professeur ? $salaire->professeur->Nom . ' ' . $salaire->professeur->Prenom : 'Inconnu',
                'montant_total' => $salaire->Montant,
                'montant_verse' => $salaire->Montant_actuel,
                'pourcentage' => $salaire->Pourcentage,
                'date_salaire' => $salaire->Date_Salaire,
                'notes' => $salaire->Notes,
            ],
            'professeurs' => Professeur::select('id', 'Nom', 'Prenom')
                ->orderBy('Nom')
                ->get()
                ->map(function($professeur) {
                    return [
                        'id' => $professeur->id,
                        'nom_complet' => $professeur->Nom . ' ' . $professeur->Prenom,
                    ];
                }),
            'valeurs_salaires' => Valeurs_Salaires::all()
                ->map(function($valeur) {
                    return [
                        'id' => $valeur->id,
                        'libelle' => $valeur->Libelle,
                        'pourcentage' => $valeur->Pourcentage,
                    ];
                }),
        ]);
    }

    /**
     * Mettre à jour un salaire existant
     */
    public function update(Request $request, Salaires $salaire)
    {
        $this->authorize('update', $salaire);
        
        $validated = $request->validate([
            'montant_total' => 'required|numeric|min:0',
            'montant_verse' => 'required|numeric|min:0|max:' . $request->input('montant_total'),
            'pourcentage' => 'required|numeric|min:0|max:100',
            'date_salaire' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $montantTotal = (float)$validated['montant_total'];
        $montantVerse = (float)$validated['montant_verse'];
        $reste = max(0, $montantTotal - $montantVerse);
        $etat = $reste <= 0 ? 'payé' : 'partiel';
        $pourcentage = (float)$validated['pourcentage'];

        // Mettre à jour le salaire
        $salaire->update([
            'Montant' => $montantTotal,
            'Montant_actuel' => $montantVerse,
            'Reste' => $reste,
            'Pourcentage' => $pourcentage,
            'Etat' => $etat,
            'Date_Salaire' => $validated['date_salaire'],
            'Notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('salaires.show', $salaire)
            ->with('success', 'Salaire mis à jour avec succès.');
    }

    /**
     * Supprimer un salaire
     */
    public function destroy(Salaires $salaire)
    {
        $this->authorize('delete', $salaire);
        
        $salaire->delete();

        return redirect()->route('salaires.index')
            ->with('success', 'Salaire supprimé avec succès.');
    }

    /**
     * Obtenir l'historique des salaires d'un professeur
     */
    public function historiqueProfesseur(Professeur $professeur)
    {
        $this->authorize('view', $professeur);
        
        return response()->json(
            $professeur->salaires()
                ->select('id', 'Montant', 'Montant_actuel', 'Reste', 'Pourcentage', 'Etat', 'Date_Salaire', 'created_at')
                ->orderBy('Date_Salaire', 'desc')
                ->get()
                ->map(function($salaire) {
                    return [
                        'id' => $salaire->id,
                        'montant' => number_format($salaire->Montant, 2, ',', ' '),
                        'montant_verse' => number_format($salaire->Montant_actuel, 2, ',', ' '),
                        'reste' => number_format($salaire->Reste, 2, ',', ' '),
                        'pourcentage' => $salaire->Pourcentage . '%',
                        'etat' => $salaire->Etat,
                        'date_salaire' => $salaire->Date_Salaire ? Carbon::parse($salaire->Date_Salaire)->format('d/m/Y') : null,
                        'date_creation' => $salaire->created_at ? $salaire->created_at->format('d/m/Y H:i') : null,
                    ];
                })
        );
    }
}
