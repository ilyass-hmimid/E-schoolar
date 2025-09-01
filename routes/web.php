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
use App\Http\Controllers\Admin\EvenementController as AdminEvenementController;

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
| Routes Publiques
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', function () {
    if (Auth::check()) {
        return app(LoginController::class)->redirectBasedOnRole(Auth::user());
    }
    return view('welcome');
})->name('welcome');

// Authentification
Auth::routes(['verify' => true]);

// Home route for authenticated users
Route::get('/home', function () {
    $user = auth()->user();
    
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('professeur')) {
        return redirect()->route('professeur.dashboard');
    } elseif ($user->hasRole('assistant')) {
        return redirect()->route('assistant.dashboard');
    } elseif ($user->hasRole('eleve')) {
        return redirect()->route('eleve.dashboard');
    }
    
    return redirect()->route('welcome');
})->middleware(['auth', 'verified', 'active'])->name('home');

// Routes accessibles aux invités
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [LoginController::class, 'reset'])->name('password.update');
});

// Page d'aide
Route::get('/aide', function () {
    return view('help.index');
})->name('aide');

// Page de contact
Route::get('/contact', function () {
    return view('support.contact');
})->name('contact');

// Email verification
Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

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
        // Gestion des événements
        Route::resource('evenements', AdminEvenementController::class);
        // Tableau de bord
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des utilisateurs
        Route::resource('users', AdminUserController::class);
        Route::post('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/import', [AdminUserController::class, 'import'])->name('users.import');
        Route::get('users/export', [AdminUserController::class, 'export'])->name('users.export');
        
        // Gestion des centres
        Route::resource('centres', AdminCentreController::class);
        Route::post('centres/{centre}/toggle-status', [AdminCentreController::class, 'toggleStatus'])->name('centres.toggle-status');
        Route::post('centres/import', [AdminCentreController::class, 'import'])->name('centres.import');
        Route::get('centres/export', [AdminCentreController::class, 'export'])->name('centres.export');
        
        // Gestion des professeurs
        Route::resource('professeurs', AdminProfesseurController::class);
        Route::post('professeurs/{professeur}/toggle-status', [AdminProfesseurController::class, 'toggleStatus'])->name('professeurs.toggle-status');
        Route::post('professeurs/import', [AdminProfesseurController::class, 'import'])->name('professeurs.import');
        Route::get('professeurs/export', [AdminProfesseurController::class, 'export'])->name('professeurs.export');
        
        // Gestion des élèves
        Route::resource('eleves', AdminEleveController::class);
        Route::post('eleves/{eleve}/toggle-status', [AdminEleveController::class, 'toggleStatus'])->name('eleves.toggle-status');
        Route::post('eleves/import', [AdminEleveController::class, 'import'])->name('eleves.import');
        Route::get('eleves/export', [AdminEleveController::class, 'export'])->name('eleves.export');
        
        // Gestion des cours
        Route::resource('cours', \App\Http\Controllers\Admin\CoursController::class);
        
        // Gestion des paiements
        Route::resource('paiements', AdminPaiementController::class);
        Route::post('paiements/import', [AdminPaiementController::class, 'import'])->name('paiements.import');
        Route::get('paiements/export', [AdminPaiementController::class, 'export'])->name('paiements.export');
        
        // Gestion des enseignements
        Route::resource('enseignements', AdminEnseignementController::class);
        Route::post('enseignements/{enseignement}/toggle-status', [AdminEnseignementController::class, 'toggleStatus'])->name('enseignements.toggle-status');
        
        // Gestion des absences
        Route::resource('absences', AdminAbsenceController::class);
        
        // Gestion des salaires
        Route::resource('salaires', AdminSalaireController::class);
        
        // Gestion des notifications
        Route::resource('notifications', AdminNotificationController::class);
        
        // Gestion des rapports
        Route::prefix('rapports')->name('rapports.')->group(function () {
            Route::get('/', [AdminRapportController::class, 'index'])->name('index');
            Route::get('generer', [AdminRapportController::class, 'generer'])->name('generer');
            Route::get('telecharger', [AdminRapportController::class, 'telecharger'])->name('telecharger');
            Route::get('export', [AdminRapportController::class, 'export'])->name('export');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Routes Professeur
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:professeur'])->prefix('professeur')->name('professeur.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [ProfesseurDashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des classes
        Route::resource('classes', ProfesseurClasseController::class);
        Route::get('classes/{classe}/export', [ProfesseurClasseController::class, 'export'])->name('classes.export');
        Route::post('classes/{classe}/eleves', [ProfesseurClasseController::class, 'addEleve'])->name('classes.eleves.add');
        Route::delete('classes/{classe}/eleves/{eleve}', [ProfesseurClasseController::class, 'removeEleve'])->name('classes.eleves.remove');
        
        // Gestion des notes
        Route::resource('notes', ProfesseurNoteController::class);
        
        // Calculer la moyenne d'un étudiant dans une matière
        Route::get('/notes/etudiant/{etudiantId}/matiere/{matiereId}/moyenne', 
            [ProfesseurNoteController::class, 'calculerMoyenne']
        )->name('notes.calculer-moyenne');
        
        // Gestion des absences
        Route::resource('absences', ProfesseurAbsenceController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Routes Assistant
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:assistant'])->prefix('assistant')->name('assistant.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [AssistantDashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des absences
        Route::resource('absences', AssistantAbsenceController::class);
        Route::get('absences/eleve/{eleve}', [AssistantAbsenceController::class, 'byEleve'])->name('absences.by_eleve');
        Route::post('absences/import', [AssistantAbsenceController::class, 'import'])->name('absences.import');
        Route::get('absences/export', [AssistantAbsenceController::class, 'export'])->name('absences.export');
            
        // Gestion des élèves (lecture seule)
        Route::get('eleves', [AssistantEleveController::class, 'index'])->name('eleves.index');
        Route::get('eleves/{eleve}', [AssistantEleveController::class, 'show'])->name('eleves.show');
            
        // Gestion des classes (lecture seule)
        Route::get('classes', [AssistantClasseController::class, 'index'])->name('classes.index');
        Route::get('classes/{classe}', [AssistantClasseController::class, 'show'])->name('classes.show');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes Élève
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:eleve'])->prefix('eleve')->name('eleve.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [EleveDashboardController::class, 'index'])->name('dashboard');
        
        // Gestion du profil
        Route::get('/profil', [EleveProfilController::class, 'edit'])->name('profil.edit');
        Route::put('/profil', [EleveProfilController::class, 'update'])->name('profil.update');
        Route::put('/profil/password', [EleveProfilController::class, 'updatePassword'])->name('profil.password');
        
        // Gestion des cours
        Route::get('/cours', [EleveCoursController::class, 'index'])->name('cours.index');
        Route::get('/cours/{cours}', [EleveCoursController::class, 'show'])->name('cours.show');
        
        // Gestion des notes
        Route::get('/notes', [EleveNoteController::class, 'index'])->name('notes.index');
        Route::get('/notes/{note}', [EleveNoteController::class, 'show'])->name('notes.show');
        
        // Gestion des devoirs
        Route::get('/devoirs', [EleveDevoirController::class, 'index'])->name('devoirs.index');
        Route::get('/devoirs/{devoir}', [EleveDevoirController::class, 'show'])->name('devoirs.show');
        Route::get('/devoirs/{devoir}/rendre', [EleveDevoirController::class, 'showRendreForm'])->name('devoirs.rendre');
        Route::post('/devoirs/{devoir}/rendre', [EleveDevoirController::class, 'rendreDevoir'])->name('devoirs.rendre.store');
        
        // Gestion des absences
        Route::get('/absences', [EleveAbsenceController::class, 'index'])->name('absences.index');
        Route::get('/absences/{absence}', [EleveAbsenceController::class, 'show'])->name('absences.show');
        Route::get('/absences/{absence}/justifier', [EleveAbsenceController::class, 'showJustificationForm'])->name('absences.justifier');
        Route::post('/absences/{absence}/justifier', [EleveAbsenceController::class, 'justifier'])->name('absences.justifier.store');
        
        // Gestion des paiements
        Route::get('/paiements', [ElevePaiementController::class, 'index'])->name('paiements.index');
        Route::get('/paiements/{paiement}', [ElevePaiementController::class, 'show'])->name('paiements.show');
        Route::get('/paiements/{paiement}/facture', [ElevePaiementController::class, 'facture'])->name('paiements.facture');
    });
});

// Routes d'authentification
Auth::routes(['verify' => true]);

// Route racine
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->hasRole('professeur')) {
            return redirect()->route('professeur.dashboard');
        } elseif (auth()->user()->hasRole('assistant')) {
            return redirect()->route('assistant.dashboard');
        } else {
            return redirect()->route('eleve.dashboard');
        }
    }
    return view('home');
})->name('home');

// Routes de contact support
Route::prefix('contact')->name('support.')->group(function () {
    Route::get('/', [\App\Http\Controllers\SupportController::class, 'contact'])
        ->name('contact');
    Route::post('/', [\App\Http\Controllers\SupportController::class, 'send'])
        ->name('contact.send');
});

// Ancienne route home (redirige vers le tableau de bord si connecté)
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('home.legacy');

// Importer les routes d'administration
require __DIR__.'/admin.php';
