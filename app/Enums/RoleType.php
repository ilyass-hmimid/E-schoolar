<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum RoleType: int
{
    case ADMIN = 1;
    case PROFESSEUR = 2;
    case ASSISTANT = 3;
    case ELEVE = 4;

    /**
     * Obtenir le libellé du rôle
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrateur',
            self::PROFESSEUR => 'Professeur',
            self::ASSISTANT => 'Assistant',
            self::ELEVE => 'Élève',
        };
    }

    /**
     * Obtenir la classe CSS pour le badge du rôle
     */
    public function badgeClass(): string
    {
        return match($this) {
            self::ADMIN => 'bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full',
            self::PROFESSEUR => 'bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full',
            self::ASSISTANT => 'bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full',
            self::ELEVE => 'bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full',
        };
    }

    /**
     * Obtenir les permissions associées à ce rôle
     */
    public function permissions(): array
    {
        return match($this) {
            self::ADMIN => [
                'manage_users',
                'manage_professeurs',
                'manage_eleves',
                'manage_matieres',
                'manage_classes',
                'manage_absences',
                'manage_paiements',
                'view_reports',
                'manage_settings',
            ],
            self::PROFESSEUR => [
                'view_own_classes',
                'manage_own_notes',
                'mark_absences',
                'view_own_schedule',
            ],
            self::ASSISTANT => [
                'mark_absences',
                'view_eleves',
                'view_classes',
                'manage_presences',
            ],
            self::ELEVE => [
                'view_notes',
                'view_absences',
                'view_emploi_du_temps',
                'view_profil',
            ],
        };
    }

    /**
     * Vérifier si le rôle a une certaine permission
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions());
    }

    /**
     * Obtenir tous les rôles sous forme de tableau pour les formulaires
     */
    public static function forSelect(): array
    {
        return collect(self::cases())->mapWithKeys(fn($role) => [
            $role->value => $role->label()
        ])->toArray();
    }
}

