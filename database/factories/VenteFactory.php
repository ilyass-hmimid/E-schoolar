<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vente>
 */
class VenteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'montant' => $this->faker->randomFloat(2, 10, 1000),
            'methode_paiement' => $this->faker->randomElement(['carte', 'virement', 'especes', 'cheque']),
            'reference_paiement' => $this->faker->uuid,
            'statut' => $this->faker->randomElement(['en_attente', 'payee', 'annulee', 'remboursee']),
            'date_paiement' => $this->faker->dateTimeBetween('-1 year'),
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
