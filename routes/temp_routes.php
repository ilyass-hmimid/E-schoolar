<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AvatarController;

// Routes d'authentification
Auth::routes();

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
});

// Routes protégées par authentification
Route::middleware(['auth', 'active'])->group(function () {
    // Redirection pour les utilisateurs authentifiés
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    // Routes du profil utilisateur
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::patch('/avatar', [AvatarController::class, 'update'])->name('avatar');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // ROUTES ADMIN (Rôle: admin)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des utilisateurs
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::post('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/import', [\App\Http\Controllers\Admin\UserController::class, 'import'])->name('users.import');
        Route::get('users/export', [\App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');
    });
});
