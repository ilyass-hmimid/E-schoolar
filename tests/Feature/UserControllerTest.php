<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test d'accès à la liste des utilisateurs pour un administrateur
     */
    public function test_admin_can_view_users_list()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)
            ->get(route('users.index'));
            
        $response->assertStatus(200);
        $response->assertViewIs('users.index');
    }

    /**
     * Test d'accès refusé pour un utilisateur non autorisé
     */
    public function test_non_admin_cannot_view_users_list()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $response = $this->actingAs($user)
            ->get(route('users.index'));
            
        $response->assertStatus(403);
    }

    /**
     * Test de création d'un nouvel utilisateur
     */
    public function test_admin_can_create_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user'
        ];
        
        $response = $this->actingAs($admin)
            ->post(route('users.store'), $userData);
            
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => $userData['email']]);
    }

    /**
     * Test de validation des champs requis lors de la création
     */
    public function test_create_user_requires_name_email_and_password()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)
            ->post(route('users.store'), []);
            
        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    /**
     * Test de mise à jour d'un utilisateur
     */
    public function test_admin_can_update_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'editor'
        ];
        
        $response = $this->actingAs($admin)
            ->put(route('users.update', $user), $updateData);
            
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'editor'
        ]);
    }

    /**
     * Test de suppression d'un utilisateur
     */
    public function test_admin_can_delete_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        
        $response = $this->actingAs($admin)
            ->delete(route('users.destroy', $user));
            
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /**
     * Test d'affichage d'un utilisateur
     */
    public function test_can_view_user_details()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        
        $response = $this->actingAs($admin)
            ->get(route('users.show', $user));
            
        $response->assertStatus(200);
        $response->assertViewIs('users.show');
        $response->assertViewHas('user', $user);
    }

    /**
     * Test de la fonctionnalité de recherche
     */
    public function test_can_search_users()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['name' => 'John Doe']);
        $user2 = User::factory()->create(['name' => 'Jane Smith']);
        
        $response = $this->actingAs($admin)
            ->get(route('users.index', ['search' => 'John']));
            
        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Smith');
    }

    /**
     * Test de la pagination
     */
    public function test_users_are_paginated()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Créer plus d'utilisateurs que le nombre par page (par défaut 15)
        User::factory()->count(20)->create();
        
        $response = $this->actingAs($admin)
            ->get(route('users.index'));
            
        $response->assertViewHas('users');
        $this->assertEquals(15, $response->viewData('users')->count());
    }

    /**
     * Test de la validation des rôles
     */
    public function test_cannot_assign_invalid_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'invalid-role'
        ];
        
        $response = $this->actingAs($admin)
            ->post(route('users.store'), $userData);
            
        $response->assertSessionHasErrors('role');
    }
}
