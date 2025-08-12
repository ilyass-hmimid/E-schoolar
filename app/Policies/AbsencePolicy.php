<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Absence;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbsencePolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir n'importe quel modèle.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_absence');
    }

    /**
     * Détermine si l'utilisateur peut voir le modèle.
     */
    public function view(User $user, Absence $absence): bool
    {
        // L'utilisateur peut voir sa propre absence
        if ($user->id === $absence->etudiant->id) {
            return true;
        }
        
        // Le parent peut voir les absences de son enfant
        if ($user->role === 'parent' && $user->id === $absence->etudiant->parent_id) {
            return true;
        }
        
        // Le professeur peut voir les absences de ses élèves
        if ($user->role === 'professeur' && $absence->seance->IdProf === $user->id) {
            return true;
        }
        
        // L'administrateur peut tout voir
        return $user->can('view_absence');
    }

    /**
     * Détermine si l'utilisateur peut créer des modèles.
     */
    public function create(User $user): bool
    {
        return $user->can('create_absence');
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour le modèle.
     */
    public function update(User $user, Absence $absence): bool
    {
        // L'administrateur peut tout modifier
        if ($user->can('update_absence')) {
            return true;
        }
        
        // Le professeur peut modifier les absences de ses élèves
        if ($user->role === 'professeur' && $absence->seance->IdProf === $user->id) {
            return true;
        }
        
        return false;
    }

    /**
     * Détermine si l'utilisateur peut supprimer le modèle.
     */
    public function delete(User $user, Absence $absence): bool
    {
        // Seul l'administrateur peut supprimer une absence
        return $user->can('delete_absence');
    }

    /**
     * Détermine si l'utilisateur peut supprimer de manière permanente le modèle.
     */
    public function forceDelete(User $user, Absence $absence): bool
    {
        return $user->can('force_delete_absence');
    }
    
    /**
     * Détermine si l'utilisateur peut restaurer le modèle.
     */
    public function restore(User $user, Absence $absence): bool
    {
        return $user->can('restore_absence');
    }
    
    /**
     * Détermine si l'utilisateur peut répliquer le modèle.
     */
    public function replicate(User $user, Absence $absence): bool
    {
        return $user->can('replicate_absence');
    }
    
    /**
     * Détermine si l'utilisateur peut envoyer des notifications pour les absences non justifiées.
     */
    public function notify(User $user): bool
    {
        return $user->can('notify_absence');
    }
}
