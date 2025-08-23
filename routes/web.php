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
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\EnseignementController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\SalaireController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\ComponentExampleController;

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

// Routes d'authentification
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [LoginController::class, 'reset'])->name('password.update');
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
    
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        
        // Gestion des utilisateurs
        Route::resource('users', UserController::class);
        Route::get('/api/users', [UserController::class, 'apiIndex'])->name('users.api.index');
        Route::get('/api/users/stats', [UserController::class, 'apiStats'])->name('users.api.stats');
        
        // Gestion des élèves
        Route::resource('eleves', \App\Http\Controllers\EleveController::class);
        
        // Gestion des professeurs
        Route::resource('professeurs', \App\Http\Controllers\ProfesseurController::class);
        
        // Gestion des assistants
        Route::resource('assistants', \App\Http\Controllers\AssistantController::class);
        
        // Gestion des matières
        Route::resource('matieres', MatiereController::class);
        Route::get('/api/matieres/stats', [MatiereController::class, 'stats'])->name('matieres.api.stats');
        
        // Gestion des filières
        Route::resource('filieres', FiliereController::class);
        
        // Gestion des niveaux
        Route::resource('niveaux', NiveauController::class);
        
        // Gestion des inscriptions
        Route::resource('inscriptions', \App\Http\Controllers\InscriptionController::class);
        
        // Gestion des absences
        Route::resource('absences', AbsenceController::class);
        
        // Gestion des paiements
        Route::resource('paiements', PaiementController::class);
        
        // Gestion des salaires
        Route::resource('salaires', SalaireController::class);
        Route::post('/salaires/generer', [SalaireController::class, 'generer'])->name('salaires.generer');
        Route::post('/salaires/{salaire}/valider', [SalaireController::class, 'valider'])->name('salaires.valider');
        
        // Rapports et statistiques
        Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
        Route::get('/statistiques', [RapportController::class, 'statistiques'])->name('statistiques');
        Route::get('/api/filieres/stats', [FiliereController::class, 'stats'])->name('filieres.api.stats');
        
        // Gestion des niveaux
        Route::resource('niveaux', NiveauController::class);
        Route::get('/api/niveaux/stats', [NiveauController::class, 'stats'])->name('niveaux.api.stats');
        
        // Gestion des enseignements
        Route::resource('enseignements', EnseignementController::class);
        Route::get('/api/enseignements/stats', [EnseignementController::class, 'stats'])->name('enseignements.api.stats');
        
        // Gestion des salaires
        Route::resource('salaires', SalaireController::class);
        Route::post('/salaires/{salaire}/payer', [SalaireController::class, 'payer'])->name('salaires.payer');
        Route::post('/salaires/calculer-automatiquement', [SalaireController::class, 'calculerAutomatiquement'])->name('salaires.calculer-automatiquement');
        Route::get('/salaires/rapport', [SalaireController::class, 'genererRapport'])->name('salaires.rapport');
        
        // Gestion des notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
            Route::get('/statistiques', [NotificationController::class, 'statistiques'])->name('statistiques');
            Route::post('/absence/{absence}', [NotificationController::class, 'notifierAbsence'])->name('absence');
            Route::post('/paiement/{paiement}', [NotificationController::class, 'notifierPaiement'])->name('paiement');
            Route::post('/rappels-paiement', [NotificationController::class, 'envoyerRappelsPaiement'])->name('rappels-paiement');
            Route::post('/systeme', [NotificationController::class, 'envoyerNotificationSysteme'])->name('systeme');
            Route::get('/tester-configuration', [NotificationController::class, 'testerConfiguration'])->name('tester-configuration');
        });
        
        // Gestion des rapports
        Route::prefix('rapports')->name('rapports.')->group(function () {
            Route::get('/', [RapportController::class, 'index'])->name('index');
            Route::get('/absences', [RapportController::class, 'absences'])->name('absences');
            Route::get('/paiements', [RapportController::class, 'paiements'])->name('paiements');
            Route::get('/salaires', [RapportController::class, 'salaires'])->name('salaires');
            
            // Export PDF des rapports
            Route::get('/global/export', [RapportController::class, 'exportGlobal'])->name('export-global');
            Route::get('/absences/export', [RapportController::class, 'exportAbsences'])->name('export-absences');
            Route::get('/paiements/export', [RapportController::class, 'exportPaiements'])->name('export-paiements');
            Route::get('/salaires/export', [RapportController::class, 'exportSalaires'])->name('export-salaires');
            Route::get('/salaires/{salaire}/bulletin', [RapportController::class, 'exportSalarySlip'])->name('export-salary-slip');
        });
    });
    
    // ============================================================================
    // ROUTES PROFESSEUR
    // ============================================================================
    
    Route::middleware('role:professeur')->prefix('professeur')->name('professeur.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [DashboardController::class, 'professeur'])->name('dashboard');
        
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
        
        // Gestion des absences
        Route::resource('absences', \App\Http\Controllers\Professeur\AbsenceController::class);
        
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
    });
    
    // ============================================================================
    // ROUTES ASSISTANT
    // ============================================================================
    
    Route::middleware('role:assistant')->prefix('assistant')->name('assistant.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [DashboardController::class, 'assistant'])->name('dashboard');
        
        // Gestion des élèves
        Route::get('/eleves', function () {
            return Inertia::render('Assistant/Eleves');
        })->name('eleves');
        
        // Gestion des inscriptions
        Route::get('/inscriptions', function () {
            return Inertia::render('Assistant/Inscriptions');
        })->name('inscriptions');
        
        // Gestion des absences
        Route::get('/absences', function () {
            return Inertia::render('Assistant/Absences');
        })->name('absences');
        
        // Messagerie
        Route::get('/messagerie', function () {
            return Inertia::render('Assistant/Messagerie');
        })->name('messagerie');
        
        // Annonces
        Route::get('/annonces', function () {
            return Inertia::render('Assistant/Annonces');
        })->name('annonces');
        
        // Événements
        Route::get('/evenements', function () {
            return Inertia::render('Assistant/Evenements');
        })->name('evenements');
        
        // Documents
        Route::get('/documents', function () {
            return Inertia::render('Assistant/Documents');
        })->name('documents');
        
        // Ressources partagées
        Route::get('/ressources', function () {
            return Inertia::render('Assistant/Ressources');
        })->name('ressources');
        
        // Profil
        Route::get('/profil', function () {
            return Inertia::render('Assistant/Profil');
        })->name('profil');
        
        // Paramètres
        Route::get('/parametres', function () {
            return Inertia::render('Assistant/Parametres');
        })->name('parametres');
    });
    
    // ============================================================================
    // ROUTES ÉLÈVE
    // ============================================================================
    
    Route::middleware('role:eleve')->prefix('eleve')->name('eleve.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [DashboardController::class, 'eleve'])->name('dashboard');
        
        // Emploi du temps
        Route::get('/emploi-du-temps', function () {
            return Inertia::render('Eleve/EmploiDuTemps');
        })->name('emploi-du-temps');
        
        // Matières suivies
        Route::get('/matieres', function () {
            return Inertia::render('Eleve/MesMatieres');
        })->name('matieres');
        
        // Notes
        Route::get('/notes', function () {
            return Inertia::render('Eleve/MesNotes');
        })->name('notes');
        
        // Absences
        Route::get('/absences', function () {
            return Inertia::render('Eleve/MesAbsences');
        })->name('absences');
        
        // Devoirs
        Route::get('/devoirs', function () {
            return Inertia::render('Eleve/MesDevoirs');
        })->name('devoirs');
        
        // Cours en ligne
        Route::get('/cours', function () {
            return Inertia::render('Eleve/CoursEnLigne');
        })->name('cours');
        
        // Ressources
        Route::get('/ressources', function () {
            return Inertia::render('Eleve/Ressources');
        })->name('ressources');
        
        // Bulletins
        Route::get('/bulletins', function () {
            return Inertia::render('Eleve/MesBulletins');
        })->name('bulletins');
        
        // Événements
        Route::get('/evenements', function () {
            return Inertia::render('Eleve/Evenements');
        })->name('evenements');
        
        // Messagerie
        Route::get('/messagerie', function () {
            return Inertia::render('Eleve/Messagerie');
        })->name('messagerie');
        
        // Profil
        Route::get('/profil', function () {
            return Inertia::render('Eleve/Profil');
        })->name('profil');
        
        // Paramètres
        Route::get('/parametres', function () {
            return Inertia::render('Eleve/Parametres');
        })->name('parametres');
    });
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