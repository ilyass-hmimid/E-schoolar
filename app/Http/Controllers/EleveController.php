<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class EleveController extends Controller
{
    /**
     * Affiche la liste des élèves
     */
    public function index()
    {
        $eleves = User::role(RoleType::ELEVE->value)
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

        return Inertia::render('Eleves/Index', [
            'eleves' => $eleves,
            'niveaux' => Niveau::select('id', 'nom')->get(),
            'filieres' => Filiere::select('id', 'nom')->get(),
        ]);
    }

    /**
     * Affiche le formulaire de création d'un élève
     */
    public function create()
    {
        return Inertia::render('Eleves/Create', [
            'niveaux' => Niveau::select('id', 'nom')->get(),
            'filieres' => Filiere::select('id', 'nom')->get(),
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
            'address' => 'nullable|string|max:255',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'required|exists:filieres,id',
            'date_debut' => 'required|date',
            'somme_a_payer' => 'required|numeric|min:0',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'niveau_id' => $validated['niveau_id'],
            'filiere_id' => $validated['filiere_id'],
            'date_debut' => $validated['date_debut'],
            'somme_a_payer' => $validated['somme_a_payer'],
            'role' => RoleType::ELEVE->value,
            'is_active' => true,
        ]);

        return redirect()->route('eleves.index')
            ->with('success', 'Élève créé avec succès');
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
                    'valeur' => $note->valeur,
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
            'niveaux' => Niveau::select('id', 'nom')->get(),
            'filieres' => Filiere::select('id', 'nom')->get(),
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
