<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Cours;
use App\Models\Salle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmploiDuTempsController extends Controller
{
    /**
     * Afficher la liste des emplois du temps
     */
    public function index()
    {
        $classes = Classe::with(['niveau', 'cours.matiere', 'cours.professeur.user'])
            ->active()
            ->orderBy('niveau_id')
            ->orderBy('nom')
            ->get();

        return view('admin.pedagogie.emplois-du-temps.index', compact('classes'));
    }

    /**
     * Afficher le formulaire de création d'un cours dans l'emploi du temps
     */
    public function create()
    {
        $classes = Classe::active()->with('niveau')->orderBy('nom')->get();
        $salles = Salle::disponible()->get();
        
        // Récupérer les créneaux horaires
        $creneaux = $this->getCreneauxHoraires();
        
        // Jours de la semaine
        $jours = [
            'lundi' => 'Lundi',
            'mardi' => 'Mardi',
            'mercredi' => 'Mercredi',
            'jeudi' => 'Jeudi',
            'vendredi' => 'Vendredi',
            'samedi' => 'Samedi',
        ];

        return view('admin.pedagogie.emplois-du-temps.create', compact('classes', 'salles', 'creneaux', 'jours'));
    }

    /**
     * Enregistrer un nouveau cours dans l'emploi du temps
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'professeur_id' => 'required|exists:professeurs,id',
            'salle_id' => 'required|exists:salles,id',
            'jour_semaine' => ['required', Rule::in(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'])],
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'frequence' => 'required|in:unique,hebdomadaire,mensuel',
            'couleur' => 'nullable|string|max:7',
            'remarques' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Vérifier les conflits
            $conflits = $this->verifierConflits($validated);
            
            if ($conflits->isNotEmpty()) {
                return back()
                    ->withInput()
                    ->with('warning', 'Conflit détecté avec un ou plusieurs cours existants')
                    ->with('conflits', $conflits);
            }

            // Créer le ou les cours selon la fréquence
            $cours = $this->creerCoursSelonFrequence($validated);

            DB::commit();

            return redirect()
                ->route('admin.emplois-du-temps.index')
                ->with('success', __('packs.emplois_du_temps.success.created'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création du cours : ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', __('packs.emplois_du_temps.errors.create'));
        }
    }

    /**
     * Afficher un emploi du temps spécifique
     */
    public function show($id)
    {
        // Si c'est une classe
        if (is_numeric($id)) {
            $classe = Classe::with(['cours' => function($query) {
                $query->with(['matiere', 'professeur.user', 'salle']);
            }])->findOrFail($id);

            $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
            $creneaux = $this->getCreneauxHoraires();
            
            return view('admin.pedagogie.emplois-du-temps.show-classe', compact('classe', 'jours', 'creneaux'));
        }
        
        // Si c'est un professeur
        if (str_starts_with($id, 'prof_')) {
            $professeurId = str_replace('prof_', '', $id);
            $professeur = \App\Models\Professeur::with(['cours' => function($query) {
                $query->with(['matiere', 'classe.niveau', 'salle']);
            }])->findOrFail($professeurId);

            $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
            $creneaux = $this->getCreneauxHoraires();
            
            return view('admin.pedagogie.emplois-du-temps.show-professeur', compact('professeur', 'jours', 'creneaux'));
        }
        
        // Si c'est une salle
        if (str_starts_with($id, 'salle_')) {
            $salleId = str_replace('salle_', '', $id);
            $salle = Salle::with(['cours' => function($query) {
                $query->with(['matiere', 'classe.niveau', 'professeur.user']);
            }])->findOrFail($salleId);

            $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
            $creneaux = $this->getCreneauxHoraires();
            
            return view('admin.pedagogie.emplois-du-temps.show-salle', compact('salle', 'jours', 'creneaux'));
        }
        
        abort(404);
    }

    /**
     * Afficher le formulaire d'édition d'un cours
     */
    public function edit(Cours $cours)
    {
        $classes = Classe::active()->with('niveau')->orderBy('nom')->get();
        $salles = Salle::disponible()->get();
        $creneaux = $this->getCreneauxHoraires();
        $jours = [
            'lundi' => 'Lundi',
            'mardi' => 'Mardi',
            'mercredi' => 'Mercredi',
            'jeudi' => 'Jeudi',
            'vendredi' => 'Vendredi',
            'samedi' => 'Samedi',
        ];

        return view('admin.pedagogie.emplois-du-temps.edit', compact('cours', 'classes', 'salles', 'creneaux', 'jours'));
    }

    /**
     * Mettre à jour un cours dans l'emploi du temps
     */
    public function update(Request $request, Cours $cours)
    {
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'professeur_id' => 'required|exists:professeurs,id',
            'salle_id' => 'required|exists:salles,id',
            'jour_semaine' => ['required', Rule::in(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'])],
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'date' => 'required|date',
            'couleur' => 'nullable|string|max:7',
            'remarques' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Vérifier les conflits (sauf avec le cours actuel)
            $conflits = $this->verifierConflits($validated, $cours->id);
            
            if ($conflits->isNotEmpty()) {
                return back()
                    ->withInput()
                    ->with('warning', 'Conflit détecté avec un ou plusieurs cours existants')
                    ->with('conflits', $conflits);
            }

            $cours->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.emplois-du-temps.show', $cours->classe_id)
                ->with('success', __('packs.emplois_du_temps.success.updated'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la mise à jour du cours : ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', __('packs.emplois_du_temps.errors.update'));
        }
    }

    /**
     * Supprimer un cours de l'emploi du temps
     */
    public function destroy(Cours $cours)
    {
        try {
            $classeId = $cours->classe_id;
            $cours->delete();
            
            return redirect()
                ->route('admin.emplois-du-temps.show', $classeId)
                ->with('success', __('packs.emplois_du_temps.success.deleted'));
                
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du cours : ' . $e->getMessage());
            
            return back()
                ->with('error', __('packs.emplois_du_temps.errors.delete'));
        }
    }

    /**
     * Vérifier les conflits d'horaires
     */
    private function verifierConflits($data, $excludeCoursId = null)
    {
        $query = Cours::where('jour_semaine', $data['jour_semaine'])
            ->where(function($q) use ($data) {
                $q->whereBetween('heure_debut', [$data['heure_debut'], $data['heure_fin']])
                  ->orWhereBetween('heure_fin', [$data['heure_debut'], $data['heure_fin']])
                  ->orWhere(function($q) use ($data) {
                      $q->where('heure_debut', '<=', $data['heure_debut'])
                        ->where('heure_fin', '>=', $data['heure_fin']);
                  });
            });

        // Vérifier les conflits de salle
        $salleQuery = clone $query;
        $conflitsSalle = $salleQuery->where('salle_id', $data['salle_id'])
            ->when($excludeCoursId, function($q) use ($excludeCoursId) {
                $q->where('id', '!=', $excludeCoursId);
            })
            ->with('classe', 'matiere')
            ->get();

        // Vérifier les conflits de professeur
        $profQuery = clone $query;
        $conflitsProf = $profQuery->where('professeur_id', $data['professeur_id'])
            ->when($excludeCoursId, function($q) use ($excludeCoursId) {
                $q->where('id', '!=', $excludeCoursId);
            })
            ->with('classe', 'matiere')
            ->get();

        // Vérifier les conflits de classe
        $classeQuery = clone $query;
        $conflitsClasse = $classeQuery->where('classe_id', $data['classe_id'])
            ->when($excludeCoursId, function($q) use ($excludeCoursId) {
                $q->where('id', '!=', $excludeCoursId);
            })
            ->with('matiere', 'professeur.user')
            ->get();

        return collect()
            ->merge($conflitsSalle->map(fn($c) => [...$c->toArray(), 'type' => 'salle']))
            ->merge($conflitsProf->map(fn($c) => [...$c->toArray(), 'type' => 'professeur']))
            ->merge($conflitsClasse->map(fn($c) => [...$c->toArray(), 'type' => 'classe']))
            ->unique('id');
    }

    /**
     * Créer des cours selon la fréquence spécifiée
     */
    private function creerCoursSelonFrequence($data)
    {
        $dateDebut = Carbon::parse($data['date_debut']);
        $dateFin = Carbon::parse($data['date_fin']);
        $cours = [];

        switch ($data['frequence']) {
            case 'unique':
                $cours[] = Cours::create($data);
                break;

            case 'hebdomadaire':
                $currentDate = $dateDebut->copy();
                while ($currentDate->lte($dateFin)) {
                    if ($currentDate->dayOfWeek === $this->getJourSemaineIndex($data['jour_semaine'])) {
                        $cours[] = Cours::create([
                            'classe_id' => $data['classe_id'],
                            'matiere_id' => $data['matiere_id'],
                            'professeur_id' => $data['professeur_id'],
                            'salle_id' => $data['salle_id'],
                            'jour_semaine' => $data['jour_semaine'],
                            'heure_debut' => $data['heure_debut'],
                            'heure_fin' => $data['heure_fin'],
                            'date' => $currentDate->format('Y-m-d'),
                            'couleur' => $data['couleur'] ?? null,
                            'remarques' => $data['remarques'] ?? null,
                        ]);
                    }
                    $currentDate->addDay();
                }
                break;

            case 'mensuel':
                $currentDate = $dateDebut->copy();
                while ($currentDate->lte($dateFin)) {
                    if ($currentDate->day === $dateDebut->day) {
                        $cours[] = Cours::create([
                            'classe_id' => $data['classe_id'],
                            'matiere_id' => $data['matiere_id'],
                            'professeur_id' => $data['professeur_id'],
                            'salle_id' => $data['salle_id'],
                            'jour_semaine' => $data['jour_semaine'],
                            'heure_debut' => $data['heure_debut'],
                            'heure_fin' => $data['heure_fin'],
                            'date' => $currentDate->format('Y-m-d'),
                            'couleur' => $data['couleur'] ?? null,
                            'remarques' => $data['remarques'] ?? null,
                        ]);
                    }
                    $currentDate->addDay();
                    
                    // Si on a dépassé la fin du mois, passer au mois suivant
                    if ($currentDate->day === 1) {
                        $currentDate->addMonth();
                        $currentDate->day($dateDebut->day);
                    }
                }
                break;
        }

        return $cours;
    }

    /**
     * Obtenir l'index du jour de la semaine (0 pour dimanche, 1 pour lundi, etc.)
     */
    private function getJourSemaineIndex($jour)
    {
        return [
            'dimanche' => 0,
            'lundi' => 1,
            'mardi' => 2,
            'mercredi' => 3,
            'jeudi' => 4,
            'vendredi' => 5,
            'samedi' => 6,
        ][strtolower($jour)];
    }

    /**
     * Obtenir les créneaux horaires de la journée
     */
    private function getCreneauxHoraires()
    {
        $creneaux = [];
        $debut = Carbon::parse('08:00');
        $fin = Carbon::parse('20:00');
        $dureeCours = 60; // en minutes

        while ($debut->addMinutes($dureeCours)->lte($fin)) {
            $heureFin = $debut->copy()->addMinutes($dureeCours);
            $creneaux[] = [
                'debut' => $debut->format('H:i'),
                'fin' => $heureFin->format('H:i'),
                'texte' => $debut->format('H:i') . ' - ' . $heureFin->format('H:i'),
            ];
        }

        return $creneaux;
    }
}
