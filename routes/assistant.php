<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth', 'role:assistant'])->prefix('assistant')->name('assistant.')->group(function () {
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
    
    // Profil
    Route::get('/profil', function () {
        return Inertia::render('Assistant/Profil');
    })->name('profil');
});
