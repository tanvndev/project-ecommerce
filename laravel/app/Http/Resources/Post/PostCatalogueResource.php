<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostCatalogueResource extends JsonResource
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
            'key' => $this->id,
            'canonical' => $this->canonical,
            'name' => $this->name,
            'description' => $this->description,
            'publish' => $this->publish,
            // 'image' => $this->image,
            'childrens' => PostCatalogueResource::collection($this->childrens),
        ];
    }
}
