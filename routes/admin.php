<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EleveController;
use App\Http\Controllers\Admin\ProfesseurController;
use App\Http\Controllers\Admin\PaiementController;
use App\Http\Controllers\Admin\AbsenceController;
use App\Http\Controllers\Admin\ClasseController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'active', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
    // Tableau de bord
    // Tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
        
    // Recherche
    Route::get('/search', [DashboardController::class, 'search'])
        ->name('search');
    
    // Gestion des utilisateurs
    Route::resource('users', UserController::class)
        ->names('users')
        ->middleware('validate:user');
    
    // Gestion des élèves
    Route::resource('eleves', EleveController::class)
        ->names('eleves')
        ->middleware('validate:user');
    
    // Gestion des professeurs
    Route::resource('professeurs', ProfesseurController::class)
        ->names('professeurs')
        ->middleware('validate:user');
    
    // Gestion des paiements
    Route::resource('paiements', PaiementController::class)
        ->names('paiements')
        ->middleware('validate:paiement');
        
    // Gestion des paiements des élèves
    Route::prefix('paiements/eleves')
        ->name('paiements.eleves.')
        ->group(function () {
            Route::get('/', [PaiementController::class, 'indexEleves'])->name('index');
            Route::get('/create', [PaiementController::class, 'createEleve'])->name('create');
            Route::post('/', [PaiementController::class, 'storeEleve'])->name('store');
            Route::get('/{paiement}', [PaiementController::class, 'showEleve'])->name('show');
            Route::get('/{paiement}/edit', [PaiementController::class, 'editEleve'])->name('edit');
            Route::put('/{paiement}', [PaiementController::class, 'updateEleve'])->name('update');
            Route::delete('/{paiement}', [PaiementController::class, 'destroyEleve'])->name('destroy');
        });
    
    // Gestion des absences
    Route::resource('absences', AbsenceController::class)
        ->names('absences')
        ->middleware('validate:absence');
        
    // Gestion des classes
    Route::resource('classes', ClasseController::class)
        ->names('classes')
        ->middleware('validate:classe');
        
    // Fonctionnalités supplémentaires pour les absences
    Route::prefix('absences')
        ->name('absences.')
        ->group(function () {
            // Télécharger un justificatif
            Route::get('{absence}/justificatif', [AbsenceController::class, 'telechargerJustificatif'])
                ->name('justificatif.download');
                
            // Exporter les absences en Excel
            Route::get('export', [AbsenceController::class, 'export'])
                ->name('export');
                
            // Générer un PDF pour une absence
            Route::get('{absence}/pdf', [AbsenceController::class, 'genererPdf'])
                ->name('pdf');
                
            // Notifier les parents
            Route::post('{absence}/notify-parents', [AbsenceController::class, 'notifyParents'])
                ->name('notify-parents');
        });
    
    // Fonctionnalités supplémentaires pour les paiements
    Route::prefix('paiements')
        ->name('paiements.')
        ->group(function () {
            // Exporter les paiements en Excel
            Route::get('export', [PaiementController::class, 'export'])
                ->name('export');
                
            // Générer une facture PDF
            Route::get('{paiement}/facture', [PaiementController::class, 'genererFacture'])
                ->name('facture');
        });
    
    // Tableau de bord
    Route::get('/statistiques', [DashboardController::class, 'statistiques'])
        ->name('statistiques');
});
