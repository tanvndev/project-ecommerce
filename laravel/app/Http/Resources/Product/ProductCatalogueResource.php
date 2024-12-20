<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCatalogueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'key'              => $this->id,
            'canonical'        => $this->canonical,
            'name'             => $this->name,
            'description'      => $this->description,
            'publish'          => $this->publish,
            'is_featured'      => $this->is_featured,
            'order'            => $this->order,
            'parent_id'        => $this->parent_id,
            'image'            => $this->image,
            'meta_description' => $this->meta_description,
            'meta_title'       => $this->meta_title,
            'canonical'        => $this->cacanonical,
            'childrens'        => ProductCatalogueResource::collection($this->childrens),
        ];
    }
}
