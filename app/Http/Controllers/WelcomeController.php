<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        return Inertia::render('Welcome', [
            'canLogin' => !Auth::check(),
            'canRegister' => false,
            'laravelVersion' => app()->version(),
            'phpVersion' => PHP_VERSION,
            'isAuthenticated' => Auth::check(),
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ] : null,
        ]);
    }
}
