<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Absence;
use App\Models\Paiement;
use App\Models\Salaire;
use App\Models\Matiere;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Enums\RoleType;
use App\Services\PdfExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RapportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role !== RoleType::ADMIN) {
            abort(403, 'Accès non autorisé');
        }

        return Inertia::render('Rapports/Index', [
            'stats' => $this->getGlobalStats(),
            'charts' => $this->getChartData(),
        ]);
    }

    public function absences(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        $filters = $request->only(['date_debut', 'date_fin', 'niveau_id', 'filiere_id', 'matiere_id']);
        
        return Inertia::render('Rapports/Absences', [
            'stats' => $this->getAbsenceStats($filters),
            'charts' => $this->getAbsenceChartData($filters),
            'filters' => $filters,
            'niveaux' => Niveau::all(['id', 'nom']),
            'filieres' => Filiere::all(['id', 'nom']),
            'matieres' => Matiere::actifs()->get(['id', 'nom']),
        ]);
    }

    public function paiements(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        $filters = $request->only(['date_debut', 'date_fin', 'statut', 'niveau_id']);
        
        return Inertia::render('Rapports/Paiements', [
            'stats' => $this->getPaymentStats($filters),
            'charts' => $this->getPaymentChartData($filters),
            'filters' => $filters,
            'niveaux' => Niveau::all(['id', 'nom']),
        ]);
    }

    public function salaires(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN])) {
            abort(403, 'Accès non autorisé');
        }

        $filters = $request->only(['mois', 'annee', 'professeur_id', 'matiere_id']);
        
        return Inertia::render('Rapports/Salaires', [
            'stats' => $this->getSalaryStats($filters),
            'charts' => $this->getSalaryChartData($filters),
            'filters' => $filters,
            'professeurs' => User::professeurs()->get(['id', 'name']),
            'matieres' => Matiere::actifs()->get(['id', 'nom']),
        ]);
    }

    public function exportAbsences(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        $filters = $request->only(['date_debut', 'date_fin', 'niveau_id', 'filiere_id', 'matiere_id']);
        
        $pdfService = new PdfExportService();
        $pdf = $pdfService->generateAbsenceReport($filters);
        
        $filename = 'rapport_absences_' . date('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function exportPaiements(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        $filters = $request->only(['date_debut', 'date_fin', 'statut', 'niveau_id']);
        
        $paiements = Paiement::with(['etudiant', 'matiere'])
            ->when($filters['date_debut'] ?? null, function ($query, $date) {
                return $query->where('date_paiement', '>=', $date);
            })
            ->when($filters['date_fin'] ?? null, function ($query, $date) {
                return $query->where('date_paiement', '<=', $date);
            })
            ->when($filters['statut'] ?? null, function ($query, $statut) {
                return $query->where('statut', $statut);
            })
            ->when($filters['niveau_id'] ?? null, function ($query, $niveauId) {
                return $query->whereHas('etudiant', function ($q) use ($niveauId) {
                    $q->where('niveau_id', $niveauId);
                });
            })
            ->orderBy('date_paiement', 'desc')
            ->get();

        $filename = 'rapport_paiements_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($paiements) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Étudiant', 'Matière', 'Montant', 'Date', 'Statut', 'Méthode'
            ]);

            foreach ($paiements as $paiement) {
                fputcsv($file, [
                    $paiement->etudiant->name,
                    $paiement->matiere->nom,
                    number_format($paiement->montant, 2) . ' DH',
                    $paiement->date_paiement->format('d/m/Y'),
                    $paiement->statut,
                    $paiement->methode_paiement,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportGlobal(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== RoleType::ADMIN) {
            abort(403, 'Accès non autorisé');
        }

        $pdfService = new PdfExportService();
        $pdf = $pdfService->generateGlobalReport();
        
        $filename = 'rapport_global_' . date('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function exportSalaires(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::PROFESSEUR])) {
            abort(403, 'Accès non autorisé');
        }

        $filters = $request->only(['mois', 'annee', 'professeur_id', 'matiere_id', 'statut']);
        
        $pdfService = new PdfExportService();
        $pdf = $pdfService->generateSalaryReport($filters);
        
        $filename = 'rapport_salaires_' . date('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function exportSalarySlip(Request $request, Salaire $salaire)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur peut accéder à ce bulletin
        if ($user->role === RoleType::PROFESSEUR && $salaire->professeur_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::PROFESSEUR])) {
            abort(403, 'Accès non autorisé');
        }

        $pdfService = new PdfExportService();
        $pdf = $pdfService->generateSalarySlip($salaire);
        
        $filename = 'bulletin_salaire_' . $salaire->professeur->name . '_' . $salaire->mois . '_' . $salaire->annee . '.pdf';
        
        return $pdf->download($filename);
    }

    private function getGlobalStats()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();

        return [
            'total_eleves' => User::eleves()->count(),
            'total_professeurs' => User::professeurs()->count(),
            'total_assistants' => User::assistants()->count(),
            'total_paiements_mois' => Paiement::where('date_paiement', '>=', $startOfMonth)->sum('montant'),
            'total_paiements_annee' => Paiement::where('date_paiement', '>=', $startOfYear)->sum('montant'),
            'absences_mois' => Absence::where('date_absence', '>=', $startOfMonth)->count(),
            'absences_justifiees_mois' => Absence::where('date_absence', '>=', $startOfMonth)
                ->where('justifie', true)->count(),
            'taux_presence_mois' => $this->calculateAttendanceRate($startOfMonth),
        ];
    }

    private function getChartData()
    {
        // Évolution des paiements sur 12 mois
        $paiementsData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $paiementsData[] = [
                'mois' => $date->format('M Y'),
                'montant' => Paiement::whereYear('date_paiement', $date->year)
                    ->whereMonth('date_paiement', $date->month)
                    ->sum('montant')
            ];
        }

        // Répartition des absences par type
        $absencesParType = Absence::select('type_absence', DB::raw('count(*) as total'))
            ->groupBy('type_absence')
            ->get();

        // Répartition des paiements par statut
        $paiementsParStatut = Paiement::select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->get();

        return [
            'evolution_paiements' => [
                'labels' => array_column($paiementsData, 'mois'),
                'datasets' => [[
                    'label' => 'Montant des paiements (DH)',
                    'data' => array_column($paiementsData, 'montant'),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.1
                ]]
            ],
            'absences_par_type' => [
                'labels' => $absencesParType->pluck('type_absence')->toArray(),
                'datasets' => [[
                    'data' => $absencesParType->pluck('total')->toArray(),
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                    ]
                ]]
            ],
            'paiements_par_statut' => [
                'labels' => $paiementsParStatut->pluck('statut')->toArray(),
                'datasets' => [[
                    'data' => $paiementsParStatut->pluck('total')->toArray(),
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                    ]
                ]]
            ]
        ];
    }

    private function getAbsenceStats($filters)
    {
        $query = Absence::query();

        $this->applyAbsenceFilters($query, $filters);

        $total = $query->count();
        $justifiees = (clone $query)->where('justifie', true)->count();
        $nonJustifiees = (clone $query)->where('justifie', false)->count();

        return [
            'total' => $total,
            'justifiees' => $justifiees,
            'non_justifiees' => $nonJustifiees,
            'taux_justification' => $total > 0 ? round(($justifiees / $total) * 100, 2) : 0,
        ];
    }

    private function getAbsenceChartData($filters)
    {
        $query = Absence::query();
        $this->applyAbsenceFilters($query, $filters);

        // Absences par jour sur 30 jours
        $absencesParJour = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = (clone $query)->whereDate('date_absence', $date)->count();
            $absencesParJour[] = [
                'date' => $date->format('d/m'),
                'total' => $count
            ];
        }

        // Absences par matière
        $absencesParMatiere = (clone $query)
            ->join('matieres', 'absences.matiere_id', '=', 'matieres.id')
            ->select('matieres.nom', DB::raw('count(*) as total'))
            ->groupBy('matieres.id', 'matieres.nom')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return [
            'evolution_quotidienne' => [
                'labels' => array_column($absencesParJour, 'date'),
                'datasets' => [[
                    'label' => 'Nombre d\'absences',
                    'data' => array_column($absencesParJour, 'total'),
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.1
                ]]
            ],
            'par_matiere' => [
                'labels' => $absencesParMatiere->pluck('nom')->toArray(),
                'datasets' => [[
                    'label' => 'Absences par matière',
                    'data' => $absencesParMatiere->pluck('total')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                ]]
            ]
        ];
    }

    private function getPaymentStats($filters)
    {
        $query = Paiement::query();
        $this->applyPaymentFilters($query, $filters);

        $total = $query->sum('montant');
        $valides = (clone $query)->where('statut', 'validé')->sum('montant');
        $en_attente = (clone $query)->where('statut', 'en_attente')->sum('montant');
        $annules = (clone $query)->where('statut', 'annulé')->sum('montant');

        return [
            'total' => $total,
            'valides' => $valides,
            'en_attente' => $en_attente,
            'annules' => $annules,
            'taux_validation' => $total > 0 ? round(($valides / $total) * 100, 2) : 0,
        ];
    }

    private function getPaymentChartData($filters)
    {
        $query = Paiement::query();
        $this->applyPaymentFilters($query, $filters);

        // Évolution des paiements sur 30 jours
        $paiementsParJour = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $montant = (clone $query)->whereDate('date_paiement', $date)->sum('montant');
            $paiementsParJour[] = [
                'date' => $date->format('d/m'),
                'montant' => $montant
            ];
        }

        // Paiements par méthode
        $paiementsParMethode = (clone $query)
            ->select('methode_paiement', DB::raw('count(*) as total'))
            ->groupBy('methode_paiement')
            ->get();

        return [
            'evolution_quotidienne' => [
                'labels' => array_column($paiementsParJour, 'date'),
                'datasets' => [[
                    'label' => 'Montant des paiements (DH)',
                    'data' => array_column($paiementsParJour, 'montant'),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'tension' => 0.1
                ]]
            ],
            'par_methode' => [
                'labels' => $paiementsParMethode->pluck('methode_paiement')->toArray(),
                'datasets' => [[
                    'data' => $paiementsParMethode->pluck('total')->toArray(),
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                    ]
                ]]
            ]
        ];
    }

    private function getSalaryStats($filters)
    {
        $query = Salaire::query();
        $this->applySalaryFilters($query, $filters);

        $total = $query->sum('montant_brut');
        $payes = (clone $query)->where('statut', 'payé')->sum('montant_brut');
        $en_attente = (clone $query)->where('statut', 'en_attente')->sum('montant_brut');

        return [
            'total' => $total,
            'payes' => $payes,
            'en_attente' => $en_attente,
            'taux_paiement' => $total > 0 ? round(($payes / $total) * 100, 2) : 0,
        ];
    }

    private function getSalaryChartData($filters)
    {
        $query = Salaire::query();
        $this->applySalaryFilters($query, $filters);

        // Salaires par professeur
        $salairesParProfesseur = (clone $query)
            ->join('users', 'salaires.professeur_id', '=', 'users.id')
            ->select('users.name', DB::raw('sum(salaires.montant_brut) as total'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Évolution des salaires sur 12 mois
        $salairesParMois = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $montant = (clone $query)
                ->whereYear('mois_periode', $date->year)
                ->whereMonth('mois_periode', $date->month)
                ->sum('montant_brut');
            $salairesParMois[] = [
                'mois' => $date->format('M Y'),
                'montant' => $montant
            ];
        }

        return [
            'par_professeur' => [
                'labels' => $salairesParProfesseur->pluck('name')->toArray(),
                'datasets' => [[
                    'label' => 'Salaires par professeur (DH)',
                    'data' => $salairesParProfesseur->pluck('total')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                ]]
            ],
            'evolution_mensuelle' => [
                'labels' => array_column($salairesParMois, 'mois'),
                'datasets' => [[
                    'label' => 'Total des salaires (DH)',
                    'data' => array_column($salairesParMois, 'montant'),
                    'borderColor' => 'rgb(168, 85, 247)',
                    'backgroundColor' => 'rgba(168, 85, 247, 0.1)',
                    'tension' => 0.1
                ]]
            ]
        ];
    }

    private function applyAbsenceFilters($query, $filters)
    {
        if ($filters['date_debut'] ?? null) {
            $query->where('date_absence', '>=', $filters['date_debut']);
        }
        if ($filters['date_fin'] ?? null) {
            $query->where('date_absence', '<=', $filters['date_fin']);
        }
        if ($filters['niveau_id'] ?? null) {
            $query->whereHas('etudiant', function ($q) use ($filters) {
                $q->where('niveau_id', $filters['niveau_id']);
            });
        }
        if ($filters['filiere_id'] ?? null) {
            $query->whereHas('etudiant', function ($q) use ($filters) {
                $q->where('filiere_id', $filters['filiere_id']);
            });
        }
        if ($filters['matiere_id'] ?? null) {
            $query->where('matiere_id', $filters['matiere_id']);
        }
    }

    private function applyPaymentFilters($query, $filters)
    {
        if ($filters['date_debut'] ?? null) {
            $query->where('date_paiement', '>=', $filters['date_debut']);
        }
        if ($filters['date_fin'] ?? null) {
            $query->where('date_paiement', '<=', $filters['date_fin']);
        }
        if ($filters['statut'] ?? null) {
            $query->where('statut', $filters['statut']);
        }
        if ($filters['niveau_id'] ?? null) {
            $query->whereHas('etudiant', function ($q) use ($filters) {
                $q->where('niveau_id', $filters['niveau_id']);
            });
        }
    }

    private function applySalaryFilters($query, $filters)
    {
        if ($filters['mois'] ?? null) {
            $query->whereMonth('mois_periode', $filters['mois']);
        }
        if ($filters['annee'] ?? null) {
            $query->whereYear('mois_periode', $filters['annee']);
        }
        if ($filters['professeur_id'] ?? null) {
            $query->where('professeur_id', $filters['professeur_id']);
        }
        if ($filters['matiere_id'] ?? null) {
            $query->where('matiere_id', $filters['matiere_id']);
        }
    }

    private function calculateAttendanceRate($startDate)
    {
        $totalSeances = 1000; // À calculer selon votre logique métier
        $absences = Absence::where('date_absence', '>=', $startDate)->count();
        
        if ($totalSeances === 0) return 100;
        
        return round((($totalSeances - $absences) / $totalSeances) * 100, 2);
    }
}
