<?php

namespace App\Http\Controllers;

use App\Models\Salaire;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Paiement;
use App\Enums\RoleType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SalaireController extends Controller
{
    /**
     * Affiche la liste des salaires avec filtres
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Salaire::with(['professeur:id,name,email', 'matiere:id,nom']);

        // Filtres selon le rôle
        if ($user->role === RoleType::PROFESSEUR) {
            $query->where('professeur_id', $user->id);
        }

        // Filtres de recherche
        $filters = $request->only(['search', 'matiere_id', 'statut', 'mois_periode']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('professeur', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('matiere_id')) {
            $query->where('matiere_id', $request->matiere_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('mois_periode')) {
            $query->where('mois_periode', $request->mois_periode);
        }

        $salaires = $query->orderBy('mois_periode', 'desc')->paginate(15);

        // Statistiques
        $stats = [
            'total_salaires' => $query->count(),
            'total_montant_brut' => $query->sum('montant_brut'),
            'total_montant_net' => $query->sum('montant_net'),
            'salaires_payes' => $query->where('statut', 'paye')->count(),
            'salaires_en_attente' => $query->where('statut', 'en_attente')->count(),
        ];

        return Inertia::render('Salaires/Index', [
            'salaires' => $salaires,
            'stats' => $stats,
            'filters' => $filters,
            'matieres' => Matiere::actifs()->get(['id', 'nom']),
            'canCreate' => in_array($user->role, [RoleType::ADMIN]),
            'canPay' => in_array($user->role, [RoleType::ADMIN]),
        ]);
    }

    /**
     * Affiche le formulaire de création d'un salaire
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN])) {
            abort(403, 'Accès non autorisé');
        }

        $professeurs = User::professeurs()->actifs()->get(['id', 'name', 'email']);
        $matieres = Matiere::actifs()->get(['id', 'nom']);

        return Inertia::render('Salaires/Create', [
            'professeurs' => $professeurs,
            'matieres' => $matieres,
        ]);
    }

    /**
     * Enregistre un nouveau salaire
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN])) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'professeur_id' => 'required|exists:users,id',
            'matiere_id' => 'required|exists:matieres,id',
            'mois_periode' => 'required|date_format:Y-m',
            'nombre_eleves' => 'required|integer|min:1',
            'prix_unitaire' => 'required|numeric|min:0',
            'commission_prof' => 'required|numeric|min:0|max:100',
            'commentaires' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $salaire = Salaire::create([
                'professeur_id' => $validated['professeur_id'],
                'matiere_id' => $validated['matiere_id'],
                'mois_periode' => $validated['mois_periode'],
                'nombre_eleves' => $validated['nombre_eleves'],
                'prix_unitaire' => $validated['prix_unitaire'],
                'commission_prof' => $validated['commission_prof'],
                'commentaires' => $validated['commentaires'],
                'statut' => 'en_attente',
            ]);

            DB::commit();

            return redirect()->route('salaires.index')
                ->with('success', 'Salaire créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()]);
        }
    }

    /**
     * Affiche les détails d'un salaire
     */
    public function show(Salaire $salaire)
    {
        $user = Auth::user();
        
        // Vérifier les permissions
        if ($user->role === RoleType::PROFESSEUR && $salaire->professeur_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        $salaire->load(['professeur:id,name,email,phone', 'matiere:id,nom']);

        return Inertia::render('Salaires/Show', [
            'salaire' => $salaire,
            'canEdit' => in_array($user->role, [RoleType::ADMIN]),
            'canPay' => in_array($user->role, [RoleType::ADMIN]) && $salaire->statut === 'en_attente',
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'un salaire
     */
    public function edit(Salaire $salaire)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN])) {
            abort(403, 'Accès non autorisé');
        }

        $salaire->load(['professeur:id,name,email', 'matiere:id,nom']);
        $professeurs = User::professeurs()->actifs()->get(['id', 'name', 'email']);
        $matieres = Matiere::actifs()->get(['id', 'nom']);

        return Inertia::render('Salaires/Edit', [
            'salaire' => $salaire,
            'professeurs' => $professeurs,
            'matieres' => $matieres,
        ]);
    }

    /**
     * Met à jour un salaire existant
     */
    public function update(Request $request, Salaire $salaire)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN])) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'professeur_id' => 'required|exists:users,id',
            'matiere_id' => 'required|exists:matieres,id',
            'mois_periode' => 'required|date_format:Y-m',
            'nombre_eleves' => 'required|integer|min:1',
            'prix_unitaire' => 'required|numeric|min:0',
            'commission_prof' => 'required|numeric|min:0|max:100',
            'commentaires' => 'nullable|string|max:1000',
        ]);

        $salaire->update($validated);

        return redirect()->route('salaires.show', $salaire)
            ->with('success', 'Salaire mis à jour avec succès.');
    }

    /**
     * Supprime un salaire
     */
    public function destroy(Salaire $salaire)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN])) {
            abort(403, 'Accès non autorisé');
        }

        $salaire->delete();
        
        return redirect()->route('salaires.index')
            ->with('success', 'Salaire supprimé avec succès.');
    }

    /**
     * Marque un salaire comme payé
     */
    public function payer(Request $request, Salaire $salaire)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN])) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'date_paiement' => 'required|date',
            'commentaires' => 'nullable|string|max:1000',
        ]);

        $salaire->update([
            'statut' => 'paye',
            'date_paiement' => $validated['date_paiement'],
            'commentaires' => $validated['commentaires'] ?? $salaire->commentaires,
        ]);

        return back()->with('success', 'Salaire marqué comme payé.');
    }

    /**
     * Calcule automatiquement les salaires pour une période donnée
     */
    public function calculerAutomatiquement(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN])) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'mois_periode' => 'required|date_format:Y-m',
            'professeur_id' => 'nullable|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $mois = $validated['mois_periode'];
            $professeurId = $validated['professeur_id'] ?? null;

            // Récupérer tous les paiements validés pour le mois
            $query = Paiement::where('statut', 'valide')
                ->where('mois_periode', $mois)
                ->whereNotNull('matiere_id');

            if ($professeurId) {
                $query->whereHas('matiere.enseignements', function ($q) use ($professeurId) {
                    $q->where('professeur_id', $professeurId);
                });
            }

            $paiements = $query->with(['matiere.enseignements.professeur'])->get();

            $salairesCrees = 0;
            $salairesMisAJour = 0;

            // Grouper par professeur et matière
            $groupedPaiements = $paiements->groupBy(function ($paiement) {
                $enseignement = $paiement->matiere->enseignements->first();
                return $enseignement ? $enseignement->professeur_id : null;
            })->filter();

            foreach ($groupedPaiements as $professeurId => $paiementsProf) {
                $paiementsParMatiere = $paiementsProf->groupBy('matiere_id');

                foreach ($paiementsParMatiere as $matiereId => $paiementsMatiere) {
                    $matiere = $paiementsMatiere->first()->matiere;
                    $enseignement = $matiere->enseignements->where('professeur_id', $professeurId)->first();

                    if (!$enseignement) continue;

                    $nombreEleves = $paiementsMatiere->count();
                    $montantTotal = $paiementsMatiere->sum('montant');
                    $prixUnitaire = $matiere->prix_mensuel;
                    $commissionProf = $matiere->commission_prof;

                    // Vérifier si un salaire existe déjà pour cette période
                    $salaireExistant = Salaire::where('professeur_id', $professeurId)
                        ->where('matiere_id', $matiereId)
                        ->where('mois_periode', $mois)
                        ->first();

                    if ($salaireExistant) {
                        // Mettre à jour le salaire existant
                        $salaireExistant->update([
                            'nombre_eleves' => $nombreEleves,
                            'prix_unitaire' => $prixUnitaire,
                            'commission_prof' => $commissionProf,
                        ]);
                        $salairesMisAJour++;
                    } else {
                        // Créer un nouveau salaire
                        Salaire::create([
                            'professeur_id' => $professeurId,
                            'matiere_id' => $matiereId,
                            'mois_periode' => $mois,
                            'nombre_eleves' => $nombreEleves,
                            'prix_unitaire' => $prixUnitaire,
                            'commission_prof' => $commissionProf,
                            'statut' => 'en_attente',
                            'commentaires' => 'Calculé automatiquement',
                        ]);
                        $salairesCrees++;
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Calcul terminé : {$salairesCrees} salaires créés, {$salairesMisAJour} salaires mis à jour.",
                'salaires_crees' => $salairesCrees,
                'salaires_mis_a_jour' => $salairesMisAJour,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Génère un rapport des salaires
     */
    public function genererRapport(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::PROFESSEUR])) {
            abort(403, 'Accès non autorisé');
        }
        
        $validated = $request->validate([
            'mois_debut' => 'required|date_format:Y-m',
            'mois_fin' => 'required|date_format:Y-m|after_or_equal:mois_debut',
            'professeur_id' => 'nullable|exists:users,id',
            'statut' => 'nullable|in:en_attente,paye',
        ]);
        
        $query = Salaire::with(['professeur:id,name,email', 'matiere:id,nom'])
            ->whereBetween('mois_periode', [$validated['mois_debut'], $validated['mois_fin']]);

        if ($validated['professeur_id']) {
            $query->where('professeur_id', $validated['professeur_id']);
        } elseif ($user->role === RoleType::PROFESSEUR) {
            $query->where('professeur_id', $user->id);
        }

        if ($validated['statut']) {
            $query->where('statut', $validated['statut']);
        }

        $salaires = $query->get();

        $rapport = [
            'periode' => [
                'debut' => $validated['mois_debut'],
                'fin' => $validated['mois_fin'],
            ],
            'statistiques' => [
                'total_salaires' => $salaires->count(),
                'total_montant_brut' => $salaires->sum('montant_brut'),
                'total_montant_net' => $salaires->sum('montant_net'),
                'salaires_payes' => $salaires->where('statut', 'paye')->count(),
                'salaires_en_attente' => $salaires->where('statut', 'en_attente')->count(),
            ],
            'par_professeur' => $salaires->groupBy('professeur.name')->map(function ($group) {
                return [
                    'total_salaires' => $group->count(),
                    'total_montant_brut' => $group->sum('montant_brut'),
                    'total_montant_net' => $group->sum('montant_net'),
                    'salaires_payes' => $group->where('statut', 'paye')->count(),
                    'salaires_en_attente' => $group->where('statut', 'en_attente')->count(),
                ];
            }),
            'par_matiere' => $salaires->groupBy('matiere.nom')->map(function ($group) {
                return [
                    'total_salaires' => $group->count(),
                    'total_montant_brut' => $group->sum('montant_brut'),
                    'total_montant_net' => $group->sum('montant_net'),
                    'salaires_payes' => $group->where('statut', 'paye')->count(),
                    'salaires_en_attente' => $group->where('statut', 'en_attente')->count(),
                ];
            }),
        ];
        
        return response()->json($rapport);
    }
}
