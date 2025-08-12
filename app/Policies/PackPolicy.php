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
        return $user->can('view_any_pack');
    }

    /**
     * Détermine si l'utilisateur peut voir le modèle.
     */
    public function view(User $user, Pack $pack): bool
    {
        return $user->can('view_pack');
    }

    /**
     * Détermine si l'utilisateur peut créer des modèles.
     */
    public function create(User $user): bool
    {
        return $user->can('create_pack');
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour le modèle.
     */
    public function update(User $user, Pack $pack): bool
    {
        return $user->can('update_pack');
    }

    /**
     * Détermine si l'utilisateur peut supprimer le modèle.
     */
    public function delete(User $user, Pack $pack): bool
    {
        // Ne pas permettre la suppression si le pack est utilisé dans des inscriptions
        if ($pack->inscriptions()->exists()) {
            return false;
        }
        
        return $user->can('delete_pack');
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
}
