<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Pack;
use App\Models\Matiere;
use App\Models\Tarif;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaiementApiController extends Controller
{
    /**
     * Récupère les informations d'un pack
     */
    public function getPackInfo($id)
    {
        $pack = Pack::select(['id', 'nom', 'prix', 'description', 'duree_jours', 'nbr_seances'])
            ->with(['tarifs' => function($query) {
                $query->where('est_actif', true)
                    ->where(function($q) {
                        $now = now();
                        $q->whereNull('periode_validite_debut')
                            ->orWhere('periode_validite_debut', '<=', $now);
                    })
                    ->where(function($q) {
                        $now = now();
                        $q->whereNull('periode_validite_fin')
                            ->orWhere('periode_validite_fin', '>=', $now);
                    });
            }])
            ->findOrFail($id);

        return response()->json($pack);
    }

    /**
     * Récupère les tarifs en fonction des filtres
     */
    public function getTarifs(Request $request)
    {
        $query = Tarif::query();
        
        if ($request->has('pack_id')) {
            $query->where('pack_id', $request->pack_id);
        }
        
        if ($request->has('matiere_id')) {
            $query->where('matiere_id', $request->matiere_id);
        }
        
        if ($request->has('niveau_id')) {
            $query->where('niveau_id', $request->niveau_id);
        }
        
        if ($request->has('filiere_id')) {
            $query->where('filiere_id', $request->filiere_id);
        }
        
        // Filtrer par période de validité
        $now = now();
        $query->where('est_actif', true)
            ->where(function($q) use ($now) {
                $q->whereNull('periode_validite_debut')
                  ->orWhere('periode_validite_debut', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('periode_validite_fin')
                  ->orWhere('periode_validite_fin', '>=', $now);
            });
        
        return response()->json($query->get());
    }

    /**
     * Récupère les statistiques de paiement
     */
    public function getStats(Request $request)
    {
        $user = $request->user();
        
        $query = Paiement::query();
        
        // Filtres par rôle
        if ($user->hasRole('assistant')) {
            $query->where('assistant_id', $user->id);
        } elseif ($user->hasRole('eleve')) {
            $query->where('etudiant_id', $user->id);
        }
        
        // Filtres de date
        if ($request->has('date_debut')) {
            $query->where('date_paiement', '>=', $request->date_debut);
        }
        
        if ($request->has('date_fin')) {
            $query->where('date_paiement', '<=', $request->date_fin);
        }
        
        // Calcul des statistiques
        $stats = [
            'total' => $query->count(),
            'montant_total' => $query->sum('montant'),
            'par_statut' => $query->select('statut', DB::raw('count(*) as total'), DB::raw('sum(montant) as montant_total'))
                ->groupBy('statut')
                ->get()
                ->keyBy('statut'),
            'par_mois' => $query->select(
                    DB::raw('DATE_FORMAT(date_paiement, "%Y-%m") as mois'),
                    DB::raw('count(*) as total'),
                    DB::raw('sum(montant) as montant_total')
                )
                ->groupBy('mois')
                ->orderBy('mois')
                ->get(),
            'par_matiere' => $query->join('matieres', 'paiements.matiere_id', '=', 'matieres.id')
                ->select('matieres.nom', DB::raw('count(*) as total'), DB::raw('sum(paiements.montant) as montant_total'))
                ->whereNotNull('paiements.matiere_id')
                ->groupBy('matieres.nom')
                ->get(),
            'par_pack' => $query->join('packs', 'paiements.pack_id', '=', 'packs.id')
                ->select('packs.nom', DB::raw('count(*) as total'), DB::raw('sum(paiements.montant) as montant_total'))
                ->whereNotNull('paiements.pack_id')
                ->groupBy('packs.nom')
                ->get(),
        ];
        
        return response()->json($stats);
    }

    /**
     * Vérifie si un étudiant a déjà un paiement pour un pack/mois donné
     */
    public function checkExistingPaiement(Request $request)
    {
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:users,id',
            'pack_id' => 'nullable|exists:packs,id',
            'matiere_id' => 'nullable|exists:matieres,id',
            'mois_periode' => 'nullable|date_format:Y-m',
        ]);
        
        $query = Paiement::where('etudiant_id', $validated['etudiant_id'])
            ->where('statut', '!=', 'annule');
            
        if (!empty($validated['pack_id'])) {
            $query->where('pack_id', $validated['pack_id']);
        }
        
        if (!empty($validated['matiere_id'])) {
            $query->where('matiere_id', $validated['matiere_id']);
        }
        
        if (!empty($validated['mois_periode'])) {
            $query->where('mois_periode', $validated['mois_periode']);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Un paiement existe déjà pour cette période.' : 'Aucun paiement trouvé pour cette période.'
        ]);
    }

    /**
     * Récupère l'historique des paiements d'un étudiant
     */
    public function getStudentPayments($etudiantId)
    {
        $paiements = Paiement::with(['matiere:id,nom', 'pack:id,nom', 'tarif:id,nom,montant'])
            ->where('etudiant_id', $etudiantId)
            ->orderBy('date_paiement', 'desc')
            ->get();
            
        return response()->json($paiements);
    }

    /**
     * Exporte les paiements au format Excel
     */
    public function export(Request $request)
    {
        $query = Paiement::with(['etudiant:id,name,email', 'matiere:id,nom', 'pack:id,nom', 'assistant:id,name']);
        
        // Appliquer les filtres
        if ($request->has('date_debut')) {
            $query->where('date_paiement', '>=', $request->date_debut);
        }
        
        if ($request->has('date_fin')) {
            $query->where('date_paiement', '<=', $request->date_fin);
        }
        
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }
        
        if ($request->has('mode_paiement')) {
            $query->where('mode_paiement', $request->mode_paiement);
        }
        
        $paiements = $query->get();
        
        $fileName = 'export-paiements-' . now()->format('Y-m-d') . '.xlsx';
        
        return (new \App\Exports\PaiementsExport($paiements))->download($fileName);
    }
}
