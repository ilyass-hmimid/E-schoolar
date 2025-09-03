<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('getDashboardUrl')) {
    /**
     * Get the dashboard URL based on the authenticated user's role
     *
     * @return string
     */
    function getDashboardUrl()
    {
        if (!Auth::check()) {
            return url('/');
        }

        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        } elseif ($user->hasRole('professeur')) {
            return route('professeur.dashboard');
        } elseif ($user->hasRole('assistant')) {
            return route('assistant.dashboard');
        } elseif ($user->hasRole('eleve')) {
            return route('eleve.dashboard');
        }
        
        return url('/');
    }
}
