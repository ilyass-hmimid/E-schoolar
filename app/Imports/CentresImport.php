<?php

namespace App\Imports;

use App\Models\Centre;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class CentresImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation,
    SkipsOnError,
    SkipsOnFailure,
    WithChunkReading
{
    use SkipsErrors, SkipsFailures;
    
    /**
     * Nombre de lignes importées avec succès
     *
     * @var int
     */
    protected $importedCount = 0;
    
    /**
     * Nombre de lignes ignorées (doublons)
     *
     * @var int
     */
    protected $skippedCount = 0;
    
    /**
     * Liste des erreurs d'importation
     *
     * @var array
     */
    protected $errors = [];
    
    /**
     * Numéro de ligne actuel
     *
     * @var int
     */
    protected $currentRow = 0;
    
    /**
     * Incrémente le numéro de ligne actuel
     *
     * @return int
     */
    protected function getRowNumber(): int
    {
        return $this->currentRow;
    }
    
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->currentRow++;
        // Vérifier si le centre existe déjà par email
        $centre = Centre::where('email', $row['email'] ?? null)->first();
        
        // Si le centre existe, le mettre à jour
        if ($centre) {
            $centre->update([
                'nom' => $row['nom'] ?? $centre->nom,
                'adresse' => $row['adresse'] ?? $centre->adresse,
                'ville' => $row['ville'] ?? $centre->ville,
                'pays' => $row['pays'] ?? $centre->pays ?? 'Maroc',
                'telephone' => $row['telephone'] ?? $centre->telephone,
                'is_active' => isset($row['statut']) ? (strtolower($row['statut']) === 'actif') : $centre->is_active,
            ]);
            
            $this->skippedCount++;
            
            // Ajouter un message d'avertissement pour les mises à jour
            $this->errors[] = [
                'row' => $this->getRowNumber(),
                'message' => "Le centre avec l'email {$row['email']} existe déjà et a été mis à jour",
                'type' => 'warning'
            ];
            
            return null;
        }
        
        // Créer un nouveau centre
        $this->importedCount++;
        
        return new Centre([
            'nom' => $row['nom'],
            'adresse' => $row['adresse'],
            'ville' => $row['ville'],
            'pays' => $row['pays'] ?? 'Maroc',
            'telephone' => $row['telephone'],
            'email' => $row['email'],
            'is_active' => isset($row['statut']) ? (strtolower($row['statut']) === 'actif') : true,
            'description' => $row['description'] ?? null,
        ]);
    }
    
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            '*.nom' => 'required|string|max:255',
            '*.adresse' => 'required|string|max:500',
            '*.ville' => 'required|string|max:100',
            '*.pays' => 'nullable|string|max:100',
            '*.telephone' => 'required|string|max:20',
            '*.email' => 'required|email|unique:centres,email',
            '*.statut' => 'nullable|string|in:Actif,Inactif,actif,inactif',
            '*.description' => 'nullable|string',
        ];
    }
    
    /**
     * Messages de validation personnalisés
     */
    public function customValidationMessages()
    {
        return [
            'nom.required' => 'Le champ nom est obligatoire',
            'adresse.required' => 'Le champ adresse est obligatoire',
            'ville.required' => 'Le champ ville est obligatoire',
            'telephone.required' => 'Le champ téléphone est obligatoire',
            'email.required' => 'Le champ email est obligatoire',
            'email.email' => 'L\'adresse email n\'est pas valide',
            'email.unique' => 'Un centre avec cette adresse email existe déjà',
        ];
    }
    
    /**
     * Traitement par lots pour améliorer les performances
     */
    public function chunkSize(): int
    {
        return 1000;
    }
    
    /**
     * Incrémente le compteur de lignes importées
     */
    public function incrementImportedCount()
    {
        $this->importedCount++;
    }
    
    /**
     * Récupère le nombre de lignes importées
     *
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->importedCount;
    }
    
    /**
     * Récupère le nombre de lignes ignorées
     *
     * @return int
     */
    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
    
    /**
     * Récupère la liste des erreurs
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Gestion des échecs de validation
     *
     * @param \Maatwebsite\Excel\Validators\Failure ...$failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
                'type' => 'error'
            ];
        }
        
        $this->skippedCount++;
    }
}
