<?php

namespace Database\Factories;

use App\Models\Niveau;
use Illuminate\Database\Eloquent\Factories\Factory;

class NiveauFactory extends Factory
{
    protected $model = Niveau::class;

    public function definition()
    {
        return [
            'code' => 'N' . $this->faker->unique()->numberBetween(100, 999),
            'nom' => 'Niveau ' . $this->faker->unique()->numberBetween(1, 10),
            'description' => $this->faker->sentence,
            'ordre' => $this->faker->unique()->numberBetween(1, 10),
            'est_actif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
