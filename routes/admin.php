<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NiveauController;
use App\Http\Controllers\Admin\FiliereController;
use App\Http\Controllers\Admin\MatiereController;
use App\Http\Controllers\Admin\ParametreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    // Tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');
    
    // Routes pour la gestion des niveaux
    Route::resource('niveaux', NiveauController::class)
        ->names('admin.niveaux')
        ->middleware('validate:niveau');
    
    // Routes pour la gestion des filières
    Route::resource('filieres', FiliereController::class)
        ->names('admin.filieres')
        ->middleware('validate:filiere');
    
    // Routes pour la gestion des matières
    Route::resource('matieres', MatiereController::class)
        ->names('admin.matieres')
        ->middleware('validate:matiere');
    
    // Routes pour la gestion des paramètres
    Route::prefix('parametres')
        ->name('admin.parametres.')
        ->group(function () {
            Route::get('/', [ParametreController::class, 'index'])->name('index');
            Route::put('/', [ParametreController::class, 'update'])->name('update');
        });
    
    // Routes pour la gestion des élèves
    Route::prefix('eleves')
        ->name('admin.eleves.')
        ->middleware(['role:admin', 'validate:user'])
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\EleveController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\EleveController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\EleveController::class, 'store'])->name('store');
            Route::get('/{eleve}', [\App\Http\Controllers\EleveController::class, 'show'])->name('show');
            Route::get('/{eleve}/edit', [\App\Http\Controllers\EleveController::class, 'edit'])->name('edit');
            Route::put('/{eleve}', [\App\Http\Controllers\EleveController::class, 'update'])->name('update');
            Route::delete('/{eleve}', [\App\Http\Controllers\EleveController::class, 'destroy'])->name('destroy');
        });
        
    // Routes pour la gestion des cours
    Route::prefix('cours')
        ->name('admin.cours.')
        ->middleware(['role:admin'])
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CoursController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\CoursController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\CoursController::class, 'store'])->name('store');
            Route::get('/{cour}', [\App\Http\Controllers\Admin\CoursController::class, 'show'])->name('show');
            Route::get('/{cour}/edit', [\App\Http\Controllers\Admin\CoursController::class, 'edit'])->name('edit');
            Route::put('/{cour}', [\App\Http\Controllers\Admin\CoursController::class, 'update'])->name('update');
            Route::delete('/{cour}', [\App\Http\Controllers\Admin\CoursController::class, 'destroy'])->name('destroy');
        });
});
