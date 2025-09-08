<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;

// Redirection de la racine
Route::get('/', function () {
    return redirect()->route('home');
});

// Page d'accueil publique
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Routes d'authentification (accessibles uniquement aux invités)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Déconnexion (accessible à tous pour éviter les erreurs)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Routes protégées nécessitant une authentification
Route::middleware('auth')->group(function () {
    // Déconnexion avec middleware auth
    Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');
});

// Routes d'administration (nécessitent d'être admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Profil utilisateur
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile');
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
    
    // Tableau de bord administrateur
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Gestion des professeurs
    Route::prefix('professeurs')->name('professeurs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ProfesseurController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ProfesseurController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ProfesseurController::class, 'store'])->name('store');
        Route::get('/{professeur}', [\App\Http\Controllers\Admin\ProfesseurController::class, 'show'])->name('show');
        Route::get('/{professeur}/edit', [\App\Http\Controllers\Admin\ProfesseurController::class, 'edit'])->name('edit');
        Route::put('/{professeur}', [\App\Http\Controllers\Admin\ProfesseurController::class, 'update'])->name('update');
        Route::delete('/{professeur}', [\App\Http\Controllers\Admin\ProfesseurController::class, 'destroy'])->name('destroy');
    });
    
    // Gestion des élèves
    Route::prefix('eleves')->name('eleves.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EleveController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\EleveController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\EleveController::class, 'store'])->name('store');
        Route::get('/{eleve}', [\App\Http\Controllers\Admin\EleveController::class, 'show'])->name('show');
        Route::get('/{eleve}/edit', [\App\Http\Controllers\Admin\EleveController::class, 'edit'])->name('edit');
        Route::put('/{eleve}', [\App\Http\Controllers\Admin\EleveController::class, 'update'])->name('update');
        Route::delete('/{eleve}', [\App\Http\Controllers\Admin\EleveController::class, 'destroy'])->name('destroy');
        
        // Gestion du mot de passe
        Route::get('/{eleve}/password', [\App\Http\Controllers\Admin\EleveController::class, 'editPassword'])->name('password.edit');
        Route::put('/{eleve}/password', [\App\Http\Controllers\Admin\EleveController::class, 'updatePassword'])->name('password.update');
        
        // Activation/désactivation
        Route::post('/{eleve}/activate', [\App\Http\Controllers\Admin\EleveController::class, 'activate'])->name('activate');
        Route::post('/{eleve}/deactivate', [\App\Http\Controllers\Admin\EleveController::class, 'deactivate'])->name('deactivate');
        
        // Gestion des matières des élèves
        Route::get('/{eleve}/matieres', [\App\Http\Controllers\EleveMatiereController::class, 'edit'])->name('matieres.edit');
        Route::put('/{eleve}/matieres', [\App\Http\Controllers\EleveMatiereController::class, 'update'])->name('matieres.update');
    });

    // Gestion des matières
    Route::prefix('matieres')->name('matieres.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MatiereController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\MatiereController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\MatiereController::class, 'store'])->name('store');
        Route::get('/{matiere}/edit', [\App\Http\Controllers\Admin\MatiereController::class, 'edit'])->name('edit');
        Route::put('/{matiere}', [\App\Http\Controllers\Admin\MatiereController::class, 'update'])->name('update');
        Route::delete('/{matiere}', [\App\Http\Controllers\Admin\MatiereController::class, 'destroy'])->name('destroy');
        
        // Assignation des professeurs aux matières
        Route::post('/{matiere}/assign-professeur', [\App\Http\Controllers\Admin\MatiereController::class, 'assignProfesseur'])->name('assign-professeur');
        Route::delete('/{matiere}/remove-professeur/{professeur}', [\App\Http\Controllers\Admin\MatiereController::class, 'removeProfesseur'])->name('remove-professeur');
    });

    // Gestion des salaires
    Route::prefix('salaires')->name('salaires.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SalaireController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\SalaireController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\SalaireController::class, 'store'])->name('store');
        Route::get('/{salaire}', [\App\Http\Controllers\Admin\SalaireController::class, 'show'])->name('show');
        Route::get('/{salaire}/edit', [\App\Http\Controllers\Admin\SalaireController::class, 'edit'])->name('edit');
        Route::put('/{salaire}', [\App\Http\Controllers\Admin\SalaireController::class, 'update'])->name('update');
        Route::delete('/{salaire}', [\App\Http\Controllers\Admin\SalaireController::class, 'destroy'])->name('destroy');
        
        // Export des salaires
        Route::get('/export', [\App\Http\Controllers\Admin\SalaireController::class, 'export'])->name('export');
        
        // Marquer un salaire comme payé
        Route::post('/{salaire}/payer', [\App\Http\Controllers\Admin\SalaireController::class, 'payer'])->name('payer');
        
        // Générer une fiche de paie PDF
        Route::get('/{salaire}/fiche-paie', [\App\Http\Controllers\Admin\SalaireController::class, 'fichePaie'])->name('fiche-paie');
        
        // Rapport des salaires
        Route::get('/rapport', [\App\Http\Controllers\Admin\SalaireController::class, 'rapport'])->name('rapport');
    });

    // Gestion des paiements
    Route::prefix('paiements')->name('paiements.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PaiementController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PaiementController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PaiementController::class, 'store'])->name('store');
        Route::get('/{paiement}', [\App\Http\Controllers\Admin\PaiementController::class, 'show'])->name('show');
        Route::get('/{paiement}/edit', [\App\Http\Controllers\Admin\PaiementController::class, 'edit'])->name('edit');
        Route::put('/{paiement}', [\App\Http\Controllers\Admin\PaiementController::class, 'update'])->name('update');
        Route::delete('/{paiement}', [\App\Http\Controllers\Admin\PaiementController::class, 'destroy'])->name('destroy');
        
        // Marquer un paiement comme payé
        Route::post('/{paiement}/payer', [\App\Http\Controllers\Admin\PaiementController::class, 'marquerCommePaye'])->name('payer');
        
        // Exporter les paiements
        Route::get('/export', [\App\Http\Controllers\Admin\PaiementController::class, 'export'])->name('export');
    });
    
    // Gestion des absences
    Route::prefix('absences')->name('absences.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AbsenceController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AbsenceController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AbsenceController::class, 'store'])->name('store');
        Route::get('/{absence}', [\App\Http\Controllers\Admin\AbsenceController::class, 'show'])->name('show');
        Route::get('/{absence}/edit', [\App\Http\Controllers\Admin\AbsenceController::class, 'edit'])->name('edit');
        Route::put('/{absence}', [\App\Http\Controllers\Admin\AbsenceController::class, 'update'])->name('update');
        Route::delete('/{absence}', [\App\Http\Controllers\Admin\AbsenceController::class, 'destroy'])->name('destroy');
        
        // Justifier une absence
        Route::post('/{absence}/justifier', [\App\Http\Controllers\Admin\AbsenceController::class, 'justifier'])->name('justifier');
        
        // Exporter les absences
        Route::get('/export', [\App\Http\Controllers\Admin\AbsenceController::class, 'export'])->name('export');
        
        // Rapport des absences
        Route::get('/rapport', [\App\Http\Controllers\Admin\AbsenceController::class, 'rapport'])->name('rapport');
    });
    
});
