<?php

namespace App\Http\Requests;

use App\Enums\RoleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Déterminer si l'utilisateur est autorisé à faire cette requête
     */
    public function authorize(): bool
    {
        return true; // L'autorisation est gérée par les middlewares
    }

    /**
     * Règles de validation pour la mise à jour d'un utilisateur
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user?->id,
            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
            'role' => [
                'required',
                'string',
                'in:' . implode(',', [
                    RoleType::ADMIN->value,
                    RoleType::PROFESSEUR->value,
                    RoleType::ASSISTANT->value,
                    RoleType::ELEVE->value
                ])
            ],
            'phone' => 'nullable|string|max:20|regex:/^[0-9\-\+\(\)\s]+$/i',
            'address' => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean',
            
            // Champs spécifiques aux élèves
            'parent_name' => [
                'nullable',
                'required_if:role,' . RoleType::ELEVE->value,
                'string',
                'max:255'
            ],
            'parent_phone' => [
                'nullable',
                'required_if:role,' . RoleType::ELEVE->value,
                'string',
                'max:20',
                'regex:/^[0-9\-\+\(\)\s]+$/i'
            ],
            'date_naissance' => [
                'nullable',
                'required_if:role,' . RoleType::ELEVE->value,
                'date',
                'before:today'
            ],
            'niveau_id' => [
                'nullable',
                'required_if:role,' . RoleType::ELEVE->value,
                'exists:niveaux,id'
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'L\'email doit être une adresse email valide',
            'email.unique' => 'Cet email est déjà utilisé',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas',
            'password.min' => 'Le mot de passe doit contenir au moins :min caractères',
            'role.required' => 'Le rôle est obligatoire',
            'role.in' => 'Le rôle sélectionné est invalide',
            'phone.regex' => 'Le format du numéro de téléphone est invalide',
            'date_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui',
            'niveau_id.exists' => 'Le niveau sélectionné est invalide',
            'parent_name.required_if' => 'Le nom du parent est obligatoire pour un élève',
            'parent_phone.required_if' => 'Le téléphone du parent est obligatoire pour un élève',
            'date_naissance.required_if' => 'La date de naissance est obligatoire pour un élève',
            'niveau_id.required_if' => 'Le niveau est obligatoire pour un élève',
        ];
    }

    /**
     * Préparer les données pour validation
     */
    protected function prepareForValidation()
    {
        // S'assurer que les champs booléens sont correctement castés
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN)
            ]);
        }
    }
}
