<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enseignant>
 */
class EnseignantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specialites = [
            'Mathématiques', 'Physique', 'Chimie', 'Sciences de la Vie et de la Terre',
            'Philosophie', 'Français', 'Anglais', 'Arabe', 'Histoire-Géographie',
            'Éducation Islamique', 'Éducation Physique et Sportive', 'Informatique'
        ];

        $diplomes = [
            'Doctorat', 'Master', 'Licence', 'Diplôme des Écoles Normales Supérieures',
            'Diplôme des Hautes Études de Professeur', 'Diplôme des Études Supérieures'
        ];

        return [
            'user_id' => User::factory()->create(['role' => 'professeur'])->id,
            'specialite' => $this->faker->randomElement($specialites),
            'diplome' => $this->faker->randomElement($diplomes),
            'date_embauche' => $this->faker->dateTimeBetween('-10 years', '-1 year'),
            'statut' => $this->faker->randomElement(['actif', 'inactif', 'en_conge']), // Seules les valeurs définies dans la migration sont autorisées
            'bio' => $this->faker->paragraph(),
            'cv_path' => $this->faker->optional()->url(),
            'photo_path' => $this->faker->optional()->imageUrl(200, 200, 'people', true),
            'competences' => json_encode($this->faker->randomElements([
                'Pédagogie', 'Gestion de classe', 'TICE', 'Travail en équipe',
                'Communication', 'Créativité', 'Organisation', 'Gestion du stress'
            ], 4)),
        ];
    }
}
