<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Etudiant;
use App\Models\Enseignant;
use App\Models\Classe;
use App\Models\Cours;
use App\Models\Paiement;
use App\Models\Absence;
use App\Models\Notification;
use App\Models\Niveau;
use App\Models\Filiere;
use Illuminate\Support\Facades\Schema;
use App\Models\Matiere;
use App\Enums\RoleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Initialiser Faker
        $this->faker = \Faker\Factory::create('fr_FR');
        
        // Désactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Vider les tables
        $this->truncateTables([
            'users', 'etudiants', 'enseignants', 'classes', 'cours',
            'paiements', 'absences', 'notifications', 'enseignements', 'inscriptions'
        ]);

        // Activer les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Créer les niveaux s'ils n'existent pas
        $niveaux = [
            ['nom' => '6ème', 'code' => '6EME', 'description' => 'Sixième année du collège', 'ordre' => 1],
            ['nom' => '5ème', 'code' => '5EME', 'description' => 'Cinquième année du collège', 'ordre' => 2],
            ['nom' => '4ème', 'code' => '4EME', 'description' => 'Quatrième année du collège', 'ordre' => 3],
            ['nom' => '3ème', 'code' => '3EME', 'description' => 'Troisième année du collège', 'ordre' => 4],
            ['nom' => 'Tronc Commun', 'code' => 'TC', 'description' => 'Tronc commun scientifique et littéraire', 'ordre' => 5],
            ['nom' => '1ère année Bac', 'code' => '1BAC', 'description' => 'Première année du baccalauréat', 'ordre' => 6],
            ['nom' => '2ème année Bac', 'code' => '2BAC', 'description' => 'Deuxième année du baccalauréat', 'ordre' => 7],
        ];

        foreach ($niveaux as $niveau) {
            Niveau::firstOrCreate(['code' => $niveau['code']], $niveau);
        }

        // Créer les filières avec niveau_id
        $niveaux = Niveau::all();
        $filieres = [
            [
                'code' => 'SM', 
                'nom' => 'Sciences Mathématiques', 
                'description' => 'Filière Sciences Mathématiques',
                'duree_annees' => 2,
                'frais_inscription' => 1500,
                'frais_mensuel' => 800,
                'niveau_id' => $niveaux->where('code', '1BAC')->first()->id
            ],
            [
                'code' => 'SPC', 
                'nom' => 'Sciences Physiques', 
                'description' => 'Filière Sciences Physiques et Chimie',
                'duree_annees' => 2,
                'frais_inscription' => 1400,
                'frais_mensuel' => 750,
                'niveau_id' => $niveaux->where('code', '1BAC')->first()->id
            ],
            [
                'code' => 'SVT', 
                'nom' => 'Sciences Vie et Terre', 
                'description' => 'Filière Sciences de la Vie et de la Terre',
                'duree_annees' => 2,
                'frais_inscription' => 1300,
                'frais_mensuel' => 700,
                'niveau_id' => $niveaux->where('code', '1BAC')->first()->id
            ],
            [
                'code' => 'LET', 
                'nom' => 'Lettres', 
                'description' => 'Filière Littéraire',
                'duree_annees' => 2,
                'frais_inscription' => 1200,
                'frais_mensuel' => 650,
                'niveau_id' => $niveaux->where('code', '1BAC')->first()->id
            ],
            [
                'code' => 'ECO', 
                'nom' => 'Sciences Économiques', 
                'description' => 'Filière Sciences Économiques',
                'duree_annees' => 2,
                'frais_inscription' => 1400,
                'frais_mensuel' => 750,
                'niveau_id' => $niveaux->where('code', '1BAC')->first()->id
            ],
            [
                'code' => 'SH', 
                'nom' => 'Sciences Humaines', 
                'description' => 'Filière Sciences Humaines',
                'duree_annees' => 2,
                'frais_inscription' => 1300,
                'frais_mensuel' => 700,
                'niveau_id' => $niveaux->where('code', '1BAC')->first()->id
            ],
        ];

        foreach ($filieres as $filiere) {
            Filiere::firstOrCreate(['code' => $filiere['code']], $filiere);
        }

        // Créer les matières
        $matieres = [
            ['code' => 'MATH', 'nom' => 'Mathématiques', 'type' => 'scientifique', 'prix' => 300, 'prix_prof' => 150],
            ['code' => 'PHYS', 'nom' => 'Physique', 'type' => 'scientifique', 'prix' => 280, 'prix_prof' => 140],
            ['code' => 'CHIM', 'nom' => 'Chimie', 'type' => 'scientifique', 'prix' => 260, 'prix_prof' => 130],
            ['code' => 'SVT', 'nom' => 'Sciences de la Vie et de la Terre', 'type' => 'scientifique', 'prix' => 250, 'prix_prof' => 125],
            ['code' => 'FR', 'nom' => 'Français', 'type' => 'litteraire', 'prix' => 200, 'prix_prof' => 100],
            ['code' => 'AR', 'nom' => 'Arabe', 'type' => 'litteraire', 'prix' => 180, 'prix_prof' => 90],
            ['code' => 'EN', 'nom' => 'Anglais', 'type' => 'langue', 'prix' => 220, 'prix_prof' => 110],
            ['code' => 'HIST', 'nom' => 'Histoire-Géographie', 'type' => 'litteraire', 'prix' => 190, 'prix_prof' => 95],
            ['code' => 'PHILO', 'nom' => 'Philosophie', 'type' => 'litteraire', 'prix' => 210, 'prix_prof' => 105],
            ['code' => 'ECO', 'nom' => 'Économie', 'type' => 'scientifique', 'prix' => 230, 'prix_prof' => 115],
        ];

        foreach ($matieres as $matiere) {
            Matiere::firstOrCreate(['code' => $matiere['code']], $matiere);
        }

        // Créer les utilisateurs (admins, professeurs, élèves, assistants)
        $this->createUsers();
        
        // Créer les classes
        $this->createClasses();
        
        // Créer les cours
        $this->createCours();
        
        // Créer les paiements
        $this->createPaiements();
        
        // Créer les absences
        $this->createAbsences();
        
        // Créer les notifications
        $this->createNotifications();
    }

    protected function createUsers()
    {
        // Créer l'administrateur
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => RoleType::ADMIN->value,
                'is_active' => true,
            ]
        );

        // Créer l'assistant
        User::firstOrCreate(
            ['email' => 'assistant@example.com'],
            [
                'name' => 'Assistant',
                'password' => Hash::make('password'),
                'role' => RoleType::ASSISTANT->value,
                'is_active' => true,
            ]
        );

        // Créer 10 professeurs
        for ($i = 1; $i <= 10; $i++) {
            $email = 'professeur' . $i . '@example.com';
            $userData = [
                'name' => 'Professeur ' . $i,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => RoleType::PROFESSEUR->value,
                'is_active' => true,
                'phone' => $this->faker->phoneNumber,
            ];
            
            $user = User::firstOrCreate(['email' => $email], $userData);
            
            // Create corresponding Enseignant record
            Enseignant::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nom' => 'Professeur',
                    'prenom' => 'Prénom' . $i,
                    'email' => $email,
                    'telephone' => $user->phone,
                    'specialite' => $this->faker->randomElement(['Mathématiques', 'Physique', 'Chimie', 'SVT', 'Français', 'Arabe', 'Anglais', 'Histoire', 'Géographie', 'Philosophie']),
                    'date_naissance' => now()->subYears(rand(30, 60))->format('Y-m-d'),
                    'date_embauche' => now()->subYears(rand(1, 10))->format('Y-m-d'),
                    'salaire_base' => $this->faker->numberBetween(8000, 15000),
                ]
            );
        }

        // Créer 50 étudiants
        for ($i = 1; $i <= 50; $i++) {
            $email = 'etudiant' . $i . '@example.com';
            $userData = [
                'name' => 'Étudiant ' . $i,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => RoleType::ELEVE->value,
                'is_active' => true,
                'phone' => $this->faker->phoneNumber,
            ];
            
            $user = User::firstOrCreate(['email' => $email], $userData);
            
            // Create corresponding Etudiant record
            $codeEtudiant = 'ETD' . str_pad($i, 4, '0', STR_PAD_LEFT);
            Etudiant::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'code_etudiant' => $codeEtudiant,
                    'nom' => 'Étudiant',
                    'prenom' => 'Prénom' . $i,
                    'email' => $email,
                    'telephone' => $user->phone,
                    'adresse' => $this->faker->address,
                    'ville' => $this->faker->city,
                    'pays' => 'Maroc',
                    'date_naissance' => $this->faker->dateTimeBetween('-25 years', '-15 years')->format('Y-m-d'),
                    'lieu_naissance' => $this->faker->city,
                    'cin' => 'AB' . $this->faker->randomNumber(6),
                    'cne' => $this->faker->randomNumber(8),
                    'sexe' => $this->faker->randomElement(['M', 'F']),
                    'notes' => null,
                    'classe_id' => null, // Will be set when creating classes
                ]
            );
        }

        // Créer 5 assistants
        for ($i = 1; $i <= 5; $i++) {
            $email = 'assistant' . $i . '@example.com';
            $userData = [
                'name' => 'Assistant ' . $i,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => RoleType::ASSISTANT->value,
                'is_active' => true,
                'phone' => $this->faker->phoneNumber,
            ];
            
            $user = User::firstOrCreate(['email' => $email], $userData);
            
            // Create corresponding Assistant record if table exists
            if (Schema::hasTable('assistants')) {
                Assistant::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nom' => 'Assistant',
                        'prenom' => 'Prénom' . $i,
                        'email' => $email,
                        'telephone' => $user->phone,
                        'date_naissance' => $this->faker->dateTimeBetween('-50 years', '-20 years')->format('Y-m-d'),
                        'adresse' => $this->faker->address,
                        'date_embauche' => now()->subYears(rand(1, 5))->format('Y-m-d'),
                    ]
                );
            }
        }
    }

    private function createClasses(): void
    {
        $niveaux = Niveau::all();
        $filieres = Filiere::all();
        $professeurs = User::where('role', RoleType::PROFESSEUR->value)->get();

        foreach ($niveaux as $niveau) {
            foreach ($filieres as $filiere) {
                // Ne créer des classes que pour certaines combinaisons réalistes
                if ($this->shouldCreateClass($niveau->libelle, $filiere->libelle)) {
                    Classe::factory()->create([
                        'libelle' => $niveau->libelle . ' ' . $filiere->libelle,
                        'niveau_id' => $niveau->id,
                        'filiere_id' => $filiere->id,
                        'professeur_principal_id' => $professeurs->random()->id,
                        'annee_scolaire' => (date('Y') - 1) . '-' . date('Y'),
                        'capacite_max' => $this->faker->numberBetween(20, 40),
                    ]);
                }
            }
        }
    }

    private function shouldCreateClass($niveau, $filiere): bool
    {
        // Vérifier si $niveau est un objet et récupérer son nom
        $niveauNom = is_object($niveau) ? $niveau->nom : $niveau;
        
        // Vérifier si $filiere est un objet et récupérer son nom
        $filiereNom = is_object($filiere) ? $filiere->nom : $filiere;
        
        // Logique pour déterminer si une classe devrait être créée pour cette combinaison
        if (in_array($niveauNom, ['6ème', '5ème', '4ème', '3ème'])) {
            return $filiereNom === 'Sciences Mathématiques' || $filiereNom === 'Lettres';
        }
        
        if ($niveauNom === 'Tronc Commun') {
            return $filiereNom === 'Sciences Mathématiques' || $filiereNom === 'Lettres' || $filiereNom === 'Sciences Humaines';
        }
        
        if ($niveauNom === '1ère année Bac') {
            return $filiereNom === 'Sciences Mathématiques' || $filiereNom === 'Sciences Physiques' || $filiereNom === 'Sciences de la Vie et de la Terre' || $filiereNom === 'Lettres';
        }
        
        if ($niveauNom === '2ème année Bac') {
            return $filiereNom === 'Sciences Mathématiques' || $filiereNom === 'Sciences Physiques' || $filiereNom === 'Sciences de la Vie et de la Terre' || $filiereNom === 'Lettres' || $filiereNom === 'Sciences Économiques';
        }
        
        return false;
    }

    private function createCours(): void
    {
        $classes = Classe::all();
        $matieres = Matiere::all();
        $professeurs = User::where('role', RoleType::PROFESSEUR->value)->get();
        
        foreach ($classes as $classe) {
            $nbMatieres = $this->faker->numberBetween(5, 8);
            $matieresSelectionnees = $matieres->random($nbMatieres);
            
            foreach ($matieresSelectionnees as $matiere) {
                $professeur = $professeurs->random();
                
                // Créer le cours
                $cours = Cours::factory()->create([
                    'libelle' => $matiere->nom . ' - ' . $classe->libelle,
                    'matiere_id' => $matiere->id,
                    'classe_id' => $classe->id,
                    'professeur_id' => $professeur->id,
                    'annee_scolaire' => (date('Y') - 1) . '-' . date('Y'),
                ]);
                
                // Associer le professeur à la matière s'il ne l'est pas déjà
                if (!$professeur->matieres->contains($matiere->id)) {
                    $professeur->matieres()->attach($matiere->id);
                }
            }
        }
    }

    private function createPaiements(): void
    {
        $etudiants = Etudiant::all();
        $users = User::whereIn('role', [
            1, // ADMIN = 1
            3  // ASSISTANT = 3
        ])->get();
        
        // Get some matieres for paiements
        $matieres = Matiere::all();
        
        // If no matieres exist, create a default one
        if ($matieres->isEmpty()) {
            $matiere = Matiere::create([
                'nom' => 'Frais de scolarité',
                'code' => 'SCOL',
                'prix' => 5000,
                'prix_prof' => 1000,
                'type' => 'obligatoire',
                'description' => 'Frais de scolarité annuels'
            ]);
            $matieres = collect([$matiere]);
        }
        
        // If no users exist with the required roles, skip creating payments
        if ($users->isEmpty()) {
            $this->command->info('Skipping payments creation: No users with required roles (ADMIN or ASSISTANT) found.');
            return;
        }
        
        foreach ($etudiants as $etudiant) {
            // Créer entre 1 et 3 paiements par étudiant
            $nbPaiements = $this->faker->numberBetween(1, 3);
            
            for ($i = 0; $i < $nbPaiements; $i++) {
                $montant = $this->faker->numberBetween(500, 2000);
                $montantPaye = $this->faker->numberBetween(300, $montant);
                $reste = $montant - $montantPaye;
                $modePaiement = $this->faker->randomElement(['especes', 'cheque', 'virement', 'carte']);
                $datePaiement = $this->faker->dateTimeBetween('-6 months', 'now');
                $moisPeriode = $datePaiement->format('Y-m');
                $statut = $montantPaye >= $montant ? 'paye' : 'partiel';
                
                $assistant = $users->isNotEmpty() ? $users->random() : null;
                
                $paiementData = [
                    'etudiant_id' => $etudiant->id,
                    'eleve_id' => $etudiant->id, // Same as etudiant_id for backward compatibility
                    'matiere_id' => $matieres->isNotEmpty() ? $matieres->random()->id : null,
                    'assistant_id' => $assistant ? $assistant->id : null,
                    'user_id' => $assistant ? $assistant->id : 1, // Use assistant's ID or default to admin (ID 1)
                    'montant' => $montant,
                    'montant_paye' => $montant, // Set montant_paye to full amount for now
                    'reste' => 0, // No remaining amount since we're paying in full
                    'mode_paiement' => $modePaiement,
                    'reference_paiement' => 'PAY-' . strtoupper(uniqid()),
                    'date_paiement' => $datePaiement->format('Y-m-d'),
                    'statut' => 'valide',
                    'commentaires' => $this->faker->sentence(),
                    'mois_periode' => $moisPeriode,
                ];
                
                // Create the paiement directly without using the factory
                Paiement::create($paiementData);
            }
        }
    }

    private function createAbsences(): void
    {
        $etudiants = Etudiant::all();
        $cours = Cours::all();
        $users = User::whereIn('role', [
            1, // ADMIN = 1
            2, // PROFESSEUR = 2
            3  // ASSISTANT = 3
        ])->get();
        
        // If no cours exist, skip creating absences
        if ($cours->isEmpty() || $users->isEmpty()) {
            $this->command->info('Skipping absences creation: No cours or users available.');
            return;
        }
        
        // Créer environ 3 absences par étudiant
        foreach ($etudiants as $etudiant) {
            $nbAbsences = $this->faker->numberBetween(0, 5);
            
            for ($i = 0; $i < $nbAbsences; $i++) {
                $coursAbsent = $cours->random();
                $dateAbsence = $this->faker->dateTimeBetween('-3 months', 'now');
                $justifie = $this->faker->boolean(30); // 30% de chance d'être justifié
                
                Absence::create([
                    'etudiant_id' => $etudiant->id,
                    'cours_id' => $coursAbsent->id,
                    'user_id' => $users->random()->id,
                    'date_absence' => $dateAbsence,
                    'justifie' => $justifie,
                    'motif' => $justifie ? $this->faker->randomElement([
                        'Maladie', 'Problème de transport', 'Rendez-vous médical',
                        'Raison familiale', 'Autre raison personnelle'
                    ]) : null,
                    'date_saisie' => $dateAbsence,
                    'annee_scolaire' => (date('Y') - 1) . '-' . date('Y'),
                ]);
            }
        }
    }

    private function createNotifications(): void
    {
        $users = User::all();
        
        foreach ($users as $user) {
            // Créer entre 3 et 10 notifications par utilisateur
            $nbNotifications = $this->faker->numberBetween(3, 10);
            
            for ($i = 0; $i < $nbNotifications; $i++) {
                $type = $this->faker->randomElement(['info', 'success', 'warning', 'error']);
                $title = $this->generateNotificationTitle($type);
                $message = $this->generateNotificationMessage($title);
                
                // Generate notification data
                $isRead = $this->faker->boolean(70);
                $notificationData = [
                    'titre' => $title,
                    'contenu' => $message,
                    'user_id' => $user->id,
                    'type' => $type,
                    'est_lu' => $isRead,
                    'date_lecture' => $isRead ? $this->faker->dateTimeBetween('-3 months', 'now') : null,
                    'lien' => $this->faker->optional(0.7)->url,
                    'donnees' => [
                        'type' => $type,
                        'title' => $title,
                    ],
                    'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
                    'updated_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
                ];
                
                // Only add eleve_id if the user is not a student
                if ($user->role !== RoleType::ELEVE->value) {
                    $eleve = User::where('role', RoleType::ELEVE->value)->inRandomOrder()->first();
                    if ($eleve) {
                        $notificationData['donnees']['eleve_id'] = $eleve->id;
                    }
                }
                
                Notification::create($notificationData);
            }
        }
    }

    private function generateNotificationTitle(string $type): string
    {
        $templates = [
            'info' => [
                'Nouvelle annonce : ' . $this->faker->sentence(3, true),
                'Mise à jour du calendrier : ' . $this->faker->sentence(3, true),
                'Rappel : ' . $this->faker->sentence(3, true),
            ],
            'success' => [
                'Paiement confirmé pour ' . $this->faker->name,
                'Inscription validée pour ' . $this->faker->name,
                'Absence justifiée avec succès',
            ],
            'warning' => [
                'Attention : ' . $this->faker->sentence(3, true),
                'Paiement en retard pour ' . $this->faker->name,
                'Absence non justifiée le ' . $this->faker->date('d/m/Y'),
            ],
            'error' => [
                'Erreur lors du traitement de votre demande',
                'Échec de la validation du paiement',
                'Problème technique détecté',
            ]
        ];
        
        return $this->faker->randomElement($templates[$type]);
    }

    private function generateNotificationMessage(string $titre): string
    {
        $messages = [
            'Nouvelle annonce' => 'Une nouvelle annonce a été publiée. Veuillez la consulter dès que possible.',
            'Mise à jour du calendrier' => 'Le calendrier scolaire a été mis à jour avec de nouvelles dates importantes.',
            'Rappel' => 'Rappel : ' . $this->faker->sentence(6, true),
            'Paiement confirmé' => 'Votre paiement a été confirmé avec succès. Merci pour votre confiance.',
            'Inscription validée' => 'Votre inscription a été validée. Bienvenue dans notre établissement !',
            'Absence justifiée' => 'Votre absence a été enregistrée comme justifiée.',
            'Attention' => 'Attention : ' . $this->faker->sentence(6, true),
            'Paiement en retard' => 'Votre paiement est en retard. Veuillez régulariser votre situation au plus vite.',
            'Absence non justifiée' => 'Votre absence du ' . $this->faker->date('d/m/Y') . ' n\'a pas encore été justifiée.',
            'Erreur' => 'Une erreur est survenue lors du traitement de votre demande. Veuillez réessayer.',
            'Échec' => 'L\'opération a échoué. Veuillez contacter l\'administrateur si le problème persiste.',
            'Problème technique' => 'Un problème technique a été détecté. Notre équipe travaille à sa résolution.',
        ];
        
        foreach ($messages as $key => $message) {
            if (str_contains($titre, $key)) {
                return $message;
            }
        }
        
        return $this->faker->paragraph(2, true);
    }

    /**
     * Vider les tables spécifiées
     */
    private function truncateTables(array $tables): void
    {
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }
}
