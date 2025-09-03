<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Contrôleurs principaux
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\RedirectController;

// Contrôleurs Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EleveController as AdminEleveController;
use App\Http\Controllers\Admin\ProfesseurController as AdminProfesseurController;
use App\Http\Controllers\Admin\PaiementController as AdminPaiementController;
use App\Http\Controllers\Admin\AbsenceController as AdminAbsenceController;

// Contrôleurs Professeur
use App\Http\Controllers\Professeur\DashboardController as ProfesseurDashboardController;
use App\Http\Controllers\Professeur\AbsenceController as ProfesseurAbsenceController;

// Contrôleurs Assistant
use App\Http\Controllers\Assistant\DashboardController as AssistantDashboardController;
use App\Http\Controllers\Assistant\AbsenceController as AssistantAbsenceController;
use App\Http\Controllers\Assistant\EleveController as AssistantEleveController;

// Contrôleurs Élève
use App\Http\Controllers\Eleve\DashboardController as EleveDashboardController;
use App\Http\Controllers\Eleve\AbsenceController as EleveAbsenceController;
use App\Http\Controllers\Eleve\PaiementController as ElevePaiementController;

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/

// Authentification
Auth::routes([
    'verify' => true,
    'login' => true,
    'logout' => true,
    'register' => false,
    'reset' => true,
    'confirm' => true,
    'verification' => ['verify', 'resend']
]);

// Page d'accueil
Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->hasRole('professeur')) {
            return redirect()->route('professeur.dashboard');
        } elseif (Auth::user()->hasRole('assistant')) {
            return redirect()->route('assistant.dashboard');
        } elseif (Auth::user()->hasRole('eleve')) {
            return redirect()->route('eleve.dashboard');
        }
    }
    return view('auth.login');
})->name('home');

// Routes accessibles aux invités
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [LoginController::class, 'reset'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Routes Protégées
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'active'])->group(function () {
    // Tableau de bord selon le rôle
    Route::get('/dashboard', [RedirectController::class, 'redirectToDashboard'])
        ->name('dashboard');

    // Routes du profil utilisateur
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::patch('/avatar', [AvatarController::class, 'update'])->name('avatar');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes Admin
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des élèves
        Route::resource('eleves', AdminEleveController::class);
        
        // Gestion des professeurs
        Route::resource('professeurs', AdminProfesseurController::class);
        
        // Gestion des paiements
        Route::resource('paiements', AdminPaiementController::class);
        
        // Gestion des absences
        Route::resource('absences', AdminAbsenceController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Routes Professeur
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:professeur'])->prefix('professeur')->name('professeur.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [ProfesseurDashboardController::class, 'index'])->name('dashboard');
        
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
            
        // Gestion des élèves (lecture seule)
        Route::get('eleves', [AssistantEleveController::class, 'index'])->name('eleves.index');
        Route::get('eleves/{eleve}', [AssistantEleveController::class, 'show'])->name('eleves.show');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes Élève
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:eleve'])->prefix('eleve')->name('eleve.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [EleveDashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des absences
        Route::get('/absences', [EleveAbsenceController::class, 'index'])->name('absences.index');
        
        // Gestion des paiements
        Route::get('/paiements', [ElevePaiementController::class, 'index'])->name('paiements.index');
        Route::get('/paiements/{paiement}', [ElevePaiementController::class, 'show'])->name('paiements.show');
    });
});

// Routes d'authentification
Auth::routes([
    'verify' => true,
    'login' => true,
    'logout' => false, // Désactive la route de déconnexion par défaut
    'register' => true,
    'reset' => true,
    'confirm' => true,
    'verification' => ['verify', 'resend']
]);

// Route de déconnexion personnalisée
Route::post('/logout', [\App\Http\Controllers\Auth\LogoutController::class, '__invoke'])
    ->name('logout');

// Route d'accueil
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
