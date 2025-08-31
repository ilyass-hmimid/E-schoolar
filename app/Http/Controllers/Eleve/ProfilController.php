<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class ProfilController extends Controller
{
    /**
     * Affiche le formulaire de modification du profil
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = auth()->user();
        
        // Si l'utilisateur a une relation élève, on la charge
        if (method_exists($user, 'eleve')) {
            $user->load('eleve');
            
            // Fusionner les données de l'utilisateur et de l'élève pour faciliter l'accès dans la vue
            $userData = array_merge(
                $user->toArray(),
                $user->eleve ? $user->eleve->toArray() : []
            );
            
            // Créer un nouvel objet utilisateur avec les données fusionnées
            $user = (object) $userData;
        }
        
        return view('eleve.profil.edit', compact('user'));
    }

    /**
     * Met à jour les informations du profil
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        $rules = [
            // Informations personnelles
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telephone' => ['nullable', 'string', 'max:20'],
            'date_naissance' => ['nullable', 'date'],
            'lieu_naissance' => ['nullable', 'string', 'max:255'],
            'adresse' => ['nullable', 'string'],
            'ville' => ['nullable', 'string', 'max:100'],
            'code_postal' => ['nullable', 'string', 'max:20'],
            'pays' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            
            // Informations médicales
            'groupe_sanguin' => ['nullable', 'string', 'max:10'],
            'allergies' => ['nullable', 'string'],
            'maladies_chroniques' => ['nullable', 'string'],
            'traitement_medical' => ['nullable', 'string'],
            'contact_urgence_nom' => ['nullable', 'string', 'max:255'],
            'contact_urgence_telephone' => ['nullable', 'string', 'max:20'],
            'contact_urgence_lien' => ['nullable', 'string', 'max:100'],
            'remarques_sante' => ['nullable', 'string'],
            
            // Informations scolaires
            'annee_scolaire' => ['nullable', 'string', 'max:50'],
            'niveau_etude' => ['nullable', 'string', 'max:100'],
            'etablissement_precedent' => ['nullable', 'string', 'max:255'],
            'dernier_diplome' => ['nullable', 'string', 'max:255'],
        ];
        
        $validated = $request->validate($rules);

        // Démarrer une transaction de base de données
        DB::beginTransaction();
        
        try {
            // Traitement de la photo de profil
            if ($request->hasFile('photo')) {
                // Supprimer l'ancienne photo si elle existe
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }
                
                // Enregistrer la nouvelle photo
                $path = $request->file('photo')->store('profiles', 'public');
                
                // Redimensionner l'image si nécessaire
                $image = Image::make(storage_path('app/public/' . $path))->fit(200, 200);
                $image->save();
                
                $validated['photo'] = $path;
            } else {
                unset($validated['photo']);
            }
            
            // Mettre à jour les informations de base de l'utilisateur
            $userData = [
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'telephone' => $validated['telephone'] ?? null,
                'photo' => $validated['photo'] ?? $user->photo,
            ];
            
            $user->update($userData);
            
            // Mettre à jour les informations de l'élève si la relation existe
            if (method_exists($user, 'eleve') && $user->eleve) {
                $eleveData = [
                    'date_naissance' => $validated['date_naissance'] ?? null,
                    'lieu_naissance' => $validated['lieu_naissance'] ?? null,
                    'adresse' => $validated['adresse'] ?? null,
                    'ville' => $validated['ville'] ?? null,
                    'code_postal' => $validated['code_postal'] ?? null,
                    'pays' => $validated['pays'] ?? null,
                    'telephone' => $validated['telephone'] ?? null,
                    'groupe_sanguin' => $validated['groupe_sanguin'] ?? null,
                    'allergies' => $validated['allergies'] ?? null,
                    'maladies_chroniques' => $validated['maladies_chroniques'] ?? null,
                    'traitement_medical' => $validated['traitement_medical'] ?? null,
                    'contact_urgence_nom' => $validated['contact_urgence_nom'] ?? null,
                    'contact_urgence_telephone' => $validated['contact_urgence_telephone'] ?? null,
                    'contact_urgence_lien' => $validated['contact_urgence_lien'] ?? null,
                    'remarques_sante' => $validated['remarques_sante'] ?? null,
                    'annee_scolaire' => $validated['annee_scolaire'] ?? null,
                    'niveau_etude' => $validated['niveau_etude'] ?? null,
                    'etablissement_precedent' => $validated['etablissement_precedent'] ?? null,
                    'dernier_diplome' => $validated['dernier_diplome'] ?? null,
                ];
                
                $user->eleve->update($eleveData);
            }
            
            // Valider les modifications dans la base de données
            DB::commit();
            
            return redirect()->route('eleve.profil.edit')
                ->with('success', 'Votre profil a été mis à jour avec succès.');
                
        } catch (\Exception $e) {
            // En cas d'erreur, annuler les modifications
            DB::rollBack();
            
            // Supprimer la photo téléchargée si une erreur s'est produite
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de votre profil. Veuillez réessayer.');
        }
        
    }
    
    /**
     * Met à jour le mot de passe de l'utilisateur
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Met à jour le mot de passe de l'utilisateur
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required', 
                'confirmed',
                'min:8',
                'regex:/[a-z]/',      // au moins une minuscule
                'regex:/[A-Z]/',      // au moins une majuscule
                'regex:/[0-9]/',      // au moins un chiffre
                'regex:/[@$!%*#?&]/', // au moins un caractère spécial
            ],
        ], [
            'password.required' => 'Le champ nouveau mot de passe est obligatoire.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min' => 'Le mot de passe doit contenir au moins :min caractères.',
            'password.regex' => 'Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.',
            'current_password.required' => 'Le mot de passe actuel est requis.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
        ]);
        
        try {
            $user = $request->user();
            $user->update([
                'password' => Hash::make($validated['password']),
                'password_changed_at' => now(),
            ]);
            
            // Déconnecter l'utilisateur après la modification du mot de passe
            Auth::logoutOtherDevices($validated['password']);
            
            return back()->with('success', 'Votre mot de passe a été mis à jour avec succès.');
            
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de votre mot de passe. Veuillez réessayer.');
        }
    }
}
