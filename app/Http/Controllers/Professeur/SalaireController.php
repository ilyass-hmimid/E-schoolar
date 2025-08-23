<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use App\Models\Salaire;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SalaireController extends Controller
{
    /**
     * Affiche la liste des salaires du professeur
     */
    public function index()
    {
        $professeur = Auth::user();
        
        // Récupérer les salaires du professeur connecté
        $salaires = $professeur->salaires()
            ->orderBy('date_paiement', 'desc')
            ->get()
            ->map(function($salaire) {
                return [
                    'id' => $salaire->id,
                    'reference' => $salaire->reference,
                    'periode' => $salaire->mois . ' ' . $salaire->annee,
                    'date_paiement' => $salaire->date_paiement->format('d/m/Y'),
                    'montant_brut' => $salaire->montant_brut,
                    'montant_net' => $salaire->montant_net,
                    'statut' => $this->getStatutSalaire($salaire)
                ];
            });
        
        return Inertia::render('Professeur/Salaires/Index', [
            'salaires' => $salaires
        ]);
    }

    /**
     * Affiche les détails d'un salaire
     */
    public function show($id)
    {
        $professeur = Auth::user();
        
        // Récupérer le salaire du professeur connecté
        $salaire = $professeur->salaires()
            ->with(['professeur.user'])
            ->findOrFail($id);
        
        // Formater les données pour la vue
        $salaireData = [
            'id' => $salaire->id,
            'reference' => $salaire->reference,
            'periode' => $salaire->mois . ' ' . $salaire->annee,
            'date_paiement' => $salaire->date_paiement,
            'statut' => $this->getStatutSalaire($salaire),
            'montant_brut' => $salaire->montant_brut,
            'montant_net' => $salaire->montant_net,
            'gains' => [
                ['libelle' => 'Salaire de base', 'montant' => $salaire->salaire_base],
                ['libelle' => 'Heures supplémentaires', 'montant' => $salaire->heures_supplementaires],
                ['libelle' => 'Primes', 'montant' => $salaire->primes],
                ['libelle' => 'Indemnités', 'montant' => $salaire->indemnites],
            ],
            'retenues' => [
                ['libelle' => 'CNSS', 'montant' => $salaire->cnss],
                ['libelle' => 'Retenue à la source', 'montant' => $salaire->retenue_source],
                ['libelle' => 'Autres retenues', 'montant' => $salaire->autres_retenues],
            ],
            'notes' => $salaire->notes
        ];
        
        return Inertia::render('Professeur/Salaires/Show', [
            'salaire' => $salaireData
        ]);
    }
    
    /**
     * Détermine le statut du salaire
     */
    private function getStatutSalaire($salaire)
    {
        $today = Carbon::today();
        $datePaiement = Carbon::parse($salaire->date_paiement);
        
        if ($salaire->est_paye) {
            return 'payé';
        } elseif ($datePaiement->isFuture()) {
            return 'en attente';
        } else {
            return 'en retard';
        }
    }
}
