<?php

namespace App\Http\Livewire\Admin\Users;

use Livewire\Component;
use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class CreateUser extends Component
{
    use WithFileUploads;

    // Informations de base
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role = RoleType::ELEVE->value;
    
    // Informations de contact
    public $phone;
    public $address;
    public $city;
    public $postal_code;
    public $country = 'Maroc';
    
    // Informations supplémentaires
    public $date_naissance;
    public $lieu_naissance;
    public $cin;
    public $cne;
    public $sexe = 'M';
    public $situation_familiale;
    public $nombre_enfants = 0;
    
    // Informations professionnelles
    public $profession;
    public $societe;
    public $fonction;
    
    // Informations académiques
    public $niveau_etude;
    public $filiere;
    public $etablissement;
    
    // Statut et photo
    public $photo;
    public $is_active = true;
    public $bio;
    
    // Parents (pour les élèves)
    public $parent_name;
    public $parent_phone;
    public $parent_email;
    public $parent_profession;
    
    // Options pour les sélecteurs
    public $roles = [];
    public $niveaux = [];
    public $filieres = [];
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|confirmed|min:8',
        'role' => 'required|in:' . 'admin,professeur,assistant,eleve',
        'phone' => 'nullable|string|max:20',
        'cin' => 'nullable|string|max:20',
        'cne' => 'nullable|string|max:20',
        'date_naissance' => 'nullable|date',
        'photo' => 'nullable|image|max:2048',
    ];
    
    public function mount()
    {
        $this->roles = [
            ['value' => RoleType::ADMIN->value, 'label' => 'Administrateur'],
            ['value' => RoleType::PROFESSEUR->value, 'label' => 'Professeur'],
            ['value' => RoleType::ASSISTANT->value, 'label' => 'Assistant'],
            ['value' => RoleType::ELEVE->value, 'label' => 'Élève'],
        ];
        
        // Charger les niveaux et filières depuis la base de données si nécessaire
        $this->niveaux = \App\Models\Niveau::all()->map(function($niveau) {
            return ['value' => $niveau->id, 'label' => $niveau->nom];
        })->toArray();
        
        $this->filieres = \App\Models\Filiere::all()->map(function($filiere) {
            return ['value' => $filiere->id, 'label' => $filiere->nom];
        })->toArray();
    }
    
    public function save()
    {
        $this->validate();
        
        try {
            // Traitement de la photo si elle existe
            $photoPath = null;
            if ($this->photo) {
                $photoPath = $this->photo->store('profile-photos', 'public');
            }
            
            // Création de l'utilisateur
            $user = User::create([
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
                'niveau_etude' => $this->niveau_etude,
                'filiere' => $this->filiere,
                'etablissement' => $this->etablissement,
                'photo' => $photoPath,
                'is_active' => $this->is_active,
                'bio' => $this->bio,
                'parent_name' => $this->parent_name,
                'parent_phone' => $this->parent_phone,
                'parent_email' => $this->parent_email,
                'parent_profession' => $this->parent_profession,
            ]);
            
            // Assigner le rôle
            $user->assignRole($this->role);
            
            // Émettre un événement pour la notification
            $this->dispatch('user-created', [
                'type' => 'success',
                'message' => 'Utilisateur créé avec succès',
                'user_id' => $user->id
            ]);
            
            // Rediriger vers la liste des utilisateurs
            return redirect()->route('admin.users.index');
            
        } catch (\Exception $e) {
            $this->addError('form_error', 'Une erreur est survenue lors de la création de l\'utilisateur.');
            \Log::error('Erreur création utilisateur', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.admin.users.create-user');
    }
}
