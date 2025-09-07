<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class BaseAdminController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login');
            }
            
            // Autoriser uniquement les administrateurs
            if (!$user->is_admin) {
                auth()->logout();
                return redirect()->route('login')
                    ->with('error', 'Accès non autorisé. Seul un administrateur peut accéder à cette page.');
            }
            
            return $next($request);
        });
    }

    /**
     * Get the common data for all views
     *
     * @return array
     */
    protected function getCommonData()
    {
        $user = auth()->user();
        
        // Définir les variables pour la barre latérale
        return [
            'user' => $user,
            'isAdmin' => $user->is_admin ?? false,
            'isStudent' => $user->role === 'eleve',
            'isProfessor' => $user->role === 'professeur',
            'isParent' => $user->role === 'parent',
            'currentRoute' => request()->route() ? request()->route()->getName() : '',
        ];
    }
}
