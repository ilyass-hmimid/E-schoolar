<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Vérifier si l'utilisateur existe déjà
$user = User::where('email', 'admin@teste.com')->first();

if ($user) {
    echo "Mise à jour de l'utilisateur existant...\n";
    $user->password = Hash::make('motdepass');
    $user->is_active = true;
    $user->save();
    echo "Mot de passe mis à jour pour l'utilisateur: " . $user->email . "\n";
} else {
    // Créer un nouvel utilisateur admin
    $user = User::create([
        'name' => 'Administrateur',
        'email' => 'admin@teste.com',
        'password' => Hash::make('motdepass'),
        'is_active' => true,
    ]);
    
    // Attribuer le rôle admin
    $user->assignRole('admin');
    
    echo "Nouvel utilisateur admin créé avec succès!\n";
    echo "Email: admin@teste.com\n";
    echo "Mot de passe: motdepass\n";
}
