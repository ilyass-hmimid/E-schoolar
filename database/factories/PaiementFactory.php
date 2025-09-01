<?php

namespace Database\Factories;

use App\Models\Etudiant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paiement>
 */
class PaiementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $etudiant = Etudiant::inRandomOrder()->first() ?? Etudiant::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        
        $montant = $this->faker->numberBetween(500, 5000);
        $montantPaye = $this->faker->numberBetween(100, $montant);
        $resteAPayer = $montant - $montantPaye;
        
        $datePaiement = $this->faker->dateTimeBetween('-1 year', 'now');
        $moisPaiement = $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m');
        
        return [
            'etudiant_id' => $etudiant->id,
            'user_id' => $user->id,
            'montant' => $montant,
            'montant_paye' => $montantPaye,
            'reste_a_payer' => $resteAPayer,
            'mode_paiement' => $this->faker->randomElement(['espèces', 'chèque', 'virement', 'carte bancaire']),
            'numero_cheque' => $this->faker->optional(0.3)->bankAccountNumber,
            'date_paiement' => $datePaiement,
            'mois_paiement' => $moisPaiement,
            'annee_scolaire' => (date('Y') - 1) . '-' . date('Y'),
            'statut' => $resteAPayer > 0 ? 'partiel' : 'payé',
            'description' => $this->faker->optional()->sentence,
        ];
    }

    /**
     * Configure the model factory to create a fully paid payment.
     */
    public function paye(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'montant_paye' => $attributes['montant'],
                'reste_a_payer' => 0,
                'statut' => 'payé',
            ];
        });
    }

    /**
     * Configure the model factory to create a partial payment.
     */
    public function partiel(): static
    {
        return $this->state(function (array $attributes) {
            $montantPaye = $this->faker->numberBetween(100, $attributes['montant'] - 1);
            return [
                'montant_paye' => $montantPaye,
                'reste_a_payer' => $attributes['montant'] - $montantPaye,
                'statut' => 'partiel',
            ];
        });
    }

    /**
     * Configure the model factory to create an unpaid payment.
     */
    public function impaye(): static
    {
        return $this->state(fn (array $attributes) => [
            'montant_paye' => 0,
            'reste_a_payer' => $attributes['montant'],
            'statut' => 'impayé',
        ]);
    }

    /**
     * Configure the model factory to create a payment for a specific student.
     */
    public function forEtudiant($etudiantId): static
    {
        return $this->state(fn (array $attributes) => [
            'etudiant_id' => $etudiantId,
        ]);
    }

    /**
     * Configure the model factory to create a payment for a specific school year.
     */
    public function forAnneeScolaire($anneeScolaire): static
    {
        return $this->state(fn (array $attributes) => [
            'annee_scolaire' => $anneeScolaire,
        ]);
    }
}
