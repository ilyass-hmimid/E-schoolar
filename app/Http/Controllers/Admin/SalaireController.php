<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salaire;
use App\Models\User;
use App\Models\Paiement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaireController extends Controller
{
    /**
     * Affiche la liste des salaires
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Salaire::with(['professeur', 'payePar'])
            ->latest('periode_debut');
            
        // Filtrage par professeur
        if ($request->has('professeur_id') && $request->professeur_id) {
            $query->where('professeur_id', $request->professeur_id);
        }
        
        // Filtrage par statut
        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }
        
        // Filtrage par date
        if ($request->has('date_debut') && $request->date_debut) {
            $query->where('periode_fin', '>=', $request->date_debut);
        }
        
        if ($request->has('date_fin') && $request->date_fin) {
            $query->where('periode_debut', '<=', $request->date_fin);
        }
        
        $salaires = $query->paginate(15);
        $professeurs = User::where('role', 'professeur')
            ->where('status', 'actif')
            ->orderBy('name')
            ->get();
        
        return view('admin.salaires.index', compact('salaires', 'professeurs'));
    }

    /**
     * Affiche le formulaire de création d'un salaire
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $professeurs = User::where('role', 'professeur')
            ->where('status', 'actif')
            ->orderBy('name')
            ->get();
            
        return view('admin.salaires.create', compact('professeurs'));
    }

    /**
     * Calcule le salaire d'un professeur pour une période donnée
     *
     * @param  int  $professeurId
     * @param  string  $dateDebut
     * @param  string  $dateFin
     * @return array
     */
    private function calculerSalaire($professeurId, $dateDebut, $dateFin)
    {
        // Récupérer le professeur avec ses matières
        $professeur = User::with(['matieresEnseignees' => function($query) use ($dateDebut, $dateFin) {
            $query->wherePivot('est_responsable', true);
        }])->findOrFail($professeurId);
        
        // Récupérer les paiements des élèves pour les matières du professeur
        $paiements = Paiement::whereIn('matiere_id', $professeur->matieresEnseignees->pluck('id'))
            ->where('statut', 'valide')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->select([
                'matiere_id',
                DB::raw('SUM(montant) as total'),
                DB::raw('COUNT(DISTINCT eleve_id) as nombre_eleves')
            ])
            ->groupBy('matiere_id')
            ->get();
        
        // Calculer le total des paiements et le salaire du professeur
        $totalPaiements = $paiements->sum('total');
        $salaire = $totalPaiements * ($professeur->pourcentage_remuneration / 100);
        
        // Préparer les détails par matière
        $details = [];
        foreach ($professeur->matieresEnseignees as $matiere) {
            $paiementMatiere = $paiements->firstWhere('matiere_id', $matiere->id);
            $details[] = [
                'matiere' => $matiere->nom,
                'nombre_eleves' => $paiementMatiere->nombre_eleves ?? 0,
                'total_paiements' => $paiementMatiere->total ?? 0,
                'pourcentage' => $professeur->pourcentage_remuneration,
                'montant' => ($paiementMatiere->total ?? 0) * ($professeur->pourcentage_remuneration / 100),
            ];
        }
        
        return [
            'professeur_id' => $professeur->id,
            'periode_debut' => $dateDebut,
            'periode_fin' => $dateFin,
            'montant_total' => $salaire,
            'details' => $details,
            'nombre_matieres' => $professeur->matieresEnseignees->count(),
            'nombre_eleves' => $paiements->sum('nombre_eleves'),
        ];
    }

    /**
     * Prépare un nouveau salaire
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function preparer(Request $request)
    {
        $request->validate([
            'professeur_id' => 'required|exists:users,id',
            'periode_debut' => 'required|date',
            'periode_fin' => 'required|date|after_or_equal:periode_debut',
        ]);
        
        // Vérifier que le professeur est bien un professeur actif
        $professeur = User::findOrFail($request->professeur_id);
        if ($professeur->role !== 'professeur' || $professeur->status !== 'actif') {
            return response()->json([
                'success' => false,
                'message' => 'Le professeur sélectionné n\'est pas valide.'
            ], 422);
        }
        
        // Vérifier qu'il n'y a pas de salaire existant pour cette période
        $salaireExistant = Salaire::where('professeur_id', $professeur->id)
            ->where(function($query) use ($request) {
                $query->whereBetween('periode_debut', [$request->periode_debut, $request->periode_fin])
                      ->orWhereBetween('periode_fin', [$request->periode_debut, $request->periode_fin])
                      ->orWhere(function($q) use ($request) {
                          $q->where('periode_debut', '<=', $request->periode_debut)
                            ->where('periode_fin', '>=', $request->periode_fin);
                      });
            })
            ->exists();
            
        if ($salaireExistant) {
            return response()->json([
                'success' => false,
                'message' => 'Un salaire existe déjà pour cette période.'
            ], 422);
        }
        
        // Calculer le salaire
        $salaire = $this->calculerSalaire(
            $request->professeur_id,
            $request->periode_debut,
            $request->periode_fin
        );
        
        return response()->json([
            'success' => true,
            'data' => $salaire
        ]);
    }

    /**
     * Enregistre un nouveau salaire
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'professeur_id' => 'required|exists:users,id',
            'periode_debut' => 'required|date',
            'periode_fin' => 'required|date|after_or_equal:periode_debut',
            'montant_total' => 'required|numeric|min:0',
            'details' => 'required|array',
            'mode_paiement' => 'required|in:virement,especes,cheque',
            'reference' => 'nullable|string|max:100',
            'commentaire' => 'nullable|string|max:1000',
        ]);
        
        // Vérifier que le professeur est bien un professeur actif
        $professeur = User::findOrFail($validated['professeur_id']);
        if ($professeur->role !== 'professeur' || $professeur->status !== 'actif') {
            return back()->with('error', 'Le professeur sélectionné n\'est pas valide.');
        }
        
        // Vérifier qu'il n'y a pas de salaire existant pour cette période
        $salaireExistant = Salaire::where('professeur_id', $professeur->id)
            ->where(function($query) use ($validated) {
                $query->whereBetween('periode_debut', [$validated['periode_debut'], $validated['periode_fin']])
                      ->orWhereBetween('periode_fin', [$validated['periode_debut'], $validated['periode_fin']])
                      ->orWhere(function($q) use ($validated) {
                          $q->where('periode_debut', '<=', $validated['periode_debut'])
                            ->where('periode_fin', '>=', $validated['periode_fin']);
                      });
            })
            ->exists();
            
        if ($salaireExistant) {
            return back()->with('error', 'Un salaire existe déjà pour cette période.');
        }
        
        // Créer le salaire
        $salaire = new Salaire([
            'professeur_id' => $validated['professeur_id'],
            'periode_debut' => $validated['periode_debut'],
            'periode_fin' => $validated['periode_fin'],
            'montant' => $validated['montant_total'],
            'details' => $validated['details'],
            'mode_paiement' => $validated['mode_paiement'],
            'reference' => $validated['reference'] ?? null,
            'commentaire' => $validated['commentaire'] ?? null,
            'statut' => 'paye',
            'paye_par' => auth()->id(),
            'date_paiement' => now(),
        ]);
        
        $salaire->save();
        
        return redirect()->route('admin.salaires.show', $salaire)
            ->with('success', 'Salaire enregistré avec succès.');
    }

    /**
     * Affiche les détails d'un salaire
     *
     * @param  \App\Models\Salaire  $salaire
     * @return \Illuminate\View\View
     */
    public function show(Salaire $salaire)
    {
        $salaire->load(['professeur', 'payePar']);
        return view('admin.salaires.show', compact('salaire'));
    }

    /**
     * Télécharge la fiche de paie d'un salaire
     *
     * @param  \App\Models\Salaire  $salaire
     * @return \Illuminate\Http\Response
     */
    public function fichePaie(Salaire $salaire)
    {
        $salaire->load(['professeur', 'payePar']);
        
        $pdf = \PDF::loadView('admin.salaires.fiche-paie', compact('salaire'));
        
        return $pdf->download("fiche-paie-{$salaire->professeur->name}-{$salaire->periode_debut}-{$salaire->periode_fin}.pdf");
    }

    /**
     * Affiche le rapport des salaires
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function rapport(Request $request)
    {
        $query = Salaire::with(['professeur', 'payePar'])
            ->select(
                'salaires.*',
                DB::raw('YEAR(periode_debut) as annee', 'YEAR(periode_fin) as annee_fin'),
                DB::raw('MONTH(periode_debut) as mois_debut', 'MONTH(periode_fin) as mois_fin')
            )
            ->orderBy('periode_debut', 'desc');
            
        // Filtrage par année
        $annee = $request->input('annee', date('Y'));
        $query->where(function($q) use ($annee) {
            $q->whereYear('periode_debut', $annee)
              ->orWhereYear('periode_fin', $annee);
        });
        
        // Filtrage par mois
        if ($request->has('mois') && $request->mois) {
            $query->where(function($q) use ($request) {
                $q->whereMonth('periode_debut', $request->mois)
                  ->orWhereMonth('periode_fin', $request->mois);
            });
        }
        
        // Filtrage par professeur
        if ($request->has('professeur_id') && $request->professeur_id) {
            $query->where('professeur_id', $request->professeur_id);
        }
        
        // Filtrage par statut
        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }
        
        $salaires = $query->get();
        
        // Calcul des statistiques
        $totalSalaires = $salaires->sum('montant');
        $moyenneParProfesseur = $salaires->groupBy('professeur_id')->count() > 0 
            ? $totalSalaires / $salaires->groupBy('professeur_id')->count() 
            : 0;
            
        $parMois = $salaires->groupBy(function($salaire) {
            return Carbon::parse($salaire->periode_debut)->format('Y-m');
        })->map(function($salaires) {
            return [
                'montant' => $salaires->sum('montant'),
                'count' => $salaires->count(),
            ];
        });
        
        $professeurs = User::where('role', 'professeur')
            ->where('status', 'actif')
            ->orderBy('name')
            ->get();
            
        $annees = range(date('Y') - 5, date('Y') + 1);
        
        return view('admin.salaires.rapport', compact(
            'salaires',
            'totalSalaires',
            'moyenneParProfesseur',
            'parMois',
            'professeurs',
            'annees',
            'annee'
        ));
    }
}
