<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CentreController;
use App\Http\Controllers\ProfesseurController;
use App\Http\Controllers\Api\SalaireController;
use App\Http\Controllers\Api\ConfigurationSalaireController;
use App\Http\Controllers\Api\MatiereController;

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

// Routes API publiques (si nécessaires)
Route::middleware('api')->group(function () {
    // Liste des packs actifs (accessible sans authentification)
    Route::get('/packs/actifs', [\App\Http\Controllers\Api\PackController::class, 'index']);
    
    // Détails d'un pack (accessible sans authentification)
    Route::get('/packs/{pack}', [\App\Http\Controllers\Api\PackController::class, 'show']);
});

// Routes API protégées par authentification
Route::middleware(['auth:sanctum', 'active'])->group(function () {
    // ============================================================================
    // ROUTES API GESTION DES PACKS
    // ============================================================================
    Route::prefix('packs')->group(function () {
        // Liste des packs avec filtres et pagination
        Route::get('/', [\App\Http\Controllers\Api\PackController::class, 'index']);
        
        // Statistiques des packs
        Route::get('/stats', [\App\Http\Controllers\Api\PackController::class, 'stats']);
        
        // Détails d'un pack
        Route::get('/{pack}', [\App\Http\Controllers\Api\PackController::class, 'show']);
    });

    // ============================================================================
    // ROUTES API GESTION DES MATIÈRES
    // ============================================================================
    Route::prefix('matieres')->group(function () {
        // Liste des matières avec filtres et pagination
        Route::get('/', [MatiereController::class, 'index']);
        
        // Statistiques des matières
        Route::get('/stats', [MatiereController::class, 'stats']);
        
        // Détails d'une matière
        Route::get('/{matiere}', [MatiereController::class, 'show']);
        
        // Création d'une matière
        Route::post('/', [MatiereController::class, 'store']);
        
        // Mise à jour d'une matière
        Route::put('/{matiere}', [MatiereController::class, 'update']);
        
        // Suppression d'une matière
        Route::delete('/{matiere}', [MatiereController::class, 'destroy']);
    });
    
    // ============================================================================
    // ============================================================================
    // ROUTES API GESTION DES SALAIRES
    // ============================================================================
    
    Route::prefix('salaires')->group(function () {
        // Liste des salaires avec filtres
        Route::get('/', [\App\Http\Controllers\Api\SalaireApiController::class, 'index']);
        
        // Détails d'un salaire
        Route::get('/{id}', [\App\Http\Controllers\Api\SalaireApiController::class, 'show']);
        
        // Calculer les salaires pour un mois donné
        Route::post('/calculer', [\App\Http\Controllers\Api\SalaireApiController::class, 'calculerSalaires']);
        
        // Valider le paiement d'un salaire
        Route::post('/{id}/valider', [\App\Http\Controllers\Api\SalaireApiController::class, 'validerPaiement']);
        
        // Annuler un salaire
        Route::post('/{id}/annuler', [\App\Http\Controllers\Api\SalaireApiController::class, 'annulerSalaire']);
        
        // Gestion des configurations de salaires
        Route::prefix('configurations')->group(function () {
            // Liste des configurations
            Route::get('/', [\App\Http\Controllers\Api\SalaireApiController::class, 'configurations']);
            
            // Mise à jour d'une configuration
            Route::put('/{id}', [\App\Http\Controllers\Api\SalaireApiController::class, 'updateConfiguration']);
        });
    });
    
    // ============================================================================
    // ROUTES API UTILISATEURS
    // ============================================================================
    
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
    // ROUTES API GESTION DES SALAIRES
    // ============================================================================
    
    Route::prefix('salaires')->group(function () {
        // Calculer le salaire pour une matière spécifique (admin/assistant)
        Route::post('/calculer', [SalaireController::class, 'calculerSalaireMatiere']);
        
        // Calculer tous les salaires pour un mois donné (admin/assistant)
        Route::post('/calculer-tous', [SalaireController::class, 'calculerTousLesSalaires']);
        
        // Lister les salaires avec filtres (admin/assistant)
        Route::get('/', [SalaireController::class, 'listerSalaires']);
        
        // Valider un paiement de salaire (admin/assistant)
        Route::post('/{id}/valider', [SalaireController::class, 'validerPaiement']);
        
        // Récupérer les salaires de l'utilisateur connecté (pour les professeurs)
        Route::get('/mes-salaires', [SalaireController::class, 'mesSalaires']);
    });
    
    // ============================================================================
    // ROUTES API CONFIGURATION DES SALAIRES
    // ============================================================================
    
    Route::prefix('configuration-salaires')->group(function () {
        // Lister toutes les configurations
        Route::get('/', [ConfigurationSalaireController::class, 'index']);
        
        // Afficher une configuration spécifique
        Route::get('/{id}', [ConfigurationSalaireController::class, 'show']);
        
        // Créer une nouvelle configuration
        Route::post('/', [ConfigurationSalaireController::class, 'store']);
        
        // Mettre à jour une configuration existante
        Route::put('/{id}', [ConfigurationSalaireController::class, 'update']);
        
        // Supprimer une configuration
        Route::delete('/{id}', [ConfigurationSalaireController::class, 'destroy']);
        
        // Récupérer les matières non configurées
        Route::get('/matieres/non-configurées', [ConfigurationSalaireController::class, 'matieresNonConfigurees']);
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

    // ============================================================================
    // ROUTES API GESTION DES PAIEMENTS
    // ============================================================================
    
    Route::prefix('paiements')->group(function () {
        // Récupérer les informations d'un pack
        Route::get('/packs/{id}', [\App\Http\Controllers\Api\PaiementApiController::class, 'getPackInfo']);
        
        // Récupérer les tarifs avec filtres
        Route::get('/tarifs', [\App\Http\Controllers\Api\PaiementApiController::class, 'getTarifs']);
        
        // Vérifier l'existence d'un paiement
        Route::get('/check-existing', [\App\Http\Controllers\Api\PaiementApiController::class, 'checkExistingPaiement']);
        
        // Récupérer l'historique des paiements d'un étudiant
        Route::get('/etudiant/{etudiantId}', [\App\Http\Controllers\Api\PaiementApiController::class, 'getStudentPayments']);
        
        // Exporter les paiements
        Route::get('/export', [\App\Http\Controllers\Api\PaiementApiController::class, 'export']);
        
        // Statistiques des paiements
        Route::get('/stats', [\App\Http\Controllers\Api\PaiementApiController::class, 'getStats']);
    });
});

// Route par défaut pour les requêtes API non trouvées
Route::fallback(function () {
        return response()->json([
        'message' => 'Route API non trouvée.',
        'status' => 404
    ], 404);
});
