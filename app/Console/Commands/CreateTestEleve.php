<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateTestEleve extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-test-eleve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test student and associate with subjects';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create a test student user
        $user = \App\Models\User::create([
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => 'eleve',
            'is_active' => true,
            'phone' => '0612345678',
            'address' => '123 Test Street',
            'niveau_id' => 1, // Assuming niveau with ID 1 exists
            'filiere_id' => 1, // Assuming filiere with ID 1 exists
        ]);

        // Create a corresponding eleve record
        $eleve = \App\Models\Eleve::create([
            'user_id' => $user->id,
            'classe_id' => 1, // Assuming a class with ID 1 exists
            'date_naissance' => '2000-01-01',
            'ville' => 'Test City',
            'code_postal' => '10000',
            'pays' => 'Maroc',
            'nom_pere' => 'Test Father',
            'profession_pere' => 'Test Profession',
            'telephone_pere' => '0612345679',
            'nom_mere' => 'Test Mother',
            'profession_mere' => 'Test Profession',
            'telephone_mere' => '0612345680',
            'notes' => 'Élève de test',
            'est_actif' => true,
        ]);

        // Get some subjects to associate
        $matieres = \App\Models\Matiere::take(3)->get();
        
        if ($matieres->isEmpty()) {
            $this->error('No subjects found. Please seed the database with subjects first.');
            return 1;
        }

        // Associate subjects with the student
        $eleve->matieres()->attach($matieres->pluck('id'));

        $this->info('Test student created successfully!');
        $this->info('Email: student@test.com');
        $this->info('Password: password');
        $this->info('Associated subjects: ' . $matieres->pluck('nom')->implode(', '));

        return 0;
    }
}
