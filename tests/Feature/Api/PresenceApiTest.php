<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Etudiant;
use App\Models\Presence;
use App\Enums\RoleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class PresenceApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur professeur avec un token d'API
        $this->professeur = User::factory()->create([
            'role' => RoleType::PROFESSEUR,
            'email' => 'professeur@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // Créer un token d'API pour l'utilisateur
        $this->token = $this->professeur->createToken('test-token')->plainTextToken;
        
        // Créer une classe
        $this->classe = Classe::factory()->create([
            'nom' => 'GLSI-1',
            'niveau' => '1ère année',
            'professeur_principal_id' => $this->professeur->id,
        ]);
        
        // Créer une matière
        $this->matiere = Matiere::factory()->create([
            'code' => 'MATH-101',
            'nom' => 'Mathématiques',
        ]);
        
        // Lier le professeur à la matière et à la classe via un enseignement
        $this->matiere->professeurs()->attach($this->professeur, [
            'niveau_id' => 1,
            'filiere_id' => 1,
            'nombre_heures_semaine' => 4,
            'classe_id' => $this->classe->id,
        ]);
        
        // Créer des étudiants dans la classe
        $this->etudiants = Etudiant::factory(3)->create([
            'classe_id' => $this->classe->id,
        ]);
        
        // Créer une présence existante pour les tests
        $this->presence = Presence::create([
            'etudiant_id' => $this->etudiants[0]->id,
            'matiere_id' => $this->matiere->id,
            'classe_id' => $this->classe->id,
            'professeur_id' => $this->professeur->id,
            'date_seance' => now()->subDay()->format('Y-m-d'),
            'heure_debut' => '08:00:00',
            'heure_fin' => '10:00:00',
            'statut' => 'present',
            'remarques' => 'Très attentif',
        ]);
    }
    
    /** @test */
    public function it_can_list_presences()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->get('/api/presences');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id', 'etudiant_id', 'matiere_id', 'classe_id', 'professeur_id',
                    'date_seance', 'heure_debut', 'heure_fin', 'statut', 'remarques',
                    'created_at', 'updated_at',
                    'etudiant' => ['id', 'nom', 'prenom'],
                    'matiere' => ['id', 'nom'],
                    'classe' => ['id', 'nom'],
                ]
            ],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => [
                'current_page', 'from', 'last_page', 'links', 'path',
                'per_page', 'to', 'total'
            ]
        ]);
    }
    
    /** @test */
    public function it_can_show_a_presence()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->get("/api/presences/{$this->presence->id}");
        
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $this->presence->id,
                'etudiant_id' => $this->presence->etudiant_id,
                'matiere_id' => $this->presence->matiere_id,
                'classe_id' => $this->presence->classe_id,
                'professeur_id' => $this->presence->professeur_id,
                'statut' => $this->presence->statut,
                'remarques' => $this->presence->remarques,
            ]
        ]);
    }
    
    /** @test */
    public function it_can_create_a_presence()
    {
        $etudiant = $this->etudiants[1];
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->postJson('/api/presences', [
            'etudiant_id' => $etudiant->id,
            'matiere_id' => $this->matiere->id,
            'classe_id' => $this->classe->id,
            'date_seance' => now()->format('Y-m-d'),
            'heure_debut' => '10:00',
            'heure_fin' => '12:00',
            'statut' => 'present',
            'remarques' => 'Très bon travail',
        ]);
        
        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'etudiant_id' => $etudiant->id,
                'matiere_id' => $this->matiere->id,
                'classe_id' => $this->classe->id,
                'statut' => 'present',
                'remarques' => 'Très bon travail',
            ]
        ]);
        
        $this->assertDatabaseHas('presences', [
            'etudiant_id' => $etudiant->id,
            'matiere_id' => $this->matiere->id,
            'classe_id' => $this->classe->id,
            'statut' => 'present',
            'remarques' => 'Très bon travail',
        ]);
    }
    
    /** @test */
    public function it_can_update_a_presence()
    {
        $newRemarques = 'Remarque mise à jour via API';
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->putJson("/api/presences/{$this->presence->id}", [
            'etudiant_id' => $this->presence->etudiant_id,
            'matiere_id' => $this->presence->matiere_id,
            'classe_id' => $this->presence->classe_id,
            'date_seance' => $this->presence->date_seance,
            'heure_debut' => $this->presence->heure_debut,
            'heure_fin' => $this->presence->heure_fin,
            'statut' => 'retard',
            'remarques' => $newRemarques,
        ]);
        
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $this->presence->id,
                'statut' => 'retard',
                'remarques' => $newRemarques,
            ]
        ]);
        
        $this->assertDatabaseHas('presences', [
            'id' => $this->presence->id,
            'statut' => 'retard',
            'remarques' => $newRemarques,
        ]);
    }
    
    /** @test */
    public function it_can_delete_a_presence()
    {
        $presenceToDelete = Presence::create([
            'etudiant_id' => $this->etudiants[2]->id,
            'matiere_id' => $this->matiere->id,
            'classe_id' => $this->classe->id,
            'professeur_id' => $this->professeur->id,
            'date_seance' => now()->format('Y-m-d'),
            'heure_debut' => '14:00:00',
            'heure_fin' => '16:00:00',
            'statut' => 'present',
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->delete("/api/presences/{$presenceToDelete->id}");
        
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Présence supprimée avec succès.',
        ]);
        
        $this->assertDatabaseMissing('presences', ['id' => $presenceToDelete->id]);
    }
    
    /** @test */
    public function it_can_get_etudiants_for_class_and_subject()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->get("/api/classes/{$this->classe->id}/matieres/{$this->matiere->id}/etudiants");
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id', 'nom', 'prenom', 'email', 'photo',
                    'presence' => ['id', 'statut', 'remarques']
                ]
            ]
        ]);
    }
    
    /** @test */
    public function it_can_mark_multiple_presences()
    {
        $etudiantsData = [];
        
        foreach ($this->etudiants as $etudiant) {
            $etudiantsData[] = [
                'etudiant_id' => $etudiant->id,
                'statut' => 'present',
                'remarques' => 'Bon travail',
            ];
        }
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->postJson('/api/presences/marquer', [
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiere->id,
            'date_seance' => now()->format('Y-m-d'),
            'heure_debut' => '08:00',
            'heure_fin' => '10:00',
            'etudiants' => $etudiantsData,
        ]);
        
        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Présences enregistrées avec succès.',
        ]);
        
        foreach ($this->etudiants as $etudiant) {
            $this->assertDatabaseHas('presences', [
                'etudiant_id' => $etudiant->id,
                'matiere_id' => $this->matiere->id,
                'classe_id' => $this->classe->id,
                'statut' => 'present',
                'remarques' => 'Bon travail',
            ]);
        }
    }
    
    /** @test */
    public function it_can_get_statistics()
    {
        // Créer quelques présences pour les statistiques
        $dates = [
            now()->startOfMonth()->format('Y-m-d'),
            now()->subDays(5)->format('Y-m-d'),
            now()->format('Y-m-d'),
        ];
        
        foreach ($dates as $date) {
            Presence::create([
                'etudiant_id' => $this->etudiants[0]->id,
                'matiere_id' => $this->matiere->id,
                'classe_id' => $this->classe->id,
                'professeur_id' => $this->professeur->id,
                'date_seance' => $date,
                'heure_debut' => '08:00:00',
                'heure_fin' => '10:00:00',
                'statut' => 'present',
            ]);
        }
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->get('/api/statistiques/presences', [
            'mois' => now()->month,
            'annee' => now()->year,
        ]);
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'total_seances',
                'total_heures',
                'taux_presence',
                'presences_par_jour' => [],
                'presences_par_matiere' => [],
                'presences_par_etudiant' => [],
            ]
        ]);
    }
    
    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->postJson('/api/presences', []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'etudiant_id',
            'matiere_id',
            'classe_id',
            'date_seance',
            'heure_debut',
            'heure_fin',
            'statut',
        ]);
    }
    
    /** @test */
    public function it_requires_authentication()
    {
        // Tester sans token
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/presences');
        
        $response->assertStatus(401);
        
        // Tester avec un token invalide
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token',
            'Accept' => 'application/json',
        ])->get('/api/presences');
        
        $response->assertStatus(401);
    }
    
    /** @test */
    public function it_requires_professor_role()
    {
        // Créer un utilisateur étudiant
        $etudiant = User::factory()->create([
            'role' => RoleType::ELEVE,
            'email' => 'etudiant@example.com',
            'password' => bcrypt('password'),
        ]);
        
        $token = $etudiant->createToken('test-token')->plainTextToken;
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->get('/api/presences');
        
        $response->assertStatus(403);
    }
}
