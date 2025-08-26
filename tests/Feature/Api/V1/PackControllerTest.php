<?php

namespace Tests\Feature\Api\V1;

use App\Models\Matiere;
use App\Models\Pack;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PackControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur admin pour les tests
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
        
        // Créer des matières de test
        $this->matieres = Matiere::factory()->count(3)->create();
    }

    /** @test */
    public function it_can_list_packs()
    {
        // Créer des packs de test
        $packs = Pack::factory()->count(5)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/packs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nom',
                        'type',
                        'prix',
                        'prix_formate',
                        'est_actif',
                        'est_populaire',
                        'created_at',
                        'links' => []
                    ]
                ],
                'meta' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                ],
                'links' => []
            ]);
    }

    /** @test */
    public function it_can_show_a_pack()
    {
        $pack = Pack::factory()->create();
        
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/packs/{$pack->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $pack->id,
                    'nom' => $pack->nom,
                    'type' => [
                        'code' => $pack->type,
                    ],
                    'prix' => (float) $pack->prix,
                    'est_actif' => (bool) $pack->est_actif,
                ]
            ]);
    }

    /** @test */
    public function it_can_create_a_pack()
    {
        $matieresData = $this->matieres->take(2)->map(function($matiere) {
            return [
                'id' => $matiere->id,
                'nombre_heures' => $this->faker->numberBetween(10, 40)
            ];
        })->toArray();

        $packData = [
            'nom' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'type' => 'cours',
            'prix' => $this->faker->randomFloat(2, 100, 1000),
            'prix_promo' => $this->faker->randomFloat(2, 50, 900),
            'duree_jours' => $this->faker->numberBetween(30, 365),
            'est_actif' => true,
            'est_populaire' => false,
            'matieres' => $matieresData
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/packs', $packData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'nom',
                    'type',
                    'prix',
                    'prix_promo',
                    'duree_jours',
                    'est_actif',
                    'est_populaire',
                    'matieres' => [
                        '*' => ['id', 'pivot']
                    ]
                ]
            ]);

        $this->assertDatabaseHas('packs', [
            'nom' => $packData['nom'],
            'type' => $packData['type'],
            'prix' => $packData['prix'],
            'est_actif' => $packData['est_actif'],
        ]);
    }

    /** @test */
    public function it_can_update_a_pack()
    {
        $pack = Pack::factory()->create();
        $matieres = $this->matieres->take(2);
        
        // Attacher des matières au pack
        $pack->matieres()->attach($matieres->pluck('id')->toArray(), [
            'nombre_heures_par_matiere' => 10
        ]);

        $updateData = [
            'nom' => 'Pack Mis à Jour',
            'prix' => 999.99,
            'est_actif' => false,
            'matieres' => $matieres->map(function($matiere) {
                return [
                    'id' => $matiere->id,
                    'nombre_heures' => 15
                ];
            })->toArray()
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/v1/packs/{$pack->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Pack mis à jour avec succès',
                'data' => [
                    'nom' => $updateData['nom'],
                    'prix' => $updateData['prix'],
                    'est_actif' => $updateData['est_actif'],
                ]
            ]);

        $this->assertDatabaseHas('packs', [
            'id' => $pack->id,
            'nom' => $updateData['nom'],
            'prix' => $updateData['prix'],
            'est_actif' => $updateData['est_actif'],
        ]);
    }

    /** @test */
    public function it_can_delete_a_pack()
    {
        $pack = Pack::factory()->create([
            'est_actif' => true
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/v1/packs/{$pack->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Pack supprimé avec succès'
            ]);

        $this->assertSoftDeleted('packs', [
            'id' => $pack->id
        ]);
    }

    /** @test */
    public function it_cannot_delete_a_pack_with_associated_ventes()
    {
        $pack = Pack::factory()->create();
        
        // Simuler une vente associée au pack
        $pack->ventes()->attach(1, [
            'prix' => 100,
            'quantite' => 1
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/v1/packs/{$pack->id}");

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Impossible de supprimer ce pack car il est associé à des ventes ou inscriptions'
            ]);

        $this->assertDatabaseHas('packs', [
            'id' => $pack->id,
            'deleted_at' => null
        ]);
    }

    /** @test */
    public function it_can_toggle_pack_status()
    {
        $pack = Pack::factory()->create([
            'est_actif' => false
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/packs/{$pack->id}/toggle-status");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Pack activé avec succès',
                'data' => [
                    'id' => $pack->id,
                    'est_actif' => true
                ]
            ]);

        $this->assertDatabaseHas('packs', [
            'id' => $pack->id,
            'est_actif' => true
        ]);
    }

    /** @test */
    public function it_can_toggle_pack_popularity()
    {
        $pack = Pack::factory()->create([
            'est_populaire' => false
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/packs/{$pack->id}/toggle-popularity");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Pack mis en avant avec succès',
                'data' => [
                    'id' => $pack->id,
                    'est_populaire' => true
                ]
            ]);

        $this->assertDatabaseHas('packs', [
            'id' => $pack->id,
            'est_populaire' => true
        ]);
    }

    /** @test */
    public function it_can_duplicate_a_pack()
    {
        $pack = Pack::factory()->create([
            'nom' => 'Pack Original',
            'est_actif' => true,
            'est_populaire' => true
        ]);

        $pack->matieres()->attach($this->matieres->first()->id, [
            'nombre_heures_par_matiere' => 20
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/packs/{$pack->id}/duplicate");

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Pack dupliqué avec succès',
                'data' => [
                    'nom' => 'Pack Original (Copie)',
                    'est_actif' => false,
                    'est_populaire' => false,
                ]
            ]);

        $this->assertDatabaseHas('packs', [
            'nom' => 'Pack Original (Copie)',
            'est_actif' => false,
            'est_populaire' => false,
        ]);
    }

    /** @test */
    public function it_can_get_pack_statistics()
    {
        // Créer des packs avec différents statuts
        Pack::factory()->count(3)->create(['est_actif' => true]);
        Pack::factory()->count(2)->create(['est_actif' => false]);
        Pack::factory()->create(['est_populaire' => true]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/packs/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total',
                    'active',
                    'average_price',
                    'total_sales',
                ]
            ])
            ->assertJson([
                'data' => [
                    'total' => 6,
                    'active' => 3,
                ]
            ]);
    }
}
