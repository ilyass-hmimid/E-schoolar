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
                
                // Créer l'utilisateur étudiant
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $prenom . ' ' . $nom,
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'password' => Hash::make('password'),
                        'role' => RoleType::ELEVE,
                        'phone' => $telephone,
                        'address' => rand(1, 200) . ' Rue ' . $nom . ', ' . $ville,
                        'niveau_id' => $classe->niveau_id,
                        'filiere_id' => $classe->filiere_id,
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ]
                );
                
                // Attribuer le rôle étudiant
                if (!$user->hasRole('eleve')) {
                    $user->assignRole('eleve');
                }
                
                // Créer le profil étudiant
                Etudiant::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'classe_id' => $classe->id,
                        'date_naissance' => now()->subYears(rand(15, 20))->subMonths(rand(0, 11))->subDays(rand(0, 30)),
                        'lieu_naissance' => $ville,
                        'adresse' => $user->address,
                        'telephone' => $telephone,
                        'nom_pere' => 'M. ' . $noms[array_rand($noms)],
                        'telephone_pere' => '06' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                        'nom_mere' => 'Mme. ' . $noms[array_rand($noms)],
                        'telephone_mere' => '06' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                        'date_inscription' => now()->subMonths(rand(0, 6)),
                        'derniere_annee_etudes' => $classe->niveau->libelle,
                        'dernier_etablissement' => 'Lycée ' . $noms[array_rand($noms)],
                        'numero_securite_sociale' => 'J' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                        'numero_carte_nationale' => 'AB' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
                        'est_boursier' => rand(0, 1) == 1,
                        'est_handicape' => rand(0, 10) > 8, // 20% de chance d'être en situation de handicap
                        'informations_handicap' => function() {
                            return rand(0, 1) == 1 ? 'Besoins éducatifs particuliers' : null;
                        },
                        'regime_special' => function() {
                            return rand(0, 1) == 1 ? 'Régime sans gluten' : null;
                        },
                        'est_redoublant' => rand(0, 10) > 7, // 30% de chance d'être redoublant
                    ]
                );
                
                $etudiantsCrees++;
                $this->command->info("Étudiant créé : $prenom $nom ($email) dans la classe " . $classe->libelle);
                
                // Mettre à jour l'utilisateur avec l'ID de l'étudiant
                $user->etudiant_id = $user->etudiant->id;
                $user->save();
            }
        }
        
        $this->command->info("$etudiantsCrees étudiants créés avec succès !");
    }
}
