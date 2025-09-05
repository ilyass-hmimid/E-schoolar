<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Professeur;
use App\Enums\RoleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
                // Créer l'utilisateur
                $user = User::create([
                    'name' => 'Professeur ' . $i,
                    'email' => 'professeur' . $i . '@allotawjih.com',
                    'password' => Hash::make('password'),
                    'role' => RoleType::PROFESSEUR,
                    'phone' => '06' . str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT),
                    'address' => 'Adresse du professeur ' . $i,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
                
                $user->assignRole('professeur');
                
                // Créer le profil professeur avec tous les champs requis
                $dateNaissance = now()->subYears(rand(25, 60))->subMonths(rand(0, 11))->format('Y-m-d');
                $dateEmbauche = now()->subYears(rand(1, 10))->subMonths(rand(0, 11))->format('Y-m-d');
                
                Professeur::create([
                    'user_id' => $user->id,
                    'nom' => 'Professeur',
                    'prenom' => $i,
                    'email' => $user->email,
                    'telephone' => $user->phone,
                    'specialite' => $this->getRandomSpecialite(),
                    'date_naissance' => $dateNaissance,
                    'lieu_naissance' => 'Ville ' . $i,
                    'adresse' => $user->address,
                    'ville' => 'Ville ' . $i,
                    'pays' => 'Maroc',
                    'cin' => 'A' . str_pad($i, 7, '0', STR_PAD_LEFT),
                    'carte_sejour' => null,
                    'numero_securite_sociale' => 'J' . str_pad($i, 11, '0', STR_PAD_LEFT),
                    'situation_familiale' => $this->getRandomSituationFamiliale(),
                    'nombre_enfants' => rand(0, 5),
                    'niveau_etude' => 'Bac +5',
                    'diplome' => 'Master',
                    'specialite_diplome' => $this->getRandomSpecialite(),
                    'etablissement_diplome' => 'Université ' . $i,
                    'annee_obtention_diplome' => (string)(now()->year - rand(5, 30)),
                    'salaire_base' => rand(5000, 15000),
                    'type_contrat' => 'CDI',
                    'date_embauche' => $dateEmbauche,
                    'date_fin_contrat' => null,
                    'banque' => 'Banque ' . $i,
                    'numero_compte_bancaire' => 'MA' . str_pad($i, 22, '0', STR_PAD_LEFT),
                    'nom_urgence' => 'Contact Urgence ' . $i,
                    'telephone_urgence' => '06' . str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT),
                    'lien_parente_urgence' => 'Famille',
                    'notes' => 'Notes pour le professeur ' . $i,
                    'est_actif' => true,
                ]);
                
                $this->command->info("Professeur #$i créé : " . $user->email);
            }
        } else {
            foreach ($professeurs as $professeur) {
                if (!Professeur::where('user_id', $professeur->id)->exists()) {
                    // Créer le profil professeur avec tous les champs requis
                    $dateNaissance = now()->subYears(rand(25, 60))->subMonths(rand(0, 11))->format('Y-m-d');
                    $dateEmbauche = now()->subYears(rand(1, 10))->subMonths(rand(0, 11))->format('Y-m-d');
                    
                    $nomPrenom = explode(' ', $professeur->name, 2);
                    $nom = $nomPrenom[0] ?? 'Professeur';
                    $prenom = $nomPrenom[1] ?? 'Inconnu';
                    
                    Professeur::create([
                        'user_id' => $professeur->id,
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'email' => $professeur->email,
                        'telephone' => $professeur->phone,
                        'specialite' => $this->getRandomSpecialite(),
                        'date_naissance' => $dateNaissance,
                        'lieu_naissance' => 'Ville inconnue',
                        'adresse' => $professeur->address ?? 'Adresse inconnue',
                        'ville' => 'Ville inconnue',
                        'pays' => 'Maroc',
                        'cin' => 'B' . str_pad($professeur->id, 7, '0', STR_PAD_LEFT),
                        'carte_sejour' => null,
                        'numero_securite_sociale' => 'K' . str_pad($professeur->id, 11, '0', STR_PAD_LEFT),
                        'situation_familiale' => $this->getRandomSituationFamiliale(),
                        'nombre_enfants' => rand(0, 5),
                        'niveau_etude' => 'Bac +5',
                        'diplome' => 'Master',
                        'specialite_diplome' => $this->getRandomSpecialite(),
                        'etablissement_diplome' => 'Université inconnue',
                        'annee_obtention_diplome' => (string)now()->subYears(rand(5, 30))->year,
                        'salaire_base' => rand(5000, 15000),
                        'type_contrat' => 'CDI',
                        'date_embauche' => $dateEmbauche,
                        'date_fin_contrat' => null,
                        'banque' => 'Banque inconnue',
                        'numero_compte_bancaire' => 'MA' . str_pad($professeur->id, 22, '0', STR_PAD_LEFT),
                        'nom_urgence' => 'Contact Urgence',
                        'telephone_urgence' => '06' . str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT),
                        'lien_parente_urgence' => 'Famille',
                        'notes' => 'Notes pour le professeur ' . $professeur->id,
                        'est_actif' => true,
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
    
    /**
     * Obtenir une situation familiale aléatoire
     */
    private function getRandomSituationFamiliale(): string
    {
        $situations = [
            'Célibataire',
            'Marié(e)',
            'Divorcé(e)',
            'Veuf/Veuve',
        ];
        
        return $situations[array_rand($situations)];
    }
}
