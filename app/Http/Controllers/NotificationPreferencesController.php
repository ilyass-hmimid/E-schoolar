<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationPreferencesController extends Controller
{
    /**
     * Affiche le formulaire des préférences de notification
     */
    public function edit()
    {
        $user = Auth::user();
        $preferences = $user->notification_preferences ?? [
            'email' => true,
            'database' => true,
            'sms' => false,
            'push' => false,
        ];

        return view('profile.notification-preferences', [
            'preferences' => $preferences
        ]);
    }

    /**
     * Met à jour les préférences de notification
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'email' => 'boolean',
            'database' => 'boolean',
            'sms' => 'boolean',
            'push' => 'boolean',
        ]);

        $user = Auth::user();
        $user->notification_preferences = $validated;
        $user->save();

        return back()->with('status', 'Préférences de notification mises à jour avec succès.');
    }
}
