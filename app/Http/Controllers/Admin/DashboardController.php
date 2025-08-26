<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Models\Matiere;
use App\Enums\RoleType;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'administration
     */
    public function index()
    {
        $stats = [
            'niveaux' => Niveau::count(),
            'filieres' => Filiere::count(),
            'matieres' => Matiere::count(),
            'eleves' => User::where('role', RoleType::ELEVE->value)->count(),
            'professeurs' => User::where('role', RoleType::PROFESSEUR->value)->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
        ]);
    }
}
