<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    /**
     * Update the user's password.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request): RedirectResponse
    {
        $this->authorize('updatePassword', $request->user());

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'password_changed_at' => now(),
        ]);

        return back()->with('status', 'Votre mot de passe a été mis à jour avec succès.');
    }
}
