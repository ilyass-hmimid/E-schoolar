<?php

namespace App\Http\Controllers;

use App\Services\GestionSalaireService;
use Illuminate\Http\Request;

class TestSalaireController extends Controller
{
    protected $gestionSalaireService;

    public function __construct(GestionSalaireService $gestionSalaireService)
    {
        $this->gestionSalaireService = $gestionSalaireService;
    }

    /**
     * Teste le calcul des salaires pour un mois donné
     */
    public function testCalculerSalaires(string $moisPeriode = null)
    {
        $mois = $moisPeriode ?: now()->format('Y-m');
        $resultat = $this->gestionSalaireService->calculerSalaires($mois);
        
        return response()->json($resultat);
    }

    /**
     * Teste la validation de paiement d'un salaire
     */
    public function testValiderPaiement($salaireId)
    {
        $salaire = \App\Models\Salaire::findOrFail($salaireId);
        $resultat = $this->gestionSalaireService->validerPaiement($salaire);
        
        return response()->json($resultat);
    }

    /**
     * Teste l'annulation d'un salaire
     */
    public function testAnnulerSalaire($salaireId, Request $request)
    {
        $salaire = \App\Models\Salaire::findOrFail($salaireId);
        $raison = $request->input('raison', 'Raison non spécifiée');
        $resultat = $this->gestionSalaireService->annulerSalaire($salaire, $raison);
        
        return response()->json($resultat);
    }
}
