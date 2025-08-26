<?php

namespace Tests\Unit\Models;

use App\Models\Classe;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Etudiant;
use App\Models\Enseignement;
use App\Models\Presence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClasseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_classe()
    {
        $classe = Classe::create([
            'code_classe' => 'GLSI-1',
            'nom' => 'Génie Logiciel et Systèmes d\'Information 1',
            'niveau' => '1ère année',
            'annee_scolaire' => '2023-2024',
            'filiere_id' => 1,
            'professeur_principal_id' => User::factory()->create(['role' => 1])->id,
            'effectif_max' => 40,
            'description' => 'Classe de première année en GLSI',
            'est_actif' => true,
        ]);

        $this->assertDatabaseHas('classes', [
            'code_classe' => 'GLSI-1',
            'nom' => 'Génie Logiciel et Systèmes d\'Information 1',
            'niveau' => '1ère année',
            'est_actif' => true,
        ]);
    }

    /** @test */
    public function it_has_filiere_relation()
    {
        $classe = Classe::factory()->create();
        
        $this->assertInstanceOf(\App\Models\Filiere::class, $classe->filiere);
    }

    /** @test */
    public function it_has_professeur_principal_relation()
    {
        $professeur = User::factory()->create(['role' => 1]);
        $classe = Classe::factory()->create(['professeur_principal_id' => $professeur->id]);
        
        $this->assertInstanceOf(User::class, $classe->professeurPrincipal);
        $this->assertEquals($professeur->id, $classe->professeurPrincipal->id);
    }

    /** @test */
    public function it_has_etudiants_relation()
    {
        $classe = Classe::factory()->create();
        $etudiant = Etudiant::factory()->create(['classe_id' => $classe->id]);
        
        $this->assertCount(1, $classe->etudiants);
        $this->assertInstanceOf(Etudiant::class, $classe->etudiants->first());
        $this->assertEquals($etudiant->id, $classe->etudiants->first()->id);
    }

    /** @test */
    public function it_has_matieres_relation()
    {
        $classe = Classe::factory()->create();
        $matiere = Matiere::factory()->create();
        $professeur = User::factory()->create(['role' => 1]);
        
        Enseignement::create([
            'matiere_id' => $matiere->id,
            'classe_id' => $classe->id,
            'professeur_id' => $professeur->id,
            'nombre_heures_semaine' => 4
        ]);
        
        $this->assertCount(1, $classe->matieres);
        $this->assertInstanceOf(Matiere::class, $classe->matieres->first());
        $this->assertEquals($matiere->id, $classe->matieres->first()->id);
    }

    /** @test */
    public function it_has_professeurs_relation()
    {
        $classe = Classe::factory()->create();
        $professeur = User::factory()->create(['role' => 1]);
        $matiere = Matiere::factory()->create();
        
        Enseignement::create([
            'matiere_id' => $matiere->id,
            'classe_id' => $classe->id,
            'professeur_id' => $professeur->id,
            'nombre_heures_semaine' => 4
        ]);
        
        $this->assertCount(1, $classe->professeurs);
        $this->assertInstanceOf(User::class, $classe->professeurs->first());
        $this->assertEquals($professeur->id, $classe->professeurs->first()->id);
    }

    /** @test */
    public function it_has_presences_relation()
    {
        $classe = Classe::factory()->create();
        $etudiant = Etudiant::factory()->create(['classe_id' => $classe->id]);
        $matiere = Matiere::factory()->create();
        $professeur = User::factory()->create(['role' => 1]);
        
        $presence = Presence::create([
            'etudiant_id' => $etudiant->id,
            'matiere_id' => $matiere->id,
            'classe_id' => $classe->id,
            'professeur_id' => $professeur->id,
            'date_seance' => now(),
            'heure_debut' => '08:00',
            'heure_fin' => '10:00',
            'statut' => 'present',
        ]);
        
        $this->assertCount(1, $classe->presences);
        $this->assertInstanceOf(Presence::class, $classe->presences->first());
        $this->assertEquals($presence->id, $classe->presences->first()->id);
    }

    /** @test */
    public function it_has_enseignements_relation()
    {
        $classe = Classe::factory()->create();
        $matiere = Matiere::factory()->create();
        $professeur = User::factory()->create(['role' => 1]);
        
        $enseignement = Enseignement::create([
            'matiere_id' => $matiere->id,
            'classe_id' => $classe->id,
            'professeur_id' => $professeur->id,
            'nombre_heures_semaine' => 4
        ]);
        
        $this->assertCount(1, $classe->enseignements);
        $this->assertInstanceOf(Enseignement::class, $classe->enseignements->first());
        $this->assertEquals($enseignement->id, $classe->enseignements->first()->id);
    }

    /** @test */
    public function it_gets_libelle_complet_attribute()
    {
        $filiere = \App\Models\Filiere::factory()->create(['nom' => 'Génie Logiciel']);
        $classe = Classe::factory()->create([
            'niveau' => '1ère année',
            'nom' => 'GLSI',
            'filiere_id' => $filiere->id
        ]);
        
        $this->assertEquals('1ère année - GLSI (Génie Logiciel)', $classe->libelle_complet);
    }

    /** @test */
    public function it_gets_effectif_actuel_attribute()
    {
        $classe = Classe::factory()->create();
        Etudiant::factory(3)->create(['classe_id' => $classe->id]);
        
        $this->assertEquals(3, $classe->effectif_actuel);
    }

    /** @test */
    public function it_checks_if_classe_is_complete()
    {
        $classe = Classe::factory()->create(['effectif_max' => 2]);
        
        // Classe non complète
        $this->assertFalse($classe->est_complete);
        
        // Ajouter des étudiants jusqu'à la capacité maximale
        Etudiant::factory(2)->create(['classe_id' => $classe->id]);
        
        // Rafraîchir le modèle pour obtenir les dernières données
        $classe->refresh();
        
        $this->assertTrue($classe->est_complete);
    }

    /** @test */
    public function it_scopes_active_classes()
    {
        $activeClass = Classe::factory()->create(['est_actif' => true]);
        $inactiveClass = Classe::factory()->create(['est_actif' => false]);
        
        $activeClasses = Classe::actives()->get();
        
        $this->assertTrue($activeClasses->contains($activeClass));
        $this->assertFalse($activeClasses->contains($inactiveClass));
    }

    /** @test */
    public function it_scopes_classes_by_academic_year()
    {
        $class2023 = Classe::factory()->create(['annee_scolaire' => '2023-2024']);
        $class2024 = Classe::factory()->create(['annee_scolaire' => '2024-2025']);
        
        $classes2024 = Classe::anneeScolaire('2024-2025')->get();
        
        $this->assertFalse($classes2024->contains($class2023));
        $this->assertTrue($classes2024->contains($class2024));
    }

    /** @test */
    public function it_scopes_classes_by_level()
    {
        $class1 = Classe::factory()->create(['niveau' => '1ère année']);
        $class2 = Classe::factory()->create(['niveau' => '2ème année']);
        
        $firstYearClasses = Classe::niveau('1ère année')->get();
        
        $this->assertTrue($firstYearClasses->contains($class1));
        $this->assertFalse($firstYearClasses->contains($class2));
    }

    /** @test */
    public function it_scopes_classes_by_filiere()
    {
        $filiere1 = \App\Models\Filiere::factory()->create();
        $filiere2 = \App\Models\Filiere::factory()->create();
        
        $class1 = Classe::factory()->create(['filiere_id' => $filiere1->id]);
        $class2 = Classe::factory()->create(['filiere_id' => $filiere2->id]);
        
        $filiere1Classes = Classe::filiere($filiere1->id)->get();
        
        $this->assertTrue($filiere1Classes->contains($class1));
        $this->assertFalse($filiere1Classes->contains($class2));
    }
}
