<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Salaire;
use App\Models\ConfigurationSalaire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class SalaireApiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $professeur;
    protected $matiere;
    protected $configurationSalaire;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Création d'un administrateur
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password')
        ]);
        
        // Création d'un professeur
        $this->professeur = User::factory()->create([
            'role' => 'professeur',
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@example.com',
            'password' => Hash::make('password')
        ]);
        
        // Création d'une matière
        $this->matiere = Matiere::factory()->create([
            'nom' => 'Mathématiques',
            'description' => 'Mathématiques avancées',
            'coefficient' => 4
        ]);
        
        // Création d'une configuration de salaire pour la matière
        $this->configurationSalaire = ConfigurationSalaire::create([
            'matiere_id' => $this->matiere->id,
            'prix_unitaire' => 150.50,
            'commission_prof' => 30.0,
            'prix_min' => 100.00,
            'prix_max' => 200.00,
            'est_actif' => true,
            'description' => 'Configuration pour les cours de mathématiques'
        ]);
        
        // Connexion et récupération du token
        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password'
        ]);
        
        $this->token = $response->json('token');
    }
    
    /** @test */
    public function test_lister_salaires()
    {
        // Créer des salaires de test
        Salaire::factory()->count(3)->create([
            'professeur_id' => $this->professeur->id,
            'matiere_id' => $this->matiere->id,
            'mois_periode' => '2025-08',
            'statut' => 'en_attente'
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/salaires?mois_periode=2025-08');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'professeur_id',
                            'matiere_id',
                            'mois_periode',
                            'nombre_eleves',
                            'prix_unitaire',
                            'commission_prof',
                            'montant_brut',
                            'montant_commission',
                            'montant_net',
                            'statut',
                            'date_paiement',
                            'commentaires',
                            'created_at',
                            'updated_at',
                            'professeur' => [
                                'id',
                                'nom',
                                'prenom',
                                'email'
                            ],
                            'matiere' => [
                                'id',
                                'nom',
                                'description',
                                'coefficient'
                            ]
                        ]
                    ],
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total'
                ]
            ]);
            
        $response->assertJson([
            'success' => true
        ]);
    }
    
    /** @test */
    public function test_calculer_salaires()
    {
        // Simuler des données nécessaires pour le calcul
        DB::table('enseignements')->insert([
            'professeur_id' => $this->professeur->id,
            'matiere_id' => $this->matiere->id,
            'date_debut' => '2025-08-01',
            'date_fin' => '2025-08-31',
            'statut' => 'en_cours',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Simuler des inscriptions d'élèves
        DB::table('inscriptions')->insert([
            ['user_id' => 10, 'matiere_id' => $this->matiere->id, 'date_inscription' => '2025-08-15', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 11, 'matiere_id' => $this->matiere->id, 'date_inscription' => '2025-08-16', 'created_at' => now(), 'updated_at' => now()],
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->postJson('/api/salaires/calculer', [
            'mois_periode' => '2025-08'
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'salaires' => [
                    '*' => [
                        'id',
                        'professeur_id',
                        'matiere_id',
                        'mois_periode',
                        'nombre_eleves',
                        'prix_unitaire',
                        'commission_prof',
                        'montant_brut',
                        'montant_commission',
                        'montant_net',
                        'statut',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
            
        $response->assertJson([
            'success' => true,
            'message' => 'Calcul des salaires effectué avec succès'
        ]);
        
        // Vérifier qu'un salaire a bien été créé
        $this->assertDatabaseHas('salaires', [
            'professeur_id' => $this->professeur->id,
            'matiere_id' => $this->matiere->id,
            'mois_periode' => '2025-08',
            'nombre_eleves' => 2,
            'statut' => 'en_attente'
        ]);
    }
    
    /** @test */
    public function test_valider_paiement()
    {
        // Créer un salaire de test
        $salaire = Salaire::create([
            'professeur_id' => $this->professeur->id,
            'matiere_id' => $this->matiere->id,
            'mois_periode' => '2025-08',
            'nombre_eleves' => 3,
            'prix_unitaire' => 150.50,
            'commission_prof' => 30.0,
            'montant_brut' => 451.50,
            'montant_commission' => 135.45,
            'montant_net' => 316.05,
            'statut' => 'en_attente'
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->postJson("/api/salaires/{$salaire->id}/valider", [
            'date_paiement' => '2025-08-24',
            'commentaires' => 'Paiement effectué par virement bancaire'
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'salaire' => [
                    'id',
                    'statut',
                    'date_paiement',
                    'commentaires'
                ]
            ]);
            
        $response->assertJson([
            'success' => true,
            'message' => 'Paiement validé avec succès',
            'salaire' => [
                'statut' => 'paye',
                'date_paiement' => '2025-08-24 00:00:00',
                'commentaires' => 'Paiement effectué par virement bancaire'
            ]
        ]);
        
        // Vérifier que le statut a bien été mis à jour dans la base de données
        $this->assertDatabaseHas('salaires', [
            'id' => $salaire->id,
            'statut' => 'paye',
            'date_paiement' => '2025-08-24 00:00:00'
        ]);
    }
    
    /** @test */
    public function test_annuler_salaire()
    {
        // Créer un salaire de test
        $salaire = Salaire::create([
            'professeur_id' => $this->professeur->id,
            'matiere_id' => $this->matiere->id,
            'mois_periode' => '2025-08',
            'nombre_eleves' => 3,
            'prix_unitaire' => 150.50,
            'commission_prof' => 30.0,
            'montant_brut' => 451.50,
            'montant_commission' => 135.45,
            'montant_net' => 316.05,
            'statut' => 'en_attente'
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->postJson("/api/salaires/{$salaire->id}/annuler", [
            'raison' => 'Erreur dans le calcul du nombre d\'élèves'
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'salaire' => [
                    'id',
                    'statut',
                    'commentaires'
                ]
            ]);
            
        $response->assertJson([
            'success' => true,
            'message' => 'Salaire annulé avec succès',
            'salaire' => [
                'statut' => 'annule'
            ]
        ]);
        
        // Vérifier que le statut a bien été mis à jour dans la base de données
        $this->assertDatabaseHas('salaires', [
            'id' => $salaire->id,
            'statut' => 'annule'
        ]);
        
        // Vérifier que le commentaire contient la raison de l'annulation
        $updatedSalaire = Salaire::find($salaire->id);
        $this->assertStringContainsString("Erreur dans le calcul du nombre d'élèves", $updatedSalaire->commentaires);
    }
    
    /** @test */
    public function test_lister_configurations()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/salaires/configurations');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'matiere_id',
                            'prix_unitaire',
                            'commission_prof',
                            'prix_min',
                            'prix_max',
                            'est_actif',
                            'description',
                            'created_at',
                            'updated_at',
                            'matiere' => [
                                'id',
                                'nom',
                                'description',
                                'coefficient'
                            ]
                        ]
                    ],
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total'
                ]
            ]);
            
        $response->assertJson([
            'success' => true
        ]);
    }
    
    /** @test */
    public function test_mettre_a_jour_configuration()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->putJson("/api/salaires/configurations/{$this->configurationSalaire->id}", [
            'prix_unitaire' => 160.75,
            'commission_prof' => 35.0,
            'prix_min' => 110.00,
            'prix_max' => 220.00,
            'est_actif' => true,
            'description' => 'Configuration mise à jour pour les cours de mathématiques'
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'matiere_id',
                    'prix_unitaire',
                    'commission_prof',
                    'prix_min',
                    'prix_max',
                    'est_actif',
                    'description',
                    'created_at',
                    'updated_at',
                    'matiere' => [
                        'id',
                        'nom',
                        'description',
                        'coefficient'
                    ]
                ]
            ]);
            
        $response->assertJson([
            'success' => true,
            'message' => 'Configuration mise à jour avec succès',
            'data' => [
                'prix_unitaire' => 160.75,
                'commission_prof' => 35.0,
                'prix_min' => 110.00,
                'prix_max' => 220.00,
                'est_actif' => true,
                'description' => 'Configuration mise à jour pour les cours de mathématiques'
            ]
        ]);
        
        // Vérifier que la configuration a bien été mise à jour dans la base de données
        $this->assertDatabaseHas('configuration_salaires', [
            'id' => $this->configurationSalaire->id,
            'prix_unitaire' => 160.75,
            'commission_prof' => 35.0,
            'description' => 'Configuration mise à jour pour les cours de mathématiques'
        ]);
    }
}
