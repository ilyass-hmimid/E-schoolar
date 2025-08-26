<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Enseignement;
use Inertia\Inertia;

class NoteController extends Controller
{
    /**
     * Affiche la liste des notes de l'élève
     */
    public function index()
    {
        $eleve = auth()->user();
        
        $notes = Note::where('eleve_id', $eleve->id)
            ->with(['enseignement.matiere', 'enseignement.professeur'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Calculer la moyenne générale
        $moyenne = Note::where('eleve_id', $eleve->id)
            ->selectRaw('AVG(note) as moyenne')
            ->first()
            ->moyenne;
            
        return Inertia::render('Eleve/Notes/Index', [
            'notes' => $notes,
            'moyenne' => $moyenne,
        ]);
    }
    
    /**
     * Affiche les détails d'une note
     */
    public function show(Note $note)
    {
        $this->authorize('view', $note);
        
        $note->load(['enseignement.matiere', 'enseignement.professeur']);
        
        return Inertia::render('Eleve/Notes/Show', [
            'note' => $note,
        ]);
    }
}
