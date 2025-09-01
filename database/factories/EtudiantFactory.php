<?php

namespace Database\Factories;

use App\Models\Classe;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Etudiant>
 */
class EtudiantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $classe = Classe::inRandomOrder()->first() ?? Classe::factory()->create();
        $filiere = Filiere::inRandomOrder()->first() ?? Filiere::factory()->create();
        $niveau = Niveau::inRandomOrder()->first() ?? Niveau::factory()->create();
        
        return [
            'user_id' => User::factory()->withRole('eleve'),
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'email' => $this->faker->unique()->safeEmail,
            'telephone' => $this->faker->phoneNumber,
            'telephone2' => $this->faker->optional()->phoneNumber,
            'adresse' => $this->faker->address,
            'date_naissance' => $this->faker->dateTimeBetween('-30 years', '-15 years'),
            'lieu_naissance' => $this->faker->city,
            'classe_id' => $classe->id,
            'filiere_id' => $filiere->id,
            'niveau_id' => $niveau->id,
            'date_inscription' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'numero_etudiant' => 'ETU' . $this->faker->unique()->numberBetween(1000, 9999),
            'nom_pere' => $this->faker->name('male'),
            'nom_mere' => $this->faker->name('female'),
            'telephone_pere' => $this->faker->phoneNumber,
            'telephone_mere' => $this->faker->phoneNumber,
            'adresse_parents' => $this->faker->address,
            'photo' => null, // Vous pouvez ajouter une logique pour générer des photos si nécessaire
            'est_actif' => $this->faker->boolean(90), // 90% de chance d'être actif
        ];
    }

    /**
     * Configure the model factory to create an inactive student.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }

    /**
     * Configure the model factory to create a student with a specific class.
     */
    public function withClasse($classeId): static
    {
        return $this->state(fn (array $attributes) => [
            'classe_id' => $classeId,
        ]);
    }

    /**
     * Configure the model factory to create a student with a specific filiere.
     */
    public function withFiliere($filiereId): static
    {
        return $this->state(fn (array $attributes) => [
            'filiere_id' => $filiereId,
        ]);
    }
}
