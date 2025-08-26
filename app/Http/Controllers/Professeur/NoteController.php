<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\Etudiant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NoteController extends Controller
{
    /**
     * Affiche la liste des notes
     */
    public function index()
    {
        $notes = Note::with(['etudiant', 'matiere'])
            ->where('professeur_id', Auth::id())
            ->latest()
            ->paginate(15);
            
        $matieres = Auth::user()->matieresEnseignees()
            ->orderBy('nom')
            ->get(['id', 'nom']);

        return Inertia::render('Professeur/Notes/Index', [
            'notes' => $notes,
            'matieres' => $matieres,
            'filters' => request()->only(['search', 'matiere', 'classe', 'date_debut', 'date_fin'])
        ]);
    }

    /**
     * Affiche le formulaire de création d'une note
     */
    public function create()
    {
        $matieres = Auth::user()->matieresEnseignees()
            ->orderBy('nom')
            ->get(['id', 'nom']);
            
        $classes = Classe::whereHas('enseignements', function($query) {
                $query->where('professeur_id', Auth::id());
            })
            ->orderBy('nom')
            ->get(['id', 'nom']);

        return Inertia::render('Professeur/Notes/Create', [
            'matieres' => $matieres,
            'classes' => $classes,
        ]);
    }

    /**
     * Enregistre une nouvelle note
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'matiere_id' => 'required|exists:matieres,id',
            'type_note' => 'required|in:devoir,composition,examen,participation',
            'valeur' => 'required|numeric|min:0|max:20',
            'coefficient' => 'required|numeric|min:0.1|max:5',
            'date_evaluation' => 'required|date',
            'commentaire' => 'nullable|string|max:500',
        ]);
        
        // Vérifier que la matière est bien enseignée par le professeur
        if (!Auth::user()->matieresEnseignees()->where('matieres.id', $validated['matiere_id'])->exists()) {
            return back()->withErrors(['matiere_id' => 'Vous n\'enseignez pas cette matière.']);
        }
        
        // Ajouter l'ID du professeur
        $validated['professeur_id'] = Auth::id();
        
        // Créer la note
        $note = Note::create($validated);
        
        return redirect()->route('professeur.notes.index')
            ->with('success', 'La note a été enregistrée avec succès.');
    }

    /**
     * Affiche les détails d'une note
     */
    public function show($id)
    {
        $note = Note::with(['etudiant', 'matiere', 'professeur'])
            ->where('id', $id)
            ->where('professeur_id', Auth::id())
            ->firstOrFail();

        return Inertia::render('Professeur/Notes/Show', [
            'note' => $note
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'une note
     */
    public function edit($id)
    {
        $note = Note::where('id', $id)
            ->where('professeur_id', Auth::id())
            ->firstOrFail();
            
        $matieres = Auth::user()->matieresEnseignees()
            ->orderBy('nom')
            ->get(['id', 'nom']);
            
        $etudiants = Etudiant::whereHas('inscriptions', function($query) use ($note) {
                $query->whereIn('classe_id', function($q) use ($note) {
                    $q->select('classe_id')
                      ->from('enseignements')
                      ->where('matiere_id', $note->matiere_id)
                      ->where('professeur_id', Auth::id());
                });
            })
            ->orWhere('id', $note->etudiant_id)
            ->orderBy('nom')
            ->get(['id', 'nom', 'prenom']);

        return Inertia::render('Professeur/Notes/Edit', [
            'note' => $note,
            'matieres' => $matieres,
            'etudiants' => $etudiants,
        ]);
    }

    /**
     * Met à jour une note existante
     */
    public function update(Request $request, $id)
    {
        $note = Note::where('id', $id)
            ->where('professeur_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'matiere_id' => 'required|exists:matieres,id',
            'type_note' => 'required|in:devoir,composition,examen,participation',
            'valeur' => 'required|numeric|min:0|max:20',
            'coefficient' => 'required|numeric|min:0.1|max:5',
            'date_evaluation' => 'required|date',
            'commentaire' => 'nullable|string|max:500',
        ]);
        
        // Vérifier que la matière est bien enseignée par le professeur
        if (!Auth::user()->matieresEnseignees()->where('matieres.id', $validated['matiere_id'])->exists()) {
            return back()->withErrors(['matiere_id' => 'Vous n\'enseignez pas cette matière.']);
        }
        
        $note->update($validated);
        
        return redirect()->route('professeur.notes.index')
            ->with('success', 'La note a été mise à jour avec succès.');
    }
    
    /**
     * Supprime une note
     */
    public function destroy($id)
    {
        $note = Note::where('id', $id)
            ->where('professeur_id', Auth::id())
            ->firstOrFail();
            
        $note->delete();
        
        return redirect()->route('professeur.notes.index')
            ->with('success', 'La note a été supprimée avec succès.');
    }
    
    /**
     * Récupère les étudiants d'une classe pour une matière donnée
     */
    public function getEtudiantsByClasseMatiere($classeId, $matiereId)
    {
        // Vérifier que le professeur enseigne bien cette matière
        if (!Auth::user()->matieresEnseignees()->where('matieres.id', $matiereId)->exists()) {
            return response()->json(['error' => 'Accès non autorisé.'], 403);
        }
        
        $etudiants = Etudiant::whereHas('inscriptions', function($query) use ($classeId) {
                $query->where('classe_id', $classeId);
            })
            ->orderBy('nom')
            ->get(['id', 'nom', 'prenom']);
            
        return response()->json($etudiants);
    }
    
    /**
     * Récupère les notes d'un étudiant pour une matière donnée
     */
    public function getNotesEtudiantMatiere($etudiantId, $matiereId)
    {
        // Vérifier que le professeur enseigne bien cette matière
        if (!Auth::user()->matieresEnseignees()->where('matieres.id', $matiereId)->exists()) {
            return response()->json(['error' => 'Accès non autorisé.'], 403);
        }
        
        $notes = Note::where('etudiant_id', $etudiantId)
            ->where('matiere_id', $matiereId)
            ->where('professeur_id', Auth::id())
            ->orderBy('date_evaluation', 'desc')
            ->get();
            
        return response()->json($notes);
    }
}
