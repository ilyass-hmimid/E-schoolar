<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir n'importe quel utilisateur.
     */
    public function viewAny(User $user): bool
    {
        // Les administrateurs peuvent voir tous les utilisateurs
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Les chefs de département peuvent voir les utilisateurs de leur département
        if ($user->hasRole('chef_departement')) {
            return true;
        }
        
        // Les professeurs peuvent voir les étudiants de leurs classes
        if ($user->hasRole('professeur')) {
            return true;
        }
        
        // Vérifier la permission spécifique
        return $user->can('view_any_user');
    }

    /**
     * Détermine si l'utilisateur peut voir un utilisateur spécifique.
     */
    public function view(User $user, User $model): bool
    {
        // Un utilisateur peut voir son propre profil
        if ($user->id === $model->id) {
            return true;
        }
        
        // Un admin peut voir n'importe quel utilisateur
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un parent peut voir le profil de son enfant
        if ($user->hasRole('parent') && $model->parent_id === $user->id) {
            return true;
        }
        
        // Un professeur peut voir les profils de ses élèves
        if ($user->hasRole('professeur') && $model->hasRole('etudiant')) {
            $professeurClasses = $user->classesEnseignees->pluck('id');
            $etudiantClasse = $model->classe_id;
            return $professeurClasses->contains($etudiantClasse);
        }
        
        // Un chef de département peut voir les utilisateurs de son département
        if ($user->hasRole('chef_departement') && $model->departement_id === $user->departement_id) {
            return true;
        }
        
        // Vérifier la permission spécifique
        return $user->can('view_user');
    }

    /**
     * Détermine si l'utilisateur peut créer des utilisateurs.
     */
    public function create(User $user, ?string $role = null): bool
    {
        // Vérifier d'abord si l'utilisateur a la permission de base
        if (!$user->can('create_user')) {
            return false;
        }
        
        // Un administrateur peut créer n'importe quel type d'utilisateur
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un chef de département peut créer des professeurs et des étudiants dans son département
        if ($user->hasRole('chef_departement')) {
            return in_array($role, ['professeur', 'etudiant']);
        }
        
        // Un secrétaire peut créer des étudiants et des parents
        if ($user->hasRole('secretaire')) {
            return in_array($role, ['etudiant', 'parent']);
        }
        
        // Par défaut, pas de permission
        return false;
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour un utilisateur.
     */
    public function update(User $user, User $model): bool
    {
        // Un utilisateur peut mettre à jour son propre profil
        if ($user->id === $model->id) {
            return true;
        }
        
        // Un admin peut mettre à jour n'importe quel utilisateur
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un parent peut mettre à jour le profil de son enfant
        if ($user->hasRole('parent') && $model->parent_id === $user->id) {
            return true;
        }
        
        // Un chef de département peut mettre à jour les utilisateurs de son département
        if ($user->hasRole('chef_departement') && $model->departement_id === $user->departement_id) {
            return true;
        }
        
        // Vérifier la permission spécifique
        return $user->can('update_user');
    }

    /**
     * Détermine si l'utilisateur peut supprimer un utilisateur.
     */
    public function delete(User $user, User $model): bool
    {
        // Un utilisateur ne peut pas se supprimer lui-même
        if ($user->id === $model->id) {
            return false;
        }
        
        // Un admin peut supprimer n'importe quel autre utilisateur
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un chef de département peut supprimer les utilisateurs de son département
        if ($user->hasRole('chef_departement') && $model->departement_id === $user->departement_id) {
            // Ne peut pas supprimer d'autres administrateurs
            return !$model->hasRole('admin');
        }
        
        // Vérifier la permission spécifique
        return $user->can('delete_user');
    }

    /**
     * Détermine si l'utilisateur peut restaurer un utilisateur supprimé.
     */
    public function restore(User $user, User $model): bool
    {
        // Seul un administrateur peut restaurer un utilisateur
        return $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut supprimer définitivement un utilisateur.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Seul un administrateur peut supprimer définitivement un utilisateur
        return $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut changer le statut d'un autre utilisateur.
     */
    public function toggleStatus(User $user, User $model): bool
    {
        // Un utilisateur ne peut pas changer son propre statut
        if ($user->id === $model->id) {
            return false;
        }
        
        // Un administrateur peut changer le statut de n'importe quel utilisateur
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un chef de département peut désactiver les utilisateurs de son département
        if ($user->hasRole('chef_departement') && $model->departement_id === $user->departement_id) {
            // Ne peut pas désactiver d'autres administrateurs
            return !$model->hasRole('admin');
        }
        
        return false;
    }
    
    /**
     * Détermine si l'utilisateur peut réinitialiser le mot de passe d'un autre utilisateur.
     */
    public function resetPassword(User $user, User $model): bool
    {
        // Un administrateur peut réinitialiser n'importe quel mot de passe
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un parent peut réinitialiser le mot de passe de son enfant
        if ($user->hasRole('parent') && $model->parent_id === $user->id) {
            return true;
        }
        
        // Un chef de département peut réinitialiser les mots de passe des utilisateurs de son département
        if ($user->hasRole('chef_departement') && $model->departement_id === $user->departement_id) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Détermine si l'utilisateur peut voir les activités d'un autre utilisateur.
     */
    public function viewActivities(User $user, User $model): bool
    {
        // Un utilisateur peut voir ses propres activités
        if ($user->id === $model->id) {
            return true;
        }
        
        // Un administrateur peut voir les activités de n'importe quel utilisateur
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un parent peut voir les activités de son enfant
        if ($user->hasRole('parent') && $model->parent_id === $user->id) {
            return true;
        }
        
        // Un professeur peut voir les activités de ses élèves
        if ($user->hasRole('professeur') && $model->hasRole('etudiant')) {
            $professeurClasses = $user->classesEnseignees->pluck('id');
            $etudiantClasse = $model->classe_id;
            return $professeurClasses->contains($etudiantClasse);
        }
        
        return false;
    }
    
    /**
     * Détermine si l'utilisateur peut attribuer des rôles à un autre utilisateur.
     */
    public function assignRoles(User $user, User $model): bool
    {
        // Seul un administrateur peut attribuer des rôles
        return $user->hasRole('admin');
    }
    
    /**
     * Détermine si l'utilisateur peut gérer les permissions d'un autre utilisateur.
     */
    public function managePermissions(User $user, User $model): bool
    {
        // Seul un administrateur peut gérer les permissions
        return $user->hasRole('admin');
    }
}
