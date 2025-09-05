<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Contrôleurs principaux
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\HomeController;

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

// Page d'accueil
// Test routes for error pages (only in local environment)
if (app()->environment('local')) {
    // Test route for Classe model
    Route::get('/test/classe', function () {
        try {
            // Test creating a new class
            $classe = new App\Models\Classe([
                'nom' => 'Test Class',
                'code' => 'TEST-001',
                'niveau_id' => 1, // Assuming this ID exists in the niveaux table
                'filiere_id' => 1, // Assuming this ID exists in the filieres table
                'annee_scolaire' => '2024-2025',
                'capacite_max' => 30,
                'est_actif' => true,
            ]);
            
            // Save the class
            $classe->save();
            
            // Test relationships
            $classe->load('niveau', 'filiere');
            
            return [
                'message' => 'Class created successfully',
                'class' => [
                    'id' => $classe->id,
                    'nom' => $classe->nom,
                    'code' => $classe->code,
                    'niveau' => $classe->niveau ? $classe->niveau->nom : null,
                    'filiere' => $classe->filiere ? $classe->filiere->nom : null,
                    'nom_complet' => $classe->nom_complet,
                    'effectif_actuel' => $classe->effectif_actuel,
                    'est_complete' => $classe->est_complete,
                ]
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }
    })->name('test.classe');
    
    // Existing test routes
    Route::get('/test/403', function () {
        abort(403, 'Accès non autorisé');
    })->name('test.403');

    Route::get('/test/404', function () {
        abort(404);
    })->name('test.404');

    Route::get('/test/500', function () {
        throw new \Exception('Erreur serveur de test');
    })->name('test.500');
}

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
    return view('home');
})->name('home');

// Authentification
Auth::routes([
    'register' => false, // Désactive l'enregistrement
    'verify' => true,    // Active la vérification d'email
]);

// Routes accessibles aux invités
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
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
    // Gestion du profil utilisateur
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::patch('/avatar', [AvatarController::class, 'update'])->name('avatar');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Les routes d'administration ont été déplacées vers routes/admin.php
    // pour une meilleure organisation et maintenabilité du code.

    // Routes pour les professeurs
    Route::middleware(['role:professeur'])->prefix('professeur')->name('professeur.')->group(function () {
        // Tableau de bord professeur
        Route::get('/dashboard', [ProfesseurDashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des absences
        Route::resource('absences', ProfesseurAbsenceController::class);
    });

    // Routes pour les assistants
    Route::middleware(['role:assistant'])->prefix('assistant')->name('assistant.')->group(function () {
        // Tableau de bord assistant
        Route::get('/dashboard', [AssistantDashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des élèves
        Route::resource('eleves', AssistantEleveController::class);
        
        // Gestion des absences
        Route::resource('absences', AssistantAbsenceController::class);
    });

    // Routes pour les élèves
    Route::middleware(['role:eleve'])->prefix('eleve')->name('eleve.')->group(function () {
        // Tableau de bord élève
        Route::get('/dashboard', [EleveDashboardController::class, 'index'])->name('dashboard');
        
        // Voir les absences
        Route::get('/absences', [EleveAbsenceController::class, 'index'])->name('absences.index');
        
        // Voir les paiements
        Route::get('/paiements', [ElevePaiementController::class, 'index'])->name('paiements.index');
    });
});

// Route de débogage pour vérifier les rôles (uniquement en développement)
if (app()->environment('local')) {
    Route::get('/debug/roles', function() {
        if (!auth()->check()) {
            return response()->json(['error' => 'Non connecté'], 401);
        }
        
        $user = auth()->user();
        
        // Debug output for the current user's role
        $output = [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->name ?? 'No role',
            'is_active' => $user->is_active,
            'role_type' => get_class($user->role),
            'is_admin' => $user->role === \App\Enums\RoleType::ADMIN ? 'Yes' : 'No',
            'role_value' => $user->role->value ?? null,
            'role_name' => $user->role->name ?? null,
            'role_label' => $user->role->label() ?? null,
        ];
        
        // Check if user is in the admin group
        $output['in_admin_group'] = $user->role === \App\Enums\RoleType::ADMIN ? 'Yes' : 'No';
        
        return response()->json($output);
        return [
            'user_id' => $user->id,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'is_admin' => $user->hasRole('admin'),
            'all_roles' => \Spatie\Permission\Models\Role::all()->pluck('name')
        ];
    })->middleware('auth');
}
