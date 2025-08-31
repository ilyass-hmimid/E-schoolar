<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Contrôleurs principaux
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\DashboardController;

// Contrôleurs Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CentreController as AdminCentreController;
use App\Http\Controllers\Admin\ProfesseurController as AdminProfesseurController;
use App\Http\Controllers\Admin\EleveController as AdminEleveController;
use App\Http\Controllers\Admin\PaiementController as AdminPaiementController;
use App\Http\Controllers\Admin\EnseignementController as AdminEnseignementController;
use App\Http\Controllers\Admin\AbsenceController as AdminAbsenceController;
use App\Http\Controllers\Admin\SalaireController as AdminSalaireController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\RapportController as AdminRapportController;

// Contrôleurs Professeur
use App\Http\Controllers\Professeur\DashboardController as ProfesseurDashboardController;
use App\Http\Controllers\Professeur\ClasseController as ProfesseurClasseController;
use App\Http\Controllers\Professeur\NoteController as ProfesseurNoteController;
use App\Http\Controllers\Professeur\AbsenceController as ProfesseurAbsenceController;

// Contrôleurs Assistant
use App\Http\Controllers\Assistant\DashboardController as AssistantDashboardController;
use App\Http\Controllers\Assistant\AbsenceController as AssistantAbsenceController;
use App\Http\Controllers\Assistant\EleveController as AssistantEleveController;
use App\Http\Controllers\Assistant\ClasseController as AssistantClasseController;

// Contrôleurs Élève
use App\Http\Controllers\Eleve\DashboardController as EleveDashboardController;
use App\Http\Controllers\Eleve\ProfilController as EleveProfilController;
use App\Http\Controllers\Eleve\CoursController as EleveCoursController;
use App\Http\Controllers\Eleve\NoteController as EleveNoteController;
use App\Http\Controllers\Eleve\DevoirController as EleveDevoirController;
use App\Http\Controllers\Eleve\AbsenceController as EleveAbsenceController;
use App\Http\Controllers\Eleve\PaiementController as ElevePaiementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authentification
Auth::routes(['verify' => true]);

/*
|--------------------------------------------------------------------------
| Routes Protégées
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'active'])->group(function () {
    // Tableau de bord selon le rôle
    Route::get('/dashboard', function () {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->hasRole('professeur')) {
            return redirect()->route('professeur.dashboard');
        } elseif (auth()->user()->hasRole('assistant')) {
            return redirect()->route('assistant.dashboard');
        } else {
            return redirect()->route('eleve.dashboard');
        }
    })->name('dashboard');

    // Routes du profil utilisateur
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::patch('/avatar', [AvatarController::class, 'update'])->name('avatar');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes Administrateur
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des utilisateurs
        Route::resource('users', AdminUserController::class);
        Route::post('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/import', [AdminUserController::class, 'import'])->name('users.import');
        Route::get('users/export', [AdminUserController::class, 'export'])->name('users.export');
    });

    });

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/

// Page d'aide
Route::get('/aide', function () {
    return view('help.index');
})->name('aide');

// Page de contact
Route::get('/contact', function () {
    return view('support.contact');
})->name('contact');

// Contact du support
Route::get('/support/contact', function () {
    return view('support.contact');
})->name('support.contact');

// Exemples de composants
Route::get('/composants', [ComponentExampleController::class, 'index'])->name('components.examples');

// Routes d'authentification de base
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Email verification
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Routes accessibles aux invités
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [LoginController::class, 'reset'])->name('password.update');
});

// Routes protégées par authentification et rôle
// Inclure les routes d'administration des utilisateurs
require __DIR__.'/admin_users.php';

Route::middleware(['auth', 'active'])->group(function () {
    // Redirection pour les utilisateurs authentifiés
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');
    
    // Route de tableau de bord principal (redirige vers le bon tableau de bord selon le rôle)
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Routes du profil utilisateur
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::patch('/avatar', [AvatarController::class, 'update'])->name('avatar');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // ============================================================================
    // ROUTES ADMIN (Rôle: admin)
    // ============================================================================
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des utilisateurs
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::post('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/import', [\App\Http\Controllers\Admin\UserController::class, 'import'])->name('users.import');
        Route::get('users/export', [\App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');
        
        // Gestion des centres
        Route::resource('centres', \App\Http\Controllers\Admin\CentreController::class);
        Route::post('centres/{centre}/toggle-status', [\App\Http\Controllers\Admin\CentreController::class, 'toggleStatus'])->name('centres.toggle-status');
        Route::post('centres/import', [\App\Http\Controllers\Admin\CentreController::class, 'import'])->name('centres.import');
        Route::get('centres/export', [\App\Http\Controllers\Admin\CentreController::class, 'export'])->name('centres.export');
        
        // Gestion des professeurs
        Route::resource('professeurs', \App\Http\Controllers\Admin\ProfesseurController::class);
        Route::post('professeurs/{professeur}/toggle-status', [\App\Http\Controllers\Admin\ProfesseurController::class, 'toggleStatus'])->name('professeurs.toggle-status');
        Route::post('professeurs/import', [\App\Http\Controllers\Admin\ProfesseurController::class, 'import'])->name('professeurs.import');
        Route::get('professeurs/export', [\App\Http\Controllers\Admin\ProfesseurController::class, 'export'])->name('professeurs.export');
        
        // Gestion des élèves
        Route::resource('eleves', \App\Http\Controllers\Admin\EleveController::class);
        Route::post('eleves/{eleve}/toggle-status', [\App\Http\Controllers\Admin\EleveController::class, 'toggleStatus'])->name('eleves.toggle-status');
        Route::post('eleves/import', [\App\Http\Controllers\Admin\EleveController::class, 'import'])->name('eleves.import');
        Route::get('eleves/export', [\App\Http\Controllers\Admin\EleveController::class, 'export'])->name('eleves.export');
        
        // Gestion des paiements
        Route::resource('paiements', \App\Http\Controllers\Admin\PaiementController::class);
        Route::post('paiements/import', [\App\Http\Controllers\Admin\PaiementController::class, 'import'])->name('paiements.import');
        Route::get('paiements/export', [\App\Http\Controllers\Admin\PaiementController::class, 'export'])->name('paiements.export');
        
        // Gestion des enseignements
        Route::resource('enseignements', \App\Http\Controllers\Admin\EnseignementController::class);
        Route::post('enseignements/{enseignement}/toggle-status', [\App\Http\Controllers\Admin\EnseignementController::class, 'toggleStatus'])->name('enseignements.toggle-status');
        
        // Gestion des absences
        Route::resource('absences', \App\Http\Controllers\Admin\AbsenceController::class);
        
        // Gestion des salaires
        Route::resource('salaires', \App\Http\Controllers\Admin\SalaireController::class);
        
        // Gestion des notifications
        Route::resource('notifications', \App\Http\Controllers\Admin\NotificationController::class);
        
        // Gestion des rapports
        Route::prefix('rapports')->name('rapports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\RapportController::class, 'index'])->name('index');
            Route::get('generer', [\App\Http\Controllers\Admin\RapportController::class, 'generer'])->name('generer');
            Route::get('telecharger', [\App\Http\Controllers\Admin\RapportController::class, 'telecharger'])->name('telecharger');
            Route::get('export', [\App\Http\Controllers\Admin\RapportController::class, 'export'])->name('export');
        });
        
        // Gestion des configurations de salaires
        Route::prefix('salaires')->name('salaires.')->group(function () {
            Route::put('{configuration}', [\App\Http\Controllers\Admin\SalaireController::class, 'updateConfiguration'])->name('update-configuration');
            Route::delete('{configuration}', [\App\Http\Controllers\Admin\SalaireController::class, 'destroyConfiguration'])->name('destroy-configuration');
        });
    });

    // ============================================================================
    // ROUTES PROFESSEUR (Rôle: professeur)
    // ============================================================================
    Route::middleware(['role:professeur'])->prefix('professeur')->name('professeur.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Professeur\DashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des classes
        Route::resource('classes', \App\Http\Controllers\Professeur\ClasseController::class);
        Route::get('classes/{classe}/export', [\App\Http\Controllers\Professeur\ClasseController::class, 'export'])
            ->name('classes.export');
        Route::post('classes/{classe}/eleves', [\App\Http\Controllers\Professeur\ClasseController::class, 'addEleve'])
            ->name('classes.eleves.add');
        Route::delete('classes/{classe}/eleves/{eleve}', [\App\Http\Controllers\Professeur\ClasseController::class, 'removeEleve'])
            ->name('classes.eleves.remove');
        
        // Gestion des notes
        Route::resource('notes', \App\Http\Controllers\Professeur\NoteController::class);
        
        // Calculer la moyenne d'un étudiant dans une matière
        Route::get('/notes/etudiant/{etudiantId}/matiere/{matiereId}/moyenne', 
            [\App\Http\Controllers\Professeur\NoteController::class, 'calculerMoyenne']
        )->name('notes.calculer-moyenne');
        
        // Gestion des absences
        Route::resource('absences', \App\Http\Controllers\Professeur\AbsenceController::class);
    });

    // ============================================================================
    // ROUTES ASSISTANT (Rôle: assistant)
    // ============================================================================
    Route::middleware(['role:assistant'])->prefix('assistant')->name('assistant.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [\App\Http\Controllers\Assistant\DashboardController::class, 'index'])
            ->name('dashboard');
        
        // Gestion des absences
        Route::resource('absences', \App\Http\Controllers\Assistant\AbsenceController::class);
        Route::get('absences/eleve/{eleve}', [\App\Http\Controllers\Assistant\AbsenceController::class, 'byEleve'])
            ->name('absences.by_eleve');
        Route::post('absences/import', [\App\Http\Controllers\Assistant\AbsenceController::class, 'import'])
            ->name('absences.import');
        Route::get('absences/export', [\App\Http\Controllers\Assistant\AbsenceController::class, 'export'])
            ->name('absences.export');
            
        // Gestion des élèves (lecture seule)
        Route::get('eleves', [\App\Http\Controllers\Assistant\EleveController::class, 'index'])
            ->name('eleves.index');
        Route::get('eleves/{eleve}', [\App\Http\Controllers\Assistant\EleveController::class, 'show'])
            ->name('eleves.show');
            
        // Gestion des classes (lecture seule)
        Route::get('classes', [\App\Http\Controllers\Assistant\ClasseController::class, 'index'])
            ->name('classes.index');
        Route::get('classes/{classe}', [\App\Http\Controllers\Assistant\ClasseController::class, 'show'])
            ->name('classes.show');
    });

    // ============================================================================
    // ROUTES ELEVE (Rôle: eleve)
    // ============================================================================
    Route::middleware(['role:eleve'])->prefix('eleve')->name('eleve.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [\App\Http\Controllers\Eleve\DashboardController::class, 'index'])->name('dashboard');
        
        // Gestion du profil
        Route::get('/profil', [\App\Http\Controllers\Eleve\ProfilController::class, 'edit'])->name('profil.edit');
        Route::put('/profil', [\App\Http\Controllers\Eleve\ProfilController::class, 'update'])->name('profil.update');
        Route::put('/profil/password', [\App\Http\Controllers\Eleve\ProfilController::class, 'updatePassword'])->name('profil.password');
        
        // Gestion des cours
        Route::get('/cours', [\App\Http\Controllers\Eleve\CoursController::class, 'index'])->name('cours.index');
        Route::get('/cours/{cours}', [\App\Http\Controllers\Eleve\CoursController::class, 'show'])->name('cours.show');
        
        // Gestion des notes
        Route::get('/notes', [\App\Http\Controllers\Eleve\NoteController::class, 'index'])->name('notes.index');
        Route::get('/notes/{note}', [\App\Http\Controllers\Eleve\NoteController::class, 'show'])->name('notes.show');
        
        // Gestion des devoirs
        Route::get('/devoirs', [\App\Http\Controllers\Eleve\DevoirController::class, 'index'])->name('devoirs.index');
        Route::get('/devoirs/{devoir}', [\App\Http\Controllers\Eleve\DevoirController::class, 'show'])->name('devoirs.show');
        Route::get('/devoirs/{devoir}/rendre', [\App\Http\Controllers\Eleve\DevoirController::class, 'showRendreForm'])->name('devoirs.rendre');
        Route::post('/devoirs/{devoir}/rendre', [\App\Http\Controllers\Eleve\DevoirController::class, 'rendreDevoir'])->name('devoirs.rendre.store');
        
        // Gestion des absences
        Route::get('/absences', [\App\Http\Controllers\Eleve\AbsenceController::class, 'index'])->name('absences.index');
        Route::get('/absences/{absence}', [\App\Http\Controllers\Eleve\AbsenceController::class, 'show'])->name('absences.show');
        Route::get('/absences/{absence}/justifier', [\App\Http\Controllers\Eleve\AbsenceController::class, 'showJustificationForm'])->name('absences.justifier');
        Route::post('/absences/{absence}/justifier', [\App\Http\Controllers\Eleve\AbsenceController::class, 'justifier'])->name('absences.justifier.store');
        
        // Gestion des paiements
        Route::get('/paiements', [\App\Http\Controllers\Eleve\PaiementController::class, 'index'])->name('paiements.index');
        Route::get('/paiements/{paiement}', [\App\Http\Controllers\Eleve\PaiementController::class, 'show'])->name('paiements.show');
        Route::get('/paiements/{paiement}/facture', [\App\Http\Controllers\Eleve\PaiementController::class, 'facture'])->name('paiements.facture');
    });

});

// Routes pour les préférences de notification
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/notifications', [\App\Http\Controllers\NotificationPreferencesController::class, 'edit'])
        ->name('notification-preferences.edit');
    Route::put('/profile/notifications', [\App\Http\Controllers\NotificationPreferencesController::class, 'update'])
        ->name('notification-preferences.update');
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