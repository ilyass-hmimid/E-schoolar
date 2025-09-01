<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Cours;
use App\Models\Classe;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class AbsenceController extends Controller
{
    /**
     * Affiche la liste des absences
     */
    public function index(Request $request)
    {
        $query = Absence::query()
            ->with(['eleve.classe', 'cours.matiere', 'cours.professeur'])
            ->orderBy('date_absence', 'desc')
            ->orderBy('created_at', 'desc');
        
        // Filtres
        if ($request->filled('eleve_id')) {
            $query->where('eleve_id', $request->eleve_id);
        }
        
        if ($request->filled('classe_id')) {
            $query->whereHas('eleve', function($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date_absence', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date_absence', '<=', $request->date_fin);
        }
        
        if ($request->filled('justifiee') && in_array($request->justifiee, ['0', '1'])) {
            $query->where('justifiee', (bool)$request->justifiee);
        }
        
        $absences = $query->paginate(15);
        $classes = Classe::active()->orderBy('nom')->get();
        $eleves = $request->filled('classe_id') 
            ? Eleve::where('classe_id', $request->classe_id)->orderBy('nom')->orderBy('prenom')->get()
            : collect();
            
        return view('admin.pedagogie.absences.index', compact(
            'absences', 
            'classes',
            'eleves',
            'request'
        ));
    }

    /**
     * Affiche le formulaire de création d'une absence
     */
    public function create(Request $request)
    {
        $classes = Classe::active()->with('niveau')->orderBy('nom')->get();
        $eleves = collect();
        $cours = collect();
        
        // Si une classe est sélectionnée, charger les élèves et les cours
        if ($request->filled('classe_id')) {
            $eleves = Eleve::where('classe_id', $request->classe_id)
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get();
                
            // Charger les cours pour cette classe
            $cours = Cours::where('classe_id', $request->classe_id)
                ->with(['matiere', 'professeur.user'])
                ->whereDate('date_cours', '>=', now()->subDays(30))
                ->orderBy('date_cours', 'desc')
                ->get();
        }
        
        return view('admin.pedagogie.absences.create', compact(
            'classes',
            'eleves',
            'cours',
            'request'
        ));
    }

    /**
     * Enregistre une nouvelle absence
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'cours_id' => 'required|exists:cours,id',
            'date_absence' => 'required|date|before_or_equal:today',
            'type_absence' => ['required', Rule::in(['absence', 'retard', 'sortie_anticipée'])],
            'duree_absence' => 'nullable|integer|min:1|required_if:type_absence,retard,sortie_anticipée',
            'justifiee' => 'boolean',
            'motif' => 'nullable|string|max:500',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::beginTransaction();
            
            // Vérifier si une absence similaire existe déjà
            $existingAbsence = Absence::where('eleve_id', $validated['eleve_id'])
                ->where('cours_id', $validated['cours_id'])
                ->whereDate('date_absence', $validated['date_absence'])
                ->first();
                
            if ($existingAbsence) {
                return back()
                    ->withInput()
                    ->with('error', __('packs.absences.errors.already_exists'));
            }
            
            // Gestion du fichier justificatif
            if ($request->hasFile('justificatif')) {
                $path = $request->file('justificatif')->store('justificatifs', 'public');
                $validated['chemin_justificatif'] = $path;
                $validated['justifiee'] = true; // Un justificatif implique que l'absence est justifiée
            }
            
            // Ajouter l'utilisateur qui a créé l'absence
            $validated['utilisateur_creation_id'] = auth()->id();
            
            // Créer l'absence
            $absence = Absence::create($validated);
            
            // Mettre à jour le statut de présence de l'élève pour ce cours
            $this->mettreAJourStatutPresence($absence);
            
            // Envoyer une notification aux parents si configuré
            if (config('app.notify_parents_on_absence')) {
                $this->notifierParents($absence);
            }
            
            DB::commit();
            
            return redirect()
                ->route('admin.absences.show', $absence)
                ->with('success', __('packs.absences.success.created'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création de l\'absence : ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', __('packs.absences.errors.create'));
        }
    }

    /**
     * Affiche les détails d'une absence
     */
    public function show(Absence $absence)
    {
        $absence->load([
            'eleve.classe.niveau', 
            'cours.matiere', 
            'cours.professeur.user',
            'utilisateurCreation',
            'utilisateurModification'
        ]);
        
        // Calculer le nombre total d'absences de l'élève
        $statsAbsences = [
            'total' => Absence::where('eleve_id', $absence->eleve_id)
                ->where('justifiee', false)
                ->count(),
            'justifiees' => Absence::where('eleve_id', $absence->eleve_id)
                ->where('justifiee', true)
                ->count(),
        ];
        
        return view('admin.pedagogie.absences.show', compact(
            'absence',
            'statsAbsences'
        ));
    }
    
    /**
     * Affiche le formulaire d'édition d'une absence
     */
    public function edit(Absence $absence)
    {
        $absence->load(['eleve.classe', 'cours.matiere', 'cours.professeur']);
        $classes = Classe::active()->with('niveau')->orderBy('nom')->get();
        
        // Charger les cours pour la classe de l'élève
        $cours = Cours::where('classe_id', $absence->eleve->classe_id)
            ->with(['matiere', 'professeur.user'])
            ->whereDate('date_cours', '>=', now()->subDays(60))
            ->orderBy('date_cours', 'desc')
            ->get();
        
        return view('admin.pedagogie.absences.edit', compact(
            'absence',
            'classes',
            'cours'
        ));
    }

    /**
     * Met à jour une absence
     */
    public function update(Request $request, Absence $absence)
    {
        $validated = $request->validate([
            'cours_id' => 'required|exists:cours,id',
            'date_absence' => 'required|date|before_or_equal:today',
            'type_absence' => ['required', Rule::in(['absence', 'retard', 'sortie_anticipée'])],
            'duree_absence' => 'nullable|integer|min:1|required_if:type_absence,retard,sortie_anticipée',
            'justifiee' => 'boolean',
            'motif' => 'nullable|string|max:500',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'supprimer_justificatif' => 'boolean',
        ]);

        try {
            DB::beginTransaction();
            
            // Gestion du fichier justificatif
            if ($request->hasFile('justificatif')) {
                // Supprimer l'ancien fichier s'il existe
                if ($absence->chemin_justificatif) {
                    Storage::disk('public')->delete($absence->chemin_justificatif);
                }
                
                $path = $request->file('justificatif')->store('justificatifs', 'public');
                $validated['chemin_justificatif'] = $path;
                $validated['justifiee'] = true;
            } elseif ($request->boolean('supprimer_justificatif') && $absence->chemin_justificatif) {
                Storage::disk('public')->delete($absence->chemin_justificatif);
                $validated['chemin_justificatif'] = null;
                $validated['justifiee'] = false;
            }
            
            // Ajouter l'utilisateur qui a modifié l'absence
            $validated['utilisateur_modification_id'] = auth()->id();
            
            // Sauvegarder les modifications
            $ancienCoursId = $absence->cours_id;
            $absence->update($validated);
            
            // Mettre à jour le statut de présence si le cours a changé
            if ($ancienCoursId != $validated['cours_id']) {
                $this->mettreAJourStatutPresence($absence, true, $ancienCoursId);
            }
            
            $this->mettreAJourStatutPresence($absence);
            
            DB::commit();
            
            return redirect()
                ->route('admin.absences.show', $absence)
                ->with('success', __('packs.absences.success.updated'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la mise à jour de l\'absence : ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', __('packs.absences.errors.update'));
        }
    }

    /**
     * Supprime une absence
     */
    public function destroy(Absence $absence)
    {
        try {
            // Supprimer le fichier justificatif s'il existe
            if ($absence->chemin_justificatif) {
                Storage::disk('public')->delete($absence->chemin_justificatif);
            }
            
            $coursId = $absence->cours_id;
            $eleveId = $absence->eleve_id;
            
            $absence->delete();
            
            // Mettre à jour le statut de présence de l'élève pour ce cours
            $this->mettreAJourStatutPresence($absence, true);
            
            return redirect()
                ->route('admin.absences.index')
                ->with('success', __('packs.absences.success.deleted'));
                
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression de l\'absence : ' . $e->getMessage());
            
            return back()
                ->with('error', __('packs.absences.errors.delete'));
        }
    }
    
    /**
     * Télécharge le justificatif d'absence
     */
    public function telechargerJustificatif(Absence $absence)
    {
        if (!$absence->chemin_justificatif || !Storage::disk('public')->exists($absence->chemin_justificatif)) {
            abort(404, __('packs.absences.errors.justificatif_not_found'));
        }
        
        return Storage::disk('public')->download(
            $absence->chemin_justificatif,
            'justificatif-absence-' . $absence->id . '.' . pathinfo($absence->chemin_justificatif, PATHINFO_EXTENSION)
        );
    }
    
    /**
     * Exporte les absences au format Excel
     */
    public function export(Request $request)
    {
        $query = Absence::query()
            ->with(['eleve.classe', 'cours.matiere', 'cours.professeur.user']);
        
        // Appliquer les mêmes filtres que pour l'index
        if ($request->filled('eleve_id')) {
            $query->where('eleve_id', $request->eleve_id);
        }
        
        if ($request->filled('classe_id')) {
            $query->whereHas('eleve', function($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date_absence', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date_absence', '<=', $request->date_fin);
        }
        
        if ($request->filled('justifiee') && in_array($request->justifiee, ['0', '1'])) {
            $query->where('justifiee', (bool)$request->justifiee);
        }
        
        $absences = $query->orderBy('date_absence', 'desc')->get();
        
        // Générer un nom de fichier avec la date
        $fileName = 'export-absences-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
        
        // Utiliser la classe d'exportation Excel (à créer)
        return (new \App\Exports\AbsencesExport($absences))->download($fileName);
    }
    
    /**
     * Génère un PDF pour une absence
     */
    public function genererPdf(Absence $absence)
    {
        $absence->load([
            'eleve.classe.niveau', 
            'cours.matiere', 
            'cours.professeur.user',
            'utilisateurCreation'
        ]);
        
        $pdf = PDF::loadView('admin.pedagogie.absences.pdf', compact('absence'));
        
        return $pdf->download('absence-' . $absence->id . '.pdf');
    }
    
    /**
     * Met à jour le statut de présence d'un élève pour un cours
     * 
     * @param Absence $absence L'absence à traiter
     * @param bool $suppression Si true, c'est pour la suppression d'une absence
     * @param int|null $ancienCoursId Ancien ID de cours (en cas de modification)
     */
    private function mettreAJourStatutPresence(Absence $absence, bool $suppression = false, ?int $ancienCoursId = null)
    {
        // Si c'est une suppression ou une modification de cours, réinitialiser le statut de l'ancien cours
        if (($suppression || $ancienCoursId) && $ancienCoursId) {
            $this->mettreAJourPresenceCours($absence->eleve_id, $ancienCoursId);
        }
        
        // Si ce n'est pas une suppression, mettre à jour le statut pour le nouveau cours
        if (!$suppression) {
            $this->mettreAJourPresenceCours($absence->eleve_id, $absence->cours_id);
        }
    }
    
    /**
     * Met à jour le statut de présence d'un élève pour un cours spécifique
     */
    private function mettreAJourPresenceCours(int $eleveId, int $coursId)
    {
        // Vérifier s'il y a des absences non justifiées pour cet élève et ce cours
        $absencesNonJustifiees = Absence::where('eleve_id', $eleveId)
            ->where('cours_id', $coursId)
            ->where('justifiee', false)
            ->exists();
        
        // Mettre à jour la table de présence (à adapter selon votre structure)
        DB::table('presences') // Remplacez par votre modèle de présence si nécessaire
            ->updateOrInsert(
                ['eleve_id' => $eleveId, 'cours_id' => $coursId],
                [
                    'present' => !$absencesNonJustifiees,
                    'updated_at' => now(),
                ]
            );
    }
    
    /**
     * Envoie une notification aux parents concernant une absence
     */
    private function notifierParents(Absence $absence)
    {
        try {
            $absence->load(['eleve.responsables.user']);
            
            // Envoyer une notification à chaque responsable
            foreach ($absence->eleve->responsables as $responsable) {
                if ($responsable->user->email) {
                    // Utiliser la notification Laravel
                    $responsable->user->notify(new \App\Notifications\AbsenceNotification($absence));
                }
                
                // Envoyer une notification par SMS si le numéro est disponible
                if (config('services.sms.enabled') && $responsable->telephone) {
                    $this->envoyerSMSAbsence($responsable, $absence);
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'envoi des notifications d\'absence : ' . $e->getMessage());
        }
    }
    
    /**
     * Envoie un SMS de notification d'absence
     */
    private function envoyerSMSAbsence($responsable, Absence $absence)
    {
        try {
            $message = sprintf(
                "[%s] Absence de %s - %s le %s à %s. Motif: %s",
                config('app.name'),
                $absence->eleve->prenom . ' ' . $absence->eleve->nom,
                $absence->type_absence,
                $absence->date_absence->format('d/m/Y'),
                $absence->cours->heure_debut,
                $absence->motif ?? 'Non précisé'
            );
            
            // Utiliser un service d'envoi de SMS (ex: Twilio, etc.)
            // À adapter selon votre fournisseur de services SMS
            if (class_exists('Twilio\Rest\Client')) {
                $twilio = new \Twilio\Rest\Client(
                    config('services.twilio.sid'),
                    config('services.twilio.token')
                );
                
                $twilio->messages->create(
                    $responsable->telephone,
                    [
                        'from' => config('services.twilio.from'),
                        'body' => $message
                    ]
                );
            }
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'envoi du SMS d\'absence : ' . $e->getMessage());
        }
    }
}
