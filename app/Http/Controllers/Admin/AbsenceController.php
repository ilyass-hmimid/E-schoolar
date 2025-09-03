<?php

namespace App\Http\Controllers\Admin;

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Cours;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use App\Notifications\AbsenceNotification;
use App\Notifications\AbsenceEnregistree;
use App\Notifications\AbsenceEleve;
use App\Notifications\AbsenceEleveProfesseur;
use App\Notifications\JustificationAbsenceTraitee;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsencesExport;
use App\Imports\AbsencesImport;

class AbsenceController extends Controller
{
    /**
     * Affiche la liste des absences
     */
    public function index(Request $request)
    {
        $query = Absence::query()
            ->with([
                'etudiant.classe', 
                'matiere', 
                'professeur',
                'assistant',
                'validateur',
                'justificateur'
            ])
            ->orderBy('date_absence', 'desc')
            ->orderBy('created_at', 'desc');
        
        $query = $this->applyFilters($query, $request);
        
        $absences = $query->paginate(15);
        $classes = Classe::active()->orderBy('nom')->get();
        $matieres = Matiere::orderBy('nom')->get();
        $professeurs = User::role('professeur')->orderBy('name')->get();
        
        $etudiants = $request->filled('classe_id') 
            ? User::role('etudiant')
                ->where('classe_id', $request->classe_id)
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get()
            : collect();
            
        return view('admin.absences.index', [
            'absences' => $absences,
            'classes' => $classes,
            'etudiants' => $etudiants,
            'matieres' => $matieres,
            'professeurs' => $professeurs,
            'request' => $request,
            'statutsJustification' => Absence::STATUT_JUSTIFICATION,
            'typesAbsence' => [
                'absence' => 'Absence',
                'retard' => 'Retard',
                'sortie_anticipée' => 'Sortie anticipée'
            ]
        ]);
    }

    /**
     * Applique les filtres sur la requête des absences
     */
    private function applyFilters($query, Request $request)
    {
        // Filtre par étudiant
        if ($request->filled('etudiant_id')) {
            $query->where('etudiant_id', $request->etudiant_id);
        }
        
        // Filtre par classe
        if ($request->filled('classe_id')) {
            $etudiantsIds = User::role('etudiant')
                ->where('classe_id', $request->classe_id)
                ->pluck('id');
                
            $query->whereIn('etudiant_id', $etudiantsIds);
        }
        
        // Filtre par matière
        if ($request->filled('matiere_id')) {
            $query->where('matiere_id', $request->matiere_id);
        }
        
        // Filtre par professeur
        if ($request->filled('professeur_id')) {
            $query->where('professeur_id', $request->professeur_id);
        }
        
        // Filtre par type d'absence
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filtre par statut de justification
        if ($request->filled('statut_justification') && in_array($request->statut_justification, ['en_attente', 'validee', 'rejetee'])) {
            $query->where('statut_justification', $request->statut_justification);
        }
        
        // Filtre par date de début
        if ($request->filled('date_debut')) {
            $query->whereDate('date_absence', '>=', $request->date_debut);
        }
        
        // Filtre par date de fin
        if ($request->filled('date_fin')) {
            $query->whereDate('date_absence', '<=', $request->date_fin);
        }
        
        // Filtre par plage horaire
        if ($request->filled('heure_debut')) {
            $query->whereTime('heure_fin', '>=', $request->heure_debut);
        }
        
        if ($request->filled('heure_fin')) {
            $query->whereTime('heure_debut', '<=', $request->heure_fin);
        }
        
        // Tri
        $sortField = $request->get('sort_field', 'date_absence');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        return $query->orderBy($sortField, $sortDirection);
    }

    /**
     * Affiche le formulaire de création d'une absence
     */
    public function create(Request $request)
    {
        $classes = Classe::active()->with('niveau')->orderBy('nom')->get();
        $matieres = Matiere::orderBy('nom')->get();
        $professeurs = User::role('professeur')->orderBy('name')->get();
        $etudiants = collect();
        
        // Si une classe est sélectionnée, charger les étudiants
        if ($request->filled('classe_id')) {
            $etudiants = User::role('etudiant')
                ->where('classe_id', $request->classe_id)
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get();
        }
        
        return view('admin.absences.create', [
            'classes' => $classes,
            'etudiants' => $etudiants,
            'matieres' => $matieres,
            'professeurs' => $professeurs,
            'request' => $request,
            'typesAbsence' => [
                'absence' => 'Absence',
                'retard' => 'Retard',
                'sortie_anticipée' => 'Sortie anticipée'
            ]
        ]);
    }

    /**
     * Enregistre une nouvelle absence
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:users,id',
            'matiere_id' => 'required|exists:matieres,id',
            'professeur_id' => 'required|exists:users,id',
            'date_absence' => 'required|date|before_or_equal:today',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type' => ['required', Rule::in(['absence', 'retard', 'sortie_anticipée'])],
            'duree_retard' => 'nullable|integer|min:1|required_if:type,retard,sortie_anticipée',
            'motif' => 'required|string|max:500',
            'piece_jointe' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'justifiee' => 'boolean',
            'statut_justification' => 'nullable|in:en_attente,validee,rejetee'
        ]);

        try {
            DB::beginTransaction();
            
            // Vérifier si une absence similaire existe déjà
            $existingAbsence = Absence::where('etudiant_id', $validated['etudiant_id'])
                ->where('matiere_id', $validated['matiere_id'])
                ->whereDate('date_absence', $validated['date_absence'])
                ->whereBetween('heure_debut', [$validated['heure_debut'], $validated['heure_fin']])
                ->orWhereBetween('heure_fin', [$validated['heure_debut'], $validated['heure_fin']])
                ->first();
                
            if ($existingAbsence) {
                return back()
                    ->withInput()
                    ->with('error', 'Une absence ou un chevauchement existe déjà pour cet étudiant sur cette plage horaire.');
            }
            
            // Gestion du fichier justificatif
            if ($request->hasFile('piece_jointe')) {
                $file = $request->file('piece_jointe');
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('pieces_jointes/absences', $fileName, 'public');
                $validated['piece_jointe'] = $path;
                
                // Si une pièce jointe est fournie, marquer comme justifiée en attente de validation
                $validated['justifiee'] = true;
                $validated['statut_justification'] = 'en_attente';
                $validated['date_justification'] = now();
                $validated['justified_by'] = $validated['etudiant_id'];
            } else {
                $validated['justifiee'] = $request->has('justifiee') ? (bool)$request->justifiee : false;
                $validated['statut_justification'] = $validated['justifiee'] ? 'validee' : 'en_attente';
            }
            
            // Ajouter l'assistant qui a créé l'absence (utilisateur connecté)
            $validated['assistant_id'] = Auth::id();
            
            // Créer l'absence
            $absence = Absence::create($validated);
            
            // Les notifications aux parents ont été supprimées
            
            DB::commit();
            
            return redirect()
                ->route('admin.absences.show', $absence)
                ->with('success', 'L\'absence a été enregistrée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création de l\'absence : ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement de l\'absence.');
        }
    }

    /**
     * Affiche les détails d'une absence
     */
    public function show(Absence $absence)
    {
        $absence->load([
            'etudiant.classe', 
            'matiere', 
            'professeur',
            'assistant',
            'validateur',
            'justificateur'
        ]);
        
        // Calculer les statistiques d'absences de l'étudiant
        $statsAbsences = [
            'total' => Absence::where('etudiant_id', $absence->etudiant_id)
                ->where('type', 'absence')
                ->count(),
            'justifiees' => Absence::where('etudiant_id', $absence->etudiant_id)
                ->where('type', 'absence')
                ->where('statut_justification', 'validee')
                ->count(),
            'en_attente' => Absence::where('etudiant_id', $absence->etudiant_id)
                ->where('type', 'absence')
                ->where('statut_justification', 'en_attente')
                ->count(),
            'rejetees' => Absence::where('etudiant_id', $absence->etudiant_id)
                ->where('type', 'absence')
                ->where('statut_justification', 'rejetee')
                ->count(),
            'retards' => Absence::where('etudiant_id', $absence->etudiant_id)
                ->where('type', 'retard')
                ->count(),
            'sorties' => Absence::where('etudiant_id', $absence->etudiant_id)
                ->where('type', 'sortie_anticipée')
                ->count()
        ];
        
        // Récupérer l'historique des justifications si disponible
        $historiqueJustifications = $absence->justifications()
            ->with('utilisateur')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.absences.show', [
            'absence' => $absence,
            'statsAbsences' => $statsAbsences,
            'historiqueJustifications' => $historiqueJustifications,
            'statutsJustification' => Absence::STATUT_JUSTIFICATION
        ]);
    }
    
    /**
     * Affiche le formulaire d'édition d'une absence
     */
    public function edit(Absence $absence)
    {
        $absence->load(['etudiant.classe', 'matiere', 'professeur']);
        
        $classes = Classe::active()->with('niveau')->orderBy('nom')->get();
        $matieres = Matiere::orderBy('nom')->get();
        $professeurs = User::role('professeur')->orderBy('name')->get();
        
        // Charger les étudiants de la même classe que l'absence
        $etudiants = $absence->etudiant->classe_id 
            ? User::role('etudiant')
                ->where('classe_id', $absence->etudiant->classe_id)
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get()
            : collect();
        
        return view('admin.absences.edit', [
            'absence' => $absence,
            'classes' => $classes,
            'etudiants' => $etudiants,
            'matieres' => $matieres,
            'professeurs' => $professeurs,
            'typesAbsence' => [
                'absence' => 'Absence',
                'retard' => 'Retard',
                'sortie_anticipée' => 'Sortie anticipée'
            ],
            'statutsJustification' => Absence::STATUT_JUSTIFICATION
        ]);
    }

    /**
     * Met à jour une absence
     */
    public function update(Request $request, Absence $absence)
    {
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:users,id',
            'matiere_id' => 'required|exists:matieres,id',
            'professeur_id' => 'required|exists:users,id',
            'date_absence' => 'required|date|before_or_equal:today',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type' => ['required', Rule::in(['absence', 'retard', 'sortie_anticipée'])],
            'duree_retard' => 'nullable|integer|min:1|required_if:type,retard,sortie_anticipée',
            'motif' => 'required|string|max:500',
            'piece_jointe' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'supprimer_piece_jointe' => 'boolean',
            'justifiee' => 'boolean',
            'statut_justification' => 'required|in:en_attente,validee,rejetee',
            'commentaire_validation' => 'nullable|string|max:1000|required_if:statut_justification,validee,rejetee'
        ]);

        try {
            DB::beginTransaction();
            
            // Gestion du fichier justificatif
            if ($request->hasFile('piece_jointe')) {
                // Supprimer l'ancien fichier s'il existe
                if ($absence->piece_jointe) {
                    Storage::disk('public')->delete($absence->piece_jointe);
                }
                
                $file = $request->file('piece_jointe');
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('pieces_jointes/absences', $fileName, 'public');
                $validated['piece_jointe'] = $path;
                
                // Mettre à jour les informations de justification
                $validated['justifiee'] = true;
                $validated['statut_justification'] = 'en_attente';
                $validated['date_justification'] = now();
                $validated['justified_by'] = $validated['etudiant_id'];
                
                // Réinitialiser les informations de validation
                $validated['valide_par'] = null;
                $validated['date_validation'] = null;
                $validated['commentaire_validation'] = null;
            } elseif ($request->boolean('supprimer_piece_jointe') && $absence->piece_jointe) {
                Storage::disk('public')->delete($absence->piece_jointe);
                $validated['piece_jointe'] = null;
                $validated['justifiee'] = false;
                $validated['statut_justification'] = 'en_attente';
                $validated['date_justification'] = null;
                $validated['justified_by'] = null;
            }
            
            // Gestion de la validation de la justification
            if (in_array($validated['statut_justification'], ['validee', 'rejetee']) && 
                $absence->statut_justification !== $validated['statut_justification']) {
                $validated['valide_par'] = Auth::id();
                $validated['date_validation'] = now();
                
                // Si la justification est rejetée, conserver la pièce jointe
                if ($validated['statut_justification'] === 'rejetee') {
                    $validated['justifiee'] = false;
                } else {
                    $validated['justifiee'] = true;
                }
                
                // Enregistrer l'historique de la justification
                $absence->justifications()->create([
                    'statut' => $validated['statut_justification'],
                    'commentaire' => $validated['commentaire_validation'],
                    'utilisateur_id' => Auth::id(),
                    'date_validation' => now()
                ]);
                
                // Envoyer une notification à l'étudiant
                if (config('app.notify_on_justification')) {
                    $this->notifierValidationJustification($absence);
                }
            }
            
            // Mettre à jour l'absence
            $absence->update($validated);
            
            DB::commit();
            
            return redirect()
                ->route('admin.absences.show', $absence)
                ->with('success', 'L\'absence a été mise à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la mise à jour de l\'absence : ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'absence.');
        }
    }

    /**
     * Supprime une absence
     */
    public function destroy(Absence $absence)
    {
        try {
            DB::beginTransaction();
            
            // Supprimer le fichier justificatif s'il existe
            if ($absence->piece_jointe) {
                Storage::disk('public')->delete($absence->piece_jointe);
            }
            
            // Supprimer l'historique des justifications
            $absence->justifications()->delete();
            
            // Supprimer l'absence
            $absence->delete();
            
            DB::commit();
            
            return redirect()
                ->route('admin.absences.index')
                ->with('success', 'L\'absence a été supprimée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la suppression de l\'absence : ' . $e->getMessage());
            
            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'absence.');
        }
    }
    
    /**
     * Télécharge la pièce jointe d'une absence
     */
    public function telechargerPieceJointe(Absence $absence)
    {
        if (!$absence->piece_jointe || !Storage::disk('public')->exists($absence->piece_jointe)) {
            abort(404, 'La pièce jointe demandée n\'existe pas.');
        }
        
        return Storage::disk('public')->download(
            $absence->piece_jointe, 
            'piece-jointe-absence-' . $absence->id . '.' . pathinfo($absence->piece_jointe, PATHINFO_EXTENSION)
        );
    }
    
    /**
     * Affiche la pièce jointe d'une absence
     */
    public function afficherPieceJointe(Absence $absence)
    {
        if (!$absence->piece_jointe || !Storage::disk('public')->exists($absence->piece_jointe)) {
            abort(404, 'La pièce jointe demandée n\'existe pas.');
        }
        
        $file = Storage::disk('public')->get($absence->piece_jointe);
        $type = Storage::disk('public')->mimeType($absence->piece_jointe);
        
        return response($file, 200)->header('Content-Type', $type);
    }
    
    /**
     * Exporte les absences au format Excel
     */
    public function exporterExcel(Request $request)
    {
        $query = Absence::with([
            'etudiant.classe',
            'matiere',
            'professeur',
            'assistant',
            'validateur',
            'justificateur'
        ]);
        
        $query = $this->applyFilters($query, $request);
        
        return Excel::download(
            new AbsencesExport($query->get()),
            'export-absences-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
    
    /**
     * Exporte les absences au format PDF
     */
    public function exporterPdf(Request $request)
    {
        $query = Absence::with([
            'etudiant.classe',
            'matiere',
            'professeur',
            'assistant'
        ]);
        
        $query = $this->applyFilters($query, $request);
        $absences = $query->get();
        
        $pdf = PDF::loadView('admin.absences.export-pdf', [
            'absences' => $absences,
            'filtres' => $request->all(),
            'dateExport' => now()->format('d/m/Y H:i')
        ]);
        
        return $pdf->download('export-absences-' . now()->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Importe des absences depuis un fichier Excel
     */
    public function importerExcel(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:xlsx,xls,csv|max:5120'
        ]);
        
        try {
            Excel::import(new AbsencesImport, $request->file('fichier'));
            
            return redirect()
                ->route('admin.absences.index')
                ->with('success', 'L\'import des absences a été effectué avec succès.');
                
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'import des absences : ' . $e->getMessage());
            
            return back()
                ->with('error', 'Une erreur est survenue lors de l\'import du fichier : ' . $e->getMessage());
        }
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
        
        $pdf = PDF::loadView('admin.absences.pdf', compact('absence'));
        
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
     * Envoie un SMS de notification d'absence
     */
    private function envoyerSMSAbsence($responsable, Absence $absence)
    {
        try {
            $message = sprintf(
                "[%s] Absence de %s - %s le %s de %s à %s. Matière: %s. Motif: %s",
                config('app.name'),
                $absence->etudiant->prenom . ' ' . $absence->etudiant->nom,
                $absence->type,
                $absence->date_absence->format('d/m/Y'),
                $absence->heure_debut,
                $absence->heure_fin,
                $absence->matiere->nom,
                $absence->motif ?? 'Non précisé'
            );
            
            // Utiliser un service d'envoi de SMS (ex: Twilio, etc.)
            if (class_exists('Twilio\Rest\Client')) {
                $twilio = new \Twilio\Rest\Client(
                    config('services.twilio.sid'),
                    config('services.twilio.token')
                );
                
                $twilio->messages->create(
                    $this->formatPhoneNumber($responsable->telephone),
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
    
    /**
     * Formate un numéro de téléphone pour l'envoi de SMS
     */
    private function formatPhoneNumber($phone)
    {
        // Supprimer tous les caractères non numériques
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Si le numéro commence par 0, le remplacer par +212 (Maroc)
        if (strpos($phone, '0') === 0) {
            $phone = '212' . substr($phone, 1);
        }
        
        // Ajouter le préfixe + si nécessaire
        if (strpos($phone, '+') !== 0) {
            $phone = '+' . $phone;
        }
        
        return $phone;
    }
}
