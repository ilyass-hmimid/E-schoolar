<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Salaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ProfesseurController extends BaseAdminController
{
    /**
     * Affiche la liste des professeurs
     *
     * @return \Illuminate\View\View
     */
    /**
     * Affiche la liste des professeurs avec leurs statistiques
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $professeurs = User::where('role', 'professeur')
            ->with(['matieres' => function($query) {
                $query->where('est_active', true);
            }])
            ->withCount(['matieres' => function($query) {
                $query->where('est_active', true);
            }])
            ->latest()
            ->paginate(15);
            
        // Charger les statistiques pour chaque professeur
        foreach ($professeurs as $professeur) {
            $professeur->loadCount(['matieres' => function($query) {
                $query->where('est_active', true);
            }]);
            
            $professeur->salaire_mensuel = $professeur->getSalaireMensuelAttribute();
            $professeur->total_eleves = $professeur->matieres->sum('eleves_count');
        }
            
        return view('admin.professeurs.index', compact('professeurs'));
    }

    /**
     * Affiche le formulaire de création d'un professeur
     *
     * @return \Illuminate\View\View
     */
    /**
     * Affiche le formulaire de création d'un professeur
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Récupérer les matières actives groupées par niveau
        $matieresParNiveau = [
            'primaire' => [
                'label' => 'Primaire',
                'matieres' => Matiere::where('niveau', 'primaire')
                    ->where('est_active', true)
                    ->orderBy('nom')
                    ->get()
            ],
            'college' => [
                'label' => 'Collège',
                'matieres' => Matiere::where('niveau', 'college')
                    ->where('est_active', true)
                    ->orderBy('nom')
                    ->get()
            ],
            'lycee' => [
                'label' => 'Lycée',
                'matieres' => Matiere::where('niveau', 'lycee')
                    ->where('est_active', true)
                    ->orderBy('nom')
                    ->get()
            ]
        ];
        
        return view('admin.professeurs.create', compact('matieresParNiveau'));
    }

    /**
     * Enregistre un nouveau professeur
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Enregistre un nouveau professeur
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:255',
            'date_naissance' => 'required|date|before:today',
            'lieu_naissance' => 'required|string|max:255',
            'date_embauche' => 'required|date',
            'pourcentage_remuneration' => 'required|numeric|min:1|max:100',
            'matieres' => 'required|array|min:1',
            'matieres.*' => 'exists:matieres,id',
            'pourcentages' => 'required|array',
            'pourcentages.*' => 'required|numeric|min:1|max:100',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Vérifier que chaque matière a un pourcentage valide
        if (count($validated['matieres']) !== count($validated['pourcentages'])) {
            return back()->withInput()->withErrors([
                'pourcentages' => 'Un pourcentage est requis pour chaque matière sélectionnée.'
            ]);
        }

        // Démarrer une transaction pour s'assurer que tout est créé correctement
        DB::beginTransaction();

        try {
            // Création de l'utilisateur professeur
            $professeur = User::create([
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'telephone' => $validated['telephone'],
                'adresse' => $validated['adresse'],
                'date_naissance' => $validated['date_naissance'],
                'lieu_naissance' => $validated['lieu_naissance'],
                'date_embauche' => $validated['date_embauche'],
                'pourcentage_remuneration' => $validated['pourcentage_remuneration'],
                'password' => Hash::make($validated['password']),
                'role' => 'professeur',
                'est_actif' => true,
            ]);

            // Association des matières avec leurs pourcentages respectifs
            $matieresAvecPourcentages = [];
            foreach ($validated['matieres'] as $index => $matiereId) {
                $matieresAvecPourcentages[$matiereId] = [
                    'pourcentage_remuneration' => $validated['pourcentages'][$index],
                    'date_debut' => now(),
                ];
            }
            
            $professeur->matieres()->sync($matieresAvecPourcentages);

            DB::commit();

            return redirect()->route('admin.professeurs.show', $professeur)
                ->with('success', 'Professeur créé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création du professeur: ' . $e->getMessage());
            
            return back()->withInput()->withErrors([
                'error' => 'Une erreur est survenue lors de la création du professeur. Veuillez réessayer.'
            ]);
        }
    }

    /**
     * Affiche les détails d'un professeur
     *
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\View\View
     */
    /**
     * Affiche les détails d'un professeur
     *
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\View\View
     */
    public function show(User $professeur)
    {
        // Charger les matières actives avec le nombre d'élèves
        $professeur->load([
            'matieres' => function($query) {
                $query->where('est_active', true)
                    ->withCount('eleves');
            },
            'paiements' => function($query) {
                $query->latest()->take(5);
            }
        ]);
        
        // Calculer les statistiques
        $salaireMensuel = $professeur->getSalaireMensuelAttribute();
        $salaireMoisPrecedent = $professeur->getSalaireMensuelAttribute(null, 'mois_precedent');
        $paiementsMoisCourant = $professeur->getSoldeTotalAttribute('mois_courant');
        $paiementsMoisPrecedent = $professeur->getSoldeTotalAttribute('mois_precedent');
        
        // Récupération des statistiques détaillées
        $statistiques = [
            'nombre_matieres' => $professeur->matieres->count(),
            'nombre_eleves' => $professeur->matieres->sum('eleves_count'),
            'salaire_mensuel' => $salaireMensuel,
            'salaire_mois_precedent' => $salaireMoisPrecedent,
            'paiements_mois_courant' => $paiementsMoisCourant,
            'paiements_mois_precedent' => $paiementsMoisPrecedent,
            'solde_restant' => max(0, $salaireMensuel - $paiementsMoisCourant),
            'repartition_salaire' => $professeur->repartition_salaire,
        ];
        
        // Charger les années disponibles pour les statistiques
        $anneesPaiements = $professeur->paiements()
            ->selectRaw('YEAR(date_paiement) as annee')
            ->distinct()
            ->orderBy('annee', 'desc')
            ->pluck('annee')
            ->filter()
            ->toArray();
        
        return view('admin.professeurs.show', [
            'professeur' => $professeur,
            'statistiques' => $statistiques,
            'anneesPaiements' => $anneesPaiements,
        ]);
    }

    /**
     * Affiche le formulaire de modification d'un professeur
     *
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\View\View
     */
    /**
     * Affiche le formulaire de modification d'un professeur
     *
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\View\View
     */
    public function edit(User $professeur)
    {
        $this->authorize('update', $professeur);
        
        // Récupérer les matières actives groupées par niveau
        $matieresParNiveau = [
            'primaire' => [
                'label' => 'Primaire',
                'matieres' => Matiere::where('niveau', 'primaire')
                    ->where('est_active', true)
                    ->orderBy('nom')
                    ->get()
            ],
            'college' => [
                'label' => 'Collège',
                'matieres' => Matiere::where('niveau', 'college')
                    ->where('est_active', true)
                    ->orderBy('nom')
                    ->get()
            ],
            'lycee' => [
                'label' => 'Lycée',
                'matieres' => Matiere::where('niveau', 'lycee')
                    ->where('est_active', true)
                    ->orderBy('nom')
                    ->get()
            ]
        ];
        
        // Charger les matières du professeur avec les pourcentages
        $professeur->load(['matieres' => function($query) {
            $query->withPivot('pourcentage_remuneration');
        }]);
        
        // Préparer les données pour la vue
        $matieresProfesseur = [];
        foreach ($professeur->matieres as $matiere) {
            $matieresProfesseur[$matiere->id] = [
                'id' => $matiere->id,
                'nom' => $matiere->nom,
                'niveau' => $matiere->niveau,
                'pourcentage' => $matiere->pivot->pourcentage_remuneration ?? $professeur->pourcentage_remuneration,
                'date_debut' => $matiere->pivot->date_debut ?? null,
                'date_fin' => $matiere->pivot->date_fin ?? null,
            ];
        }
        
        return view('admin.professeurs.edit', [
            'professeur' => $professeur,
            'matieresParNiveau' => $matieresParNiveau,
            'matieresProfesseur' => $matieresProfesseur
        ]);
    }

    /**
     * Met à jour un professeur existant
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Met à jour un professeur existant
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $professeur)
    {
        $this->authorize('update', $professeur);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($professeur->id),
            ],
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:255',
            'date_naissance' => 'required|date|before:today',
            'lieu_naissance' => 'required|string|max:255',
            'date_embauche' => 'required|date',
            'pourcentage_remuneration' => 'required|numeric|min:1|max:100',
            'matieres' => 'required|array|min:1',
            'matieres.*' => 'exists:matieres,id',
            'pourcentages' => 'required|array',
            'pourcentages.*' => 'required|numeric|min:1|max:100',
            'est_actif' => 'boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Vérifier que chaque matière a un pourcentage valide
        if (count($validated['matieres']) !== count($validated['pourcentages'])) {
            return back()->withInput()->withErrors([
                'pourcentages' => 'Un pourcentage est requis pour chaque matière sélectionnée.'
            ]);
        }

        // Démarrer une transaction
        DB::beginTransaction();

        try {
            // Mise à jour des informations de base
            $updateData = [
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'],
                'name' => $validated['nom'] . ' ' . $validated['prenom'],
                'email' => $validated['email'],
                'telephone' => $validated['telephone'],
                'adresse' => $validated['adresse'],
                'date_naissance' => $validated['date_naissance'],
                'lieu_naissance' => $validated['lieu_naissance'],
                'date_embauche' => $validated['date_embauche'],
                'pourcentage_remuneration' => $validated['pourcentage_remuneration'],
                'est_actif' => $validated['est_actif'] ?? $professeur->est_actif,
            ];

            // Mise à jour du mot de passe si fourni
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $professeur->update($updateData);

            // Mettre à jour les matières du professeur avec les pourcentages
            $matieresAvecPourcentages = [];
            foreach ($validated['matieres'] as $index => $matiereId) {
                $matieresAvecPourcentages[$matiereId] = [
                    'pourcentage_remuneration' => $validated['pourcentages'][$index],
                    'date_debut' => now(),
                ];
            }
            
            $professeur->matieres()->sync($matieresAvecPourcentages);

            DB::commit();

            return redirect()->route('admin.professeurs.show', $professeur)
                ->with('success', 'Informations du professeur mises à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la mise à jour du professeur: ' . $e->getMessage());
            
            return back()->withInput()->withErrors([
                'error' => 'Une erreur est survenue lors de la mise à jour du professeur. Veuillez réessayer.'
            ]);
        }
    }

    /**
     * Affiche le formulaire de changement de mot de passe
     *
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\View\View
     */
    public function editPassword(User $professeur)
    {
        $this->authorize('update', $professeur);
        
        return view('admin.professeurs.password', compact('professeur'));
    }

    /**
     * Met à jour le mot de passe du professeur
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request, User $professeur)
    {
        $this->authorize('update', $professeur);
        
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $professeur->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.professeurs.show', $professeur)
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }

    /**
     * Désactive un professeur
     *
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Désactive un professeur
     *
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivate(User $professeur)
    {
        $this->authorize('delete', $professeur);
        
        // Vérifier si le professeur a des cours à venir
        $coursAVenir = $professeur->cours()
            ->where('date_debut', '>=', now())
            ->exists();
            
        if ($coursAVenir) {
            return back()->withErrors([
                'error' => 'Impossible de désactiver ce professeur car il a des cours à venir.'
            ]);
        }
        
        $professeur->update(['est_actif' => false]);
        
        return redirect()->route('admin.professeurs.show', $professeur)
            ->with('success', 'Le professeur a été désactivé avec succès.');
    }

    /**
     * Réactive un professeur
     *
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(User $professeur)
    {
        $this->authorize('update', $professeur);
        
        $professeur->update(['est_actif' => true]);
        
        return redirect()->route('admin.professeurs.show', $professeur)
            ->with('success', 'Le professeur a été réactivé avec succès.');
    }

    /**
     * Affiche l'historique des paiements d'un professeur
     *
     * @param  \App\Models\User  $professeur
     * @param  string|null  $annee
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function salaires(User $professeur, ?string $annee = null)
    {
        $this->authorize('view', $professeur);
        
        // Si aucune année n'est spécifiée, utiliser l'année en cours
        $annee = $annee ?? now()->year;
        
        // Récupérer les paiements groupés par mois
        $paiements = $professeur->paiements()
            ->select(
                DB::raw('YEAR(date_paiement) as annee'),
                DB::raw('MONTH(date_paiement) as mois'),
                DB::raw('SUM(montant) as total_mois')
            )
            ->whereYear('date_paiement', $annee)
            ->groupBy('annee', 'mois')
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->get();
            
        // Calculer le total des paiements pour l'année
        $totalAnnee = $paiements->sum('total_mois');
        
        // Récupérer les années disponibles pour le filtre
        $anneesDisponibles = $professeur->paiements()
            ->select(DB::raw('YEAR(date_paiement) as annee'))
            ->distinct()
            ->orderBy('annee', 'desc')
            ->pluck('annee')
            ->filter()
            ->toArray();
            
        // Si l'année demandée n'est pas dans les années disponibles, rediriger vers l'année la plus récente
        if (!in_array($annee, $anneesDisponibles) && !empty($anneesDisponibles)) {
            return redirect()->route('admin.professeurs.salaires', [
                'professeur' => $professeur->id,
                'annee' => $anneesDisponibles[0]
            ]);
        }
        
        // Préparer les données pour le graphique
        $donneesGraphique = [
            'labels' => [],
            'data' => []
        ];
        
        $mois = [
            1 => 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];
        
        foreach ($mois as $num => $nom) {
            $paiementMois = $paiements->firstWhere('mois', $num);
            $donneesGraphique['labels'][] = $nom;
            $donneesGraphique['data'][] = $paiementMois ? (float) $paiementMois->total_mois : 0;
        }
        
        return view('admin.professeurs.salaires', [
            'professeur' => $professeur,
            'paiements' => $paiements,
            'annee' => $annee,
            'anneesDisponibles' => $anneesDisponibles,
            'totalAnnee' => $totalAnnee,
            'donneesGraphique' => $donneesGraphique,
            'mois' => $mois
        ]);
    }

    /**
     * Affiche les détails d'un salaire
     *
     * @param  \App\Models\User  $professeur
     * @param  \App\Models\Salaire  $salaire
     * @return \Illuminate\View\View
     */
    public function showSalaire(User $professeur, Salaire $salaire)
    {
        $this->authorize('view', $professeur);
        
        if ($salaire->professeur_id !== $professeur->id) {
            abort(404);
        }
        
        return view('admin.professeurs.salaire-show', compact('professeur', 'salaire'));
    }
}
