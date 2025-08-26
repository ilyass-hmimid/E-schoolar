<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CentreController;
use App\Http\Controllers\ProfesseurController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Profile\AvatarController;
use App\Http\Controllers\EnseignementController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\SalaireController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\ComponentExampleController;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| Routes Web - Allo Tawjih
|--------------------------------------------------------------------------
| Routes pour la plateforme de gestion des centres de cours
|--------------------------------------------------------------------------
*/

// ============================================================================
// ROUTES PUBLIQUES
// ============================================================================

// Page d'accueil
Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');

// Exemples de composants
Route::get('/composants', [ComponentExampleController::class, 'index'])->name('components.examples');

// Déconnexion complète
Route::get('/logout-complete', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/')->with('status', 'Déconnexion effectuée');
});

// Routes accessibles aux invités
Route::middleware('guest')->group(function () {
    Route::get('/test/auth', [TestController::class, 'testAuth'])->name('test.auth');
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [LoginController::class, 'reset'])->name('password.update');
});

// Routes protégées par authentification
Route::middleware('auth')->group(function () {
    // Routes de test protégées par rôles
    Route::get('/test/role', [TestController::class, 'testRole'])->middleware('role:admin')->name('test.role');
    Route::get('/test/admin', [TestController::class, 'testAdmin'])->middleware('role:admin')->name('test.admin');
    Route::get('/test/professor', [TestController::class, 'testProfessor'])->middleware('role:professeur')->name('test.professor');
    Route::get('/test/assistant', [TestController::class, 'testAssistant'])->middleware('role:assistant')->name('test.assistant');
    Route::get('/test/student', [TestController::class, 'testStudent'])->middleware('role:eleve')->name('test.student');
    Route::get('/test/spatie-roles', [TestController::class, 'testSpatieRoles'])->name('test.spatie-roles');
});

// ============================================================================
// ROUTES AUTHENTIFIÉES
// ============================================================================

Route::middleware(['auth', 'active'])->group(function () {
    
    // Déconnexion
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Redirection pour les utilisateurs authentifiés
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');
    
    // Route du dashboard principal (redirige selon le rôle)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Routes du profil utilisateur
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::patch('/avatar', [AvatarController::class, 'update'])->name('avatar');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
    
    // ============================================================================
    // ROUTES ADMIN (Super utilisateur)
    // ============================================================================
    
    // Inclure les routes administratives
    require __DIR__.'/admin.php';
    
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des packs
        Route::prefix('packs')->name('packs.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PackController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\PackController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\PackController::class, 'store'])->name('store');
            Route::get('/{pack}', [\App\Http\Controllers\Admin\PackController::class, 'show'])->name('show');
            Route::get('/{pack}/edit', [\App\Http\Controllers\Admin\PackController::class, 'edit'])->name('edit');
            Route::put('/{pack}', [\App\Http\Controllers\Admin\PackController::class, 'update'])->name('update');
            Route::delete('/{pack}', [\App\Http\Controllers\Admin\PackController::class, 'destroy'])->name('destroy');
            
            // Actions supplémentaires
            Route::post('/{pack}/toggle-status', [\App\Http\Controllers\Admin\PackController::class, 'toggleStatus'])
                ->name('toggle-status');
            Route::post('/{pack}/toggle-popularity', [\App\Http\Controllers\Admin\PackController::class, 'togglePopularity'])
                ->name('toggle-popularity');
            Route::get('/{pack}/duplicate', [\App\Http\Controllers\Admin\PackController::class, 'duplicate'])
                ->name('duplicate');
        });

        // Gestion des utilisateurs
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::get('/api/users', [\App\Http\Controllers\UserController::class, 'apiIndex'])->name('users.api.index');
        Route::get('/api/users/stats', [\App\Http\Controllers\UserController::class, 'apiStats'])->name('users.api.stats');
        
        // Gestion des élèves
        Route::resource('eleves', \App\Http\Controllers\EleveController::class);
        
        // Gestion des professeurs
        Route::resource('professeurs', \App\Http\Controllers\Admin\ProfesseurController::class);
        
        // Gestion des assistants
        Route::resource('assistants', \App\Http\Controllers\Admin\AssistantController::class);
        
        // Gestion des inscriptions
        Route::resource('inscriptions', \App\Http\Controllers\Admin\InscriptionController::class);
        
        // Gestion des absences
        Route::resource('absences', \App\Http\Controllers\AbsenceController::class);
        
        // Gestion des paiements
        Route::resource('paiements', \App\Http\Controllers\PaiementController::class);
        Route::post('/paiements/{paiement}/validate', [\App\Http\Controllers\PaiementController::class, 'validatePaiement'])->name('paiements.validate');
        Route::get('/paiements/{paiement}/receipt', [\App\Http\Controllers\PaiementController::class, 'downloadReceipt'])->name('paiements.receipt');
        
        // Gestion des salaires
        Route::prefix('salaires')->name('salaires.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SalaireController::class, 'index'])->name('index');
            Route::get('/calculer', [\App\Http\Controllers\SalaireController::class, 'calculer'])->name('calculer');
            Route::post('/calculer', [\App\Http\Controllers\SalaireController::class, 'calculerSalaires'])->name('calculer.store');
            Route::get('/{salaire}', [\App\Http\Controllers\SalaireController::class, 'show'])->name('show');
            Route::get('/{salaire}/edit', [\App\Http\Controllers\SalaireController::class, 'edit'])->name('edit');
            Route::put('/{salaire}', [\App\Http\Controllers\SalaireController::class, 'update'])->name('update');
            Route::post('/{salaire}/valider', [\App\Http\Controllers\SalaireController::class, 'validerPaiement'])->name('valider');
            Route::post('/{salaire}/annuler', [\App\Http\Controllers\SalaireController::class, 'annuler'])->name('annuler');
            Route::get('/export/excel', [\App\Http\Controllers\SalaireController::class, 'exportExcel'])->name('export.excel');
            Route::get('/export/pdf', [\App\Http\Controllers\SalaireController::class, 'exportPdf'])->name('export.pdf');
            Route::post('/generer', [\App\Http\Controllers\SalaireController::class, 'generer'])->name('generer');
            
            // Configuration des salaires
            Route::prefix('configuration')->name('configuration.')->group(function () {
                Route::get('/', [\App\Http\Controllers\SalaireController::class, 'configuration'])->name('index');
                Route::post('/', [\App\Http\Controllers\SalaireController::class, 'storeConfiguration'])->name('store');
                Route::put('/{configuration}', [\App\Http\Controllers\SalaireController::class, 'updateConfiguration'])->name('update');
                Route::delete('/{configuration}', [\App\Http\Controllers\SalaireController::class, 'destroyConfiguration'])->name('destroy');
                Route::get('/matiere/{matiereId}', [\App\Http\Controllers\SalaireController::class, 'getConfigurationByMatiere'])->name('matiere');
            });
        });
        
        // Rapports et statistiques
        Route::get('/rapports', [\App\Http\Controllers\RapportController::class, 'index'])->name('rapports.index');
        Route::get('/statistiques', [\App\Http\Controllers\RapportController::class, 'statistiques'])->name('statistiques');
        Route::get('/api/filieres/stats', [\App\Http\Controllers\Admin\FiliereController::class, 'stats'])->name('filieres.api.stats');
        
        // Gestion des niveaux
        Route::resource('niveaux', \App\Http\Controllers\Admin\NiveauController::class);
        Route::get('/api/niveaux/stats', [\App\Http\Controllers\Admin\NiveauController::class, 'stats'])->name('niveaux.api.stats');
        
        // Gestion des enseignements
        Route::resource('enseignements', \App\Http\Controllers\EnseignementController::class);
        Route::get('/api/enseignements/stats', [\App\Http\Controllers\EnseignementController::class, 'stats'])->name('enseignements.api.stats');
        
        // Gestion des salaires
        Route::resource('salaires', \App\Http\Controllers\SalaireController::class);
        Route::post('/salaires/{salaire}/payer', [\App\Http\Controllers\SalaireController::class, 'payer'])->name('salaires.payer');
        Route::post('/salaires/calculer-automatiquement', [\App\Http\Controllers\SalaireController::class, 'calculerAutomatiquement'])->name('salaires.calculer-automatiquement');
        Route::get('/salaires/rapport', [\App\Http\Controllers\SalaireController::class, 'genererRapport'])->name('salaires.rapport');
        
        // Gestion des notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
            Route::post('/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
            Route::get('/statistiques', [\App\Http\Controllers\NotificationController::class, 'statistiques'])->name('statistiques');
            Route::post('/absence/{absence}', [\App\Http\Controllers\NotificationController::class, 'notifierAbsence'])->name('absence');
            Route::post('/paiement/{paiement}', [\App\Http\Controllers\NotificationController::class, 'notifierPaiement'])->name('paiement');
            Route::post('/rappels-paiement', [\App\Http\Controllers\NotificationController::class, 'envoyerRappelsPaiement'])->name('rappels-paiement');
            Route::post('/systeme', [\App\Http\Controllers\NotificationController::class, 'envoyerNotificationSysteme'])->name('systeme');
            Route::get('/tester-configuration', [\App\Http\Controllers\NotificationController::class, 'testerConfiguration'])->name('tester-configuration');
        });
        
        // Gestion des rapports
        Route::prefix('rapports')->name('rapports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\RapportController::class, 'index'])->name('index');
            
            // Rapports d'absences
            Route::prefix('absences')->name('absences.')->group(function () {
                Route::get('/', [\App\Http\Controllers\RapportController::class, 'absences'])->name('index');
                Route::get('/export', [\App\Http\Controllers\RapportController::class, 'exportAbsences'])->name('export');
            });
            
            // Rapports de paiements
            Route::prefix('paiements')->name('paiements.')->group(function () {
                Route::get('/', [\App\Http\Controllers\RapportController::class, 'paiements'])->name('index');
                Route::get('/export', [\App\Http\Controllers\RapportController::class, 'exportPaiements'])->name('export');
            });
            
            // Rapports d'effectifs
            Route::prefix('effectifs')->name('effectifs.')->group(function () {
                Route::get('/', [\App\Http\Controllers\RapportController::class, 'effectifs'])->name('index');
                Route::get('/export', [\App\Http\Controllers\RapportController::class, 'exportEffectifs'])->name('export');
            });
            
            // Rapports financiers
            Route::prefix('financiers')->name('financiers.')->group(function () {
                Route::get('/', [\App\Http\Controllers\RapportController::class, 'financiers'])->name('index');
                Route::get('/export', [\App\Http\Controllers\RapportController::class, 'exportFinanciers'])->name('export');
            });
        });
    
    }); // Fin du groupe admin
    
    // ============================================================================
    // ROUTES PROFESSEUR
    // ============================================================================
    
    Route::middleware('role:professeur')->prefix('professeur')->name('professeur.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'professeur'])->name('dashboard');
        
        // Emploi du temps
        Route::get('/emploi-du-temps', function () {
            return Inertia::render('Professeur/EmploiDuTemps');
        })->name('emploi-du-temps');
        
        // Matières enseignées
        Route::get('/matieres', function () {
            return Inertia::render('Professeur/MesMatieres');
        })->name('matieres');
        
        // Classes assignées
        Route::get('/classes', function () {
            return Inertia::render('Professeur/MesClasses');
        })->name('classes');
        
        // Gestion des notes
        Route::resource('notes', \App\Http\Controllers\Professeur\NoteController::class);
        
        // Routes API pour la gestion des notes
        Route::prefix('api')->group(function () {
            // Récupérer les étudiants d'une classe pour une matière
            Route::get('/classes/{classe}/matieres/{matiere}/etudiants', [\App\Http\Controllers\Professeur\NoteController::class, 'getEtudiantsByClasseMatiere'])
                ->name('api.etudiants.by-classe-matiere');
                
            // Récupérer les notes d'un étudiant pour une matière
            Route::get('/etudiants/{etudiant}/matieres/{matiere}/notes', [\App\Http\Controllers\Professeur\NoteController::class, 'getNotesEtudiantMatiere'])
                ->name('api.notes.etudiant-matiere');
        });
        
        // Gestion des absences
        Route::resource('absences', \App\Http\Controllers\Professeur\AbsenceController::class);
        
        // Gestion des présences
        Route::resource('presences', \App\Http\Controllers\Professeur\PresenceController::class);
        
        // Routes API pour la gestion des présences
        Route::prefix('api')->group(function () {
            // Récupérer les étudiants d'une classe pour une matière
            Route::get('/etudiants/by-classe-matiere', [\App\Http\Controllers\Professeur\PresenceController::class, 'getEtudiantsByClasseMatiere'])
                ->name('api.etudiants.by-classe-matiere');
                
            // Marquer les présences pour une séance
            Route::post('/presences/marquer', [\App\Http\Controllers\Professeur\PresenceController::class, 'marquerPresences'])
                ->name('api.presences.marquer');
                
            // Récupérer les statistiques de présence
            Route::get('/presences/statistiques', [\App\Http\Controllers\Professeur\PresenceController::class, 'getStatistiques'])
                ->name('api.presences.statistiques');
        });
        
        // Bulletins
        Route::get('/bulletins', function () {
            return Inertia::render('Professeur/Bulletins');
        })->name('bulletins');
        
        // Dépôt de cours
        Route::resource('cours', \App\Http\Controllers\Professeur\CoursController::class);
        
        // Gestion des devoirs
        Route::resource('devoirs', \App\Http\Controllers\Professeur\DevoirController::class);
        
        // Ressources partagées
        Route::resource('ressources', \App\Http\Controllers\Professeur\RessourceController::class);
        
        // Profil
        Route::get('/profil', [\App\Http\Controllers\Professeur\ProfilController::class, 'edit'])->name('profil.edit');
        Route::put('/profil', [\App\Http\Controllers\Professeur\ProfilController::class, 'update'])->name('profil.update');
        
        // Gestion des salaires
        Route::get('/salaires', [\App\Http\Controllers\Professeur\SalaireController::class, 'index'])->name('salaires.index');
        Route::get('/salaires/{salaire}', [\App\Http\Controllers\Professeur\SalaireController::class, 'show'])->name('salaires.show');
    }); // Fin du groupe professeur
    
    // ============================================================================
    // ROUTES ASSISTANT
    // ============================================================================
    
    require __DIR__.'/assistant.php';
}); // Fin du groupe auth active

// Routes de test (à supprimer en production)
Route::prefix('test')->group(function () {
    Route::get('/salaires/calculer/{mois?}', [\App\Http\Controllers\TestSalaireController::class, 'testCalculerSalaires'])->name('test.salaires.calculer');
    Route::post('/salaires/{salaire}/valider', [\App\Http\Controllers\TestSalaireController::class, 'testValiderPaiement'])->name('test.salaires.valider');
    Route::post('/salaires/{salaire}/annuler', [\App\Http\Controllers\TestSalaireController::class, 'testAnnulerSalaire'])->name('test.salaires.annuler');
});

// ============================================================================
// ROUTE CATCH-ALL POUR INERTIA.JS (doit être en dernier et seulement pour les routes non définies)
// ============================================================================

Route::fallback(function () {
    if (request()->is('api/*') || request()->is('_debugbar/*')) {
        abort(404);
    }
    
    // Si la route n'existe pas, on renvoie la vue d'erreur 404
    return Inertia::render('Errors/404', [
        'status' => 404,
    ])->toResponse(request())->setStatusCode(404);
});