<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DebugController extends Controller
{
    public function checkAuth()
    {
        try {
            // Vérifier la connexion à la base de données
            DB::connection()->getPdo();
            
            // Vérifier l'authentification
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'not_authenticated',
                    'message' => 'Aucun utilisateur connecté.'
                ]);
            }
            
            // Vérifier si l'utilisateur est admin
            $isAdmin = $user->is_admin ?? false;
            
            return response()->json([
                'status' => 'success',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_admin' => $isAdmin,
                    'role' => $user->role ?? 'non défini'
                ],
                'is_authenticated' => Auth::check(),
                'is_admin' => $isAdmin
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
