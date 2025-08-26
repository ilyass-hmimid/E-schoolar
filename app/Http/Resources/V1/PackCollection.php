<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PackCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = PackResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->total(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'path' => $request->url(),
                'query' => $request->query(),
                'filters' => [
                    'search' => $request->input('search'),
                    'type' => $request->input('type'),
                    'status' => $request->input('status'),
                ],
                'sort' => [
                    'by' => $request->input('sort_by', 'created_at'),
                    'order' => $request->input('sort_order', 'desc'),
                ],
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\JsonResponse  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $jsonResponse = json_decode($response->getContent(), true);
        
        // Réorganiser la réponse pour avoir une structure plus logique
        $response->setData([
            'success' => true,
            'data' => $jsonResponse['data'] ?? [],
            'meta' => $jsonResponse['meta'] ?? null,
            'links' => $jsonResponse['links'] ?? null,
        ]);
    }
}
