<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ProfesseurController extends Controller
{
    /**
     * Afficher la liste des professeurs
     */
    /**
     * Afficher la liste des professeurs
     */
    public function index()
    {
        try {
            $professeurs = User::role('professeur')
                ->with('matieres')
                ->latest()
                ->paginate(10);
                
            // Debug: Check if view exists
            if (!view()->exists('admin.professeurs.index')) {
                \Log::error('View not found: admin.professeurs.index');
                return redirect()->route('admin.dashboard')->with('error', 'La vue des professeurs est introuvable.');
            }
            
            return view('admin.professeurs.index', compact('professeurs'));
            
        } catch (\Exception $e) {
            \Log::error('Error in ProfesseurController@index: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Une erreur est survenue lors du chargement de la liste des professeurs.');
        }
    }

    /**
     * Afficher le formulaire de création d'un professeur
     */
    /**
     * Afficher le formulaire de création d'un professeur
     */
    public function create()
    {
        $matieres = Matiere::orderBy('nom')->get();
        return view('admin.professeurs.create', compact('matieres'));
    }

    /**
     * Enregistrer un nouveau professeur
     */
    /**
     * Enregistrer un nouveau professeur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
            'matieres' => 'required|array',
            'matieres.*' => 'exists:matieres,id',
        ]);

        // Créer l'utilisateur
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'telephone' => $validated['telephone'],
            'adresse' => $validated['adresse'],
            'date_naissance' => $validated['date_naissance'],
            'email_verified_at' => now(),
        ]);

        // Assigner le rôle de professeur
        $user->assignRole('professeur');
        
        // Attacher les matières enseignées
        $user->matieres()->sync($validated['matieres']);

        return redirect()->route('admin.professeurs.index')
            ->with('success', 'Le professeur a été créé avec succès.');
    }

    /**
     * Afficher les détails d'un professeur
     */
    /**
     * Afficher les détails d'un professeur
     */
    public function show(User $professeur)
    {
        $professeur->load(['matieres', 'cours' => function($query) {
            $query->with('classe', 'matiere')
                ->orderBy('jour')
                ->orderBy('heure_debut');
        }]);

        return view('admin.professeurs.show', compact('professeur'));
    }

    /**
     * Afficher le formulaire de modification d'un professeur
     */
    /**
     * Afficher le formulaire de modification d'un professeur
     */
    public function edit(User $professeur)
    {
        $matieres = Matiere::orderBy('nom')->get();
        $professeur->load('matieres');
        
        return view('admin.professeurs.edit', compact('professeur', 'matieres'));
    }

    /**
     * Mettre à jour un professeur
     */
    /**
     * Mettre à jour un professeur
     */
    public function update(Request $request, User $professeur)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($professeur->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
            'matieres' => 'required|array',
            'matieres.*' => 'exists:matieres,id',
        ]);

        // Mise à jour des informations de base
        $professeur->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'adresse' => $validated['adresse'],
            'date_naissance' => $validated['date_naissance'],
        ]);

        // Mise à jour du mot de passe si fourni
        if (!empty($validated['password'])) {
            $professeur->update([
                'password' => Hash::make($validated['password']),
            ]);
        }
        
        // Mise à jour des matières enseignées
        $professeur->matieres()->sync($validated['matieres']);

        return redirect()->route('admin.professeurs.show', $professeur)
            ->with('success', 'Les informations du professeur ont été mises à jour avec succès.');
    }

    /**
     * Supprimer un professeur
     */
    /**
     * Supprimer un professeur
     */
    public function destroy(User $professeur)
    {
        // Vérifier s'il y a des cours associés
        if ($professeur->cours()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce professeur car il a des cours associés.');
        }

        $professeur->delete();

        return redirect()->route('admin.professeurs.index')
            ->with('success', 'Le professeur a été supprimé avec succès.');
    }
}
