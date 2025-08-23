<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AssistantController extends Controller
{
    /**
     * Affiche la liste des assistants
     */
    public function index()
    {
        $assistants = User::role(RoleType::ASSISTANT->value)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($assistant) {
                return [
                    'id' => $assistant->id,
                    'name' => $assistant->name,
                    'email' => $assistant->email,
                    'phone' => $assistant->phone,
                    'address' => $assistant->address,
                    'date_embauche' => $assistant->date_embauche ? $assistant->date_embauche->format('d/m/Y') : null,
                    'salaire' => $assistant->salaire,
                    'is_active' => $assistant->is_active,
                    'created_at' => $assistant->created_at->format('d/m/Y'),
                ];
            });

        return Inertia::render('Assistants/Index', [
            'assistants' => $assistants
        ]);
    }

    /**
     * Affiche le formulaire de création d'un assistant
     */
    public function create()
    {
        return Inertia::render('Assistants/Create');
    }

    /**
     * Enregistre un nouvel assistant
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'date_embauche' => 'required|date',
            'salaire' => 'required|numeric|min:0',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'date_embauche' => $validated['date_embauche'],
            'salaire' => $validated['salaire'],
            'role' => RoleType::ASSISTANT->value,
            'is_active' => true,
        ]);

        return redirect()->route('assistants.index')
            ->with('success', 'Assistant créé avec succès');
    }

    /**
     * Affiche les détails d'un assistant
     */
    public function show(User $assistant)
    {
        $this->authorize('view', $assistant);
        
        return Inertia::render('Assistants/Show', [
            'assistant' => [
                'id' => $assistant->id,
                'name' => $assistant->name,
                'email' => $assistant->email,
                'phone' => $assistant->phone,
                'address' => $assistant->address,
                'date_embauche' => $assistant->date_embauche ? $assistant->date_embauche->format('Y-m-d') : null,
                'salaire' => $assistant->salaire,
                'is_active' => $assistant->is_active,
                'created_at' => $assistant->created_at->format('d/m/Y'),
            ]
        ]);
    }

    /**
     * Affiche le formulaire de modification d'un assistant
     */
    public function edit(User $assistant)
    {
        $this->authorize('update', $assistant);
        
        return Inertia::render('Assistants/Edit', [
            'assistant' => [
                'id' => $assistant->id,
                'name' => $assistant->name,
                'email' => $assistant->email,
                'phone' => $assistant->phone,
                'address' => $assistant->address,
                'date_embauche' => $assistant->date_embauche ? $assistant->date_embauche->format('Y-m-d') : null,
                'salaire' => $assistant->salaire,
                'is_active' => $assistant->is_active,
            ]
        ]);
    }

    /**
     * Met à jour un assistant
     */
    public function update(Request $request, User $assistant)
    {
        $this->authorize('update', $assistant);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $assistant->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'date_embauche' => 'required|date',
            'salaire' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'date_embauche' => $validated['date_embauche'],
            'salaire' => $validated['salaire'],
            'is_active' => $validated['is_active'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $assistant->update($updateData);

        return redirect()->route('assistants.index')
            ->with('success', 'Assistant mis à jour avec succès');
    }

    /**
     * Supprime un assistant
     */
    public function destroy(User $assistant)
    {
        $this->authorize('delete', $assistant);
        
        $assistant->delete();
        
        return redirect()->route('assistants.index')
            ->with('success', 'Assistant supprimé avec succès');
    }
}
