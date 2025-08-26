<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ParametreController extends Controller
{
    public function index()
    {
        // Définir les paramètres par défaut s'ils n'existent pas
        $parametresParDefaut = [
            'nom_ecole' => 'Allo Tawjih',
            'email_contact' => 'contact@allotawjih.ma',
            'telephone_contact' => '+212 6 00 00 00 00',
            'adresse' => 'Adresse de l\'école, Ville, Maroc',
        ];

        // Récupérer ou créer les paramètres avec leurs valeurs par défaut
        $parametres = [];
        foreach ($parametresParDefaut as $cle => $valeur) {
            $parametre = Parametre::firstOrCreate(
                ['cle' => $cle],
                [
                    'valeur' => $valeur,
                    'type' => 'texte',
                    'description' => 'Paramètre système: ' . $cle
                ]
            );
            $parametres[$cle] = $parametre->valeur;
        }
        
        return Inertia::render('Admin/Parametres/Index', [
            'parametres' => $parametres
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nom_ecole' => 'required|string|max:255',
            'email_contact' => 'required|email|max:255',
            'telephone_contact' => 'required|string|max:20',
            'adresse' => 'required|string|max:500',
        ]);

        foreach ($validated as $cle => $valeur) {
            Parametre::updateOrCreate(
                ['cle' => $cle],
                [
                    'valeur' => $valeur, 
                    'type' => 'texte',
                    'description' => 'Paramètre système: ' . $cle
                ]
            );
        }

        return back()->with('success', 'Paramètres mis à jour avec succès.');
    }
}
