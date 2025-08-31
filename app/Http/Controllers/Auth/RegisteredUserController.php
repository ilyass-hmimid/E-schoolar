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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Illuminate\Http\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     * Seul l'administrateur peut accéder à cette page
     */
    /**
     * Display the registration view.
     * Seul l'administrateur peut accéder à cette page
     */
    public function create(): Response
    {
        $this->authorize('create', User::class);
        
        return Inertia::location(route('admin.users.create'))
            ->with('message', 'Accès à la création d\'utilisateur');
    }

    /**
     * Handle an incoming registration request.
     * Seul l'administrateur peut créer des comptes
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Vérifier l'autorisation de création d'utilisateur
        $this->authorize('create', User::class);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,professeur,assistant,eleve'],
        ]);

        try {
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
                
        } catch (\Exception $e) {
            if (app()->environment('local', 'development')) {
                \Log::debug('Erreur lors de la création du compte utilisateur', [
                    'error' => $e->getMessage(),
                    'request_data' => $request->except(['password', 'password_confirmation'])
                ]);
            } else {
                \Log::error('Erreur lors de la création du compte utilisateur', [
                    'error' => $e->getMessage(),
                    'user_data' => [
                        'name' => $request->name,
                        'email' => $request->email,
                        'role' => $request->role
                    ]
                ]);
            }
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de la création du compte. Veuillez réessayer.']);
        }
    }
}
