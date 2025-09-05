<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InscriptionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        $anneeScolaire = "$currentYear-$nextYear";
        
        // Get sample data
        $etudiants = DB::table('users')->where('role', 'etudiant')->get();
        $classes = DB::table('classes')->get();
        
        // If no students or classes, we can't create inscriptions
        if ($etudiants->isEmpty() || $classes->isEmpty()) {
            $this->command->warn('No students or classes found. Skipping inscriptions seeding.');
            return;
        }
        
        $statuses = ['inscrit', 'termine', 'abandonne', 'exclu'];
        $inscriptions = [];
        
        // Create 1-3 inscriptions per class (or fewer if not enough students)
        foreach ($classes as $classe) {
            // Determine how many students to select (1-3, but no more than available)
            $numStudents = min(rand(1, 3), $etudiants->count());
            if ($numStudents === 0) break; // No more students to assign
            
            // Get a random subset of students for this class
            $classStudents = $etudiants->random($numStudents);
            
            // Handle case where we only have one student (random returns a single model, not a collection)
            $classStudents = collect([$classStudents])->flatten();
            
            foreach ($classStudents as $etudiant) {
                // Random status (weighted towards 'inscrit')
                $weights = [
                    'inscrit' => 50,
                    'termine' => 17,
                    'abandonne' => 17,
                    'exclu' => 16
                ];
                $rand = mt_rand(1, 100);
                $cumulative = 0;
                $status = 'inscrit'; // Default status
                
                foreach ($weights as $statusKey => $weight) {
                    $cumulative += $weight;
                    if ($rand <= $cumulative) {
                        $status = $statusKey;
                        break;
                    }
                }
                
                $inscriptions[] = [
                    'etudiant_id' => $etudiant->id,
                    'classe_id' => $classe->id,
                    'annee_scolaire' => $anneeScolaire,
                    'statut' => $status,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                
                // Remove this student to avoid duplicate inscriptions in the same class
                $etudiants = $etudiants->reject(function ($item) use ($etudiant) {
                    return $item->id === $etudiant->id;
                });
                
                // If we've run out of students, break the loop
                if ($etudiants->isEmpty()) {
                    break 2;
                }
            }
        }
        
        // Insert inscriptions
        foreach ($inscriptions as $inscription) {
            // Check if this inscription already exists
            $exists = DB::table('inscriptions')
                ->where('etudiant_id', $inscription['etudiant_id'])
                ->where('classe_id', $inscription['classe_id'])
                ->where('annee_scolaire', $inscription['annee_scolaire'])
                ->exists();
                
            if (!$exists) {
                DB::table('inscriptions')->insert($inscription);
            }
        }
        
        $this->command->info('Successfully seeded inscriptions table.');
    }
}
