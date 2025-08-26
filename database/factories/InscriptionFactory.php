<?php

namespace Database\Factories;

use App\Models\Inscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class InscriptionFactory extends Factory
{
    protected $model = Inscription::class;

    public function definition()
    {
        return [
            'IdEtudiant' => User::factory(),
            'IdFil' => Filiere::factory(),
            'pack_id' => null, // À remplacer par un ID de pack si nécessaire
            'heures_restantes' => $this->faker->numberBetween(10, 100),
            'date_expiration' => $this->faker->dateTimeBetween('now', '+1 year'),
            'DateInsc' => now(),
            'Montant' => $this->faker->numberBetween(1000, 5000),
            'ModePaiement' => $this->faker->randomElement(['espèce', 'chèque', 'virement']),
            'Statut' => 'actif',
            'Commentaires' => $this->faker->optional()->sentence,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
