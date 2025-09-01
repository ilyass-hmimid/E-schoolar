<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        
        $types = ['info', 'success', 'warning', 'error'];
        $type = $this->faker->randomElement($types);
        
        // Exemples de notifications réalistes
        $templates = [
            'info' => [
                'Nouvelle annonce : ' . $this->faker->sentence,
                'Mise à jour du calendrier : ' . $this->faker->sentence,
                'Rappel : ' . $this->faker->sentence,
            ],
            'success' => [
                'Paiement confirmé pour ' . $this->faker->name,
                'Inscription validée pour ' . $this->faker->name,
                'Absence justifiée avec succès',
            ],
            'warning' => [
                'Attention : ' . $this->faker->sentence,
                'Paiement en retard pour ' . $this->faker->name,
                'Absence non justifiée le ' . $this->faker->date('d/m/Y'),
            ],
            'error' => [
                'Erreur lors du traitement de votre demande',
                'Échec de la validation du paiement',
                'Problème technique détecté',
            ]
        ];
        
        $titre = $this->faker->randomElement($templates[$type]);
        
        return [
            'user_id' => $user->id,
            'titre' => $titre,
            'message' => $this->faker->paragraph,
            'type' => $type,
            'lue' => $this->faker->boolean(70), // 70% de chance d'être lue
            'date_envoi' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'lien' => $this->faker->optional(0.7)->url, // 70% de chance d'avoir un lien
        ];
    }

    /**
     * Configure the model factory to create a read notification.
     */
    public function lue(): static
    {
        return $this->state(fn (array $attributes) => [
            'lue' => true,
        ]);
    }

    /**
     * Configure the model factory to create an unread notification.
     */
    public function nonLue(): static
    {
        return $this->state(fn (array $attributes) => [
            'lue' => false,
        ]);
    }

    /**
     * Configure the model factory to create a notification for a specific user.
     */
    public function forUser($userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }

    /**
     * Configure the model factory to create a notification of a specific type.
     */
    public function ofType($type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }
}
