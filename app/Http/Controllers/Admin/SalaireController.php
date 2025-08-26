<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salaire;
use App\Models\ConfigurationSalaire;
use App\Models\Matiere;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SalaireController extends Controller
{
    /**
     * Affiche la liste des salaires
     */
    public function index(Request $request)
    {
        $query = Salaire::with(['professeur', 'matiere'])
            ->orderBy('mois_periode', 'desc')
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->has('mois_periode')) {
            $query->where('mois_periode', $request->mois_periode);
        }

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('professeur_id')) {
            $query->where('professeur_id', $request->professeur_id);
        }

        $salaires = $query->paginate(15)->withQueryString();

        // Pour les filtres
        $professeurs = User::role('professeur')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $statuts = [
            ['value' => 'en_attente', 'label' => 'En attente'],
            ['value' => 'paye', 'label' => 'Payé'],
            ['value' => 'annule', 'label' => 'Annulé'],
        ];

        return Inertia::render('Admin/Salaires/Index', [
            'salaires' => $salaires,
            'filters' => $request->all(['mois_periode', 'statut', 'professeur_id']),
            'professeurs' => $professeurs,
            'statuts' => $statuts,
        ]);
    }

    /**
     * Affiche le formulaire de création d'un salaire
     */
    public function create()
    {
        $professeurs = User::role('professeur')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $matieres = Matiere::orderBy('nom')->get(['id', 'nom']);

        return Inertia::render('Admin/Salaires/Create', [
            'professeurs' => $professeurs,
            'matieres' => $matieres,
        ]);
    }

    /**
     * Affiche les détails d'un salaire
     */
    public function show($id)
    {
        $salaire = Salaire::with(['professeur', 'matiere'])->findOrFail($id);
        
        return Inertia::render('Admin/Salaires/Show', [
            'salaire' => $salaire,
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'un salaire
     */
    public function edit($id)
    {
        $salaire = Salaire::findOrFail($id);
        $professeurs = User::role('professeur')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $matieres = Matiere::orderBy('nom')->get(['id', 'nom']);
        $statuts = [
            ['value' => 'en_attente', 'label' => 'En attente'],
            ['value' => 'paye', 'label' => 'Payé'],
            ['value' => 'annule', 'label' => 'Annulé'],
        ];

        return Inertia::render('Admin/Salaires/Edit', [
            'salaire' => $salaire,
            'professeurs' => $professeurs,
            'matieres' => $matieres,
            'statuts' => $statuts,
        ]);
    }

    /**
     * Met à jour un salaire
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'montant_brut' => 'required|numeric|min:0',
            'montant_net' => 'required|numeric|min:0',
            'montant_commission' => 'required|numeric|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
            'commission_prof' => 'required|numeric|min:0|max:100',
            'statut' => 'required|in:en_attente,paye,annule',
            'date_paiement' => 'nullable|date',
            'commentaires' => 'nullable|string|max:1000',
        ]);

        $salaire = Salaire::findOrFail($id);
        
        $salaire->update([
            'montant_brut' => $request->montant_brut,
            'montant_net' => $request->montant_net,
            'montant_commission' => $request->montant_commission,
            'prix_unitaire' => $request->prix_unitaire,
            'commission_prof' => $request->commission_prof,
            'statut' => $request->statut,
            'date_paiement' => $request->date_paiement,
            'commentaires' => $request->commentaires,
        ]);

        return redirect()->route('admin.salaires.show', $salaire->id)
            ->with('success', 'Salaire mis à jour avec succès.');
    }

    /**
     * Affiche la page de configuration des salaires
     */
    public function configuration()
    {
        $configurations = ConfigurationSalaire::with('matiere')
            ->orderBy('matiere_id')
            ->paginate(15);

        $matieresSansConfig = Matiere::whereNotIn('id', function($query) {
            $query->select('matiere_id')->from('configuration_salaires');
        })->get(['id', 'nom']);

        return Inertia::render('Admin/Salaires/Configuration', [
            'configurations' => $configurations,
            'matieresSansConfig' => $matieresSansConfig,
        ]);
    }

    /**
     * Calcule les salaires pour un mois donné
     */
    public function calculerSalaires(Request $request)
    {
        $request->validate([
            'mois_periode' => 'required|date_format:Y-m',
        ]);

        // Vérifier si le calcul a déjà été effectué pour ce mois
        $dejaCalcule = Salaire::where('mois_periode', $request->mois_periode)->exists();
        
        if ($dejaCalcule) {
            return back()->with('warning', 'Les salaires pour ce mois ont déjà été calculés.');
        }

        // Utiliser le service pour calculer les salaires
        $calculSalaireService = app(\App\Services\CalculSalaireService::class);
        $resultat = $calculSalaireService->calculerTousLesSalaires($request->mois_periode);

        if ($resultat['success']) {
            return back()->with('success', 'Calcul des salaires effectué avec succès pour ' . count($resultat['salaires']) . ' professeurs.');
        }

        return back()->with('error', 'Une erreur est survenue lors du calcul des salaires: ' . ($resultat['message'] ?? ''));
    }

    /**
     * Exporte les salaires au format Excel
     */
    public function export(Request $request)
    {
        $request->validate([
            'mois_periode' => 'required|date_format:Y-m',
            'format' => 'required|in:excel,pdf',
        ]);

        $salaires = Salaire::with(['professeur', 'matiere'])
            ->where('mois_periode', $request->mois_periode)
            ->orderBy('professeur_id')
            ->get();

        if ($salaires->isEmpty()) {
            return back()->with('warning', 'Aucun salaire trouvé pour le mois sélectionné.');
        }

        $periode = \Carbon\Carbon::createFromFormat('Y-m', $request->mois_periode)->format('F Y');
        $nomFichier = 'salaires-' . strtolower(str_replace(' ', '-', $periode));

        if ($request->format === 'excel') {
            return (new \App\Exports\SalairesExport($salaires, $periode))->download($nomFichier . '.xlsx');
        }

        // Pour le PDF, on utilisera une vue spécifique
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.salaires-pdf', [
            'salaires' => $salaires,
            'periode' => $periode,
            'totalBrut' => $salaires->sum('montant_brut'),
            'totalNet' => $salaires->sum('montant_net'),
            'totalCommission' => $salaires->sum('montant_commission'),
        ]);

        return $pdf->download($nomFichier . '.pdf');
    }
}
