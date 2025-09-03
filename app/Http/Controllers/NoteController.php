<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\TypeEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class NoteController extends Controller
{
    /**
     * Affiche la liste des notes avec filtres
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Note::class);
        
        $filters = $request->only([
            'etudiant_id', 'matiere_id', 'classe_id', 'type_evaluation_id',
            'date_debut', 'date_fin', 'note_min', 'note_max'
        ]);

        $query = Note::with([
            'etudiant:id,Nom,Prenom', 
            'matiere:id,Libelle',
            'typeEvaluation:id,Libelle,Coefficient'
        ]);

        // Appliquer les filtres
        if (!empty($filters['etudiant_id'])) {
            $query->where('IdEtu', $filters['etudiant_id']);
        }

        if (!empty($filters['matiere_id'])) {
            $query->where('IdMat', $filters['matiere_id']);
        }

        if (!empty($filters['classe_id'])) {
            $query->whereHas('etudiant', function($q) use ($filters) {
                $q->where('IdClasse', $filters['classe_id']);
            });
        }

        if (!empty($filters['type_evaluation_id'])) {
            $query->where('IdTypeEval', $filters['type_evaluation_id']);
        }

        if (!empty($filters['date_debut'])) {
            $query->whereDate('Date_Eval', '>=', $filters['date_debut']);
        }

        if (!empty($filters['date_fin'])) {
            $query->whereDate('Date_Eval', '<=', $filters['date_fin']);
        }

        if (isset($filters['note_min']) && is_numeric($filters['note_min'])) {
            $query->where('Note', '>=', (float)$filters['note_min']);
        }

        if (isset($filters['note_max']) && is_numeric($filters['note_max'])) {
            $query->where('Note', '<=', (float)$filters['note_max']);
        }

        $notes = $query->latest('Date_Eval')
            ->paginate(15)
            ->withQueryString()
            ->through(function ($note) {
                return [
                    'id' => $note->id,
                    'etudiant' => $note->etudiant ? $note->etudiant->Nom . ' ' . $note->etudiant->Prenom : 'Inconnu',
                    'matiere' => $note->matiere ? $note->matiere->Libelle : 'Inconnue',
                    'type_evaluation' => $note->typeEvaluation ? $note->typeEvaluation->Libelle : 'Inconnu',
                    'coefficient' => $note->typeEvaluation ? $note->typeEvaluation->Coefficient : 1,
                    'note' => $note->Note,
                    'note_sur_20' => $this->convertirEnNoteSur20($note->Note, $note->typeEvaluation),
                    'appreciation' => $this->getAppreciation($note->Note, $note->typeEvaluation),
                    'date_evaluation' => $note->Date_Eval->format('d/m/Y'),
                    'commentaire' => $note->Commentaire,
                ];
            });

        return Inertia::render('Notes/Index', [
            'notes' => $notes,
            'filters' => $filters,
            'matieres' => Matiere::select('id', 'Libelle')
                ->orderBy('Libelle')
                ->get(),
            'classes' => Classe::select('id', 'Libelle')
                ->orderBy('Libelle')
                ->get(),
            'types_evaluation' => TypeEvaluation::select('id', 'Libelle', 'Coefficient')
                ->orderBy('Libelle')
                ->get(),
            'can' => [
                'create' => auth()->user()->can('create', Note::class),
                'edit' => auth()->user()->can('update', Note::class),
                'delete' => auth()->user()->can('delete', Note::class),
                'export' => auth()->user()->can('export', Note::class),
            ]
        ]);
    }

    /**
     * Affiche le formulaire de création d'une note
     */
    public function create()
    {
        $this->authorize('create', Note::class);
        
        return Inertia::render('Notes/Create', [
            'etudiants' => Etudiant::select('id', 'Nom', 'Prenom', 'IdClasse')
                ->with('classe:id,Libelle')
                ->orderBy('Nom')
                ->get()
                ->map(function($etudiant) {
                    return [
                        'id' => $etudiant->id,
                        'nom_complet' => $etudiant->Nom . ' ' . $etudiant->Prenom,
                        'classe' => $etudiant->classe ? $etudiant->classe->Libelle : 'Non défini',
                    ];
                }),
            'matieres' => Matiere::select('id', 'Libelle')
                ->orderBy('Libelle')
                ->get(),
            'types_evaluation' => TypeEvaluation::select('id', 'Libelle', 'Coefficient', 'NoteMaximale')
                ->orderBy('Libelle')
                ->get()
                ->map(function($type) {
                    return [
                        'id' => $type->id,
                        'libelle' => $type->Libelle,
                        'coefficient' => $type->Coefficient,
                        'note_maximale' => $type->NoteMaximale,
                    ];
                }),
        ]);
    }

    /**
     * Enregistre une nouvelle note
     */
    public function store(Request $request)
    {
        $this->authorize('create', Note::class);
        
        $typeEvaluation = TypeEvaluation::findOrFail($request->input('type_evaluation_id'));
        
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:Etudiant,id',
            'matiere_id' => 'required|exists:Matiere,id',
            'type_evaluation_id' => 'required|exists:Type_Evaluation,id',
            'note' => [
                'required', 
                'numeric', 
                'min:0',
                'max:' . $typeEvaluation->NoteMaximale,
            ],
            'date_evaluation' => 'required|date',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $note = Note::create([
            'IdEtu' => $validated['etudiant_id'],
            'IdMat' => $validated['matiere_id'],
            'IdTypeEval' => $validated['type_evaluation_id'],
            'Note' => $validated['note'],
            'Date_Eval' => $validated['date_evaluation'],
            'Commentaire' => $validated['commentaire'] ?? null,
            'Date_Ajout' => now(),
        ]);

        return redirect()->route('notes.show', $note)
            ->with('success', 'La note a été enregistrée avec succès.');
    }

    /**
     * Affiche les détails d'une note
     */
    public function show(Note $note)
    {
        $this->authorize('view', $note);
        
        $note->load([
            'etudiant:id,Nom,Prenom,IdClasse',
            'etudiant.classe:id,Libelle',
            'matiere:id,Libelle',
            'typeEvaluation:id,Libelle,Coefficient,NoteMaximale',
        ]);
        
        $noteSur20 = $this->convertirEnNoteSur20($note->Note, $note->typeEvaluation);
        $appreciation = $this->getAppreciation($note->Note, $note->typeEvaluation);

        return Inertia::render('Notes/Show', [
            'note' => [
                'id' => $note->id,
                'etudiant' => [
                    'id' => $note->etudiant->id,
                    'nom_complet' => $note->etudiant->Nom . ' ' . $note->etudiant->Prenom,
                    'classe' => $note->etudiant->classe ? $note->etudiant->classe->Libelle : 'Non défini',
                ],
                'matiere' => [
                    'id' => $note->matiere->id,
                    'libelle' => $note->matiere->Libelle,
                ],
                'type_evaluation' => [
                    'id' => $note->typeEvaluation->id,
                    'libelle' => $note->typeEvaluation->Libelle,
                    'coefficient' => $note->typeEvaluation->Coefficient,
                    'note_maximale' => $note->typeEvaluation->NoteMaximale,
                ],
                'note' => $note->Note,
                'note_sur_20' => $noteSur20,
                'appreciation' => $appreciation,
                'date_evaluation' => $note->Date_Eval->format('d/m/Y'),
                'commentaire' => $note->Commentaire,
                'date_ajout' => $note->Date_Ajout->format('d/m/Y H:i'),
            ]
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'une note
     */
    public function edit(Note $note)
    {
        $this->authorize('update', $note);
        
        $note->load([
            'etudiant:id,Nom,Prenom',
            'matiere:id,Libelle',
            'typeEvaluation:id,Libelle,Coefficient,NoteMaximale',
        ]);

        return Inertia::render('Notes/Edit', [
            'note' => [
                'id' => $note->id,
                'etudiant' => [
                    'id' => $note->etudiant->id,
                    'nom_complet' => $note->etudiant->Nom . ' ' . $note->etudiant->Prenom,
                ],
                'matiere' => [
                    'id' => $note->matiere->id,
                    'libelle' => $note->matiere->Libelle,
                ],
                'type_evaluation' => [
                    'id' => $note->typeEvaluation->id,
                    'libelle' => $note->typeEvaluation->Libelle,
                    'coefficient' => $note->typeEvaluation->Coefficient,
                    'note_maximale' => $note->typeEvaluation->NoteMaximale,
                ],
                'note' => $note->Note,
                'date_evaluation' => $note->Date_Eval->format('Y-m-d'),
                'commentaire' => $note->Commentaire,
            ],
            'types_evaluation' => TypeEvaluation::select('id', 'Libelle', 'Coefficient', 'NoteMaximale')
                ->orderBy('Libelle')
                ->get()
                ->map(function($type) {
                    return [
                        'id' => $type->id,
                        'libelle' => $type->Libelle,
                        'coefficient' => $type->Coefficient,
                        'note_maximale' => $type->NoteMaximale,
                    ];
                }),
        ]);
    }

    /**
     * Met à jour une note existante
     */
    public function update(Request $request, Note $note)
    {
        $this->authorize('update', $note);
        
        $typeEvaluation = TypeEvaluation::findOrFail($request->input('type_evaluation_id', $note->IdTypeEval));
        
        $validated = $request->validate([
            'type_evaluation_id' => 'required|exists:Type_Evaluation,id',
            'note' => [
                'required', 
                'numeric', 
                'min:0',
                'max:' . $typeEvaluation->NoteMaximale,
            ],
            'date_evaluation' => 'required|date',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $note->update([
            'IdTypeEval' => $validated['type_evaluation_id'],
            'Note' => $validated['note'],
            'Date_Eval' => $validated['date_evaluation'],
            'Commentaire' => $validated['commentaire'] ?? null,
        ]);

        return redirect()->route('notes.show', $note)
            ->with('success', 'La note a été mise à jour avec succès.');
    }

    /**
     * Supprime une note
     */
    public function destroy(Note $note)
    {
        $this->authorize('delete', $note);
        
        $note->delete();
        
        return redirect()->route('notes.index')
            ->with('success', 'La note a été supprimée avec succès.');
    }

    /**
     * Exporte les notes au format CSV
     */
    public function exportCsv(Request $request)
    {
        $this->authorize('export', Note::class);
        
        $filters = $request->only([
            'etudiant_id', 'matiere_id', 'classe_id', 'type_evaluation_id',
            'date_debut', 'date_fin'
        ]);

        $query = Note::with([
            'etudiant:id,Nom,Prenom', 
            'matiere:id,Libelle',
            'typeEvaluation:id,Libelle,Coefficient',
            'etudiant.classe:id,Libelle'
        ]);

        // Appliquer les mêmes filtres que pour l'index
        if (!empty($filters['etudiant_id'])) {
            $query->where('IdEtu', $filters['etudiant_id']);
        }

        if (!empty($filters['matiere_id'])) {
            $query->where('IdMat', $filters['matiere_id']);
        }

        if (!empty($filters['classe_id'])) {
            $query->whereHas('etudiant', function($q) use ($filters) {
                $q->where('IdClasse', $filters['classe_id']);
            });
        }

        if (!empty($filters['type_evaluation_id'])) {
            $query->where('IdTypeEval', $filters['type_evaluation_id']);
        }

        if (!empty($filters['date_debut'])) {
            $query->whereDate('Date_Eval', '>=', $filters['date_debut']);
        }

        if (!empty($filters['date_fin'])) {
            $query->whereDate('Date_Eval', '<=', $filters['date_fin']);
        }

        $notes = $query->orderBy('Date_Eval', 'desc')->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=notes_" . date('Y-m-d') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($notes) {
            $file = fopen('php://output', 'w');
            
            // En-tête du CSV
            fputcsv($file, [
                'Étudiant',
                'Classe',
                'Matière',
                'Type d\'évaluation',
                'Note',
                'Note sur 20',
                'Coefficient',
                'Appréciation',
                'Date d\'évaluation',
                'Commentaire'
            ], ';');
            
            // Données
            foreach ($notes as $note) {
                $noteSur20 = $this->convertirEnNoteSur20($note->Note, $note->typeEvaluation);
                $appreciation = $this->getAppreciation($note->Note, $note->typeEvaluation);
                
                fputcsv($file, [
                    $note->etudiant ? $note->etudiant->Nom . ' ' . $note->etudiant->Prenom : 'Inconnu',
                    $note->etudiant && $note->etudiant->classe ? $note->etudiant->classe->Libelle : 'Non défini',
                    $note->matiere ? $note->matiere->Libelle : 'Inconnue',
                    $note->typeEvaluation ? $note->typeEvaluation->Libelle : 'Inconnu',
                    str_replace('.', ',', $note->Note),
                    str_replace('.', ',', $noteSur20),
                    $note->typeEvaluation ? $note->typeEvaluation->Coefficient : 1,
                    $appreciation,
                    $note->Date_Eval->format('d/m/Y'),
                    $note->Commentaire ?? '',
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Convertit une note en note sur 20
     * Note: Dans la nouvelle structure, les notes sont déjà stockées sur 20
     */
    private function convertirEnNoteSur20($note, $typeEvaluation = null)
    {
        // Dans la nouvelle structure, les notes sont déjà sur 20
        return $note;
    }
    
    /**
     * Retourne une appréciation en fonction de la note
     */
    private function getAppreciation($note, $typeEvaluation)
    {
        $noteSur20 = $this->convertirEnNoteSur20($note, $typeEvaluation);
        
        if ($noteSur20 >= 16) {
            return 'Excellent';
        } elseif ($noteSur20 >= 14) {
            return 'Très bien';
        } elseif ($noteSur20 >= 12) {
            return 'Bien';
        } elseif ($noteSur20 >= 10) {
            return 'Assez bien';
        } elseif ($noteSur20 >= 8) {
            return 'Passable';
        } elseif ($noteSur20 >= 5) {
            return 'Insuffisant';
        } else {
            return 'Très insuffisant';
        }
    }
    
    /**
     * Calcule la moyenne d'un étudiant dans une matière
     */
    public function moyenneEtudiantMatiere($etudiantId, $matiereId)
    {
        $notes = Note::where('etudiant_id', $etudiantId)
            ->where('matiere_id', $matiereId)
            ->get();
            
        if ($notes->isEmpty()) {
            return [
                'moyenne' => null,
                'notes' => [],
            ];
        }
        
        $totalPondere = 0;
        $totalCoefficients = 0;
        $notesDetaillees = [];
        
        foreach ($notes as $note) {
            $noteSur20 = $note->note; // La note est déjà sur 20 dans la nouvelle structure
            $coefficient = $note->coefficient ?? 1;
            
            $totalPondere += $noteSur20 * $coefficient;
            $totalCoefficients += $coefficient;
            
            $notesDetaillees[] = [
                'type_evaluation' => $note->type ?? 'Contrôle',
                'note' => $note->note,
                'note_sur_20' => $noteSur20,
                'coefficient' => $coefficient,
                'date' => $note->date_evaluation->format('d/m/Y'),
            ];
        }
        
        $moyenne = $totalCoefficients > 0 ? $totalPondere / $totalCoefficients : 0;
        
        return [
            'moyenne' => round($moyenne, 2),
            'notes' => $notesDetaillees,
        ];
    }
    
    /**
     * Calcule le bulletin de notes d'un étudiant
     */
    public function bulletinEtudiant($etudiantId, $classeId = null)
    {
        $etudiant = Etudiant::with(['classe.matieres'])->findOrFail($etudiantId);
        
        // Si une classe est spécifiée, on l'utilise, sinon on prend la classe actuelle de l'étudiant
        $classe = $classeId 
            ? Classe::with('matieres')->findOrFail($classeId)
            : $etudiant->classe;
            
        if (!$classe) {
            return [
                'etudiant' => [
                    'id' => $etudiant->id,
                    'nom' => $etudiant->Nom . ' ' . $etudiant->Prenom,
                ],
                'classe' => null,
                'matieres' => [],
                'moyenne_generale' => null,
            ];
        }
        
        $matieresAvecNotes = [];
        $totalPondere = 0;
        $totalCoefficients = 0;
        
        foreach ($classe->matieres as $matiere) {
            $moyenneMatiere = $this->moyenneEtudiantMatiere($etudiant->id, $matiere->id);
            
            if ($moyenneMatiere['moyenne'] !== null) {
                $matieresAvecNotes[] = [
                    'id' => $matiere->id,
                    'libelle' => $matiere->Libelle,
                    'moyenne' => $moyenneMatiere['moyenne'],
                    'appreciation' => $this->getAppreciation($moyenneMatiere['moyenne'], null),
                    'coefficient' => $matiere->Coefficient ?? 1,
                    'notes' => $moyenneMatiere['notes'],
                ];
                
                $totalPondere += $moyenneMatiere['moyenne'] * ($matiere->Coefficient ?? 1);
                $totalCoefficients += $matiere->Coefficient ?? 1;
            }
        }
        
        $moyenneGenerale = $totalCoefficients > 0 ? $totalPondere / $totalCoefficients : 0;
        
        return [
            'etudiant' => [
                'id' => $etudiant->id,
                'nom' => $etudiant->Nom . ' ' . $etudiant->Prenom,
            ],
            'classe' => [
                'id' => $classe->id,
                'libelle' => $classe->Libelle,
            ],
            'matieres' => $matieresAvecNotes,
            'moyenne_generale' => round($moyenneGenerale, 2),
            'appreciation_generale' => $this->getAppreciation($moyenneGenerale, null),
        ];
    }
    
    /**
     * Exporte le bulletin de notes d'un étudiant au format PDF
     */
    public function exportBulletinPdf($etudiantId, $classeId = null)
    {
        $bulletin = $this->bulletinEtudiant($etudiantId, $classeId);
        
        // Ici, vous pourriez utiliser une bibliothèque comme barryvdh/laravel-dompdf
        // pour générer un PDF à partir d'une vue
        // Exemple :
        // $pdf = PDF::loadView('pdf.bulletin', $bulletin);
        // return $pdf->download('bulletin_' . $bulletin['etudiant']['nom'] . '.pdf');
        
        // Pour l'instant, on retourne simplement les données en JSON
        return response()->json($bulletin);
    }
}
