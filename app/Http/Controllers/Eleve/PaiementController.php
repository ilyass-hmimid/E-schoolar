<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Facture;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaiementController extends Controller
{
    /**
     * Affiche l'historique des paiements de l'élève
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        
        // Récupérer les paiements de l'élève
        $paiements = Paiement::where('eleve_id', $user->id)
            ->with(['facture'])
            ->orderBy('date_paiement', 'desc')
            ->paginate(10);
        
        // Calculer le total des paiements
        $totalPaye = Paiement::where('eleve_id', $user->id)
            ->where('statut', 'payé')
            ->sum('montant');
        
        // Calculer le montant total des factures impayées
        $totalFactures = Facture::where('eleve_id', $user->id)
            ->where('statut', '!=', 'payée')
            ->sum('montant_total');
            
        $resteAPayer = max(0, $totalFactures - $totalPaye);
        
        // Récupérer les prochains paiements à effectuer
        $prochainsPaiements = Facture::where('eleve_id', $user->id)
            ->where('date_echeance', '>=', now())
            ->where('statut', '!=', 'payée')
            ->orderBy('date_echeance')
            ->take(3)
            ->get();
        
        // Statistiques des paiements par mois pour le graphique
        $statistiquesPaiements = Paiement::select(
                DB::raw('YEAR(date_paiement) as annee'),
                DB::raw('MONTH(date_paiement) as mois'),
                DB::raw('SUM(montant) as total')
            )
            ->where('eleve_id', $user->id)
            ->where('statut', 'payé')
            ->where('date_paiement', '>=', now()->subMonths(6))
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();
            
        // Préparer les données pour le graphique
        $labels = [];
        $donnees = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $mois = $date->format('m/Y');
            $labels[] = $date->locale('fr')->shortMonthName . ' ' . $date->year;
            
            $paiementMois = $statistiquesPaiements->first(function ($item) use ($date) {
                return $item->annee == $date->year && $item->mois == $date->month;
            });
            
            $donnees[] = $paiementMois ? (float)$paiementMois->total : 0;
        }
        
        return view('eleve.paiements.index', [
            'paiements' => $paiements,
            'totalPaye' => $totalPaye,
            'resteAPayer' => $resteAPayer,
            'prochainsPaiements' => $prochainsPaiements,
            'labelsGraphique' => $labels,
            'donneesGraphique' => $donnees,
        ]);
    }
    
    /**
     * Affiche les détails d'un paiement
     *
     * @param  \App\Models\Paiement  $paiement
     * @return \Illuminate\View\View
     */
    public function show(Paiement $paiement)
    {
        $this->authorize('view', $paiement);
        
        // Charger les relations nécessaires
        $paiement->load([
            'eleve',
            'facture.lignes',
            'facture.eleve.user',
            'facture.creancier'
        ]);
        
        // Récupérer l'historique des paiements pour la même facture
        $historiquePaiements = Paiement::where('facture_id', $paiement->facture_id)
            ->where('id', '!=', $paiement->id)
            ->orderBy('date_paiement', 'desc')
            ->get();
        
        // Calculer le montant total payé pour la facture
        $totalPaye = Paiement::where('facture_id', $paiement->facture_id)
            ->where('statut', 'payé')
            ->sum('montant');
            
        $resteAPayer = max(0, $paiement->facture->montant_total - $totalPaye);
        
        return view('eleve.paiements.show', [
            'paiement' => $paiement,
            'historiquePaiements' => $historiquePaiements,
            'totalPaye' => $totalPaye,
            'resteAPayer' => $resteAPayer,
        ]);
    }
}
