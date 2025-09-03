<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EleveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Eleve::with(['classe', 'classe.niveau']);
            
            // Search functionality
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenom', 'like', "%{$search}%")
                      ->orWhere('cni', 'like', "%{$search}%")
                      ->orWhere('cne', 'like', "%{$search}%");
                });
            }
            
            // Filter by class
            if ($request->has('classe_id')) {
                $query->where('classe_id', $request->input('classe_id'));
            }
            
            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }
            
            $eleves = $query->latest()->paginate(20);
            $classes = Classe::active()->with('niveau')->get();
            
            // Debug: Check if view exists
            if (!view()->exists('admin.eleves.index')) {
                \Log::error('View not found: admin.eleves.index');
                return redirect()->route('admin.dashboard')->with('error', 'La vue des élèves est introuvable.');
            }
            
            return view('admin.eleves.index', compact('eleves', 'classes'));
            
        } catch (\Exception $e) {
            \Log::error('Error in EleveController@index: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Une erreur est survenue lors du chargement de la liste des élèves.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = Classe::active()
            ->with('niveau')
            ->get()
            ->groupBy('niveau.nom');
            
        return view('admin.eleves.create', compact('classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cni' => ['nullable', 'string', 'max:20', 'unique:eleves,cni'],
            'cne' => ['required', 'string', 'max:20', 'unique:eleves,cne'],
            'nom' => ['required', 'string', 'max:50'],
            'prenom' => ['required', 'string', 'max:50'],
            'date_naissance' => ['required', 'date'],
            'lieu_naissance' => ['required', 'string', 'max:100'],
            'adresse' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'sexe' => ['required', 'in:Homme,Femme'],
            'classe_id' => ['required', 'exists:classes,id'],
            'date_inscription' => ['required', 'date'],
            'nom_pere' => ['required', 'string', 'max:100'],
            'profession_pere' => ['nullable', 'string', 'max:100'],
            'telephone_pere' => ['nullable', 'string', 'max:20'],
            'nom_mere' => ['required', 'string', 'max:100'],
            'profession_mere' => ['nullable', 'string', 'max:100'],
            'telephone_mere' => ['nullable', 'string', 'max:20'],
            'adresse_parents' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:actif,inactif,abandonne,diplome'],
            'remarques' => ['nullable', 'string'],
        ]);
        
        try {
            DB::beginTransaction();
            
            // Generate a unique code for the student
            $code = 'ELV-' . strtoupper(Str::random(8));
            while (Eleve::where('code', $code)->exists()) {
                $code = 'ELV-' . strtoupper(Str::random(8));
            }
            
            $validated['code'] = $code;
            $validated['created_by'] = auth()->id();
            
            $eleve = Eleve::create($validated);
            
            DB::commit();
            
            return redirect()
                ->route('admin.eleves.show', $eleve->id)
                ->with('success', 'Élève ajouté avec succès');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de l\'élève: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Eleve $eleve)
    {
        $eleve->load([
            'classe',
            'classe.niveau',
            'absences' => function($query) {
                $query->latest()->take(5);
            },
            'paiements' => function($query) {
                $query->latest()->take(5);
            }
        ]);
        
        return view('admin.eleves.show', compact('eleve'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Eleve $eleve)
    {
        $classes = Classe::active()
            ->with('niveau')
            ->get()
            ->groupBy('niveau.nom');
            
        return view('admin.eleves.edit', compact('eleve', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Eleve $eleve)
    {
        $validated = $request->validate([
            'cni' => ['nullable', 'string', 'max:20', Rule::unique('eleves')->ignore($eleve->id)],
            'cne' => ['required', 'string', 'max:20', Rule::unique('eleves')->ignore($eleve->id)],
            'nom' => ['required', 'string', 'max:50'],
            'prenom' => ['required', 'string', 'max:50'],
            'date_naissance' => ['required', 'date'],
            'lieu_naissance' => ['required', 'string', 'max:100'],
            'adresse' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'sexe' => ['required', 'in:Homme,Femme'],
            'classe_id' => ['required', 'exists:classes,id'],
            'date_inscription' => ['required', 'date'],
            'nom_pere' => ['required', 'string', 'max:100'],
            'profession_pere' => ['nullable', 'string', 'max:100'],
            'telephone_pere' => ['nullable', 'string', 'max:20'],
            'nom_mere' => ['required', 'string', 'max:100'],
            'profession_mere' => ['nullable', 'string', 'max:100'],
            'telephone_mere' => ['nullable', 'string', 'max:20'],
            'adresse_parents' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:actif,inactif,abandonne,diplome'],
            'remarques' => ['nullable', 'string'],
        ]);
        
        try {
            $eleve->update($validated);
            
            return redirect()
                ->route('admin.eleves.show', $eleve->id)
                ->with('success', 'Informations de l\'élève mises à jour avec succès');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Eleve $eleve)
    {
        try {
            $eleve->delete();
            
            return redirect()
                ->route('admin.eleves.index')
                ->with('success', 'Élève supprimé avec succès');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression: ' . $e->getMessage());
        }
    }
    
    /**
     * Get students by class for AJAX requests
     */
    public function getByClasse(Classe $classe)
    {
        $eleves = $classe->eleves()
            ->select('id', 'nom', 'prenom', 'cne')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
            
        return response()->json($eleves);
    }
}
