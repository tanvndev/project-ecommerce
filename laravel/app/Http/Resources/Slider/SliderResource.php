<?php

namespace App\Http\Resources\Slider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
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
            'key'     => $this->id,
            'id'      => $this->id,
            'name'    => $this->name,
            'code'    => $this->code,
            'items'   => collect($this->items)
                ->sortBy('id')
                ->values() // Re-index the collection
                ->toArray(), // Convert back to array
            'setting' => [
                ...$this->setting,
                'autoPlay'    => filter_var($this->setting['autoPlay'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'showArrow'   => filter_var($this->setting['showArrow'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'pauseOnHover' => filter_var($this->setting['pauseOnHover'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ],
            'publish' => $this->publish,
        ];
    }
}
