<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SimpleLoginController as LoginController;
use App\Http\Controllers\DashboardController;

// Redirection de la racine vers le tableau de bord admin pour les utilisateurs connectés, sinon vers la page de connexion
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

// Page de connexion
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Déconnexion
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Redirection de /dashboard vers le tableau de bord admin
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth');

// Routes d'administration
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Tableau de bord
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Gestion des absences
    Route::resource('absences', \App\Http\Controllers\Admin\AbsenceController::class)->except(['show']);
    Route::get('absences/export', [\App\Http\Controllers\Admin\AbsenceController::class, 'export'])->name('absences.export');
    
    // Gestion des élèves
    Route::resource('eleves', \App\Http\Controllers\Admin\EleveController::class);
    
    // Gestion des paiements
    Route::resource('paiements', \App\Http\Controllers\Admin\PaiementController::class);
    
    // Gestion des professeurs
    Route::resource('professeurs', \App\Http\Controllers\Admin\ProfesseurController::class);
    
    // Gestion des matières
    Route::resource('matieres', \App\Http\Controllers\Admin\MatiereController::class);
    
    // Gestion des salaires
    Route::resource('salaires', \App\Http\Controllers\Admin\SalaireController::class);
    Route::post('salaires/generate', [\App\Http\Controllers\Admin\SalaireController::class, 'generateSalairesMensuels'])
        ->name('salaires.generate');
    
    // Autres routes administratives...
});

// Configuration des salaires
Route::middleware(['auth', 'admin'])
    ->prefix('admin/configuration/salaires')
    ->name('admin.configuration.salaires.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ConfigurationSalaireController::class, 'index'])
            ->name('index');
            
        Route::post('/matieres/{matiereId}/prix', 
            [\App\Http\Controllers\Admin\ConfigurationSalaireController::class, 'updateMatierePrix'])
            ->name('matieres.update-prix')
            ->where('matiereId', '[0-9]+');
            
        Route::post('/professeurs/pourcentage', 
            [\App\Http\Controllers\Admin\ConfigurationSalaireController::class, 'updateProfesseurPourcentage'])
            ->name('professeurs.update-pourcentage');
    });

/*
// Anciennes routes d'administration (commentées pour le moment)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    // Tableau de bord
    Route::get('/dashboard', [\App\Http\Controllers\Admin\NewDashboardController::class, 'index'])->name('dashboard');
    
    // Autres routes administratives...
});
*/
