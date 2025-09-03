<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Classe;
use App\Models\Paiement;
use App\Models\Absence;
use App\Models\Enseignement;
use App\Enums\RoleType;
use App\Enums\StatutPaiement;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->admin = User::factory()->create([
            'role' => RoleType::ADMIN->value,
            'email_verified_at' => now(),
            'active' => true
        ]);
        
        // Create test students
        $this->students = User::factory()->count(5)->create([
            'role' => RoleType::ELEVE->value,
            'created_at' => now()->subDays(10)
        ]);
        
        // Create test teachers
        $this->teachers = User::factory()->count(3)->create([
            'role' => RoleType::PROFESSEUR->value,
            'disponible' => true
        ]);
        
        // Create test classes
        $this->classes = Classe::factory()->count(2)->create(['active' => true]);
        
        // Create test payments
        Paiement::factory()->create([
            'user_id' => $this->students[0]->id,
            'statut' => StatutPaiement::PAYE->value,
            'montant' => 1000,
            'date_paiement' => now(),
            'date_echeance' => now()->addMonth()
        ]);
        
        // Create test absences
        Absence::factory()->create([
            'user_id' => $this->students[0]->id,
            'date_absence' => now(),
            'justifiee' => false
        ]);
        
        // Create test classes
        Enseignement::factory()->create([
            'date_debut' => now()->addDay(),
            'date_fin' => now()->addDay()->addHours(2),
            'classe_id' => $this->classes[0]->id,
            'professeur_id' => $this->teachers[0]->id,
            'cours_id' => 1 // Assuming course with ID 1 exists
        ]);
    }
    
    /** @test */
    public function admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));
            
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard-new');
    }
    
    /** @test */
    public function dashboard_shows_correct_student_count()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));
            
        $response->assertSee('Élèves');
        $response->assertSee($this->students->count());
    }
    
    /** @test */
    public function dashboard_shows_correct_teacher_count()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));
            
        $response->assertSee('Professeurs');
        $response->assertSee($this->teachers->count());
    }
    
    /** @test */
    public function dashboard_shows_correct_class_count()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));
            
        $response->assertSee('Classes actives');
        $response->assertSee($this->classes->count());
    }
    
    /** @test */
    public function dashboard_shows_upcoming_classes()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));
            
        $response->assertSee('Prochains cours');
        $response->assertSee($this->teachers[0]->name);
    }
}
