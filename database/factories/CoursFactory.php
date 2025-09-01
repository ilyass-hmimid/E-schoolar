<?php

namespace Database\Factories;

use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Professeur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cours>
 */
class CoursFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $classe = Classe::inRandomOrder()->first() ?? Classe::factory()->create();
        $matiere = Matiere::inRandomOrder()->first() ?? Matiere::factory()->create();
        $professeur = Professeur::inRandomOrder()->first() ?? Professeur::factory()->create();
        
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $heureDebut = $this->faker->randomElement([
            '08:00:00', '09:00:00', '10:00:00', '11:00:00', 
            '14:00:00', '15:00:00', '16:00:00', '17:00:00'
        ]);
        
        // Ajouter 1 ou 2 heures à l'heure de début pour l'heure de fin
        $heureFin = date('H:i:s', strtotime($heureDebut) + $this->faker->randomElement([3600, 7200]));
        
        return [
            'classe_id' => $classe->id,
            'matiere_id' => $matiere->id,
            'professeur_id' => $professeur->id,
            'jour' => $this->faker->randomElement($jours),
            'heure_debut' => $heureDebut,
            'heure_fin' => $heureFin,
            'salle' => 'Salle ' . $this->faker->randomElement(['A', 'B', 'C', 'D']) . $this->faker->numberBetween(1, 20),
            'annee_scolaire' => (date('Y') - 1) . '-' . date('Y'),
            'est_actif' => true,
        ];
    }

    /**
     * Configure the model factory to create an inactive course.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }

    /**
     * Configure the model factory to create a course for a specific class.
     */
    public function withClasse($classeId): static
    {
        return $this->state(fn (array $attributes) => [
            'classe_id' => $classeId,
        ]);
    }

    /**
     * Configure the model factory to create a course for a specific subject.
     */
    public function withMatiere($matiereId): static
    {
        return $this->state(fn (array $attributes) => [
            'matiere_id' => $matiereId,
        ]);
    }

    /**
     * Configure the model factory to create a course taught by a specific professor.
     */
    public function withProfesseur($professeurId): static
    {
        return $this->state(fn (array $attributes) => [
            'professeur_id' => $professeurId,
        ]);
    }
}
