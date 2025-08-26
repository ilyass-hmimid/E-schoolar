<?php

namespace Database\Factories;

use App\Models\Filiere;
use Illuminate\Database\Eloquent\Factories\Factory;

class FiliereFactory extends Factory
{
    protected $model = Filiere::class;

    public function definition()
    {
        $filiere = $this->faker->unique()->randomElement([
            'Informatique', 'Gestion', 'Marketing', 'Comptabilité', 'Ressources Humaines'
        ]);
        
        return [
            'code' => strtoupper(substr($filiere, 0, 3)) . $this->faker->unique()->numberBetween(100, 999),
            'nom' => $filiere,
            'description' => 'Formation en ' . $filiere,
            'duree_annees' => $this->faker->numberBetween(1, 5),
            'frais_inscription' => $this->faker->numberBetween(1000, 5000),
            'frais_mensuel' => $this->faker->numberBetween(500, 2000),
            'est_actif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
