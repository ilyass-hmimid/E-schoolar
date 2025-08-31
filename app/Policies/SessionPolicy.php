<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Session;

class SessionPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir n'importe quelle session.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut voir une session spécifique.
     */
    public function view(User $user, string $sessionId): bool
    {
        // Un utilisateur peut voir sa propre session
        if (Session::get('session_id') === $sessionId) {
            return true;
        }
        
        // Un admin peut voir n'importe quelle session
        return $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut se connecter.
     */
    public function login(?User $user): bool
    {
        // Tous les utilisateurs non connectés peuvent voir le formulaire de connexion
        return $user === null;
    }

    /**
     * Détermine si l'utilisateur peut se déconnecter.
     */
    public function logout(User $user): bool
    {
        // Seuls les utilisateurs connectés peuvent se déconnecter
        return $user !== null;
    }

    /**
     * Détermine si l'utilisateur peut forcer la déconnexion d'autres utilisateurs.
     */
    public function forceLogout(User $user, string $sessionId): bool
    {
        // Un utilisateur ne peut pas se déconnecter lui-même via cette méthode
        if (Session::get('session_id') === $sessionId) {
            return false;
        }
        
        // Seul un administrateur peut forcer la déconnexion
        return $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut voir les sessions actives.
     */
    public function viewActiveSessions(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut supprimer une session.
     */
    public function delete(User $user, string $sessionId): bool
    {
        // Un utilisateur peut supprimer sa propre session (déconnexion)
        if (Session::get('session_id') === $sessionId) {
            return true;
        }
        
        // Un admin peut supprimer n'importe quelle session
        return $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut renouveler sa session.
     */
    public function renew(User $user): bool
    {
        // Tous les utilisateurs connectés peuvent renouveler leur session
        return $user !== null;
    }

    /**
     * Détermine si l'utilisateur peut voir les détails de sa session.
     */
    public function viewSessionDetails(User $user): bool
    {
        return true;
    }

    /**
     * Détermine si l'utilisateur peut voir le tableau de bord.
     */
    public function viewDashboard(User $user): bool
    {
        // Vérifier si l'utilisateur est actif
        return $user->is_active;
    }

    /**
     * Détermine si l'utilisateur peut accéder à la page de réinitialisation de mot de passe.
     */
    public function requestPasswordReset(?User $user): bool
    {
        // Tous les utilisateurs non connectés peuvent demander une réinitialisation
        return $user === null;
    }

    /**
     * Détermine si l'utilisateur peut réinitialiser son mot de passe.
     */
    public function resetPassword(?User $user): bool
    {
        // Tous les utilisateurs non connectés peuvent réinitialiser leur mot de passe
        return $user === null;
    }
}
