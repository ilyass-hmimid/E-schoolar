<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AssistantController extends Controller
{
    /**
     * Afficher la liste des assistants
     */
    public function index()
    {
        $assistants = User::role('assistant')
            ->with('centre')
            ->latest()
            ->paginate(10);
            
        return view('admin.assistants.index', compact('assistants'));
    }

    /**
     * Afficher le formulaire de création d'un assistant
     */
    public function create()
    {
        $centres = \App\Models\Centre::all();
        return view('admin.assistants.create', compact('centres'));
    }

    /**
     * Enregistrer un nouvel assistant
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'centre_id' => 'required|exists:centres,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Créer l'utilisateur
        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'adresse' => $validated['adresse'],
            'date_naissance' => $validated['date_naissance'],
            'centre_id' => $validated['centre_id'],
            'password' => bcrypt($validated['password']),
        ]);

        // Assigner le rôle d'assistant
        $user->assignRole('assistant');

        return redirect()->route('admin.assistants.index')
            ->with('success', 'Assistant créé avec succès');
    }

    /**
     * Afficher les détails d'un assistant
     */
    public function show($id)
    {
        $assistant = User::with('centre')->findOrFail($id);
        return view('admin.assistants.show', compact('assistant'));
    }

    /**
     * Afficher le formulaire de modification d'un assistant
     */
    public function edit($id)
    {
        $assistant = User::findOrFail($id);
        $centres = \App\Models\Centre::all();
        return view('admin.assistants.edit', compact('assistant', 'centres'));
    }

    /**
     * Mettre à jour un assistant
     */
    public function update(Request $request, $id)
    {
        $assistant = User::findOrFail($id);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $assistant->id,
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'centre_id' => 'required|exists:centres,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'adresse' => $validated['adresse'],
            'date_naissance' => $validated['date_naissance'],
            'centre_id' => $validated['centre_id'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        $assistant->update($data);

        return redirect()->route('admin.assistants.index')
            ->with('success', 'Assistant mis à jour avec succès');
    }

    /**
     * Supprimer un assistant
     */
    public function destroy($id)
    {
        $assistant = User::findOrFail($id);
        $assistant->delete();

        return redirect()->route('admin.assistants.index')
            ->with('success', 'Assistant supprimé avec succès');
    }
}
