<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Absence;
use App\Models\User;
use App\Models\Evenement;
use App\Models\Classe;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssistantDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'assistant
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $aujourdhui = now();
        
        // Récupérer les données pour le tableau de bord
        $stats = [
            // Statistiques principales
            'total_eleves' => User::role('eleve')->count(),
            'absences_jour' => Absence::whereDate('date_absence', $aujourdhui)->count(),
            'paiements_mois' => Paiement::whereMonth('date_paiement', $aujourdhui->month)
                ->whereYear('date_paiement', $aujourdhui->year)
                ->sum('montant'),
            'evenements_semaine' => Evenement::whereBetween('date_debut', [
                $aujourdhui->startOfDay(),
                $aujourdhui->copy()->addDays(7)->endOfDay()
            ])->count(),
                
            // Statistiques supplémentaires
            'paiements_attente' => Paiement::where('statut', 'en_attente')
                ->whereMonth('date_paiement', $aujourdhui->month)
                ->sum('montant'),
            'classes_actives' => Classe::where('is_active', true)->count()
        ];
        
        // Récupérer les dernières absences
        $dernieres_absences = Absence::with(['eleve', 'matiere'])
            ->latest()
            ->take(5)
            ->get();
            
        // Récupérer les prochains événements
        $prochains_evenements = Evenement::where('date_debut', '>=', $aujourdhui->startOfDay())
            ->orderBy('date_debut')
            ->take(5)
            ->get();
        
        return view('assistant.dashboard', [
            'stats' => $stats,
            'dernieres_absences' => $dernieres_absences,
            'prochains_evenements' => $prochains_evenements,
            'aujourdhui' => $aujourdhui->format('Y-m-d')
        ]);
    }
}
