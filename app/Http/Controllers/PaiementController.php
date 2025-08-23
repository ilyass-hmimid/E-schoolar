<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Pack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Enums\RoleType;

class PaiementController extends Controller
{
    /**
     * Affiche la liste des paiements
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Paiement::with(['etudiant:id,name,email', 'matiere:id,nom', 'pack:id,nom', 'assistant:id,name']);

        // Filtres selon le rôle
        if ($user->role === RoleType::ASSISTANT) {
            $query->where('assistant_id', $user->id);
        } elseif ($user->role === RoleType::ELEVE) {
            $query->where('etudiant_id', $user->id);
        }

        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('etudiant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_debut')) {
            $query->where('date_paiement', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('date_paiement', '<=', $request->date_fin);
        }

        $paiements = $query->orderBy('date_paiement', 'desc')->paginate(15);

        // Statistiques
        $stats = [
            'total_paiements' => $query->count(),
            'total_montant' => $query->sum('montant'),
            'paiements_valides' => $query->where('statut', 'valide')->count(),
            'paiements_en_attente' => $query->where('statut', 'en_attente')->count(),
        ];

        return Inertia::render('Paiements/Index', [
            'paiements' => $paiements,
            'stats' => $stats,
            'filters' => $request->only(['search', 'statut', 'date_debut', 'date_fin']),
            'canCreate' => in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT]),
            'canValidate' => in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT]),
        ]);
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        $eleves = User::eleves()->actifs()->get(['id', 'name', 'email', 'somme_a_payer']);
        $matieres = Matiere::actifs()->get(['id', 'nom', 'prix_mensuel']);
        $packs = Pack::all(['id', 'nom', 'prix']);

        return Inertia::render('Paiements/Create', [
            'eleves' => $eleves,
            'matieres' => $matieres,
            'packs' => $packs,
            'modes_paiement' => [
                'especes' => 'Espèces',
                'cheque' => 'Chèque',
                'virement' => 'Virement bancaire',
                'carte' => 'Carte bancaire',
            ],
        ]);
    }

    /**
     * Enregistre un nouveau paiement
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'etudiant_id' => 'required|exists:users,id',
            'matiere_id' => 'nullable|exists:matieres,id',
            'pack_id' => 'nullable|exists:packs,id',
            'montant' => 'required|numeric|min:0',
            'mode_paiement' => 'required|in:especes,cheque,virement,carte',
            'reference_paiement' => 'nullable|string|max:255',
            'commentaires' => 'nullable|string|max:500',
            'mois_periode' => 'nullable|date_format:Y-m',
        ]);

        // Vérifier que l'étudiant existe et est actif
        $etudiant = User::eleves()->actifs()->findOrFail($validated['etudiant_id']);

        // Vérifier que soit matiere_id soit pack_id est fourni
        if (empty($validated['matiere_id']) && empty($validated['pack_id'])) {
            return back()->withErrors(['matiere_id' => 'Veuillez sélectionner une matière ou un pack']);
        }

        // Générer une référence unique si non fournie
        if (empty($validated['reference_paiement'])) {
            $validated['reference_paiement'] = 'PAY-' . date('Ymd') . '-' . strtoupper(uniqid());
        }

        // Déterminer le statut selon le mode de paiement
        $statut = in_array($validated['mode_paiement'], ['especes', 'carte']) ? 'valide' : 'en_attente';

        try {
            DB::beginTransaction();

            $paiement = Paiement::create([
                'etudiant_id' => $validated['etudiant_id'],
                'matiere_id' => $validated['matiere_id'],
                'pack_id' => $validated['pack_id'],
                'assistant_id' => $user->id,
                'montant' => $validated['montant'],
                'mode_paiement' => $validated['mode_paiement'],
                'reference_paiement' => $validated['reference_paiement'],
                'date_paiement' => now(),
                'statut' => $statut,
                'commentaires' => $validated['commentaires'],
                'mois_periode' => $validated['mois_periode'] ?? now()->format('Y-m'),
            ]);

            // Mettre à jour la somme à payer de l'étudiant
            if ($statut === 'valide') {
                $etudiant->decrement('somme_a_payer', $validated['montant']);
            }

            DB::commit();

            return redirect()->route('paiements.index')
                ->with('success', 'Paiement enregistré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage()]);
        }
    }

    /**
     * Affiche un paiement spécifique
     */
    public function show(Paiement $paiement)
    {
        $user = Auth::user();
        
        // Vérifier les permissions
        if ($user->role === RoleType::ELEVE && $paiement->etudiant_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }
        
        if ($user->role === RoleType::ASSISTANT && $paiement->assistant_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        $paiement->load(['etudiant:id,name,email', 'matiere:id,nom', 'pack:id,nom', 'assistant:id,name']);

        return Inertia::render('Paiements/Show', [
            'paiement' => $paiement,
            'canEdit' => in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT]),
            'canValidate' => in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT]) && $paiement->statut === 'en_attente',
        ]);
    }

    /**
     * Valide un paiement
     */
    public function validatePaiement(Paiement $paiement)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        if ($paiement->statut !== 'en_attente') {
            return back()->withErrors(['error' => 'Ce paiement ne peut pas être validé.']);
        }

        try {
            DB::beginTransaction();

            $paiement->update([
                'statut' => 'valide',
                'date_paiement' => now(),
            ]);

            // Mettre à jour la somme à payer de l'étudiant
            $paiement->etudiant->decrement('somme_a_payer', $paiement->montant);

            DB::commit();

            return back()->with('success', 'Paiement validé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la validation: ' . $e->getMessage()]);
        }
    }

    /**
     * Annule un paiement
     */
    public function cancelPaiement(Paiement $paiement)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        if ($paiement->statut === 'annule') {
            return back()->withErrors(['error' => 'Ce paiement est déjà annulé.']);
        }

        try {
            DB::beginTransaction();

            $paiement->update([
                'statut' => 'annule',
                'commentaires' => $paiement->commentaires . "\n[ANNULÉ le " . now()->format('d/m/Y H:i') . " par " . $user->name . "]",
            ]);

            // Remettre à jour la somme à payer si le paiement était validé
            if ($paiement->statut === 'valide') {
                $paiement->etudiant->increment('somme_a_payer', $paiement->montant);
            }

            DB::commit();

            return back()->with('success', 'Paiement annulé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de l\'annulation: ' . $e->getMessage()]);
        }
    }

    /**
     * Exporte les paiements en CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        $query = Paiement::with(['etudiant:id,name,email', 'matiere:id,nom', 'pack:id,nom', 'assistant:id,name']);

        // Appliquer les mêmes filtres que dans index()
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('etudiant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_debut')) {
            $query->where('date_paiement', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('date_paiement', '<=', $request->date_fin);
        }

        $paiements = $query->orderBy('date_paiement', 'desc')->get();

        $filename = 'paiements_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($paiements) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID', 'Étudiant', 'Matière/Pack', 'Montant', 'Mode de paiement',
                'Référence', 'Date', 'Statut', 'Assistant', 'Commentaires'
            ]);

            // Données
            foreach ($paiements as $paiement) {
                fputcsv($file, [
                    $paiement->id,
                    $paiement->etudiant->name,
                    $paiement->matiere ? $paiement->matiere->nom : ($paiement->pack ? $paiement->pack->nom : 'N/A'),
                    number_format($paiement->montant, 2, ',', ' ') . ' DH',
                    $paiement->mode_paiement_label,
                    $paiement->reference_paiement,
                    $paiement->date_paiement->format('d/m/Y H:i'),
                    $paiement->statut_label,
                    $paiement->assistant->name,
                    $paiement->commentaires,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
