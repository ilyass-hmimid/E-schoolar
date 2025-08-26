<?php

namespace Tests\Unit\Models;

use App\Models\Presence;
use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresenceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_presence()
    {
        $etudiant = Etudiant::factory()->create();
        $matiere = Matiere::factory()->create();
        $classe = Classe::factory()->create();
        $professeur = User::factory()->create(['role' => 1]);
        
        $presence = Presence::create([
            'etudiant_id' => $etudiant->id,
            'matiere_id' => $matiere->id,
            'classe_id' => $classe->id,
            'professeur_id' => $professeur->id,
            'date_seance' => '2023-05-15',
            'heure_debut' => '08:00:00',
            'heure_fin' => '10:00:00',
            'statut' => 'present',
            'remarques' => 'Très attentif',
        ]);

        $this->assertDatabaseHas('presences', [
            'etudiant_id' => $etudiant->id,
            'matiere_id' => $matiere->id,
            'classe_id' => $classe->id,
            'professeur_id' => $professeur->id,
            'date_seance' => '2023-05-15',
            'statut' => 'present',
            'remarques' => 'Très attentif',
        ]);
    }

    /** @test */
    public function it_has_etudiant_relation()
    {
        $etudiant = Etudiant::factory()->create();
        $presence = Presence::factory()->create(['etudiant_id' => $etudiant->id]);
        
        $this->assertInstanceOf(Etudiant::class, $presence->etudiant);
        $this->assertEquals($etudiant->id, $presence->etudiant->id);
    }

    /** @test */
    public function it_has_matiere_relation()
    {
        $matiere = Matiere::factory()->create();
        $presence = Presence::factory()->create(['matiere_id' => $matiere->id]);
        
        $this->assertInstanceOf(Matiere::class, $presence->matiere);
        $this->assertEquals($matiere->id, $presence->matiere->id);
    }

    /** @test */
    public function it_has_classe_relation()
    {
        $classe = Classe::factory()->create();
        $presence = Presence::factory()->create(['classe_id' => $classe->id]);
        
        $this->assertInstanceOf(Classe::class, $presence->classe);
        $this->assertEquals($classe->id, $presence->classe->id);
    }

    /** @test */
    public function it_has_professeur_relation()
    {
        $professeur = User::factory()->create(['role' => 1]);
        $presence = Presence::factory()->create(['professeur_id' => $professeur->id]);
        
        $this->assertInstanceOf(User::class, $presence->professeur);
        $this->assertEquals($professeur->id, $presence->professeur->id);
    }

    /** @test */
    public function it_gets_date_seance_formatted_attribute()
    {
        $presence = Presence::factory()->create(['date_seance' => '2023-05-15']);
        
        $this->assertEquals('15/05/2023', $presence->date_seance_formatted);
    }

    /** @test */
    public function it_gets_heure_debut_formatted_attribute()
    {
        $presence = Presence::factory()->create(['heure_debut' => '08:30:00']);
        
        $this->assertEquals('08:30', $presence->heure_debut_formatted);
    }

    /** @test */
    public function it_gets_heure_fin_formatted_attribute()
    {
        $presence = Presence::factory()->create(['heure_fin' => '10:15:00']);
        
        $this->assertEquals('10:15', $presence->heure_fin_formatted);
    }

    /** @test */
    public function it_gets_duree_attribute()
    {
        $presence = Presence::factory()->create([
            'heure_debut' => '08:00:00',
            'heure_fin' => '10:30:00'
        ]);
        
        $this->assertEquals(2.5, $presence->duree);
    }

    /** @test */
    public function it_gets_duree_formatted_attribute()
    {
        $presence = Presence::factory()->create([
            'heure_debut' => '08:00:00',
            'heure_fin' => '10:30:00'
        ]);
        
        $this->assertEquals('2h 30m', $presence->duree_formatted);
    }

    /** @test */
    public function it_gets_statut_libelle_attribute()
    {
        $present = Presence::factory()->create(['statut' => 'present']);
        $absent = Presence::factory()->create(['statut' => 'absent']);
        $retard = Presence::factory()->create(['statut' => 'retard']);
        $justifie = Presence::factory()->create(['statut' => 'justifie']);
        
        $this->assertEquals('Présent', $present->statut_libelle);
        $this->assertEquals('Absent', $absent->statut_libelle);
        $this->assertEquals('Retard', $retard->statut_libelle);
        $this->assertEquals('Absence justifiée', $justifie->statut_libelle);
    }

    /** @test */
    public function it_gets_is_present_attribute()
    {
        $present = Presence::factory()->create(['statut' => 'present']);
        $absent = Presence::factory()->create(['statut' => 'absent']);
        
        $this->assertTrue($present->is_present);
        $this->assertFalse($absent->is_present);
    }

    /** @test */
    public function it_gets_is_absent_attribute()
    {
        $present = Presence::factory()->create(['statut' => 'present']);
        $absent = Presence::factory()->create(['statut' => 'absent']);
        
        $this->assertFalse($present->is_absent);
        $this->assertTrue($absent->is_absent);
    }

    /** @test */
    public function it_gets_is_retard_attribute()
    {
        $retard = Presence::factory()->create(['statut' => 'retard']);
        $present = Presence::factory()->create(['statut' => 'present']);
        
        $this->assertTrue($retard->is_retard);
        $this->assertFalse($present->is_retard);
    }

    /** @test */
    public function it_gets_is_justifie_attribute()
    {
        $justifie = Presence::factory()->create(['statut' => 'justifie']);
        $present = Presence::factory()->create(['statut' => 'present']);
        
        $this->assertTrue($justifie->is_justifie);
        $this->assertFalse($present->is_justifie);
    }

    /** @test */
    public function it_scopes_presents()
    {
        $present = Presence::factory()->create(['statut' => 'present']);
        $absent = Presence::factory()->create(['statut' => 'absent']);
        
        $presents = Presence::presents()->get();
        
        $this->assertTrue($presents->contains($present));
        $this->assertFalse($presents->contains($absent));
    }

    /** @test */
    public function it_scopes_absents()
    {
        $present = Presence::factory()->create(['statut' => 'present']);
        $absent = Presence::factory()->create(['statut' => 'absent']);
        
        $absents = Presence::absents()->get();
        
        $this->assertTrue($absents->contains($absent));
        $this->assertFalse($absents->contains($present));
    }

    /** @test */
    public function it_scopes_retards()
    {
        $retard = Presence::factory()->create(['statut' => 'retard']);
        $present = Presence::factory()->create(['statut' => 'present']);
        
        $retards = Presence::retards()->get();
        
        $this->assertTrue($retards->contains($retard));
        $this->assertFalse($retards->contains($present));
    }

    /** @test */
    public function it_scopes_justifies()
    {
        $justifie = Presence::factory()->create(['statut' => 'justifie']);
        $present = Presence::factory()->create(['statut' => 'present']);
        
        $justifies = Presence::justifies()->get();
        
        $this->assertTrue($justifies->contains($justifie));
        $this->assertFalse($justifies->contains($present));
    }

    /** @test */
    public function it_scopes_by_date()
    {
        $today = Presence::factory()->create(['date_seance' => now()->format('Y-m-d')]);
        $yesterday = Presence::factory()->create(['date_seance' => now()->subDay()->format('Y-m-d')]);
        
        $todayPresences = Presence::date(now()->format('Y-m-d'))->get();
        
        $this->assertTrue($todayPresences->contains($today));
        $this->assertFalse($todayPresences->contains($yesterday));
    }

    /** @test */
    public function it_scopes_by_month()
    {
        $thisMonth = Presence::factory()->create(['date_seance' => now()->format('Y-m-d')]);
        $lastMonth = Presence::factory()->create(['date_seance' => now()->subMonth()->format('Y-m-d')]);
        
        $currentMonthPresences = Presence::mois(now()->month)->get();
        
        $this->assertTrue($currentMonthPresences->contains($thisMonth));
        $this->assertFalse($currentMonthPresences->contains($lastMonth));
    }

    /** @test */
    public function it_scopes_by_year()
    {
        $thisYear = Presence::factory()->create(['date_seance' => now()->format('Y-m-d')]);
        $lastYear = Presence::factory()->create(['date_seance' => now()->subYear()->format('Y-m-d')]);
        
        $currentYearPresences = Presence::annee(now()->year)->get();
        
        $this->assertTrue($currentYearPresences->contains($thisYear));
        $this->assertFalse($currentYearPresences->contains($lastYear));
    }

    /** @test */
    public function it_scopes_by_etudiant()
    {
        $etudiant1 = Etudiant::factory()->create();
        $etudiant2 = Etudiant::factory()->create();
        
        $presence1 = Presence::factory()->create(['etudiant_id' => $etudiant1->id]);
        $presence2 = Presence::factory()->create(['etudiant_id' => $etudiant2->id]);
        
        $etudiant1Presences = Presence::etudiant($etudiant1->id)->get();
        
        $this->assertTrue($etudiant1Presences->contains($presence1));
        $this->assertFalse($etudiant1Presences->contains($presence2));
    }

    /** @test */
    public function it_scopes_by_matiere()
    {
        $matiere1 = Matiere::factory()->create();
        $matiere2 = Matiere::factory()->create();
        
        $presence1 = Presence::factory()->create(['matiere_id' => $matiere1->id]);
        $presence2 = Presence::factory()->create(['matiere_id' => $matiere2->id]);
        
        $matiere1Presences = Presence::matiere($matiere1->id)->get();
        
        $this->assertTrue($matiere1Presences->contains($presence1));
        $this->assertFalse($matiere1Presences->contains($presence2));
    }

    /** @test */
    public function it_scopes_by_classe()
    {
        $classe1 = Classe::factory()->create();
        $classe2 = Classe::factory()->create();
        
        $presence1 = Presence::factory()->create(['classe_id' => $classe1->id]);
        $presence2 = Presence::factory()->create(['classe_id' => $classe2->id]);
        
        $classe1Presences = Presence::classe($classe1->id)->get();
        
        $this->assertTrue($classe1Presences->contains($presence1));
        $this->assertFalse($classe1Presences->contains($presence2));
    }

    /** @test */
    public function it_scopes_by_professeur()
    {
        $professeur1 = User::factory()->create(['role' => 1]);
        $professeur2 = User::factory()->create(['role' => 1]);
        
        $presence1 = Presence::factory()->create(['professeur_id' => $professeur1->id]);
        $presence2 = Presence::factory()->create(['professeur_id' => $professeur2->id]);
        
        $professeur1Presences = Presence::professeur($professeur1->id)->get();
        
        $this->assertTrue($professeur1Presences->contains($presence1));
        $this->assertFalse($professeur1Presences->contains($presence2));
    }
}
