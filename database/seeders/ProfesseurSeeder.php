<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Professeur;
use App\Enums\RoleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfesseurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Vider la table des professeurs
        DB::table('professeurs')->truncate();
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Créer 10 professeurs
        $professeurs = User::where('role', RoleType::PROFESSEUR)->get();
        
        if ($professeurs->isEmpty()) {
            $this->command->info('Aucun utilisateur avec le rôle professeur trouvé. Création de 10 professeurs...');
            
            for ($i = 1; $i <= 10; $i++) {
                $user = User::factory()->create([
                    'role' => RoleType::PROFESSEUR,
                    'email' => 'professeur' . $i . '@allotawjih.com',
                ]);
                $user->assignRole('professeur');
                
                Professeur::factory()->create([
                    'user_id' => $user->id,
                    'specialite' => $this->getRandomSpecialite(),
                    'date_embauche' => now()->subYears(rand(1, 10))->subMonths(rand(0, 11)),
                    'salaire' => rand(6000, 15000),
                ]);
                
                $this->command->info("Professeur #$i créé : " . $user->email);
            }
        } else {
            foreach ($professeurs as $professeur) {
                if (!$professeur->professeur) {
                    Professeur::factory()->create([
                        'user_id' => $professeur->id,
                        'specialite' => $this->getRandomSpecialite(),
                        'date_embauche' => now()->subYears(rand(1, 10))->subMonths(rand(0, 11)),
                        'salaire' => rand(6000, 15000),
                    ]);
                    
                    $this->command->info("Profil professeur créé pour l'utilisateur : " . $professeur->email);
                }
            }
        }
    }
    
    /**
     * Obtenir une spécialité aléatoire
     */
    private function getRandomSpecialite(): string
    {
        $specialites = [
            'Mathématiques',
            'Physique-Chimie',
            'Sciences de la Vie et de la Terre',
            'Français',
            'Anglais',
            'Arabe',
            'Philosophie',
            'Histoire-Géographie',
            'Éducation Islamique',
            'Éducation Physique et Sportive',
            'Informatique',
            'Sciences Économiques',
            'Comptabilité',
        ];
        
        return $specialites[array_rand($specialites)];
    }
}
