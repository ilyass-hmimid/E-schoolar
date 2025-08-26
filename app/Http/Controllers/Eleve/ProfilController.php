<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class ProfilController extends Controller
{
    /**
     * Affiche le profil de l'élève
     */
    public function edit()
    {
        $user = auth()->user();
        return Inertia::render('Eleve/Profil/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'photo' => $user->profile_photo_url,
            ],
        ]);
    }

    /**
     * Met à jour le profil de l'élève
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'min:8', 'different:current_password'],
        ]);
        
        // Mise à jour des informations de base
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        
        // Mise à jour du mot de passe si fourni
        if ($request->filled('new_password')) {
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);
        }
        
        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}
