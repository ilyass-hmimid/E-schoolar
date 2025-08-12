<?php

namespace App\Http\Controllers;

use App\Models\Pack;
use App\Models\Matiere;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PackController extends Controller
{
    /**
     * Affiche la liste des packs
     */
    public function index()
    {
        $this->authorize('viewAny', Pack::class);
        
        $packs = Pack::withCount('matieres')
            ->latest()
            ->get()
            ->map(function ($pack) {
                return [
                    'id' => $pack->id,
                    'nom' => $pack->nom,
                    'description' => $pack->description,
                    'nombre_heures' => $pack->nombre_heures,
                    'prix' => $pack->prix,
                    'prix_formate' => $pack->prix_formate,
                    'est_actif' => $pack->est_actif,
                    'nb_matieres' => $pack->matieres_count,
                    'created_at' => $pack->created_at->format('d/m/Y H:i'),
                    'updated_at' => $pack->updated_at->format('d/m/Y H:i'),
                ];
            });

        return Inertia::render('Packs/Index', [
            'packs' => $packs,
            'can' => [
                'create' => auth()->user()->can('create', Pack::class),
                'edit' => auth()->user()->can('update', Pack::class),
                'delete' => auth()->user()->can('delete', Pack::class),
            ]
        ]);
    }

    /**
     * Affiche le formulaire de création d'un pack
     */
    public function create()
    {
        $this->authorize('create', Pack::class);
        
        return Inertia::render('Packs/Create', [
            'matieres' => Matiere::select('id', 'Libelle')
                ->orderBy('Libelle')
                ->get()
                ->map(function($matiere) {
                    return [
                        'id' => $matiere->id,
                        'libelle' => $matiere->Libelle,
                    ];
                }),
            'niveaux' => Niveau::select('id', 'Libelle')
                ->orderBy('Libelle')
                ->get()
                ->map(function($niveau) {
                    return [
                        'id' => $niveau->id,
                        'libelle' => $niveau->Libelle,
                    ];
                }),
        ]);
    }

    /**
     * Enregistre un nouveau pack
     */
    public function store(Request $request)
    {
        $this->authorize('create', Pack::class);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'description' => 'nullable|string',
            'nombre_heures' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0',
            'est_actif' => 'boolean',
            'matieres' => 'required|array|min:1',
            'matieres.*.id' => 'required|exists:Matiere,id',
            'matieres.*.heures' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {
            // Création du pack
            $pack = Pack::create([
                'nom' => $validated['nom'],
                'description' => $validated['description'] ?? null,
                'nombre_heures' => $validated['nombre_heures'],
                'prix' => $validated['prix'],
                'est_actif' => $validated['est_actif'] ?? true,
            ]);

            // Ajout des matières avec le nombre d'heures
            $matieresAvecHeures = [];
            foreach ($validated['matieres'] as $matiere) {
                $matieresAvecHeures[$matiere['id']] = [
                    'nombre_heures_par_matiere' => $matiere['heures']
                ];
            }
            $pack->matieres()->sync($matieresAvecHeures);

            return redirect()->route('packs.show', $pack)
                ->with('success', 'Le pack a été créé avec succès.');
        });
    }

    /**
     * Affiche les détails d'un pack
     */
    public function show(Pack $pack)
    {
        $this->authorize('view', $pack);
        
        $pack->load(['matieres' => function($query) {
            $query->select('id', 'Libelle');
        }]);

        return Inertia::render('Packs/Show', [
            'pack' => [
                'id' => $pack->id,
                'nom' => $pack->nom,
                'description' => $pack->description,
                'nombre_heures' => $pack->nombre_heures,
                'prix' => $pack->prix,
                'prix_formate' => $pack->prix_formate,
                'est_actif' => $pack->est_actif,
                'matieres' => $pack->matieres->map(function($matiere) {
                    return [
                        'id' => $matiere->id,
                        'libelle' => $matiere->Libelle,
                        'heures' => $matiere->pivot->nombre_heures_par_matiere,
                    ];
                }),
                'created_at' => $pack->created_at->format('d/m/Y H:i'),
                'updated_at' => $pack->updated_at->format('d/m/Y H:i'),
            ]
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'un pack
     */
    public function edit(Pack $pack)
    {
        $this->authorize('update', $pack);
        
        $pack->load('matieres');
        
        return Inertia::render('Packs/Edit', [
            'pack' => [
                'id' => $pack->id,
                'nom' => $pack->nom,
                'description' => $pack->description,
                'nombre_heures' => $pack->nombre_heures,
                'prix' => $pack->prix,
                'est_actif' => $pack->est_actif,
                'matieres' => $pack->matieres->map(function($matiere) {
                    return [
                        'id' => $matiere->id,
                        'libelle' => $matiere->Libelle,
                        'heures' => $matiere->pivot->nombre_heures_par_matiere,
                    ];
                }),
            ],
            'matieres' => Matiere::select('id', 'Libelle')
                ->orderBy('Libelle')
                ->get()
                ->map(function($matiere) {
                    return [
                        'id' => $matiere->id,
                        'libelle' => $matiere->Libelle,
                    ];
                }),
        ]);
    }

    /**
     * Met à jour un pack existant
     */
    public function update(Request $request, Pack $pack)
    {
        $this->authorize('update', $pack);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'description' => 'nullable|string',
            'nombre_heures' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0',
            'est_actif' => 'boolean',
            'matieres' => 'required|array|min:1',
            'matieres.*.id' => 'required|exists:Matiere,id',
            'matieres.*.heures' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($pack, $validated) {
            // Mise à jour du pack
            $pack->update([
                'nom' => $validated['nom'],
                'description' => $validated['description'] ?? null,
                'nombre_heures' => $validated['nombre_heures'],
                'prix' => $validated['prix'],
                'est_actif' => $validated['est_actif'] ?? true,
            ]);

            // Mise à jour des matières avec le nombre d'heures
            $matieresAvecHeures = [];
            foreach ($validated['matieres'] as $matiere) {
                $matieresAvecHeures[$matiere['id']] = [
                    'nombre_heures_par_matiere' => $matiere['heures']
                ];
            }
            $pack->matieres()->sync($matieresAvecHeures);

            return redirect()->route('packs.show', $pack)
                ->with('success', 'Le pack a été mis à jour avec succès.');
        });
    }

    /**
     * Supprime un pack
     */
    public function destroy(Pack $pack)
    {
        $this->authorize('delete', $pack);
        
        // Vérifier si le pack est utilisé dans des inscriptions
        if ($pack->inscriptions()->exists()) {
            return redirect()->back()
                ->with('error', 'Ce pack ne peut pas être supprimé car il est utilisé dans des inscriptions.');
        }
        
        $pack->delete();
        
        return redirect()->route('packs.index')
            ->with('success', 'Le pack a été supprimé avec succès.');
    }
    
    /**
     * Active ou désactive un pack
     */
    public function toggleStatus(Pack $pack)
    {
        $this->authorize('update', $pack);
        
        $pack->update([
            'est_actif' => !$pack->est_actif
        ]);
        
        $status = $pack->est_actif ? 'activé' : 'désactivé';
        
        return redirect()->back()
            ->with('success', "Le pack a été $status avec succès.");
    }
    
    /**
     * Récupère la liste des packs actifs pour le formulaire d'inscription
     */
    public function getPacksActifs()
    {
        return response()->json(
            Pack::actif()
                ->select('id', 'nom', 'nombre_heures', 'prix')
                ->with(['matieres' => function($query) {
                    $query->select('id', 'Libelle');
                }])
                ->get()
                ->map(function($pack) {
                    return [
                        'id' => $pack->id,
                        'nom' => $pack->nom,
                        'nombre_heures' => $pack->nombre_heures,
                        'prix' => $pack->prix,
                        'prix_formate' => $pack->prix_formate,
                        'matieres' => $pack->matieres->map(function($matiere) {
                            return [
                                'id' => $matiere->id,
                                'libelle' => $matiere->Libelle,
                            ];
                        }),
                    ];
                })
        );
    }
}
