<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Traits\HasRoles;

class EleveController extends Controller
{
    use HasRoles;
    /**
     * Affiche la liste des élèves
     */
    public function index()
    {
        $eleves = User::role('eleve')
            ->with(['niveau:id,nom', 'filiere:id,nom'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($eleve) {
                return [
                    'id' => $eleve->id,
                    'name' => $eleve->name,
                    'email' => $eleve->email,
                    'phone' => $eleve->phone,
                    'address' => $eleve->address,
                    'niveau' => $eleve->niveau ? $eleve->niveau->nom : null,
                    'filiere' => $eleve->filiere ? $eleve->filiere->nom : null,
                    'somme_a_payer' => $eleve->somme_a_payer,
                    'date_debut' => $eleve->date_debut ? $eleve->date_debut->format('d/m/Y') : null,
                    'created_at' => $eleve->created_at->format('d/m/Y'),
                    'is_active' => $eleve->is_active,
                ];
            });

        return Inertia::render('Admin/Eleves/Index', [
            'eleves' => $eleves,
            'niveaux' => Niveau::select('id', 'nom')->orderBy('nom')->get(),
            'filieres' => Filiere::select('id', 'nom')->orderBy('nom')->get(),
        ]);
    }

    /**
     * Affiche le formulaire de création d'un élève
     */
    public function create()
    {
        return Inertia::render('Admin/Eleves/Create', [
            'niveaux' => Niveau::select('id', 'nom')->orderBy('nom')->get(),
            'filieres' => Filiere::select('id', 'nom')->orderBy('nom')->get(),
            'matieres' => Matiere::select('id', 'nom')->orderBy('nom')->get(),
        ]);
    }

    /**
     * Enregistre un nouvel élève
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'required|exists:filieres,id',
            'matieres' => 'required|array|min:1',
            'matieres.*' => 'exists:matieres,id',
            'somme_a_payer' => 'nullable|numeric|min:0',
        ]);

        // Démarrer une transaction pour s'assurer que tout se passe bien
        return \DB::transaction(function () use ($validated) {
            // Créer l'utilisateur
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'niveau_id' => $validated['niveau_id'],
                'filiere_id' => $validated['filiere_id'],
                'somme_a_payer' => $validated['somme_a_payer'] ?? 0,
                'date_debut' => now(),
                'role' => RoleType::ELEVE->value,
                'is_active' => true,
            ]);
            
            // Assigner le rôle à l'utilisateur
            $user->assignRole(RoleType::ELEVE->name);

            // Créer les inscriptions pour chaque matière
            foreach ($validated['matieres'] as $matiereId) {
                Inscription::create([
                    'etudiant_id' => $user->id,
                    'matiere_id' => $matiereId,
                    'niveau_id' => $validated['niveau_id'],
                    'filiere_id' => $validated['filiere_id'],
                    'date_inscription' => now(),
                    'annee_scolaire' => now()->format('Y') . '/' . (now()->year + 1),
                    'statut' => 'actif',
                    'montant' => $validated['somme_a_payer'] ?? 0,
                ]);
            }

            return redirect()->route('admin.eleves.index')
                ->with('success', 'Élève créé avec succès');
        });
    }

    /**
     * Affiche les détails d'un élève
     */
    public function show(User $eleve)
    {
        $this->authorize('view', $eleve);
        
        $eleve->load(['niveau', 'filiere', 'notes.matiere', 'absences.matiere']);
        
        return Inertia::render('Eleves/Show', [
            'eleve' => $eleve,
            'notes' => $eleve->notes->map(function($note) {
                return [
                    'id' => $note->id,
                    'matiere' => $note->matiere->nom,
                    'valeur' => $note->note,
                    'type' => $note->type,
                    'date' => $note->created_at->format('d/m/Y'),
                ];
            }),
            'absences' => $eleve->absences->map(function($absence) {
                return [
                    'id' => $absence->id,
                    'matiere' => $absence->matiere->nom,
                    'date' => $absence->date_absence->format('d/m/Y'),
                    'justifiee' => $absence->justifiee,
                    'commentaire' => $absence->commentaire,
                ];
            }),
        ]);
    }

    /**
     * Affiche le formulaire de modification d'un élève
     */
    public function edit(User $eleve)
    {
        $this->authorize('update', $eleve);
        
        return Inertia::render('Eleves/Edit', [
            'eleve' => [
                'id' => $eleve->id,
                'name' => $eleve->name,
                'email' => $eleve->email,
                'phone' => $eleve->phone,
                'address' => $eleve->address,
                'niveau_id' => $eleve->niveau_id,
                'filiere_id' => $eleve->filiere_id,
                'date_debut' => $eleve->date_debut,
                'somme_a_payer' => $eleve->somme_a_payer,
                'is_active' => $eleve->is_active,
            ],
            'niveaux' => Niveau::select('id', 'nom')->orderBy('nom')->get(),
            'filieres' => Filiere::select('id', 'nom')->orderBy('nom')->get(),
        ]);
    }

    /**
     * Met à jour un élève
     */
    public function update(Request $request, User $eleve)
    {
        $this->authorize('update', $eleve);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $eleve->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'required|exists:filieres,id',
            'date_debut' => 'required|date',
            'somme_a_payer' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'niveau_id' => $validated['niveau_id'],
            'filiere_id' => $validated['filiere_id'],
            'date_debut' => $validated['date_debut'],
            'somme_a_payer' => $validated['somme_a_payer'],
            'is_active' => $validated['is_active'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $eleve->update($updateData);

        return redirect()->route('eleves.index')
            ->with('success', 'Élève mis à jour avec succès');
    }

    /**
     * Supprime un élève
     */
    public function destroy(User $eleve)
    {
        $this->authorize('delete', $eleve);
        
        $eleve->delete();
        
        return redirect()->route('eleves.index')
            ->with('success', 'Élève supprimé avec succès');
    }
}
