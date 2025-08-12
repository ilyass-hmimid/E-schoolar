<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Paiment;
use App\Models\Valeurs_Paiments;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PaimentController extends Controller
{
    /**
     * Afficher la liste des paiements
     */
    public function index()
    {
        $this->authorize('viewAny', Paiment::class);
        
        $paiments = Paiment::with(['etudiant' => function($query) {
                $query->select('id', 'Nom', 'Prenom');
            }])
            ->latest()
            ->get()
            ->map(function ($paiment) {
                return [
                    'id' => $paiment->id,
                    'etudiant_id' => $paiment->IdEtu,
                    'etudiant_nom' => $paiment->etudiant ? $paiment->etudiant->Nom . ' ' . $paiment->etudiant->Prenom : 'Inconnu',
                    'montant' => number_format($paiment->Montant, 2, ',', ' '),
                    'somme_payee' => number_format($paiment->SommeApaye, 2, ',', ' '),
                    'reste' => number_format($paiment->Reste, 2, ',', ' '),
                    'etat' => $paiment->Etat,
                    'date_paiment' => $paiment->Date_Paiment ? Carbon::parse($paiment->Date_Paiment)->format('d/m/Y') : null,
                    'created_at' => $paiment->created_at ? $paiment->created_at->format('d/m/Y H:i') : null,
                ];
            });

        return Inertia::render('Paiements/Index', [
            'paiements' => $paiments,
            'can' => [
                'create' => Auth::user()->can('create', Paiment::class),
                'edit' => Auth::user()->can('update', Paiment::class),
                'delete' => Auth::user()->can('delete', Paiment::class),
            ]
        ]);
    }

    /**
     * Afficher le formulaire de création d'un paiement
     */
    public function create()
    {
        $this->authorize('create', Paiment::class);
        
        return Inertia::render('Paiements/Create', [
            'etudiants' => Etudiant::select('id', 'Nom', 'Prenom')
                ->orderBy('Nom')
                ->get()
                ->map(function($etudiant) {
                    return [
                        'id' => $etudiant->id,
                        'nom_complet' => $etudiant->Nom . ' ' . $etudiant->Prenom,
                    ];
                }),
            'valeurs_paiements' => Valeurs_Paiments::all()
                ->map(function($valeur) {
                    return [
                        'id' => $valeur->id,
                        'libelle' => $valeur->Libelle,
                        'montant' => $valeur->Valeur,
                    ];
                }),
        ]);
    }

    /**
     * Enregistrer un nouveau paiement
     */
    public function store(Request $request)
    {
        $this->authorize('create', Paiment::class);
        
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:Etudiant,id',
            'valeur_paiment_id' => 'required|exists:Valeurs_Paiments,id',
            'montant_paye' => 'required|numeric|min:0',
            'date_paiment' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $valeurPaiment = Valeurs_Paiments::findOrFail($validated['valeur_paiment_id']);
        $montantTotal = $valeurPaiment->Valeur;
        $montantPaye = (float)$validated['montant_paye'];
        $reste = max(0, $montantTotal - $montantPaye);
        $etat = $reste <= 0 ? 'payé' : 'partiel';

        $paiment = Paiment::create([
            'IdEtu' => $validated['etudiant_id'],
            'Montant' => $montantTotal,
            'SommeApaye' => $montantPaye,
            'Reste' => $reste,
            'Etat' => $etat,
            'Date_Paiment' => $validated['date_paiment'],
            'Notes' => $validated['notes'] ?? null,
        ]);

        // Mettre à jour le solde de l'étudiant
        $etudiant = Etudiant::find($validated['etudiant_id']);
        if ($etudiant) {
            $etudiant->SommeApaye += $montantPaye;
            $etudiant->save();
        }

        return redirect()->route('paiements.index')
            ->with('success', 'Paiement enregistré avec succès.');
    }

    /**
     * Afficher les détails d'un paiement
     */
    public function show(Paiment $paiment)
    {
        $this->authorize('view', $paiment);
        
        $paiment->load(['etudiant' => function($query) {
            $query->select('id', 'Nom', 'Prenom');
        }]);

        return Inertia::render('Paiements/Show', [
            'paiment' => [
                'id' => $paiment->id,
                'etudiant_id' => $paiment->IdEtu,
                'etudiant_nom' => $paiment->etudiant ? $paiment->etudiant->Nom . ' ' . $paiment->etudiant->Prenom : 'Inconnu',
                'montant' => number_format($paiment->Montant, 2, ',', ' '),
                'somme_payee' => number_format($paiment->SommeApaye, 2, ',', ' '),
                'reste' => number_format($paiment->Reste, 2, ',', ' '),
                'etat' => $paiment->Etat,
                'date_paiment' => $paiment->Date_Paiment ? Carbon::parse($paiment->Date_Paiment)->format('d/m/Y') : null,
                'notes' => $paiment->Notes,
                'created_at' => $paiment->created_at ? $paiment->created_at->format('d/m/Y H:i') : null,
                'updated_at' => $paiment->updated_at ? $paiment->updated_at->format('d/m/Y H:i') : null,
            ]
        ]);
    }

    /**
     * Afficher le formulaire d'édition d'un paiement
     */
    public function edit(Paiment $paiment)
    {
        $this->authorize('update', $paiment);
        
        $paiment->load(['etudiant' => function($query) {
            $query->select('id', 'Nom', 'Prenom');
        }]);

        return Inertia::render('Paiements/Edit', [
            'paiment' => [
                'id' => $paiment->id,
                'etudiant_id' => $paiment->IdEtu,
                'etudiant_nom' => $paiment->etudiant ? $paiment->etudiant->Nom . ' ' . $paiment->etudiant->Prenom : 'Inconnu',
                'montant' => $paiment->Montant,
                'montant_paye' => $paiment->SommeApaye,
                'date_paiment' => $paiment->Date_Paiment,
                'notes' => $paiment->Notes,
            ],
            'etudiants' => Etudiant::select('id', 'Nom', 'Prenom')
                ->orderBy('Nom')
                ->get()
                ->map(function($etudiant) {
                    return [
                        'id' => $etudiant->id,
                        'nom_complet' => $etudiant->Nom . ' ' . $etudiant->Prenom,
                    ];
                }),
        ]);
    }

    /**
     * Mettre à jour un paiement existant
     */
    public function update(Request $request, Paiment $paiment)
    {
        $this->authorize('update', $paiment);
        
        $validated = $request->validate([
            'montant' => 'required|numeric|min:0',
            'montant_paye' => 'required|numeric|min:0|max:' . $paiment->Montant,
            'date_paiment' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $ancienMontantPaye = $paiment->SommeApaye;
        $nouveauMontantPaye = (float)$validated['montant_paye'];
        $difference = $nouveauMontantPaye - $ancienMontantPaye;
        
        $reste = max(0, $paiment->Montant - $nouveauMontantPaye);
        $etat = $reste <= 0 ? 'payé' : 'partiel';

        // Mettre à jour le paiement
        $paiment->update([
            'SommeApaye' => $nouveauMontantPaye,
            'Reste' => $reste,
            'Etat' => $etat,
            'Date_Paiment' => $validated['date_paiment'],
            'Notes' => $validated['notes'] ?? null,
        ]);

        // Mettre à jour le solde de l'étudiant
        $etudiant = Etudiant::find($paiment->IdEtu);
        if ($etudiant) {
            $etudiant->SommeApaye += $difference;
            $etudiant->save();
        }

        return redirect()->route('paiements.show', $paiment)
            ->with('success', 'Paiement mis à jour avec succès.');
    }

    /**
     * Supprimer un paiement
     */
    public function destroy(Paiment $paiment)
    {
        $this->authorize('delete', $paiment);
        
        // Mettre à jour le solde de l'étudiant avant de supprimer le paiement
        $etudiant = Etudiant::find($paiment->IdEtu);
        if ($etudiant) {
            $etudiant->SommeApaye -= $paiment->SommeApaye;
            $etudiant->save();
        }

        $paiment->delete();

        return redirect()->route('paiements.index')
            ->with('success', 'Paiement supprimé avec succès.');
    }

    /**
     * Obtenir l'historique des paiements d'un étudiant
     */
    public function historiqueEtudiant(Etudiant $etudiant)
    {
        $this->authorize('view', $etudiant);
        
        return response()->json(
            $etudiant->paiements()
                ->select('id', 'Montant', 'SommeApaye', 'Reste', 'Etat', 'Date_Paiment', 'created_at')
                ->orderBy('Date_Paiment', 'desc')
                ->get()
                ->map(function($paiment) {
                    return [
                        'id' => $paiment->id,
                        'montant' => number_format($paiment->Montant, 2, ',', ' '),
                        'somme_payee' => number_format($paiment->SommeApaye, 2, ',', ' '),
                        'reste' => number_format($paiment->Reste, 2, ',', ' '),
                        'etat' => $paiment->Etat,
                        'date_paiment' => $paiment->Date_Paiment ? Carbon::parse($paiment->Date_Paiment)->format('d/m/Y') : null,
                        'date_creation' => $paiment->created_at ? $paiment->created_at->format('d/m/Y H:i') : null,
                    ];
                })
        );
    }
}
