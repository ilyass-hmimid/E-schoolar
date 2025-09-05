<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FiliereTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        // Get all niveau IDs
        $niveaux = DB::table('niveaux')->get()->keyBy('code');
        
        $filieres = [
            // Baccalauréat Scientifique
            [
                'nom' => 'Sciences Mathématiques',
                'code' => 'SM',
                'description' => 'Filière Sciences Mathématiques (Baccalauréat)',
                'niveau_id' => $niveaux['2BAC']->id,
                'frais_mensuel' => 800.00,
                'est_actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Sciences Physiques',
                'code' => 'SP',
                'description' => 'Filière Sciences Physiques (Baccalauréat)',
                'niveau_id' => $niveaux['2BAC']->id,
                'frais_mensuel' => 800.00,
                'est_actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Sciences de la Vie et de la Terre',
                'code' => 'SVT',
                'description' => 'Filière Sciences de la Vie et de la Terre (Baccalauréat)',
                'niveau_id' => $niveaux['2BAC']->id,
                'frais_mensuel' => 750.00,
                'est_actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Sciences Économiques',
                'code' => 'SE',
                'description' => 'Filière Sciences Économiques (Baccalauréat)',
                'niveau_id' => $niveaux['2BAC']->id,
                'frais_mensuel' => 700.00,
                'est_actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Tronc Commun
            [
                'nom' => 'Tronc Commun Scientifique',
                'code' => 'TCS',
                'description' => 'Tronc Commun Scientifique',
                'niveau_id' => $niveaux['TC']->id,
                'frais_mensuel' => 700.00,
                'est_actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Tronc Commun Littéraire',
                'code' => 'TCL',
                'description' => 'Tronc Commun Littéraire',
                'niveau_id' => $niveaux['TC']->id,
                'frais_mensuel' => 650.00,
                'est_actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Collège
            [
                'nom' => 'Collège - 3ème Année',
                'code' => 'COLL3',
                'description' => 'Troisième année collégiale',
                'niveau_id' => $niveaux['COLLEGE']->id,
                'frais_mensuel' => 600.00,
                'est_actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Collège - 2ème Année',
                'code' => 'COLL2',
                'description' => 'Deuxième année collégiale',
                'niveau_id' => $niveaux['COLLEGE']->id,
                'frais_mensuel' => 550.00,
                'est_actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Collège - 1ère Année',
                'code' => 'COLL1',
                'description' => 'Première année collégiale',
                'niveau_id' => $niveaux['COLLEGE']->id,
                'frais_mensuel' => 500.00,
                'est_actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Insert filieres
        foreach ($filieres as $filiere) {
            // Check if filiere already exists
            if (!DB::table('filieres')->where('code', $filiere['code'])->exists()) {
                DB::table('filieres')->insert($filiere);
            } else {
                // Update existing filiere
                DB::table('filieres')
                    ->where('code', $filiere['code'])
                    ->update($filiere);
            }
        }
    }
}
