<?php

namespace Database\Factories;

use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Professeur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classe>
 */
class ClasseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filiere = Filiere::inRandomOrder()->first() ?? Filiere::factory()->create();
        $niveau = Niveau::inRandomOrder()->first() ?? Niveau::factory()->create();
        $professeur = Professeur::inRandomOrder()->first() ?? Professeur::factory()->create();
        
        $niveaux = [
            '6ème', '5ème', '4ème', '3ème',
            '2ème année Bac', '1ère année Bac',
            'Tronc Commun', '1ère année Bac Sciences', '2ème année Bac Sciences',
            '1ère année Bac Lettres', '2ème année Bac Lettres',
            '1ère année Bac Économie', '2ème année Bac Économie',
            '1ère année Bac Techniques', '2ème année Bac Techniques'
        ];
        
        $niveau_libelle = $this->faker->randomElement($niveaux);
        $annee_scolaire = (date('Y') - 1) . '-' . date('Y');
        
        return [
            'libelle' => $niveau_libelle . ' ' . $filiere->libelle,
            'niveau_id' => $niveau->id,
            'filiere_id' => $filiere->id,
            'professeur_principal_id' => $professeur->id,
            'annee_scolaire' => $annee_scolaire,
            'capacite_max' => $this->faker->numberBetween(20, 40),
            'description' => $this->faker->optional()->sentence,
            'est_actif' => true,
        ];
    }

    /**
     * Configure the model factory to create an inactive class.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }

    /**
     * Configure the model factory to create a class for a specific level.
     */
    public function withNiveau($niveauId): static
    {
        return $this->state(fn (array $attributes) => [
            'niveau_id' => $niveauId,
        ]);
    }

    /**
     * Configure the model factory to create a class for a specific field of study.
     */
    public function withFiliere($filiereId): static
    {
        return $this->state(fn (array $attributes) => [
            'filiere_id' => $filiereId,
        ]);
    }

    /**
     * Configure the model factory to create a class for a specific school year.
     */
    public function withAnneeScolaire($anneeScolaire): static
    {
        return $this->state(fn (array $attributes) => [
            'annee_scolaire' => $anneeScolaire,
        ]);
    }
}
