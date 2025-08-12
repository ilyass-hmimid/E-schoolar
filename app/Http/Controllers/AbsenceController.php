<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Etudiant;
use App\Models\Seance;
use App\Models\Matiere;
use App\Models\Classe;
use App\Services\AbsenceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AbsenceController extends Controller
{
    protected $absenceService;

    public function __construct(AbsenceService $absenceService)
    {
        $this->absenceService = $absenceService;
    }

    /**
     * Affiche la liste des absences avec filtres
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Absence::class);
        
        $filters = $request->only([
            'etudiant_id', 'matiere_id', 'classe_id', 'professeur_id', 
            'date_debut', 'date_fin', 'justifiee'
        ]);

        $query = Absence::with([
            'etudiant:id,Nom,Prenom', 
            'seance.matiere:id,Libelle',
            'seance.professeur:id,Nom,Prenom'
        ]);

        // Appliquer les filtres
        if (!empty($filters['etudiant_id'])) {
            $query->where('IdEtu', $filters['etudiant_id']);
        }

        if (!empty($filters['matiere_id'])) {
            $query->whereHas('seance', function($q) use ($filters) {
                $q->where('IdMat', $filters['matiere_id']);
            });
        }

        if (!empty($filters['classe_id'])) {
            $query->whereHas('etudiant', function($q) use ($filters) {
                $q->where('IdClasse', $filters['classe_id']);
            });
        }

        if (!empty($filters['professeur_id'])) {
            $query->whereHas('seance', function($q) use ($filters) {
                $q->where('IdProf', $filters['professeur_id']);
            });
        }

        if (!empty($filters['date_debut'])) {
            $query->whereDate('date_absence', '>=', $filters['date_debut']);
        }

        if (!empty($filters['date_fin'])) {
            $query->whereDate('date_absence', '<=', $filters['date_fin']);
        }

        if (isset($filters['justifiee']) && $filters['justifiee'] !== '') {
            if ($filters['justifiee']) {
                $query->whereNotNull('justificatif');
            } else {
                $query->whereNull('justificatif');
            }
        }

        $absences = $query->latest('date_absence')
            ->paginate(15)
            ->withQueryString()
            ->through(function ($absence) {
                return [
                    'id' => $absence->id,
                    'etudiant' => $absence->etudiant ? $absence->etudiant->Nom . ' ' . $absence->etudiant->Prenom : 'Inconnu',
                    'matiere' => $absence->seance && $absence->seance->matiere ? $absence->seance->matiere->Libelle : 'Inconnue',
                    'professeur' => $absence->seance && $absence->seance->professeur ? 
                        $absence->seance->professeur->Nom . ' ' . $absence->seance->professeur->Prenom : 'Inconnu',
                    'date_absence' => $absence->date_absence->format('d/m/Y H:i'),
                    'est_justifiee' => !is_null($absence->justificatif),
                    'commentaire' => $absence->commentaire,
                    'justificatif' => $absence->justificatif,
                    'created_at' => $absence->created_at->format('d/m/Y H:i'),
                ];
            });

        return Inertia::render('Absences/Index', [
            'absences' => $absences,
            'filters' => $filters,
            'matieres' => Matiere::select('id', 'Libelle')
                ->orderBy('Libelle')
                ->get(),
            'classes' => Classe::select('id', 'Libelle')
                ->orderBy('Libelle')
                ->get(),
            'can' => [
                'create' => auth()->user()->can('create', Absence::class),
                'edit' => auth()->user()->can('update', Absence::class),
                'delete' => auth()->user()->can('delete', Absence::class),
            ]
        ]);
    }

    /**
     * Affiche le formulaire de création d'une absence
     */
    public function create()
    {
        $this->authorize('create', Absence::class);
        
        return Inertia::render('Absences/Create', [
            'etudiants' => Etudiant::select('id', 'Nom', 'Prenom')
                ->orderBy('Nom')
                ->get()
                ->map(function($etudiant) {
                    return [
                        'id' => $etudiant->id,
                        'nom_complet' => $etudiant->Nom . ' ' . $etudiant->Prenom,
                    ];
                }),
            'matieres' => Matiere::select('id', 'Libelle')
                ->orderBy('Libelle')
                ->get(),
        ]);
    }

    /**
     * Enregistre une nouvelle absence
     */
    public function store(Request $request)
    {
        $this->authorize('create', Absence::class);
        
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:Etudiant,id',
            'seance_id' => 'required|exists:Seance,id',
            'date_absence' => 'required|date',
            'justificatif' => 'nullable|string|max:255',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $absence = $this->absenceService->enregistrerAbsence(
            $validated['etudiant_id'],
            $validated['seance_id'],
            $validated['justificatif'] ?? null,
            $validated['commentaire'] ?? null
        );

        return redirect()->route('absences.show', $absence)
            ->with('success', 'L\'absence a été enregistrée avec succès.');
    }

    /**
     * Affiche les détails d'une absence
     */
    public function show(Absence $absence)
    {
        $this->authorize('view', $absence);
        
        $absence->load([
            'etudiant:id,Nom,Prenom,Email,Telephone',
            'seance.matiere:id,Libelle',
            'seance.professeur:id,Nom,Prenom',
            'seance.classe:id,Libelle',
        ]);

        return Inertia::render('Absences/Show', [
            'absence' => [
                'id' => $absence->id,
                'etudiant' => [
                    'id' => $absence->etudiant->id,
                    'nom_complet' => $absence->etudiant->Nom . ' ' . $absence->etudiant->Prenom,
                    'email' => $absence->etudiant->Email,
                    'telephone' => $absence->etudiant->Telephone,
                ],
                'matiere' => $absence->seance->matiere->Libelle,
                'professeur' => $absence->seance->professeur ? 
                    $absence->seance->professeur->Nom . ' ' . $absence->seance->professeur->Prenom : 'Inconnu',
                'classe' => $absence->seance->classe->Libelle,
                'date_absence' => $absence->date_absence->format('d/m/Y H:i'),
                'est_justifiee' => !is_null($absence->justificatif),
                'justificatif' => $absence->justificatif,
                'commentaire' => $absence->commentaire,
                'created_at' => $absence->created_at->format('d/m/Y H:i'),
                'updated_at' => $absence->updated_at->format('d/m/Y H:i'),
            ]
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'une absence
     */
    public function edit(Absence $absence)
    {
        $this->authorize('update', $absence);
        
        $absence->load([
            'etudiant:id,Nom,Prenom',
            'seance.matiere:id,Libelle',
        ]);

        return Inertia::render('Absences/Edit', [
            'absence' => [
                'id' => $absence->id,
                'etudiant_id' => $absence->IdEtu,
                'etudiant_nom' => $absence->etudiant ? $absence->etudiant->Nom . ' ' . $absence->etudiant->Prenom : 'Inconnu',
                'matiere' => $absence->seance && $absence->seance->matiere ? $absence->seance->matiere->Libelle : 'Inconnue',
                'date_absence' => $absence->date_absence->format('Y-m-d\TH:i'),
                'justificatif' => $absence->justificatif,
                'commentaire' => $absence->commentaire,
            ]
        ]);
    }

    /**
     * Met à jour une absence existante
     */
    public function update(Request $request, Absence $absence)
    {
        $this->authorize('update', $absence);
        
        $validated = $request->validate([
            'justificatif' => 'nullable|string|max:255',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $absence->update([
            'justificatif' => $validated['justificatif'] ?? null,
            'commentaire' => $validated['commentaire'] ?? null,
            'date_modification' => now(),
        ]);

        return redirect()->route('absences.show', $absence)
            ->with('success', 'L\'absence a été mise à jour avec succès.');
    }

    /**
     * Supprime une absence
     */
    public function destroy(Absence $absence)
    {
        $this->authorize('delete', $absence);
        
        $absence->delete();
        
        return redirect()->route('absences.index')
            ->with('success', 'L\'absence a été supprimée avec succès.');
    }

    /**
     * Marque une absence comme justifiée
     */
    public function justifier(Request $request, Absence $absence)
    {
        $this->authorize('update', $absence);
        
        $validated = $request->validate([
            'justificatif' => 'required|string|max:255',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $absence->update([
            'justificatif' => $validated['justificatif'],
            'commentaire' => $validated['commentaire'] ?? null,
            'date_modification' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'L\'absence a été marquée comme justifiée.');
    }

    /**
     * Récupère les statistiques d'absences pour un étudiant
     */
    public function statistiquesEtudiant(Etudiant $etudiant, Request $request)
    {
        $this->authorize('view', $etudiant);
        
        $debut = $request->input('debut') ? Carbon::parse($request->input('debut')) : null;
        $fin = $request->input('fin') ? Carbon::parse($request->input('fin')) : null;
        
        $statistiques = $this->absenceService->getStatistiquesEtudiant($etudiant->id, $debut, $fin);
        
        return Inertia::render('Absences/StatistiquesEtudiant', [
            'etudiant' => [
                'id' => $etudiant->id,
                'nom_complet' => $etudiant->Nom . ' ' . $etudiant->Prenom,
                'classe' => $etudiant->classe ? $etudiant->classe->Libelle : 'Non défini',
            ],
            'statistiques' => $statistiques,
            'filtres' => [
                'debut' => $debut ? $debut->format('Y-m-d') : null,
                'fin' => $fin ? $fin->format('Y-m-d') : null,
            ]
        ]);
    }

    /**
     * Génère un rapport d'absences
     */
    public function genererRapport(Request $request)
    {
        $this->authorize('viewAny', Absence::class);
        
        $validated = $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'matiere_id' => 'nullable|exists:Matiere,id',
            'classe_id' => 'nullable|exists:Classe,id',
        ]);
        
        $rapport = $this->absenceService->genererRapportAbsences(
            Carbon::parse($validated['date_debut']),
            Carbon::parse($validated['date_fin']),
            $validated['matiere_id'] ?? null,
            $validated['classe_id'] ?? null
        );
        
        return response()->json($rapport);
    }
    
    /**
     * Envoie des notifications pour les absences non justifiées
     */
    public function notifierParents(Request $request)
    {
        $this->authorize('notify', Absence::class);
        
        $seuilJours = $request->input('seuil_jours', 3);
        $resultats = $this->absenceService->notifierParentsAbsencesNonJustifiees($seuilJours);
        
        return response()->json([
            'message' => 'Notifications envoyées avec succès',
            'notifications' => $resultats,
        ]);
    }
}
