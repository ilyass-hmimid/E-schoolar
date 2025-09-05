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
                'name' => 'Administrateur',
                'email' => $email,
                'password' => Hash::make($password),
                'is_active' => true,
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
