<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NiveauController;
use App\Http\Controllers\Admin\FiliereController;
use App\Http\Controllers\Admin\MatiereController;
use App\Http\Controllers\Admin\ParametreController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\Admin\AssistantController;
use App\Http\Controllers\Admin\ClasseController;
use App\Http\Controllers\Admin\PaiementEleveController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CentreController as AdminCentreController;
use App\Http\Controllers\Admin\ProfesseurController as AdminProfesseurController;
use App\Http\Controllers\Admin\EleveController as AdminEleveController;
use App\Http\Controllers\Admin\EvenementController as AdminEvenementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PaiementController as AdminPaiementController;
use App\Http\Controllers\Admin\EnseignementController as AdminEnseignementController;
use App\Http\Controllers\Admin\AbsenceController as AdminAbsenceController;
use App\Http\Controllers\Admin\SalaireController as AdminSalaireController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\RapportController as AdminRapportController;

Route::middleware(['auth', 'verified', 'active', 'role:admin'])->group(function () {
    // Tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');
        
    // Search route
    Route::get('/search', [DashboardController::class, 'search'])
        ->name('admin.search');
    
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
    
    // Routes pour la gestion des paiements des élèves
    Route::prefix('paiements/eleves')->name('admin.paiements.eleves.')->group(function () {
        Route::get('/', [PaiementEleveController::class, 'index'])->name('index');
        Route::get('/create', [PaiementEleveController::class, 'create'])->name('create');
        Route::post('/', [PaiementEleveController::class, 'store'])->name('store');
        Route::get('/{paiementEleve}', [PaiementEleveController::class, 'show'])->name('show');
        Route::get('/{paiementEleve}/edit', [PaiementEleveController::class, 'edit'])->name('edit');
        Route::put('/{paiementEleve}', [PaiementEleveController::class, 'update'])->name('update');
        Route::delete('/{paiementEleve}', [PaiementEleveController::class, 'destroy'])->name('destroy');
        Route::patch('/{paiementEleve}/status', [PaiementEleveController::class, 'updateStatus'])->name('update-status');
        Route::get('/eleve/{eleve}/details', [PaiementEleveController::class, 'getEleveDetails'])->name('eleve.details');
    });
    
    // Routes pour la gestion des paiements des professeurs
    Route::prefix('paiements/professeurs')->name('admin.paiements.professeurs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PaiementProfesseurController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PaiementProfesseurController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PaiementProfesseurController::class, 'store'])->name('store');
        Route::get('/{paiementProfesseur}', [\App\Http\Controllers\Admin\PaiementProfesseurController::class, 'show'])->name('show');
        Route::get('/{paiementProfesseur}/edit', [\App\Http\Controllers\Admin\PaiementProfesseurController::class, 'edit'])->name('edit');
        Route::put('/{paiementProfesseur}', [\App\Http\Controllers\Admin\PaiementProfesseurController::class, 'update'])->name('update');
        Route::delete('/{paiementProfesseur}', [\App\Http\Controllers\Admin\PaiementProfesseurController::class, 'destroy'])->name('destroy');
        Route::patch('/{paiementProfesseur}/status', [\App\Http\Controllers\Admin\PaiementProfesseurController::class, 'updateStatus'])->name('update-status');
        // Export des paiements
        Route::get('/export', [\App\Http\Controllers\Admin\PaiementProfesseurController::class, 'export'])->name('export');
    });
    
    // Routes pour la gestion des assistants
    Route::resource('assistants', AssistantController::class)
        ->names('admin.assistants')
        ->middleware('validate:user');
        
    // Routes pour la gestion des professeurs
    Route::resource('professeurs', \App\Http\Controllers\Admin\ProfesseurController::class)
        ->names('admin.professeurs')
        ->middleware('validate:user');
        
    // Routes pour la gestion des élèves
    Route::resource('eleves', \App\Http\Controllers\Admin\EleveController::class)
        ->names('admin.eleves')
        ->middleware('validate:user');
        
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
        
    // Routes pour la gestion des administrateurs
    Route::prefix('administrateurs')
        ->name('admin.administrateurs.')
        ->middleware(['role:admin', 'validate:user'])
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdministrateurController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\AdministrateurController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\AdministrateurController::class, 'store'])->name('store');
            Route::get('/{administrateur}', [\App\Http\Controllers\Admin\AdministrateurController::class, 'show'])->name('show');
            Route::get('/{administrateur}/edit', [\App\Http\Controllers\Admin\AdministrateurController::class, 'edit'])->name('edit');
            Route::put('/{administrateur}', [\App\Http\Controllers\Admin\AdministrateurController::class, 'update'])->name('update');
            Route::delete('/{administrateur}', [\App\Http\Controllers\Admin\AdministrateurController::class, 'destroy'])->name('destroy');
        });
        
    // Routes pour la gestion des examens
    Route::prefix('examens')
        ->name('admin.examens.')
        ->middleware(['role:admin'])
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ExamenController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\ExamenController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\ExamenController::class, 'store'])->name('store');
            Route::get('/{examen}', [\App\Http\Controllers\Admin\ExamenController::class, 'show'])->name('show');
            Route::get('/{examen}/edit', [\App\Http\Controllers\Admin\ExamenController::class, 'edit'])->name('edit');
            Route::put('/{examen}', [\App\Http\Controllers\Admin\ExamenController::class, 'update'])->name('update');
            Route::delete('/{examen}', [\App\Http\Controllers\Admin\ExamenController::class, 'destroy'])->name('destroy');
        });
        
    // Routes pour la gestion des absences
    Route::prefix('absences')
        ->name('admin.absences.')
        ->middleware(['role:admin,assistant'])
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AbsenceController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\AbsenceController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\AbsenceController::class, 'store'])->name('store');
            Route::get('/{absence}', [\App\Http\Controllers\Admin\AbsenceController::class, 'show'])->name('show');
            Route::get('/{absence}/edit', [\App\Http\Controllers\Admin\AbsenceController::class, 'edit'])->name('edit');
            Route::put('/{absence}', [\App\Http\Controllers\Admin\AbsenceController::class, 'update'])->name('update');
            Route::delete('/{absence}', [\App\Http\Controllers\Admin\AbsenceController::class, 'destroy'])->name('destroy');
        });
        
    // Routes pour la gestion des paiements
    Route::prefix('paiements')
        ->name('admin.paiements.')
        ->middleware(['role:admin,assistant'])
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PaiementController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\PaiementController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\PaiementController::class, 'store'])->name('store');
            Route::get('/{paiement}', [\App\Http\Controllers\Admin\PaiementController::class, 'show'])->name('show');
            Route::get('/{paiement}/edit', [\App\Http\Controllers\Admin\PaiementController::class, 'edit'])->name('edit');
            Route::put('/{paiement}', [\App\Http\Controllers\Admin\PaiementController::class, 'update'])->name('update');
            Route::delete('/{paiement}', [\App\Http\Controllers\Admin\PaiementController::class, 'destroy'])->name('destroy');
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
    
    Route::get('notes', [\App\Http\Controllers\Admin\NoteController::class, 'index'])->name('admin.notes.index');
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
        
    // Routes pour la gestion des factures
    Route::resource('factures', \App\Http\Controllers\Admin\FactureController::class)
        ->names('admin.factures')
        ->middleware('validate:facture');
    
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
            // Paramètres du compte administrateur
            Route::get('/compte', [\App\Http\Controllers\Admin\ParametreController::class, 'compte'])->name('compte');
            Route::put('/compte', [\App\Http\Controllers\Admin\ParametreController::class, 'updateCompte'])->name('compte.update');
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
