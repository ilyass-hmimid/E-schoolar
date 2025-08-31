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
        // Les administrateurs et la direction ont accès à tous les rapports
        if ($user->hasAnyRole(['admin', 'direction'])) {
            return true;
        }
        
        // Les chefs de département peuvent voir les rapports de leur département
        if ($user->hasRole('chef_departement')) {
            return true;
        }
        
        // Vérifier la permission spécifique
        return $user->can('view_any_rapport');
    }

    /**
     * Détermine si l'utilisateur peut générer des rapports
     */
    public function generate(User $user, string $reportType = null): bool
    {
        // Vérifier si l'utilisateur a un rôle autorisé
        if (!$user->hasAnyRole(['admin', 'direction', 'chef_departement', 'secretaire'])) {
            return false;
        }
        
        // Vérifier les permissions spécifiques selon le type de rapport
        if ($reportType) {
            switch ($reportType) {
                case 'financier':
                    return $user->can('generate_financial_report');
                case 'pédagogique':
                    return $user->can('generate_educational_report');
                case 'effectifs':
                    return $user->can('generate_enrollment_report');
                default:
                    return $user->can('generate_rapport');
            }
        }
        
        // Permission générique pour la génération de rapports
        return $user->can('generate_rapport');
    }
    
    /**
     * Détermine si l'utilisateur peut voir les statistiques
     */
    public function viewStats(User $user, string $statType = null): bool
    {
        // Les administrateurs et la direction ont accès à toutes les statistiques
        if ($user->hasAnyRole(['admin', 'direction'])) {
            return true;
        }
        
        // Vérifier les permissions spécifiques selon le type de statistiques
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
     * Détermine si l'utilisateur peut exporter des données
     */
    public function export(User $user, string $dataType = null): bool
    {
        // Vérifier si l'utilisateur a un rôle autorisé
        if (!$user->hasAnyRole(['admin', 'direction', 'secretaire', 'professeur'])) {
            return false;
        }
        
        // Vérifier les permissions spécifiques selon le type de données
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
     * Détermine si l'utilisateur peut accéder aux rapports financiers
     */
    public function viewFinancialReports(User $user): bool
    {
        // Les administrateurs et la direction ont accès aux rapports financiers
        if ($user->hasAnyRole(['admin', 'direction', 'comptable'])) {
            return true;
        }
        
        // Vérifier la permission spécifique
        return $user->can('view_financial_reports');
    }
    
    /**
     * Détermine si l'utilisateur peut accéder aux rapports pédagogiques
     */
    public function viewEducationalReports(User $user): bool
    {
        // Les administrateurs, la direction et les chefs de département ont accès aux rapports pédagogiques
        if ($user->hasAnyRole(['admin', 'direction', 'chef_departement'])) {
            return true;
        }
        
        // Vérifier la permission spécifique
        return $user->can('view_educational_reports');
    }
    
    /**
     * Détermine si l'utilisateur peut accéder aux rapports d'effectifs
     */
    public function viewEnrollmentReports(User $user): bool
    {
        // Les administrateurs, la direction et les secrétaires ont accès aux rapports d'effectifs
        if ($user->hasAnyRole(['admin', 'direction', 'secretaire'])) {
            return true;
        }
        
        // Vérifier la permission spécifique
        return $user->can('view_enrollment_reports');
    }
    
    /**
     * Détermine si l'utilisateur peut accéder aux rapports de présence
     */
    public function viewAttendanceReports(User $user): bool
    {
        // Les administrateurs, la direction, les professeurs et les secrétaires ont accès aux rapports de présence
        if ($user->hasAnyRole(['admin', 'direction', 'professeur', 'secretaire'])) {
            return true;
        }
        
        // Vérifier la permission spécifique
        return $user->can('view_attendance_reports');
    }
    
    /**
     * Détermine si l'utilisateur peut accéder aux rapports personnalisés
     */
    public function createCustomReport(User $user): bool
    {
        // Seuls les administrateurs et la direction peuvent créer des rapports personnalisés
        if (!$user->hasAnyRole(['admin', 'direction'])) {
            return false;
        }
        
        // Vérifier la permission spécifique
        return $user->can('create_custom_report');
    }
    
    /**
     * Détermine si l'utilisateur peut planifier des rapports récurrents
     */
    public function scheduleRecurringReport(User $user): bool
    {
        // Seuls les administrateurs et la direction peuvent planifier des rapports récurrents
        if (!$user->hasAnyRole(['admin', 'direction'])) {
            return false;
        }
        
        // Vérifier la permission spécifique
        return $user->can('schedule_recurring_report');
    }
}
