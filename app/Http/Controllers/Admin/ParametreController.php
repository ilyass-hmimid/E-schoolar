<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    public function index()
    {
        // Récupérer les paramètres existants
        $parametres = [
            'nom_etablissement' => Parametre::where('cle', 'nom_etablissement')->value('valeur') ?? 'Allo Tawjih',
            'adresse' => Parametre::where('cle', 'adresse')->value('valeur') ?? 'Adresse de l\'école, Ville, Maroc',
            'telephone' => Parametre::where('cle', 'telephone')->value('valeur') ?? '+212 6 00 00 00 00',
            'email_contact' => Parametre::where('cle', 'email_contact')->value('valeur') ?? 'contact@allotawjih.ma',
            'annee_scolaire' => Parametre::where('cle', 'annee_scolaire')->value('valeur') ?? date('Y') . '-' . (date('Y') + 1),
        ];
        
        return view('admin.parametres.index', compact('parametres'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nom_etablissement' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email_contact' => 'required|email|max:255',
            'annee_scolaire' => 'required|string|max:9',
        ]);

        foreach ($validated as $cle => $valeur) {
            Parametre::updateOrCreate(
                ['cle' => $cle],
                ['valeur' => $valeur, 'type' => 'texte', 'description' => 'Paramètre système: ' . $cle]
            );
        }

        return redirect()->back()->with('success', 'Paramètres mis à jour avec succès.');
    }
}
