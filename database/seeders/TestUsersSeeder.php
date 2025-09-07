<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Matiere;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestUsersSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Créer un professeur de test
        $professeur = User::updateOrCreate(
            ['email' => 'prof@demo.com'],
            [
                'name' => 'Professeur',
                'email' => 'prof@demo.com',
                'password' => Hash::make('password'),
                'role' => 'professeur',
                'status' => 'actif',
                'adresse' => 'Adresse du professeur',
                'telephone' => '0600000001',
                'date_naissance' => Carbon::now()->subYears(35),
                'pourcentage_remuneration' => 70, // 70% du montant des cours
                'date_embauche' => Carbon::now()->subYear(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Créer un élève de test
        $eleve = User::updateOrCreate(
            ['email' => 'eleve@demo.com'],
            [
                'name' => 'Élève',
                'email' => 'eleve@demo.com',
                'password' => Hash::make('password'),
                'role' => 'eleve',
                'status' => 'actif',
                'adresse' => 'Adresse de l\'élève',
                'telephone' => '0600000002',
                'date_naissance' => Carbon::now()->subYears(15),
                'cne' => 'A123456789',
                'nom_pere' => 'Père de l\'élève',
                'telephone_pere' => '0600000003',
                'nom_mere' => 'Mère de l\'élève',
                'telephone_mere' => '0600000004',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Associer le professeur aux matières (Maths et Physique)
        $maths = Matiere::where('nom', 'Mathématiques')->first();
        $physique = Matiere::where('nom', 'Physique-Chimie')->first();
        
        if ($maths) {
            $professeur->matieresEnseignees()->syncWithoutDetaching([
                $maths->id => [
                    'est_responsable' => true,
                    'date_debut' => now(),
                ]
            ]);
        }
        
        if ($physique) {
            $professeur->matieresEnseignees()->syncWithoutDetaching([
                $physique->id => [
                    'est_responsable' => true,
                    'date_debut' => now(),
                ]
            ]);
        }

        // Inscrire l'élève aux matières (Maths)
        if ($maths) {
            $eleve->matieres()->syncWithoutDetaching([
                $maths->id => ['date_inscription' => now()]
            ]);
        }

        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ Utilisateurs de test créés avec succès!');
        $this->command->info('👨‍🏫 Professeur: prof@demo.com / password');
        $this->command->info('👨‍🎓 Élève: eleve@demo.com / password');
    }
}
