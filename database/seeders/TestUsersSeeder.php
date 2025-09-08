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
                'prenom' => 'Test',
                'email' => 'prof@demo.com',
                'password' => Hash::make('password'),
                'role' => 'professeur',
                'status' => 'actif',
                'is_active' => true,
                'adresse' => 'Adresse du professeur',
                'telephone' => '0600000001',
                'date_naissance' => Carbon::now()->subYears(35),
                'lieu_naissance' => 'Ville de naissance',
                'sexe' => 'Homme',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Créer un élève de test
        $eleve = User::updateOrCreate(
            ['email' => 'eleve@demo.com'],
            [
                'name' => 'Élève',
                'prenom' => 'Test',
                'email' => 'eleve@demo.com',
                'password' => Hash::make('password'),
                'role' => 'eleve',
                'status' => 'actif',
                'is_active' => true,
                'adresse' => 'Adresse de l\'élève',
                'sexe' => 'Femme',
                'telephone' => '0600000002',
                'date_naissance' => Carbon::now()->subYears(15),
                'lieu_naissance' => 'Ville de naissance',
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
        
        // Récupérer un niveau et une filière existants
        $niveau = DB::table('niveaux')->where('code', '2BAC')->first();
        $filiere = DB::table('filieres')->where('code', 'SM')->first();
        
        if ($maths && $niveau && $filiere) {
            $professeur->matieresEnseignees()->syncWithoutDetaching([
                $maths->id => [
                    'niveau_id' => $niveau->id,
                    'filiere_id' => $filiere->id,
                    'date_debut' => now(),
                ]
            ]);
        }
        
        if ($physique && $niveau && $filiere) {
            $professeur->matieresEnseignees()->syncWithoutDetaching([
                $physique->id => [
                    'niveau_id' => $niveau->id,
                    'filiere_id' => $filiere->id,
                    'date_debut' => now(),
                ]
            ]);
        }

        // Inscrire l'élève aux matières (Maths)
        if ($maths) {
            $eleve->matieres()->syncWithoutDetaching([$maths->id]);
        }

        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ Utilisateurs de test créés avec succès!');
        $this->command->info('👨‍🏫 Professeur: prof@demo.com / password');
        $this->command->info('👨‍🎓 Élève: eleve@demo.com / password');
    }
}
