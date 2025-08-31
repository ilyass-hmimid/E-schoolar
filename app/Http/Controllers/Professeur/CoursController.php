<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use App\Models\Enseignement;
use App\Models\Matiere;
use App\Models\Niveau;
use App\Models\Filiere;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CoursController extends Controller
{
    /**
     * Affiche la liste des cours du professeur
     */
    public function index()
    {
        // Récupérer l'ID du professeur connecté
        $professeurId = Auth::id();
        
        // Récupérer les cours du professeur avec les relations chargées
        $cours = Cache::remember("professeur.{$professeurId}.cours", now()->addHours(1), function () use ($professeurId) {
            return Enseignement::with(['matiere', 'niveau', 'filiere'])
                ->where('professeur_id', $professeurId)
                ->where('est_actif', true)
                ->orderBy('jour_cours')
                ->orderBy('heure_debut')
                ->get()
                ->map(function ($cours) {
                    return [
                        'id' => $cours->id,
                        'matiere' => $cours->matiere->nom,
                        'niveau' => $cours->niveau->nom,
                        'filiere' => $cours->filiere->nom,
                        'jour' => $this->getJourSemaine($cours->jour_cours),
                        'heure_debut' => $cours->heure_debut->format('H:i'),
                        'heure_fin' => $cours->heure_fin->format('H:i'),
                        'duree' => $cours->heure_fin->diffInHours($cours->heure_debut) . 'h',
                        'est_actif' => $cours->est_actif,
                    ];
                });
        });

        return Inertia::render('Professeur/Cours/Index', [
            'cours' => $cours,
            'joursSemaine' => $this->getJoursSemaine()
        ]);
    }

    /**
     * Affiche le formulaire de création d'un cours
     */
    public function create()
    {
        $professeurId = Auth::id();
        
        // Récupérer les matières, niveaux et filières avec mise en cache
        $matieres = Cache::remember('matieres.liste', 3600, function () {
            return Matiere::select('id', 'nom', 'code')
                ->orderBy('nom')
                ->get();
        });

        $niveaux = Cache::remember('niveaux.liste', 3600, function () {
            return Niveau::select('id', 'nom')
                ->orderBy('ordre')
                ->get();
        });

        $filieres = Cache::remember('filieres.liste', 3600, function () {
            return Filiere::select('id', 'nom', 'code')
                ->orderBy('nom')
                ->get();
        });

        return Inertia::render('Professeur/Cours/Create', [
            'matieres' => $matieres,
            'niveaux' => $niveaux,
            'filieres' => $filieres,
            'joursSemaine' => $this->getJoursSemaine()
        ]);
    }

    /**
     * Enregistre un nouveau cours
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'matiere_id' => 'required|exists:matieres,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'required|exists:filieres,id',
            'jour_cours' => 'required|integer|between:1,7',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'est_actif' => 'boolean'
        ]);

        // Ajouter l'ID du professeur connecté
        $validated['professeur_id'] = Auth::id();
        $validated['est_actif'] = $validated['est_actif'] ?? true;

        // Créer le cours
        $cours = Enseignement::create($validated);

        // Invalider le cache
        Cache::forget('professeur.' . Auth::id() . '.cours');

        return redirect()->route('professeur.cours.index')
            ->with('success', 'Le cours a été créé avec succès.');
    }

    /**
     * Affiche les détails d'un cours
     */
    public function show($id)
    {
        $cours = Enseignement::with(['matiere', 'niveau', 'filiere'])
            ->where('professeur_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('Professeur/Cours/Show', [
            'cours' => [
                'id' => $cours->id,
                'matiere' => $cours->matiere->nom,
                'niveau' => $cours->niveau->nom,
                'filiere' => $cours->filiere->nom,
                'jour' => $this->getJourSemaine($cours->jour_cours),
                'jour_num' => $cours->jour_cours,
                'heure_debut' => $cours->heure_debut->format('H:i'),
                'heure_fin' => $cours->heure_fin->format('H:i'),
                'est_actif' => $cours->est_actif,
                'created_at' => $cours->created_at->format('d/m/Y H:i'),
                'updated_at' => $cours->updated_at->format('d/m/Y H:i')
            ]
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'un cours
     */
    public function edit($id)
    {
        $cours = Enseignement::where('professeur_id', Auth::id())
            ->findOrFail($id);

        $matieres = Cache::remember('matieres.liste', 3600, function () {
            return Matiere::select('id', 'nom', 'code')
                ->orderBy('nom')
                ->get();
        });

        $niveaux = Cache::remember('niveaux.liste', 3600, function () {
            return Niveau::select('id', 'nom')
                ->orderBy('ordre')
                ->get();
        });

        $filieres = Cache::remember('filieres.liste', 3600, function () {
            return Filiere::select('id', 'nom', 'code')
                ->orderBy('nom')
                ->get();
        });

        return Inertia::render('Professeur/Cours/Edit', [
            'cours' => [
                'id' => $cours->id,
                'matiere_id' => $cours->matiere_id,
                'niveau_id' => $cours->niveau_id,
                'filiere_id' => $cours->filiere_id,
                'jour_cours' => (int)$cours->jour_cours,
                'heure_debut' => $cours->heure_debut->format('H:i'),
                'heure_fin' => $cours->heure_fin->format('H:i'),
                'est_actif' => $cours->est_actif
            ],
            'matieres' => $matieres,
            'niveaux' => $niveaux,
            'filieres' => $filieres,
            'joursSemaine' => $this->getJoursSemaine()
        ]);
    }

    /**
     * Met à jour un cours existant
     */
    public function update(Request $request, $id)
    {
        $cours = Enseignement::where('professeur_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'matiere_id' => 'required|exists:matieres,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'required|exists:filieres,id',
            'jour_cours' => 'required|integer|between:1,7',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'est_actif' => 'boolean'
        ]);

        $cours->update($validated);

        // Invalider le cache
        Cache::forget('professeur.' . Auth::id() . '.cours');

        return redirect()->route('professeur.cours.index')
            ->with('success', 'Le cours a été mis à jour avec succès.');
    }

    /**
     * Supprime un cours
     */
    public function destroy($id)
    {
        $cours = Enseignement::where('professeur_id', Auth::id())
            ->findOrFail($id);
            
        $cours->delete();

        // Invalider le cache
        Cache::forget('professeur.' . Auth::id() . '.cours');

        return redirect()->route('professeur.cours.index')
            ->with('success', 'Le cours a été supprimé avec succès.');
    }

    /**
     * Retourne le libellé du jour de la semaine
     */
    private function getJourSemaine($jourNumero)
    {
        $jours = $this->getJoursSemaine();
        return $jours[$jourNumero - 1] ?? 'Inconnu';
    }

    /**
     * Retourne la liste des jours de la semaine
     */
    private function getJoursSemaine()
    {
        return [
            'Lundi',
            'Mardi',
            'Mercredi',
            'Jeudi',
            'Vendredi',
            'Samedi',
            'Dimanche'
        ];
    }
}
