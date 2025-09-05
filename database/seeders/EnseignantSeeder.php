<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Enseignant;
use App\Enums\RoleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EnseignantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Vider la table des enseignants et des utilisateurs associés
        $userIds = Enseignant::pluck('user_id')->toArray();
        if (!empty($userIds)) {
            User::whereIn('id', $userIds)->delete();
            Enseignant::truncate();
        }
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Données des professeurs à créer
        $professeurs = [
            [
                'nom' => 'Alaoui',
                'prenom' => 'Karim',
                'name' => 'Karim Alaoui',
                'email' => 'k.alaoui@example.com',
                'telephone' => '0612345678',
                'specialite' => 'Mathématiques',
                'date_naissance' => '1980-05-15',
                'date_embauche' => '2015-09-01',
                'salaire_base' => 10000,
            ],
            [
                'nom' => 'Benali',
                'prenom' => 'Fatima',
                'name' => 'Fatima Benali',
                'email' => 'f.benali@example.com',
                'telephone' => '0623456789',
                'specialite' => 'Physique-Chimie',
                'date_naissance' => '1985-08-22',
                'date_embauche' => '2017-09-01',
                'salaire_base' => 9500,
            ],
            [
                'nom' => 'El Mansouri',
                'prenom' => 'Mehdi',
                'name' => 'Mehdi El Mansouri',
                'email' => 'm.elmansouri@example.com',
                'telephone' => '0634567890',
                'specialite' => 'Sciences de la Vie et de la Terre',
                'date_naissance' => '1978-11-30',
                'date_embauche' => '2010-09-01',
                'salaire_base' => 12000,
            ],
            [
                'nom' => 'Zouhairi',
                'prenom' => 'Amina',
                'name' => 'Amina Zouhairi',
                'email' => 'a.zouhairi@example.com',
                'telephone' => '0645678901',
                'specialite' => 'Français',
                'date_naissance' => '1982-03-18',
                'date_embauche' => '2018-09-01',
                'salaire_base' => 9000,
            ],
            [
                'nom' => 'El Amrani',
                'prenom' => 'Youssef',
                'name' => 'Youssef El Amrani',
                'email' => 'y.elamrani@example.com',
                'telephone' => '0656789012',
                'specialite' => 'Philosophie',
                'date_naissance' => '1975-07-25',
                'date_embauche' => '2008-09-01',
                'salaire_base' => 11000,
            ],
        ];

        foreach ($professeurs as $prof) {
            // Créer l'utilisateur
            $user = User::create([
                'name' => $prof['name'],
                'email' => $prof['email'],
                'password' => Hash::make('password'),
                'role' => RoleType::PROFESSEUR->value,
                'phone' => $prof['telephone'],
                'is_active' => true,
            ]);

            // Créer l'enseignant
            Enseignant::create([
                'user_id' => $user->id,
                'nom' => $prof['nom'],
                'prenom' => $prof['prenom'],
                'email' => $prof['email'],
                'telephone' => $prof['telephone'],
                'specialite' => $prof['specialite'],
                'date_naissance' => $prof['date_naissance'],
                'date_embauche' => $prof['date_embauche'],
                'salaire_base' => $prof['salaire_base'],
            ]);
        }

        $this->command->info(count($professeurs) . ' professeurs créés avec succès !');
    }
}
