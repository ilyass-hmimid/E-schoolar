<?php

// phpcs:disable SlevomatCodingStandard.Namespaces.UnusedUses

namespace App\Http\Livewire\Admin\Users;

use Livewire\Component;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Models\Inscription;
use App\Enums\RoleType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

class CreateUser extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role;
    public $is_active = true;
    
    // Informations de contact
    public $phone;
    public $address;
    public $city;
    public $postal_code;
    public $country;
    
    // Informations personnelles
    public $date_naissance;
    public $lieu_naissance;
    public $cin;
    public $cne;
    public $sexe;
    public $situation_familiale;
    public $nombre_enfants;
    
    // Informations professionnelles
    public $profession;
    public $societe;
    public $fonction;
    
    // Informations scolaires
    public $niveau_id;
    public $filiere_id;
    public $etablissement;
    public $bio;
    
    // Photo de profil
    public $photo;
    
    // Données pour les sélecteurs
    public $niveaux = [];
    public $filieres = [];
    public $matieres = [];
    public $matieres_selectionnees = [];
    
    // Rôles disponibles
    public $roles = [
        ['value' => 'admin', 'label' => 'Administrateur'],
        ['value' => 'professeur', 'label' => 'Professeur'],
        ['value' => 'eleve', 'label' => 'Élève'],
        ['value' => 'assistant', 'label' => 'Assistant'],
    ];
    
    public function mount()
    {
        $this->niveaux = Niveau::orderBy('nom')->get();
        $this->filieres = Filiere::orderBy('nom')->get();
        
        // Charger les matières si une filière est déjà sélectionnée
        if ($this->filiere_id) {
            $this->loadMatieres();
        }
    }
    
    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,professeur,eleve,assistant',
            'is_active' => 'boolean',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:100',
            'cin' => 'nullable|string|max:20',
            'cne' => 'nullable|string|max:20',
            'sexe' => 'nullable|in:homme,femme',
            'situation_familiale' => 'nullable|string|max:50',
            'nombre_enfants' => 'nullable|integer|min:0',
            'profession' => 'nullable|string|max:100',
            'societe' => 'nullable|string|max:100',
            'fonction' => 'nullable|string|max:100',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'filiere_id' => 'nullable|exists:filieres,id',
            'etablissement' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ];
        
        // Règles spécifiques pour les élèves
        if ($this->role === 'eleve') {
            $rules['niveau_id'] = 'required|exists:niveaux,id';
            $rules['filiere_id'] = 'required|exists:filieres,id';
            $rules['matieres_selectionnees'] = 'required|array|min:1';
            $rules['matieres_selectionnees.*'] = 'exists:matieres,id';
        }
        
        return $rules;
    }
    
    public function updatedRole($value)
    {
        // Réinitialiser les champs spécifiques au rôle
        if ($value !== 'eleve') {
            $this->niveau_id = null;
            $this->filiere_id = null;
            $this->matieres = [];
            $this->matieres_selectionnees = [];
        } else if ($this->filiere_id) {
            $this->loadMatieres();
        }
    }
    
    public function updatedFiliereId()
    {
        $this->matieres_selectionnees = [];
        $this->loadMatieres();
    }
    
    public function loadMatieres()
    {
        if ($this->filiere_id) {
            $this->matieres = Matiere::where('filiere_id', $this->filiere_id)
                ->orderBy('nom')
                ->get();
        } else {
            $this->matieres = [];
        }
    }
    
    public function save()
    {
        $this->validate($this->rules());
        
        try {
            return DB::transaction(function () {
                // Création de l'utilisateur
                $user = new User([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'role' => $this->role,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'city' => $this->city,
                    'postal_code' => $this->postal_code,
                    'country' => $this->country,
                    'date_naissance' => $this->date_naissance,
                    'lieu_naissance' => $this->lieu_naissance,
                    'cin' => $this->cin,
                    'cne' => $this->cne,
                    'sexe' => $this->sexe,
                    'situation_familiale' => $this->situation_familiale,
                    'nombre_enfants' => $this->nombre_enfants,
                    'profession' => $this->profession,
                    'societe' => $this->societe,
                    'fonction' => $this->fonction,
                    'niveau_id' => $this->niveau_id,
                    'filiere_id' => $this->filiere_id,
                    'etablissement' => $this->etablissement,
                    'is_active' => $this->is_active,
                    'bio' => $this->bio,
                ]);
                
                // Sauvegarder l'utilisateur
                $user->save();
                
                // Traitement de la photo si fournie
                if ($this->photo) {
                    $user->updateProfilePhoto($this->photo);
                }
                
                // Assigner le rôle
                $user->assignRole($this->role);
                
                // Si c'est un élève, créer les inscriptions aux matières
                if ($this->role === 'eleve' && !empty($this->matieres_selectionnees)) {
                    foreach ($this->matieres_selectionnees as $matiereId) {
                        Inscription::create([
                            'etudiant_id' => $user->id,
                            'matiere_id' => $matiereId,
                            'niveau_id' => $this->niveau_id,
                            'filiere_id' => $this->filiere_id,
                            'date_inscription' => now(),
                            'annee_scolaire' => now()->format('Y') . '/' . (now()->year + 1),
                            'statut' => 'actif',
                            'montant' => 0, // À ajuster selon la logique métier
                        ]);
                    }
                }
                
                session()->flash('message', 'Utilisateur créé avec succès!');
                
                return redirect()->route('admin.users.index');
            });
        } catch (\Exception $e) {
            \Log::error('Erreur création utilisateur', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Une erreur est survenue lors de la création de l\'utilisateur: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.admin.users.create-user', [
            'roles' => $this->roles,
            'niveaux' => $this->niveaux,
            'filieres' => $this->filieres,
            'matieres' => $this->matieres,
        ]);
    }
}
