<?php

namespace App\Policies;

use App\Models\Paiement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaiementPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir n'importe quel paiement.
     */
    public function viewAny(User $user): bool
    {
        // Les administrateurs et les assistants peuvent tout voir
        if ($user->hasAnyRole(['admin', 'assistant'])) {
            return true;
        }
        
        // Les étudiants peuvent voir leurs propres paiements
        if ($user->hasRole('etudiant')) {
            return true;
        }
        
        // Les parents peuvent voir les paiements de leurs enfants
        if ($user->hasRole('parent')) {
            return true;
        }
        
        // La direction peut voir tous les paiements
        if ($user->hasRole('direction')) {
            return true;
        }
        
        return false;
    }

    /**
     * Détermine si l'utilisateur peut voir un paiement spécifique.
     */
    public function view(User $user, Paiement $paiement): bool
    {
        // Un élève peut voir ses propres paiements
        if ($user->hasRole('eleve') && $paiement->eleve_id === $user->id) {
            return true;
        }
        
        // Admin et assistant peuvent voir tous les paiements
        return $user->hasAnyRole(['admin', 'assistant']);
    }

    /**
     * Détermine si l'utilisateur peut créer des paiements.
     */
    public function create(User $user): bool
    {
        // Vérifier si l'utilisateur a un rôle autorisé
        if (!$user->hasAnyRole(['admin', 'assistant', 'comptable'])) {
            return false;
        }
        
        // Vérifier si l'utilisateur a la permission spécifique
        if (!$user->can('create_paiement')) {
            return false;
        }
        
        // Vérifier si la période de paiement est ouverte (si applicable)
        if (!$this->isPaiementPeriodOpen() && !$user->hasRole('admin')) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Vérifie si la période de paiement est ouverte.
     */
    protected function isPaiementPeriodOpen(): bool
    {
        // Implémentez la logique pour vérifier si la période de paiement est ouverte
        // Par exemple, en vérifiant les dates dans la base de données ou dans un fichier de configuration
        return true; // À remplacer par la logique réelle
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour un paiement.
     */
    public function update(User $user, Paiement $paiement): bool
    {
        // Seul un admin peut mettre à jour un paiement
        return $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut supprimer un paiement.
     */
    public function delete(User $user, Paiement $paiement): bool
    {
        // Seul un admin peut supprimer un paiement
        return $user->hasRole('admin');
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
        
        // Le parent peut voir les factures de son enfant
        if ($user->hasRole('parent') && $paiement->eleve->parent_id === $user->id) {
            return true;
        }
        
        // Admin, assistant et comptable peuvent générer toutes les factures
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
        
        // Le parent peut voir les reçus de son enfant
        if ($user->hasRole('parent') && $paiement->eleve->parent_id === $user->id) {
            return true;
        }
        
        // Admin, assistant et comptable peuvent voir tous les reçus
        return $user->hasAnyRole(['admin', 'assistant', 'comptable']);
    }
}
