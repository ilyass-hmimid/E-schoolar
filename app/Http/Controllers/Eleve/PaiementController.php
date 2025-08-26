<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use Inertia\Inertia;

class PaiementController extends Controller
{
    /**
     * Affiche l'historique des paiements de l'élève
     */
    public function index()
    {
        $eleve = auth()->user();
        
        $paiements = Paiement::where('eleve_id', $eleve->id)
            ->orderBy('date_paiement', 'desc')
            ->paginate(10);
            
        // Calculer le total des paiements
        $totalPaye = Paiement::where('eleve_id', $eleve->id)
            ->where('statut', 'payé')
            ->sum('montant');
            
        // Calculer le montant restant à payer
        $montantTotal = 0; // À remplacer par la logique de calcul du montant total dû
        $resteAPayer = max(0, $montantTotal - $totalPaye);
            
        return Inertia::render('Eleve/Paiements/Index', [
            'paiements' => $paiements,
            'totalPaye' => $totalPaye,
            'resteAPayer' => $resteAPayer,
        ]);
    }
    
    /**
     * Affiche les détails d'un paiement
     */
    public function show(Paiement $paiement)
    {
        $this->authorize('view', $paiement);
        
        return Inertia::render('Eleve/Paiements/Show', [
            'paiement' => $paiement->load('eleve'),
        ]);
    }
}
