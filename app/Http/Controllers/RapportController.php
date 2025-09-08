<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Absence;
use App\Models\Paiement;
use App\Models\Matiere;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Enums\RoleType;
use App\Services\PdfExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RapportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role !== RoleType::ADMIN) {
            abort(403, 'Accès non autorisé');
        }

        $stats = $this->getGlobalStats();
        $charts = $this->getChartData();
        
        return view('admin.rapports.index', compact('stats', 'charts'));
    }

    public function absences(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        $filters = $request->only(['date_debut', 'date_fin', 'niveau_id', 'filiere_id', 'matiere_id']);
        
        $stats = $this->getAbsenceStats($filters);
        $charts = $this->getAbsenceChartData($filters);
        $niveaux = Niveau::all(['id', 'nom']);
        $filieres = Filiere::all(['id', 'nom']);
        $matieres = Matiere::actifs()->get(['id', 'nom']);
        
        return view('admin.rapports.absences', compact('stats', 'charts', 'filters', 'niveaux', 'filieres', 'matieres'));
    }

    public function paiements(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        $filters = $request->only(['date_debut', 'date_fin', 'statut', 'niveau_id']);
        
        $stats = $this->getPaymentStats($filters);
        $charts = $this->getPaymentChartData($filters);
        $niveaux = Niveau::all(['id', 'nom']);
        
        return view('admin.rapports.paiements', compact('stats', 'charts', 'filters', 'niveaux'));
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

        $filters = $request->only(['date_debut', 'date_fin', 'statut', 'niveau_id', 'format', 'search']);
        
        $query = Paiement::with(['etudiant', 'matiere', 'pack']);
        
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
        
        if (!empty($filters['niveau_id'])) {
            $query->whereHas('etudiant', function($q) use ($filters) {
                $q->where('niveau_id', $filters['niveau_id']);
            });
        }
        
        // Filtre de recherche
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('reference_paiement', 'like', "%{$search}%")
                  ->orWhereHas('etudiant', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Si l'utilisateur est un assistant, ne voir que ses paiements
        if ($user->role === RoleType::ASSISTANT) {
            $query->where('assistant_id', $user->id);
        }
        
        $paiements = $query->orderBy('date_paiement', 'desc')->get();

        $exportService = new \App\Services\PaiementExportService();
        
        $format = strtolower($filters['format'] ?? 'pdf');
        
        if ($format === 'excel') {
            return $exportService->exportToExcel($paiements);
        }
        
        // Par défaut, on exporte en PDF
        return $exportService->exportToPdf($paiements);
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

    /**
     * Récupère les statistiques globales pour le tableau de bord
     */
    private function getGlobalStats()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();

        // Données pour le graphique d'évolution des inscriptions
        $inscriptions = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::where('role', RoleType::ETUDIANT)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
                
            $labels[] = $date->format('M Y');
            $inscriptions[] = $count;
        }
        
        // Données pour le graphique des filières
        $filieres = Filiere::withCount('etudiants')
            ->orderBy('etudiants_count', 'desc')
            ->limit(5)
            ->get();
            
        $filieresLabels = $filieres->pluck('nom')->toArray();
        $filieresData = $filieres->pluck('etudiants_count')->toArray();

        return [
            // Statistiques générales
            'total_eleves' => User::eleves()->count(),
            'total_professeurs' => User::professeurs()->count(),
            'total_assistants' => User::assistants()->count(),
            'total_paiements_mois' => Paiement::where('date_paiement', '>=', $startOfMonth)->sum('montant'),
            'total_paiements_annee' => Paiement::where('date_paiement', '>=', $startOfYear)->sum('montant'),
            'absences_mois' => Absence::where('date_absence', '>=', $startOfMonth)->count(),
            'absences_justifiees_mois' => Absence::where('date_absence', '>=', $startOfMonth)
                ->where('justifie', true)->count(),
            'taux_presence_mois' => $this->calculateAttendanceRate($startOfMonth),
            
            // Données pour les graphiques
            'inscriptions' => [
                'labels' => $labels,
                'data' => $inscriptions
            ],
            'filieres' => [
                'labels' => $filieresLabels,
                'data' => $filieresData
            ]
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

        // Calculer le taux de présence
        $totalSeances = DB::table('seances')
            ->when(!empty($filters['date_debut']), function($q) use ($filters) {
                $q->where('date_seance', '>=', $filters['date_debut']);
            })
            ->when(!empty($filters['date_fin']), function($q) use ($filters) {
                $q->where('date_seance', '<=', $filters['date_fin']);
            })
            ->when(!empty($filters['matiere_id']), function($q) use ($filters) {
                $q->where('matiere_id', $filters['matiere_id']);
            })
            ->count();
            
        $tauxPresence = $totalSeances > 0 ? round((1 - ($total / $totalSeances)) * 100, 1) : 100;

        return [
            'total' => $total,
            'justifiees' => $justifiees,
            'non_justifiees' => $nonJustifiees,
            'taux_justification' => $total > 0 ? round(($justifiees / $total) * 100, 1) : 0,
            'taux_presence' => $tauxPresence,
            'total_seances' => $totalSeances
        ];
    }

    private function getAbsenceChartData($filters)
    {
        $query = Absence::query();
        $this->applyAbsenceFilters($query, $filters);

        // Absences par jour sur 30 jours
        $absencesParJour = [];
        $now = Carbon::now();
        
        for ($i = 29; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $count = (clone $query)
                ->whereDate('date_absence', $date->format('Y-m-d'))
                ->count();
                
            $absencesParJour[] = [
                'date' => $date->format('d/m'),
                'count' => $count
            ];
        }
        
        // Absences par type
        $absencesParType = (clone $query)
            ->select('type_absence', DB::raw('count(*) as total'))
            ->groupBy('type_absence')
            ->pluck('total', 'type_absence')
            ->toArray();
            
        // Absences par matière (top 5)
        $absencesParMatiere = (clone $query)
            ->select('matieres.nom', DB::raw('count(absences.id) as total'))
            ->join('matieres', 'absences.matiere_id', '=', 'matieres.id')
            ->groupBy('matieres.nom')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'matiere' => $item->nom,
                    'total' => $item->total
                ];
            });
            
        return [
            'par_jour' => [
                'labels' => array_column($absencesParJour, 'date'),
                'datasets' => [[
                    'label' => 'Nombre d\'absences',
                    'data' => array_column($absencesParJour, 'count'),
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.1
                ]]
            ],
            'par_type' => [
                'labels' => array_keys($absencesParType),
                'datasets' => [[
                    'data' => array_values($absencesParType),
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)'
                    ]
                ]]
            ],
            'par_matiere' => [
                'labels' => $absencesParMatiere->pluck('matiere')->toArray(),
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
        $query = Paiement::with(['etudiant', 'pack']);
        $this->applyPaymentFilters($query, $filters);

        $total = $query->sum('montant');
        $totalPaiements = $query->count();
        $moyenne = $totalPaiements > 0 ? $total / $totalPaiements : 0;
        
        $payes = (clone $query)->where('statut', 'paye')->sum('montant');
        $enAttente = (clone $query)->where('statut', 'en_attente')->sum('montant');
        $annules = (clone $query)->where('statut', 'annule')->sum('montant');
        
        // Derniers paiements pour le tableau de bord
        $derniersPaiements = (clone $query)
            ->orderBy('date_paiement', 'desc')
            ->limit(5)
            ->get()
            ->map(function($paiement) {
                return [
                    'etudiant' => $paiement->etudiant->name ?? 'N/A',
                    'montant' => $paiement->montant,
                    'date' => $paiement->date_paiement->format('d/m/Y'),
                    'statut' => $paiement->statut,
                    'pack' => $paiement->pack->nom ?? 'N/A'
                ];
            });

        return [
            'total' => $total,
            'moyenne' => $moyenne,
            'total_paiements' => $totalPaiements,
            'payes' => $payes,
            'en_attente' => $enAttente,
            'annules' => $annules,
            'derniers_paiements' => $derniersPaiements,
            'taux_paiement' => $total > 0 ? round(($payes / $total) * 100, 1) : 0
        ];
    }

    private function getPaymentChartData($filters)
    {
        $query = Paiement::query();
        $this->applyPaymentFilters($query, $filters);

        // Évolution des paiements sur 30 jours
        $paiementsParJour = [];
        $now = Carbon::now();
        
        for ($i = 29; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $total = (clone $query)
                ->whereDate('date_paiement', $date->format('Y-m-d'))
                ->sum('montant');
                
            $paiementsParJour[] = [
                'date' => $date->format('d/m'),
                'montant' => $total
            ];
        }
        
        // Paiements par statut
        $paiementsParStatut = (clone $query)
            ->select('statut', DB::raw('sum(montant) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut')
            ->toArray();
            
        // Paiements par méthode (top 5)
        $paiementsParMethode = (clone $query)
            ->select('mode_paiement', DB::raw('sum(montant) as total'))
            ->groupBy('mode_paiement')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'methode' => $item->mode_paiement,
                    'total' => $item->total
                ];
            });
            
        return [
            'par_jour' => [
                'labels' => array_column($paiementsParJour, 'date'),
                'datasets' => [[
                    'label' => 'Montant des paiements (DH)',
                    'data' => array_column($paiementsParJour, 'montant'),
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.1
                ]]
            ],
            'par_statut' => [
                'labels' => array_keys($paiementsParStatut),
                'datasets' => [[
                    'data' => array_values($paiementsParStatut),
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ]
                ]]
            ],
            'par_methode' => [
                'labels' => $paiementsParMethode->pluck('methode')->toArray(),
                'datasets' => [[
                    'label' => 'Montant par méthode (DH)',
                    'data' => $paiementsParMethode->pluck('total')->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)'
                    ]
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
