<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        
        // Définir le layout par défaut pour les routes admin
        if ($request->is('admin/*')) {
            $this->rootView = 'admin';
        } else {
            $this->rootView = 'app';
        }
        
        $userData = null;
        if ($user) {
            // Convertir le rôle en chaîne de caractères en utilisant l'énumération RoleType
            $roleValue = is_object($user->role) ? $user->role->value : $user->role;
            $roleLabel = '';
            
            // Déterminer le libellé du rôle
            switch ($roleValue) {
                case 1: // ADMIN
                    $roleLabel = 'admin';
                    break;
                case 2: // PROFESSEUR
                    $roleLabel = 'professeur';
                    break;
                case 3: // ASSISTANT
                    $roleLabel = 'assistant';
                    break;
                case 4: // ELEVE
                    $roleLabel = 'eleve';
                    break;
                default:
                    $roleLabel = 'guest';
            }
            
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $roleLabel, // Utiliser la chaîne de caractères pour le rôle
                'role_value' => $roleValue, // Conserver la valeur numérique pour référence
                'role_label' => $roleLabel, // Pour compatibilité avec le code existant
                'is_active' => $user->is_active,
            ];
        }
        
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $userData,
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                    'warning' => $request->session()->get('warning'),
                    'info' => $request->session()->get('info'),
                ];
            },
        ]);
    }
}
