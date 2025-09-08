<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use App\Models\Salaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SalaireController extends Controller
{
    /**
     * Affiche la liste des salaires du professeur
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $professeur = Auth::user();
        
        // Initialisation de la requête
        $query = $professeur->salaires()
            ->with(['professeur.user'])
            ->orderBy('periode', 'desc');
        
        // Filtrage par statut
        if ($request->has('statut') && in_array($request->statut, ['en_attente', 'paye', 'retard', 'annule'])) {
            $query->where('statut', $request->statut);
        }
        
        // Filtrage par période
        if ($request->has('periode')) {
            $query->where('periode', 'like', $request->periode . '%');
        }
        
        // Récupération des salaires avec pagination
        $salaires = $query->paginate(10);
        
        // Statistiques des salaires
        $statistiques = [
            'total' => $professeur->salaires()->count(),
            'total_brut' => $professeur->salaires()->sum('salaire_brut'),
            'total_net' => $professeur->salaires()->sum('salaire_net'),
            'moyenne_mensuelle' => $professeur->salaires()->avg('salaire_net'),
            'dernier_paiement' => $professeur->salaires()
                ->where('statut', 'paye')
                ->orderBy('date_paiement', 'desc')
                ->first(),
            'par_annee' => $professeur->salaires()
                ->selectRaw('YEAR(periode) as annee, COUNT(*) as total, SUM(salaire_net) as montant_total')
                ->groupBy('annee')
                ->orderBy('annee', 'desc')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->annee => [
                        'total' => $item->total,
                        'montant_total' => $item->montant_total
                    ]];
                })
        ];
        
        // Liste des années disponibles pour le filtre
        $annees = $professeur->salaires()
            ->selectRaw('YEAR(periode) as annee')
            ->distinct()
            ->orderBy('annee', 'desc')
            ->pluck('annee');
        
        return view('professeur.salaires.index', [
            'salaires' => $salaires,
            'statistiques' => $statistiques,
            'annees' => $annees,
            'filters' => $request->only(['statut', 'periode'])
        ]);
    }

    /**
     * Affiche les détails d'un salaire
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        $professeur = Auth::user();
        
        try {
            // Récupérer le salaire du professeur connecté avec les relations nécessaires
            $salaire = $professeur->salaires()
                ->with(['professeur.user', 'paiements'])
                ->findOrFail($id);
                
            // Formater les données pour la vue
            $salaire->loadMissing(['professeur.user']);
            $salaire->periode_formatted = Carbon::parse($salaire->periode)->format('F Y');
            $salaire->date_paiement_formatted = $salaire->date_paiement ? 
                Carbon::parse($salaire->date_paiement)->format('d/m/Y') : null;
                
            // Calculer le nombre de jours restants pour faire une réclamation (30 jours après paiement)
            $dateLimiteReclamation = null;
            $joursRestants = null;
            
            if ($salaire->date_paiement) {
                $dateLimiteReclamation = Carbon::parse($salaire->date_paiement)->addDays(30);
                $joursRestants = now()->diffInDays($dateLimiteReclamation, false);
                
                // Si la date limite est dépassée, on met à jour le statut de la réclamation si elle existe
                if ($joursRestants < 0) {
                    $salaire->reclamations()
                        ->where('statut', 'en_attente')
                        ->update(['statut' => 'expiree']);
                }
            }
            
            // Historique des modifications
            $historique = $salaire->activities()
                ->with('causer')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($activity) {
                    return [
                        'description' => $activity->description,
                        'causer' => $activity->causer ? $activity->causer->name : 'Système',
                        'date' => $activity->created_at->format('d/m/Y H:i'),
                        'properties' => $activity->properties
                    ];
                });
            
            // Récupérer les réclamations liées à ce salaire
            $reclamations = $salaire->reclamations()
                ->orderBy('created_at', 'desc')
                ->get();
            
            return view('professeur.salaires.show', [
                'salaire' => $salaire,
                'historique' => $historique,
                'reclamations' => $reclamations,
                'date_limite_reclamation' => $dateLimiteReclamation,
                'jours_restants_reclamation' => $joursRestants > 0 ? $joursRestants : 0,
                'peut_faire_reclamation' => $salaire->statut === 'paye' && 
                                         $joursRestants > 0 &&
                                         !$salaire->reclamations()->whereIn('statut', ['en_attente', 'en_cours'])->exists()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du salaire: ' . $e->getMessage());
            return redirect()->route('professeur.salaires.index')
                ->with('error', 'Une erreur est survenue lors de la récupération des détails du salaire.');
        }
    }
    
    /**
     * Affiche le formulaire de réclamation pour un salaire
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function reclamation($id)
    {
        $professeur = Auth::user();
        $salaire = $professeur->salaires()->findOrFail($id);
        
        // Vérifier si le salaire est payé
        if ($salaire->statut !== 'paye') {
            return redirect()->route('professeur.salaires.index')
                ->with('error', 'Vous ne pouvez pas faire de réclamation pour un salaire non payé.');
        }
        
        return view('professeur.salaires.reclamation', [
            'salaire' => $salaire
        ]);
    }
    
    /**
     * Traite la réclamation d'un salaire
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function traiterReclamation(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:montant_incorrect,heures_manquantes,prime_manquante,retenue_incorrecte,autre',
            'sujet' => 'required|string|max:255',
            'message' => 'required|string|min:20|max:5000',
            'piece_jointe' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // 10MB max
        ]);
        
        $professeur = Auth::user();
        
        try {
            // Démarrer une transaction pour assurer l'intégrité des données
            return DB::transaction(function () use ($request, $professeur, $id) {
                $salaire = $professeur->salaires()
                    ->with(['reclamations' => function($query) {
                        $query->whereIn('statut', ['en_attente', 'en_cours']);
                    }])
                    ->findOrFail($id);
                
                // Vérifier si une réclamation est déjà en cours
                if ($salaire->reclamations->isNotEmpty()) {
                    return redirect()->route('professeur.salaires.show', $salaire)
                        ->with('warning', 'Une réclamation est déjà en cours pour ce salaire.');
                }
                
                // Vérifier que la date limite de réclamation n'est pas dépassée
                if ($salaire->date_paiement) {
                    $dateLimite = Carbon::parse($salaire->date_paiement)->addDays(30);
                    
                    if (now()->gt($dateLimite)) {
                        return redirect()->route('professeur.salaires.show', $salaire)
                            ->with('error', 'Le délai pour déposer une réclamation pour ce salaire est expiré.');
                    }
                }
                
                // Traitement de la pièce jointe si elle existe
                $pieceJointePath = null;
                if ($request->hasFile('piece_jointe')) {
                    $extension = $request->file('piece_jointe')->getClientOriginalExtension();
                    $filename = 'reclamation-' . Str::uuid() . '.' . $extension;
                    $pieceJointePath = $request->file('piece_jointe')->storeAs('reclamations', $filename, 'public');
                }
                
                // Enregistrer la réclamation dans la base de données
                $reclamation = $salaire->reclamations()->create([
                    'professeur_id' => $professeur->id,
                    'type' => $request->type,
                    'sujet' => $request->sujet,
                    'message' => $request->message,
                    'piece_jointe' => $pieceJointePath,
                    'statut' => 'en_attente',
                    'reference' => 'REC-' . strtoupper(Str::random(8)),
                ]);
                
                // Mettre à jour le statut du salaire si nécessaire
                if ($salaire->statut !== 'en_reclamation') {
                    $salaire->update(['statut' => 'en_reclamation']);
                }
                
                // Envoyer une notification à l'administration
                // $admins = User::role('admin')->get();
                // Notification::send($admins, new NouvelleReclamationNotification($reclamation));
                
                // Envoyer un email de confirmation au professeur
                // $professeur->notify(new ReclamationSoumiseNotification($reclamation));
                
                // Enregistrer une activité
                activity()
                    ->causedBy($professeur)
                    ->performedOn($salaire)
                    ->withProperties([
                        'type' => $request->type,
                        'sujet' => $request->sujet,
                        'reclamation_id' => $reclamation->id,
                    ])
                    ->log('Réclamation déposée pour le salaire');
                
                // Créer une notification dans la base de données
                $salaire->notifications()->create([
                    'titre' => 'Nouvelle réclamation',
                    'message' => 'Une nouvelle réclamation a été soumise pour le salaire de ' . $salaire->periode,
                    'type' => 'reclamation',
                    'lue' => false,
                    'user_id' => $professeur->id,
                    'lien' => route('admin.salaires.reclamations.show', $reclamation->id)
                ]);
                
                return redirect()->route('professeur.salaires.show', $salaire)
                    ->with('success', 'Votre réclamation a été enregistrée avec succès. Nous traiterons votre demande dans les plus brefs délais.');
            });
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement de la réclamation: ' . $e->getMessage());
            
            // Supprimer la pièce jointe en cas d'erreur
            if (isset($pieceJointePath) && Storage::disk('public')->exists($pieceJointePath)) {
                Storage::disk('public')->delete($pieceJointePath);
            }
            
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors du traitement de votre réclamation. Veuillez réessayer.');
        }
    }
    
    /**
     * Télécharge la fiche de paie au format PDF
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function telechargerFichePaie($id)
    {
        $professeur = Auth::user();
        
        try {
            $salaire = $professeur->salaires()
                ->with(['professeur.user', 'paiements'])
                ->findOrFail($id);
            
            // Vérifier si le salaire est payé
            if ($salaire->statut !== 'paye') {
                return redirect()->back()
                    ->with('error', 'La fiche de paie n\'est pas encore disponible.');
            }
            
            // Enregistrer l'activité de téléchargement
            activity()
                ->causedBy($professeur)
                ->performedOn($salaire)
                ->log('Téléchargement de la fiche de paie');
            
            // Générer un nom de fichier convivial
            $nomFichier = 'fiche-paie-' . Str::slug($professeur->nom . ' ' . $professeur->prenom) . 
                         '-' . Carbon::parse($salaire->periode)->format('m-Y') . '.pdf';
            
            // Charger la vue et générer le PDF
            $pdf = PDF::loadView('professeur.salaires.fiche_paie', [
                'salaire' => $salaire,
                'date_emission' => now()->format('d/m/Y'),
                'est_telechargement' => true
            ]);
            
            // Options du PDF
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 96,
                'defaultFont' => 'Arial'
            ]);
            
            // Retourner le PDF en téléchargement
            return $pdf->download($nomFichier);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération de la fiche de paie: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la génération de la fiche de paie. Veuillez réessayer.');
        }
    }
    
    /**
     * Affiche une prévisualisation de la fiche de paie
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function previewFichePaie($id)
    {
        $professeur = Auth::user();
        
        try {
            $salaire = $professeur->salaires()
                ->with(['professeur.user', 'paiements'])
                ->findOrFail($id);
            
            // Vérifier si le salaire est payé
            if ($salaire->statut !== 'paye') {
                return redirect()->back()
                    ->with('error', 'La fiche de paie n\'est pas encore disponible.');
            }
            
            return view('professeur.salaires.fiche_paie', [
                'salaire' => $salaire,
                'date_emission' => now()->format('d/m/Y'),
                'est_telechargement' => false
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage de la prévisualisation: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors du chargement de la prévisualisation.');
        }
    }
    
    /**
     * Affiche l'historique des réclamations du professeur
     *
     * @return \Illuminate\View\View
     */
    public function historiqueReclamations()
    {
        $professeur = Auth::user();
        
        $reclamations = $professeur->reclamations()
            ->with(['salaire', 'reponses' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('professeur.salaires.historique_reclamations', [
            'reclamations' => $reclamations
        ]);
    }
    
    /**
     * Détermine le statut du salaire
     *
     * @param  \App\Models\Salaire  $salaire
     * @return string
     */
    /**
     * Détermine le statut du salaire
     *
     * @param  \App\Models\Salaire  $salaire
     * @return string
     */
    private function getStatutSalaire($salaire)
    {
        if ($salaire->statut === 'paye') {
            return 'Payé';
        } elseif ($salaire->statut === 'en_attente') {
            return 'En attente';
        } else {
            return 'Non payé';
        }
    }
}
