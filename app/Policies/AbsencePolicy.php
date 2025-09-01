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
        // Les administrateurs, assistants et professeurs peuvent voir toutes les absences
        return $user->hasAnyRole(['admin', 'assistant', 'professeur']) || 
               $user->can('view_any_absence');
    }

    /**
     * Détermine si l'utilisateur peut voir le modèle.
     */
    public function view(User $user, Absence $absence): bool
    {
        // L'administrateur peut tout voir
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // L'assistant peut voir les absences de son établissement
        if ($user->hasRole('assistant')) {
            return $user->etablissement_id === $absence->eleve->classe->etablissement_id;
        }
        
        // Le professeur peut voir les absences de ses élèves
        if ($user->hasRole('professeur')) {
            return $user->professeur->cours->pluck('id')->contains($absence->cours_id);
        }
        
        // Le parent peut voir les absences de ses enfants
        if ($user->hasRole('parent')) {
            return $user->eleves->contains($absence->eleve_id);
        }
        
        // L'élève peut voir ses propres absences
        if ($user->hasRole('eleve')) {
            return $user->id === $absence->eleve_id;
        }
        
        return $user->can('view_absence');
    }

    /**
     * Détermine si l'utilisateur peut créer des modèles.
     */
    public function create(User $user): bool
    {
        // Les administrateurs, assistants et professeurs peuvent créer des absences
        return $user->hasAnyRole(['admin', 'assistant', 'professeur']) || 
               $user->can('create_absence');
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour le modèle.
     */
    public function update(User $user, Absence $absence): bool
    {
        // L'administrateur peut tout modifier
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // L'assistant peut modifier les absences de son établissement
        if ($user->hasRole('assistant')) {
            return $user->etablissement_id === $absence->eleve->classe->etablissement_id;
        }
        
        // Le professeur peut modifier les absences de ses élèves
        if ($user->hasRole('professeur')) {
            return $user->professeur->cours->pluck('id')->contains($absence->cours_id);
        }
        
        return $user->can('update_absence');
    }

    /**
     * Détermine si l'utilisateur peut supprimer le modèle.
     */
    public function delete(User $user, Absence $absence): bool
    {
        // Seul l'administrateur peut supprimer définitivement une absence
        return $user->hasRole('admin') || $user->can('delete_absence');
    }
    
    /**
     * Détermine si l'utilisateur peut justifier une absence.
     */
    public function justify(User $user, Absence $absence): bool
    {
        // Les administrateurs et assistants peuvent justifier n'importe quelle absence
        if ($user->hasAnyRole(['admin', 'assistant'])) {
            return true;
        }
        
        // Le professeur peut justifier les absences de ses élèves
        if ($user->hasRole('professeur')) {
            return $user->professeur->cours->pluck('id')->contains($absence->cours_id);
        }
        
        // L'étudiant peut justifier sa propre absence
        if ($user->id === $absence->eleve_id) {
            return true;
        }
        
        // Le parent peut justifier l'absence de son enfant
        if ($user->hasRole('parent') && $user->eleves->contains($absence->eleve_id)) {
            return true;
        }
        
        // Vérifier la permission spécifique
        return $user->can('justify_absence');
    }
    
    /**
     * Détermine si l'utilisateur peut exporter les absences.
     */
    public function export(User $user): bool
    {
        // Les administrateurs, assistants, professeurs et secrétaires peuvent exporter les absences
        return $user->hasAnyRole(['admin', 'assistant', 'professeur', 'secretaire']) || 
               $user->can('export_absences');
    }
    
    /**
     * Détermine si l'utilisateur peut accéder aux statistiques des absences.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'professeur', 'direction']) || $user->can('view_absence_statistics');
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
        return $user->hasAnyRole(['admin', 'professeur']) || 
               $user->can('notify_absence');
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
