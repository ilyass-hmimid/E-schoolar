<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Paiement;
use App\Models\User;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaiementController extends BaseAdminController
{
    /**
     * Affiche la liste des paiements
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Paiement::with(['eleve', 'matiere', 'enregistrePar'])
            ->latest('date_paiement');
            
        // Filtrage par élève
        if ($request->has('eleve_id') && $request->eleve_id) {
            $query->where('eleve_id', $request->eleve_id);
        }
        
        // Filtrage par matière
        if ($request->has('matiere_id') && $request->matiere_id) {
            $query->where('matiere_id', $request->matiere_id);
        }
        
        // Filtrage par type de paiement
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Filtrage par statut
        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }
        
        // Filtrage par date
        if ($request->has('date_debut') && $request->date_debut) {
            $query->whereDate('date_paiement', '>=', $request->date_debut);
        }
        
        if ($request->has('date_fin') && $request->date_fin) {
            $query->whereDate('date_paiement', '<=', $request->date_fin);
        }
        
        $paiements = $query->paginate(20);
        $eleves = User::where('role', 'eleve')->orderBy('name')->get();
        $matieres = Matiere::orderBy('nom')->get();
        
        return view('admin.paiements.index', compact('paiements', 'eleves', 'matieres'));
    }

    /**
     * Affiche le formulaire de création d'un paiement
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $eleves = User::where('role', 'eleve')
            ->where('status', 'actif')
            ->orderBy('name')
            ->get();
            
        $matieres = Matiere::orderBy('nom')->get();
        
        return view('admin.paiements.create', compact('eleves', 'matieres'));
    }

    /**
     * Enregistre un nouveau paiement
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'eleve_id' => 'required|exists:users,id',
            'matiere_id' => 'required|exists:matieres,id',
            'type' => 'required|in:inscription,mensualite,autre',
            'montant' => 'required|numeric|min:0',
            'date_paiement' => 'required|date|before_or_equal:today',
            'mois_couvre' => 'nullable|date',
            'mode_paiement' => 'required|in:especes,virement,cheque,cmi',
            'reference' => 'nullable|string|max:100',
            'commentaire' => 'nullable|string|max:1000',
        ]);
        
        // Vérifier que l'élève est bien actif
        $eleve = User::findOrFail($validated['eleve_id']);
        if ($eleve->role !== 'eleve' || $eleve->status !== 'actif') {
            return back()->with('error', 'Seuls les élèves actifs peuvent effectuer des paiements.');
        }
        
        // Vérifier que la matière existe
        $matiere = Matiere::findOrFail($validated['matiere_id']);
        
        // Vérifier que l'élève est inscrit à la matière pour les mensualités
        if ($validated['type'] === 'mensualite' && !$eleve->matieres->contains($matiere->id)) {
            return back()->with('error', 'L\'élève n\'est pas inscrit à cette matière.');
        }
        
        // Vérifier les doublons pour les mensualités
        if ($validated['type'] === 'mensualite' && $validated['mois_couvre']) {
            $moisCouvre = Carbon::parse($validated['mois_couvre']);
            $debutMois = $moisCouvre->copy()->startOfMonth();
            $finMois = $moisCouvre->copy()->endOfMonth();
            
            $paiementExistant = Paiement::where('eleve_id', $eleve->id)
                ->where('matiere_id', $matiere->id)
                ->where('type', 'mensualite')
                ->whereDate('mois_couvre', '>=', $debutMois)
                ->whereDate('mois_couvre', '<=', $finMois)
                ->exists();
                
            if ($paiementExistant) {
                return back()->with('error', 'Un paiement existe déjà pour cette matière et cette période.');
            }
        }
        
        // Créer le paiement
        $paiement = new Paiement([
            'eleve_id' => $validated['eleve_id'],
            'matiere_id' => $validated['matiere_id'],
            'type' => $validated['type'],
            'montant' => $validated['montant'],
            'date_paiement' => $validated['date_paiement'],
            'mois_couvre' => $validated['mois_couvre'] ?? null,
            'mode_paiement' => $validated['mode_paiement'],
            'reference' => $validated['reference'] ?? null,
            'commentaire' => $validated['commentaire'] ?? null,
            'statut' => 'valide',
            'enregistre_par' => Auth::id(),
        ]);
        
        $paiement->save();
        
        return redirect()->route('admin.paiements.show', $paiement)
            ->with('success', 'Paiement enregistré avec succès.');
    }

    /**
     * Affiche les détails d'un paiement
     *
     * @param  \App\Models\Paiement  $paiement
     * @return \Illuminate\View\View
     */
    public function show(Paiement $paiement)
    {
        $paiement->load(['eleve', 'matiere', 'enregistrePar']);
        return view('admin.paiements.show', compact('paiement'));
    }

    /**
     * Affiche le formulaire d'édition d'un paiement
     *
     * @param  \App\Models\Paiement  $paiement
     * @return \Illuminate\View\View
     */
    public function edit(Paiement $paiement)
    {
        $eleves = User::where('role', 'eleve')
            ->where('status', 'actif')
            ->orderBy('name')
            ->get();
            
        $matieres = Matiere::orderBy('nom')->get();
        
        return view('admin.paiements.edit', compact('paiement', 'eleves', 'matieres'));
    }

    /**
     * Met à jour un paiement
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Paiement  $paiement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Paiement $paiement)
    {
        $validated = $request->validate([
            'eleve_id' => 'required|exists:users,id',
            'matiere_id' => 'required|exists:matieres,id',
            'type' => 'required|in:inscription,mensualite,autre',
            'montant' => 'required|numeric|min:0',
            'date_paiement' => 'required|date',
            'mois_couvre' => 'nullable|date',
            'mode_paiement' => 'required|in:especes,virement,cheque,cmi',
            'reference' => 'nullable|string|max:100',
            'commentaire' => 'nullable|string|max:1000',
            'statut' => 'required|in:en_attente,valide,annule',
        ]);
        
        // Vérifier que l'élève est bien actif
        $eleve = User::findOrFail($validated['eleve_id']);
        if ($eleve->role !== 'eleve' || $eleve->status !== 'actif') {
            return back()->with('error', 'Seuls les élèves actifs peuvent effectuer des paiements.');
        }
        
        // Vérifier que la matière existe
        $matiere = Matiere::findOrFail($validated['matiere_id']);
        
        // Vérifier que l'élève est inscrit à la matière pour les mensualités
        if ($validated['type'] === 'mensualite' && !$eleve->matieres->contains($matiere->id)) {
            return back()->with('error', 'L\'élève n\'est pas inscrit à cette matière.');
        }
        
        // Vérifier les doublons pour les mensualités (sauf pour le paiement actuel)
        if ($validated['type'] === 'mensualite' && $validated['mois_couvre']) {
            $moisCouvre = Carbon::parse($validated['mois_couvre']);
            $debutMois = $moisCouvre->copy()->startOfMonth();
            $finMois = $moisCouvre->copy()->endOfMonth();
            
            $paiementExistant = Paiement::where('id', '!=', $paiement->id)
                ->where('eleve_id', $eleve->id)
                ->where('matiere_id', $matiere->id)
                ->where('type', 'mensualite')
                ->whereDate('mois_couvre', '>=', $debutMois)
                ->whereDate('mois_couvre', '<=', $finMois)
                ->exists();
                
            if ($paiementExistant) {
                return back()->with('error', 'Un autre paiement existe déjà pour cette matière et cette période.');
            }
        }
        
        // Mettre à jour le paiement
        $paiement->update([
            'eleve_id' => $validated['eleve_id'],
            'matiere_id' => $validated['matiere_id'],
            'type' => $validated['type'],
            'montant' => $validated['montant'],
            'date_paiement' => $validated['date_paiement'],
            'mois_couvre' => $validated['mois_couvre'] ?? null,
            'mode_paiement' => $validated['mode_paiement'],
            'reference' => $validated['reference'] ?? null,
            'commentaire' => $validated['commentaire'] ?? null,
            'statut' => $validated['statut'],
        ]);
        
        return redirect()->route('admin.paiements.show', $paiement)
            ->with('success', 'Paiement mis à jour avec succès.');
    }

    /**
     * Annule un paiement
     *
     * @param  \App\Models\Paiement  $paiement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function annuler(Paiement $paiement)
    {
        if ($paiement->statut === 'annule') {
            return back()->with('error', 'Ce paiement est déjà annulé.');
        }
        
        $paiement->update([
            'statut' => 'annule',
            'commentaire' => $paiement->commentaire . "\n\nAnnulé le " . now()->format('d/m/Y H:i') . ' par ' . Auth::user()->name,
        ]);
        
        return back()->with('success', 'Le paiement a été annulé avec succès.');
    }

    /**
     * Affiche le formulaire d'import de paiements
     *
     * @return \Illuminate\View\View
     */
    public function showImportForm()
    {
        return view('admin.paiements.import');
    }

    /**
     * Traite l'import de paiements
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:csv,txt|max:1024',
        ]);
        
        // Logique d'import à implémenter
        
        return back()->with('success', 'Import des paiements en cours...');
    }
    
    /**
     * Affiche le rapport des paiements
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function rapport(Request $request)
    {
        $query = Paiement::with(['eleve', 'matiere'])
            ->select(
                'paiements.*',
                DB::raw('YEAR(date_paiement) as annee'),
                DB::raw('MONTH(date_paiement) as mois')
            )
            ->where('statut', 'valide')
            ->orderBy('date_paiement', 'desc');
            
        // Filtrage par année
        $annee = $request->input('annee', date('Y'));
        $query->whereYear('date_paiement', $annee);
        
        // Filtrage par mois
        if ($request->has('mois') && $request->mois) {
            $query->whereMonth('date_paiement', $request->mois);
        }
        
        // Filtrage par type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Filtrage par matière
        if ($request->has('matiere_id') && $request->matiere_id) {
            $query->where('matiere_id', $request->matiere_id);
        }
        
        $paiements = $query->get();
        
        // Calcul des totaux
        $totalGeneral = $paiements->sum('montant');
        $totalParMatiere = $paiements->groupBy('matiere.nom')->map->sum('montant');
        $totalParType = $paiements->groupBy('type')->map->sum('montant');
        
        $matieres = Matiere::orderBy('nom')->get();
        $annees = range(date('Y') - 5, date('Y') + 1);
        
        return view('admin.paiements.rapport', compact(
            'paiements',
            'totalGeneral',
            'totalParMatiere',
            'totalParType',
            'matieres',
            'annees',
            'annee'
        ));
    }
}
