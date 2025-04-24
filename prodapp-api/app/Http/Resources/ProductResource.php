<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'prix' => $this->prix,
            'quantite' => $this->quantite,
            'image' => $this->image ? asset('storage/' . $this->image) : null, // Ajouter l'URL complète pour l'image
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
