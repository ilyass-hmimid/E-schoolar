<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Enseignant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Enums\RoleType;

class EnseignantController extends Controller
{
    /**
     * Afficher la liste des enseignants
     */
    public function index()
    {
        $enseignants = User::role(RoleType::PROFESSEUR->value)
            ->with('enseignant')
            ->latest()
            ->paginate(10);
            
        return view('admin.enseignants.index', compact('enseignants'));
    }

    /**
     * Afficher le formulaire de création d'un enseignant
     */
    public function create()
    {
        return view('admin.enseignants.create');
    }

    /**
     * Enregistrer un nouvel enseignant
     */
    public function store(Request $request)
    {
        $validated = $request->validate(config('validation_rules.enseignant'));

        // Créer l'utilisateur
        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'password' => Hash::make('password'), // Mot de passe par défaut
            'telephone' => $validated['telephone'] ?? null,
            'adresse' => $validated['adresse'] ?? null,
            'date_naissance' => $validated['date_naissance'] ?? null,
        ]);

        // Assigner le rôle d'enseignant
        $user->assignRole(RoleType::PROFESSEUR->value);

        // Créer le profil enseignant
        $user->enseignant()->create([
            'specialite' => $validated['specialite'],
            'diplome' => $validated['diplome'],
        ]);

        return redirect()->route('admin.enseignants.index')
            ->with('success', 'Enseignant créé avec succès.');
    }

    /**
     * Afficher les détails d'un enseignant
     */
    public function show($id)
    {
        $enseignant = Enseignant::with([
            'user', 
            'cours.matiere', 
            'cours.classe.niveau', 
            'notes.eleve', 
            'notes.matiere'
        ])->findOrFail($id);
        
        return view('admin.enseignants.show', compact('enseignant'));
    }

    /**
     * Afficher le formulaire d'édition d'un enseignant
     */
    public function edit($id)
    {
        $enseignant = Enseignant::with('user')->findOrFail($id);
        return view('admin.enseignants.edit', compact('enseignant'));
    }

    /**
     * Mettre à jour un enseignant
     */
    public function update(Request $request, $id)
    {
        $enseignant = Enseignant::findOrFail($id);
        
        // Récupérer les règles de validation et ajuster la règle d'unicité de l'email
        $validationRules = config('validation_rules.enseignant');
        $validationRules['email'] = 'required|email|unique:users,email,' . $enseignant->user_id;
        
        $validated = $request->validate($validationRules);

        // Préparer les données de l'utilisateur
        $userData = [
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
            'adresse' => $validated['adresse'] ?? null,
            'date_naissance' => $validated['date_naissance'] ?? null,
        ];

        // Mettre à jour le mot de passe si fourni
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        // Mettre à jour l'utilisateur
        $enseignant->user->update($userData);

        // Mettre à jour le profil enseignant
        $enseignant->update([
            'specialite' => $validated['specialite'],
            'diplome' => $validated['diplome'],
        ]);

        return redirect()->route('admin.enseignants.show', $enseignant->id)
            ->with('success', 'Enseignant mis à jour avec succès.');
    }

    /**
     * Supprimer un enseignant
     */
    public function destroy($id)
    {
        $enseignant = Enseignant::findOrFail($id);
        $user = $enseignant->user;
        $enseignant->delete();
        $user->delete();

        return redirect()->route('admin.enseignants.index')
            ->with('success', 'Enseignant supprimé avec succès.');
    }
}
