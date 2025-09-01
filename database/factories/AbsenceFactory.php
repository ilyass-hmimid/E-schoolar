<?php

namespace Database\Factories;

use App\Models\Cours;
use App\Models\Etudiant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Absence>
 */
class AbsenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $etudiant = Etudiant::inRandomOrder()->first() ?? Etudiant::factory()->create();
        $cours = Cours::inRandomOrder()->first() ?? Cours::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        
        $dateAbsence = $this->faker->dateTimeBetween('-6 months', 'now');
        $justifie = $this->faker->boolean(30); // 30% de chance d'être justifié
        
        return [
            'etudiant_id' => $etudiant->id,
            'cours_id' => $cours->id,
            'user_id' => $user->id,
            'date_absence' => $dateAbsence,
            'justifie' => $justifie,
            'motif' => $justifie ? $this->faker->randomElement([
                'Maladie', 'Problème de transport', 'Rendez-vous médical',
                'Raison familiale', 'Autre raison personnelle'
            ]) : null,
            'piece_justificative' => $justifie && $this->faker->boolean(50) ? 'justificatif_' . $this->faker->uuid . '.pdf' : null,
            'date_saisie' => $dateAbsence,
            'annee_scolaire' => (date('Y') - 1) . '-' . date('Y'),
        ];
    }

    /**
     * Configure the model factory to create a justified absence.
     */
    public function justifie(): static
    {
        return $this->state(fn (array $attributes) => [
            'justifie' => true,
            'motif' => $this->faker->randomElement([
                'Maladie', 'Problème de transport', 'Rendez-vous médical',
                'Raison familiale', 'Autre raison personnelle'
            ]),
            'piece_justificative' => $this->faker->boolean(70) ? 'justificatif_' . $this->faker->uuid . '.pdf' : null,
        ]);
    }

    /**
     * Configure the model factory to create an unjustified absence.
     */
    public function nonJustifie(): static
    {
        return $this->state(fn (array $attributes) => [
            'justifie' => false,
            'motif' => null,
            'piece_justificative' => null,
        ]);
    }

    /**
     * Configure the model factory to create an absence for a specific student.
     */
    public function forEtudiant($etudiantId): static
    {
        return $this->state(fn (array $attributes) => [
            'etudiant_id' => $etudiantId,
        ]);
    }

    /**
     * Configure the model factory to create an absence for a specific course.
     */
    public function forCours($coursId): static
    {
        return $this->state(fn (array $attributes) => [
            'cours_id' => $coursId,
        ]);
    }

    /**
     * Configure the model factory to create an absence for a specific date.
     */
    public function forDate($date): static
    {
        return $this->state(fn (array $attributes) => [
            'date_absence' => $date,
            'date_saisie' => $date,
        ]);
    }
}
