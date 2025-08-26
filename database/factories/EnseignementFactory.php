<?php

namespace Database\Factories;

use App\Models\Enseignement;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Niveau;
use App\Models\Filiere;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnseignementFactory extends Factory
{
    protected $model = Enseignement::class;

    public function definition()
    {
        return [
            'professeur_id' => User::factory(),
            'matiere_id' => Matiere::factory(),
            'niveau_id' => Niveau::factory(),
            'filiere_id' => Filiere::factory(),
            'nombre_heures_semaine' => $this->faker->numberBetween(1, 10),
            'jour_cours' => $this->faker->dayOfWeek,
            'heure_debut' => $this->faker->time('H:i'),
            'heure_fin' => $this->faker->time('H:i', strtotime('+2 hours')),
            'date_debut' => now(),
            'date_fin' => now()->addMonths(3),
            'est_actif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
