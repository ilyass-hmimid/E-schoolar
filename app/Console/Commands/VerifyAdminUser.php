<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class VerifyAdminUser extends Command
{
    protected $signature = 'user:verify-admin';
    protected $description = 'Vérifie et crée un utilisateur admin si nécessaire';

    public function handle()
    {
        $email = 'admin@allotawjih.com';
        $password = 'password';
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'Admin',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 1, // ADMIN role
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            
            $this->info('Admin user created successfully!');
        } else {
            $user->update([
                'password' => Hash::make($password),
                'role' => 1,
                'is_active' => true,
            ]);
            
            $this->info('Admin user updated successfully!');
        }
        
        $this->info('Email: ' . $email);
        $this->info('Password: ' . $password);
        
        return Command::SUCCESS;
    }
}
