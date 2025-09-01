<?php

namespace App\Policies;

use App\Models\Classe;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassePolicy
{
    use HandlesAuthorization;

    /**
     * Déterminer si l'utilisateur peut voir n'importe quelle classe.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Déterminer si l'utilisateur peut voir la classe.
     */
    public function view(User $user, Classe $classe): bool
    {
        // Un administrateur peut voir toutes les classes
        if ($user->hasRole('admin')) {
            return true;
        }

        // Un professeur peut voir ses propres classes
        if ($user->hasRole('professeur') && $user->professeur) {
            return $classe->professeur_principal_id === $user->professeur->id || 
                   $classe->enseignants()->where('professeur_id', $user->professeur->id)->exists();
        }

        // Un élève peut voir sa propre classe
        if ($user->hasRole('eleve') && $user->eleve) {
            return $classe->eleves()->where('eleve_id', $user->eleve->id)->exists();
        }

        // Un assistant peut voir les classes de son niveau
        if ($user->hasRole('assistant') && $user->assistant) {
            return $classe->niveau_id === $user->assistant->niveau_id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut créer des classes.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Déterminer si l'utilisateur peut mettre à jour la classe.
     */
    public function update(User $user, Classe $classe): bool
    {
        // Un administrateur peut modifier n'importe quelle classe
        if ($user->hasRole('admin')) {
            return true;
        }

        // Un professeur principal peut modifier sa propre classe
        if ($user->hasRole('professeur') && $user->professeur) {
            return $classe->professeur_principal_id === $user->professeur->id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut supprimer la classe.
     */
    public function delete(User $user, Classe $classe): bool
    {
        // Seul un administrateur peut supprimer une classe
        // et seulement si elle n'a pas d'élèves ni de cours
        return $user->hasRole('admin') && 
               !$classe->eleves()->exists() && 
               !$classe->cours()->exists();
    }

    /**
     * Déterminer si l'utilisateur peut restaurer la classe.
     */
    public function restore(User $user, Classe $classe): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Déterminer si l'utilisateur peut supprimer définitivement la classe.
     */
    public function forceDelete(User $user, Classe $classe): bool
    {
        return $user->hasRole('admin');
    }
}
