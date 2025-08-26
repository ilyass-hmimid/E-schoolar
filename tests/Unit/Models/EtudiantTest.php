<?php

namespace Tests\Unit\Models;

use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Presence;
use App\Models\Absence;
use App\Models\Note;
use App\Models\Paiement;
use App\Models\Inscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EtudiantTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_etudiant()
    {
        $classe = Classe::factory()->create();
        
        $etudiant = Etudiant::create([
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue de Paris',
            'date_naissance' => '2000-01-01',
            'lieu_naissance' => 'Paris',
            'classe_id' => $classe->id,
            'filiere_id' => 1,
            'niveau_id' => 1,
            'date_inscription' => now(),
            'numero_etudiant' => 'ETU' . str_pad(1, 5, '0', STR_PAD_LEFT),
            'est_actif' => true,
        ]);

        $this->assertDatabaseHas('etudiants', [
            'email' => 'jean.dupont@example.com',
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'est_actif' => true,
        ]);
    }

    /** @test */
    public function it_has_classe_relation()
    {
        $classe = Classe::factory()->create();
        $etudiant = Etudiant::factory()->create(['classe_id' => $classe->id]);
        
        $this->assertInstanceOf(Classe::class, $etudiant->classe);
        $this->assertEquals($classe->id, $etudiant->classe->id);
    }

    /** @test */
    public function it_has_filiere_relation()
    {
        $filiere = \App\Models\Filiere::factory()->create();
        $etudiant = Etudiant::factory()->create(['filiere_id' => $filiere->id]);
        
        $this->assertInstanceOf(\App\Models\Filiere::class, $etudiant->filiere);
        $this->assertEquals($filiere->id, $etudiant->filiere->id);
    }

    /** @test */
    public function it_has_niveau_relation()
    {
        $niveau = \App\Models\Niveau::factory()->create();
        $etudiant = Etudiant::factory()->create(['niveau_id' => $niveau->id]);
        
        $this->assertInstanceOf(\App\Models\Niveau::class, $etudiant->niveau);
        $this->assertEquals($niveau->id, $etudiant->niveau->id);
    }

    /** @test */
    public function it_has_matieres_relation()
    {
        $etudiant = Etudiant::factory()->create();
        $matiere = Matiere::factory()->create();
        
        // Créer une inscription pour lier l'étudiant à la matière
        Inscription::create([
            'etudiant_id' => $etudiant->id,
            'matiere_id' => $matiere->id,
            'annee_scolaire' => '2023-2024',
            'note' => 15.5,
            'appreciation' => 'Très bon élève'
        ]);
        
        $this->assertCount(1, $etudiant->matieres);
        $this->assertInstanceOf(Matiere::class, $etudiant->matieres->first());
        $this->assertEquals($matiere->id, $etudiant->matieres->first()->id);
    }

    /** @test */
    public function it_has_presences_relation()
    {
        $etudiant = Etudiant::factory()->create();
        $classe = Classe::factory()->create();
        $matiere = Matiere::factory()->create();
        $professeur = \App\Models\User::factory()->create(['role' => 1]);
        
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
        
        $this->assertCount(1, $etudiant->presences);
        $this->assertInstanceOf(Presence::class, $etudiant->presences->first());
        $this->assertEquals($presence->id, $etudiant->presences->first()->id);
    }

    /** @test */
    public function it_has_absences_relation()
    {
        $etudiant = Etudiant::factory()->create();
        $matiere = Matiere::factory()->create();
        $professeur = \App\Models\User::factory()->create(['role' => 1]);
        
        $absence = Absence::create([
            'etudiant_id' => $etudiant->id,
            'matiere_id' => $matiere->id,
            'professeur_id' => $professeur->id,
            'date_absence' => now(),
            'justifiee' => false,
            'justificatif' => null,
        ]);
        
        $this->assertCount(1, $etudiant->absences);
        $this->assertInstanceOf(Absence::class, $etudiant->absences->first());
        $this->assertEquals($absence->id, $etudiant->absences->first()->id);
    }

    /** @test */
    public function it_has_notes_relation()
    {
        $etudiant = Etudiant::factory()->create();
        $matiere = Matiere::factory()->create();
        
        $note = Note::create([
            'etudiant_id' => $etudiant->id,
            'matiere_id' => $matiere->id,
            'valeur' => 15.5,
            'type' => 'devoir',
            'date_evaluation' => now(),
            'appreciation' => 'Très bon travail',
        ]);
        
        $this->assertCount(1, $etudiant->notes);
        $this->assertInstanceOf(Note::class, $etudiant->notes->first());
        $this->assertEquals($note->id, $etudiant->notes->first()->id);
    }

    /** @test */
    public function it_has_paiements_relation()
    {
        $etudiant = Etudiant::factory()->create();
        
        $paiement = Paiement::create([
            'etudiant_id' => $etudiant->id,
            'montant' => 1500.00,
            'date_paiement' => now(),
            'mode_paiement' => 'virement',
            'reference' => 'PAY' . time(),
            'statut' => 'validé',
        ]);
        
        $this->assertCount(1, $etudiant->paiements);
        $this->assertInstanceOf(Paiement::class, $etudiant->paiements->first());
        $this->assertEquals($paiement->id, $etudiant->paiements->first()->id);
    }

    /** @test */
    public function it_has_inscriptions_relation()
    {
        $etudiant = Etudiant::factory()->create();
        $matiere = Matiere::factory()->create();
        
        $inscription = Inscription::create([
            'etudiant_id' => $etudiant->id,
            'matiere_id' => $matiere->id,
            'annee_scolaire' => '2023-2024',
            'note' => 14.5,
            'appreciation' => 'Bon travail',
        ]);
        
        $this->assertCount(1, $etudiant->inscriptions);
        $this->assertInstanceOf(Inscription::class, $etudiant->inscriptions->first());
        $this->assertEquals($inscription->id, $etudiant->inscriptions->first()->id);
    }

    /** @test */
    public function it_gets_nom_complet_attribute()
    {
        $etudiant = Etudiant::factory()->create([
            'nom' => 'Dupont',
            'prenom' => 'Jean'
        ]);
        
        $this->assertEquals('Jean Dupont', $etudiant->nom_complet);
    }

    /** @test */
    public function it_checks_if_etudiant_is_actif()
    {
        $actif = Etudiant::factory()->create(['est_actif' => true]);
        $inactif = Etudiant::factory()->create(['est_actif' => false]);
        
        $this->assertTrue($actif->estActif());
        $this->assertFalse($inactif->estActif());
    }

    /** @test */
    public function it_scopes_active_etudiants()
    {
        $actif = Etudiant::factory()->create(['est_actif' => true]);
        $inactif = Etudiant::factory()->create(['est_actif' => false]);
        
        $actifs = Etudiant::actifs()->get();
        
        $this->assertTrue($actifs->contains($actif));
        $this->assertFalse($actifs->contains($inactif));
    }

    /** @test */
    public function it_scopes_etudiants_by_classe()
    {
        $classe1 = Classe::factory()->create();
        $classe2 = Classe::factory()->create();
        
        $etudiant1 = Etudiant::factory()->create(['classe_id' => $classe1->id]);
        $etudiant2 = Etudiant::factory()->create(['classe_id' => $classe2->id]);
        
        $classe1Etudiants = Etudiant::deLaClasse($classe1->id)->get();
        
        $this->assertTrue($classe1Etudiants->contains($etudiant1));
        $this->assertFalse($classe1Etudiants->contains($etudiant2));
    }

    /** @test */
    public function it_scopes_etudiants_by_filiere()
    {
        $filiere1 = \App\Models\Filiere::factory()->create();
        $filiere2 = \App\Models\Filiere::factory()->create();
        
        $etudiant1 = Etudiant::factory()->create(['filiere_id' => $filiere1->id]);
        $etudiant2 = Etudiant::factory()->create(['filiere_id' => $filiere2->id]);
        
        $filiere1Etudiants = Etudiant::deLaFiliere($filiere1->id)->get();
        
        $this->assertTrue($filiere1Etudiants->contains($etudiant1));
        $this->assertFalse($filiere1Etudiants->contains($etudiant2));
    }

    /** @test */
    public function it_scopes_etudiants_by_niveau()
    {
        $niveau1 = \App\Models\Niveau::factory()->create();
        $niveau2 = \App\Models\Niveau::factory()->create();
        
        $etudiant1 = Etudiant::factory()->create(['niveau_id' => $niveau1->id]);
        $etudiant2 = Etudiant::factory()->create(['niveau_id' => $niveau2->id]);
        
        $niveau1Etudiants = Etudiant::duNiveau($niveau1->id)->get();
        
        $this->assertTrue($niveau1Etudiants->contains($etudiant1));
        $this->assertFalse($niveau1Etudiants->contains($etudiant2));
    }
}
