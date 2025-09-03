<?php

namespace Database\Factories;

use App\Enums\RoleType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 4, // ELEVE = 4
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Set the user's role.
     * 
     * @param int|string $role Peut être une valeur numérique (1-4) ou une chaîne ('admin', 'professeur', etc.)
     */
    public function withRole($role): static
    {
        // Si c'est une chaîne, convertir en valeur numérique
        if (is_string($role)) {
            $role = strtolower(trim($role));
            $role = match($role) {
                'admin', 'administrateur', '1' => 1,
                'professeur', 'prof', 'teacher', '2' => 2,
                'assistant', 'assist', '3' => 3,
                'eleve', 'etudiant', 'student', '4' => 4,
                default => 4, // Par défaut, on considère que c'est un élève
            };
        }
        
        return $this->state(fn (array $attributes) => [
            'role' => (int)$role, // S'assurer que c'est un entier
        ]);
    }

    /**
     * Configure the model factory to use a specific password.
     */
    public function withPassword(string $password): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => Hash::make($password),
        ]);
    }
}
