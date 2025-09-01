<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Paiement;
use App\Models\Enseignement;
use App\Models\Cours;
use App\Enums\RoleType;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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
        $activitesRecentes = $this->getActivitesRecentes($now);
        
        // Données pour les prochains cours
        $prochainsCours = $this->getProchainsCours($now);
        
        // Données pour le graphique des revenus
        $revenusData = $this->getRevenusMensuels($now);
        
        return view('admin.dashboard', [
            'elevesCount' => $elevesCount,
            'professeursCount' => $professeursCount,
            'coursCount' => $coursCount,
            'revenus' => $revenus,
            'activitesRecentes' => $activitesRecentes,
            'prochainsCours' => $prochainsCours,
            'revenusParMois' => $revenusData['revenusParMois'],
            'labels' => $revenusData['labels']
        ]);
    }
    
    /**
     * Récupère les activités récentes
     */
    private function getActivitesRecentes(Carbon $now): array
    {
        // Dans une application réelle, ces données viendraient de la base de données
        return [
            [
                'titre' => 'Nouvelle inscription',
                'description' => '5 nouveaux élèves se sont inscrits',
                'date' => $now->copy()->subHours(2),
                'icone' => 'fas fa-user-plus',
                'couleur' => 'blue'
            ],
            [
                'titre' => 'Paiement reçu',
                'description' => 'Paiement de 1,200 DH pour le cours de Mathématiques',
                'date' => $now->copy()->subDays(1),
                'icone' => 'fas fa-money-bill-wave',
                'couleur' => 'green'
            ],
            [
                'titre' => 'Nouveau cours programmé',
                'description' => 'Cours de Physique-Chimie ajouté par M. Ahmed',
                'date' => $now->copy()->subDays(2),
                'icone' => 'fas fa-calendar-plus',
                'couleur' => 'purple'
            ],
            [
                'titre' => 'Mise à jour du système',
                'description' => 'Mise à jour vers la version 1.2.0 effectuée',
                'date' => $now->copy()->subDays(3),
                'icone' => 'fas fa-sync-alt',
                'couleur' => 'yellow'
            ],
            [
                'titre' => 'Nouveau professeur',
                'description' => 'Mme Leila a rejoint notre équipe',
                'date' => $now->copy()->subDays(5),
                'icone' => 'fas fa-chalkboard-teacher',
                'couleur' => 'pink'
            ]
        ];
    }
    
    /**
     * Récupère les prochains cours programmés
     */
    private function getProchainsCours(Carbon $now): array
    {
        // Dans une application réelle, ces données viendraient de la base de données
        return [
            [
                'matiere' => 'Mathématiques',
                'professeur' => 'M. Ahmed',
                'date' => $now->copy()->addDays(1)->setTime(14, 0),
                'heure_debut' => '14:00',
                'heure_fin' => '16:00',
                'statut' => 'Confirmé',
                'statut_couleur' => 'green',
                'icone' => 'fas fa-square-root-alt',
                'couleur' => 'blue'
            ],
            [
                'matiere' => 'Physique',
                'professeur' => 'Mme Leila',
                'date' => $now->copy()->addDays(2)->setTime(10, 0),
                'heure_debut' => '10:00',
                'heure_fin' => '12:00',
                'statut' => 'En attente',
                'statut_couleur' => 'yellow',
                'icone' => 'fas fa-atom',
                'couleur' => 'purple'
            ],
            [
                'matiere' => 'Français',
                'professeur' => 'M. Karim',
                'date' => $now->copy()->addDays(3)->setTime(15, 30),
                'heure_debut' => '15:30',
                'heure_fin' => '17:30',
                'statut' => 'Confirmé',
                'statut_couleur' => 'green',
                'icone' => 'fas fa-book',
                'couleur' => 'green'
            ]
        ];
    }
    
    /**
     * Récupère les données des revenus mensuels pour le graphique
     */
    private function getRevenusMensuels(Carbon $now): array
    {
        $revenusParMois = [];
        $labels = [];
        
        // Générer les données pour les 6 derniers mois
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $mois = $date->isoFormat('MMM');
            $annee = $date->year;
            
            $labels[] = $mois;
            
            // Dans une application réelle, on ferait une requête à la base de données
            // Ici, on génère des données aléatoires pour la démo
            $revenu = rand(8000, 20000);
            
            // Pour simuler une croissance progressive
            if ($i === 5) {
                $revenu = rand(8000, 12000);
            } elseif ($i === 0) {
                $revenu = rand(18000, 25000);
            }
            
            $revenusParMois[] = $revenu;
        }
        
        return [
            'revenusParMois' => $revenusParMois,
            'labels' => $labels
        ];
    }
}
