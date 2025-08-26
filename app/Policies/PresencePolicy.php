<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Presence;
use Illuminate\Auth\Access\HandlesAuthorization;

class PresencePolicy
{
    use HandlesAuthorization;

    /**
     * Déterminer si l'utilisateur peut voir la liste des présences
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('professeur');
    }

    /**
     * Déterminer si l'utilisateur peut voir une présence spécifique
     */
    public function view(User $user, Presence $presence)
    {
        // Un professeur ne peut voir que les présences qu'il a créées
        return $user->id === $presence->professeur_id;
    }

    /**
     * Déterminer si l'utilisateur peut créer des présences
     */
    public function create(User $user)
    {
        return $user->hasRole('professeur');
    }

    /**
     * Déterminer si l'utilisateur peut mettre à jour une présence
     */
    public function update(User $user, Presence $presence)
    {
        // Un professeur ne peut mettre à jour que les présences qu'il a créées
        return $user->id === $presence->professeur_id;
    }

    /**
     * Déterminer si l'utilisateur peut supprimer une présence
     */
    public function delete(User $user, Presence $presence)
    {
        // Un professeur ne peut supprimer que les présences qu'il a créées
        return $user->id === $presence->professeur_id;
    }

    /**
     * Déterminer si l'utilisateur peut restaurer une présence supprimée
     */
    public function restore(User $user, Presence $presence)
    {
        // Un professeur ne peut restaurer que les présences qu'il a créées
        return $user->id === $presence->professeur_id;
    }

    /**
     * Déterminer si l'utilisateur peut supprimer définitivement une présence
     */
    public function forceDelete(User $user, Presence $presence)
    {
        // Seul un administrateur peut supprimer définitivement une présence
        return $user->hasRole('admin');
    }
}
