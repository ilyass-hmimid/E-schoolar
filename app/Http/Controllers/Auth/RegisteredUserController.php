<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Illuminate\Http\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     * Seul l'administrateur peut accéder à cette page
     */
    public function create(): Response
    {
        // Rediriger vers la page de connexion avec un message d'erreur
        return Inertia::location(route('login'))->with('error', 'L\'inscription n\'est pas autorisée.');
    }

    /**
     * Handle an incoming registration request.
     * Seul l'administrateur peut créer des comptes
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Vérifier si l'utilisateur est un administrateur
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Non autorisé.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,professeur,assistant,eleve,parent'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        event(new Registered($user));

        // Ne pas connecter automatiquement l'utilisateur
        // L'administrateur recevra un e-mail de confirmation
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès. Un e-mail de confirmation a été envoyé.');
    }
}
