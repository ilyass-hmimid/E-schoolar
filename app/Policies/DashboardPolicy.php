<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut accéder au tableau de bord administrateur
     */
    public function viewAdminDashboard(User $user): bool
    {
        // Les administrateurs et la direction ont accès complet au tableau de bord
        if ($user->hasAnyRole(['admin', 'direction'])) {
            return true;
        }
        
        // Les chefs de département ont accès à une version limitée du tableau de bord
        if ($user->hasRole('chef_departement')) {
            return true;
        }
        
        // Vérifier la permission spécifique
        return $user->can('view_admin_dashboard');
    }
    
    /**
     * Détermine si l'utilisateur peut effectuer des recherches avancées
     */
    public function search(User $user, ?string $scope = null): bool
    {
        // Les administrateurs et la direction peuvent effectuer toutes les recherches
        if ($user->hasAnyRole(['admin', 'direction'])) {
            return true;
        }
        
        // Gestion des scopes de recherche spécifiques
        if ($scope) {
            $permission = 'search_' . strtolower($scope);
            if ($user->can($permission)) {
                return true;
            }
        }
        
        // Permission générique de recherche
        return $user->can('search') || $this->viewAdminDashboard($user);
    }
    
    /**
     * Détermine si l'utilisateur peut voir les statistiques
     */
    public function viewStats(User $user, ?string $statType = null): bool
    {
        // Les administrateurs et la direction peuvent voir toutes les statistiques
        if ($user->hasAnyRole(['admin', 'direction'])) {
            return true;
        }
        
        // Gestion des types de statistiques spécifiques
        if ($statType) {
            $permission = 'view_' . strtolower($statType) . '_stats';
            if ($user->can($permission)) {
                return true;
            }
        }
        
        // Permission générique pour voir les statistiques
        return $user->can('view_stats');
    }
    
    /**
     * Détermine si l'utilisateur peut voir les rapports
     */
    public function viewReports(User $user, ?string $reportType = null): bool
    {
        // Les administrateurs et la direction peuvent voir tous les rapports
        if ($user->hasAnyRole(['admin', 'direction'])) {
            return true;
        }
        
        // Gestion des types de rapports spécifiques
        if ($reportType) {
            $permission = 'view_' . strtolower($reportType) . '_reports';
            if ($user->can($permission)) {
                return true;
            }
        }
        
        // Permission générique pour voir les rapports
        return $user->can('view_reports');
    }
    
    /**
     * Détermine si l'utilisateur peut voir les alertes
     */
    public function viewAlerts(User $user, ?string $alertType = null): bool
    {
        // Les administrateurs et la direction peuvent voir toutes les alertes
        if ($user->hasAnyRole(['admin', 'direction'])) {
            return true;
        }
        
        // Gestion des types d'alertes spécifiques
        if ($alertType) {
            $permission = 'view_' . strtolower($alertType) . '_alerts';
            if ($user->can($permission)) {
                return true;
            }
        }
        
        // Permission générique pour voir les alertes
        return $user->can('view_alerts');
    }
    
    /**
     * Détermine si l'utilisateur peut exporter des données
     */
    public function exportData(User $user, ?string $dataType = null): bool
    {
        // Les administrateurs et la direction peuvent exporter toutes les données
        if ($user->hasAnyRole(['admin', 'direction'])) {
            return true;
        }
        
        // Gestion des types de données spécifiques pour l'exportation
        if ($dataType) {
            $permission = 'export_' . strtolower($dataType);
            if ($user->can($permission)) {
                return true;
            }
        }
        
        // Permission générique pour l'exportation de données
        return $user->can('export_data');
    }
    
    /**
     * Détermine si l'utilisateur peut personnaliser son tableau de bord
     */
    public function customizeDashboard(User $user): bool
    {
        // Les administrateurs, la direction et les chefs de département peuvent personnaliser leur tableau de bord
        return $user->hasAnyRole(['admin', 'direction', 'chef_departement']) || 
               $user->can('customize_dashboard');
    }
    
    /**
     * Détermine si l'utilisateur peut accéder aux outils d'analyse avancée
     */
    public function accessAnalyticsTools(User $user): bool
    {
        // Seuls les administrateurs et la direction ont accès aux outils d'analyse avancée
        return $user->hasAnyRole(['admin', 'direction']) || 
               $user->can('access_analytics_tools');
    }
    
    /**
     * Détermine si l'utilisateur peut configurer les widgets du tableau de bord
     */
    public function configureWidgets(User $user): bool
    {
        // Les administrateurs et la direction peuvent configurer les widgets
        if ($user->hasAnyRole(['admin', 'direction'])) {
            return true;
        }
        
        // Les chefs de département peuvent configurer certains widgets
        if ($user->hasRole('chef_departement')) {
            return true;
        }
        
        return $user->can('configure_widgets');
    }
    
    /**
     * Détermine si l'utilisateur peut accéder aux tableaux de bord partagés
     */
    public function viewSharedDashboards(User $user): bool
    {
        // Les administrateurs et la direction peuvent voir tous les tableaux de bord partagés
        if ($user->hasAnyRole(['admin', 'direction'])) {
            return true;
        }
        
        // Les chefs de département peuvent voir les tableaux de bord partagés avec leur département
        if ($user->hasRole('chef_departement')) {
            return true;
        }
        
        return $user->can('view_shared_dashboards');
    }
}
