<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Matiere;
use Illuminate\Http\Request;

class EleveMatiereController extends Controller
{
    /**
     * Affiche le formulaire d'édition des matières d'un élève
     */
    public function edit(Eleve $eleve)
    {
        $eleve->load('matieres');
        $matieres = Matiere::all();
        
        return view('eleves.matieres.edit', [
            'eleve' => $eleve,
            'matieres' => $matieres,
            'matieresSelectionnees' => $eleve->matieres->pluck('id')->toArray()
        ]);
    }

    /**
     * Met à jour les matières d'un élève
     */
    public function update(Request $request, Eleve $eleve)
    {
        $request->validate([
            'matieres' => 'required|array',
            'matieres.*' => 'exists:matieres,id',
        ]);

        $eleve->matieres()->sync($request->matieres);

        return redirect()->route('eleves.index')
            ->with('success', 'Les matières de l\'élève ont été mises à jour avec succès.');
    }
}
