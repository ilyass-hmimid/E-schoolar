<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CoursController extends Controller
{
    /**
     * Affiche la liste des cours de l'élève
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $aujourdhui = Carbon::today();
        $finDeSemaine = $aujourdhui->copy()->addWeek();
        
        // Récupérer les prochaines présences (7 prochains jours)
        $cours = $user->presences()
            ->with(['matiere', 'classe', 'professeur'])
            ->where('date_seance', '>=', $aujourdhui)
            ->orderBy('date_seance')
            ->orderBy('heure_debut')
            ->paginate(10);
            
        return view('eleve.cours.index', compact('cours'));
    }

    /**
     * Affiche les détails d'un cours
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $cours = Auth::user()->presences()
            ->with(['matiere', 'classe', 'professeur'])
            ->findOrFail($id);
            
        return view('eleve.cours.show', compact('cours'));
    }
}
