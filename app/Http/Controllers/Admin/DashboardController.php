<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Paiement;
use App\Models\Enseignement;
use App\Enums\RoleType;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'administration
     */
    public function index()
    {
        $now = Carbon::now();
        
        // Données pour les cartes de statistiques
        $elevesCount = User::where('role', RoleType::ELEVE->value)->count();
        $professeursCount = User::where('role', RoleType::PROFESSEUR->value)->count();
        $coursCount = Enseignement::where('date_fin', '>=', now())->count();
        $revenus = Paiement::where('statut', 'valide')
            ->whereYear('date_paiement', $now->year)
            ->sum('montant');
        
        // Données pour les activités récentes
        $activites = [
            [
                'icon' => 'fa-user-graduate',
                'titre' => 'Nouvelle inscription',
                'description' => '5 nouveaux élèves se sont inscrits cette semaine',
                'date' => $now->copy()->subDays(2)->format('d/m/Y H:i')
            ],
            [
                'icon' => 'fa-chalkboard-teacher',
                'titre' => 'Nouveau professeur',
                'description' => 'M. Ahmed a rejoint notre équipe de professeurs',
                'date' => $now->copy()->subDays(5)->format('d/m/Y H:i')
            ],
            [
                'icon' => 'fa-book',
                'titre' => 'Nouveau cours',
                'description' => 'Un nouveau cours de Mathématiques a été ajouté',
                'date' => $now->copy()->subWeek()->format('d/m/Y H:i')
            ]
        ];
        
        // Données pour le graphique des revenus
        $revenusParMois = [];
        $labels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $mois = $date->isoFormat('MMM');
            $annee = $date->year;
            
            $labels[] = "$mois $annee";
            
            $revenu = Paiement::whereYear('date_paiement', $annee)
                ->whereMonth('date_paiement', $date->month)
                ->where('statut', 'valide')
                ->sum('montant');
            
            $revenusParMois[] = (float) $revenu;
        }
        
        return view('admin.dashboard', [
            'elevesCount' => $elevesCount,
            'professeursCount' => $professeursCount,
            'coursCount' => $coursCount,
            'revenus' => $revenus,
            'activites' => $activites,
            'revenusParMois' => $revenusParMois,
            'labels' => $labels
        ]);
    }
}
