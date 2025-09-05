<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CentreController;
use App\Http\Controllers\Api\MatiereController;
use App\Http\Controllers\Api\EleveController;
use App\Http\Controllers\Api\ProfesseurController;
use App\Http\Controllers\Api\AbsenceController;
use App\Http\Controllers\Api\PaiementApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Voici où vous pouvez enregistrer les routes API de votre application.
| Ces routes sont chargées par le RouteServiceProvider et toutes seront
| affectées au groupe de middleware "api".
|
*/

// Routes API protégées par authentification
Route::middleware(['auth:sanctum', 'active'])->group(function () {
    // ============================================================================
    // ROUTES API GESTION DES ÉLÈVES
    // ============================================================================
    Route::prefix('eleves')->group(function () {
        // Liste des élèves avec filtres et pagination
        Route::get('/', [EleveController::class, 'index']);
        
        // Détails d'un élève
        Route::get('/{eleve}', [EleveController::class, 'show']);
        
        // Créer un élève
        Route::post('/', [EleveController::class, 'store']);
        
        // Mettre à jour un élève
        Route::put('/{eleve}', [EleveController::class, 'update']);
        
        // Supprimer un élève
        Route::delete('/{eleve}', [EleveController::class, 'destroy']);
    });

    // ============================================================================
    // ROUTES API GESTION DES PROFESSEURS
    // ============================================================================
    Route::prefix('professeurs')->group(function () {
        // Liste des professeurs avec filtres et pagination
        Route::get('/', [ProfesseurController::class, 'index']);
        
        // Détails d'un professeur
        Route::get('/{professeur}', [ProfesseurController::class, 'show']);
        
        // Créer un professeur
        Route::post('/', [ProfesseurController::class, 'store']);
        
        // Mettre à jour un professeur
        Route::put('/{professeur}', [ProfesseurController::class, 'update']);
        
        // Supprimer un professeur
        Route::delete('/{professeur}', [ProfesseurController::class, 'destroy']);
    });
    
    // ============================================================================
    // ROUTES API GESTION DES MATIÈRES
    // ============================================================================
    Route::prefix('matieres')->group(function () {
        // Liste des matières avec filtres et pagination
        Route::get('/', [MatiereController::class, 'index']);
        
        // Détails d'une matière
        Route::get('/{matiere}', [MatiereController::class, 'show']);
        
        // Créer une matière
        Route::post('/', [MatiereController::class, 'store']);
        
        // Mettre à jour une matière
        Route::put('/{matiere}', [MatiereController::class, 'update']);
        
        // Supprimer une matière
        Route::delete('/{matiere}', [MatiereController::class, 'destroy']);
    });
    
    // ============================================================================
    // ROUTES API GESTION DES ABSENCES
    // ============================================================================
    Route::prefix('absences')->group(function () {
        // Liste des absences avec filtres
        Route::get('/', [AbsenceController::class, 'index']);
        
        // Enregistrer une absence
        Route::post('/', [AbsenceController::class, 'store']);
        
        // Détails d'une absence
        Route::get('/{absence}', [AbsenceController::class, 'show']);
        
        // Mettre à jour une absence
        Route::put('/{absence}', [AbsenceController::class, 'update']);
        
        // Supprimer une absence
        Route::delete('/{absence}', [AbsenceController::class, 'destroy']);
        
        // Statistiques des absences
        Route::get('/statistiques', [AbsenceController::class, 'statistics']);
    });
    
    // ============================================================================
    // ROUTES API GESTION DES PAIEMENTS
    // ============================================================================
    Route::prefix('paiements')->group(function () {
        // Liste des paiements avec filtres
        Route::get('/', [PaiementApiController::class, 'index']);
        
        // Enregistrer un paiement
        Route::post('/', [PaiementApiController::class, 'store']);
        
        // Détails d'un paiement
        Route::get('/{paiement}', [PaiementApiController::class, 'show']);
        
        // Mettre à jour un paiement
        Route::put('/{paiement}', [PaiementApiController::class, 'update']);
        
        // Supprimer un paiement
        Route::delete('/{paiement}', [PaiementApiController::class, 'destroy']);
        
        // Exporter les paiements
        Route::get('/export', [PaiementApiController::class, 'export']);
        
        // Statistiques des paiements
        Route::get('/statistiques', [PaiementApiController::class, 'getStats']);
    });
    
    // ============================================================================
    // ROUTES API GESTION DES FILIÈRES
    // ============================================================================
    Route::prefix('filieres')->group(function () {
        // Liste des filières
        Route::get('/', [CentreController::class, 'getFilieres']);
        
        // Détails d'une filière
        Route::get('/{id}', [CentreController::class, 'getFiliere']);
        
        // Créer une filière
        Route::post('/', [CentreController::class, 'storeFiliere']);
        
        // Mettre à jour une filière
        Route::put('/{id}', [CentreController::class, 'updateFiliere']);
        
        // Supprimer une filière
        Route::delete('/{id}', [CentreController::class, 'destroyFiliere']);
    });
    
    // ============================================================================
    // ROUTES API GESTION DES NIVEAUX
    // ============================================================================
    Route::prefix('niveaux')->group(function () {
        // Liste des niveaux
        Route::get('/', [CentreController::class, 'getNiveaux']);
        
        // Détails d'un niveau
        Route::get('/{id}', [CentreController::class, 'getNiveau']);
        
        // Créer un niveau
        Route::post('/', [CentreController::class, 'storeNiveau']);
        
        // Mettre à jour un niveau
        Route::put('/{id}', [CentreController::class, 'updateNiveau']);
        
        // Supprimer un niveau
        Route::delete('/{id}', [CentreController::class, 'destroyNiveau']);
    });
});

// Route par défaut pour les requêtes API non trouvées
Route::fallback(function () {
    return response()->json([
        'message' => 'Route API non trouvée.',
        'status' => 404
    ], 404);
});
