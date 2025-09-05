<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(IllwareConsoleKernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Récupérer l'utilisateur admin
$user = User::where('email', 'admin@teste.com')->first();

if ($user) {
    // Mettre à jour le mot de passe et activer le compte
    $user->password = Hash::make('motdepass');
    $user->is_active = true;
    $user->save();
    
    echo "Mot de passe mis à jour pour l'utilisateur: " . $user->email . "\n";
    echo "Nouveau mot de passe: motdepass\n";
} else {
    echo "L'utilisateur admin@teste.com n'existe pas.\n";
}
