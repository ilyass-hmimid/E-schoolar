<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Constructeur du contrôleur
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('active');
    }
    
    /**
     * Redirige l'utilisateur vers le tableau de bord approprié selon son rôle
     */
    public function index()
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est actif
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.',
            ]);
        }
        
        // Récupérer la valeur du rôle de manière sécurisée
        $roleValue = null;
        if (is_object($user->role) && isset($user->role->value)) {
            $roleValue = $user->role->value;
        } elseif (is_numeric($user->role)) {
            $roleValue = $user->role;
        }
        
        // Rediriger vers le contrôleur approprié selon le rôle
        switch ($roleValue) {
            case RoleType::ADMIN->value:
                return redirect()->route('admin.dashboard');
                
            case RoleType::PROFESSEUR->value:
                return redirect()->route('professeur.dashboard');
                
            case RoleType::ASSISTANT->value:
                return redirect()->route('assistant.dashboard');
                
            case RoleType::ELEVE->value:
                return redirect()->route('eleve.dashboard');
                
            default:
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Rôle utilisateur non reconnu. Veuillez contacter l\'administrateur.',
                ]);
        }
    }

    /**
     * Affiche le tableau de bord professeur
     */
    public function professeur()
    {
        $user = auth()->user();
        $moisCourant = now()->format('Y-m');
        
        // Statistiques du professeur
        $stats = [
            'matieres_enseignees' => $user->matieresEnseignees()->count(),
            'cours_ce_mois' => $user->cours()
                ->whereMonth('date_debut', now()->month)
                ->count(),
            'salaire_mois' => $user->calculerSalaire($moisCourant) ?? 0,
            'absences_aujourd_hui' => Absence::where('professeur_id', $user->id)
                ->whereDate('date_absence', now()->toDateString())
                ->count(),
        ];

        // Récupérer les matières enseignées par le professeur avec eager loading
        $matieres = $user->matieresEnseignees()
            ->with([
                'niveau:id,nom',
                'filiere:id,nom',
                'cours' => function($query) use ($user) {
                    $query->where('professeur_id', $user->id)
                        ->where('date_fin', '>=', now())
                        ->orderBy('date_debut')
                        ->limit(1);
                }
            ])
            ->select('matieres.id', 'matieres.nom', 'niveau_id', 'filiere_id')
            ->get()
            ->map(function($matiere) {
                return [
                    'id' => $matiere->id,
                    'nom' => $matiere->nom,
                    'niveau' => $matiere->niveau->nom ?? 'N/A',
                    'filiere' => $matiere->filiere->nom ?? 'N/A',
                    'prochain_cours' => $matiere->cours->first()?->date_debut->format('d/m/Y H:i') ?? 'Aucun cours prévu'
                ];
            });

        // Prochains cours avec eager loading optimisé
        $prochainsCours = $user->cours()
            ->where('date_debut', '>=', now())
            ->orderBy('date_debut')
            ->limit(5)
            ->with([
                'matiere:id,nom',
                'salle:id,nom',
                'classe:id,nom',
                'absences' => function($query) {
                    $query->select('id', 'cours_id', 'etudiant_id', 'justifiee')
                        ->whereDate('date_absence', now()->toDateString());
                }
            ])
            ->select('id', 'matiere_id', 'salle_id', 'classe_id', 'date_debut', 'date_fin')
            ->get()
            ->map(function($cours) {
                return [
                    'id' => $cours->id,
                    'matiere' => $cours->matiere->nom,
                    'date_debut' => $cours->date_debut->format('d/m/Y H:i'),
                    'date_fin' => $cours->date_fin->format('H:i'),
                    'salle' => $cours->salle->nom ?? 'N/A',
                    'classe' => $cours->classe->nom ?? 'N/A',
                    'absents' => $cours->absences->count()
                ];
            });
            
        // Dernières notes saisies avec eager loading optimisé
        $dernieresNotes = $user->notes()
            ->with([
                'etudiant:id,name',
                'matiere:id,nom',
                'cours:id,date_debut'
            ])
            ->select('id', 'etudiant_id', 'matiere_id', 'cours_id', 'valeur', 'type', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($note) {
                return [
                    'id' => $note->id,
                    'etudiant' => $note->etudiant->name,
                    'matiere' => $note->matiere->nom,
                    'valeur' => $note->valeur,
                    'type' => $note->type,
                    'date' => $note->created_at->format('d/m/Y'),
                    'cours_date' => $note->cours ? $note->cours->date_debut->format('d/m/Y') : 'N/A'
                ];
            });
            
        // Dernières absences enregistrées avec eager loading optimisé
        $dernieresAbsences = Absence::where('professeur_id', $user->id)
            ->with([
                'etudiant:id,name',
                'matiere:id,nom',
                'cours:id,date_debut',
                'justification:id,motif,piece_jointe'
            ])
            ->select('id', 'etudiant_id', 'matiere_id', 'cours_id', 'date_absence', 'justifiee', 'justification_id')
            ->orderBy('date_absence', 'desc')
            ->limit(5)
            ->get()
            ->map(function($absence) {
                return [
                    'id' => $absence->id,
                    'etudiant' => $absence->etudiant->name,
                    'matiere' => $absence->matiere->nom,
                    'date' => $absence->date_absence->format('d/m/Y'),
                    'justifiee' => $absence->justifiee ? 'Oui' : 'Non',
                    'cours_date' => $absence->cours ? $absence->cours->date_debut->format('d/m/Y H:i') : 'N/A',
                    'justification' => $absence->justification ? 'Oui' : 'Non'
                ];
            });
        
        return Inertia::render('Dashboard/Professeur/Index', [
            'stats' => $stats,
            'matieres' => $matieres,
            'prochainsCours' => $prochainsCours,
            'dernieresNotes' => $dernieresNotes,
            'dernieresAbsences' => $dernieresAbsences,
        ]);
    }

    /**
     * Affiche le tableau de bord assistant
     */
    public function assistant()
    {
        // Dernières inscriptions
        $dernieresInscriptions = \App\Models\Inscription::with(['etudiant', 'classe'])
            ->orderBy('date_inscription', 'desc')
            ->limit(5)
            ->get()
            ->map(function($inscription) {
                return [
                    'id' => $inscription->id,
                    'etudiant' => $inscription->etudiant->name,
                    'classe' => $inscription->classe->nom,
                    'date' => $inscription->date_inscription->format('d/m/Y'),
                    'montant' => number_format($inscription->montant_inscription, 2, ',', ' ') . ' DH',
                ];
            });
            
        // Derniers paiements
        $derniersPaiements = Paiement::with(['etudiant'])
            ->orderBy('date_paiement', 'desc')
            ->limit(5)
            ->get()
            ->map(function($paiement) {
                return [
                    'id' => $paiement->id,
                    'etudiant' => $paiement->etudiant->name,
                    'montant' => number_format($paiement->montant, 2, ',', ' ') . ' DH',
                    'date' => $paiement->date_paiement->format('d/m/Y'),
                    'statut' => $paiement->statut,
                ];
            });
            
        // Dernières absences
        $dernieresAbsences = Absence::with(['etudiant', 'matiere'])
            ->orderBy('date_absence', 'desc')
            ->limit(5)
            ->get()
            ->map(function($absence) {
                return [
                    'id' => $absence->id,
                    'etudiant' => $absence->etudiant->name,
                    'matiere' => $absence->matiere->nom,
                    'date' => $absence->date_absence->format('d/m/Y'),
                    'justifiee' => $absence->justifiee ? 'Oui' : 'Non',
                ];
            });
            
        // Prochains événements
        $prochainsEvenements = \App\Models\Evenement::where('date_debut', '>=', now())
            ->orderBy('date_debut')
            ->limit(5)
            ->get()
            ->map(function($evenement) {
                return [
                    'id' => $evenement->id,
                    'titre' => $evenement->titre,
                    'date_debut' => $evenement->date_debut->format('d/m/Y H:i'),
                    'date_fin' => $evenement->date_fin->format('d/m/Y H:i'),
                    'lieu' => $evenement->lieu ?? 'Non spécifié',
                ];
            });
        
        return Inertia::render('Dashboard/Assistant/Index', [
            'dernieresInscriptions' => $dernieresInscriptions,
            'derniersPaiements' => $derniersPaiements,
            'dernieresAbsences' => $dernieresAbsences,
            'prochainsEvenements' => $prochainsEvenements,
        ]);
    }

    /**
     * Affiche le tableau de bord élève
     */
    public function eleve()
    {
        $user = auth()->user();
        
        // Dernières notes
        $dernieresNotes = $user->notes()
            ->with(['matiere'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($note) {
                return [
                    'id' => $note->id,
                    'matiere' => $note->matiere->nom,
                    'valeur' => $note->valeur,
                    'type' => $note->type,
                    'date' => $note->created_at->format('d/m/Y'),
                    'appreciation' => $note->appreciation ?? 'Aucune appréciation',
                ];
            });
            
        // Prochains cours
        $prochainsCours = $user->cours()
            ->where('date_debut', '>=', now())
            ->orderBy('date_debut')
            ->limit(5)
            ->with(['matiere', 'salle', 'professeur'])
            ->get()
            ->map(function($cours) {
                return [
                    'id' => $cours->id,
                    'matiere' => $cours->matiere->nom,
                    'professeur' => $cours->professeur->name,
                    'date' => $cours->date_debut->format('d/m/Y'),
                    'heure_debut' => $cours->date_debut->format('H:i'),
                    'heure_fin' => $cours->date_fin->format('H:i'),
                    'salle' => $cours->salle->nom ?? 'Non spécifié',
                ];
            });
            
        // Dernières absences
        $absences = $user->absences()
            ->with(['matiere'])
            ->orderBy('date_absence', 'desc')
            ->limit(5)
            ->get()
            ->map(function($absence) {
                return [
                    'id' => $absence->id,
                    'matiere' => $absence->matiere->nom,
                    'date' => $absence->date_absence->format('d/m/Y'),
                    'justifiee' => $absence->justifiee ? 'Oui' : 'Non',
                    'commentaire' => $absence->commentaire ?? 'Aucun commentaire',
                ];
            });
            
        // Prochains devoirs
        $prochainsDevoirs = $user->devoirs()
            ->where('date_limite', '>=', now())
            ->orderBy('date_limite')
            ->limit(5)
            ->with('matiere')
            ->get()
            ->map(function($devoir) {
                return [
                    'id' => $devoir->id,
                    'titre' => $devoir->titre,
                    'matiere' => $devoir->matiere->nom,
                    'date_limite' => $devoir->date_limite->format('d/m/Y H:i'),
                    'rendu' => $devoir->pivot->date_rendu ? 'Oui' : 'Non',
                    'note' => $devoir->pivot->note ?? 'Non noté',
                ];
            });
            
        return Inertia::render('Dashboard/Eleve/Index', [
            'dernieresNotes' => $dernieresNotes,
            'prochainsCours' => $prochainsCours,
            'absences' => $absences,
            'prochainsDevoirs' => $prochainsDevoirs,
        ]);
    }

    /**
     * Récupère les inscriptions par mois pour un nombre de mois donné
     */
    private function getInscriptionsParMois(int $nbMois): array
    {
        $dateDebut = now()->subMonths($nbMois - 1)->startOfMonth();
        $dateFin = now()->endOfMonth();
        
        $inscriptions = DB::table('inscriptions')
            ->select(
                DB::raw('YEAR(DateInsc) as annee'),
                DB::raw('MONTH(DateInsc) as mois'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('DateInsc', [$dateDebut, $dateFin])
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();
            
        return $this->formaterDonneesMensuelles($inscriptions, $dateDebut, $dateFin);
    }
    
    /**
     * Récupère les paiements par mois pour un nombre de mois donné
     */
    private function getPaiementsParMois(int $nbMois): array
    {
        $dateDebut = now()->subMonths($nbMois - 1)->startOfMonth();
        $dateFin = now()->endOfMonth();
        
        $paiements = DB::table('paiements')
            ->select(
                DB::raw('YEAR(date_paiement) as annee'),
                DB::raw('MONTH(date_paiement) as mois'),
                DB::raw('SUM(montant) as total')
            )
            ->where('statut', 'valide')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();
            
        return $this->formaterDonneesMensuelles($paiements, $dateDebut, $dateFin);
    }
    
    /**
     * Formate les données mensuelles pour les graphiques
     */
    private function formaterDonneesMensuelles($donnees, $dateDebut, $dateFin): array
    {
        $periodes = collect(CarbonPeriod::create($dateDebut, '1 month', $dateFin));
        
        $donneesParMois = $donnees->mapWithKeys(function($item) {
            return [
                $item->annee . '-' . str_pad($item->mois, 2, '0', STR_PAD_LEFT) => $item->total ?? 0
            ];
        });
        
        return $periodes->map(function($date) use ($donneesParMois) {
            $cle = $date->year . '-' . str_pad($date->month, 2, '0', STR_PAD_LEFT);
            return [
                'mois' => $date->translatedFormat('M Y'),
                'valeur' => $donneesParMois[$cle] ?? 0,
            ];
        })->toArray();
    }
}
