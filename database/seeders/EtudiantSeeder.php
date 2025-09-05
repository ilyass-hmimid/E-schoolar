<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Classe;
use App\Models\Etudiant;
use App\Enums\RoleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EtudiantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Vider la table des étudiants
        DB::table('etudiants')->truncate();
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Récupérer les classes
        $classes = Classe::all();
        
        if ($classes->isEmpty()) {
            $this->command->error('Veuillez d\'abord exécuter ClasseSeeder pour créer des classes.');
            return;
        }

        // Tableau des prénoms et noms marocains courants
        $prenoms = ['Mohamed', 'Ahmed', 'Fatima', 'Youssef', 'Amina', 'Mehdi', 'Sara', 'Omar', 'Nada', 'Imane', 'Karim', 'Hassan', 'Lina', 'Zineb', 'Amine'];
        $noms = ['Alaoui', 'El Mansouri', 'Benjelloun', 'El Fassi', 'Bennani', 'Cherkaoui', 'Idrissi', 'El Khayat', 'Bouzidi', 'Moujtahid', 'Rahmouni', 'Saidi', 'Tazi', 'Zeroual', 'Bennacer'];
        $villes = ['Casablanca', 'Rabat', 'Marrakech', 'Fès', 'Tanger', 'Meknès', 'Agadir', 'Oujda', 'Kénitra', 'Tétouan'];
        
        // Créer 50 étudiants
        $etudiantsCrees = 0;
        
        foreach ($classes as $classe) {
            // Nombre d'étudiants par classe (entre 15 et 25)
            $nombreEtudiants = rand(15, 25);
            
            for ($i = 0; $i < $nombreEtudiants && $etudiantsCrees < 50; $i++) {
                $prenom = $prenoms[array_rand($prenoms)];
                $nom = $noms[array_rand($noms)];
                $email = strtolower($prenom . '.' . $nom . ($i + 1) . '@etudiant.allotawjih.com');
                $telephone = '06' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
                $ville = $villes[array_rand($villes)];
                
                // Créer l'utilisateur
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => "$prenom $nom",
                        'email' => $email,
                        'password' => Hash::make('password'),
                        'phone' => $telephone,
                        'role' => RoleType::ELEVE->value,
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ]
                );
                
                // Attribuer le rôle étudiant
                if (!$user->hasRole('eleve')) {
                    $user->assignRole('eleve');
                }
                
                // Créer le profil étudiant
                $codeEtudiant = 'ETU' . $user->id . '_' . time() . rand(10, 99);
                Etudiant::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id' => $user->id,
                        'classe_id' => $classe->id,
                        'code_etudiant' => $codeEtudiant,
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'email' => $user->email,
                        'telephone' => $telephone,
                        'adresse' => rand(1, 200) . ' Rue ' . $nom . ', ' . $ville,
                        'ville' => $ville,
                        'pays' => 'Maroc',
                        'date_naissance' => now()->subYears(rand(15, 20))->subMonths(rand(0, 11))->subDays(rand(0, 30))->format('Y-m-d'),
                        'lieu_naissance' => $ville,
                        'cin' => 'AB' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
                        'cne' => 'G' . str_pad(rand(100000, 999999), 7, '0', STR_PAD_LEFT),
                        'sexe' => ['M', 'F'][rand(0, 1)],
                        'photo' => null,
                        'notes' => null, // 30% de chance d'être redoublant
                    ]
                );
                
                $etudiantsCrees++;
                $this->command->info("Étudiant créé : $prenom $nom ($email) dans la classe " . $classe->nom);
            }
        }
        
        $this->command->info("$etudiantsCrees étudiants créés avec succès !");
    }
}
