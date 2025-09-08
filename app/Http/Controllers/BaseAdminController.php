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
