<?php

namespace App\Imports;

use App\Models\Absence;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Classe;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AbsencesImport implements ToCollection, WithHeadingRow, WithValidation
{
    private $errors = [];
    private $imported = 0;
    private $skipped = 0;
    
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                // Valider la ligne
                $validator = Validator::make($row->toArray(), [
                    'etudiant' => 'required|string|max:255',
                    'matiere' => 'required|string|max:255',
                    'date_absence' => 'required|date',
                    'heure_debut' => 'required|date_format:H:i',
                    'heure_fin' => 'required|date_format:H:i|after:heure_debut',
                    'type' => 'required|in:absence,retard',
                    'duree_retard' => 'nullable|integer|min:1',
                    'motif' => 'nullable|string|max:500',
                    'justifiee' => 'boolean',
                    'statut_justification' => 'nullable|in:en_attente,acceptee,refusee',
                ]);
                
                if ($validator->fails()) {
                    $this->errors[] = [
                        'row' => $index + 2, // +2 car l'en-tête est la ligne 1 et on commence à compter à partir de 0
                        'errors' => $validator->errors()->all()
                    ];
                    $this->skipped++;
                    continue;
                }
                
                // Trouver l'étudiant
                $etudiant = User::where('nom', 'like', '%' . $row['etudiant'] . '%')
                    ->orWhere('prenom', 'like', '%' . $row['etudiant'] . '%')
                    ->orWhere('email', $row['etudiant'])
                    ->first();
                
                if (!$etudiant) {
                    $this->errors[] = [
                        'row' => $index + 2,
                        'errors' => ['Étudiant non trouvé']
                    ];
                    $this->skipped++;
                    continue;
                }
                
                // Trouver la matière
                $matiere = Matiere::where('nom', 'like', '%' . $row['matiere'] . '%')
                    ->orWhere('code', 'like', '%' . $row['matiere'] . '%')
                    ->first();
                
                if (!$matiere) {
                    $this->errors[] = [
                        'row' => $index + 2,
                        'errors' => ['Matière non trouvée']
                    ];
                    $this->skipped++;
                    continue;
                }
                
                // Vérifier si l'absence existe déjà
                $existingAbsence = Absence::where('etudiant_id', $etudiant->id)
                    ->where('matiere_id', $matiere->id)
                    ->whereDate('date_absence', $row['date_absence'])
                    ->where('heure_debut', $row['heure_debut'])
                    ->first();
                
                if ($existingAbsence) {
                    $this->errors[] = [
                        'row' => $index + 2,
                        'errors' => ['Une absence existe déjà pour cet étudiant à cette date et heure']
                    ];
                    $this->skipped++;
                    continue;
                }
                
                // Créer l'absence
                Absence::create([
                    'etudiant_id' => $etudiant->id,
                    'matiere_id' => $matiere->id,
                    'classe_id' => $etudiant->classe_id,
                    'professeur_id' => $matiere->professeur_id ?? null,
                    'assistant_id' => auth()->id(),
                    'date_absence' => $row['date_absence'],
                    'heure_debut' => $row['heure_debut'],
                    'heure_fin' => $row['heure_fin'],
                    'type' => $row['type'],
                    'duree_retard' => $row['duree_retard'] ?? null,
                    'motif' => $row['motif'] ?? null,
                    'justifiee' => $row['justifiee'] ?? false,
                    'statut_justification' => $row['statut_justification'] ?? 'en_attente',
                    'saisie_par' => auth()->id(),
                ]);
                
                $this->imported++;
                
            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'errors' => [$e->getMessage()]
                ];
                $this->skipped++;
                continue;
            }
        }
    }
    
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'etudiant' => 'required|string|max:255',
            'matiere' => 'required|string|max:255',
            'date_absence' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type' => 'required|in:absence,retard',
            'duree_retard' => 'nullable|integer|min:1',
            'motif' => 'nullable|string|max:500',
            'justifiee' => 'boolean',
            'statut_justification' => 'nullable|in:en_attente,acceptee,refusee',
        ];
    }
    
    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'etudiant.required' => 'Le champ étudiant est obligatoire',
            'matiere.required' => 'Le champ matière est obligatoire',
            'date_absence.required' => 'La date d\'absence est obligatoire',
            'date_absence.date' => 'La date d\'absence doit être une date valide',
            'heure_debut.required' => 'L\'heure de début est obligatoire',
            'heure_debut.date_format' => 'L\'heure de début doit être au format HH:MM',
            'heure_fin.required' => 'L\'heure de fin est obligatoire',
            'heure_fin.date_format' => 'L\'heure de fin doit être au format HH:MM',
            'heure_fin.after' => 'L\'heure de fin doit être postérieure à l\'heure de début',
            'type.required' => 'Le type d\'absence est obligatoire',
            'type.in' => 'Le type doit être "absence" ou "retard"',
            'duree_retard.integer' => 'La durée du retard doit être un nombre entier',
            'duree_retard.min' => 'La durée du retard doit être supérieure à 0',
            'motif.max' => 'Le motif ne doit pas dépasser 500 caractères',
            'justifiee.boolean' => 'Le champ justifiée doit être vrai ou faux',
            'statut_justification.in' => 'Le statut de justification doit être "en_attente", "acceptee" ou "refusee"',
        ];
    }
    
    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * @return int
     */
    public function getImportedCount()
    {
        return $this->imported;
    }
    
    /**
     * @return int
     */
    public function getSkippedCount()
    {
        return $this->skipped;
    }
}
