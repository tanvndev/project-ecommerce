<?php

namespace App\Http\Resources\SearchHistory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // {
        //     "width": "4",
        //     "effect": "fade",
        //     "height": "4",
        //     "autoPlay": "true",
        //     "showArrow": "true",
        //     "navigation": "thumbnails",
        //     "effectSpeed": "4",
        //     "pauseOnHover": "true",
        //     "transitionSpeed": "4"
        //   }
        return [
            'id'        => $this->id,
            'keyword'   => $this->keyword,
            'count'     => $this->count,
        ];
    }
}
