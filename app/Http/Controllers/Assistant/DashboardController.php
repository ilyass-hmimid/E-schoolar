<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Evenement;
use App\Models\Classe;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'assistant
     */
    public function index()
    {
        $aujourdhui = now();
        $debutMois = $aujourdhui->copy()->startOfMonth();
        $finMois = $aujourdhui->copy()->endOfMonth();
        
        // Statistiques générales
        $stats = [
            'absences_aujourdhui' => Absence::whereDate('date', $aujourdhui->format('Y-m-d'))->count(),
            'paiements_attente' => Paiement::where('statut', 'en_attente')
                ->whereMonth('date_paiement', $aujourdhui->month)
                ->sum('montant'),
            'evenements_a_venir' => Evenement::where('date_debut', '>=', $aujourdhui)
                ->where('date_debut', '<=', $aujourdhui->copy()->addDays(7))
                ->count(),
            'total_eleves' => Eleve::count(),
            'total_classes' => Classe::count(),
            'nouveaux_eleves' => Eleve::whereBetween('created_at', [$debutMois, $finMois])->count(),
            'taux_absenteisme' => $this->calculerTauxAbsenteisme($debutMois, $finMois)
        ];
        
        // Derniers paiements
        $derniersPaiements = Paiement::with(['eleve', 'utilisateur'])
            ->where('statut', 'valide')
            ->orderBy('date_paiement', 'desc')
            ->take(5)
            ->get()
            ->map(function($paiement) {
                return [
                    'id' => $paiement->id,
                    'eleve' => $paiement->eleve ? $paiement->eleve->nom . ' ' . $paiement->eleve->prenom : 'Anonyme',
                    'montant' => number_format($paiement->montant, 2, ',', ' '),
                    'date' => $paiement->date_paiement->format('d/m/Y'),
                    'mode' => $paiement->mode_paiement,
                    'enregistre_par' => $paiement->utilisateur ? $paiement->utilisateur->name : 'Système'
                ];
            });
            
        // Dernières absences
        $dernieresAbsences = Absence::with(['eleve', 'classe'])
            ->whereDate('date', '>=', $aujourdhui->copy()->subDays(7))
            ->orderBy('date', 'desc')
            ->take(5)
            ->get()
            ->map(function($absence) {
                return [
                    'id' => $absence->id,
                    'eleve' => $absence->eleve->nom . ' ' . $absence->eleve->prenom,
                    'classe' => $absence->classe ? $absence->classe->nom : 'Non défini',
                    'date' => $absence->date->format('d/m/Y'),
                    'justifiee' => $absence->justifiee,
                    'motif' => $absence->motif
                ];
            });
            
        // Prochains événements
        $prochainsEvenements = Evenement::with('createur')
            ->where('date_fin', '>=', $aujourdhui)
            ->orderBy('date_debut')
            ->take(5)
            ->get()
            ->map(function($evenement) {
                return [
                    'id' => $evenement->id,
                    'titre' => $evenement->titre,
                    'date_debut' => Carbon::parse($evenement->date_debut)->format('d/m/Y H:i'),
                    'date_fin' => Carbon::parse($evenement->date_fin)->format('d/m/Y H:i'),
                    'type' => $evenement->type,
                    'createur' => $evenement->createur ? $evenement->createur->name : 'Système'
                ];
            });
        
        return Inertia::render('Assistant/Dashboard', [
            'stats' => $stats,
            'derniersPaiements' => $derniersPaiements,
            'dernieresAbsences' => $dernieresAbsences,
            'prochainsEvenements' => $prochainsEvenements
        ]);
    }
    
    /**
     * Calcule le taux d'absentéisme global
     */
    private function calculerTauxAbsenteisme($debut, $fin)
    {
        $total_eleves = Eleve::count();
        if ($total_eleves === 0) {
            return 0;
        }
        
        $jours_ouvres = $this->compterJoursOuvres($debut, $fin);
        $total_jours_eleves = $total_eleves * $jours_ouvres;
        
        if ($total_jours_eleves === 0) {
            return 0;
        }
        
        $total_absences = Absence::whereBetween('date', [
            $debut->format('Y-m-d'),
            $fin->format('Y-m-d')
        ])->count();
        
        return round(($total_absences / $total_jours_eleves) * 100, 2);
    }
    
    /**
     * Compte le nombre de jours ouvrés entre deux dates
     */
    private function compterJoursOuvres($debut, $fin)
    {
        $jours = 0;
        $date = $debut->copy();
        
        while ($date->lte($fin)) {
            if ($date->isWeekday()) {
                $jours++;
            }
            $date->addDay();
        }
        
        return $jours;
    }
}
