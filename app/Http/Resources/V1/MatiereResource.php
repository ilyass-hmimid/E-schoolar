<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatiereResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'couleur' => $this->couleur,
            'est_obligatoire' => (bool) $this->est_obligatoire,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
            
            // Données de la relation pivot (si disponible)
            'pivot' => $this->whenPivotLoaded('matiere_pack', function () {
                return [
                    'nombre_heures_par_matiere' => (int) $this->pivot->nombre_heures_par_matiere,
                ];
            }),
        ];
    }
}
