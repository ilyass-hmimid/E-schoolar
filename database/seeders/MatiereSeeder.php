<?php

namespace Database\Seeders;

use App\Models\Matiere;
use Illuminate\Database\Seeder;

class MatiereSeeder extends Seeder
{
    /**
     * Exécuter les seeds de la base de données.
     */
    public function run(): void
    {
        // Utiliser la méthode du modèle pour initialiser les matières fixes
        Matiere::initialiserMatieresFixes();
        
        $this->command->info('Les matières fixes ont été initialisées avec succès.');
    }
}
