<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Pack;
use App\Models\Tarif;
use App\Models\User;
use App\Models\Matiere;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class PaiementController extends Controller
{
    /**
     * Afficher le formulaire de création d'un paiement
     */
    public function create()
    {
        // Récupérer les données nécessaires pour les formulaires
        $etudiants = User::where('is_active', true)
            ->whereIn('role', [RoleType::ELEVE->value])
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        $matieres = Matiere::where('is_active', true)
            ->select('id', 'nom')
            ->orderBy('nom')
            ->get();

        $packs = Pack::where('is_active', true)
            ->select('id', 'nom', 'prix')
            ->orderBy('nom')
            ->get();

        $tarifs = Tarif::where('is_active', true)
            ->select('id', 'nom', 'montant', 'pack_id')
            ->orderBy('nom')
            ->get();

        return Inertia::render('Admin/Paiements/Create', [
            'etudiants' => $etudiants,
            'matieres' => $matieres,
            'packs' => $packs,
            'tarifs' => $tarifs,
        ]);
    }

    /**
     * Afficher les détails d'un paiement
     */
    public function show(Paiement $paiement)
    {
        $paiement->load([
            'etudiant',
            'matiere',
            'pack',
            'tarif',
            'assistant'
        ]);

        return Inertia::render('Admin/Paiements/Show', [
            'paiement' => $paiement
        ]);
    }

    /**
     * Afficher le formulaire d'édition d'un paiement
     */
    public function edit(Paiement $paiement)
    {
        $paiement->load(['etudiant', 'matiere', 'pack', 'tarif']);
        
        // Récupérer les données nécessaires pour les formulaires
        $etudiants = User::where('is_active', true)
            ->whereIn('role', [RoleType::ELEVE->value])
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        $matieres = Matiere::where('is_active', true)
            ->select('id', 'nom')
            ->orderBy('nom')
            ->get();

        $packs = Pack::where('is_active', true)
            ->select('id', 'nom', 'prix')
            ->orderBy('nom')
            ->get();

        $tarifs = Tarif::where('is_active', true)
            ->select('id', 'nom', 'montant', 'pack_id')
            ->orderBy('nom')
            ->get();

        return Inertia::render('Admin/Paiements/Edit', [
            'paiement' => $paiement,
            'etudiants' => $etudiants,
            'matieres' => $matieres,
            'packs' => $packs,
            'tarifs' => $tarifs,
        ]);
    }

    /**
     * Mettre à jour un paiement existant
     */
    public function update(Request $request, Paiement $paiement)
    {
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:users,id',
            'matiere_id' => 'nullable|exists:matieres,id',
            'pack_id' => 'nullable|exists:packs,id',
            'tarif_id' => 'nullable|exists:tarifs,id',
            'montant' => 'required|numeric|min:0',
            'mode_paiement' => 'required|in:especes,cheque,virement,carte',
            'reference_paiement' => 'nullable|string|max:255',
            'date_paiement' => 'required|date',
            'statut' => 'required|in:en_attente,valide,annule',
            'commentaires' => 'nullable|string',
            'mois_periode' => 'nullable|date_format:Y-m',
        ]);

        // Si un tarif est spécifié, on utilise le montant du tarif
        if (!empty($validated['tarif_id'])) {
            $tarif = Tarif::findOrFail($validated['tarif_id']);
            $validated['montant'] = $tarif->montant;
        }

        try {
            DB::beginTransaction();

            // Mettre à jour le paiement
            $paiement->update($validated);

            // Mettre à jour l'inscription si nécessaire
            if ($paiement->pack_id) {
                $this->updateInscriptionPack($paiement);
            }

            DB::commit();

            return redirect()->route('admin.paiements.show', $paiement)
                ->with('success', 'Paiement mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la mise à jour du paiement: ' . $e->getMessage());
            
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour du paiement.');
        }
    }

    /**
     * Supprimer un paiement
     */
    public function destroy(Paiement $paiement)
    {
        try {
            DB::beginTransaction();
            
            // Vérifier si le paiement peut être supprimé
            if ($paiement->statut === 'valide') {
                return back()->with('error', 'Impossible de supprimer un paiement validé.');
            }

            // Supprimer le paiement
            $paiement->delete();
            
            DB::commit();
            
            return redirect()->route('admin.paiements.index')
                ->with('success', 'Paiement supprimé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la suppression du paiement: ' . $e->getMessage());
            
            return back()->with('error', 'Une erreur est survenue lors de la suppression du paiement.');
        }
    }

    /**
     * Mettre à jour le statut d'un paiement
     */
    public function updateStatus(Request $request, Paiement $paiement)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,valide,annule'
        ]);

        try {
            $paiement->update(['statut' => $request->statut]);
            
            return back()->with('success', 'Statut du paiement mis à jour avec succès.');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour du statut du paiement: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour du statut.');
        }
    }

    /**
     * Générer un reçu de paiement
     */
    public function generateReceipt(Paiement $paiement)
    {
        $paiement->load(['etudiant', 'pack', 'matiere']);
        
        $pdf = \PDF::loadView('pdf.receipt', [
            'paiement' => $paiement
        ]);
        
        return $pdf->download('recu-paiement-' . $paiement->id . '.pdf');
    }
    
    /**
     * Récupérer la liste des étudiants pour le formulaire
     */
    public function getEtudiants()
    {
        $etudiants = User::where('is_active', true)
            ->where('role', RoleType::ELEVE->value)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
            
        return response()->json($etudiants);
    }
    
    /**
     * Récupérer la liste des matières pour le formulaire
     */
    public function getMatieres()
    {
        $matieres = Matiere::where('is_active', true)
            ->select('id', 'nom')
            ->orderBy('nom')
            ->get();
            
        return response()->json($matieres);
    }
    
    /**
     * Récupérer la liste des packs pour le formulaire
     */
    public function getPacks()
    {
        $packs = Pack::where('is_active', true)
            ->select('id', 'nom', 'prix')
            ->orderBy('nom')
            ->get();
            
        return response()->json($packs);
    }
    
    /**
     * Récupérer la liste des tarifs pour le formulaire
     */
    public function getTarifs()
    {
        $tarifs = Tarif::where('is_active', true)
            ->select('id', 'nom', 'montant', 'pack_id')
            ->with('pack:id,nom')
            ->orderBy('nom')
            ->get();
            
        return response()->json($tarifs);
    }

    /**
     * Afficher la liste des paiements
     */
    public function index(Request $request)
    {
        $query = Paiement::with(['etudiant', 'matiere', 'pack', 'tarif', 'assistant'])
            ->latest('date_paiement');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('etudiant', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_paiement', '>=', $request->input('date_debut'));
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_paiement', '<=', $request->input('date_fin'));
        }

        $paiements = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Paiements/Index', [
            'paiements' => $paiements,
            'filters' => $request->only(['search', 'statut', 'date_debut', 'date_fin'])
        ]);
    }

    /**
     * Calculer le salaire d'un professeur sur une période donnée
     */
    public function calculateSalaire(Request $request, User $professeur)
    {
        $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ]);

        $dateDebut = Carbon::parse($request->input('date_debut'))->startOfDay();
        $dateFin = Carbon::parse($request->input('date_fin'))->endOfDay();

        // Récupérer les paiements liés aux matières enseignées par le professeur
        $paiements = Paiement::whereHas('matiere', function($query) use ($professeur) {
                $query->where('professeur_id', $professeur->id);
            })
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->where('statut', 'valide')
            ->with(['matiere', 'etudiant'])
            ->get();

        // Calculer le total des paiements
        $total = $paiements->sum('montant');
        
        // Calculer la part du professeur (par exemple 70% du montant total)
        $pourcentageProfesseur = 70; // À définir dans la configuration
        $salaire = $total * ($pourcentageProfesseur / 100);

        return response()->json([
            'professeur' => $professeur->only(['id', 'name', 'email']),
            'periode' => [
                'debut' => $dateDebut->format('Y-m-d'),
                'fin' => $dateFin->format('Y-m-d'),
            ],
            'nombre_paiements' => $paiements->count(),
            'total_paiements' => $total,
            'pourcentage_professeur' => $pourcentageProfesseur,
            'salaire_brut' => $salaire,
            'details_paiements' => $paiements->map(function($paiement) {
                return [
                    'id' => $paiement->id,
                    'date' => $paiement->date_paiement->format('d/m/Y'),
                    'etudiant' => $paiement->etudiant->name,
                    'matiere' => $paiement->matiere->nom,
                    'montant' => $paiement->montant,
                ];
            })
        ]);
    }

    /**
     * Enregistrer un nouveau paiement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:users,id',
            'matiere_id' => 'nullable|exists:matieres,id',
            'pack_id' => 'nullable|exists:packs,id',
            'tarif_id' => 'nullable|exists:tarifs,id',
            'montant' => 'required|numeric|min:0',
            'mode_paiement' => 'required|in:especes,cheque,virement,carte',
            'reference_paiement' => 'nullable|string|max:255',
            'date_paiement' => 'required|date',
            'statut' => 'required|in:en_attente,valide,annule',
            'commentaires' => 'nullable|string',
            'mois_periode' => 'nullable|date_format:Y-m',
        ]);

        // Si un tarif est spécifié, on utilise le montant du tarif
        if (!empty($validated['tarif_id'])) {
            $tarif = Tarif::findOrFail($validated['tarif_id']);
            $validated['montant'] = $tarif->montant;
            
            // Si le tarif est lié à un pack, on s'assure que le pack est cohérent
            if ($tarif->pack_id && $validated['pack_id'] !== $tarif->pack_id) {
                return back()->withErrors([
                    'tarif_id' => 'Le tarif sélectionné ne correspond pas au pack choisi.'
                ]);
            }
        }

        // Vérifier si l'utilisateur a le rôle d'assistant
        $user = $request->user();
        if ($user->hasRole('assistant')) {
            $validated['assistant_id'] = $user->id;
            $validated['statut'] = 'en_attente'; // Les paiements des assistants sont en attente de validation
        }

        try {
            DB::beginTransaction();

            $paiement = Paiement::create($validated);

            // Mettre à jour le statut de l'inscription si nécessaire
            if ($validated['pack_id']) {
                $this->updateInscriptionPack($paiement);
            }

            DB::commit();

            return redirect()->route('admin.paiements.index')
                ->with('success', 'Paiement enregistré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage());
            
            return back()->with('error', 'Une erreur est survenue lors de l\'enregistrement du paiement.');
        }
    }

    /**
     * Mettre à jour le statut de l'inscription liée au pack
     */
    protected function updateInscriptionPack(Paiement $paiement)
    {
        // Vérifier s'il existe déjà une inscription active pour cet étudiant et ce pack
        $inscription = \App\Models\Inscription::where('etudiant_id', $paiement->etudiant_id)
            ->where('pack_id', $paiement->pack_id)
            ->where('date_fin', '>=', now())
            ->first();

        if ($inscription) {
            // Prolonger l'inscription existante
            $dateDebut = Carbon::parse($inscription->date_fin);
            $dateFin = $dateDebut->copy()->addDays($paiement->pack->duree_jours);
            
            $inscription->update([
                'date_fin' => $dateFin,
                'statut' => 'actif',
            ]);
        } else {
            // Créer une nouvelle inscription
            $dateDebut = now();
            $dateFin = $dateDebut->copy()->addDays($paiement->pack->duree_jours);
            
            \App\Models\Inscription::create([
                'etudiant_id' => $paiement->etudiant_id,
                'pack_id' => $paiement->pack_id,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'statut' => 'actif',
                'paiement_id' => $paiement->id,
            ]);
        }
    }
}
