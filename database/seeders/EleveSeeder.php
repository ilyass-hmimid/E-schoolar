<?php

namespace Database\Seeders;

use App\Models\Eleve;
use App\Models\Classe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class EleveSeeder extends Seeder
{
    private $villesMaroc = [
        'Casablanca', 'Rabat', 'Marrakech', 'Fès', 'Tanger',
        'Agadir', 'Meknès', 'Oujda', 'Kénitra', 'Tétouan'
    ];
    
    private $prenomsGarcons = [
        'Mehdi', 'Youssef', 'Omar', 'Adam', 'Ilyas',
        'Amine', 'Ayoub', 'Anas', 'Hassan', 'Karim'
    ];
    
    private $prenomsFilles = [
        'Salma', 'Amina', 'Lina', 'Yasmin', 'Zineb',
        'Fatima', 'Sara', 'Nada', 'Houda', 'Imane'
    ];
    
    private $noms = [
        'El Amrani', 'Bennani', 'Alaoui', 'El Fassi', 'Cherkaoui',
        'Zeroual', 'Saidi', 'Moujtahid', 'Idrissi', 'Rahmouni',
        'Bouzidi', 'El Khayat', 'Bennacer', 'El Mansouri', 'Zerouali'
    ];
    
    private $professions = [
        'Médecin', 'Enseignant', 'Ingénieur', 'Comptable', 'Avocat',
        'Infirmier', 'Architecte', 'Commerçant', 'Fonctionnaire', 'Artisan',
        'Cadre', 'Technicien', 'Agriculteur', 'Journaliste', 'Artiste'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        
        // Récupérer toutes les classes
        $classes = Classe::all();
        
        if ($classes->isEmpty()) {
            $this->command->info('Aucune classe trouvée. Veuillez d\'abord créer des classes.');
            return;
        }

        // Créer 20 élèves (10 garçons et 10 filles)
        for ($i = 1; $i <= 20; $i++) {
            $classe = $classes->random();
            $estFille = $i % 2 === 0;
            $prenom = $estFille 
                ? $this->prenomsFilles[array_rand($this->prenomsFilles)]
                : $this->prenomsGarcons[array_rand($this->prenomsGarcons)];
                
            $nom = $this->noms[array_rand($this->noms)];
            $email = strtolower($prenom . '.' . str_replace(' ', '', $nom)) . $i . '@etudiant.allotawjih.com';
            $ville = $this->villesMaroc[array_rand($this->villesMaroc)];
            
            // Créer l'utilisateur
            $user = User::create([
                'name' => $prenom . ' ' . $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'eleve',
                'telephone' => '06' . $faker->numberBetween(10000000, 99999999),
                'adresse' => $faker->streetAddress . ', ' . $ville,
                'classe_id' => $classe->id,
                'is_active' => true,
                'sexe' => $estFille ? 'Femme' : 'Homme',
                'date_inscription' => now(),
                'somme_a_payer' => 0,
                'date_debut' => now(),
            ]);

            // Créer l'élève avec des données réalistes
            Eleve::create([
                'user_id' => $user->id,
                'classe_id' => $classe->id,
                'date_naissance' => now()->subYears(rand(14, 19))->subMonths(rand(0, 11))->format('Y-m-d'),
                'ville' => $ville,
                'code_postal' => $faker->postcode,
                'pays' => 'Maroc',
                'nom_pere' => 'M. ' . $nom,
                'profession_pere' => $this->professions[array_rand($this->professions)],
                'telephone_pere' => '06' . $faker->numberBetween(10000000, 99999999),
                'nom_mere' => 'Mme ' . $nom,
                'profession_mere' => $this->professions[array_rand($this->professions)],
                'telephone_mere' => '06' . $faker->numberBetween(10000000, 99999999),
                'notes' => $faker->optional(0.7)->sentence(), // 70% de chance d'avoir une note
                'est_actif' => $faker->boolean(90), // 90% de chance d'être actif
            ]);
            
            $this->command->info('Élève créé : ' . $user->name . ' (' . $email . ') dans la classe ' . $classe->libelle);
        }

        $this->command->info('20 élèves ont été créés avec succès !');
    }
}
