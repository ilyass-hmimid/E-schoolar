<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\PackHelper;

class PackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $typeConfig = config("packs.types.{$this->type}", [
            'name' => ucfirst($this->type),
            'icon' => 'cube',
            'color' => 'gray',
        ]);

        $discountPercentage = $this->prix_promo 
            ? PackHelper::calculateDiscountPercentage($this->prix, $this->prix_promo)
            : null;

        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'slug' => $this->slug,
            'description' => $this->description,
            'type' => [
                'code' => $this->type,
                'name' => $typeConfig['name'],
                'icon' => $typeConfig['icon'],
                'color' => $typeConfig['color'],
            ],
            'prix' => (float) $this->prix,
            'prix_formate' => PackHelper::formatPrice($this->prix),
            'prix_promo' => $this->prix_promo ? (float) $this->prix_promo : null,
            'prix_promo_formate' => $this->prix_promo ? PackHelper::formatPrice($this->prix_promo) : null,
            'prix_affichage' => PackHelper::getDisplayPrice($this),
            'prix_affichage_formate' => PackHelper::formatPrice(PackHelper::getDisplayPrice($this)),
            'remise_pourcentage' => $discountPercentage,
            'duree_jours' => (int) $this->duree_jours,
            'est_actif' => (bool) $this->est_actif,
            'est_populaire' => (bool) $this->est_populaire,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
            
            // Relations chargées
            'matieres' => MatiereResource::collection($this->whenLoaded('matieres')),
            'ventes_count' => $this->whenCounted('ventes', function() {
                return (int) $this->ventes_count;
            }),
            'inscriptions_count' => $this->whenCounted('inscriptions', function() {
                return (int) $this->inscriptions_count;
            }),
            
            // Liens
            'links' => [
                'self' => route('api.v1.packs.show', $this->id),
                'edit' => route('admin.packs.edit', $this->id),
                'show' => route('admin.packs.show', $this->id),
            ],
        ];
    }
}
