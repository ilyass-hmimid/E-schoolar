<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Enums\RoleType;

class PermissionSeeder extends Seeder
{
    /**
     * Liste des permissions de base avec leurs libellés
     */
    private array $permissions = [
        // Gestion des utilisateurs
        'users.view' => 'Voir les utilisateurs',
        'users.create' => 'Créer des utilisateurs',
        'users.edit' => 'Modifier les utilisateurs',
        'users.delete' => 'Supprimer des utilisateurs',
        'users.impersonate' => 'Prendre la main sur un utilisateur',
        
        // Gestion des rôles et permissions
        'roles.view' => 'Voir les rôles',
        'roles.create' => 'Créer des rôles',
        'roles.edit' => 'Modifier les rôles',
        'roles.delete' => 'Supprimer des rôles',
        'roles.assign' => 'Attribuer des rôles',
        
        // Gestion des élèves
        'eleves.view' => 'Voir les élèves',
        'eleves.create' => 'Créer des élèves',
        'eleves.edit' => 'Modifier les élèves',
        'eleves.delete' => 'Supprimer des élèves',
        'eleves.export' => 'Exporter la liste des élèves',
        
        // Gestion des professeurs
        'professeurs.view' => 'Voir les professeurs',
        'professeurs.create' => 'Créer des professeurs',
        'professeurs.edit' => 'Modifier les professeurs',
        'professeurs.delete' => 'Supprimer des professeurs',
        
        // Gestion des classes
        'classes.view' => 'Voir les classes',
        'classes.create' => 'Créer des classes',
        'classes.edit' => 'Modifier les classes',
        'classes.delete' => 'Supprimer des classes',
        
        // Gestion des matières
        'matieres.view' => 'Voir les matières',
        'matieres.create' => 'Créer des matières',
        'matieres.edit' => 'Modifier les matières',
        'matieres.delete' => 'Supprimer des matières',
        
        // Gestion des emplois du temps
        'emplois_du_temps.view' => 'Voir les emplois du temps',
        'emplois_du_temps.create' => 'Créer des emplois du temps',
        'emplois_du_temps.edit' => 'Modifier les emplois du temps',
        'emplois_du_temps.delete' => 'Supprimer des emplois du temps',
        
        // Gestion des notes
        'notes.view' => 'Voir les notes',
        'notes.create' => 'Saisir des notes',
        'notes.edit' => 'Modifier des notes',
        'notes.delete' => 'Supprimer des notes',
        'notes.export' => 'Exporter les notes',
        
        // Gestion des absences
        'absences.view' => 'Voir les absences',
        'absences.create' => 'Saisir des absences',
        'absences.edit' => 'Modifier des absences',
        'absences.delete' => 'Supprimer des absences',
        'absences.justify' => 'Justifier des absences',
        'absences.export' => 'Exporter les absences',
        
        // Gestion des paiements
        'paiements.view' => 'Voir les paiements',
        'paiements.create' => 'Enregistrer des paiements',
        'paiements.edit' => 'Modifier des paiements',
        'paiements.delete' => 'Supprimer des paiements',
        'paiements.export' => 'Exporter les paiements',
        
        // Paramètres
        'settings.view' => 'Voir les paramètres',
        'settings.edit' => 'Modifier les paramètres',
        
        // Tableau de bord
        'dashboard.view' => 'Voir le tableau de bord',
        'dashboard.admin' => 'Tableau de bord administrateur',
        'dashboard.teacher' => 'Tableau de bord professeur',
        'dashboard.student' => 'Tableau de bord élève',
    ];

    /**
     * Liste des rôles avec leurs permissions associées
     */
    private array $roles = [
        RoleType::ADMIN->value => [
            'users.view', 'users.create', 'users.edit', 'users.delete', 'users.impersonate',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete', 'roles.assign',
            'eleves.view', 'eleves.create', 'eleves.edit', 'eleves.delete', 'eleves.export',
            'professeurs.view', 'professeurs.create', 'professeurs.edit', 'professeurs.delete',
            'classes.view', 'classes.create', 'classes.edit', 'classes.delete',
            'matieres.view', 'matieres.create', 'matieres.edit', 'matieres.delete',
            'emplois_du_temps.view', 'emplois_du_temps.create', 'emplois_du_temps.edit', 'emplois_du_temps.delete',
            'notes.view', 'notes.create', 'notes.edit', 'notes.delete', 'notes.export',
            'absences.view', 'absences.create', 'absences.edit', 'absences.delete', 'absences.justify', 'absences.export',
            'paiements.view', 'paiements.create', 'paiements.edit', 'paiements.delete', 'paiements.export',
            'settings.view', 'settings.edit',
            'dashboard.view', 'dashboard.admin', 'dashboard.teacher', 'dashboard.student',
        ],
        RoleType::PROFESSEUR->value => [
            'dashboard.view', 'dashboard.teacher',
            'eleves.view',
            'classes.view',
            'matieres.view',
            'emplois_du_temps.view',
            'notes.view', 'notes.create', 'notes.edit',
            'absences.view', 'absences.create', 'absences.edit',
        ],
        RoleType::ELEVE->value => [
            'dashboard.view', 'dashboard.student',
            'classes.view',
            'emplois_du_temps.view',
            'notes.view',
            'absences.view',
        ],
        RoleType::ASSISTANT->value => [
            'dashboard.view',
            'eleves.view',
            'professeurs.view',
            'classes.view',
            'matieres.view',
            'emplois_du_temps.view',
            'notes.view',
            'absences.view', 'absences.create', 'absences.edit', 'absences.justify',
            'paiements.view', 'paiements.create',
        ],
    ];

    /**
     * Exécuter le seeder
     */
    public function run(): void
    {
        // Réinitialiser les rôles et permissions mis en cache
        app()[
            \Spatie\Permission\PermissionRegistrar::class
        ]->forgetCachedPermissions();

        // Créer les permissions
        foreach (array_keys($this->permissions) as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        // Créer les rôles et leur attribuer les permissions
        foreach ($this->roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );

            $permissionModels = Permission::whereIn('name', $permissions)->get();
            $role->syncPermissions($permissionModels);
        }

        // Attribuer le rôle admin à l'utilisateur avec l'ID 1 (super admin)
        $superAdmin = \App\Models\User::find(1);
        if ($superAdmin) {
            $superAdmin->assignRole(RoleType::ADMIN->value);
        }
    }
}
