<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pack;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir n'importe quel modèle.
     */
    public function viewAny(User $user): bool
    {
        // Les administrateurs peuvent tout voir
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Les utilisateurs avec la permission spécifique peuvent voir
        return $user->can('view_any_pack');
    }

    /**
     * Détermine si l'utilisateur peut voir le modèle.
     */
    public function view(User $user, Pack $pack): bool
    {
        // Les administrateurs peuvent tout voir
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Vérifier si le pack est actif
        if (!$pack->is_active && !$user->can('view_inactive_pack')) {
            return false;
        }
        
        // Vérifier les permissions spécifiques
        return $user->can('view_pack');
    }

    /**
     * Détermine si l'utilisateur peut créer des modèles.
     */
    public function create(User $user): bool
    {
        // Seuls les administrateurs et les gestionnaires peuvent créer des packs
        if ($user->hasAnyRole(['admin', 'gestionnaire'])) {
            return true;
        }
        
        return $user->can('create_pack');
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour le modèle.
     */
    public function update(User $user, Pack $pack): bool
    {
        // Les administrateurs peuvent tout mettre à jour
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Vérifier si le pack est verrouillé
        if ($pack->is_locked) {
            return false;
        }
        
        // Vérifier les permissions spécifiques
        return $user->can('update_pack');
    }

    /**
     * Détermine si l'utilisateur peut supprimer le modèle.
     */
    public function delete(User $user, Pack $pack): bool
    {
        // Vérifier si l'utilisateur a la permission
        if (!$user->can('delete_pack')) {
            return false;
        }

        // Ne pas permettre la suppression si le pack est utilisé dans des ventes
        if ($pack->ventes()->exists()) {
            return false;
        }

        // Ne pas permettre la suppression si le pack est utilisé dans des inscriptions
        if ($pack->inscriptions()->exists()) {
            return false;
        }
        
        return true;
    }

    /**
     * Détermine si l'utilisateur peut supprimer de manière permanente le modèle.
     */
    public function forceDelete(User $user, Pack $pack): bool
    {
        // Ne pas permettre la suppression forcée si le pack est utilisé dans des inscriptions
        if ($pack->inscriptions()->withTrashed()->exists()) {
            return false;
        }
        
        return $user->can('force_delete_pack');
    }
    
    /**
     * Détermine si l'utilisateur peut restaurer le modèle.
     */
    public function restore(User $user, Pack $pack): bool
    {
        return $user->can('restore_pack');
    }
    
    /**
     * Détermine si l'utilisateur peut réinitialiser le modèle.
     */
    public function replicate(User $user, Pack $pack): bool
    {
        return $user->can('replicate_pack');
    }

    /**
     * Détermine si l'utilisateur peut activer/désactiver le pack.
     */
    public function toggleStatus(User $user, Pack $pack): bool
    {
        // Seuls les administrateurs peuvent activer/désactiver les packs
        if (!$user->hasRole('admin')) {
            return false;
        }
        
        // Ne pas permettre de désactiver un pack avec des inscriptions actives
        if (!$pack->is_active && $pack->inscriptions()->active()->exists()) {
            return false;
        }
        
        return $user->can('update_pack');
    }

    /**
     * Détermine si l'utilisateur peut mettre en avant le pack.
     */
    public function togglePopularity(User $user, Pack $pack): bool
    {
        // Seuls les administrateurs et les gestionnaires peuvent mettre en avant les packs
        if (!$user->hasAnyRole(['admin', 'gestionnaire'])) {
            return false;
        }
        
        // Ne pas permettre de mettre en avant un pack inactif
        if (!$pack->is_active) {
            return false;
        }
        
        return $user->can('update_pack');
    }

    /**
     * Détermine si l'utilisateur peut dupliquer le pack.
     */
    public function duplicate(User $user, Pack $pack): bool
    {
        // Vérifier les permissions de base
        if (!$user->can('create_pack')) {
            return false;
        }
        
        // Ne pas permettre la duplication d'un pack inactif sauf pour les administrateurs
        if (!$pack->is_active && !$user->hasRole('admin')) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Détermine si l'utilisateur peut voir les packs inactifs.
     */
    public function viewInactive(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('view_inactive_pack');
    }
    
    /**
     * Détermine si l'utilisateur peut gérer les prix des packs.
     */
    public function managePricing(User $user, Pack $pack): bool
    {
        // Seuls les administrateurs peuvent gérer les prix
        return $user->hasRole('admin') && $user->can('manage_pricing');
    }
    
    /**
     * Détermine si l'utilisateur peut voir les statistiques du pack.
     */
    public function viewStatistics(User $user, Pack $pack): bool
    {
        // Les administrateurs et les gestionnaires peuvent voir les statistiques
        return $user->hasAnyRole(['admin', 'gestionnaire']) && $user->can('view_pack_statistics');
    }

    /**
     * Détermine si l'utilisateur peut exporter les packs.
     */
    public function export(User $user): bool
    {
        return $user->can('export_pack');
    }
}