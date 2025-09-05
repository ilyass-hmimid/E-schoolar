<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\Pack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PaiementApiController extends Controller
{
    /**
     * Affiche la liste des paiements avec filtres
     */
    public function index(Request $request)
    {
        $query = Paiement::with(['eleve', 'matiere', 'pack']);
        
        // Filtre par élève
        if ($request->has('eleve_id')) {
            $query->where('eleve_id', $request->eleve_id);
        }
        
        // Filtre par matière
        if ($request->has('matiere_id')) {
            $query->where('matiere_id', $request->matiere_id);
        }
        
        // Filtre par statut
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }
        
        // Filtre par date
        if ($request->has('date_debut') && $request->has('date_fin')) {
            $query->whereBetween('date_paiement', [
                $request->date_debut,
                $request->date_fin
            ]);
        }
        
        // Tri et pagination
        $paiements = $query->orderBy('date_paiement', 'desc')
                          ->paginate($request->per_page ?? 15);
        
        return response()->json($paiements);
    }
    
    /**
     * Enregistre un nouveau paiement
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'matiere_id' => 'nullable|exists:matieres,id',
            'pack_id' => 'nullable|exists:packs,id',
            'montant' => 'required|numeric|min:0',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|in:espèce,chèque,virement,autre',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Vérifier qu'au moins une matière ou un pack est fourni
        if (empty($request->matiere_id) && empty($request->pack_id)) {
            return response()->json([
                'message' => 'Vous devez spécifier une matière ou un pack',
                'errors' => ['matiere_id' => ['Une matière ou un pack est requis']]
            ], 422);
        }
        
        try {
            DB::beginTransaction();
            
            $paiement = new Paiement();
            $paiement->eleve_id = $request->eleve_id;
            $paiement->matiere_id = $request->matiere_id;
            $paiement->pack_id = $request->pack_id;
            $paiement->montant = $request->montant;
            $paiement->date_paiement = $request->date_paiement;
            $paiement->mode_paiement = $request->mode_paiement;
            $paiement->reference = $request->reference;
            $paiement->notes = $request->notes;
            $paiement->statut = Paiement::STATUT_PAYE;
            $paiement->save();
            
            DB::commit();
            
            return response()->json([
                'message' => 'Paiement enregistré avec succès',
                'data' => $paiement->load(['eleve', 'matiere', 'pack'])
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'enregistrement du paiement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Affiche les détails d'un paiement
     */
    public function show($id)
    {
        $paiement = Paiement::with(['eleve', 'matiere', 'pack'])->find($id);
        
        if (!$paiement) {
            return response()->json([
                'message' => 'Paiement non trouvé'
            ], 404);
        }
        
        return response()->json($paiement);
    }
    
    /**
     * Met à jour un paiement existant
     */
    public function update(Request $request, $id)
    {
        $paiement = Paiement::find($id);
        
        if (!$paiement) {
            return response()->json([
                'message' => 'Paiement non trouvé'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'montant' => 'numeric|min:0',
            'date_paiement' => 'date',
            'mode_paiement' => 'in:espèce,chèque,virement,autre',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'statut' => 'in:' . implode(',', [
                Paiement::STATUT_PAYE,
                Paiement::STATUT_ANNULE,
                Paiement::STATUT_REMBOURSE
            ])
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            DB::beginTransaction();
            
            $paiement->fill($request->only([
                'montant', 'date_paiement', 'mode_paiement', 'reference', 'notes', 'statut'
            ]));
            
            $paiement->save();
            
            DB::commit();
            
            return response()->json([
                'message' => 'Paiement mis à jour avec succès',
                'data' => $paiement->load(['eleve', 'matiere', 'pack'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la mise à jour du paiement: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour du paiement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Supprime un paiement
     */
    public function destroy($id)
    {
        $paiement = Paiement::find($id);
        
        if (!$paiement) {
            return response()->json([
                'message' => 'Paiement non trouvé'
            ], 404);
        }
        
        try {
            $paiement->delete();
            
            return response()->json([
                'message' => 'Paiement supprimé avec succès'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du paiement: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression du paiement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Exporte les paiements au format CSV
     */
    public function export(Request $request)
    {
        $query = Paiement::with(['eleve', 'matiere', 'pack']);
        
        // Appliquer les mêmes filtres que pour l'index
        if ($request->has('eleve_id')) {
            $query->where('eleve_id', $request->eleve_id);
        }
        
        if ($request->has('matiere_id')) {
            $query->where('matiere_id', $request->matiere_id);
        }
        
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }
        
        if ($request->has('date_debut') && $request->has('date_fin')) {
            $query->whereBetween('date_paiement', [
                $request->date_debut,
                $request->date_fin
            ]);
        }
        
        $paiements = $query->orderBy('date_paiement', 'desc')->get();
        
        $fileName = 'paiements_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
        
        $callback = function() use ($paiements) {
            $file = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($file, [
                'ID', 'Élève', 'Matière', 'Pack', 'Montant', 'Date',
                'Mode de paiement', 'Référence', 'Statut', 'Notes'
            ]);
            
            // Données
            foreach ($paiements as $paiement) {
                fputcsv($file, [
                    $paiement->id,
                    $paiement->eleve ? $paiement->eleve->nom_complet : '',
                    $paiement->matiere ? $paiement->matiere->nom : '',
                    $paiement->pack ? $paiement->pack->nom : '',
                    number_format($paiement->montant, 2, ',', ' '),
                    $paiement->date_paiement->format('d/m/Y'),
                    $paiement->mode_paiement,
                    $paiement->reference,
                    $paiement->statut,
                    $paiement->notes
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Récupère les statistiques des paiements
     */
    public function getStats(Request $request)
    {
        $query = Paiement::query();
        
        // Filtres
        if ($request->has('date_debut') && $request->has('date_fin')) {
            $query->whereBetween('date_paiement', [
                $request->date_debut,
                $request->date_fin
            ]);
        }
        
        // Total des paiements
        $total = $query->sum('montant');
        
        // Nombre de paiements
        $count = $query->count();
        
        // Montant moyen
        $moyenne = $count > 0 ? $total / $count : 0;
        
        // Répartition par mode de paiement
        $modes = $query->clone()
                      ->select('mode_paiement', DB::raw('SUM(montant) as total'))
                      ->groupBy('mode_paiement')
                      ->pluck('total', 'mode_paiement');
        
        // Répartition par statut
        $statuts = $query->clone()
                        ->select('statut', DB::raw('COUNT(*) as count'))
                        ->groupBy('statut')
                        ->pluck('count', 'statut');
        
        // Évolution mensuelle
        $evolution = $query->clone()
                          ->select(
                              DB::raw('DATE_FORMAT(date_paiement, "%Y-%m") as mois'),
                              DB::raw('SUM(montant) as total')
                          )
                          ->groupBy('mois')
                          ->orderBy('mois')
                          ->get();
        
        return response()->json([
            'total' => $total,
            'nombre_paiements' => $count,
            'moyenne' => round($moyenne, 2),
            'par_mode_paiement' => $modes,
            'par_statut' => $statuts,
            'evolution_mensuelle' => $evolution
        ]);
    }
}
