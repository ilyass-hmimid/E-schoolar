<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Models\Absence;
use App\Models\Paiement;
use App\Models\Salaire;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PdfExportService
{
    /**
     * Générer un rapport PDF global
     */
    public function generateGlobalReport($data = [])
    {
        $stats = $this->getGlobalStats();
        
        $html = view('pdf.global-report', [
            'stats' => $stats,
            'data' => $data,
            'generated_at' => Carbon::now()->format('d/m/Y H:i'),
        ])->render();

        return Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);
    }

    /**
     * Générer un rapport PDF des absences
     */
    public function generateAbsenceReport($filters = [])
    {
        $query = Absence::with(['etudiant', 'matiere', 'professeur', 'assistant']);
        
        // Appliquer les filtres
        if (!empty($filters['date_debut'])) {
            $query->where('date_absence', '>=', $filters['date_debut']);
        }
        if (!empty($filters['date_fin'])) {
            $query->where('date_absence', '<=', $filters['date_fin']);
        }
        if (!empty($filters['niveau_id'])) {
            $query->whereHas('etudiant', function($q) use ($filters) {
                $q->where('niveau_id', $filters['niveau_id']);
            });
        }
        if (!empty($filters['filiere_id'])) {
            $query->whereHas('etudiant', function($q) use ($filters) {
                $q->where('filiere_id', $filters['filiere_id']);
            });
        }
        if (!empty($filters['matiere_id'])) {
            $query->where('matiere_id', $filters['matiere_id']);
        }

        $absences = $query->orderBy('date_absence', 'desc')->get();
        
        // Calculer les statistiques
        $stats = [
            'total' => $absences->count(),
            'justifiees' => $absences->where('justifiee', true)->count(),
            'non_justifiees' => $absences->where('justifiee', false)->count(),
            'taux_justification' => $absences->count() > 0 ? 
                round(($absences->where('justifiee', true)->count() / $absences->count()) * 100, 2) : 0
        ];

        $html = view('pdf.absence-report', [
            'absences' => $absences,
            'stats' => $stats,
            'filters' => $filters,
            'generated_at' => Carbon::now()->format('d/m/Y H:i'),
        ])->render();

        return Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);
    }

    /**
     * Générer un rapport PDF des paiements
     */
    public function generatePaymentReport($filters = [])
    {
        $query = Paiement::with(['etudiant', 'matiere']);
        
        // Appliquer les filtres
        if (!empty($filters['date_debut'])) {
            $query->where('date_paiement', '>=', $filters['date_debut']);
        }
        if (!empty($filters['date_fin'])) {
            $query->where('date_paiement', '<=', $filters['date_fin']);
        }
        if (!empty($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }

        $paiements = $query->orderBy('date_paiement', 'desc')->get();
        
        // Calculer les statistiques
        $stats = [
            'total' => $paiements->count(),
            'montant_total' => $paiements->sum('montant'),
            'valides' => $paiements->where('statut', 'valide')->count(),
            'en_attente' => $paiements->where('statut', 'en_attente')->count(),
            'annules' => $paiements->where('statut', 'annule')->count(),
        ];

        $html = view('pdf.payment-report', [
            'paiements' => $paiements,
            'stats' => $stats,
            'filters' => $filters,
            'generated_at' => Carbon::now()->format('d/m/Y H:i'),
        ])->render();

        return Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);
    }

    /**
     * Générer un rapport PDF des salaires
     */
    public function generateSalaryReport($filters = [])
    {
        $query = Salaire::with(['professeur', 'matiere']);
        
        // Appliquer les filtres
        if (!empty($filters['mois'])) {
            $query->where('mois', $filters['mois']);
        }
        if (!empty($filters['annee'])) {
            $query->where('annee', $filters['annee']);
        }
        if (!empty($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }

        $salaires = $query->orderBy('created_at', 'desc')->get();
        
        // Calculer les statistiques
        $stats = [
            'total' => $salaires->count(),
            'montant_brut_total' => $salaires->sum('montant_brut'),
            'montant_net_total' => $salaires->sum('montant_net'),
            'payes' => $salaires->where('statut', 'paye')->count(),
            'en_attente' => $salaires->where('statut', 'en_attente')->count(),
        ];

        $html = view('pdf.salary-report', [
            'salaires' => $salaires,
            'stats' => $stats,
            'filters' => $filters,
            'generated_at' => Carbon::now()->format('d/m/Y H:i'),
        ])->render();

        return Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);
    }

    /**
     * Générer un bulletin de salaire individuel
     */
    public function generateSalarySlip(Salaire $salaire)
    {
        $html = view('pdf.salary-slip', [
            'salaire' => $salaire,
            'generated_at' => Carbon::now()->format('d/m/Y H:i'),
        ])->render();

        return Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);
    }

    /**
     * Obtenir les statistiques globales
     */
    private function getGlobalStats()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();

        return [
            'total_eleves' => User::where('role', 'eleve')->count(),
            'total_professeurs' => User::where('role', 'professeur')->count(),
            'total_assistants' => User::where('role', 'assistant')->count(),
            'paiements_mois' => Paiement::where('date_paiement', '>=', $startOfMonth)
                ->where('statut', 'valide')
                ->sum('montant'),
            'paiements_annee' => Paiement::where('date_paiement', '>=', $startOfYear)
                ->where('statut', 'valide')
                ->sum('montant'),
            'absences_mois' => Absence::where('date_absence', '>=', $startOfMonth)->count(),
            'absences_justifiees_mois' => Absence::where('date_absence', '>=', $startOfMonth)
                ->where('justifiee', true)
                ->count(),
            'taux_presence_mois' => $this->calculateAttendanceRate($startOfMonth),
        ];
    }

    /**
     * Calculer le taux de présence
     */
    private function calculateAttendanceRate($startDate)
    {
        $totalDays = Carbon::now()->diffInDays($startDate) + 1;
        $totalStudents = User::where('role', 'eleve')->count();
        $expectedAttendance = $totalDays * $totalStudents;
        
        if ($expectedAttendance == 0) return 100;
        
        $absences = Absence::where('date_absence', '>=', $startDate)->count();
        $actualAttendance = $expectedAttendance - $absences;
        
        return round(($actualAttendance / $expectedAttendance) * 100, 2);
    }
}
