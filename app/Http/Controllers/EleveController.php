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
class EleveController extends BaseAdminController
{
    /**
     * Affiche la liste des élèves
     */
    public function index()
    {
        // Initialiser la requête pour les élèves
        $query = User::where('role', 'eleve')
            ->with(['niveau:id,nom']);
            
        // Gestion du tri
        $sort = request('sort', 'name');
        $direction = request('direction', 'asc');
        
        // Vérifier si le tri est valide pour éviter les injections SQL
        $validSorts = ['name', 'email', 'cne', 'status', 'niveau_id'];
        $sort = in_array($sort, $validSorts) ? $sort : 'name';
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';
        
        $query->orderBy($sort, $direction);
            
        // Filtre par recherche
        $search = request('search');
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cne', 'like', "%{$search}%");
            });
        }
        
        // Filtre par statut
        if ($status = request('status')) {
            $query->where('status', $status);
        }
        
        // Pagination avec 20 éléments par page
        $eleves = $query->paginate(20);
        // Ajouter les paramètres de requête à la pagination
        $eleves->appends(request()->except('page'));
        
        // Statistiques
        $stats = [
            'total_eleves' => User::where('role', 'eleve')->count(),
            'eleves_actifs' => User::where('role', 'eleve')->where('status', 'actif')->count(),
            'nouveaux_eleves' => User::where('role', 'eleve')
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'taux_absences' => 0, // À implémenter avec la logique des absences
        ];
        
        return view('admin.eleves.index', [
            'eleves' => $eleves,
            'search' => $search,
            'stats' => $stats,
            'sort' => $sort,
            'direction' => $direction
        ]);
    }

    /**
     * Get the common data for all views
     *
     * @return array
     */
    protected function getCommonData()
    {
        $data = parent::getCommonData();
        
        return array_merge($data, [
            'niveaux' => Niveau::select('id', 'nom')->orderBy('nom')->get(),
            'filieres' => Filiere::select('id', 'nom')->orderBy('nom')->get(),
            'matieres' => Matiere::orderBy('nom')->get(),
        ]);
    }

    /**
     * Affiche le formulaire de création d'un élève
     */
    public function create()
    {
        $data = $this->getCommonData();
        $data['classes'] = \App\Models\Classe::select('id', 'nom')->orderBy('nom')->get();
        return view('admin.eleves.create', $data);
    }

    /**
     * Enregistre un nouvel élève (version simplifiée)
     */
    public function store(Request $request)
    {
        try {
            // Validation minimale
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'telephone' => 'required|string|max:20',
                'classe_id' => 'required|exists:classes,id',
            ]);

            // Récupérer la classe pour le niveau et la filière
            $classe = \App\Models\Classe::findOrFail($validated['classe_id']);
            
            // Créer l'utilisateur avec des valeurs par défaut
            $user = User::create([
                'name' => $validated['prenom'] . ' ' . $validated['nom'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'telephone' => $validated['telephone'],
                'password' => Hash::make('password123'),
                'classe_id' => (int)$validated['classe_id'],
                'niveau_id' => $classe->niveau_id,
                'filiere_id' => $classe->filiere_id,
                'role' => 'eleve',
                'is_active' => true,
                'date_inscription' => now()->format('Y-m-d'),
                'date_debut' => now()->format('Y-m-d'),
                'somme_a_payer' => 0.00,
            ]);

            return redirect()->route('admin.eleves.index')
                ->with('success', 'Élève créé avec succès avec le mot de passe: password123');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un élève
     */
    public function show($id)
    {
        $eleve = User::with(['niveau', 'filiere', 'notes.matiere', 'absences.matiere'])->findOrFail($id);
        
        return view('admin.eleves.show', [
            'eleve' => $eleve,
            'notes' => $eleve->notes->map(function($note) {
                return [
                    'id' => $note->id,
                    'matiere' => $note->matiere ? $note->matiere->nom : 'Matière inconnue',
                    'valeur' => $note->note,
                    'type' => $note->type,
                    'date' => $note->created_at->format('d/m/Y'),
                ];
            }),
            'absences' => $eleve->absences->map(function($absence) {
                return [
                    'id' => $absence->id,
                    'matiere' => $absence->matiere ? $absence->matiere->nom : 'Matière inconnue',
                    'date' => $absence->date_absence->format('d/m/Y'),
                    'justifiee' => $absence->justifiee,
                    'commentaire' => $absence->commentaire,
                ];
            })
        ]);
    }

    /**
     * Affiche le formulaire de modification d'un élève
     */
    public function edit($id)
    {
        $eleve = User::findOrFail($id);
        $data = $this->getCommonData();
        $data['eleve'] = $eleve;
        $data['eleve_matieres'] = $eleve->matieresEleve->pluck('id')->toArray();
        
        return view('admin.eleves.edit', $data);
    }

    /**
     * Met à jour un élève
     */
    public function update(Request $request, $id)
    {
        $eleve = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
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
