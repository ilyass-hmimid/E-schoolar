<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NiveauTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $niveaux = [
            [
                'nom' => '2ème année Baccalauréat',
                'code' => '2BAC',
                'description' => 'Deuxième année du cycle du baccalauréat',
                'ordre' => 1,
                'est_actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => '1ère année Baccalauréat',
                'code' => '1BAC',
                'description' => 'Première année du cycle du baccalauréat',
                'ordre' => 2,
                'est_actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Tronc Commun',
                'code' => 'TC',
                'description' => 'Tronc commun scientifique et littéraire',
                'ordre' => 3,
                'est_actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Collège',
                'code' => 'COLLEGE',
                'description' => 'Enseignement collégial (1ère à 3ème année)',
                'ordre' => 4,
                'est_actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Primaire',
                'code' => 'PRIMAIRE',
                'description' => 'Enseignement primaire (1ère à 6ème année)',
                'ordre' => 5,
                'est_actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Maternelle',
                'code' => 'MATERNELLE',
                'description' => 'Cycle préscolaire',
                'ordre' => 6,
                'est_actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Insert niveaux
        foreach ($niveaux as $niveau) {
            // Check if niveau already exists
            if (!DB::table('niveaux')->where('code', $niveau['code'])->exists()) {
                DB::table('niveaux')->insert($niveau);
            } else {
                // Update existing niveau
                DB::table('niveaux')
                    ->where('code', $niveau['code'])
                    ->update($niveau);
            }
        }
    }
}
