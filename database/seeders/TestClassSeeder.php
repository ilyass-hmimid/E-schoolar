<?php

namespace Database\Seeders;

use App\Models\Classe;
use Illuminate\Database\Seeder;

class TestClassSeeder extends Seeder
{
    public function run()
    {
        // Vérifier si la classe de test existe déjà
        if (Classe::where('code', 'TEST-1')->exists()) {
            echo "Test class already exists.\n";
            return;
        }

        // Créer une classe de test
        $classe = Classe::create([
            'nom' => 'Classe de Test',
            'code' => 'TEST-1',
            'filiere_id' => 1, // Première filière disponible
            'niveau_id' => 1,  // Premier niveau disponible
            'description' => 'Classe de test pour le développement',
            'capacite_max' => 30,
            'est_actif' => true,
            'annee_scolaire' => '2024-2025',
        ]);

        echo "Test class created successfully!\n";
    }
}
