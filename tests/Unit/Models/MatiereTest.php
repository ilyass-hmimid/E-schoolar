<?php

namespace Tests\Unit\Models;

use App\Models\Matiere;
use App\Models\User;
use App\Models\Classe;
use App\Models\Enseignement;
use App\Models\Presence;
use App\Models\Absence;
use App\Models\Note;
use App\Models\Paiement;
use App\Models\Pack;
use App\Models\Niveau;
use App\Models\Filiere;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatiereTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_matiere()
    {
        $matiere = Matiere::create([
            'code' => 'MATH-101',
            'nom' => 'Mathématiques Appliquées',
            'type' => 'scientifique',
            'description' => 'Cours de mathématiques appliquées',
            'prix' => 1200.50,
            'prix_prof' => 800.25,
            'est_actif' => true,
        ]);

        $this->assertDatabaseHas('matieres', [
            'code' => 'MATH-101',
            'nom' => 'Mathématiques Appliquées',
            'type' => 'scientifique',
            'est_actif' => true,
        ]);
    }

    /** @test */
    public function it_has_professeurs_relation()
    {
        $matiere = Matiere::factory()->create();
        $professeur = User::factory()->create(['role' => 1]);
        
        $matiere->professeurs()->attach($professeur, [
            'niveau_id' => 1,
            'filiere_id' => 1,
            'nombre_heures_semaine' => 4
        ]);
        
        $this->assertCount(1, $matiere->professeurs);
        $this->assertInstanceOf(User::class, $matiere->professeurs->first());
        $this->assertEquals($professeur->id, $matiere->professeurs->first()->id);
    }

    /** @test */
    public function it_has_niveaux_relation()
    {
        $matiere = Matiere::factory()->create();
        $niveau = Niveau::factory()->create();
        
        $matiere->niveaux()->attach($niveau);
        
        $this->assertCount(1, $matiere->niveaux);
        $this->assertInstanceOf(Niveau::class, $matiere->niveaux->first());
        $this->assertEquals($niveau->id, $matiere->niveaux->first()->id);
    }

    /** @test */
    public function it_has_filieres_relation()
    {
        $matiere = Matiere::factory()->create();
        $filiere = Filiere::factory()->create();
        
        $matiere->filieres()->attach($filiere);
        
        $this->assertCount(1, $matiere->filieres);
        $this->assertInstanceOf(Filiere::class, $matiere->filieres->first());
        $this->assertEquals($filiere->id, $matiere->filieres->first()->id);
    }

    /** @test */
    public function it_has_classes_relation()
    {
        $matiere = Matiere::factory()->create();
        $classe = Classe::factory()->create();
        $professeur = User::factory()->create(['role' => 1]);
        
        Enseignement::create([
            'matiere_id' => $matiere->id,
            'classe_id' => $classe->id,
            'professeur_id' => $professeur->id,
            'nombre_heures_semaine' => 4
        ]);
        
        $matiere->refresh();
        
        $this->assertCount(1, $matiere->classes);
        $this->assertInstanceOf(Classe::class, $matiere->classes->first());
        $this->assertEquals($classe->id, $matiere->classes->first()->id);
    }

    /** @test */
    public function it_has_enseignements_relation()
    {
        $matiere = Matiere::factory()->create();
        $classe = Classe::factory()->create();
        $professeur = User::factory()->create(['role' => 1]);
        
        $enseignement = Enseignement::create([
            'matiere_id' => $matiere->id,
            'classe_id' => $classe->id,
            'professeur_id' => $professeur->id,
            'nombre_heures_semaine' => 4
        ]);
        
        $this->assertCount(1, $matiere->enseignements);
        $this->assertInstanceOf(Enseignement::class, $matiere->enseignements->first());
        $this->assertEquals($enseignement->id, $matiere->enseignements->first()->id);
    }

    /** @test */
    public function it_has_absences_relation()
    {
        $matiere = Matiere::factory()->create();
        $etudiant = \App\Models\Etudiant::factory()->create();
        $professeur = User::factory()->create(['role' => 1]);
        
        $absence = Absence::create([
            'etudiant_id' => $etudiant->id,
            'matiere_id' => $matiere->id,
            'professeur_id' => $professeur->id,
            'date_absence' => now(),
            'justifiee' => false,
        ]);
        
        $this->assertCount(1, $matiere->absences);
        $this->assertInstanceOf(Absence::class, $matiere->absences->first());
        $this->assertEquals($absence->id, $matiere->absences->first()->id);
    }

    /** @test */
    public function it_has_presences_relation()
    {
        $matiere = Matiere::factory()->create();
        $etudiant = \App\Models\Etudiant::factory()->create();
        $classe = Classe::factory()->create();
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
        
        $this->assertCount(1, $matiere->presences);
        $this->assertInstanceOf(Presence::class, $matiere->presences->first());
        $this->assertEquals($presence->id, $matiere->presences->first()->id);
    }

    /** @test */
    public function it_has_notes_relation()
    {
        $matiere = Matiere::factory()->create();
        $etudiant = \App\Models\Etudiant::factory()->create();
        
        $note = Note::create([
            'etudiant_id' => $etudiant->id,
            'matiere_id' => $matiere->id,
            'valeur' => 16.5,
            'type' => 'examen',
            'date_evaluation' => now(),
        ]);
        
        $this->assertCount(1, $matiere->notes);
        $this->assertInstanceOf(Note::class, $matiere->notes->first());
        $this->assertEquals($note->id, $matiere->notes->first()->id);
    }

    /** @test */
    public function it_has_paiements_relation()
    {
        $matiere = Matiere::factory()->create();
        $etudiant = \App\Models\Etudiant::factory()->create();
        
        $paiement = Paiement::create([
            'etudiant_id' => $etudiant->id,
            'matiere_id' => $matiere->id,
            'montant' => 1200.50,
            'date_paiement' => now(),
            'mode_paiement' => 'virement',
            'reference' => 'PAY' . time(),
            'statut' => 'validé',
        ]);
        
        $this->assertCount(1, $matiere->paiements);
        $this->assertInstanceOf(Paiement::class, $matiere->paiements->first());
        $this->assertEquals($paiement->id, $matiere->paiements->first()->id);
    }

    /** @test */
    public function it_has_packs_relation()
    {
        $matiere = Matiere::factory()->create();
        $pack = Pack::factory()->create();
        
        $matiere->packs()->attach($pack, [
            'nombre_heures_par_matiere' => 20
        ]);
        
        $this->assertCount(1, $matiere->packs);
        $this->assertInstanceOf(Pack::class, $matiere->packs->first());
        $this->assertEquals($pack->id, $matiere->packs->first()->id);
    }

    /** @test */
    public function it_gets_prix_formate_attribute()
    {
        $matiere = Matiere::factory()->create(['prix' => 1250.75]);
        
        $this->assertEquals('1 250,75 DH', $matiere->prix_formate);
    }

    /** @test */
    public function it_gets_prix_prof_formate_attribute()
    {
        $matiere = Matiere::factory()->create(['prix_prof' => 850.50]);
        
        $this->assertEquals('850,50 DH', $matiere->prix_prof_formate);
    }

    /** @test */
    public function it_gets_statut_libelle_attribute()
    {
        $active = Matiere::factory()->create(['est_actif' => true]);
        $inactive = Matiere::factory()->create(['est_actif' => false]);
        
        $this->assertEquals('Actif', $active->statut_libelle);
        $this->assertEquals('Inactif', $inactive->statut_libelle);
    }

    /** @test */
    public function it_gets_nombre_eleves_attribute()
    {
        $matiere = Matiere::factory()->create();
        $etudiants = \App\Models\Etudiant::factory(3)->create();
        
        foreach ($etudiants as $etudiant) {
            Inscription::create([
                'etudiant_id' => $etudiant->id,
                'matiere_id' => $matiere->id,
                'annee_scolaire' => '2023-2024',
            ]);
        }
        
        $this->assertEquals(3, $matiere->nombre_eleves);
    }

    /** @test */
    public function it_scopes_active_matieres()
    {
        $active = Matiere::factory()->create(['est_actif' => true]);
        $inactive = Matiere::factory()->create(['est_actif' => false]);
        
        $actives = Matiere::actifs()->get();
        
        $this->assertTrue($actives->contains($active));
        $this->assertFalse($actives->contains($inactive));
    }

    /** @test */
    public function it_scopes_matieres_by_niveau()
    {
        $niveau1 = Niveau::factory()->create();
        $niveau2 = Niveau::factory()->create();
        
        $matiere1 = Matiere::factory()->create();
        $matiere2 = Matiere::factory()->create();
        
        $matiere1->niveaux()->attach($niveau1);
        $matiere2->niveaux()->attach($niveau2);
        
        $niveau1Matieres = Matiere::pourNiveau($niveau1->id)->get();
        
        $this->assertTrue($niveau1Matieres->contains($matiere1));
        $this->assertFalse($niveau1Matieres->contains($matiere2));
    }
}
