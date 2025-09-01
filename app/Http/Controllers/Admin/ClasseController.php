<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Niveau;
use App\Models\Professeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClasseController extends Controller
{
    /**
     * Afficher la liste des classes
     */
    public function index()
    {
        $classes = Classe::withCount('eleves')
            ->with(['niveau', 'professeurPrincipal.user'])
            ->latest()
            ->paginate(10);

        return view('admin.pedagogie.classes.index', compact('classes'));
    }

    /**
     * Afficher le formulaire de création d'une classe
     */
    public function create()
    {
        $niveaux = Niveau::active()->get();
        $professeurs = Professeur::with('user')->get();
        $salles = $this->getSallesDisponibles();

        return view('admin.pedagogie.classes.create', compact('niveaux', 'professeurs', 'salles'));
    }

    /**
     * Enregistrer une nouvelle classe
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'code' => [
                'required',
                'string',
                'max:20',
                'unique:classes,code',
                'regex:/^[A-Z0-9]+$/'
            ],
            'niveau_id' => 'required|exists:niveaux,id',
            'professeur_principal_id' => 'nullable|exists:professeurs,id',
            'salle' => 'nullable|string|max:50',
            'annee_scolaire' => [
                'required',
                'string',
                'max:9',
                'regex:/^\d{4}-\d{4}$/'
            ],
            'capacite_max' => 'nullable|integer|min:1|max:50',
            'description' => 'nullable|string|max:500',
            'est_active' => 'boolean',
            'generer_emploi_du_temps' => 'boolean',
            'notifier_professeurs' => 'boolean',
        ], [
            'code.regex' => 'Le code ne doit contenir que des lettres majuscules et des chiffres.',
            'annee_scolaire.regex' => 'Le format de l\'année scolaire est invalide. Format attendu: AAAA-AAAA',
            'capacite_max.max' => 'La capacité maximale ne peut pas dépasser 50 élèves.'
        ]);

        try {
            DB::beginTransaction();

            $classe = Classe::create([
                'nom' => $validated['nom'],
                'code' => $validated['code'],
                'niveau_id' => $validated['niveau_id'],
                'professeur_principal_id' => $validated['professeur_principal_id'] ?? null,
                'salle' => $validated['salle'] ?? null,
                'annee_scolaire' => $validated['annee_scolaire'],
                'capacite_max' => $validated['capacite_max'] ?? 30,
                'description' => $validated['description'] ?? null,
                'est_active' => $validated['est_active'] ?? true,
            ]);

            // Générer un emploi du temps par défaut si demandé
            if ($request->boolean('generer_emploi_du_temps')) {
                $this->genererEmploiDuTemps($classe);
            }

            // Notifier les professeurs si demandé
            if ($request->boolean('notifier_professeurs') && $classe->professeurPrincipal) {
                // TODO: Implémenter la notification
            }

            DB::commit();

            return redirect()
                ->route('admin.classes.show', $classe->id)
                ->with('success', __('packs.classes.success.created'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création de la classe : ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', __('packs.classes.errors.create'));
        }
    }

    /**
     * Afficher les détails d'une classe
     */
    public function show(Classe $classe)
    {
        $this->authorize('view', $classe);
        
        $classe->load([
            'niveau',
            'professeurPrincipal.user',
            'eleves.user' => function($query) {
                $query->orderBy('nom')->orderBy('prenom');
            },
            'enseignants.user' => function($query) {
                $query->with('matiere');
            },
            'cours' => function($query) {
                $query->with(['matiere', 'professeur.user'])
                      ->orderBy('jour_semaine')
                      ->orderBy('heure_debut');
            },
            'documents' => function($query) {
                $query->latest()->limit(5);
            }
        ]);

        // Statistiques
        $stats = [
            'eleves_count' => $classe->eleves->count(),
            'cours_count' => $classe->cours->count(),
            'enseignants_count' => $classe->enseignants->count(),
            'occupation' => $classe->capacite_max > 0 
                ? round(($classe->eleves->count() / $classe->capacite_max) * 100, 1)
                : 0,
        ];

        return view('admin.pedagogie.classes.show', compact('classe', 'stats'));
    }

    /**
     * Afficher le formulaire d'édition d'une classe
     */
    public function edit(Classe $classe)
    {
        $niveaux = Niveau::active()->get();
        $professeurs = Professeur::with('user')->get();
        $salles = $this->getSallesDisponibles();

        return view('admin.pedagogie.classes.edit', compact('classe', 'niveaux', 'professeurs', 'salles'));
    }

    /**
     * Mettre à jour une classe existante
     */
    public function update(Request $request, Classe $classe)
    {
        $this->authorize('update', $classe);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9]+$/',
                Rule::unique('classes', 'code')->ignore($classe->id),
            ],
            'niveau_id' => 'required|exists:niveaux,id',
            'professeur_principal_id' => 'nullable|exists:professeurs,id',
            'salle' => 'nullable|string|max:50',
            'annee_scolaire' => [
                'required',
                'string',
                'max:9',
                'regex:/^\d{4}-\d{4}$/'
            ],
            'capacite_max' => 'nullable|integer|min:1|max:50',
            'description' => 'nullable|string|max:500',
            'est_active' => 'boolean',
            'notifier_professeurs' => 'boolean',
        ], [
            'code.regex' => 'Le code ne doit contenir que des lettres majuscules et des chiffres.',
            'annee_scolaire.regex' => 'Le format de l\'année scolaire est invalide. Format attendu: AAAA-AAAA',
            'capacite_max.max' => 'La capacité maximale ne peut pas dépasser 50 élèves.'
        ]);

        try {
            DB::beginTransaction();

            $ancienProfesseurId = $classe->professeur_principal_id;
            $nouveauProfesseurId = $validated['professeur_principal_id'] ?? null;
            
            $classe->update([
                'nom' => $validated['nom'],
                'code' => $validated['code'],
                'niveau_id' => $validated['niveau_id'],
                'professeur_principal_id' => $nouveauProfesseurId,
                'salle' => $validated['salle'] ?? null,
                'annee_scolaire' => $validated['annee_scolaire'],
                'capacite_max' => $validated['capacite_max'] ?? $classe->capacite_max,
                'description' => $validated['description'] ?? $classe->description,
                'est_active' => $validated['est_active'] ?? $classe->est_active,
            ]);

            // Notifier si le professeur principal a changé et que la notification est demandée
            if ($request->boolean('notifier_professeurs') && $ancienProfesseurId != $nouveauProfesseurId) {
                // TODO: Implémenter la notification
            }

            DB::commit();

            return redirect()
                ->route('admin.classes.show', $classe->id)
                ->with('success', __('packs.classes.success.updated'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la mise à jour de la classe : ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', __('packs.classes.errors.update'));
        }
    }

    /**
     * Supprimer une classe
     */
    public function destroy(Classe $classe)
    {
        $this->authorize('delete', $classe);
        
        try {
            // Vérifier s'il y a des élèves inscrits
            if ($classe->eleves()->exists()) {
                return back()
                    ->with('error', __('packs.classes.errors.has_eleves'));
            }

            // Vérifier s'il y a des cours associés
            if ($classe->cours()->exists()) {
                return back()
                    ->with('error', __('packs.classes.errors.has_cours'));
            }

            $classe->delete();

            return redirect()
                ->route('admin.classes.index')
                ->with('success', __('packs.classes.success.deleted'));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression de la classe : ' . $e->getMessage());
            
            return back()
                ->with('error', __('packs.classes.errors.delete'));
        }
    }

    /**
     * Archiver une classe (désactiver)
     */
    public function archive(Classe $classe)
    {
        $this->authorize('update', $classe);
        
        try {
            $classe->update(['est_active' => false]);
            
            return back()
                ->with('success', __('packs.classes.success.archived'));
                
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'archivage de la classe : ' . $e->getMessage());
            
            return back()
                ->with('error', __('packs.classes.errors.update'));
        }
    }

    /**
     * Récupérer la liste des salles disponibles
     */
    private function getSallesDisponibles()
    {
        // Récupérer les salles déjà utilisées
        $sallesOccupees = Classe::select('salle')
            ->whereNotNull('salle')
            ->distinct()
            ->pluck('salle')
            ->toArray();

        // Liste des salles par défaut (à adapter selon l'établissement)
        $toutesLesSalles = [
            'Salle 101', 'Salle 102', 'Salle 103', 'Salle 104', 'Salle 105',
            'Salle 201', 'Salle 202', 'Salle 203', 'Salle 204', 'Salle 205',
            'Salle 301', 'Salle 302', 'Salle 303', 'Salle 304', 'Salle 305',
            'Labo Physique', 'Labo Chimie', 'Labo Informatique', 'Salle de réunion', 'CDI'
        ];

        // Filtrer les salles déjà utilisées
        $sallesDisponibles = array_diff($toutesLesSalles, $sallesOccupees);
        sort($sallesDisponibles);

        return $sallesDisponibles;
    }

    /**
     * Générer un emploi du temps par défaut pour une classe
     */
    private function genererEmploiDuTemps(Classe $classe)
    {
        // TODO: Implémenter la génération d'un emploi du temps par défaut
        // Cette méthode devrait créer des créneaux horaires vides pour chaque jour de la semaine
        // en fonction des matières du niveau de la classe
        
        // Exemple de structure à implémenter :
        /*
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $creneaux = [
            ['08:00', '09:00'],
            ['09:00', '10:00'],
            // ... autres créneaux
        ];
        
        foreach ($jours as $jour) {
            foreach ($creneaux as $creneau) {
                $classe->creneaux()->create([
                    'jour' => $jour,
                    'heure_debut' => $creneau[0],
                    'heure_fin' => $creneau[1],
                    // autres champs nécessaires
                ]);
            }
        }
        */
    }
}
