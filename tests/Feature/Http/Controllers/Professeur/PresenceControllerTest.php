<?php

namespace Tests\Feature\Http\Controllers\Professeur;

use App\Models\User;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Etudiant;
use App\Models\Presence;
use App\Enums\RoleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia;
use Carbon\Carbon;

class PresenceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur professeur
        $this->professeur = User::factory()->create([
            'role' => RoleType::PROFESSEUR,
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@example.com',
        ]);
        
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
            'nombre_heures_semaine' => 4
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
        
        // Authentifier l'utilisateur pour les tests
        $this->actingAs($this->professeur);
    }
    
    /** @test */
    public function index_displays_presences_list()
    {
        $response = $this->get(route('professeur.presences.index'));
        
        $response->assertInertia(fn (AssertableInertia $page) => 
            $page->component('professeur/presences/Index')
                ->has('presences')
        );
        
        $response->assertStatus(200);
    }
    
    /** @test */
    public function create_displays_form()
    {
        $response = $this->get(route('professeur.presences.create'));
        
        $response->assertInertia(fn (AssertableInertia $page) => 
            $page->component('professeur/presences/Create')
                ->has('matieres')
                ->has('classes')
        );
        
        $response->assertStatus(200);
    }
    
    /** @test */
    public function store_creates_new_presence()
    {
        $etudiant = $this->etudiants[0];
        
        $response = $this->post(route('professeur.presences.store'), [
            'etudiant_id' => $etudiant->id,
            'matiere_id' => $this->matiere->id,
            'classe_id' => $this->classe->id,
            'date_seance' => now()->format('Y-m-d'),
            'heure_debut' => '08:00',
            'heure_fin' => '10:00',
            'statut' => 'present',
            'remarques' => 'Bon travail',
        ]);
        
        $this->assertDatabaseHas('presences', [
            'etudiant_id' => $etudiant->id,
            'matiere_id' => $this->matiere->id,
            'classe_id' => $this->classe->id,
            'statut' => 'present',
            'remarques' => 'Bon travail',
        ]);
        
        $response->assertRedirect(route('professeur.presences.index'));
        $response->assertSessionHas('success', 'Présence enregistrée avec succès.');
    }
    
    /** @test */
    public function show_displays_presence_details()
    {
        $response = $this->get(route('professeur.presences.show', $this->presence->id));
        
        $response->assertInertia(fn (AssertableInertia $page) => 
            $page->component('professeur/presences/Show')
                ->has('presence')
        );
        
        $response->assertStatus(200);
    }
    
    /** @test */
    public function edit_displays_edit_form()
    {
        $response = $this->get(route('professeur.presences.edit', $this->presence->id));
        
        $response->assertInertia(fn (AssertableInertia $page) => 
            $page->component('professeur/presences/Edit')
                ->has('presence')
                ->has('matieres')
                ->has('classes')
        );
        
        $response->assertStatus(200);
    }
    
    /** @test */
    public function update_updates_presence()
    {
        $newRemarques = 'Remarque mise à jour';
        
        $response = $this->put(route('professeur.presences.update', $this->presence->id), [
            'etudiant_id' => $this->presence->etudiant_id,
            'matiere_id' => $this->presence->matiere_id,
            'classe_id' => $this->presence->classe_id,
            'date_seance' => $this->presence->date_seance,
            'heure_debut' => $this->presence->heure_debut,
            'heure_fin' => $this->presence->heure_fin,
            'statut' => $this->presence->statut,
            'remarques' => $newRemarques,
        ]);
        
        $this->assertDatabaseHas('presences', [
            'id' => $this->presence->id,
            'remarques' => $newRemarques,
        ]);
        
        $response->assertRedirect(route('professeur.presences.show', $this->presence->id));
        $response->assertSessionHas('success', 'Présence mise à jour avec succès.');
    }
    
    /** @test */
    public function destroy_deletes_presence()
    {
        $presenceToDelete = Presence::create([
            'etudiant_id' => $this->etudiants[1]->id,
            'matiere_id' => $this->matiere->id,
            'classe_id' => $this->classe->id,
            'professeur_id' => $this->professeur->id,
            'date_seance' => now()->format('Y-m-d'),
            'heure_debut' => '10:00:00',
            'heure_fin' => '12:00:00',
            'statut' => 'present',
        ]);
        
        $response = $this->delete(route('professeur.presences.destroy', $presenceToDelete->id));
        
        $this->assertDatabaseMissing('presences', ['id' => $presenceToDelete->id]);
        $response->assertRedirect(route('professeur.presences.index'));
        $response->assertSessionHas('success', 'Présence supprimée avec succès.');
    }
    
    /** @test */
    public function get_etudiants_returns_students_for_class_and_subject()
    {
        $response = $this->get(route('professeur.presences.etudiants', [
            'classe' => $this->classe->id,
            'matiere' => $this->matiere->id,
            'date' => now()->format('Y-m-d'),
        ]));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id', 'nom_complet', 'photo',
                'presence' => ['id', 'statut', 'remarques']
            ]
        ]);
    }
    
    /** @test */
    public function marquer_presences_marks_multiple_presences()
    {
        $etudiantsData = [];
        
        foreach ($this->etudiants as $etudiant) {
            $etudiantsData[$etudiant->id] = [
                'statut' => 'present',
                'remarques' => 'Bon travail',
            ];
        }
        
        $response = $this->post(route('professeur.presences.marquer'), [
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiere->id,
            'date_seance' => now()->format('Y-m-d'),
            'heure_debut' => '08:00',
            'heure_fin' => '10:00',
            'etudiants' => $etudiantsData,
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
        
        $response->assertRedirect(route('professeur.presences.index'));
        $response->assertSessionHas('success', 'Présences enregistrées avec succès.');
    }
    
    /** @test */
    public function get_statistiques_returns_statistics()
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
        
        $response = $this->get(route('professeur.presences.statistiques', [
            'mois' => now()->month,
            'annee' => now()->year,
        ]));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total_seances',
            'total_heures',
            'taux_presence',
            'presences_par_jour' => [],
            'presences_par_matiere' => [],
            'presences_par_etudiant' => [],
        ]);
    }
    
    /** @test */
    public function unauthorized_user_cannot_access_protected_routes()
    {
        // Créer un utilisateur étudiant (non autorisé)
        $etudiantUser = User::factory()->create([
            'role' => RoleType::ELEVE,
        ]);
        
        $this->actingAs($etudiantUser);
        
        // Tester l'accès à différentes routes
        $routes = [
            route('professeur.presences.index'),
            route('professeur.presences.create'),
            route('professeur.presences.store'),
            route('professeur.presences.show', $this->presence->id),
            route('professeur.presences.edit', $this->presence->id),
            route('professeur.presences.update', $this->presence->id),
            route('professeur.presences.destroy', $this->presence->id),
            route('professeur.presences.etudiants', ['classe' => 1, 'matiere' => 1]),
            route('professeur.presences.marquer'),
            route('professeur.presences.statistiques'),
        ];
        
        foreach ($routes as $route) {
            $method = in_array($route, [route('professeur.presences.store'), route('professeur.presences.marquer')]) ? 'post' : 'get';
            $response = $this->$method($route);
            $response->assertStatus(403); // Forbidden
        }
    }
    
    /** @test */
    public function validation_fails_when_required_fields_are_missing()
    {
        // Test de la validation pour la création
        $response = $this->post(route('professeur.presences.store'), []);
        
        $response->assertSessionHasErrors([
            'etudiant_id',
            'matiere_id',
            'classe_id',
            'date_seance',
            'heure_debut',
            'heure_fin',
            'statut',
        ]);
        
        // Test de la validation pour la mise à jour
        $response = $this->put(route('professeur.presences.update', $this->presence->id), []);
        
        $response->assertSessionHasErrors([
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
    public function it_filters_presences_by_date_range()
    {
        // Créer des présences à différentes dates
        $presenceToday = Presence::factory()->create([
            'etudiant_id' => $this->etudiants[0]->id,
            'matiere_id' => $this->matiere->id,
            'classe_id' => $this->classe->id,
            'professeur_id' => $this->professeur->id,
            'date_seance' => now()->format('Y-m-d'),
            'heure_debut' => '08:00:00',
            'heure_fin' => '10:00:00',
            'statut' => 'present',
        ]);
        
        $presenceLastWeek = Presence::factory()->create([
            'etudiant_id' => $this->etudiants[1]->id,
            'matiere_id' => $this->matiere->id,
            'classe_id' => $this->classe->id,
            'professeur_id' => $this->professeur->id,
            'date_seance' => now()->subWeek()->format('Y-m-d'),
            'heure_debut' => '08:00:00',
            'heure_fin' => '10:00:00',
            'statut' => 'absent',
        ]);
        
        // Filtrer pour n'afficher que les présences d'aujourd'hui
        $response = $this->get(route('professeur.presences.index', [
            'date_debut' => now()->format('Y-m-d'),
            'date_fin' => now()->format('Y-m-d'),
        ]));
        
        $response->assertInertia(fn (AssertableInertia $page) => 
            $page->component('professeur/presences/Index')
                ->has('presences', 1)
                ->where('presences.0.id', $presenceToday->id)
        );
    }
}
