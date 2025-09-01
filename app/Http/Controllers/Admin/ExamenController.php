<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Examen;
use App\Models\Matiere;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExamenController extends Controller
{
    /**
     * Afficher la liste des examens
     */
    public function index()
    {
        $examens = Examen::with(['classe.niveau', 'matiere'])
            ->orderBy('date_examen', 'desc')
            ->paginate(15);

        return view('admin.pedagogie.examens.index', compact('examens'));
    }

    /**
     * Afficher le formulaire de création d'un examen
     */
    public function create()
    {
        $classes = Classe::active()->with('niveau')->orderBy('nom')->get();
        $matieres = Matiere::orderBy('nom')->get();
        
        return view('admin.pedagogie.examens.create', compact('classes', 'matieres'));
    }

    /**
     * Enregistrer un nouvel examen
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_examen' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'duree' => 'required|integer|min:1|max:480', // en minutes
            'coefficient' => 'required|numeric|min:0.1|max:10',
            'type_examen' => ['required', Rule::in(['devoir', 'composition', 'partiel', 'final'])],
            'bareme' => 'required|integer|min:5|max:100',
            'consignes' => 'nullable|string',
            'est_termine' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Vérifier les conflits d'horaires
            $conflits = $this->verifierConflits($validated);
            
            if ($conflits->isNotEmpty()) {
                return back()
                    ->withInput()
                    ->with('warning', 'Conflit détecté avec un ou plusieurs examens existants')
                    ->with('conflits', $conflits);
            }

            $examen = Examen::create($validated);

            // Générer automatiquement les notes pour chaque élève de la classe
            $this->genererNotesInitiales($examen);

            DB::commit();

            return redirect()
                ->route('admin.examens.show', $examen)
                ->with('success', __('packs.examens.success.created'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création de l\'examen : ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', __('packs.examens.errors.create'));
        }
    }

    /**
     * Afficher les détails d'un examen
     */
    public function show(Examen $examen)
    {
        $examen->load([
            'classe.niveau', 
            'matiere', 
            'notes.eleve.user' => function($query) {
                $query->orderBy('nom')->orderBy('prenom');
            }
        ]);

        // Statistiques de l'examen
        $stats = $this->calculerStatistiques($examen);

        return view('admin.pedagogie.examens.show', compact('examen', 'stats'));
    }

    /**
     * Afficher le formulaire d'édition d'un examen
     */
    public function edit(Examen $examen)
    {
        $classes = Classe::active()->with('niveau')->orderBy('nom')->get();
        $matieres = Matiere::orderBy('nom')->get();
        
        return view('admin.pedagogie.examens.edit', compact('examen', 'classes', 'matieres'));
    }

    /**
     * Mettre à jour un examen
     */
    public function update(Request $request, Examen $examen)
    {
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_examen' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'duree' => 'required|integer|min:1|max:480',
            'coefficient' => 'required|numeric|min:0.1|max:10',
            'type_examen' => ['required', Rule::in(['devoir', 'composition', 'partiel', 'final'])],
            'bareme' => 'required|integer|min:5|max:100',
            'consignes' => 'nullable|string',
            'est_termine' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Vérifier les conflits (sauf avec l'examen actuel)
            $conflits = $this->verifierConflits($validated, $examen->id);
            
            if ($conflits->isNotEmpty()) {
                return back()
                    ->withInput()
                    ->with('warning', 'Conflit détecté avec un ou plusieurs examens existants')
                    ->with('conflits', $conflits);
            }

            // Si la classe change, mettre à jour les notes
            if ($examen->classe_id != $validated['classe_id']) {
                $this->mettreAJourNotesPourNouvelleClasse($examen, $validated['classe_id']);
            }

            $examen->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.examens.show', $examen)
                ->with('success', __('packs.examens.success.updated'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la mise à jour de l\'examen : ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', __('packs.examens.errors.update'));
        }
    }

    /**
     * Supprimer un examen
     */
    public function destroy(Examen $examen)
    {
        try {
            // Supprimer les notes associées
            $examen->notes()->delete();
            
            $examen->delete();
            
            return redirect()
                ->route('admin.examens.index')
                ->with('success', __('packs.examens.success.deleted'));
                
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression de l\'examen : ' . $e->getMessage());
            
            return back()
                ->with('error', __('packs.examens.errors.delete'));
        }
    }

    /**
     * Marquer un examen comme terminé
     */
    public function terminer(Examen $examen)
    {
        try {
            $examen->update(['est_termine' => true]);
            
            return back()
                ->with('success', __('packs.examens.success.termine'));
                
        } catch (\Exception $e) {
            \Log::error('Erreur lors du marquage de l\'examen comme terminé : ' . $e->getMessage());
            
            return back()
                ->with('error', __('packs.examens.errors.termine'));
        }
    }

    /**
     * Générer un PDF pour l'examen
     */
    public function genererPdf(Examen $examen)
    {
        $examen->load(['classe.niveau', 'matiere']);
        
        $pdf = \PDF::loadView('admin.pedagogie.examens.pdf', compact('examen'));
        return $pdf->download("examen-{$examen->id}.pdf");
    }

    /**
     * Vérifier les conflits d'horaires pour un examen
     */
    private function verifierConflits($data, $excludeExamenId = null)
    {
        $dateExamen = Carbon::parse($data['date_examen']);
        $heureDebut = Carbon::parse($data['heure_debut']);
        $heureFin = $heureDebut->copy()->addMinutes($data['duree']);

        $query = Examen::whereDate('date_examen', $dateExamen->format('Y-m-d'))
            ->where(function($q) use ($heureDebut, $heureFin) {
                $q->whereBetween(DB::raw('TIME(heure_debut)'), [$heureDebut->format('H:i:s'), $heureFin->format('H:i:s')])
                  ->orWhereBetween(DB::raw('TIME(ADDTIME(heure_debut, SEC_TO_TIME(duree * 60)))'), [$heureDebut->format('H:i:s'), $heureFin->format('H:i:s')])
                  ->orWhere(function($q) use ($heureDebut, $heureFin) {
                      $q->whereTime('heure_debut', '<=', $heureDebut->format('H:i:s'))
                        ->whereRaw('TIME(ADDTIME(heure_debut, SEC_TO_TIME(duree * 60))) >= ?', [$heureFin->format('H:i:s')]);
                  });
            });

        // Filtrer par classe ou par salle si nécessaire
        $query->where(function($q) use ($data) {
            $q->where('classe_id', $data['classe_id'])
              ->orWhereHas('salle', function($q) use ($data) {
                  if (isset($data['salle_id'])) {
                      $q->where('salles.id', $data['salle_id']);
                  }
              });
        });

        if ($excludeExamenId) {
            $query->where('id', '!=', $excludeExamenId);
        }

        return $query->with(['classe', 'matiere'])->get();
    }

    /**
     * Générer les notes initiales pour un examen
     */
    private function genererNotesInitiales(Examen $examen)
    {
        $eleves = $examen->classe->eleves;
        $notes = [];
        
        foreach ($eleves as $eleve) {
            $notes[] = [
                'examen_id' => $examen->id,
                'eleve_id' => $eleve->id,
                'note' => null,
                'commentaire' => null,
                'est_absent' => false,
                'est_retardataire' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if (!empty($notes)) {
            DB::table('notes')->insert($notes);
        }
    }

    /**
     * Mettre à jour les notes pour une nouvelle classe
     */
    private function mettreAJourNotesPourNouvelleClasse(Examen $examen, $nouvelleClasseId)
    {
        // Supprimer les notes des élèves qui ne sont plus dans la classe
        $examen->notes()->whereNotIn('eleve_id', function($query) use ($nouvelleClasseId) {
            $query->select('id')
                  ->from('eleves')
                  ->where('classe_id', $nouvelleClasseId);
        })->delete();
        
        // Ajouter des notes pour les nouveaux élèves
        $nouveauxEleves = \App\Models\Eleve::where('classe_id', $nouvelleClasseId)
            ->whereNotIn('id', function($query) use ($examen) {
                $query->select('eleve_id')
                      ->from('notes')
                      ->where('examen_id', $examen->id);
            })
            ->get();
            
        $notesAAjouter = [];
        
        foreach ($nouveauxEleves as $eleve) {
            $notesAAjouter[] = [
                'examen_id' => $examen->id,
                'eleve_id' => $eleve->id,
                'note' => null,
                'commentaire' => null,
                'est_absent' => false,
                'est_retardataire' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if (!empty($notesAAjouter)) {
            DB::table('notes')->insert($notesAAjouter);
        }
    }

    /**
     * Calculer les statistiques d'un examen
     */
    private function calculerStatistiques(Examen $examen)
    {
        $notes = $examen->notes()->whereNotNull('note')->pluck('note');
        $totalNotes = $notes->count();
        
        if ($totalNotes === 0) {
            return [
                'moyenne' => 0,
                'note_max' => 0,
                'note_min' => 0,
                'ecart_type' => 0,
                'taux_reussite' => 0,
                'repartition_notes' => [],
                'total_notes' => 0,
                'total_absents' => $examen->notes()->where('est_absent', true)->count(),
                'total_retardataires' => $examen->notes()->where('est_retardataire', true)->count(),
            ];
        }
        
        $somme = $notes->sum();
        $moyenne = $somme / $totalNotes;
        $noteMax = $notes->max();
        $noteMin = $notes->min();
        
        // Écart-type
        $sommeCarres = $notes->sum(function($note) use ($moyenne) {
            return pow($note - $moyenne, 2);
        });
        $ecartType = sqrt($sommeCarres / $totalNotes);
        
        // Taux de réussite (supposons que la note de passage est 10)
        $notePassage = 10;
        $nombreReussis = $notes->filter(fn($note) => $note >= $notePassage)->count();
        $tauxReussite = ($nombreReussis / $totalNotes) * 100;
        
        // Répartition des notes (histogramme)
        $bareme = $examen->bareme;
        $pas = max(1, ceil($bareme / 10)); // 10 intervalles max
        $repartition = [];
        
        for ($i = 0; $i <= $bareme; $i += $pas) {
            $borneInf = $i;
            $borneSup = min($i + $pas - 1, $bareme);
            $libelle = $pas > 1 ? "$borneInf - $borneSup" : "$borneInf";
            
            $count = $pas > 1 
                ? $notes->filter(fn($note) => $note >= $borneInf && $note <= $borneSup)->count()
                : $notes->filter(fn($note) => $note == $i)->count();
                
            $repartition[] = [
                'intervalle' => $libelle,
                'nombre' => $count,
                'pourcentage' => ($count / $totalNotes) * 100,
            ];
            
            if ($pas === 1 && $i === $bareme - 1) {
                break; // Pour éviter une boucle infinie si pas = 1
            }
        }
        
        return [
            'moyenne' => round($moyenne, 2),
            'note_max' => $noteMax,
            'note_min' => $noteMin,
            'ecart_type' => round($ecartType, 2),
            'taux_reussite' => round($tauxReussite, 1),
            'repartition_notes' => $repartition,
            'total_notes' => $totalNotes,
            'total_absents' => $examen->notes()->where('est_absent', true)->count(),
            'total_retardataires' => $examen->notes()->where('est_retardataire', true)->count(),
        ];
    }
}
