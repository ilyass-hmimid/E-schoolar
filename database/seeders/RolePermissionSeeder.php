<?php

namespace Database\Seeders;

use App\Enums\RoleType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Exécuter les seeds de la base de données.
     */
    public function run(): void
    {
        // Réinitialiser les rôles et permissions mis en cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        $permissions = [
            'view users', 'create users', 'edit users', 'delete users',
            'view roles', 'create roles', 'edit roles', 'delete roles',
            'view permissions', 'create permissions', 'edit permissions', 'delete permissions',
            'view eleves', 'create eleves', 'edit eleves', 'delete eleves',
            'view professeurs', 'create professeurs', 'edit professeurs', 'delete professeurs',
            'view classes', 'create classes', 'edit classes', 'delete classes',
            'view matieres', 'create matieres', 'edit matieres', 'delete matieres',
            'view notes', 'create notes', 'edit notes', 'delete notes',
            'view emploi du temps', 'create emploi du temps', 'edit emploi du temps', 'delete emploi du temps',
            'view absences', 'create absences', 'edit absences', 'delete absences',
            'view retards', 'create retards', 'edit retards', 'delete retards',
            'view sanctions', 'create sanctions', 'edit sanctions', 'delete sanctions',
            'view paiements', 'create paiements', 'edit paiements', 'delete paiements',
            'view statistiques', 'view parametres', 'edit parametres',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Créer les rôles et attribuer les permissions
        $this->createAdminRole();
        $this->createProfesseurRole();
        $this->createAssistantRole();
        $this->createEleveRole();
    }

    /**
     * Créer le rôle d'administrateur avec toutes les permissions
     */
    private function createAdminRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $role->syncPermissions(Permission::all());
    }

    /**
     * Créer le rôle de professeur avec les permissions appropriées
     */
    private function createProfesseurRole(): void
    {
        $permissions = [
            'view professeurs', 'edit professeurs',
            'view eleves',
            'view classes',
            'view matieres',
            'view notes', 'create notes', 'edit notes',
            'view emploi du temps',
            'view absences', 'create absences', 'edit absences',
            'view retards', 'create retards', 'edit retards',
            'view sanctions', 'create sanctions', 'edit sanctions',
        ];

        $role = Role::firstOrCreate(['name' => 'professeur', 'guard_name' => 'web']);
        $role->syncPermissions($permissions);
    }

    /**
     * Créer le rôle d'assistant avec des permissions limitées
     */
    private function createAssistantRole(): void
    {
        $permissions = [
            'view eleves',
            'view classes',
            'view matieres',
            'view notes',
            'view emploi du temps',
            'view absences', 'create absences', 'edit absences',
            'view retards', 'create retards', 'edit retards',
            'view sanctions', 'create sanctions', 'edit sanctions',
        ];

        $role = Role::firstOrCreate(['name' => 'assistant', 'guard_name' => 'web']);
        $role->syncPermissions($permissions);
    }

    /**
     * Créer le rôle d'élève avec des permissions très limitées
     */
    private function createEleveRole(): void
    {
        $permissions = [
            'view eleves',
            'view classes',
            'view matieres',
            'view notes',
            'view emploi du temps',
            'view absences',
            'view retards',
            'view sanctions',
            'view paiements',
        ];

        $role = Role::firstOrCreate(['name' => 'eleve', 'guard_name' => 'web']);
        $role->syncPermissions($permissions);
    }
}
