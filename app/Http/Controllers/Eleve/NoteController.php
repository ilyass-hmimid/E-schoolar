<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Enseignement;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    /**
     * Affiche la liste des notes de l'élève
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        
        $notes = Note::where('etudiant_id', $user->id)
            ->with(['enseignement.matiere', 'enseignement.professeur'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Calculer la moyenne générale
        $moyenne = Note::where('etudiant_id', $user->id)
            ->select(DB::raw('AVG(note) as moyenne'))
            ->first()
            ->moyenne;
            
        $moyenne = $moyenne ? number_format($moyenne, 2, ',', ' ') : 'N/A';
        
        // Calculer les moyennes par matière
        $moyennesParMatiere = Note::where('etudiant_id', $user->id)
            ->join('enseignements', 'notes.enseignement_id', '=', 'enseignements.id')
            ->join('matieres', 'enseignements.matiere_id', '=', 'matieres.id')
            ->select('matieres.nom', DB::raw('AVG(notes.note) as moyenne'))
            ->groupBy('matieres.id', 'matieres.nom')
            ->get();
            
        return view('eleve.notes.index', [
            'notes' => $notes,
            'moyenne' => $moyenne,
            'moyennesParMatiere' => $moyennesParMatiere,
        ]);
    }
    
    /**
     * Affiche les détails d'une note
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\View\View
     */
    public function show(Note $note)
    {
        $this->authorize('view', $note);
        
        $note->load([
            'enseignement.matiere', 
            'enseignement.professeur',
            'enseignement.classe',
            'commentaires' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'commentaires.user'
        ]);
        
        // Récupérer la moyenne de la classe pour cette évaluation
        $moyenneClasse = Note::where('enseignement_id', $note->enseignement_id)
            ->where('type', $note->type)
            ->select(DB::raw('AVG(note) as moyenne'))
            ->first()
            ->moyenne;
            
        $moyenneClasse = $moyenneClasse ? number_format($moyenneClasse, 2, ',', ' ') : 'N/A';
        
        // Récupérer la position dans la classe
        $position = Note::where('enseignement_id', $note->enseignement_id)
            ->where('type', $note->type)
            ->where('note', '>', $note->note)
            ->count() + 1;
            
        $totalEleves = Note::where('enseignement_id', $note->enseignement_id)
            ->where('type', $note->type)
            ->distinct('eleve_id')
            ->count();
            
        $pourcentile = $totalEleves > 0 ? round(($position / $totalEleves) * 100) : 0;
        
        return view('eleve.notes.show', [
            'note' => $note,
            'moyenneClasse' => $moyenneClasse,
            'position' => $position,
            'totalEleves' => $totalEleves,
            'pourcentile' => $pourcentile,
        ]);
    }
}
