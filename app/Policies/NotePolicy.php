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
        // Seuls les professeurs et administrateurs peuvent créer des notes
        return $user->hasRole(['professeur', 'admin']) && $user->can('create_note');
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
        if ($user->can('view_any_note')) {
            return true;
        }
        
        // Un étudiant peut voir son propre bulletin
        if ($etudiant && $user->id === $etudiant->id && $user->role === 'etudiant') {
            return true;
        }
        
        // Un parent peut voir le bulletin de son enfant
        if ($etudiant && $user->role === 'parent' && $user->id === $etudiant->parent_id) {
            return true;
        }
        
        // Un professeur peut voir les bulletins de ses élèves
        if ($etudiant && $user->role === 'professeur') {
            // Vérifier si le professeur enseigne à cet étudiant
            $classesEnseignees = $user->classesEnseignees->pluck('id');
            return $etudiant->classe && $classesEnseignees->contains($etudiant->classe->id);
        }
        
        return false;
    }
    
    /**
     * Détermine si l'utilisateur peut éditer le bulletin de notes
     */
    public function editBulletin(User $user): bool
    {
        // Seuls les professeurs et administrateurs peuvent éditer les bulletins
        return $user->hasRole(['professeur', 'admin']) && $user->can('edit_bulletin');
    }
}
