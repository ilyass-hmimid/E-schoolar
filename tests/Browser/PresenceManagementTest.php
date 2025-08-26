<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Etudiant;
use App\Models\Presence;
use App\Enums\RoleType;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PresenceManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $professeur;
    protected $classe;
    protected $matiere;
    protected $etudiants;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur professeur
        $this->professeur = User::factory()->create([
            'role' => RoleType::PROFESSEUR,
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'professeur@example.com',
            'password' => bcrypt('password'),
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
            'nombre_heures_semaine' => 4,
            'classe_id' => $this->classe->id,
        ]);
        
        // Créer des étudiants dans la classe
        $this->etudiants = Etudiant::factory(3)->create([
            'classe_id' => $this->classe->id,
        ]);
    }
    
    /** @test */
    public function test_professor_can_login_and_view_presences()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'professeur@example.com')
                    ->type('password', 'password')
                    ->press('Se connecter')
                    ->assertPathIs('/professeur/dashboard')
                    ->clickLink('Gestion des présences')
                    ->assertPathIs('/professeur/presences')
                    ->assertSee('Liste des présences');
        });
    }
    
    /** @test */
    public function test_professor_can_create_presence()
    {
        $etudiant = $this->etudiants->first();
        
        $this->browse(function (Browser $browser) use ($etudiant) {
            $browser->loginAs($this->professeur)
                    ->visit(route('professeur.presences.create'))
                    ->select('etudiant_id', $etudiant->id)
                    ->select('matiere_id', $this->matiere->id)
                    ->select('classe_id', $this->classe->id)
                    ->type('date_seance', now()->format('Y-m-d'))
                    ->type('heure_debut', '08:00')
                    ->type('heure_fin', '10:00')
                    ->select('statut', 'present')
                    ->type('remarques', 'Très attentif')
                    ->press('Enregistrer')
                    ->assertPathIs('/professeur/presences')
                    ->assertSee('Présence enregistrée avec succès.');
            
            $this->assertDatabaseHas('presences', [
                'etudiant_id' => $etudiant->id,
                'matiere_id' => $this->matiere->id,
                'classe_id' => $this->classe->id,
                'statut' => 'present',
                'remarques' => 'Très attentif',
            ]);
        });
    }
    
    /** @test */
    public function test_professor_can_mark_multiple_presences()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->professeur)
                    ->visit(route('professeur.presences.marquer.create'))
                    ->select('classe_id', $this->classe->id)
                    ->select('matiere_id', $this->matiere->id)
                    ->type('date_seance', now()->format('Y-m-d'))
                    ->type('heure_debut', '08:00')
                    ->type('heure_fin', '10:00');
            
            // Marquer chaque étudiant comme présent
            foreach ($this->etudiants as $etudiant) {
                $browser->select("etudiants[{$etudiant->id}][statut]", 'present')
                        ->type("etudiants[{$etudiant->id}][remarques]", 'Bon travail');
            }
            
            $browser->press('Enregistrer les présences')
                    ->assertPathIs('/professeur/presences')
                    ->assertSee('Présences enregistrées avec succès.');
            
            // Vérifier que les présences ont été enregistrées
            foreach ($this->etudiants as $etudiant) {
                $this->assertDatabaseHas('presences', [
                    'etudiant_id' => $etudiant->id,
                    'matiere_id' => $this->matiere->id,
                    'classe_id' => $this->classe->id,
                    'statut' => 'present',
                    'remarques' => 'Bon travail',
                ]);
            }
        });
    }
    
    /** @test */
    public function test_professor_can_edit_presence()
    {
        $presence = Presence::create([
            'etudiant_id' => $this->etudiants[0]->id,
            'matiere_id' => $this->matiere->id,
            'classe_id' => $this->classe->id,
            'professeur_id' => $this->professeur->id,
            'date_seance' => now()->format('Y-m-d'),
            'heure_debut' => '08:00:00',
            'heure_fin' => '10:00:00',
            'statut' => 'present',
            'remarques' => 'À mettre à jour',
        ]);
        
        $this->browse(function (Browser $browser) use ($presence) {
            $browser->loginAs($this->professeur)
                    ->visit(route('professeur.presences.edit', $presence->id))
                    ->select('statut', 'retard')
                    ->type('remarques', 'Étudiant en retard de 15 minutes')
                    ->press('Mettre à jour')
                    ->assertPathIs('/professeur/presences/' . $presence->id)
                    ->assertSee('Présence mise à jour avec succès.')
                    ->assertSee('retard')
                    ->assertSee('Étudiant en retard de 15 minutes');
            
            $this->assertDatabaseHas('presences', [
                'id' => $presence->id,
                'statut' => 'retard',
                'remarques' => 'Étudiant en retard de 15 minutes',
            ]);
        });
    }
    
    /** @test */
    public function test_professor_can_view_presence_details()
    {
        $presence = Presence::create([
            'etudiant_id' => $this->etudiants[0]->id,
            'matiere_id' => $this->matiere->id,
            'classe_id' => $this->classe->id,
            'professeur_id' => $this->professeur->id,
            'date_seance' => now()->format('Y-m-d'),
            'heure_debut' => '08:00:00',
            'heure_fin' => '10:00:00',
            'statut' => 'present',
            'remarques' => 'Détails de présence',
        ]);
        
        $this->browse(function (Browser $browser) use ($presence) {
            $browser->loginAs($this->professeur)
                    ->visit(route('professeur.presences.show', $presence->id))
                    ->assertSee('Détails de la présence')
                    ->assertSee($this->etudiants[0]->nom_complet)
                    ->assertSee($this->matiere->nom)
                    ->assertSee($this->classe->nom)
                    ->assertSee('present')
                    ->assertSee('Détails de présence');
        });
    }
    
    /** @test */
    public function test_professor_can_delete_presence()
    {
        $presence = Presence::create([
            'etudiant_id' => $this->etudiants[0]->id,
            'matiere_id' => $this->matiere->id,
            'classe_id' => $this->classe->id,
            'professeur_id' => $this->professeur->id,
            'date_seance' => now()->format('Y-m-d'),
            'heure_debut' => '08:00:00',
            'heure_fin' => '10:00:00',
            'statut' => 'present',
        ]);
        
        $this->browse(function (Browser $browser) use ($presence) {
            $browser->loginAs($this->professeur)
                    ->visit(route('professeur.presences.show', $presence->id))
                    ->press('Supprimer')
                    ->waitForDialog()
                    ->acceptDialog()
                    ->assertPathIs('/professeur/presences')
                    ->assertSee('Présence supprimée avec succès.');
            
            $this->assertDatabaseMissing('presences', ['id' => $presence->id]);
        });
    }
    
    /** @test */
    public function test_professor_can_view_statistics()
    {
        // Créer des présences pour les statistiques
        foreach ($this->etudiants as $etudiant) {
            Presence::create([
                'etudiant_id' => $etudiant->id,
                'matiere_id' => $this->matiere->id,
                'classe_id' => $this->classe->id,
                'professeur_id' => $this->professeur->id,
                'date_seance' => now()->format('Y-m-d'),
                'heure_debut' => '08:00:00',
                'heure_fin' => '10:00:00',
                'statut' => 'present',
            ]);
        }
        
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->professeur)
                    ->visit(route('professeur.presences.statistiques'))
                    ->assertSee('Statistiques des présences')
                    ->assertSee('Taux de présence')
                    ->assertSee('Répartition des présences')
                    ->assertSee('Présences par jour')
                    ->assertSee('Présences par matière');
        });
    }
    
    /** @test */
    public function test_validation_works_when_creating_presence()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->professeur)
                    ->visit(route('professeur.presences.create'))
                    ->press('Enregistrer')
                    ->assertSee('Le champ étudiant est obligatoire.')
                    ->assertSee('Le champ matière est obligatoire.')
                    ->assertSee('Le champ classe est obligatoire.')
                    ->assertSee('Le champ date de la séance est obligatoire.')
                    ->assertSee('Le champ heure de début est obligatoire.')
                    ->assertSee('Le champ heure de fin est obligatoire.')
                    ->assertSee('Le champ statut est obligatoire.');
        });
    }
    
    /** @test */
    public function test_unauthorized_user_cannot_access_presence_management()
    {
        // Créer un utilisateur étudiant
        $etudiant = User::factory()->create([
            'role' => RoleType::ELEVE,
            'email' => 'etudiant@example.com',
            'password' => bcrypt('password'),
        ]);
        
        $this->browse(function (Browser $browser) use ($etudiant) {
            $browser->loginAs($etudiant)
                    ->visit(route('professeur.presences.index'))
                    ->assertSee('403')
                    ->assertSee('Accès non autorisé');
                    
            $browser->visit(route('professeur.presences.create'))
                    ->assertSee('403')
                    ->assertSee('Accès non autorisé');
        });
    }
}
