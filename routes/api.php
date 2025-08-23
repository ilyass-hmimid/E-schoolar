<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CentreController;
use App\Http\Controllers\ProfesseurController;

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

// Routes API publiques (si nécessaire)
Route::middleware('api')->group(function () {
    // Routes publiques ici
});

// Routes API protégées par authentification
Route::middleware(['auth:sanctum', 'active'])->group(function () {
    
    // ============================================================================
    // ROUTES API UTILISATEURS
    // ============================================================================
    
    Route::prefix('users')->middleware('role:admin')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/role', [UserController::class, 'IsAdmin']);
        Route::get('/id-prof', [UserController::class, 'IdProf']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destory']);
    });
    
    // ============================================================================
    // ROUTES API NIVEAUX ET FILIÈRES
    // ============================================================================
    
    Route::prefix('niveaux')->group(function () {
        Route::get('/', [CentreController::class, 'getNiveaux']);
        Route::get('/niveau', [CentreController::class, 'getNiveau']);
        Route::post('/', [CentreController::class, 'storeNiveau']);
        Route::put('/{user}', [CentreController::class, 'updateNiveau']);
        Route::delete('/{user}', [CentreController::class, 'destoryNiveau']);
    });
    
    Route::prefix('filieres')->group(function () {
        Route::get('/{id}', [CentreController::class, 'getFilieres']);
        Route::get('/filiere', [CentreController::class, 'getFiliere']);
        Route::post('/', [CentreController::class, 'storeFiliere']);
        Route::put('/{user}', [CentreController::class, 'updateFiliere']);
        Route::delete('/{user}', [CentreController::class, 'destoryFiliere']);
    });
    
    // ============================================================================
    // ROUTES API MATIÈRES
    // ============================================================================
    
    Route::prefix('matieres')->group(function () {
        Route::get('/', [CentreController::class, 'getMatieres']);
        Route::get('/matiere', [CentreController::class, 'getMatiere']);
        Route::post('/', [CentreController::class, 'storeMatiere']);
        Route::put('/{user}', [CentreController::class, 'updateMatiere']);
        Route::delete('/{user}', [CentreController::class, 'destoryMatiere']);
        Route::get('/etudiant/{idEtudiant}', [CentreController::class, 'getMatiereParEtudiant']);
    });
    
    // ============================================================================
    // ROUTES API PROFESSEURS
    // ============================================================================
    
    Route::prefix('professeurs')->group(function () {
        Route::get('/', [ProfesseurController::class, 'index']);
        Route::get('/profs', [ProfesseurController::class, 'getProfs']);
        Route::get('/enseignements', [ProfesseurController::class, 'getEnseignements']);
        Route::get('/enseignements-par-prof', [CentreController::class, 'getEnseignementsParProf']);
        Route::post('/', [ProfesseurController::class, 'store']);
        Route::put('/{user}', [ProfesseurController::class, 'update']);
        Route::delete('/{user}', [ProfesseurController::class, 'destory']);
        Route::put('/enseignements/{user}', [ProfesseurController::class, 'updateEnseignement']);
        Route::delete('/enseignements/{user}', [ProfesseurController::class, 'destoryEnseignement']);
        Route::get('/for-salaire', [CentreController::class, 'getUsersForSalaire']);
        Route::get('/salaire-prof', [CentreController::class, 'getSlaireForProf']);
        Route::get('/selected/{selectedMatieres}/{selectedNiveau}/{selectedFiliere}', [CentreController::class, 'getProfesseurPourMatieres']);
    });
    
    // ============================================================================
    // ROUTES API ÉTUDIANTS
    // ============================================================================
    
    Route::prefix('etudiants')->group(function () {
        Route::get('/', [CentreController::class, 'index']);
        Route::get('/for-paiement', [CentreController::class, 'getUsersForPaiment']);
        Route::get('/for-prof', [CentreController::class, 'getUsersForProf']);
        Route::get('/for-prof-absence', [CentreController::class, 'getUsersForProfForAbsence']);
        Route::post('/', [CentreController::class, 'store']);
        Route::put('/{user}', [CentreController::class, 'update']);
        Route::delete('/{user}', [CentreController::class, 'destory']);
    });
    
    // ============================================================================
    // ROUTES API ABSENCES
    // ============================================================================
    
    Route::prefix('absences')->group(function () {
        Route::get('/liste', [CentreController::class, 'getListeAbsences']);
        Route::put('/', [CentreController::class, 'marquerAbsence']);
        Route::put('/presence', [CentreController::class, 'marquerPresence']);
    });
    
    // ============================================================================
    // ROUTES API PAIEMENTS ET SALAIRES
    // ============================================================================
    
    Route::prefix('paiements')->group(function () {
        Route::get('/valeurs', [CentreController::class, 'getNbrMat']);
        Route::get('/valeur', [CentreController::class, 'getValeurPaiments']);
        Route::put('/valeurs', [CentreController::class, 'updateValPaiment']);
        Route::put('/{user}', [CentreController::class, 'effectuerPaiement']);
    });
    
    Route::prefix('salaires')->group(function () {
        Route::get('/valeurs', [CentreController::class, 'getValeurSalaires']);
        Route::put('/valeurs', [CentreController::class, 'updateValSalaire']);
        Route::put('/{user}', [CentreController::class, 'effectuerSalaire']);
        Route::get('/calculs-provisoires', [CentreController::class, 'ModificationTotalProvisoir']);
    });
    
    // ============================================================================
    // ROUTES API INSCRIPTIONS
    // ============================================================================
    
    Route::prefix('inscriptions')->group(function () {
        Route::put('/change/{id_etudiant}/{id_matiere}/{type}', [CentreController::class, 'setInscription']);
    });
    
    // ============================================================================
    // ROUTES API ENSEIGNEMENTS
    // ============================================================================
    
    Route::prefix('enseignements')->group(function () {
        Route::post('/', [ProfesseurController::class, 'createEnseignement']);
    });
});

// Route par défaut pour les requêtes API non trouvées
Route::fallback(function () {
    return response()->json([
        'message' => 'Route API non trouvée.',
        'status' => 404
    ], 404);
});
