<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de la page de connexion
     */
    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /**
     * Test de connexion réussie
     */
    public function test_users_can_authenticate_using_the_login_screen()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test d'échec de connexion avec mot de passe invalide
     */
    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /**
     * Test de protection contre les attaques par force brute
     */
    public function test_login_throttling_after_multiple_failed_attempts()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        // Tentatives infructueuses jusqu'à la limite
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        // La 6ème tentative devrait être bloquée
        $response->assertStatus(429);
    }

    /**
     * Test de déconnexion
     */
    public function test_users_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /**
     * Test de redirection si déjà connecté
     */
    public function test_authenticated_users_are_redirected_to_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test de validation des champs requis
     */
    public function test_login_requires_email_and_password()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    /**
     * Test de validation du format d'email
     */
    public function test_login_requires_valid_email()
    {
        $response = $this->post('/login', [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test de connexion avec des identifiants inconnus
     */
    public function test_login_with_nonexistent_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test de la fonctionnalité "Se souvenir de moi"
     */
    public function test_remember_me_functionality()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
            'remember' => 'on',
        ]);

        $response->assertRedirect(route('dashboard'));
        
        // Vérifie que le cookie "remember_me" est présent
        $response->assertCookie(auth()->guard()->getRecallerName(), function ($cookie) use ($user) {
            return str_contains($cookie, $user->getAuthIdentifier());
        });
    }
}
