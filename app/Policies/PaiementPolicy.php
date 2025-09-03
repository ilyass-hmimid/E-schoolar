<?php

namespace App\Policies;

use App\Models\Paiement;
use App\Models\PaiementProfesseur;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaiementPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir n'importe quel paiement.
     */
    public function viewAny(User $user, string $type = 'eleve'): bool
    {
        // Les administrateurs peuvent tout voir
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Les assistants peuvent voir les paiements des élèves
        if ($user->hasRole('assistant') && $type === 'eleve') {
            return true;
        }
        
        // Les professeurs peuvent voir leurs propres paiements
        if ($user->hasRole('professeur') && $type === 'professeur') {
            return true;
        }
        
        // Les élèves peuvent voir leurs propres paiements
        if ($user->hasRole('eleve') && $type === 'eleve') {
            return true;
        }
        
        // La direction peut voir tous les paiements
        if ($user->hasRole('direction')) {
            return true;
        }
        
        return false;
    }

    /**
     * Détermine si l'utilisateur peut voir un paiement élève spécifique.
     */
    public function viewEleve(User $user, Paiement $paiement): bool
    {
        // Un élève peut voir ses propres paiements
        if ($user->hasRole('eleve') && $paiement->eleve_id === $user->id) {
            return true;
        }
        
        // Un parent peut voir les paiements de ses enfants
        if ($user->hasRole('parent') && $user->enfants->contains('id', $paiement->eleve_id)) {
            return true;
        }
        
        // Admin, assistant et direction peuvent voir tous les paiements
        return $user->hasAnyRole(['admin', 'assistant', 'direction']);
    }
    
    /**
     * Détermine si l'utilisateur peut voir un paiement professeur spécifique.
     */
    public function viewProfesseur(User $user, PaiementProfesseur $paiement): bool
    {
        // Un professeur peut voir ses propres paiements
        if ($user->hasRole('professeur') && $paiement->professeur_id === $user->id) {
            return true;
        }
        
        // Admin et direction peuvent voir tous les paiements
        return $user->hasAnyRole(['admin', 'direction']);
    }

    /**
     * Détermine si l'utilisateur peut créer des paiements élèves.
     */
    public function createEleve(User $user): bool
    {
        // Seuls les administrateurs et les assistants peuvent créer des paiements élèves
        return $user->hasAnyRole(['admin', 'assistant', 'direction']);
    }
    
    /**
     * Détermine si l'utilisateur peut créer des paiements professeurs.
     */
    public function createProfesseur(User $user): bool
    {
        // Seuls les administrateurs peuvent créer des paiements professeurs
        return $user->hasRole('admin') || $user->hasRole('direction');
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour un paiement élève.
     */
    public function updateEleve(User $user, Paiement $paiement): bool
    {
        // Seuls les administrateurs et les assistants peuvent mettre à jour les paiements élèves
        return $user->hasAnyRole(['admin', 'assistant', 'direction']);
    }
    
    /**
     * Détermine si l'utilisateur peut mettre à jour un paiement professeur.
     */
    public function updateProfesseur(User $user, PaiementProfesseur $paiement): bool
    {
        // Seuls les administrateurs peuvent mettre à jour les paiements professeurs
        return $user->hasRole('admin') || $user->hasRole('direction');
    }

    /**
     * Détermine si l'utilisateur peut supprimer un paiement élève.
     */
    public function deleteEleve(User $user, Paiement $paiement): bool
    {
        // Seuls les administrateurs peuvent supprimer des paiements élèves
        return $user->hasRole('admin') || $user->hasRole('direction');
    }
    
    /**
     * Détermine si l'utilisateur peut supprimer un paiement professeur.
     */
    public function deleteProfesseur(User $user, PaiementProfesseur $paiement): bool
    {
        // Seuls les administrateurs peuvent supprimer des paiements professeurs
        return $user->hasRole('admin') || $user->hasRole('direction');
    }

    /**
     * Détermine si l'utilisateur peut valider un paiement.
     */
    public function valider(User $user, Paiement $paiement): bool
    {
        // Seul un admin peut valider un paiement
        return $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut annuler un paiement.
     */
    public function annuler(User $user, Paiement $paiement): bool
    {
        // Seul un admin peut annuler un paiement
        return $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut générer une facture.
     */
    public function genererFacture(User $user, Paiement $paiement): bool
    {
        // L'étudiant peut voir sa propre facture
        if ($user->hasRole('etudiant') && $paiement->eleve_id === $user->id) {
            return true;
        }
        
        // Le secrétariat peut voir toutes les factures
        return $user->hasAnyRole(['admin', 'assistant', 'comptable']);
    }
    
    /**
     * Détermine si l'utilisateur peut exporter la liste des paiements.
     */
    public function export(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'assistant', 'comptable', 'direction']) && 
               $user->can('export_paiements');
    }
    
    /**
     * Détermine si l'utilisateur peut voir les statistiques des paiements.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'direction', 'comptable']) && 
               $user->can('view_paiement_statistics');
    }
    
    /**
     * Détermine si l'utilisateur peut envoyer un rappel de paiement.
     */
    public function sendReminder(User $user, Paiement $paiement): bool
    {
        // Seuls les administrateurs et les assistants peuvent envoyer des rappels
        if (!$user->hasAnyRole(['admin', 'assistant'])) {
            return false;
        }
        
        // Ne pas envoyer de rappel pour un paiement déjà effectué
        if ($paiement->est_paye) {
            return false;
        }
        
        // Vérifier si un rappel a déjà été envoyé récemment (par exemple, dans les 7 derniers jours)
        if ($paiement->dernier_rappel && $paiement->dernier_rappel->diffInDays(now()) < 7) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Détermine si l'utilisateur peut voir les détails d'un reçu de paiement.
     */
    public function viewReceipt(User $user, Paiement $paiement): bool
    {
        // L'étudiant peut voir son propre reçu
        if ($user->hasRole('etudiant') && $paiement->eleve_id === $user->id) {
            return true;
        }
        
        // Le secrétariat peut voir tous les reçus
        return $user->hasAnyRole(['admin', 'assistant', 'comptable']);
    }
}
