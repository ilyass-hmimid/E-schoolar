<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    /**
     * Affiche le formulaire de contact
     *
     * @return \Illuminate\View\View
     */
    public function contact()
    {
        return view('support.contact');
    }

    /**
     * Traite l'envoi du formulaire de contact
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Envoyer l'email (à implémenter avec votre configuration SMTP)
        // Mail::to(config('mail.support_email'))->send(new ContactFormMail($validated));

        return redirect()->back()->with('success', 'Votre message a été envoyé avec succès !');
    }
}
