<?php

namespace App\Services;

use App\Enums\RoleType;
use App\Models\User;

class AuthorizationService
{
    /**
     * Vérifie si l'utilisateur a une certaine permission
     */
    public function userHasPermission(User $user, string $permission): bool
    {
        if (!$user->role) {
            return false;
        }

        try {
            $role = RoleType::from($user->role);
            return $role->hasPermission($permission);
        } catch (\ValueError $e) {
            // En cas de rôle invalide
            return false;
        }
    }

    /**
     * Vérifie si l'utilisateur a un certain rôle
     */
    public function userHasRole(User $user, RoleType $role): bool
    {
        if (!$user->role) {
            return false;
        }

        try {
            $userRole = RoleType::from($user->role);
            return $userRole === $role;
        } catch (\ValueError $e) {
            return false;
        }
    }

    /**
     * Vérifie si l'utilisateur a l'un des rôles spécifiés
     */
    public function userHasAnyRole(User $user, array $roles): bool
    {
        if (!$user->role) {
            return false;
        }

        try {
            $userRole = RoleType::from($user->role);
            return in_array($userRole, $roles);
        } catch (\ValueError $e) {
            return false;
        }
    }

    /**
     * Obtient le libellé du rôle de l'utilisateur
     */
    public function getUserRoleLabel(User $user): string
    {
        if (!$user->role) {
            return 'Non défini';
        }

        try {
            return RoleType::from($user->role)->label();
        } catch (\ValueError $e) {
            return 'Rôle invalide';
        }
    }

    /**
     * Obtient la classe CSS pour le badge du rôle de l'utilisateur
     */
    public function getUserRoleBadgeClass(User $user): string
    {
        if (!$user->role) {
            return 'bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full';
        }

        try {
            return RoleType::from($user->role)->badgeClass();
        } catch (\ValueError $e) {
            return 'bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full';
        }
    }
}
