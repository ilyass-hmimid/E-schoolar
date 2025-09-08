<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Niveau;
use App\Models\Paiement;
use App\Models\Filiere;
use App\Models\Absence;
use App\Models\Inscription;
use App\Models\Classe;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class EleveController extends BaseAdminController
{
    /**
     * Affiche la liste des élèves avec filtres et tri
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Initialiser la requête pour les élèves
        $query = User::where('role', 'eleve')
            ->with(['niveau:id,nom'])
            ->withCount(['absences', 'paiements']);
            
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
        
        // Filtre par niveau
        if ($niveau_id = request('niveau_id')) {
            $query->where('niveau_id', $niveau_id);
        }
        
        // Pagination avec 20 éléments par page
        $eleves = $query->paginate(20);
        $niveaux = Niveau::orderBy('nom')->pluck('nom', 'id');
        
        if (request()->wantsJson()) {
            return response()->json([
                'data' => $eleves->items(),
                'meta' => [
                    'current_page' => $eleves->currentPage(),
                    'last_page' => $eleves->lastPage(),
                    'per_page' => $eleves->perPage(),
                    'total' => $eleves->total(),
                ]
            ]);
        }
        
        return view('admin.eleves.index', compact('eleves', 'niveaux'));
    }

    /**
     * Affiche le formulaire de création d'un élève
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        $this->authorize('create', User::class);
        
        try {
            $niveaux = Niveau::orderBy('nom')->pluck('nom', 'id');
            $filieres = Filiere::orderBy('nom')->pluck('nom', 'id');
            $matieres = Matiere::orderBy('nom')->get();
            
            if ($niveaux->isEmpty() || $filieres->isEmpty()) {
                throw new \Exception('Les données des niveaux et filières ne sont pas disponibles. Veuillez vérifier la base de données.');
            }
            
            if (request()->wantsJson()) {
                return response()->json([
                    'data' => [
                        'niveaux' => $niveaux,
                        'filieres' => $filieres,
                        'matieres' => $matieres,
                        'form_action' => route('admin.eleves.store'),
                        'method' => 'POST'
                    ]
                ]);
            }
            
            return view('admin.eleves.create', compact('niveaux', 'filieres', 'matieres'));
            
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.eleves.index')
                ->with('error', 'Une erreur est survenue lors du chargement du formulaire : ' . $e->getMessage());
        }
    }

    /**
     * Enregistre un nouvel élève
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'required|exists:filieres,id',
            'nom_pere' => 'required|string|max:100',
            'telephone_pere' => 'required|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Démarrer une transaction pour assurer l'intégrité des données
        DB::beginTransaction();
        
        try {
            // Générer un mot de passe aléatoire
            $password = Str::random(10);
            
            // Préparer les données de l'utilisateur
            $userData = [
                'name' => $validated['nom'] . ' ' . $validated['prenom'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'password' => Hash::make($password),
                'role' => 'eleve',
                'date_naissance' => $validated['date_naissance'],
                'lieu_naissance' => $validated['lieu_naissance'],
                'adresse' => $validated['adresse'],
                'telephone' => $validated['telephone'],
                'niveau_id' => $validated['niveau_id'],
                'filiere_id' => $validated['filiere_id'],
                'nom_pere' => $validated['nom_pere'],
                'telephone_pere' => $validated['telephone_pere'],
                'status' => 'actif',
                'email_verified_at' => now(),
                'is_active' => true,
                'remember_token' => Str::random(10),
                'date_inscription' => now(),
                'date_debut' => now(),
            ];
            
            // Créer l'utilisateur
            $user = User::create($userData);
            
            // Gérer la photo de profil si elle est fournie
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('profiles', 'public');
                $user->update(['photo' => $path]);
            }
            
            // Récupérer les informations de la filière et du niveau
            $filiere = Filiere::findOrFail($validated['filiere_id']);
            $niveau = Niveau::findOrFail($validated['niveau_id']);
            
            // Journalisation des valeurs de filière et niveau
            \Log::info('Valeurs de filière et niveau', [
                'filiere_id' => $filiere->id,
                'filiere_nom' => $filiere->nom,
                'niveau_id' => $niveau->id,
                'niveau_nom' => $niveau->nom
            ]);
            
            // Vérifier que la filière et le niveau ont des noms valides
            if (empty($filiere->nom) || empty($niveau->nom)) {
                $errorMsg = 'Le nom de la filière ou du niveau est vide';
                \Log::error($errorMsg, [
                    'filiere' => $filiere->toArray(),
                    'niveau' => $niveau->toArray()
                ]);
                throw new \Exception($errorMsg);
            }
            
            // Créer ou récupérer la classe avec des valeurs par défaut
            $nomClasse = trim($niveau->nom . ' ' . $filiere->nom);
            \Log::info('Recherche de la classe', ['nom_classe' => $nomClasse]);
            
            try {
                // Vérifier si une classe avec ce nom existe déjà
                $classe = Classe::where('nom', $nomClasse)->first();
                
                if (!$classe) {
                    // Journalisation avant création
                    $classeData = [
                        'nom' => $nomClasse,
                        'niveau' => $niveau->nom,
                        'filiere' => $filiere->nom,
                        'capacite' => 30,
                        'is_active' => true
                    ];
                    \Log::info('Création d\'une nouvelle classe', $classeData);
                    
                    // Créer une nouvelle classe avec create au lieu de new + save
                    $classe = Classe::create($classeData);
                    \Log::info('Nouvelle classe créée avec succès', ['classe_id' => $classe->id]);
                } else {
                    \Log::info('Classe existante récupérée', [
                        'classe_id' => $classe->id,
                        'filiere' => $classe->filiere,
                        'niveau' => $classe->niveau
                    ]);
                }
                
                // Vérifier que la classe a bien un ID
                if (empty($classe->id)) {
                    throw new \Exception('La classe n\'a pas d\'ID après création/récupération');
                }
                
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la création/récupération de la classe', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
            
            // Créer l'inscription
            Inscription::create([
                'etudiant_id' => $user->id,
                'classe_id' => $classe->id,
                'annee_scolaire' => now()->format('Y') . '-' . (now()->format('Y') + 1),
                'statut' => 'inscrit',
            ]);
            
            // Valider la transaction
            DB::commit();
            
            // Retourner une réponse appropriée
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Élève créé avec succès',
                    'data' => $user->load('inscriptions')
                ], 201);
            }
            
            return redirect()->route('admin.eleves.index')
                ->with('success', 'Élève créé avec succès');
                
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            DB::rollBack();
            
            // Journaliser l'erreur
            \Log::error('Erreur lors de la création de l\'élève : ' . $e->getMessage());
            
            // Retourner une réponse d'erreur
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'Une erreur est survenue lors de la création de l\'élève',
                    'details' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de l\'élève');
        }
    }

    /**
     * Affiche les détails d'un élève
     *
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function show(User $eleve)
    {
        $eleve->load(['niveau', 'absences.matiere', 'paiements']);
        
        if (request()->wantsJson()) {
            if ($eleve->role !== 'eleve') {
                return response()->json([
                    'message' => 'Ressource non trouvée'
                ], 404);
            }
            return response()->json(['data' => $eleve]);
        }
        
        return view('admin.eleves.show', compact('eleve'));
    }

    /**
     * Affiche le formulaire de modification d'un élève
     *
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function edit(User $eleve)
    {
        $this->authorize('update', $eleve);
        
        if ($eleve->role !== 'eleve' && request()->wantsJson()) {
            return response()->json([
                'message' => 'Ressource non trouvée'
            ], 404);
        }
        
        $niveaux = Niveau::orderBy('nom')->pluck('nom', 'id');
        $filieres = Filiere::orderBy('nom')->pluck('nom', 'id');
        $matieres = Matiere::orderBy('nom')->get();
        $eleve->load('matieres');
        
        if (request()->wantsJson()) {
            return response()->json([
                'data' => [
                    'eleve' => $eleve->load('matieres'),
                    'niveaux' => $niveaux,
                    'filieres' => $filieres,
                    'matieres' => $matieres,
                    'form_action' => route('admin.eleves.update', $eleve),
                    'method' => 'PUT'
                ]
            ]);
        }
        
        return view('admin.eleves.edit', compact('eleve', 'niveaux', 'filieres', 'matieres'));
    }

    /**
     * Met à jour un élève existant
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $eleve)
    {
        $this->authorize('update', $eleve);
        
        // Validation des données
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($eleve->id),
            ],
            'cne' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users')->ignore($eleve->id),
            ],
            'date_naissance' => 'required|date|before:today',
            'lieu_naissance' => 'required|string|max:255',
            'adresse' => 'required|string|max:500',
            'telephone' => 'required|string|max:20',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'required|exists:filieres,id',
            'nom_pere' => 'required|string|max:255',
            'telephone_pere' => 'required|string|max:20',
            'nom_mere' => 'required|string|max:255',
            'telephone_mere' => 'required|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'sometimes|required|in:actif,inactif,suspendu',
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'matieres' => 'required|array',
            'matieres.*' => 'exists:matieres,id',
        ]);

        // Préparation des données de mise à jour
        $updateData = [
            'name' => $validated['nom'] . ' ' . $validated['prenom'],
            'email' => $validated['email'],
            'cne' => $validated['cne'],
            'date_naissance' => $validated['date_naissance'],
            'lieu_naissance' => $validated['lieu_naissance'],
            'adresse' => $validated['adresse'],
            'telephone' => $validated['telephone'],
            'niveau_id' => $validated['niveau_id'],
            'nom_pere' => $validated['nom_pere'],
            'telephone_pere' => $validated['telephone_pere'],
            'nom_mere' => $validated['nom_mere'],
            'telephone_mere' => $validated['telephone_mere'],
        ];
        
        // Mise à jour du mot de passe si fourni
        if (isset($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }
        
        // Mise à jour du statut si fourni
        if (isset($validated['status'])) {
            $updateData['status'] = $validated['status'];
        }
        
        // Mise à jour de l'utilisateur
        $eleve->update($updateData);
        
        // Gestion de la photo de profil
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($eleve->photo) {
                Storage::disk('public')->delete($eleve->photo);
            }
            $path = $request->file('photo')->store('profiles', 'public');
            $eleve->update(['photo' => $path]);
        }
        
        // Mise à jour des matières
        if (isset($validated['matieres'])) {
            $eleve->matieres()->sync($validated['matieres']);
        }
        
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Élève mis à jour avec succès',
                'data' => $eleve->fresh()
            ]);
        }
        
        return redirect()->route('admin.eleves.show', $eleve)
            ->with('success', 'Élève mis à jour avec succès.');
    }

    /**
     * Affiche le formulaire de changement de mot de passe
     * 
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function editPassword(User $eleve)
    {
        $this->authorize('update', $eleve);
        
        if ($eleve->role !== 'eleve' && request()->wantsJson()) {
            return response()->json([
                'message' => 'Ressource non trouvée'
            ], 404);
        }
        
        if (request()->wantsJson()) {
            return response()->json([
                'data' => [
                    'eleve' => $eleve->only(['id', 'name', 'email']),
                    'form_action' => route('admin.eleves.password.update', $eleve),
                    'method' => 'PUT'
                ]
            ]);
        }
        
        return view('admin.eleves.password', compact('eleve'));
    }

    /**
     * Met à jour le mot de passe de l'élève
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request, User $eleve)
    {
        $this->authorize('update', $eleve);
        
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($eleve->role !== 'eleve' && request()->wantsJson()) {
            return response()->json([
                'message' => 'Ressource non trouvée'
            ], 404);
        }

        $eleve->update([
            'password' => Hash::make($validated['password']),
        ]);
        
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Mot de passe mis à jour avec succès',
                'data' => $eleve->fresh()
            ]);
        }

        return redirect()->route('admin.eleves.show', $eleve)
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }

    /**
     * Supprime un élève
     *
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(User $eleve)
    {
        $this->authorize('delete', $eleve);
        
        // Vérifier si l'utilisateur est bien un élève
        if ($eleve->role !== 'eleve') {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'La ressource demandée n\'est pas un élève.'
                ], 422);
            }
            
            return redirect()->route('admin.eleves.index')
                ->with('error', 'La ressource demandée n\'est pas un élève.');
        }
        
        // Supprimer la photo de profil si elle existe
        if ($eleve->photo && Storage::disk('public')->exists($eleve->photo)) {
            Storage::disk('public')->delete($eleve->photo);
        }
        
        // Supprimer l'élève
        $eleve->delete();
        
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Élève supprimé avec succès.'
            ], 200);
        }
        
        return redirect()->route('admin.eleves.index')
            ->with('success', 'Élève supprimé avec succès.');
    }
    
    /**
     * Désactive un élève
     *
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function deactivate(User $eleve)
    {
        $this->authorize('delete', $eleve);
        
        if ($eleve->role !== 'eleve' && request()->wantsJson()) {
            return response()->json([
                'message' => 'Ressource non trouvée'
            ], 404);
        }
        
        $eleve->update(['status' => 'inactif']);
        
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Élève désactivé avec succès',
                'data' => $eleve->fresh()
            ]);
        }
        
        return redirect()->route('admin.eleves.index')
            ->with('success', 'L\'élève a été désactivé avec succès.');
    }

    /**
     * Réactive un élève
     *
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function activate(User $eleve)
    {
        $this->authorize('update', $eleve);
        
        if ($eleve->role !== 'eleve' && request()->wantsJson()) {
            return response()->json([
                'message' => 'Ressource non trouvée'
            ], 404);
        }
        
        $eleve->update(['status' => 'actif']);
        
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Élève activé avec succès',
                'data' => $eleve->fresh()
            ]);
        }
        
        return redirect()->route('admin.eleves.show', $eleve)
            ->with('success', 'L\'élève a été réactivé avec succès.');
    }
}
