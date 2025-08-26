<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TestController extends Controller
{
    public function testAuth()
    {
        $user = Auth::user();
        
        return Inertia::render('Test/Auth', [
            'user' => $user,
            'isAuthenticated' => Auth::check(),
            'userRole' => $user ? $user->role : null,
            'isActive' => $user ? $user->is_active : false,
        ]);
    }

    public function testRole()
    {
        $user = Auth::user();
        
        return Inertia::render('Test/Role', [
            'user' => $user,
            'message' => 'Vous avez accès à cette page car vous avez le bon rôle.',
        ]);
    }

    public function testAdmin()
    {
        return Inertia::render('Test/Admin', [
            'message' => 'Page réservée aux administrateurs uniquement.',
        ]);
    }

    public function testProfessor()
    {
        return Inertia::render('Test/Professor', [
            'message' => 'Page réservée aux professeurs uniquement.',
        ]);
    }

    public function testAssistant()
    {
        return Inertia::render('Test/Assistant', [
            'message' => 'Page réservée aux assistants uniquement.',
        ]);
    }

    public function testStudent()
    {
        return Inertia::render('Test/Student', [
            'message' => 'Page réservée aux étudiants uniquement.',
        ]);
    }

    public function testSpatieRoles()
    {
        $user = Auth::user();
        
        return response()->json([
            'user' => $user->only(['id', 'name', 'email']),
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'has_role_admin' => $user->hasRole('admin'),
            'has_any_role' => $user->hasAnyRole(['admin', 'professeur', 'assistant', 'eleve']),
            'has_all_roles' => $user->hasAllRoles(['admin']),
        ]);
    }
}
