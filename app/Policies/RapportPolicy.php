<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RapportPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut accéder à la liste des rapports
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_rapport');
    }

    /**
     * Détermine si l'utilisateur peut générer des rapports
     */
    public function generate(User $user): bool
    {
        return $user->can('generate_rapport');
    }
    
    /**
     * Détermine si l'utilisateur peut voir les statistiques
     */
    public function viewStats(User $user): bool
    {
        return $user->can('view_stats');
    }
    
    /**
     * Détermine si l'utilisateur peut exporter des données
     */
    public function export(User $user): bool
    {
        return $user->can('export_data');
    }
    
    /**
     * Détermine si l'utilisateur peut accéder aux rapports financiers
     */
    public function viewFinancialReports(User $user): bool
    {
        return $user->can('view_financial_reports');
    }
    
    /**
     * Détermine si l'utilisateur peut accéder aux rapports pédagogiques
     */
    public function viewEducationalReports(User $user): bool
    {
        return $user->can('view_educational_reports');
    }
    
    /**
     * Détermine si l'utilisateur peut accéder aux rapports d'effectifs
     */
    public function viewEnrollmentReports(User $user): bool
    {
        return $user->can('view_enrollment_reports');
    }
}
