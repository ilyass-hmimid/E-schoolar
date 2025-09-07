<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Paiement;
use App\Models\Absence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EleveController extends Controller
{
    /**
     * Affiche la liste des élèves
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $eleves = User::where('role', 'eleve')
            ->withCount(['absences', 'paiements'])
            ->latest()
            ->paginate(15);
            
        return view('admin.eleves.index', compact('eleves'));
    }

    /**
     * Affiche le formulaire de création d'un élève
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $matieres = Matiere::orderBy('nom')->get();
        return view('admin.eleves.create', compact('matieres'));
    }

    /**
     * Enregistre un nouvel élève
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:500',
            'date_naissance' => 'required|date|before:today',
            'cne' => 'required|string|max:50|unique:users,cne',
            'nom_pere' => 'required|string|max:255',
            'telephone_pere' => 'required|string|max:20',
            'nom_mere' => 'required|string|max:255',
            'telephone_mere' => 'required|string|max:20',
            'matieres' => 'required|array',
            'matieres.*' => 'exists:matieres,id',
        ]);

        // Créer l'utilisateur
        $eleve = User::create([
            'name' => $validated['nom'] . ' ' . $validated['prenom'],
            'email' => $validated['email'],
            'password' => Hash::make('password'), // Mot de passe par défaut
            'role' => 'eleve',
            'status' => 'actif',
            'telephone' => $validated['telephone'],
            'adresse' => $validated['adresse'],
            'date_naissance' => $validated['date_naissance'],
            'cne' => $validated['cne'],
            'nom_pere' => $validated['nom_pere'],
            'telephone_pere' => $validated['telephone_pere'],
            'nom_mere' => $validated['nom_mere'],
            'telephone_mere' => $validated['telephone_mere'],
        ]);

        // Inscrire l'élève aux matières sélectionnées
        $eleve->matieres()->sync($validated['matieres']);

        return redirect()->route('admin.eleves.show', $eleve)
            ->with('success', 'Élève créé avec succès. Le mot de passe par défaut est "password".');
    }

    /**
     * Affiche les détails d'un élève
     *
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\View\View
     */
    public function show(User $eleve)
    {
        $this->authorize('view', $eleve);
        
        $eleve->load(['matieres', 'absences' => function($query) {
            return $query->latest()->take(10);
        }, 'paiements' => function($query) {
            return $query->latest()->take(10);
        }]);
        
        return view('admin.eleves.show', compact('eleve'));
    }

    /**
     * Affiche le formulaire de modification d'un élève
     *
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\View\View
     */
    public function edit(User $eleve)
    {
        $this->authorize('update', $eleve);
        
        $matieres = Matiere::orderBy('nom')->get();
        $eleve->load('matieres');
        
        return view('admin.eleves.edit', compact('eleve', 'matieres'));
    }

    /**
     * Met à jour les informations d'un élève
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $eleve)
    {
        $this->authorize('update', $eleve);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($eleve->id),
            ],
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:500',
            'date_naissance' => 'required|date|before:today',
            'cne' => [
                'required', 'string', 'max:50',
                Rule::unique('users', 'cne')->ignore($eleve->id),
            ],
            'status' => ['required', 'in:actif,inactif,suspendu'],
            'nom_pere' => 'required|string|max:255',
            'telephone_pere' => 'required|string|max:20',
            'nom_mere' => 'required|string|max:255',
            'telephone_mere' => 'required|string|max:20',
            'matieres' => 'required|array',
            'matieres.*' => 'exists:matieres,id',
        ]);

        // Mettre à jour l'utilisateur
        $eleve->update([
            'name' => $validated['nom'] . ' ' . $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'adresse' => $validated['adresse'],
            'date_naissance' => $validated['date_naissance'],
            'cne' => $validated['cne'],
            'status' => $validated['status'],
            'nom_pere' => $validated['nom_pere'],
            'telephone_pere' => $validated['telephone_pere'],
            'nom_mere' => $validated['nom_mere'],
            'telephone_mere' => $validated['telephone_mere'],
        ]);

        // Mettre à jour les matières de l'élève
        $eleve->matieres()->sync($validated['matieres']);

        return redirect()->route('admin.eleves.show', $eleve)
            ->with('success', 'Informations de l\'élève mises à jour avec succès.');
    }

    /**
     * Affiche le formulaire de changement de mot de passe
     *
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\View\View
     */
    public function editPassword(User $eleve)
    {
        $this->authorize('update', $eleve);
        
        return view('admin.eleves.password', compact('eleve'));
    }

    /**
     * Met à jour le mot de passe de l'élève
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request, User $eleve)
    {
        $this->authorize('update', $eleve);
        
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $eleve->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.eleves.show', $eleve)
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }

    /**
     * Désactive un élève
     *
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivate(User $eleve)
    {
        $this->authorize('delete', $eleve);
        
        $eleve->update(['status' => 'inactif']);
        
        return redirect()->route('admin.eleves.index')
            ->with('success', 'L\'élève a été désactivé avec succès.');
    }

    /**
     * Réactive un élève
     *
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(User $eleve)
    {
        $this->authorize('update', $eleve);
        
        $eleve->update(['status' => 'actif']);
        
        return redirect()->route('admin.eleves.show', $eleve)
            ->with('success', 'L\'élève a été réactivé avec succès.');
    }
}
