<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Absence;
use App\Models\Paiement;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'administration
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Statistiques générales
        $stats = [
            'total_eleves' => User::where('role', 'eleve')->count(),
            'total_professeurs' => User::where('role', 'professeur')->count(),
            'total_matieres' => \App\Models\Matiere::count(),
            'paiements_du_mois' => Paiement::whereMonth('date_paiement', now()->month)
                ->whereYear('date_paiement', now()->year)
                ->sum('montant'),
            'taux_absences' => $this->calculateAbsenceRate(),
        ];

        // Derniers élèves inscrits
        $recentStudents = User::where('role', 'eleve')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'name', 'email', 'created_at']);
            
        // Derniers paiements
        $recentPayments = Paiement::with(['eleve', 'matiere'])
            ->orderBy('date_paiement', 'desc')
            ->take(5)
            ->get();
            
        // Dernières absences
        $recentAbsences = Absence::with(['eleve', 'matiere'])
            ->orderBy('date_absence', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentStudents' => $recentStudents,
            'recentPayments' => $recentPayments,
            'recentAbsences' => $recentAbsences,
        ]);
    }

    /**
     * Calcule le taux d'absences global
     *
     * @return float
     */
    private function calculateAbsenceRate(): float
    {
        $totalStudents = User::where('role', 'eleve')->count();
        
        if ($totalStudents === 0) {
            return 0.0;
        }

        $totalAbsences = Absence::count();
        $totalPossibleAttendances = $totalStudents * 30; // Estimation de 30 jours d'école par mois
        
        if ($totalPossibleAttendances === 0) {
            return 0;
        }
        
        return ($totalAbsences / $totalPossibleAttendances) * 100;
    }
}
