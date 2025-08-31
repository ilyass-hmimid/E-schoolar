<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Centre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CentresExport;
use App\Imports\CentresImport;

class CentreController extends Controller
{
    /**
     * Afficher la liste des centres.
     */
    public function index(Request $request)
    {
        $query = Centre::withCount(['users', 'classes'])
            ->with('responsable')
            ->latest();
            
        // Recherche par nom ou email
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('ville', 'like', "%{$search}%");
            });
        }
        
        $centres = $query->paginate(10);
        
        return view('admin.centres.index', compact('centres'));
    }

    /**
     * Afficher le formulaire de création d'un centre.
     */
    public function create()
    {
        $responsables = User::role('admin')
            ->select('id', 'name')
            ->get();
            
        return view('admin.centres.create', compact('responsables'));
    }

    /**
     * Enregistrer un nouveau centre.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:centres',
            'adresse' => 'required|string|max:500',
            'ville' => 'required|string|max:100',
            'pays' => 'required|string|max:100',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:centres,email',
            'responsable_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Gestion du téléchargement du logo
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/centres/logos');
                $validated['logo'] = str_replace('public/', '', $path);
            }
            
            $validated['is_active'] = $request->has('is_active');
            
            $centre = Centre::create($validated);
            
            return redirect()
                ->route('admin.centres.show', $centre)
                ->with('success', 'Centre créé avec succès');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du centre', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du centre');
        }
    }

    /**
     * Afficher les détails d'un centre.
     */
    public function show(Centre $centre)
    {
        $centre->load([
            'classes' => function($query) {
                $query->withCount('eleves')
                      ->with('responsable')
                      ->latest();
            },
            'users' => function($query) {
                $query->role(['professeur', 'eleve', 'assistant'])
                      ->with('roles')
                      ->latest()
                      ->limit(5);
            },
            'responsable',
            'enseignants' => function($query) {
                $query->withCount('classes')
                      ->latest();
            },
            'eleves' => function($query) {
                $query->with('classe')
                      ->latest()
                      ->limit(10);
            }
        ]);
        
        // Ajouter les compteurs pour la vue
        $centre->classes_count = $centre->classes->count();
        $centre->enseignants_count = $centre->enseignants->count();
        $centre->eleves_count = $centre->eleves->count();
        
        return view('admin.centres.show', compact('centre'));
    }

    /**
     * Afficher le formulaire d'édition d'un centre.
     */
    public function edit(Centre $centre)
    {
        $responsables = User::role('admin')
            ->select('id', 'name')
            ->get();
            
        return view('admin.centres.edit', compact('centre', 'responsables'));
    }

    /**
     * Mettre à jour un centre.
     */
    public function update(Request $request, Centre $centre)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:centres,nom,' . $centre->id,
            'adresse' => 'required|string|max:500',
            'ville' => 'required|string|max:100',
            'pays' => 'required|string|max:100',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:centres,email,' . $centre->id,
            'responsable_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'remove_logo' => 'nullable|boolean',
        ]);

        try {
            // Gestion de la suppression du logo
            if ($request->has('remove_logo') && $centre->logo) {
                Storage::delete('public/' . $centre->logo);
                $validated['logo'] = null;
            }
            
            // Gestion du téléchargement du nouveau logo
            if ($request->hasFile('logo')) {
                // Supprimer l'ancien logo s'il existe
                if ($centre->logo) {
                    Storage::delete('public/' . $centre->logo);
                }
                
                $path = $request->file('logo')->store('public/centres/logos');
                $validated['logo'] = str_replace('public/', '', $path);
            }
            
            $validated['is_active'] = $request->has('is_active');
            
            $centre->update($validated);
            
            return redirect()
                ->route('admin.centres.show', $centre)
                ->with('success', 'Centre mis à jour avec succès');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du centre', [
                'centre_id' => $centre->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du centre');
        }
    }

    /**
     * Supprimer un centre.
     */
    public function destroy(Centre $centre)
    {
        try {
            // Vérifier s'il y a des utilisateurs ou des classes associés
            if ($centre->users()->exists() || $centre->classes()->exists()) {
                return back()
                    ->with('error', 'Impossible de supprimer ce centre car il est associé à des utilisateurs ou des classes');
            }
            
            // Supprimer le logo s'il existe
            if ($centre->logo) {
                Storage::delete('public/' . $centre->logo);
            }
            
            $centre->delete();
            
            return redirect()
                ->route('admin.centres.index')
                ->with('success', 'Centre supprimé avec succès');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du centre', [
                'centre_id' => $centre->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression du centre');
        }
    }
    
    /**
     * Activer/Désactiver un centre.
     */
    public function toggleStatus(Centre $centre)
    {
        try {
            $centre->update(['is_active' => !$centre->is_active]);
            
            $status = $centre->is_active ? 'activé' : 'désactivé';
            
            return back()
                ->with('success', "Le centre a été $status avec succès");
                
        } catch (\Exception $e) {
            Log::error('Erreur lors du changement de statut du centre', [
                'centre_id' => $centre->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->with('error', 'Une erreur est survenue lors du changement de statut du centre');
        }
    }
    
    /**
     * Exporter les centres au format Excel.
     */
    public function export()
    {
        try {
            return Excel::download(new CentresExport, 'centres-' . now()->format('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'exportation des centres', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de l\'exportation des centres');
        }
    }
    
    /**
     * Importer des centres depuis un fichier Excel.
     */
    public function import(Request $request)
    {
        // Valider le fichier
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ], [
            'file.required' => 'Veuillez sélectionner un fichier à importer',
            'file.mimes' => 'Le fichier doit être de type :values',
            'file.max' => 'La taille du fichier ne doit pas dépasser 10 Mo'
        ]);
        
        try {
            $import = new CentresImport();
            Excel::import($import, $request->file('file'));
            
            $importedCount = $import->getRowCount();
            $skippedCount = $import->getSkippedCount();
            
            $message = "Import terminé avec succès. ";
            $message .= "$importedCount enregistrement(s) importé(s). ";
            
            if ($skippedCount > 0) {
                $message .= "$skippedCount enregistrement(s) ignoré(s) car ils existent déjà ou contiennent des erreurs.";
            }
            
            if (!empty($import->getErrors())) {
                return back()
                    ->with('warning', $message)
                    ->with('import_errors', $import->getErrors());
            }
            
            return back()
                ->with('success', $message);
                
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            
            foreach ($failures as $failure) {
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values()
                ];
            }
            
            return back()
                ->with('error', 'Des erreurs de validation ont été trouvées dans le fichier')
                ->with('import_errors', $errors);
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'importation des centres', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->with('error', 'Une erreur est survenue lors de l\'importation: ' . $e->getMessage())
                ->withInput();
        }
    }
}
