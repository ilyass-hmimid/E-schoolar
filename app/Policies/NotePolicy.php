<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Note;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotePolicy
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
        
        // Les professeurs peuvent voir les notes de leurs élèves
        if ($user->hasRole('professeur')) {
            return true;
        }
        
        // Les parents peuvent voir les notes de leurs enfants
        if ($user->hasRole('parent')) {
            return true;
        }
        
        // Les étudiants peuvent voir leurs propres notes
        if ($user->hasRole('etudiant')) {
            return true;
        }
        
        // Vérifier la permission spécifique
        return $user->can('view_any_note');
    }

    /**
     * Détermine si l'utilisateur peut voir le modèle.
     */
    public function view(User $user, Note $note): bool
    {
        // L'étudiant peut voir ses propres notes
        if ($user->id === $note->IdEtu) {
            return true;
        }
        
        // Le parent peut voir les notes de son enfant
        if ($user->role === 'parent') {
            $etudiant = $note->etudiant;
            return $etudiant && $user->id === $etudiant->parent_id;
        }
        
        // Le professeur peut voir les notes de ses élèves
        if ($user->role === 'professeur') {
            // Vérifier si le professeur enseigne la matière de la note
            return $user->matieres->contains('id', $note->IdMat);
        }
        
        // L'administrateur peut tout voir
        return $user->can('view_note');
    }

    /**
     * Détermine si l'utilisateur peut créer des modèles.
     */
    public function create(User $user): bool
    {
        // Vérifier d'abord la permission basée sur le rôle
        if (!$user->hasAnyRole(['admin', 'professeur'])) {
            return false;
        }
        
        // Vérifier si la période de saisie des notes est ouverte
        if (!$this->isNoteSubmissionPeriodOpen()) {
            // Seuls les administrateurs peuvent saisir des notes en dehors des périodes autorisées
            return $user->hasRole('admin');
        }
        
        // Vérifier la permission spécifique
        return $user->can('create_note');
    }
    
    /**
     * Vérifie si la période de saisie des notes est ouverte.
     */
    protected function isNoteSubmissionPeriodOpen(): bool
    {
        // Implémentez la logique pour vérifier si la période de saisie est ouverte
        // Par exemple, en vérifiant les dates dans la base de données ou dans un fichier de configuration
        return true; // À remplacer par la logique réelle
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour le modèle.
     */
    public function update(User $user, Note $note): bool
    {
        // L'administrateur peut tout modifier
        if ($user->can('update_note')) {
            return true;
        }
        
        // Le professeur peut modifier les notes de ses élèves dans ses matières
        if ($user->role === 'professeur') {
            return $user->matieres->contains('id', $note->IdMat);
        }
        
        return false;
    }

    /**
     * Détermine si l'utilisateur peut supprimer le modèle.
     */
    public function delete(User $user, Note $note): bool
    {
        // Seul l'administrateur peut supprimer une note
        return $user->can('delete_note');
    }

    /**
     * Détermine si l'utilisateur peut supprimer de manière permanente le modèle.
     */
    public function forceDelete(User $user, Note $note): bool
    {
        return $user->can('force_delete_note');
    }
    
    /**
     * Détermine si l'utilisateur peut restaurer le modèle.
     */
    public function restore(User $user, Note $note): bool
    {
        return $user->can('restore_note');
    }
    
    /**
     * Détermine si l'utilisateur peut répliquer le modèle.
     */
    public function replicate(User $user, Note $note): bool
    {
        return $user->can('replicate_note');
    }
    
    /**
     * Détermine si l'utilisateur peut exporter les notes
     */
    public function export(User $user): bool
    {
        return $user->can('export_note');
    }
    
    /**
     * Détermine si l'utilisateur peut voir le bulletin de notes
     */
    public function viewBulletin(User $user, ?User $etudiant = null): bool
    {
        // L'administrateur peut voir tous les bulletins
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Si aucun étudiant n'est spécifié, seul l'admin peut voir
        if (!$etudiant) {
            return false;
        }
        
        // Un étudiant peut voir son propre bulletin
        if ($user->id === $etudiant->id && $user->hasRole('etudiant')) {
            return true;
        }
        
        // Un parent peut voir le bulletin de son enfant
        if ($user->hasRole('parent') && $user->id === $etudiant->parent_id) {
            return true;
        }
        
        // Un professeur peut voir les bulletins de ses élèves
        if ($user->hasRole('professeur')) {
            // Vérifier si le professeur enseigne à cet étudiant
            $classesEnseignees = $user->classesEnseignees->pluck('id');
            return $etudiant->classe && $classesEnseignees->contains($etudiant->classe->id);
        }
        
        // La direction peut voir tous les bulletins
        if ($user->hasRole('direction')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Détermine si l'utilisateur peut éditer le bulletin de notes
     */
    public function editBulletin(User $user): bool
    {
        // Vérifier d'abord le rôle
        if (!$user->hasAnyRole(['admin', 'professeur', 'direction'])) {
            return false;
        }
        
        // Vérifier si la période d'édition est ouverte
        if (!$this->isBulletinEditingPeriodOpen() && !$user->hasRole('admin')) {
            return false;
        }
        
        // Vérifier la permission spécifique
        return $user->can('edit_bulletin');
    }
    
    /**
     * Vérifie si la période d'édition des bulletins est ouverte.
     */
    protected function isBulletinEditingPeriodOpen(): bool
    {
        // Implémentez la logique pour vérifier si la période d'édition est ouverte
        // Par exemple, en vérifiant les dates dans la base de données ou dans un fichier de configuration
        return true; // À remplacer par la logique réelle
    }
    
    /**
     * Détermine si l'utilisateur peut valider un bulletin de notes.
     */
    public function validateBulletin(User $user): bool
    {
        // Seuls les administrateurs et la direction peuvent valider les bulletins
        return $user->hasAnyRole(['admin', 'direction']) && $user->can('validate_bulletin');
    }
    
    /**
     * Détermine si l'utilisateur peut publier les notes aux étudiants.
     */
    public function publishNotes(User $user): bool
    {
        // Seuls les administrateurs et la direction peuvent publier les notes
        return $user->hasAnyRole(['admin', 'direction']) && $user->can('publish_notes');
    }
    
    /**
     * Détermine si l'utilisateur peut consulter les statistiques des notes.
     */
    public function viewStatistics(User $user): bool
    {
        // Les administrateurs, la direction et les professeurs peuvent voir les statistiques
        return $user->hasAnyRole(['admin', 'direction', 'professeur']) && $user->can('view_note_statistics');
    }
}
