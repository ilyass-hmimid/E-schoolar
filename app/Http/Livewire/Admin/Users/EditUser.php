<?php

namespace App\Http\Livewire\Admin\Users;

use Livewire\Component;
use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;

class EditUser extends Component
{
    use WithFileUploads;

    public $userId;
    public $user;
    
    // Informations de base
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role;
    
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
    public $existingPhoto;
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
        'password' => 'nullable|confirmed|min:8',
        'role' => 'required|in:' . 'admin,professeur,assistant,eleve',
        'phone' => 'nullable|string|max:20',
        'cin' => 'nullable|string|max:20',
        'cne' => 'nullable|string|max:20',
        'date_naissance' => 'nullable|date',
        'photo' => 'nullable|image|max:2048',
    ];
    
    public function mount($userId)
    {
        $this->userId = $userId;
        $this->loadUserData();
        
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
    
    protected function loadUserData()
    {
        $this->user = User::findOrFail($this->userId);
        
        // Informations de base
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->role = $this->user->role;
        $this->is_active = $this->user->is_active;
        
        // Informations de contact
        $this->phone = $this->user->phone;
        $this->address = $this->user->address;
        $this->city = $this->user->city;
        $this->postal_code = $this->user->postal_code;
        $this->country = $this->user->country ?? 'Maroc';
        
        // Informations personnelles
        $this->date_naissance = $this->user->date_naissance;
        $this->lieu_naissance = $this->user->lieu_naissance;
        $this->cin = $this->user->cin;
        $this->cne = $this->user->cne;
        $this->sexe = $this->user->sexe ?? 'M';
        $this->situation_familiale = $this->user->situation_familiale;
        $this->nombre_enfants = $this->user->nombre_enfants ?? 0;
        
        // Informations professionnelles
        $this->profession = $this->user->profession;
        $this->societe = $this->user->societe;
        $this->fonction = $this->user->fonction;
        
        // Informations académiques
        $this->niveau_etude = $this->user->niveau_etude;
        $this->filiere = $this->user->filiere;
        $this->etablissement = $this->user->etablissement;
        
        // Photo
        $this->existingPhoto = $this->user->photo_url;
        
        // Bio
        $this->bio = $this->user->bio;
        
        // Informations des parents (pour les élèves)
        $this->parent_name = $this->user->parent_name;
        $this->parent_phone = $this->user->parent_phone;
        $this->parent_email = $this->user->parent_email;
        $this->parent_profession = $this->user->parent_profession;
    }
    
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    
    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'password' => 'nullable|confirmed|min:8',
            'role' => 'required|in:' . 'admin,professeur,assistant,eleve',
        ]);
        
        try {
            $user = User::findOrFail($this->userId);
            
            // Mise à jour des données de base
            $updateData = [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'is_active' => $this->is_active,
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
                'bio' => $this->bio,
                'parent_name' => $this->parent_name,
                'parent_phone' => $this->parent_phone,
                'parent_email' => $this->parent_email,
                'parent_profession' => $this->parent_profession,
            ];
            
            // Mise à jour du mot de passe si fourni
            if (!empty($this->password)) {
                $updateData['password'] = Hash::make($this->password);
            }
            
            // Gestion de la photo
            if ($this->photo) {
                // Supprimer l'ancienne photo si elle existe
                if ($user->photo) {
                    \Storage::disk('public')->delete($user->photo);
                }
                $updateData['photo'] = $this->photo->store('profile-photos', 'public');
            }
            
            $user->update($updateData);
            
            // Mise à jour du rôle si nécessaire
            if ($user->role !== $this->role) {
                $user->syncRoles([$this->role]);
            }
            
            // Émettre un événement pour la notification
            $this->dispatch('user-updated', [
                'type' => 'success',
                'message' => 'Utilisateur mis à jour avec succès',
                'user_id' => $user->id
            ]);
            
        } catch (\Exception $e) {
            $this->addError('form_error', 'Une erreur est survenue lors de la mise à jour de l\'utilisateur.');
            \Log::error('Erreur mise à jour utilisateur', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('user-updated', [
                'type' => 'error',
                'message' => 'Erreur lors de la mise à jour de l\'utilisateur: ' . $e->getMessage()
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.admin.users.edit-user');
    }
}
