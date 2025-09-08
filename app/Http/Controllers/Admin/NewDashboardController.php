<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord
     */
    public function index()
    {
        return view('admin.dashboard', [
            'user' => Auth::user(),
            'isAdmin' => true,
            'isStudent' => false,
            'isProfessor' => false,
            'isParent' => false,
            'currentRoute' => 'admin.dashboard'
        ]);
    }
}
