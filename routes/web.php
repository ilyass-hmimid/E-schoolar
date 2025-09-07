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
    
    // Gestion des élèves
    Route::prefix('eleves')->name('eleves.')->group(function () {
        Route::get('/', [\App\Http\Controllers\EleveController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\EleveController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\EleveController::class, 'store'])->name('store');
        Route::get('/{eleve}/edit', [\App\Http\Controllers\EleveController::class, 'edit'])->name('edit');
        Route::put('/{eleve}', [\App\Http\Controllers\EleveController::class, 'update'])->name('update');
        Route::delete('/{eleve}', [\App\Http\Controllers\EleveController::class, 'destroy'])->name('destroy');
        
        // Gestion des matières des élèves
        Route::get('/{eleve}/matieres', [\App\Http\Controllers\EleveMatiereController::class, 'edit'])->name('matieres.edit');
        Route::put('/{eleve}/matieres', [\App\Http\Controllers\EleveMatiereController::class, 'update'])->name('matieres.update');
    });
});
