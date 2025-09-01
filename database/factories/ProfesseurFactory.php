<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Professeur>
 */
class ProfesseurFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->withRole('professeur'),
            'matricule' => 'PROF' . $this->faker->unique()->numberBetween(1000, 9999),
            'specialite' => $this->faker->randomElement([
                'Mathématiques', 'Physique', 'Chimie', 'SVT',
                'Français', 'Anglais', 'Arabe', 'Philosophie',
                'Histoire-Géographie', 'Éducation Islamique', 'Éducation Physique',
                'Informatique', 'Sciences Économiques', 'Comptabilité'
            ]),
            'date_embauche' => $this->faker->dateTimeBetween('-10 years', 'now'),
            'salaire_base' => $this->faker->numberBetween(5000, 15000),
            'numero_cnss' => $this->faker->unique()->numerify('###-###-###'),
            'numero_cin' => $this->faker->unique()->numerify('AB#######'),
            'date_delivrance_cin' => $this->faker->dateTimeBetween('-20 years', '-1 year'),
            'lieu_delivrance_cin' => $this->faker->city,
            'adresse' => $this->faker->address,
            'telephone' => $this->faker->phoneNumber,
            'telephone_urgence' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'est_actif' => $this->faker->boolean(90), // 90% de chance d'être actif
            'photo' => null, // Vous pouvez ajouter une logique pour générer des photos si nécessaire
        ];
    }

    /**
     * Configure the model factory to create an inactive professor.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }

    /**
     * Configure the model factory to create a professor with a specific specialty.
     */
    public function withSpecialite(string $specialite): static
    {
        return $this->state(fn (array $attributes) => [
            'specialite' => $specialite,
        ]);
    }
}
