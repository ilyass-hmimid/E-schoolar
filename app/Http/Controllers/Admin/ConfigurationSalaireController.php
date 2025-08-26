<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigurationSalaire;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConfigurationSalaireController extends Controller
{
    /**
     * Affiche la liste des configurations de salaires
     */
    public function index(Request $request)
    {
        $configurations = ConfigurationSalaire::with('matiere')
            ->orderBy('matiere_id')
            ->paginate(15);

        // Récupérer les matières qui n'ont pas encore de configuration
        $matieresSansConfig = Matiere::whereNotIn('id', function($query) {
            $query->select('matiere_id')->from('configuration_salaires');
        })->get(['id', 'nom']);

        return Inertia::render('Admin/Salaires/Configuration', [
            'configurations' => $configurations,
            'matieresSansConfig' => $matieresSansConfig,
            'allMatieres' => Matiere::orderBy('nom')->get(['id', 'nom'])
        ]);
    }

    /**
     * Stocke une nouvelle configuration de salaire
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'matiere_id' => 'required|exists:matieres,id|unique:configuration_salaires,matiere_id',
            'prix_unitaire' => 'required|numeric|min:0',
            'commission_prof' => 'required|numeric|min:0|max:100',
            'prix_min' => 'nullable|numeric|min:0',
            'prix_max' => 'nullable|numeric|min:0|gte:prix_min',
            'description' => 'nullable|string|max:1000',
            'est_actif' => 'boolean'
        ]);

        $configuration = ConfigurationSalaire::create($validated);

        return redirect()->route('admin.configuration-salaires.index')
            ->with('success', 'Configuration enregistrée avec succès.');
    }

    /**
     * Met à jour une configuration de salaire existante
     */
    public function update(Request $request, $id)
    {
        $configuration = ConfigurationSalaire::findOrFail($id);

        $validated = $request->validate([
            'prix_unitaire' => 'required|numeric|min:0',
            'commission_prof' => 'required|numeric|min:0|max:100',
            'prix_min' => 'nullable|numeric|min:0',
            'prix_max' => 'nullable|numeric|min:0' . ($request->prix_min ? '|gte:prix_min' : ''),
            'description' => 'nullable|string|max:1000',
            'est_actif' => 'boolean'
        ]);

        $configuration->update($validated);

        return redirect()->route('admin.configuration-salaires.index')
            ->with('success', 'Configuration mise à jour avec succès.');
    }

    /**
     * Supprime une configuration de salaire
     */
    public function destroy($id)
    {
        $configuration = ConfigurationSalaire::findOrFail($id);
        $configuration->delete();

        return redirect()->route('admin.configuration-salaires.index')
            ->with('success', 'Configuration supprimée avec succès.');
    }

    /**
     * Récupère la configuration pour une matière donnée
     */
    public function getByMatiere($matiereId)
    {
        $configuration = ConfigurationSalaire::where('matiere_id', $matiereId)
            ->where('est_actif', true)
            ->first();

        if (!$configuration) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune configuration active trouvée pour cette matière.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $configuration
        ]);
    }
}
