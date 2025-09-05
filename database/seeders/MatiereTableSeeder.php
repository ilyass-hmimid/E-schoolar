<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MatiereTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $matieres = [
            [
                'nom' => 'Mathématiques',
                'code' => 'MATH',
                'prix' => 250.00,
                'prix_prof' => 150.00,
                'type' => 'Scientifique',
                'description' => 'Cours de mathématiques couvrant l\'algèbre, la géométrie et l\'analyse.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Physique',
                'code' => 'PHYS',
                'prix' => 230.00,
                'prix_prof' => 140.00,
                'type' => 'Scientifique',
                'description' => 'Cours de physique fondamentale et appliquée.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Français',
                'code' => 'FR',
                'prix' => 200.00,
                'prix_prof' => 120.00,
                'type' => 'Littéraire',
                'description' => 'Cours de langue et littérature française.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Anglais',
                'code' => 'EN',
                'prix' => 180.00,
                'prix_prof' => 100.00,
                'type' => 'Langue',
                'description' => 'Cours d\'anglais langue étrangère.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Sciences de la Vie et de la Terre',
                'code' => 'SVT',
                'prix' => 220.00,
                'prix_prof' => 130.00,
                'type' => 'Scientifique',
                'description' => 'Étude des êtres vivants et de la Terre.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Insert matieres
        foreach ($matieres as $matiere) {
            // Check if matiere already exists
            if (!DB::table('matieres')->where('code', $matiere['code'])->exists()) {
                DB::table('matieres')->insert($matiere);
            }
        }
    }
}
