<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClasseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = Classe::withCount('eleves')
            ->with('professeurPrincipal')
            ->latest()
            ->paginate(10);
            
        return view('admin.classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $professeurs = User::role('professeur')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
            
        return view('admin.classes.create', compact('professeurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100|unique:classes,nom',
            'niveau' => ['required', 'string', Rule::in(['6ème', '5ème', '4ème', '3ème', '2nde', '1ère', 'Tle'])],
            'description' => 'nullable|string|max:500',
            'professeur_principal_id' => 'nullable|exists:users,id'
        ]);

        try {
            DB::beginTransaction();
            
            $classe = Classe::create($validated);
            
            DB::commit();
            
            return redirect()
                ->route('admin.classes.index')
                ->with('success', 'La classe a été créée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la classe.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Classe $classe)
    {
        $classe->load(['professeurPrincipal', 'eleves' => function($query) {
            $query->orderBy('nom')->orderBy('prenom');
        }]);
        
        return view('admin.classes.show', compact('classe'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classe $classe)
    {
        $professeurs = User::role('professeur')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
            
        return view('admin.classes.edit', compact('classe', 'professeurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classe $classe)
    {
        $validated = $request->validate([
            'nom' => [
                'required',
                'string',
                'max:100',
                Rule::unique('classes', 'nom')->ignore($classe->id)
            ],
            'niveau' => ['required', 'string', Rule::in(['6ème', '5ème', '4ème', '3ème', '2nde', '1ère', 'Tle'])],
            'description' => 'nullable|string|max:500',
            'professeur_principal_id' => 'nullable|exists:users,id'
        ]);

        try {
            DB::beginTransaction();
            
            $classe->update($validated);
            
            DB::commit();
            
            return redirect()
                ->route('admin.classes.index')
                ->with('success', 'La classe a été mise à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de la classe.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classe $classe)
    {
        try {
            DB::beginTransaction();
            
            // Vérifier s'il y a des élèves dans la classe avant de supprimer
            if ($classe->eleves()->count() > 0) {
                return back()
                    ->with('error', 'Impossible de supprimer cette classe car elle contient des élèves.');
            }
            
            $classe->delete();
            
            DB::commit();
            
            return redirect()
                ->route('admin.classes.index')
                ->with('success', 'La classe a été supprimée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression de la classe.');
        }
    }
}
