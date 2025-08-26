<?php

namespace Database\Factories;

use App\Models\Matiere;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatiereFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Matiere::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('MAT###')),
            'nom' => $this->faker->word,
            'type' => $this->faker->randomElement(['scientifique', 'litteraire', 'technique', 'langue']),
            'description' => $this->faker->sentence,
            'prix' => $this->faker->randomFloat(2, 100, 1000),
            'prix_prof' => $this->faker->randomFloat(2, 50, 500),
            'est_actif' => $this->faker->boolean(80), // 80% de chance d'être actif
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
