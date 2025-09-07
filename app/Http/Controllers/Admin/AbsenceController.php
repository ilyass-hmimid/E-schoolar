<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\User;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsenceController extends Controller
{
    /**
     * Affiche la liste des absences
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Absence::with(['eleve', 'matiere', 'professeur'])
            ->latest('date_absence');
            
        // Filtrage par élève
        if ($request->has('eleve_id') && $request->eleve_id) {
            $query->where('eleve_id', $request->eleve_id);
        }
        
        // Filtrage par professeur
        if ($request->has('professeur_id') && $request->professeur_id) {
            $query->where('professeur_id', $request->professeur_id);
        }
        
        // Filtrage par matière
        if ($request->has('matiere_id') && $request->matiere_id) {
            $query->where('matiere_id', $request->matiere_id);
        }
        
        // Filtrage par statut
        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }
        
        // Filtrage par date
        if ($request->has('date_debut') && $request->date_debut) {
            $query->whereDate('date_absence', '>=', $request->date_debut);
        }
        
        if ($request->has('date_fin') && $request->date_fin) {
            $query->whereDate('date_absence', '<=', $request->date_fin);
        }
        
        $absences = $query->paginate(20);
        $eleves = User::where('role', 'eleve')->orderBy('name')->get();
        $professeurs = User::where('role', 'professeur')->orderBy('name')->get();
        $matieres = Matiere::orderBy('nom')->get();
        
        return view('admin.absences.index', compact('absences', 'eleves', 'professeurs', 'matieres'));
    }

    /**
     * Affiche le formulaire de création d'une absence
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $eleves = User::where('role', 'eleve')
            ->where('status', 'actif')
            ->orderBy('name')
            ->get();
            
        $professeurs = User::where('role', 'professeur')
            ->where('status', 'actif')
            ->orderBy('name')
            ->get();
            
        $matieres = Matiere::orderBy('nom')->get();
        
        return view('admin.absences.create', compact('eleves', 'professeurs', 'matieres'));
    }

    /**
     * Enregistre une nouvelle absence
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'eleve_id' => 'required|exists:users,id',
            'matiere_id' => 'required|exists:matieres,id',
            'professeur_id' => 'required|exists:users,id',
            'date_absence' => 'required|date|before_or_equal:today',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'motif' => 'nullable|string|max:1000',
            'commentaire' => 'nullable|string|max:1000',
        ]);
        
        // Vérifier que l'élève est bien actif
        $eleve = User::findOrFail($validated['eleve_id']);
        if ($eleve->role !== 'eleve' || $eleve->status !== 'actif') {
            return back()->with('error', 'Seuls les élèves actifs peuvent être marqués absents.');
        }
        
        // Vérifier que le professeur est bien un professeur actif
        $professeur = User::findOrFail($validated['professeur_id']);
        if ($professeur->role !== 'professeur' || $professeur->status !== 'actif') {
            return back()->with('error', 'Le professeur sélectionné n\'est pas valide.');
        }
        
        // Vérifier que la matière existe
        $matiere = Matiere::findOrFail($validated['matiere_id']);
        
        // Vérifier que l'élève est inscrit à la matière
        if (!$eleve->matieres->contains($matiere->id)) {
            return back()->with('error', 'L\'élève n\'est pas inscrit à cette matière.');
        }
        
        // Vérifier que le professeur enseigne cette matière
        if (!$professeur->matieresEnseignees->contains($matiere->id)) {
            return back()->with('error', 'Le professeur n\'enseigne pas cette matière.');
        }
        
        // Calculer la durée en minutes
        $debut = Carbon::parse($validated['date_absence'] . ' ' . $validated['heure_debut']);
        $fin = Carbon::parse($validated['date_absence'] . ' ' . $validated['heure_fin']);
        $duree_minutes = $fin->diffInMinutes($debut);
        
        // Vérifier les chevauchements d'absences
        $chevauchant = Absence::where('eleve_id', $eleve->id)
            ->where('date_absence', $validated['date_absence'])
            ->where(function($query) use ($debut, $fin) {
                $query->where(function($q) use ($debut, $fin) {
                    $q->where('heure_debut', '<=', $debut->format('H:i:s'))
                      ->where('heure_fin', '>', $debut->format('H:i:s'));
                })->orWhere(function($q) use ($debut, $fin) {
                    $q->where('heure_debut', '<', $fin->format('H:i:s'))
                      ->where('heure_fin', '>=', $fin->format('H:i:s'));
                })->orWhere(function($q) use ($debut, $fin) {
                    $q->where('heure_debut', '>=', $debut->format('H:i:s'))
                      ->where('heure_fin', '<=', $fin->format('H:i:s'));
                });
            })
            ->exists();
            
        if ($chevauchant) {
            return back()->with('error', 'L\'élève a déjà une absence qui chevauche cette plage horaire.');
        }
        
        // Créer l'absence
        $absence = new Absence([
            'eleve_id' => $validated['eleve_id'],
            'matiere_id' => $validated['matiere_id'],
            'professeur_id' => $validated['professeur_id'],
            'date_absence' => $validated['date_absence'],
            'heure_debut' => $validated['heure_debut'],
            'heure_fin' => $validated['heure_fin'],
            'duree_minutes' => $duree_minutes,
            'motif' => $validated['motif'] ?? null,
            'commentaire' => $validated['commentaire'] ?? null,
            'statut' => 'non_justifiee',
            'enregistre_par' => auth()->id(),
        ]);
        
        $absence->save();
        
        return redirect()->route('admin.absences.show', $absence)
            ->with('success', 'Absence enregistrée avec succès.');
    }

    /**
     * Affiche les détails d'une absence
     *
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\View\View
     */
    public function show(Absence $absence)
    {
        $absence->load(['eleve', 'matiere', 'professeur', 'enregistrePar']);
        return view('admin.absences.show', compact('absence'));
    }

    /**
     * Affiche le formulaire d'édition d'une absence
     *
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\View\View
     */
    public function edit(Absence $absence)
    {
        $eleves = User::where('role', 'eleve')
            ->where('status', 'actif')
            ->orderBy('name')
            ->get();
            
        $professeurs = User::where('role', 'professeur')
            ->where('status', 'actif')
            ->orderBy('name')
            ->get();
            
        $matieres = Matiere::orderBy('nom')->get();
        
        return view('admin.absences.edit', compact('absence', 'eleves', 'professeurs', 'matieres'));
    }

    /**
     * Met à jour une absence
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Absence $absence)
    {
        $validated = $request->validate([
            'eleve_id' => 'required|exists:users,id',
            'matiere_id' => 'required|exists:matieres,id',
            'professeur_id' => 'required|exists:users,id',
            'date_absence' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'motif' => 'nullable|string|max:1000',
            'commentaire' => 'nullable|string|max:1000',
            'statut' => 'required|in:en_attente,justifiee,non_justifiee',
        ]);
        
        // Vérifier que l'élève est bien actif
        $eleve = User::findOrFail($validated['eleve_id']);
        if ($eleve->role !== 'eleve' || $eleve->status !== 'actif') {
            return back()->with('error', 'Seuls les élèves actifs peuvent être marqués absents.');
        }
        
        // Vérifier que le professeur est bien un professeur actif
        $professeur = User::findOrFail($validated['professeur_id']);
        if ($professeur->role !== 'professeur' || $professeur->status !== 'actif') {
            return back()->with('error', 'Le professeur sélectionné n\'est pas valide.');
        }
        
        // Vérifier que la matière existe
        $matiere = Matiere::findOrFail($validated['matiere_id']);
        
        // Vérifier que l'élève est inscrit à la matière
        if (!$eleve->matieres->contains($matiere->id)) {
            return back()->with('error', 'L\'élève n\'est pas inscrit à cette matière.');
        }
        
        // Vérifier que le professeur enseigne cette matière
        if (!$professeur->matieresEnseignees->contains($matiere->id)) {
            return back()->with('error', 'Le professeur n\'enseigne pas cette matière.');
        }
        
        // Calculer la durée en minutes
        $debut = Carbon::parse($validated['date_absence'] . ' ' . $validated['heure_debut']);
        $fin = Carbon::parse($validated['date_absence'] . ' ' . $validated['heure_fin']);
        $duree_minutes = $fin->diffInMinutes($debut);
        
        // Vérifier les chevauchements d'absences (sauf pour l'absence actuelle)
        $chevauchant = Absence::where('id', '!=', $absence->id)
            ->where('eleve_id', $eleve->id)
            ->where('date_absence', $validated['date_absence'])
            ->where(function($query) use ($debut, $fin) {
                $query->where(function($q) use ($debut, $fin) {
                    $q->where('heure_debut', '<=', $debut->format('H:i:s'))
                      ->where('heure_fin', '>', $debut->format('H:i:s'));
                })->orWhere(function($q) use ($debut, $fin) {
                    $q->where('heure_debut', '<', $fin->format('H:i:s'))
                      ->where('heure_fin', '>=', $fin->format('H:i:s'));
                })->orWhere(function($q) use ($debut, $fin) {
                    $q->where('heure_debut', '>=', $debut->format('H:i:s'))
                      ->where('heure_fin', '<=', $fin->format('H:i:s'));
                });
            })
            ->exists();
            
        if ($chevauchant) {
            return back()->with('error', 'L\'élève a déjà une absence qui chevauche cette plage horaire.');
        }
        
        // Mettre à jour l'absence
        $absence->update([
            'eleve_id' => $validated['eleve_id'],
            'matiere_id' => $validated['matiere_id'],
            'professeur_id' => $validated['professeur_id'],
            'date_absence' => $validated['date_absence'],
            'heure_debut' => $validated['heure_debut'],
            'heure_fin' => $validated['heure_fin'],
            'duree_minutes' => $duree_minutes,
            'motif' => $validated['motif'] ?? null,
            'commentaire' => $validated['commentaire'] ?? null,
            'statut' => $validated['statut'],
            'traite_par' => $validated['statut'] !== 'en_attente' ? auth()->id() : null,
            'date_traitement' => $validated['statut'] !== 'en_attente' ? now() : null,
        ]);
        
        return redirect()->route('admin.absences.show', $absence)
            ->with('success', 'Absence mise à jour avec succès.');
    }

    /**
     * Justifie une absence
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\RedirectResponse
     */
    public function justifier(Request $request, Absence $absence)
    {
        $validated = $request->validate([
            'commentaire' => 'nullable|string|max:1000',
        ]);
        
        if ($absence->statut === 'justifiee') {
            return back()->with('info', 'Cette absence est déjà justifiée.');
        }
        
        $absence->update([
            'statut' => 'justifiee',
            'commentaire' => $validated['commentaire'] ?? $absence->commentaire,
            'traite_par' => auth()->id(),
            'date_traitement' => now(),
        ]);
        
        return back()->with('success', 'L\'absence a été marquée comme justifiée.');
    }

    /**
     * Marque une absence comme non justifiée
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\RedirectResponse
     */
    public function nonJustifier(Request $request, Absence $absence)
    {
        $validated = $request->validate([
            'commentaire' => 'nullable|string|max:1000',
        ]);
        
        if ($absence->statut === 'non_justifiee') {
            return back()->with('info', 'Cette absence est déjà marquée comme non justifiée.');
        }
        
        $absence->update([
            'statut' => 'non_justifiee',
            'commentaire' => $validated['commentaire'] ?? $absence->commentaire,
            'traite_par' => auth()->id(),
            'date_traitement' => now(),
        ]);
        
        return back()->with('success', 'L\'absence a été marquée comme non justifiée.');
    }

    /**
     * Supprime une absence
     *
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Absence $absence)
    {
        $absence->delete();
        
        return redirect()->route('admin.absences.index')
            ->with('success', 'L\'absence a été supprimée avec succès.');
    }
    
    /**
     * Affiche le rapport des absences
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function rapport(Request $request)
    {
        $query = Absence::with(['eleve', 'matiere', 'professeur'])
            ->select(
                'absences.*',
                DB::raw('YEAR(date_absence) as annee'),
                DB::raw('MONTH(date_absence) as mois')
            )
            ->orderBy('date_absence', 'desc');
            
        // Filtrage par année
        $annee = $request->input('annee', date('Y'));
        $query->whereYear('date_absence', $annee);
        
        // Filtrage par mois
        if ($request->has('mois') && $request->mois) {
            $query->whereMonth('date_absence', $request->mois);
        }
        
        // Filtrage par statut
        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }
        
        // Filtrage par élève
        if ($request->has('eleve_id') && $request->eleve_id) {
            $query->where('eleve_id', $request->eleve_id);
        }
        
        // Filtrage par professeur
        if ($request->has('professeur_id') && $request->professeur_id) {
            $query->where('professeur_id', $request->professeur_id);
        }
        
        // Filtrage par matière
        if ($request->has('matiere_id') && $request->matiere_id) {
            $query->where('matiere_id', $request->matiere_id);
        }
        
        $absences = $query->get();
        
        // Calcul des statistiques
        $totalAbsences = $absences->count();
        $totalHeures = $absences->sum('duree_minutes') / 60;
        $moyenneParEleve = $absences->groupBy('eleve_id')->count() > 0 
            ? $totalAbsences / $absences->groupBy('eleve_id')->count() 
            : 0;
            
        $parMatiere = $absences->groupBy('matiere.nom')->map(function($absences) {
            return [
                'count' => $absences->count(),
                'heures' => $absences->sum('duree_minutes') / 60,
            ];
        });
        
        $parStatut = $absences->groupBy('statut')->map->count();
        
        $eleves = User::where('role', 'eleve')->orderBy('name')->get();
        $professeurs = User::where('role', 'professeur')->orderBy('name')->get();
        $matieres = Matiere::orderBy('nom')->get();
        $annees = range(date('Y') - 5, date('Y') + 1);
        
        return view('admin.absences.rapport', compact(
            'absences',
            'totalAbsences',
            'totalHeures',
            'moyenneParEleve',
            'parMatiere',
            'parStatut',
            'eleves',
            'professeurs',
            'matieres',
            'annees',
            'annee'
        ));
    }
}
