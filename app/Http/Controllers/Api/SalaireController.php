<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Salaire;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SalairePaidNotification;
use Illuminate\Validation\Rule;

class SalaireController extends Controller
{
    /**
     * Enregistrer un paiement de salaire
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function enregistrerPaiement(Request $request, $id)
    {
        $salaire = Salaire::findOrFail($id);
        
        // Vérifier si le salaire est déjà payé
        if ($salaire->statut === 'paye') {
            return response()->json([
                'success' => false,
                'message' => 'Ce salaire a déjà été payé.'
            ], 400);
        }
        
        // Valider les données de la requête
        $validated = $request->validate([
            'date_paiement' => 'required|date',
            'type_paiement' => ['required', Rule::in(['virement', 'cheque', 'especes'])],
            'reference' => 'nullable|string|max:100',
            'notifier_professeur' => 'boolean',
        ]);
        
        // Démarrer une transaction de base de données
        DB::beginTransaction();
        
        try {
            // Mettre à jour le salaire
            $salaire->update([
                'statut' => 'paye',
                'date_paiement' => $validated['date_paiement'],
                'type_paiement' => $validated['type_paiement'],
                'reference' => $validated['reference'] ?? null,
            ]);
            
            // Enregistrer une activité
            activity()
                ->causedBy(auth()->user())
                ->performedOn($salaire)
                ->withProperties([
                    'date_paiement' => $validated['date_paiement'],
                    'type_paiement' => $validated['type_paiement'],
                    'montant' => $salaire->salaire_net,
                ])
                ->log('Paiement enregistré');
            
            // Envoyer une notification au professeur si demandé
            if ($request->boolean('notifier_professeur')) {
                $professeur = $salaire->professeur;
                if ($professeur) {
                    $professeur->notify(new SalairePaidNotification($salaire));
                }
            }
            
            // Valider la transaction
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Le paiement a été enregistré avec succès.',
                'data' => $salaire->fresh()
            ]);
            
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement du paiement.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Générer la fiche de paie au format PDF
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function genererFichePaie($id)
    {
        $salaire = Salaire::with(['professeur', 'professeur.user'])->findOrFail($id);
        
        // Ici, vous pouvez utiliser une bibliothèque comme barryvdh/laravel-dompdf
        // pour générer un PDF à partir d'une vue
        
        // Exemple avec DomPDF (à décommenter et configurer si nécessaire)
        /*
        $pdf = \PDF::loadView('admin.salaires.fiche_paie', [
            'salaire' => $salaire
        ]);
        
        return $pdf->download("fiche-paie-{$salaire->id}.pdf");
        */
        
        // Pour l'instant, on retourne une réponse JSON avec les données
        return response()->json([
            'success' => true,
            'message' => 'Fonctionnalité de génération de PDF à implémenter',
            'data' => $salaire
        ]);
    }
    
    /**
     * Obtenir les statistiques des salaires
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistiques(Request $request)
    {
        $annee = $request->input('annee', now()->year);
        $professeurId = $request->input('professeur_id');
        
        $query = Salaire::query()
            ->select(
                DB::raw('MONTH(periode) as mois'),
                DB::raw('SUM(salaire_brut) as total_brut'),
                DB::raw('SUM(salaire_net) as total_net'),
                DB::raw('COUNT(*) as nombre_salaires')
            )
            ->whereYear('periode', $annee)
            ->groupBy('mois')
            ->orderBy('mois');
        
        if ($professeurId) {
            $query->where('professeur_id', $professeurId);
        }
        
        $statistiques = $query->get();
        
        // Formater les données pour le graphique
        $mois = [];
        $salairesBruts = [];
        $salairesNets = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $mois[] = Carbon::createFromDate($annee, $i, 1)->format('M');
            $stat = $statistiques->firstWhere('mois', $i);
            $salairesBruts[] = $stat ? (float) $stat->total_brut : 0;
            $salairesNets[] = $stat ? (float) $stat->total_net : 0;
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $mois,
                'datasets' => [
                    [
                        'label' => 'Salaires bruts',
                        'data' => $salairesBruts,
                        'backgroundColor' => 'rgba(79, 70, 229, 0.6)',
                        'borderColor' => 'rgba(79, 70, 229, 1)',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'Salaires nets',
                        'data' => $salairesNets,
                        'backgroundColor' => 'rgba(16, 185, 129, 0.6)',
                        'borderColor' => 'rgba(16, 185, 129, 1)',
                        'borderWidth' => 1
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * Exporter les salaires au format Excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exporterExcel(Request $request)
    {
        $query = Salaire::with(['professeur']);
        
        // Filtrer par période
        if ($request->has('date_debut') && $request->has('date_fin')) {
            $query->whereBetween('periode', [
                $request->date_debut,
                $request->date_fin
            ]);
        }
        
        // Filtrer par statut
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }
        
        // Filtrer par professeur
        if ($request->has('professeur_id')) {
            $query->where('professeur_id', $request->professeur_id);
        }
        
        $salaires = $query->get();
        
        // Ici, vous pouvez utiliser une bibliothèque comme Maatwebsite/Excel
        // pour générer un fichier Excel
        
        // Exemple avec Laravel Excel (à décommenter et configurer si nécessaire)
        /*
        return Excel::download(new SalairesExport($salaires), "salaires-{$request->date_debut}-a-{$request->date_fin}.xlsx");
        */
        
        // Pour l'instant, on retourne une réponse JSON avec les données
        return response()->json([
            'success' => true,
            'message' => 'Fonctionnalité d\'export Excel à implémenter',
            'data' => $salaires
        ]);
    }
}
