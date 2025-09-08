<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'user:create-admin';
    protected $description = 'Create or update admin user';

    public function handle()
    {
        $email = 'admin@teste.com';
        $password = 'motdepass';
        
        $user = User::where('email', $email)->first();
        
        if ($user) {
            $user->password = Hash::make($password);
            $user->is_active = true;
            $user->save();
            $this->info("Mot de passe de l'administrateur mis à jour avec succès!");
        } else {
            $user = User::create([
                'name' => 'Admin',
                'prenom' => 'Admin',
                'email' => $email,
                'password' => Hash::make($password),
                'status' => 'actif',
                'role' => 'admin',
                'telephone' => '0600000000',
                'adresse' => 'Adresse admin',
                'date_naissance' => now()->subYears(30),
                'lieu_naissance' => 'Ville',
                'niveau_id' => 1,
                'filiere_id' => 1
            ]);
            
            if (class_exists('Spatie\\Permission\\Models\\Role')) {
                $user->assignRole('admin');
            }
            
            $this->info('Nouvel administrateur créé avec succès!');
        }
        
        $this->info("Email: {$email}");
        $this->info("Mot de passe: {$password}");
        
        return 0;
    }
}
