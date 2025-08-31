<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\Devoir;
use App\Models\DevoirRendu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DevoirController extends Controller
{
    /**
     * Affiche la liste des devoirs de l'élève
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $aujourdhui = Carbon::today();
        
        // Récupérer les devoirs à rendre
        $devoirs = Devoir::whereHas('classe', function($query) use ($user) {
                $query->whereHas('eleves', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            })
            ->with(['matiere', 'classe', 'devoirRendu' => function($query) use ($user) {
                $query->where('eleve_id', $user->id);
            }])
            ->where('date_limite', '>=', $aujourdhui)
            ->orderBy('date_limite')
            ->paginate(10);
            
        // Récupérer les devoirs rendus
        $devoirsRendus = DevoirRendu::where('eleve_id', $user->id)
            ->with(['devoir.matiere', 'devoir.classe'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('eleve.devoirs.index', [
            'devoirs' => $devoirs,
            'devoirsRendus' => $devoirsRendus,
            'aujourdhui' => $aujourdhui
        ]);
    }

    /**
     * Affiche les détails d'un devoir
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $devoir = Devoir::with(['matiere', 'classe', 'piecesJointes'])
            ->whereHas('classe.eleves', function($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->findOrFail($id);
            
        $devoirRendu = DevoirRendu::where('devoir_id', $id)
            ->where('eleve_id', $user->id)
            ->first();
            
        return view('eleve.devoirs.show', [
            'devoir' => $devoir,
            'devoirRendu' => $devoirRendu
        ]);
    }

    /**
     * Affiche le formulaire de rendu de devoir
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showRendreForm($id)
    {
        $user = Auth::user();
        
        $devoir = Devoir::with(['matiere', 'classe', 'piecesJointes'])
            ->whereHas('classe.eleves', function($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->findOrFail($id);
            
        // Vérifier si le devoir n'est pas déjà rendu
        $dejaRendu = DevoirRendu::where('devoir_id', $id)
            ->where('eleve_id', $user->id)
            ->exists();
            
        if ($dejaRendu) {
            return redirect()->route('eleve.devoirs.show', $id)
                ->with('error', 'Vous avez déjà rendu ce devoir.');
        }
        
        // Vérifier que la date limite n'est pas dépassée
        if (Carbon::now()->gt($devoir->date_limite)) {
            return redirect()->route('eleve.devoirs.show', $id)
                ->with('error', 'La date limite de rendu est dépassée.');
        }
        
        return view('eleve.devoirs.rendre', [
            'devoir' => $devoir
        ]);
    }

    /**
     * Traite le rendu d'un devoir
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rendreDevoir(Request $request, $id)
    {
        $user = Auth::user();
        
        $devoir = Devoir::findOrFail($id);
        
        // Vérifier que l'élève est bien dans la classe du devoir
        if (!$devoir->classe->eleves->contains($user->id)) {
            abort(403, 'Accès non autorisé à ce devoir.');
        }
        
        // Vérifier que le devoir n'est pas déjà rendu
        $dejaRendu = DevoirRendu::where('devoir_id', $id)
            ->where('eleve_id', $user->id)
            ->exists();
            
        if ($dejaRendu) {
            return redirect()->route('eleve.devoirs.show', $id)
                ->with('error', 'Vous avez déjà rendu ce devoir.');
        }
        
        // Vérifier que la date limite n'est pas dépassée
        if (Carbon::now()->gt($devoir->date_limite)) {
            return redirect()->route('eleve.devoirs.show', $id)
                ->with('error', 'La date limite de rendu est dépassée.');
        }
        
        $request->validate([
            'contenu' => 'required|string',
            'fichier' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        $cheminFichier = null;
        if ($request->hasFile('fichier')) {
            $cheminFichier = $request->file('fichier')->store('devoirs/rendus', 'public');
        }
        
        DevoirRendu::create([
            'devoir_id' => $devoir->id,
            'eleve_id' => $user->id,
            'contenu' => $request->contenu,
            'fichier' => $cheminFichier,
            'date_remise' => now(),
            'est_en_retard' => now()->gt($devoir->date_limite),
        ]);
        
        return redirect()->route('eleve.devoirs.show', $id)
            ->with('success', 'Votre devoir a bien été enregistré.');
    }
}
