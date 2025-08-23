<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;

echo "=== TEST FINAL LOGIN + DASHBOARD ===\n\n";

try {
    // 1. Test de login
    echo "1. Test de login:\n";
    $loginController = new LoginController();
    $request = Request::create('/login', 'POST', [
        'email' => 'admin@test.com',
        'password' => 'password',
        'remember' => false
    ]);
    
    $request->setLaravelSession(app('session.store'));
    $response = $loginController->login($request);
    
    echo "✅ Login réussi\n";
    echo "   Type de réponse: " . get_class($response) . "\n";
    
    // 2. Test du dashboard
    echo "\n2. Test du dashboard:\n";
    $dashboardController = new DashboardController();
    $dashboardRequest = Request::create('/dashboard', 'GET');
    $dashboardRequest->setLaravelSession(app('session.store'));
    
    $dashboardResponse = $dashboardController->index();
    
    echo "✅ Dashboard accessible\n";
    echo "   Type de réponse: " . get_class($dashboardResponse) . "\n";
    
    if (method_exists($dashboardResponse, 'getData')) {
        $data = $dashboardResponse->getData();
        echo "   Données: " . json_encode(array_keys($data)) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur:\n";
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . "\n";
    echo "   Ligne: " . $e->getLine() . "\n";
}

echo "\n=== FIN DU TEST ===\n";
