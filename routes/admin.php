<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NiveauController;
use App\Http\Controllers\Admin\FiliereController;
use App\Http\Controllers\Admin\MatiereController;
use App\Http\Controllers\Admin\ParametreController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\Admin\AssistantController;
use App\Http\Controllers\Admin\ClasseController;
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
    
    // Routes pour la gestion des cours
    Route::resource('cours', \App\Http\Controllers\Admin\CoursController::class)
        ->names('admin.cours')
        ->middleware('validate:cours');
    
    // Routes pour la gestion des paramètres
    Route::get('/parametres', [ParametreController::class, 'index'])->name('admin.parametres');
    Route::put('/parametres', [ParametreController::class, 'update'])->name('admin.parametres.update');
    
    // Routes pour la gestion des assistants
    Route::resource('assistants', AssistantController::class)
        ->names('admin.assistants')
        ->middleware('validate:user');
        
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
        
    // Routes pour la gestion des enseignants
    Route::prefix('enseignants')
        ->name('admin.enseignants.')
        ->middleware(['role:admin', 'validate:user'])
        ->group(function () {
            Route::get('/', [EnseignantController::class, 'index'])->name('index');
            Route::get('/create', [EnseignantController::class, 'create'])->name('create');
            Route::post('/', [EnseignantController::class, 'store'])->name('store');
            Route::get('/{enseignant}', [EnseignantController::class, 'show'])->name('show');
            Route::get('/{enseignant}/edit', [EnseignantController::class, 'edit'])->name('edit');
            Route::put('/{enseignant}', [EnseignantController::class, 'update'])->name('update');
            Route::delete('/{enseignant}', [EnseignantController::class, 'destroy'])->name('destroy');
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
        
    // Routes pour la gestion des classes
    Route::resource('classes', ClasseController::class)
        ->names('admin.classes')
        ->middleware('validate:classe')
        ->except(['index', 'show']);
        
    Route::get('classes', [ClasseController::class, 'index'])->name('admin.classes.index');
    Route::get('classes/{classe}', [ClasseController::class, 'show'])->name('admin.classes.show');
    Route::patch('classes/{classe}/archive', [ClasseController::class, 'archive'])->name('admin.classes.archive');
    Route::post('classes/{classe}/eleves', [ClasseController::class, 'addEleve'])->name('admin.classes.eleves.add');
    Route::delete('classes/{classe}/eleves/{eleve}', [ClasseController::class, 'removeEleve'])->name('admin.classes.eleves.remove');
    Route::get('classes/{classe}/export/eleves', [ClasseController::class, 'exportEleves'])->name('admin.classes.export.eleves');
    
    // Routes pour la gestion des absences
    Route::resource('absences', \App\Http\Controllers\Admin\AbsenceController::class)
        ->names('admin.absences')
        ->middleware('validate:absence');
        
    // Route pour l'export des absences
    Route::get('absences/export', [\App\Http\Controllers\Admin\AbsenceController::class, 'export'])
        ->name('admin.absences.export');
        
    // Route pour la notification des parents
    Route::post('absences/{absence}/notify-parents', [\App\Http\Controllers\Admin\AbsenceController::class, 'notifyParents'])
        ->name('admin.absences.notify-parents');
    Route::get('classes/{classe}/export/emploi-du-temps', [ClasseController::class, 'exportEmploiDuTemps'])->name('admin.classes.export.edt');
    
    // Routes pour la gestion des emplois du temps
    Route::resource('emplois-du-temps', \App\Http\Controllers\Admin\EmploiDuTempsController::class)
        ->names('admin.emplois-du-temps')
        ->middleware('validate:emploi_du_temps');
    
    // Routes pour la gestion des examens et notes
    Route::resource('examens', \App\Http\Controllers\Admin\ExamenController::class)
        ->names('admin.examens')
        ->middleware('validate:examen');
    
    Route::resource('notes', \App\Http\Controllers\Admin\NoteController::class)
        ->names('admin.notes')
        ->except(['index', 'create', 'store']);
    
    // Routes pour la gestion des absences
    Route::resource('absences', \App\Http\Controllers\Admin\AbsenceController::class)
        ->names('admin.absences')
        ->middleware('validate:absence');
        
    // Routes supplémentaires pour les absences
    Route::prefix('absences')
        ->name('admin.absences.')
        ->group(function () {
            // Télécharger un justificatif
            Route::get('{absence}/justificatif', [\App\Http\Controllers\Admin\AbsenceController::class, 'telechargerJustificatif'])
                ->name('justificatif.download');
                
            // Exporter les absences en Excel
            Route::get('export', [\App\Http\Controllers\Admin\AbsenceController::class, 'export'])
                ->name('export');
                
            // Générer un PDF pour une absence
            Route::get('{absence}/pdf', [\App\Http\Controllers\Admin\AbsenceController::class, 'genererPdf'])
                ->name('pdf');
        });
    
    // Routes pour la gestion des paiements
    Route::resource('paiements', \App\Http\Controllers\Admin\PaiementController::class)
        ->names('admin.paiements')
        ->middleware('validate:paiement');
    
    // Routes pour la gestion des salaires
    Route::resource('salaires', \App\Http\Controllers\Admin\SalaireController::class)
        ->names('admin.salaires')
        ->middleware('validate:salaire');
    
    // Routes pour les rapports
    Route::prefix('rapports')
        ->name('admin.rapports.')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\RapportController::class, 'index'])->name('index');
            Route::get('/scolarite', [\App\Http\Controllers\Admin\RapportController::class, 'scolarite'])->name('scolarite');
            Route::get('/presence', [\App\Http\Controllers\Admin\RapportController::class, 'presence'])->name('presence');
            Route::get('/resultats', [\App\Http\Controllers\Admin\RapportController::class, 'resultats'])->name('resultats');
            Route::get('/financier', [\App\Http\Controllers\Admin\RapportController::class, 'financier'])->name('financier');
            Route::post('/export', [\App\Http\Controllers\Admin\RapportController::class, 'export'])->name('export');
        });
    
    // Routes pour la gestion des administrateurs
    Route::resource('administrateurs', \App\Http\Controllers\Admin\AdministrateurController::class)
        ->names('admin.administrateurs')
        ->middleware('validate:user');
        
    // Routes pour la gestion des rôles et permissions
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)
        ->names('admin.roles')
        ->middleware('validate:role');
    
    Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class)
        ->names('admin.permissions')
        ->except(['create', 'edit', 'store', 'update', 'destroy']);
        
    // Routes pour les paramètres avancés
    Route::prefix('parametres')
        ->name('admin.parametres.')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ParametreController::class, 'index'])->name('index');
            Route::put('/generaux', [\App\Http\Controllers\Admin\ParametreController::class, 'updateGeneraux'])->name('generaux.update');
            Route::put('/notifications', [\App\Http\Controllers\Admin\ParametreController::class, 'updateNotifications'])->name('notifications.update');
            Route::put('/smtp', [\App\Http\Controllers\Admin\ParametreController::class, 'updateSmtp'])->name('smtp.update');
            Route::post('/test-email', [\App\Http\Controllers\Admin\ParametreController::class, 'testEmail'])->name('test-email');
            Route::post('/backup', [\App\Http\Controllers\Admin\ParametreController::class, 'createBackup'])->name('backup.create');
            Route::get('/backup/download/{filename}', [\App\Http\Controllers\Admin\ParametreController::class, 'downloadBackup'])->name('backup.download');
            Route::delete('/backup/{filename}', [\App\Http\Controllers\Admin\ParametreController::class, 'deleteBackup'])->name('backup.delete');
        });
});
