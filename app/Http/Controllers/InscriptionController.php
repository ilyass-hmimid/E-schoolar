<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\User;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Models\Pack;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InscriptionController extends Controller
{
    /**
     * Affiche la liste des inscriptions
     */
    public function index()
    {
        $inscriptions = Inscription::with(['etudiant', 'niveau', 'filiere', 'pack'])
            ->latest()
            ->get()
            ->map(function($inscription) {
                return [
                    'id' => $inscription->id,
                    'etudiant' => $inscription->etudiant->name,
                    'niveau' => $inscription->niveau->nom,
                    'filiere' => $inscription->filiere->nom,
                    'pack' => $inscription->pack->nom,
                    'date_inscription' => $inscription->date_inscription->format('d/m/Y'),
                    'montant_total' => $inscription->montant_total,
                    'statut' => $inscription->statut,
                    'created_at' => $inscription->created_at->format('d/m/Y'),
                ];
            });

        return Inertia::render('Inscriptions/Index', [
            'inscriptions' => $inscriptions,
            'statistiques' => [
                'total' => Inscription::count(),
                'validees' => Inscription::where('statut', 'validee')->count(),
                'en_attente' => Inscription::where('statut', 'en_attente')->count(),
                'annulees' => Inscription::where('statut', 'annulee')->count(),
            ]
        ]);
    }

    /**
     * Affiche le formulaire de création d'une inscription
     */
    public function create()
    {
        $etudiants = User::role(RoleType::ELEVE->value)
            ->select('id', 'name', 'email')
            ->get();

        return Inertia::render('Inscriptions/Create', [
            'etudiants' => $etudiants,
            'niveaux' => Niveau::select('id', 'nom')->get(),
            'filieres' => Filiere::select('id', 'nom')->get(),
            'packs' => Pack::select('id', 'nom', 'prix')->get(),
        ]);
    }

    /**
     * Enregistre une nouvelle inscription
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:users,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'required|exists:filieres,id',
            'pack_id' => 'required|exists:packs,id',
            'date_inscription' => 'required|date',
            'montant_total' => 'required|numeric|min:0',
            'statut' => 'required|in:en_attente,validee,annulee',
            'commentaire' => 'nullable|string|max:500',
        ]);

        // Démarrer une transaction
        DB::beginTransaction();

        try {
            // Créer l'inscription
            $inscription = Inscription::create($validated);

            // Mettre à jour l'étudiant avec le niveau et la filière
            $etudiant = User::findOrFail($validated['etudiant_id']);
            $etudiant->update([
                'niveau_id' => $validated['niveau_id'],
                'filiere_id' => $validated['filiere_id'],
                'date_inscription' => $validated['date_inscription'],
            ]);

            // Valider la transaction
            DB::commit();

            return redirect()->route('inscriptions.index')
                ->with('success', 'Inscription créée avec succès');

        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la création de l\'inscription');
        }
    }

    /**
     * Affiche les détails d'une inscription
     */
    public function show(Inscription $inscription)
    {
        $inscription->load(['etudiant', 'niveau', 'filiere', 'pack']);
        
        return Inertia::render('Inscriptions/Show', [
            'inscription' => [
                'id' => $inscription->id,
                'etudiant' => $inscription->etudiant->name,
                'niveau' => $inscription->niveau->nom,
                'filiere' => $inscription->filiere->nom,
                'pack' => $inscription->pack->nom,
                'date_inscription' => $inscription->date_inscription->format('Y-m-d'),
                'montant_total' => $inscription->montant_total,
                'statut' => $inscription->statut,
                'commentaire' => $inscription->commentaire,
                'created_at' => $inscription->created_at->format('d/m/Y H:i'),
            ]
        ]);
    }

    /**
     * Affiche le formulaire de modification d'une inscription
     */
    public function edit(Inscription $inscription)
    {
        $etudiants = User::role(RoleType::ELEVE->value)
            ->select('id', 'name', 'email')
            ->get();

        return Inertia::render('Inscriptions/Edit', [
            'inscription' => [
                'id' => $inscription->id,
                'etudiant_id' => $inscription->etudiant_id,
                'niveau_id' => $inscription->niveau_id,
                'filiere_id' => $inscription->filiere_id,
                'pack_id' => $inscription->pack_id,
                'date_inscription' => $inscription->date_inscription->format('Y-m-d'),
                'montant_total' => $inscription->montant_total,
                'statut' => $inscription->statut,
                'commentaire' => $inscription->commentaire,
            ],
            'etudiants' => $etudiants,
            'niveaux' => Niveau::select('id', 'nom')->get(),
            'filieres' => Filiere::select('id', 'nom')->get(),
            'packs' => Pack::select('id', 'nom', 'prix')->get(),
        ]);
    }

    /**
     * Met à jour une inscription
     */
    public function update(Request $request, Inscription $inscription)
    {
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:users,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'required|exists:filieres,id',
            'pack_id' => 'required|exists:packs,id',
            'date_inscription' => 'required|date',
            'montant_total' => 'required|numeric|min:0',
            'statut' => 'required|in:en_attente,validee,annulee',
            'commentaire' => 'nullable|string|max:500',
        ]);

        // Démarrer une transaction
        DB::beginTransaction();

        try {
            // Mettre à jour l'inscription
            $inscription->update($validated);

            // Mettre à jour l'étudiant avec le niveau et la filière
            $etudiant = User::findOrFail($validated['etudiant_id']);
            $etudiant->update([
                'niveau_id' => $validated['niveau_id'],
                'filiere_id' => $validated['filiere_id'],
                'date_inscription' => $validated['date_inscription'],
            ]);

            // Valider la transaction
            DB::commit();

            return redirect()->route('inscriptions.index')
                ->with('success', 'Inscription mise à jour avec succès');

        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour de l\'inscription');
        }
    }

    /**
     * Supprime une inscription
     */
    public function destroy(Inscription $inscription)
    {
        try {
            $inscription->delete();
            return redirect()->route('inscriptions.index')
                ->with('success', 'Inscription supprimée avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la suppression de l\'inscription');
        }
    }

    /**
     * Valide une inscription
     */
    public function valider(Inscription $inscription)
    {
        try {
            $inscription->update(['statut' => 'validee']);
            return back()->with('success', 'Inscription validée avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la validation de l\'inscription');
        }
    }

    /**
     * Annule une inscription
     */
    public function annuler(Inscription $inscription)
    {
        try {
            $inscription->update(['statut' => 'annulee']);
            return back()->with('success', 'Inscription annulée avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de l\'annulation de l\'inscription');
        }
    }
}
