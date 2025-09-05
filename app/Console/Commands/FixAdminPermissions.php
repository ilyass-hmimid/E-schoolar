<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class FixAdminPermissions extends Command
{
    protected $signature = 'fix:admin-permissions';
    protected $description = 'Fix admin user roles and permissions';

    public function handle()
    {
        // Démarrer une transaction pour s'assurer que tout se passe bien
        DB::beginTransaction();

        try {
            // 1. Créer ou récupérer l'utilisateur admin
            $user = User::firstOrCreate(
                ['email' => 'admin@teste.com'],
                [
                    'name' => 'Administrateur',
                    'password' => bcrypt('motdepass'),
                    'is_active' => true,
                ]
            );

            $this->info("Utilisateur admin vérifié: " . $user->email);

            // 2. Créer le rôle admin s'il n'existe pas
            $adminRole = Role::firstOrCreate(['name' => 'admin']);
            $this->info("Rôle admin vérifié");

            // 3. Créer les permissions de base si elles n'existent pas
            $permissions = [
                'access dashboard',
                'view users', 'create users', 'edit users', 'delete users',
                'view roles', 'create roles', 'edit roles', 'delete roles',
                'view permissions', 'edit permissions',
                'view absences', 'create absences', 'edit absences', 'delete absences',
                'view paiements', 'create paiements', 'edit paiements', 'delete paiements',
                'view classes', 'create classes', 'edit classes', 'delete classes',
                'view matieres', 'create matieres', 'edit matieres', 'delete matieres',
            ];

            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }
            $this->info("Permissions de base créées");

            // 4. Donner toutes les permissions au rôle admin
            $adminRole->syncPermissions($permissions);
            $this->info("Toutes les permissions ont été attribuées au rôle admin");

            // 5. Donner le rôle admin à l'utilisateur
            $user->syncRoles([$adminRole->id]);
            $this->info("Rôle admin attribué à l'utilisateur");

            // 6. S'assurer que l'utilisateur est actif
            $user->is_active = true;
            $user->save();
            $this->info("Compte utilisateur activé");

            // Tout s'est bien passé, on valide la transaction
            DB::commit();

            $this->newLine();
            $this->info('✅ Tâche terminée avec succès !');
            $this->info('Vous pouvez maintenant vous connecter avec:');
            $this->line('Email: admin@teste.com');
            $this->line('Mot de passe: motdepass');
            
        } catch (\Exception $e) {
            // En cas d'erreur, on annule tout
            DB::rollBack();
            $this->error('❌ Une erreur est survenue : ' . $e->getMessage());
            $this->error('La transaction a été annulée.');
            return 1;
        }

        return 0;
    }
}
