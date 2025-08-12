<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    // Récupérer l'utilisateur connecté
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Routes pour les notifications
    Route::prefix('notifications')->group(function () {
        // Récupérer les notifications
        Route::get('/', [NotificationController::class, 'index']);
        
        // Récupérer une notification spécifique
        Route::get('/{id}', [NotificationController::class, 'show']);
        
        // Marquer une notification comme lue
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        
        // Marquer toutes les notifications comme lues
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        
        // Supprimer une notification
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
        
        // Supprimer toutes les notifications
        Route::delete('/', [NotificationController::class, 'clearAll']);
        
        // Compter les notifications non lues
        Route::get('/unread/count', [NotificationController::class, 'unreadCount']);
        
        // Gérer les préférences de notification
        Route::prefix('preferences')->group(function () {
            // Récupérer les préférences
            Route::get('/', [NotificationController::class, 'getPreferences']);
            
            // Mettre à jour les préférences
            Route::put('/', [NotificationController::class, 'updatePreferences']);
        });
    });
});
