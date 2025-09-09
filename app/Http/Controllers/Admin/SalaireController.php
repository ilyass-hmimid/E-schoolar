<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salaire;
use App\Models\User;
use App\Models\Paiement;
use App\Models\Professeur;
use App\Exports\SalairesExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class SalaireController extends Controller
{
    /**
     * Affiche la liste des salaires
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    /**
     * Génère les salaires mensuels pour tous les professeurs
     * 
     * @param string $mois Le mois au format 'YYYY-MM' (optionnel, par défaut le mois en cours)
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateSalairesMensuels($mois = null)
    {
        $mois = $mois ?? now()->format('Y-m');
        $dateDebut = Carbon::parse($mois . '-01')->startOfMonth();
        $dateFin = $dateDebut->copy()->endOfMonth();
        
        $resultats = [];
        $professeurs = Professeur::with('matieres.eleves')->get();
        
        foreach ($professeurs as $professeur) {
            $salaire = $professeur->calculateSalaireMensuel($mois);
            
            // Enregistrer le salaire dans la base de données
            $salaireEnregistre = Salaire::updateOrCreate(
                [
                    'professeur_id' => $professeur->id,
                    'periode' => $dateDebut->format('Y-m'),
                ],
                [
                    'reference' => 'SAL-' . strtoupper(Str::random(8)),
                    'salaire_brut' => $salaire['salaire_total'],
                    'salaire_net' => $salaire['salaire_total'], // À ajuster avec les retenues si nécessaire
                    'montant' => $salaire['salaire_total'],
                    'statut' => 'en_attente',
                    'date_paiement' => null,
                    'periode_debut' => $dateDebut,
                    'periode_fin' => $dateFin,
                    'details' => json_encode($salaire['details']),
                    'paye_par' => auth()->id(),
                ]
            );
            
            $resultats[] = [
                'professeur' => $professeur->nom_complet,
                'salaire' => $salaire['salaire_total'],
                'details' => $salaire['details'],
                'salaire_id' => $salaireEnregistre->id,
            ];
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Calcul des salaires effectué avec succès',
            'data' => $resultats,
            'mois' => $dateDebut->format('F Y'),
        ]);
    }
    
    /**
     * Affiche la liste des salaires
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Salaire::with(['professeur', 'payePar'])
            ->latest('periode');
            
        // Filtrage par professeur
        if ($request->has('professeur_id') && $request->professeur_id) {
            $query->where('professeur_id', $request->professeur_id);
        }
        
        // Filtrage par statut
        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }
        
        // Filtrage par période
        if ($request->has('periode') && $request->periode) {
            $query->where('periode', $request->periode);
        }
        
        // Filtrage par type de paiement
        if ($request->has('type_paiement') && $request->type_paiement) {
            $query->where('type_paiement', $request->type_paiement);
        }
        
        // Récupération des statistiques
        $stats = [
            'total_professeurs' => User::where('role', 'professeur')->where('status', 'actif')->count(),
            'salaires_payes' => Salaire::where('statut', 'paye')->count(),
            'en_attente' => Salaire::where('statut', 'en_attente')->count(),
            'en_retard' => Salaire::where('statut', 'retard')->count(),
        ];
        
        // Calcul des pourcentages
        $total = $stats['salaires_payes'] + $stats['en_attente'] + $stats['en_retard'];
        $stats['pourcentage_paye'] = $total > 0 ? round(($stats['salaires_payes'] / $total) * 100) : 0;
        $stats['pourcentage_attente'] = $total > 0 ? round(($stats['en_attente'] / $total) * 100) : 0;
        $stats['pourcentage_retard'] = $total > 0 ? round(($stats['en_retard'] / $total) * 100) : 0;
        
        // Récupération des salaires avec pagination
        $salaires = $query->paginate(15);
        
        // Liste des professeurs pour le filtre
        $professeurs = User::where('role', 'professeur')
            ->where('status', 'actif')
            ->orderBy('name')
            ->orderBy('prenom')
            ->get();
        
        // Liste des périodes disponibles
        $periodes = Salaire::select('periode')
            ->distinct()
            ->orderBy('periode', 'desc')
            ->pluck('periode')
            ->map(function($date) {
                return [
                    'value' => $date,
                    'label' => Carbon::parse($date)->format('F Y')
                ];
            });
        
        return view('admin.salaires.index', compact('salaires', 'professeurs', 'stats', 'periodes'));
    }

    /**
     * Affiche le formulaire de création d'un salaire
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $professeurs = User::where('role', 'professeur')
            ->where('status', 'actif')
            ->orderBy('name')
            ->orderBy('prenom')
            ->get()
            ->map(function($professeur) {
                $professeur->nom_complet = $professeur->name . ' ' . $professeur->prenom;
                return $professeur;
            });
            
        return view('admin.salaires.form', [
            'salaire' => new Salaire(),
            'professeurs' => $professeurs,
            'title' => 'Créer un nouveau salaire',
            'submitText' => 'Créer',
            'method' => 'POST',
            'route' => route('admin.salaires.store')
        ]);
    }

    /**
     * Calcule le salaire d'un professeur pour une période donnée
     *
     * @param  int  $professeurId
     * @param  string  $periode
     * @return array
     */
    private function calculerSalaire($professeurId, $periode)
    {
        $professeur = User::findOrFail($professeurId);
        
        // Calcul du salaire de base (exemple: selon le nombre d'heures et le taux horaire)
        $nbHeures = 160; // Valeur par défaut, à adapter selon la logique métier
        $tauxHoraire = 100; // Valeur par défaut, à adapter selon la logique métier
        $salaireBrut = $nbHeures * $tauxHoraire;
        
        // Calcul des primes (exemples)
        $primeAnciennete = $this->calculerPrimeAnciennete($professeur, $periode);
        $primeRendement = $this->calculerPrimeRendement($professeur, $periode);
        $indemniteTransport = 200; // Montant fixe ou calculé
        $autresPrimes = 0; // À calculer selon la logique métier
        
        // Calcul des retenues
        $cnss = $this->calculerCNSS($salaireBrut);
        $ir = $this->calculerIR($salaireBrut);
        $retenuesDiverses = 0; // À calculer selon la logique métier
        
        // Calcul du salaire net
        $totalBrut = $salaireBrut + $primeAnciennete + $primeRendement + $indemniteTransport + $autresPrimes;
        $totalRetenues = $cnss + $ir + $retenuesDiverses;
        $salaireNet = max(0, $totalBrut - $totalRetenues);
        
        return [
            'professeur_id' => $professeur->id,
            'periode' => $periode,
            'nb_heures' => $nbHeures,
            'taux_horaire' => $tauxHoraire,
            'salaire_brut' => $salaireBrut,
            'prime_anciennete' => $primeAnciennete,
            'prime_rendement' => $primeRendement,
            'indemnite_transport' => $indemniteTransport,
            'autres_primes' => $autresPrimes,
            'cnss' => $cnss,
            'ir' => $ir,
            'retenues_diverses' => $retenuesDiverses,
            'salaire_net' => $salaireNet,
            'salaire_net_lettres' => $this->nombreEnLettres($salaireNet),
            'statut' => 'en_attente',
            'reference' => 'SAL-' . strtoupper(Str::random(8)),
        ];
    }
    
    /**
     * Calcule la prime d'ancienneté
     */
    private function calculerPrimeAnciennete($professeur, $periode)
    {
        if (!$professeur->date_embauche) {
            return 0;
        }
        
        $dateEmbauche = Carbon::parse($professeur->date_embauche);
        $datePeriode = Carbon::parse($periode);
        $anneesAnciennete = $dateEmbauche->diffInYears($datePeriode);
        
        // Par exemple: 2% du salaire de base par année d'ancienneté, avec un plafond de 20%
        $pourcentage = min(20, $anneesAnciennete * 2) / 100;
        
        // Supposons que le salaire de base est de 10 000 DH
        $salaireBase = 10000;
        
        return round($salaireBase * $pourcentage, 2);
    }
    
    /**
     * Calcule la prime de rendement
     */
    private function calculerPrimeRendement($professeur, $periode)
    {
        // Logique de calcul de la prime de rendement
        // À implémenter selon la logique métier
        return 500; // Valeur fixe pour l'exemple
    }
    
    /**
     * Calcule la retenue CNSS
     */
    private function calculerCNSS($salaireBrut)
    {
        // Taux CNSS: 4.29% avec un plafond
        $tauxCNSS = 0.0429;
        $plafondCNSS = 6000; // Plafond mensuel en DH
        $assiette = min($salaireBrut, $plafondCNSS);
        
        return round($assiette * $tauxCNSS, 2);
    }
    
    /**
     * Calcule l'impôt sur le revenu
     */
    private function calculerIR($salaireBrut)
    {
        // Barème progressif de l'IR (exemple simplifié)
        $tranches = [
            [0, 30000, 0],
            [30001, 50000, 10],
            [50001, 60000, 20],
            [60001, 80000, 30],
            [80001, 180000, 34],
            [180001, PHP_FLOAT_MAX, 38]
        ];
        
        $ir = 0;
        $salaireAnnuel = $salaireBrut * 12;
        
        foreach ($tranches as $tranche) {
            list($min, $max, $taux) = $tranche;
            
            if ($salaireAnnuel > $min) {
                $montantImposable = min($salaireAnnuel, $max) - $min;
                $ir += $montantImposable * ($taux / 100);
            } else {
                break;
            }
        }
        
        // Retourne l'IR mensuel
        return round($ir / 12, 2);
    }
    
    /**
     * Convertit un nombre en lettres (en français)
     */
    private function nombreEnLettres($nombre)
    {
        $unites = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
        $dizaines = ['', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante', 'quatre-vingt', 'quatre-vingt'];
        
        if ($nombre < 0) {
            return 'moins ' . $this->nombreEnLettres(abs($nombre));
        }
        
        if ($nombre < 20) {
            return $unites[$nombre];
        }
        
        if ($nombre < 100) {
            $dizaine = (int)($nombre / 10);
            $unite = $nombre % 10;
            
            if ($dizaine === 7 || $dizaine === 9) {
                return $dizaines[$dizaine] . '-' . $this->nombreEnLettres(10 + $unite);
            }
            
            $resultat = $dizaines[$dizaine];
            
            if ($dizaine === 8 && $unite === 0) {
                $resultat .= 's'; // "quatre-vingts"
            } elseif ($unite === 1 && $dizaine !== 8) {
                $resultat .= ' et un';
            } elseif ($unite > 0) {
                $resultat .= '-' . $unites[$unite];
            }
            
            return $resultat;
        }
        
        if ($nombre < 1000) {
            $centaine = (int)($nombre / 100);
            $reste = $nombre % 100;
            
            $resultat = $centaine === 1 ? 'cent' : $unites[$centaine] . ' cent';
            
            if ($centaine > 1 && $reste === 0) {
                $resultat .= 's'; // "deux cents", "trois cents", etc.
            } elseif ($reste > 0) {
                $resultat .= ' ' . $this->nombreEnLettres($reste);
            }
            
            return $resultat;
        }
        
        if ($nombre < 1000000) {
            $millier = (int)($nombre / 1000);
            $reste = $nombre % 1000;
            
            $resultat = $millier === 1 ? 'mille' : $this->nombreEnLettres($millier) . ' mille';
            
            if ($reste > 0) {
                $resultat .= ' ' . $this->nombreEnLettres($reste);
            }
            
            return $resultat;
        }
        
        if ($nombre < 1000000000) {
            $million = (int)($nombre / 1000000);
            $reste = $nombre % 1000000;
            
            $resultat = $million === 1 ? 'un million' : $this->nombreEnLettres($million) . ' millions';
            
            if ($reste > 0) {
                $resultat .= ' ' . $this->nombreEnLettres($reste);
            }
            
            return $resultat;
        }
        
        return 'nombre trop élevé';
    }

    /**
     * Prépare un nouveau salaire
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function preparer(Request $request)
    {
        $request->validate([
            'professeur_id' => 'required|exists:users,id',
            'periode' => 'required|date_format:Y-m',
        ]);
        
        // Vérifier que le professeur est bien un professeur actif
        $professeur = User::findOrFail($request->professeur_id);
        if ($professeur->role !== 'professeur' || $professeur->status !== 'actif') {
            return response()->json([
                'success' => false,
                'message' => 'Le professeur sélectionné n\'est pas valide.'
            ], 422);
        }
        
        // Vérifier qu'il n'y a pas de salaire existant pour cette période
        $salaireExistant = Salaire::where('professeur_id', $professeur->id)
            ->where('periode', $request->periode)
            ->exists();
            
        if ($salaireExistant) {
            return response()->json([
                'success' => false,
                'message' => 'Un salaire existe déjà pour cette période.'
            ], 422);
        }
        
        // Calculer le salaire
        $salaire = $this->calculerSalaire(
            $request->professeur_id,
            $request->periode
        );
        
        return response()->json([
            'success' => true,
            'data' => $salaire
        ]);
    }

    /**
     * Enregistre un nouveau salaire
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'professeur_id' => 'required|exists:users,id',
            'periode' => 'required|date_format:Y-m',
            'nb_heures' => 'required|numeric|min:0',
            'taux_horaire' => 'required|numeric|min:0',
            'salaire_brut' => 'required|numeric|min:0',
            'prime_anciennete' => 'required|numeric|min:0',
            'prime_rendement' => 'required|numeric|min:0',
            'indemnite_transport' => 'required|numeric|min:0',
            'autres_primes' => 'required|numeric|min:0',
            'cnss' => 'required|numeric|min:0',
            'ir' => 'required|numeric|min:0',
            'retenues_diverses' => 'required|numeric|min:0',
            'salaire_net' => 'required|numeric|min:0',
            'statut' => 'required|in:en_attente,paye,retard',
            'type_paiement' => 'required_if:statut,paye|in:virement,cheque,especes',
            'date_paiement' => 'required_if:statut,paye|date',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);
        
        // Vérifier que le professeur est bien un professeur actif
        $professeur = User::findOrFail($validated['professeur_id']);
        if ($professeur->role !== 'professeur' || $professeur->status !== 'actif') {
            return back()->with('error', 'Le professeur sélectionné n\'est pas valide.');
        }
        
        // Vérifier qu'il n'y a pas de salaire existant pour cette période
        $salaireExistant = Salaire::where('professeur_id', $professeur->id)
            ->where('periode', $validated['periode'])
            ->exists();
            
        if ($salaireExistant) {
            return back()->with('error', 'Un salaire existe déjà pour cette période.');
        }
        
        // Créer le salaire
        $salaire = new Salaire([
            'professeur_id' => $validated['professeur_id'],
            'periode' => $validated['periode'],
            'nb_heures' => $validated['nb_heures'],
            'taux_horaire' => $validated['taux_horaire'],
            'salaire_brut' => $validated['salaire_brut'],
            'prime_anciennete' => $validated['prime_anciennete'],
            'prime_rendement' => $validated['prime_rendement'],
            'indemnite_transport' => $validated['indemnite_transport'],
            'autres_primes' => $validated['autres_primes'],
            'cnss' => $validated['cnss'],
            'ir' => $validated['ir'],
            'retenues_diverses' => $validated['retenues_diverses'],
            'salaire_net' => $validated['salaire_net'],
            'salaire_net_lettres' => $this->nombreEnLettres($validated['salaire_net']),
            'statut' => $validated['statut'],
            'type_paiement' => $validated['type_paiement'] ?? null,
            'date_paiement' => $validated['date_paiement'] ?? null,
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'paye_par' => $validated['statut'] === 'paye' ? auth()->id() : null,
        ]);
        
        $salaire->save();
        
        // Envoyer une notification au professeur si le salaire est marqué comme payé
        if ($salaire->statut === 'paye') {
            // À implémenter: notification au professeur
            // $professeur->notify(new SalairePaye($salaire));
        }
        
        return redirect()->route('admin.salaires.show', $salaire)
            ->with('success', 'Salaire enregistré avec succès.');
    }

    /**
     * Affiche les détails d'un salaire
     *
     * @param  \App\Models\Salaire  $salaire
     * @return \Illuminate\View\View
     */
    public function show(Salaire $salaire)
    {
        $salaire->load(['professeur', 'payePar']);
        
        // Charger l'historique des modifications
        $activites = $salaire->activities()
            ->with('causer')
            ->latest()
            ->get();
        
        return view('admin.salaires.show', [
            'salaire' => $salaire,
            'activites' => $activites
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'un salaire
     *
     * @param  \App\Models\Salaire  $salaire
     * @return \Illuminate\View\View
     */
    public function edit(Salaire $salaire)
    {
        $professeurs = User::where('role', 'professeur')
            ->where('status', 'actif')
            ->orderBy('name')
            ->orderBy('prenom')
            ->get()
            ->map(function($professeur) {
                $professeur->nom_complet = $professeur->name . ' ' . $professeur->prenom;
                return $professeur;
            });
            
        return view('admin.salaires.form', [
            'salaire' => $salaire,
            'professeurs' => $professeurs,
            'title' => 'Modifier le salaire',
            'submitText' => 'Mettre à jour',
            'method' => 'PUT',
            'route' => route('admin.salaires.update', $salaire)
        ]);
    }
    
    /**
     * Met à jour un salaire existant
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Salaire  $salaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Salaire $salaire)
    {
        $validated = $request->validate([
            'professeur_id' => 'required|exists:users,id',
            'periode' => 'required|date_format:Y-m',
            'nb_heures' => 'required|numeric|min:0',
            'taux_horaire' => 'required|numeric|min:0',
            'salaire_brut' => 'required|numeric|min:0',
            'prime_anciennete' => 'required|numeric|min:0',
            'prime_rendement' => 'required|numeric|min:0',
            'indemnite_transport' => 'required|numeric|min:0',
            'autres_primes' => 'required|numeric|min:0',
            'cnss' => 'required|numeric|min:0',
            'ir' => 'required|numeric|min:0',
            'retenues_diverses' => 'required|numeric|min:0',
            'salaire_net' => 'required|numeric|min:0',
            'statut' => 'required|in:en_attente,paye,retard',
            'type_paiement' => 'required_if:statut,paye|in:virement,cheque,especes',
            'date_paiement' => 'required_if:statut,paye|date',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);
        
        // Vérifier que le professeur est bien un professeur actif
        $professeur = User::findOrFail($validated['professeur_id']);
        if ($professeur->role !== 'professeur' || $professeur->status !== 'actif') {
            return back()->with('error', 'Le professeur sélectionné n\'est pas valide.');
        }
        
        // Vérifier qu'il n'y a pas de salaire existant pour cette période (sauf celui-ci)
        $salaireExistant = Salaire::where('professeur_id', $professeur->id)
            ->where('periode', $validated['periode'])
            ->where('id', '!=', $salaire->id)
            ->exists();
            
        if ($salaireExistant) {
            return back()->with('error', 'Un autre salaire existe déjà pour cette période.');
        }
        
        // Mettre à jour le salaire
        $ancienStatut = $salaire->statut;
        $nouveauStatut = $validated['statut'];
        
        $salaire->update([
            'professeur_id' => $validated['professeur_id'],
            'periode' => $validated['periode'],
            'nb_heures' => $validated['nb_heures'],
            'taux_horaire' => $validated['taux_horaire'],
            'salaire_brut' => $validated['salaire_brut'],
            'prime_anciennete' => $validated['prime_anciennete'],
            'prime_rendement' => $validated['prime_rendement'],
            'indemnite_transport' => $validated['indemnite_transport'],
            'autres_primes' => $validated['autres_primes'],
            'cnss' => $validated['cnss'],
            'ir' => $validated['ir'],
            'retenues_diverses' => $validated['retenues_diverses'],
            'salaire_net' => $validated['salaire_net'],
            'salaire_net_lettres' => $this->nombreEnLettres($validated['salaire_net']),
            'statut' => $nouveauStatut,
            'type_paiement' => $validated['type_paiement'] ?? null,
            'date_paiement' => $validated['date_paiement'] ?? null,
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'paye_par' => $nouveauStatut === 'paye' ? auth()->id() : $salaire->paye_par,
        ]);
        
        // Envoyer une notification si le statut est passé à "payé"
        if ($ancienStatut !== 'paye' && $nouveauStatut === 'paye') {
            // À implémenter: notification au professeur
            // $professeur->notify(new SalairePaye($salaire));
        }
        
        return redirect()->route('admin.salaires.show', $salaire)
            ->with('success', 'Salaire mis à jour avec succès.');
    }
    
    /**
     * Affiche la fiche de paie d'un salaire
     *
     * @param  \App\Models\Salaire  $salaire
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function fichePaie(Salaire $salaire)
    {
        $salaire->load(['professeur', 'payePar']);
        
        // Générer un nom de fichier unique
        $filename = 'fiche-paie-' . 
                   Str::slug($salaire->professeur->nom . ' ' . $salaire->professeur->prenom) . '-' . 
                   $salaire->periode . '.pdf';
        
        // Vérifier si on veut forcer le téléchargement
        if (request()->has('download')) {
            $pdf = Pdf::loadView('admin.salaires.fiche_paie', compact('salaire'));
            return $pdf->download($filename);
        }
        
        // Sinon, afficher la vue normale
        return view('admin.salaires.fiche_paie', compact('salaire'));
    }

    /**
     * Supprime un salaire
     *
     * @param  \App\Models\Salaire  $salaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Salaire $salaire)
    {
        // Vérifier que le salaire n'est pas déjà payé
        if ($salaire->statut === 'paye') {
            return back()->with('error', 'Impossible de supprimer un salaire déjà payé.');
        }
        
        $salaire->delete();
        
        return redirect()->route('admin.salaires.index')
            ->with('success', 'Salaire supprimé avec succès.');
    }
    
    /**
     * Marque un salaire comme payé
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Salaire  $salaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function payer(Request $request, Salaire $salaire)
    {
        $validated = $request->validate([
            'date_paiement' => 'required|date',
            'type_paiement' => 'required|in:virement,cheque,especes',
            'reference' => 'nullable|string|max:100',
            'notifier_professeur' => 'boolean',
        ]);
        
        // Mettre à jour le statut du salaire
        $salaire->update([
            'statut' => 'paye',
            'type_paiement' => $validated['type_paiement'],
            'date_paiement' => $validated['date_paiement'],
            'reference' => $validated['reference'] ?? null,
            'paye_par' => auth()->id(),
        ]);
        
        // Envoyer une notification au professeur si demandé
        if ($request->has('notifier_professeur') && $request->notifier_professeur) {
            // À implémenter: notification au professeur
            // $salaire->professeur->notify(new SalairePaye($salaire));
        }
        
        return redirect()->route('admin.salaires.show', $salaire)
            ->with('success', 'Le paiement a été enregistré avec succès.');
    }
    
    /**
     * Exporte la liste des salaires au format Excel ou PDF
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function export(Request $request)
    {
        $query = Salaire::with(['professeur', 'payePar'])
            ->latest('periode');
            
        // Appliquer les filtres
        $filters = [];
        
        if ($request->has('periode') && $request->periode) {
            $query->where('periode', 'like', $request->periode . '%');
            $filters['periode'] = $request->periode;
        }
        
        if ($request->has('professeur_id') && $request->professeur_id) {
            $query->where('professeur_id', $request->professeur_id);
            $professeur = User::find($request->professeur_id);
            if ($professeur) {
                $filters['professeur'] = $professeur->nom . ' ' . $professeur->prenom;
            }
        }
        
        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
            $statuts = [
                'en_attente' => 'En attente',
                'paye' => 'Payé',
                'retard' => 'En retard'
            ];
            $filters['statut'] = $statuts[$request->statut] ?? $request->statut;
        }
        
        $salaires = $query->get();
        
        if ($salaires->isEmpty()) {
            return back()->with('warning', 'Aucun salaire trouvé avec les critères sélectionnés.');
        }
        
        $periode = $request->periode ?? now()->format('Y-m');
        
        // Générer un nom de fichier significatif
        $filename = 'export-salaires-' . now()->format('Y-m-d-His');
        if (!empty($filters)) {
            $filename .= '-' . Str::slug(implode('-', array_values($filters)));
        }
        
        // Vérifier le format d'export demandé (par défaut Excel)
        $format = $request->input('format');
        if ($format === 'pdf') {
            $filename .= '.pdf';
            
            // Préparer les données pour la vue PDF
            $pdfData = [
                'salaires' => $salaires,
                'filters' => $filters,
                'periode' => $periode,
                'totalBrut' => $salaires->sum('salaire_brut'),
                'totalNet' => $salaires->sum('salaire_net'),
                'totalRetenues' => $salaires->sum('cnss') + $salaires->sum('ir') + $salaires->sum('retenues_diverses'),
            ];
            
            // Charger la vue PDF avec les données
            $pdf = Pdf::loadView('admin.salaires.exports.pdf', $pdfData);
            
            // Télécharger le PDF
            return $pdf->download($filename);
        } else {
            // Par défaut, exporter en Excel
            $filename .= '.xlsx';
            return Excel::download(new SalairesExport($salaires, $periode, $filters), $filename);
        }
    }
    
    /**
     * Affiche le rapport des salaires
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function rapport(Request $request)
    {
        $query = Salaire::with(['professeur', 'payePar'])
            ->select(
                'salaires.*',
                DB::raw('YEAR(periode) as annee'),
                DB::raw('MONTH(periode) as mois')
            )
            ->orderBy('periode', 'desc');
            
        // Filtrage par année
        $annee = $request->input('annee', date('Y'));
        $query->whereYear('periode', $annee);
        
        // Filtrage par mois
        if ($request->has('mois') && $request->mois) {
            $query->whereMonth('periode', $request->mois);
        }
        
        // Filtrage par professeur
        if ($request->has('professeur_id') && $request->professeur_id) {
            $query->where('professeur_id', $request->professeur_id);
        }
        
        // Filtrage par statut
        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }
        
        $salaires = $query->get();
        
        // Calcul des statistiques
        $totalSalaires = $salaires->sum('salaire_net');
        $moyenneParProfesseur = $salaires->groupBy('professeur_id')->count() > 0 
            ? $totalSalaires / $salaires->groupBy('professeur_id')->count() 
            : 0;
            
        $parMois = $salaires->groupBy(function($salaire) {
            return Carbon::parse($salaire->periode)->format('Y-m');
        })->map(function($salaires) {
            return [
                'montant' => $salaires->sum('salaire_net'),
                'count' => $salaires->count(),
            ];
        });
        
        // Statistiques par statut
        $parStatut = $salaires->groupBy('statut')->map(function($salaires) {
            return [
                'count' => $salaires->count(),
                'montant' => $salaires->sum('salaire_net'),
            ];
        });
        
        // Liste des professeurs pour le filtre
        $professeurs = User::where('role', 'professeur')
            ->where('status', 'actif')
            ->orderBy('name')
            ->orderBy('prenom')
            ->get();
            
        $annees = range(date('Y') - 5, date('Y') + 1);
        
        // Données pour les graphiques
        $donneesGraphique = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Salaires nets',
                    'data' => [],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];
        
        $mois = [
            1 => 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];
        
        // Préparer les données pour le graphique par mois
        for ($i = 1; $i <= 12; $i++) {
            $moisKey = str_pad($i, 2, '0', STR_PAD_LEFT);
            $moisLabel = $mois[$i];
            $donneesGraphique['labels'][] = $moisLabel;
            
            $totalMois = $salaires->filter(function($salaire) use ($i) {
                return (int)date('m', strtotime($salaire->periode)) === $i;
            })->sum('salaire_net');
            
            $donneesGraphique['datasets'][0]['data'][] = $totalMois;
        }
        
        return view('admin.salaires.rapport', [
            'salaires' => $salaires,
            'totalSalaires' => $totalSalaires,
            'moyenneParProfesseur' => $moyenneParProfesseur,
            'parMois' => $parMois,
            'parStatut' => $parStatut,
            'professeurs' => $professeurs,
            'annees' => $annees,
            'annee' => $annee,
            'mois' => $mois,
            'donneesGraphique' => json_encode($donneesGraphique),
        ]);
    }
}
