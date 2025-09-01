<?php

namespace Database\Factories;

use App\Models\Inscription;
use App\Models\User;
use App\Models\Filiere;
use Illuminate\Database\Eloquent\Factories\Factory;

class InscriptionFactory extends Factory
{
    protected $model = Inscription::class;

    public function definition()
    {
        return [
            'etudiant_id' => User::factory(),
            'filiere_id' => Filiere::factory(),
            'pack_id' => null, // À remplacer par un ID de pack si nécessaire
            'heures_restantes' => $this->faker->numberBetween(10, 100),
            'date_expiration' => $this->faker->dateTimeBetween('now', '+1 year'),
            'date_inscription' => now(),
            'mode_paiement' => $this->faker->randomElement(['espèce', 'chèque', 'virement']),
            'annee_scolaire' => now()->year . '-' . (now()->year + 1),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
