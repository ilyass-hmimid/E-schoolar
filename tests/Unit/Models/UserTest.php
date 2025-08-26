<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Enseignement;
use App\Enums\RoleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => RoleType::PROFESSEUR,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'role' => RoleType::PROFESSEUR->value,
        ]);
    }

    /** @test */
    public function it_has_role_as_enum()
    {
        $user = User::factory()->create(['role' => RoleType::PROFESSEUR]);
        
        $this->assertInstanceOf(RoleType::class, $user->role);
        $this->assertEquals(RoleType::PROFESSEUR, $user->role);
    }

    /** @test */
    public function it_can_have_matieres()
    {
        $user = User::factory()->create(['role' => RoleType::PROFESSEUR]);
        $matiere = Matiere::factory()->create();
        
        $user->matieresEnseignees()->attach($matiere, [
            'niveau_id' => 1,
            'filiere_id' => 1,
            'nombre_heures_semaine' => 4
        ]);
        
        $this->assertCount(1, $user->matieresEnseignees);
        $this->assertEquals($matiere->id, $user->matieresEnseignees->first()->id);
    }

    /** @test */
    public function it_can_have_classes()
    {
        $user = User::factory()->create(['role' => RoleType::PROFESSEUR]);
        $classe = Classe::factory()->create();
        $matiere = Matiere::factory()->create();
        
        Enseignement::create([
            'professeur_id' => $user->id,
            'matiere_id' => $matiere->id,
            'classe_id' => $classe->id,
            'nombre_heures_semaine' => 4
        ]);
        
        $this->assertCount(1, $user->classes);
        $this->assertEquals($classe->id, $user->classes->first()->id);
    }

    /** @test */
    public function it_can_check_if_user_is_professeur()
    {
        $professeur = User::factory()->create(['role' => RoleType::PROFESSEUR]);
        $admin = User::factory()->create(['role' => RoleType::ADMIN]);
        
        $this->assertTrue($professeur->isProfesseur());
        $this->assertFalse($admin->isProfesseur());
    }

    /** @test */
    public function it_can_check_if_user_is_admin()
    {
        $admin = User::factory()->create(['role' => RoleType::ADMIN]);
        $professeur = User::factory()->create(['role' => RoleType::PROFESSEUR]);
        
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($professeur->isAdmin());
    }

    /** @test */
    public function it_can_check_if_user_is_assistant()
    {
        $assistant = User::factory()->create(['role' => RoleType::ASSISTANT]);
        $professeur = User::factory()->create(['role' => RoleType::PROFESSEUR]);
        
        $this->assertTrue($assistant->isAssistant());
        $this->assertFalse($professeur->isAssistant());
    }

    /** @test */
    public function it_can_check_if_user_is_eleve()
    {
        $eleve = User::factory()->create(['role' => RoleType::ELEVE]);
        $professeur = User::factory()->create(['role' => RoleType::PROFESSEUR]);
        
        $this->assertTrue($eleve->isEleve());
        $this->assertFalse($professeur->isEleve());
    }

    /** @test */
    public function it_can_check_if_user_has_permission()
    {
        $admin = User::factory()->create(['role' => RoleType::ADMIN]);
        $professeur = User::factory()->create(['role' => RoleType::PROFESSEUR]);
        
        $this->assertTrue($admin->hasPermission('manage_users'));
        $this->assertFalse($professeur->hasPermission('manage_users'));
    }

    /** @test */
    public function it_can_calculate_salaire_for_professeur()
    {
        $professeur = User::factory()->create(['role' => RoleType::PROFESSEUR]);
        $matiere = Matiere::factory()->create(['prix_prof' => 150]);
        
        // Créer des enseignements pour le professeur
        Enseignement::create([
            'professeur_id' => $professeur->id,
            'matiere_id' => $matiere->id,
            'classe_id' => Classe::factory()->create()->id,
            'nombre_heures_semaine' => 4
        ]);
        
        $mois = now()->format('Y-m');
        $salaire = $professeur->calculerSalaire($mois);
        
        // 4 heures/semaine * 4 semaines * 150 DH = 2400 DH
        $this->assertEquals(2400, $salaire);
    }
}
