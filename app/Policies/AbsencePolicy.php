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
        // Les administrateurs peuvent tout voir
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Les professeurs peuvent voir les absences de leurs élèves
        if ($user->hasRole('professeur')) {
            return true;
        }
        
        // Les parents peuvent voir les absences de leurs enfants
        if ($user->hasRole('parent')) {
            return true;
        }
        
        // Vérifier la permission spécifique
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
        // Les administrateurs et les professeurs peuvent créer des absences
        if ($user->hasAnyRole(['admin', 'professeur'])) {
            return true;
        }
        
        // Vérifier la permission spécifique
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
        // Seuls les administrateurs et les professeurs peuvent envoyer des notifications
        if ($user->hasAnyRole(['admin', 'professeur'])) {
            return true;
        }
        
        return $user->can('notify_absence');
    }
    
    /**
     * Détermine si l'utilisateur peut justifier une absence.
     */
    public function justify(User $user, Absence $absence): bool
    {
        // L'étudiant peut justifier sa propre absence
        if ($user->id === $absence->etudiant->id) {
            return true;
        }
        
        // Le parent peut justifier l'absence de son enfant
        if ($user->role === 'parent' && $user->id === $absence->etudiant->parent_id) {
            return true;
        }
        
        // Les administrateurs et les professeurs peuvent justifier n'importe quelle absence
        return $user->hasAnyRole(['admin', 'professeur']);
    }
    
    /**
     * Détermine si l'utilisateur peut exporter la liste des absences.
     */
    public function export(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'professeur', 'secretaire']) || $user->can('export_absences');
    }
    
    /**
     * Détermine si l'utilisateur peut voir les statistiques des absences.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'professeur', 'direction']) || $user->can('view_absence_statistics');
    }
    
    /**
     * Détermine si l'utilisateur peut envoyer un avertissement pour une absence.
     */
    public function sendWarning(User $user, Absence $absence): bool
    {
        // Seuls les administrateurs et les professeurs peuvent envoyer des avertissements
        if (!$user->hasAnyRole(['admin', 'professeur'])) {
            return false;
        }
        
        // Vérifier que l'absence n'est pas déjà justifiée
        if ($absence->est_justifie) {
            return false;
        }
        
        // Vérifier que l'avertissement n'a pas déjà été envoyé
        if ($absence->avertissement_envoye) {
            return false;
        }
        
        return true;
    }
}
