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
        return $user->hasRole('admin') || $user->can('view_admin_dashboard');
    }
    
    /**
     * Détermine si l'utilisateur peut effectuer des recherches avancées
     */
    public function search(User $user): bool
    {
        return $user->can('search') || $this->viewAdminDashboard($user);
    }
    
    /**
     * Détermine si l'utilisateur peut voir les statistiques
     */
    public function viewStats(User $user): bool
    {
        return $user->can('view_stats') || $this->viewAdminDashboard($user);
    }
    
    /**
     * Détermine si l'utilisateur peut voir les rapports
     */
    public function viewReports(User $user): bool
    {
        return $user->can('view_reports') || $this->viewAdminDashboard($user);
    }
    
    /**
     * Détermine si l'utilisateur peut voir les alertes
     */
    public function viewAlerts(User $user): bool
    {
        return $user->can('view_alerts') || $this->viewAdminDashboard($user);
    }
    
    /**
     * Détermine si l'utilisateur peut exporter des données
     */
    public function exportData(User $user): bool
    {
        return $user->can('export_data') || $this->viewAdminDashboard($user);
    }
}
