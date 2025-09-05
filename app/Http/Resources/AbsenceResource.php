<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AbsenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'etudiant_id' => $this->etudiant_id,
            'etudiant' => [
                'id' => $this->etudiant->id,
                'nom' => $this->etudiant->nom,
                'prenom' => $this->etudiant->prenom,
                'classe' => $this->etudiant->classe->nom ?? null,
            ],
            'matiere_id' => $this->matiere_id,
            'matiere' => $this->whenLoaded('matiere', function () {
                return [
                    'id' => $this->matiere->id,
                    'nom' => $this->matiere->nom,
                    'code' => $this->matiere->code,
                ];
            }),
            'cours_id' => $this->cours_id,
            'professeur_id' => $this->professeur_id,
            'professeur' => $this->whenLoaded('professeur', function () {
                return [
                    'id' => $this->professeur->id,
                    'nom' => $this->professeur->nom,
                    'prenom' => $this->professeur->prenom,
                ];
            }),
            'date_absence' => $this->date_absence->format('Y-m-d'),
            'heure_debut' => $this->heure_debut ? Carbon::parse($this->heure_debut)->format('H:i') : null,
            'heure_fin' => $this->heure_fin ? Carbon::parse($this->heure_fin)->format('H:i') : null,
            'type' => $this->type,
            'type_label' => $this->type ? Absence::TYPES[$this->type] ?? $this->type : null,
            'duree_retard' => $this->duree_retard,
            'justifiee' => $this->justifiee,
            'motif' => $this->motif,
            'justification' => $this->justification,
            'statut_justification' => $this->statut_justification,
            'statut_justification_label' => $this->statut_justification ? Absence::STATUTS_JUSTIFICATION[$this->statut_justification] ?? $this->statut_justification : null,
            'piece_jointe' => $this->piece_jointe ? asset('storage/' . $this->piece_jointe) : null,
            'date_justification' => $this->date_justification ? $this->date_justification->format('Y-m-d H:i:s') : null,
            'justified_by' => $this->justified_by,
            'validateur' => $this->whenLoaded('validateur', function () {
                return $this->validateur ? [
                    'id' => $this->validateur->id,
                    'nom' => $this->validateur->nom,
                    'prenom' => $this->validateur->prenom,
                ] : null;
            }),
            'notes' => $this->notes,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
