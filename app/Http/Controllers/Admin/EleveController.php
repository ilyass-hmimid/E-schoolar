<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Paiement;
use App\Models\Absence;
use App\Models\Inscription;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use App\Notifications\WelcomeNotification;

class EleveController extends BaseAdminController
{
    /**
     * Constructeur du contrôleur
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Affiche la liste des élèves avec filtres et tri
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Récupérer les paramètres de requête
        $search = request('search');
        $status = request('status');
        $niveau_id = request('niveau_id');
        $sort = request('sort', 'name');
        $direction = request('direction', 'asc');
        
        // Initialiser la requête avec les relations et les compteurs
        $query = User::with(['niveau', 'filiere'])
            ->where('role', '4') // Rôle élève est stocké comme '4' dans la base de données
            ->withCount(['absences', 'paiements']);
        
        // Appliquer les filtres
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cni', 'like', "%{$search}%");
            });
        }
        
        if (!empty($status)) {
            $query->where('status', $status);
        }
        
        if (!empty($niveau_id)) {
            $query->where('niveau_id', $niveau_id);
        }
        
        // Valider et appliquer le tri
        $validSorts = ['name', 'email', 'cni', 'status', 'niveau_id'];
        $sort = in_array($sort, $validSorts) ? $sort : 'name';
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';
        
        $query->orderBy($sort, $direction);
        
        // Pagination avec conservation des paramètres de requête
        $eleves = $query->paginate(20)
            ->appends([
                'search' => $search,
                'status' => $status,
                'niveau_id' => $niveau_id,
                'sort' => $sort,
                'direction' => $direction
            ]);
        
        // Récupérer les niveaux depuis la base de données
        $niveaux = \App\Models\Niveau::where('est_actif', 1)
            ->pluck('nom', 'id')
            ->toArray();
        
        // Statistiques
        $stats = [
            'total_eleves' => User::where('role', '4')->count(),
            'eleves_actifs' => User::where('role', '4')->where('status', 'actif')->count(),
            'nouveaux_eleves' => User::where('role', '4')
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'taux_absenteisme' => 0 // À calculer si nécessaire
        ];
        
        // Log pour débogage
        $fullQuery = $query->toSql();
        foreach ($query->getBindings() as $binding) {
            $fullQuery = preg_replace('/\?/', "'" . $binding . "'", $fullQuery, 1);
        }
        
        \Log::info('Requête SQL complète : ' . $fullQuery);
        \Log::info('Données des élèves récupérées', [
            'total' => $eleves->total(),
            'items_count' => $eleves->total(),
            'niveaux' => $niveaux,
            'stats' => $stats
        ]);
        
        // Retourner la réponse appropriée selon le type de requête
        if (request()->wantsJson()) {
            return response()->json([
                'data' => $eleves->items(),
                'meta' => [
                    'current_page' => $eleves->currentPage(),
                    'last_page' => $eleves->lastPage(),
                    'per_page' => $eleves->perPage(),
                    'total' => $eleves->total(),
                ],
                'stats' => $stats
            ]);
        }
        
        // Pour la vue web, on passe les stats directement à la vue
        return view('admin.eleves.index', [
            'eleves' => $eleves,
            'niveaux' => $niveaux,
            'stats' => $stats
        ]);
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
            // Récupérer les données depuis la base de données
            $niveaux = \App\Models\Niveau::where('est_actif', 1)
                ->orderBy('ordre')
                ->pluck('nom', 'id')
                ->toArray();
                
            $filieres = \App\Models\Filiere::where('est_actif', 1)
                ->where('niveau_id', 5) // Seulement les filières de 2ème année bac
                ->orderBy('nom')
                ->pluck('nom', 'id')
                ->toArray();
                
            $langues = [
                'arabe' => 'Arabe',
                'francais' => 'Français',
                'bilingue' => 'Bilingue (Arabe/Français)'
            ];
            
            // Log des données pour le débogage
            \Log::info('=== DONNÉES DU FORMULAIRE ===');
            \Log::info('Niveaux: ' . json_encode($niveaux, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            \Log::info('Filières: ' . json_encode($filieres, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            \Log::info('Langues: ' . json_encode($langues, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            \Log::info('=== FIN DES DONNÉES DU FORMULAIRE ===');
            
            // Vérifier les types et valeurs
            foreach ($niveaux as $id => $nom) {
                \Log::debug(sprintf("Niveau - ID: %s, Nom: %s, Type: %s", 
                    $id, 
                    $nom, 
                    gettype($nom)
                ));
            }
            
            foreach ($filieres as $id => $nom) {
                \Log::debug(sprintf("Filière - ID: %s, Nom: %s, Type: %s", 
                    $id, 
                    $nom, 
                    gettype($nom)
                ));
            }
            
            foreach ($langues as $code => $nom) {
                \Log::debug(sprintf("Langue - Code: %s, Nom: %s, Type: %s", 
                    $code, 
                    $nom, 
                    gettype($nom)
                ));
            }
            
            if (empty($niveaux) || empty($filieres)) {
                throw new \Exception('Aucun niveau ou filière actif trouvé dans la base de données.');
            }
            
            if (request()->wantsJson()) {
                return response()->json([
                    'data' => [
                        'niveaux' => $niveaux,
                        'filieres' => $filieres,
                        'langues' => $langues,
                        'form_action' => route('admin.eleves.store'),
                        'method' => 'POST'
                    ],
                    'success' => true
                ], 200);
            }
            
            // Log des données transmises à la vue
            \Log::info('Données transmises à la vue create:', [
                'niveaux_count' => count($niveaux),
                'filieres_count' => count($filieres),
                'langues_count' => count($langues),
                'niveaux_sample' => array_slice($niveaux, 0, 3, true), // Affiche les 3 premiers niveaux pour le débogage
                'filieres_sample' => array_slice($filieres, 0, 3, true) // Affiche les 3 premières filières pour le débogage
            ]);
            
            // Données de débogage déjà enregistrées dans les logs
            
            return view('admin.eleves.create', [
                'niveaux' => $niveaux,
                'filieres' => $filieres,
                'langues' => $langues
            ]);
            
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
        \Log::info('=== DÉBUT MÉTHODE STORE ===');
        \Log::info('Méthode: ' . $request->method());
        \Log::info('URL: ' . $request->fullUrl());
        \Log::info('En-têtes: ' . json_encode($request->header(), JSON_UNESCAPED_UNICODE));
        \Log::info('Données brutes: ' . file_get_contents('php://input'));
        \Log::info('Données reçues (all): ' . json_encode($request->all(), JSON_UNESCAPED_UNICODE));
        \Log::info('Données reçues (input): ' . json_encode($request->input(), JSON_UNESCAPED_UNICODE));
        
        // Récupérer les données depuis la base de données
        $niveaux = \App\Models\Niveau::where('est_actif', 1)
            ->orderBy('ordre')
            ->pluck('nom', 'id')
            ->toArray();
            
        $filieres = \App\Models\Filiere::where('est_actif', 1)
            ->where('niveau_id', 5) // Seulement les filières de 2ème année bac
            ->orderBy('nom')
            ->pluck('nom', 'id')
            ->toArray();
            
        $langues = [
            'arabe' => 'Arabe',
            'francais' => 'Français',
            'bilingue' => 'Bilingue (Arabe/Français)'
        ];
        
        // Log des données pour le débogage
        \Log::info('=== DONNÉES DE LA BASE DE DONNÉES ===');
        \Log::info('Niveaux disponibles: ' . json_encode($niveaux, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        \Log::info('Filières disponibles: ' . json_encode($filieres, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        \Log::info('Langues disponibles: ' . json_encode($langues, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        \Log::info('=== FIN DES DONNÉES ===');

        // Valider les données du formulaire
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'niveau_id' => ['required', 'integer', function($attribute, $value, $fail) use ($niveaux) {
                \Log::info('=== VÉRIFICATION DU NIVEAU ===');
                \Log::info('Valeur reçue: ' . $value);
                \Log::info('Type de la valeur: ' . gettype($value));
                \Log::info('IDs de niveaux valides: ' . implode(', ', array_keys($niveaux)));
                \Log::info('Valeur dans le tableau: ' . (array_key_exists($value, $niveaux) ? 'OUI' : 'NON'));
                \Log::info('=== FIN DE VÉRIFICATION ===');
                
                if (!array_key_exists($value, $niveaux)) {
                    $fail('Le niveau sélectionné est invalide. ID reçu: ' . $value);
                }
            }],
            'filiere_id' => ['required', 'integer', function($attribute, $value, $fail) use ($filieres) {
                \Log::info('=== VÉRIFICATION DE LA FILIÈRE ===');
                \Log::info('Valeur reçue: ' . $value);
                \Log::info('Type de la valeur: ' . gettype($value));
                \Log::info('IDs de filières valides: ' . implode(', ', array_keys($filieres)));
                \Log::info('Valeur dans le tableau: ' . (array_key_exists($value, $filieres) ? 'OUI' : 'NON'));
                \Log::info('=== FIN DE VÉRIFICATION ===');
                
                if (!array_key_exists($value, $filieres)) {
                    $fail('La filière sélectionnée est invalide. ID reçu: ' . $value);
                }
            }],
            'langue_enseignement' => ['required', 'string', function($attribute, $value, $fail) use ($langues) {
                $value = trim($value);
                $valueLower = strtolower($value);
                \Log::info('=== VÉRIFICATION DE LA LANGUE ===');
                \Log::info('Valeur reçue: ' . $value . ' (normalisée: ' . $valueLower . ')');
                \Log::info('Type de la valeur: ' . gettype($value));
                \Log::info('Valeurs attendues: ' . json_encode(array_keys($langues), JSON_UNESCAPED_UNICODE));
                \Log::info('Valeur dans le tableau: ' . (array_key_exists($valueLower, $langues) ? 'OUI' : 'NON'));
                \Log::info('=== FIN DE VÉRIFICATION ===');
                
                if (!array_key_exists($valueLower, $langues)) {
                    $fail('La langue d\'enseignement sélectionnée est invalide. Valeur reçue: "' . $value . '" - Valeurs attendues: ' . implode(', ', array_keys($langues)));
                }
            }],
            'nom_pere' => 'required|string|max:100',
            'telephone_pere' => 'required|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Convertir la langue en minuscules pour la cohérence
        $validated['langue_enseignement'] = strtolower($validated['langue_enseignement']);
        
        // Récupérer les informations complètes du niveau et de la filière
        $niveau = \App\Models\Niveau::find($validated['niveau_id']);
        $filiere = \App\Models\Filiere::find($validated['filiere_id']);
        
        if (!$niveau || !$filiere) {
            throw new \Exception('Niveau ou filière introuvable dans la base de données');
        }
        
        // Vérifier que la filière appartient bien au niveau sélectionné
        if ($filiere->niveau_id != $niveau->id) {
            throw new \Exception('La filière sélectionnée ne correspond pas au niveau choisi');
        }
        
        $niveauNom = $niveau->nom;
        $filiereNom = $filiere->nom;
        
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
                'role' => '4', // Rôle 4 pour les élèves
                'date_naissance' => $validated['date_naissance'],
                'lieu_naissance' => $validated['lieu_naissance'],
                'adresse' => $validated['adresse'],
                'telephone' => $validated['telephone'],
                'niveau_id' => $validated['niveau_id'],
                'filiere_id' => $validated['filiere_id'],
                'langue_enseignement' => $validated['langue_enseignement'],
                'nom_pere' => $validated['nom_pere'],
                'telephone_pere' => $validated['telephone_pere'],
                'status' => 'actif',
                'email_verified_at' => now(),
                'is_active' => true,
                'remember_token' => Str::random(10),
                'date_inscription' => now(),
                'date_debut' => now(),
            ];
            
            // Journalisation avant création de l'utilisateur
            \Log::info('Tentative de création d\'utilisateur', [
                'user_data' => array_merge($userData, ['password' => '*****']), // Masquer le mot de passe
                'has_photo' => $request->hasFile('photo')
            ]);
            
            try {
                // Créer l'utilisateur
                $user = User::create($userData);
                \Log::info('Utilisateur créé avec succès', ['user_id' => $user->id]);
                
                // Gérer la photo de profil si elle est fournie
                if ($request->hasFile('photo')) {
                    try {
                        $path = $request->file('photo')->store('profiles', 'public');
                        $user->update(['photo' => $path]);
                        \Log::info('Photo de profil enregistrée', ['path' => $path]);
                    } catch (\Exception $e) {
                        \Log::error('Erreur lors de l\'enregistrement de la photo', [
                            'error' => $e->getMessage(),
                            'user_id' => $user->id
                        ]);
                        // On continue malgré l'erreur de photo
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Échec de la création de l\'utilisateur', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
            
            // Récupérer les noms du niveau et de la filière pour les logs
            $niveauNom = $niveaux[$validated['niveau_id']] ?? 'Inconnu';
            $filiereNom = $filieres[$validated['filiere_id']] ?? 'Inconnue';
            $langue = $validated['langue_enseignement'];
            
            // Journalisation des valeurs
            \Log::info('Valeurs de filière, niveau et langue', [
                'niveau_id' => $validated['niveau_id'],
                'niveau_nom' => $niveauNom,
                'filiere_id' => $validated['filiere_id'],
                'filiere_nom' => $filiereNom,
                'langue' => $langue
            ]);
            
            // Vérifier la capacité de la classe (si nécessaire)
            $capaciteMax = 30; // Capacité par défaut
            $nombreEleves = User::where('role', '4') // Rôle élève
                ->where('niveau_id', $validated['niveau_id'])
                ->where('filiere_id', $validated['filiere_id'])
                ->where('langue_enseignement', $langue)
                ->count();
                
            if ($nombreEleves >= $capaciteMax) {
                \Log::warning('La capacité maximale de la classe est atteinte', [
                    'niveau_id' => $validated['niveau_id'],
                    'filiere_id' => $validated['filiere_id'],
                    'langue' => $langue,
                    'nombre_eleves' => $nombreEleves,
                    'capacite_max' => $capaciteMax
                ]);
                
                // On continue quand même la création, mais on enregistre un avertissement
                $messageAvertissement = "Attention : La capacité maximale de la classe est atteinte ($nombreEleves/$capaciteMax).";
                \Log::warning($messageAvertissement);
            }
            
            // Envoyer un email de bienvenue avec les identifiants (désactivé temporairement pour le débogage)
            try {
                $user->notify(new WelcomeNotification($user->email, $password));
                \Log::info('Notification de bienvenue envoyée à l\'élève', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'envoi de la notification de bienvenue', [
                    'error' => $e->getMessage(),
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                // Continuer même en cas d'échec d'envoi d'email
            }
            
            // Valider que l'utilisateur a été créé avec succès
            if (!isset($user) || !$user->exists) {
                throw new \Exception('Une erreur est survenue lors de la création de l\'utilisateur.');
            }
            
            // Valider les données avant de les utiliser
            if (empty($validated['niveau_id']) || empty($validated['filiere_id']) || empty($langue)) {
                throw new \Exception('Données de classe manquantes.');
            }
            
            // Mettre à jour les informations de classe de l'utilisateur
            $user->update([
                'niveau_id' => $validated['niveau_id'],
                'filiere_id' => $validated['filiere_id'],
                'langue_enseignement' => $langue,
                'role' => '4' // Rôle élève
            ]);
            
            // Récupérer les noms du niveau et de la filière
            $niveauNom = $niveaux[$validated['niveau_id']] ?? 'Inconnu';
            $filiereNom = $filieres[$validated['filiere_id']] ?? 'Inconnue';
            
            // Journalisation des valeurs
            \Log::info('Recherche de la classe existante', [
                'niveau_id' => $validated['niveau_id'],
                'niveau_nom' => $niveauNom,
                'filiere_id' => $validated['filiere_id'],
                'filiere_nom' => $filiereNom,
                'langue' => $langue
            ]);
            
            // Vérifier si la classe existe déjà
            $classeExistante = \DB::table('classes')
                ->where('niveau', $niveauNom)
                ->where('filiere', $filiereNom)
                ->first();
                
            // Journalisation du résultat de la recherche
            if ($classeExistante) {
                \Log::info('Classe existante trouvée', [
                    'classe_id' => $classeExistante->id,
                    'nom' => $classeExistante->nom,
                    'niveau' => $classeExistante->niveau,
                    'filiere' => $classeExistante->filiere,
                    'capacite' => $classeExistante->capacite
                ]);
            } else {
                \Log::info('Aucune classe existante trouvée, création d\'une nouvelle classe');
            }
                
            if (!$classeExistante) {
                // Préparer les données de la classe
                $classeData = [
                    'nom' => "$niveauNom $filiereNom",
                    'niveau' => $niveauNom,
                    'filiere' => $filiereNom,
                    'capacite' => 30,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                \Log::info('Création d\'une nouvelle classe', $classeData);
                
                // Créer la classe
                try {
                    $classeId = \DB::table('classes')->insertGetId($classeData);
                    \Log::info('Nouvelle classe créée avec succès', [
                        'classe_id' => $classeId,
                        'classe_data' => $classeData
                    ]);
                } catch (\Exception $e) {
                    // Enregistrer l'erreur mais continuer l'exécution
                    $errorMessage = 'Erreur lors de la création de la classe: ' . $e->getMessage();
                    \Log::error($errorMessage, [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'classe_data' => $classeData
                    ]);
                    
                    // Ajouter un message d'erreur pour l'utilisateur
                    $request->session()->put('warning', 
                        'L\'élève a été créé avec succès, mais une erreur est survenue lors de la création de la classe. ' .
                        'Veuillez vérifier les logs pour plus de détails.'
                    );
                }
            }

            // Valider la transaction
            DB::commit();
            \Log::info('Élève créé avec succès', ['user_id' => $user->id]);

            // Rediriger avec un message de succès
            return redirect()->route('admin.eleves.index')
                ->with('success', 'L\'élève a été créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création de l\'élève : ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de l\'élève : ' . $e->getMessage());
        }
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
            'cni' => [
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
            'cni' => $validated['cni'],
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
        if (!$eleve->isEleve()) {
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
